<?php
namespace App\Http\Services;

use App\Model\IeoModel;
use App\Model\IeoWallet;
use App\Model\Wallet;
use App\Model\Transaction;
use App\Model\UserRegisteredIeo;
use App\Http\Repositories\AdminIeoRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class IeoService extends BaseService
{

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

    public function getIeo()
    {
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

    public function getIeoUserRegistered()
    {
        $user = auth()->user();
        $ieoHistory = IeoModel::whereHas('userRegisteredIeo', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->get(['id', 'name', 'value', 'symbol', 'total_supply', 'max_rate', 'start_date', 'end_date']);

        $ieoHistory = $ieoHistory->map(function ($ieo) use ($user) {
            $userRegistered = $ieo->userRegisteredIeo->first();
            $frozenRate = $userRegistered ? $userRegistered->getLockedPercentage() : 0;
            $releaseRate = $userRegistered ? $userRegistered->getUnlockedPercentage() : 0;
            $winningRate = $userRegistered ? $userRegistered->calculateWinRate($ieo->id, $user->id) : 0;
            $checkIeoWallet = IeoWallet::where('user_id', $user->id)
                ->where('coin_id', $ieo->id)
                ->first();

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
                'winning_rate' => $winningRate,
                'checkIeoWallet' => $checkIeoWallet,
            ];
        });

        return response()->json(['success' => true, 'data' => $ieoHistory]);
    }

    public function registerIeo($ieoId, $amount)
    {
        $user = auth()->user();
        $ieo = IeoModel::find($ieoId);

        if ($amount <= 0) {
            return ['success' => false, 'message' => 'Vui lòng nhập số lượng lớn hơn 0!'];
        }

        $totalRegistered = UserRegisteredIeo::where('ieo_id', $ieoId)->sum('quantity');
        if ($totalRegistered + $amount > $ieo->total_supply) {
            return ['success' => false, 'message' => 'Đã hết số lượng IEO, không thể đăng ký thêm!'];
        }

        $wallet = Wallet::where('user_id', $user->id)->where('coin_id', 2)->first();
        if (!$wallet || $wallet->balance < $amount * $ieo->value) {
            return ['success' => false, 'message' => 'Số dư trong ví không đủ để đăng ký IEO!'];
        }

        $userRegisteredIeo = UserRegisteredIeo::firstOrNew(
            ['user_id' => $user->id, 'ieo_id' => $ieoId]
        );

        $userRegisteredIeo->quantity += $amount;
        $userRegisteredIeo->rating_win = $ieo->max_rate;
        $userRegisteredIeo->save();

        $wallet->balance -= $amount * $ieo->value;
        $wallet->save();

        return ['success' => true, 'message' => 'Đăng ký IEO thành công!'];
    }

    public function receiveIeo($ieoId)
    {
        $ieo = IeoModel::find($ieoId);
        $user = auth()->user();

        $userRegisteredIeo = UserRegisteredIeo::where('user_id', $user->id)
            ->where('ieo_id', $ieoId)
            ->first();

        $receivedAmount = $userRegisteredIeo->quantity * $ieo->value;
        $rating_win = $userRegisteredIeo->rating_win * $receivedAmount / 100;
        $totalRate = $rating_win + $receivedAmount;

        try {
            DB::beginTransaction();

            $ieoWallet = IeoWallet::create([
                'user_id' => $user->id,
                'coin_id' => $ieo->id,
                'balance' => $totalRate,
                'type' => '4',
                'coin_type' => '4',
                'name' => $ieo->name,
            ]);

            // Kiểm tra nếu tạo wallet không thành công
            if (!$ieoWallet) {
                throw new Exception('Không thể tạo ví IEO.');
            }

            // Tạo Transaction Log
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'trade_coin_id' => $ieoId,
                'base_coin_id' => 2,
                'amount' => $ieo->value,
                'price' => $receivedAmount,
                'last_price' => $totalRate,
            ]);

            // Kiểm tra nếu tạo transaction không thành công
            if (!$transaction) {
                throw new Exception('Không thể tạo giao dịch IEO.');
            }

            DB::commit();

            return ['success' => true, 'message' => 'Nhận IEO thành công!'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }


}
