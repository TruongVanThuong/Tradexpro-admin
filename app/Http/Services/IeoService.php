<?php
namespace App\Http\Services;

use App\Model\IeoModel;
use App\Model\UserRegisteredIeo;
use App\Http\Repositories\AdminIeoRepository;
use Exception;
use Carbon\Carbon;

class IeoService extends BaseService {

    protected $object;
    protected $model;
    protected $repository;

    public function __construct()
    {
        $this->model = new IeoModel();
        $this->repository = new AdminIeoRepository($this->model);
        $this->object = $this->repository;
        parent::__construct($this->model, $this->repository);
    }

    public function getIeoDetailsById($ieoId)
    {
        try {
            $ieo = $this->object->getIeoDetailsById($ieoId);

            if ($ieo) {
                return [
                    'success' => true,
                    'data' => $ieo,
                    'message' => __('Successfully retrieved IEO data.')
                ];
            } else {
                return [
                    'success' => false,
                    'data' => '',
                    'message' => __('Data not found')
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => __('Something went wrong. Please try again later.')
            ];
        }
    }

    /**
     * Handle the IEO delete process.
     *
     * @param int $ieoId
     * @return bool
     */
    public function adminIeoDeleteProcess($ieoId)
    {
        try {
            $ieo = ieoModel::find($ieoId);

            if (!$ieo) {
                return false;
            }

            $ieo->delete();

            return true;
        } catch (Exception $e) {
            \Log::error('Failed to delete IEO: ' . $e->getMessage());
            return false;
        }
    }

    public function getIeo(){
        $object = $this->object->getDocs();

        if (empty($object)) {
            return null;
        }

        foreach ($object as $ieo) {
            $ieo->registered = $ieo->getRegisteredQuantity();
            $ieo->status = $ieo->getStatus();
            $ieo->timeRemaining = $ieo->getTimeRemaining();
        }

        return $object;
    }

    public function getIeoUserRegistered() {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $ieoHistory = IeoModel::whereHas('userRegisteredIeo', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->get(['id', 'name', 'value', 'symbol', 'total_supply', 'max_rate', 'start_date', 'end_date']);

        $ieoHistory = $ieoHistory->map(function ($ieo) use ($user) {
            $userRegistered = $ieo->userRegisteredIeo->first();
            $frozenRate = $userRegistered ? $userRegistered->getLockedPercentage() : 0;
            $releaseRate = $userRegistered ? $userRegistered->getUnlockedPercentage() : 0;
            $winningRate = $userRegistered ? $userRegistered->calculateWinRate() : 0;
            $quantityRegistered = $userRegistered ? $userRegistered->quantity : 0;

            return [
                'id' => $ieo->id,
                'name' => $ieo->name,
                'value' => $ieo->value,
                'symbol' => $ieo->symbol,
                'max_rate' => $ieo->max_rate,
                'start_date' => $ieo->start_date,
                'end_date' => $ieo->end_date,
                'quantity' => $quantityRegistered,
                'frozen_rate' => $frozenRate,
                'release_rate' => $releaseRate,
                'winning_rate' => $winningRate
            ];
        });

        return response()->json(['success'=>true,'data' => $ieoHistory]);
    }

    public function registerIeo($ieoId, $amount)
    {
        $user = auth()->user();
        if (!$user) {
            return ['success' => false, 'message' => 'Bạn cần đăng nhập để tham gia!'];
        }

        $ieo = IeoModel::find($ieoId);

        if (!$ieo) {
            return ['success' => false, 'message' => 'IEO không tồn tại!'];
        }

        if ($amount > ($ieo->total_supply - $ieo->registered)) {
            return ['success' => false, 'message' => 'Số lượng đăng ký vượt quá số lượng còn lại!'];
        }

        if ($amount <= 0) {
            return ['success' => false, 'message' => 'Vui lòng nhập số lượng lớn hơn 0!'];
        }

        UserRegisteredIeo::create([
            'user_id' => 2,
            'ieo_id' => $ieo->id,
            'quantity' => $amount,
            'created_at' => Carbon::now()
        ]);

        return ['success' => true, 'message' => 'Đăng ký thành công!'];
    }

}
