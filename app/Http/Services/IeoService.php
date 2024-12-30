<?php
namespace App\Http\Services;

use App\Model\IeoModel;
use App\Model\IeoWallet;
use App\Model\Wallet;
use App\Model\Coin;
use App\Model\LogTranferIeoCoin;
use App\Model\Transaction;
use App\Model\UserRegisteredIeo;
use App\Http\Repositories\AdminIeoRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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
            ->with([
                'userRegisteredIeo' => function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                }
            ])
            ->get(['id', 'name', 'value', 'symbol', 'total_supply', 'max_rate', 'start_date', 'end_date']);

        $ieoHistory = $ieoHistory->map(function ($ieo) use ($user) {
            $userRegistered = $ieo->userRegisteredIeo->first();
            $frozenRate = $userRegistered ? $userRegistered->getLockedPercentage() : 0;
            $releaseRate = $userRegistered ? $userRegistered->getUnlockedPercentage() : 0;
            $winningRate = $userRegistered ? $userRegistered->calculateWinRate($ieo->id, $user->id) : 0;
            $checkIeoWallet = IeoWallet::where('user_id', $user->id)
                ->where('coin_id', $ieo->id)
                ->first();
            $checkIeoTranferHistory = LogTranferIeoCoin::where('user_id', $user->id)
                ->where('ieo_id', $ieo->id)
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
                'checkIeoTranferHistory' => $checkIeoTranferHistory,
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

    public function receiveIeoWallet($ieoId)
    {
        $user = auth()->user();
        $ieo = IeoModel::findOrFail($ieoId);
        $coin = Coin::firstWhere('coin_type', $ieo->symbol);

        if (!$coin) {
            return $this->errorResponse('Hệ thống đang chuyển đổi vui lòng liên hệ với CSKH!');
        }

        try {
            return DB::transaction(function () use ($user, $ieo, $coin) {
                $userRegisteredIeo = UserRegisteredIeo::where([
                    'user_id' => $user->id,
                    'ieo_id' => $ieo->id
                ])->firstOrFail();

                $amounts = $this->calculateIeoAmounts($userRegisteredIeo, $ieo);

                // Save to IeoWallet
                IeoWallet::create([
                    'user_id' => $user->id,
                    'name' => $ieo->name,
                    'coin_id' => $ieo->id,
                    'type' => 4,
                    'coin_type' => 4,
                    'balance' => $amounts['total']
                ]);

                return $this->successResponse('Nhận IEO thành công!');
            });
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function receiveIeo($ieoId)
    {
        $user = auth()->user();
        $ieo = IeoModel::findOrFail($ieoId);
        $coin = Coin::firstWhere('coin_type', $ieo->symbol);

        if (!$coin) {
            return $this->errorResponse('Hệ thống đang chuyển đổi vui lòng liên hệ với CSKH!');
        }

        try {
            return DB::transaction(function () use ($user, $ieo, $coin) {
                $userRegisteredIeo = UserRegisteredIeo::where([
                    'user_id' => $user->id,
                    'ieo_id' => $ieo->id
                ])->firstOrFail();

                $amounts = $this->calculateIeoAmounts($userRegisteredIeo, $ieo);

                if ($amounts['win'] > 0) {
                    $winWallet = Wallet::where([
                        'user_id' => $user->id,
                        'coin_id' => $coin->id
                    ])->firstOrFail();

                    $this->createTransactionLog(
                        $user->id,
                        $ieo->id,
                        $winWallet->id,
                        $amounts['win'],
                        'Số tiền chiến thắng coin IEO!'
                    );

                    $winWallet->balance += $amounts['win'];
                    $winWallet->save();
                }

                if ($amounts['refund'] > 0) {
                    $refundWallet = Wallet::where([
                        'user_id' => $user->id,
                        'coin_id' => 2
                    ])->firstOrFail();

                    $this->createTransactionLog(
                        $user->id,
                        $ieo->id,
                        $refundWallet->id,
                        $amounts['refund'],
                        'Số tiền hoàn trả coin IEO!'
                    );

                    $refundWallet->balance += $amounts['refund'];
                    $refundWallet->save();
                }

                return $this->successResponse('Đã lưu vào ví IEO thành công!');
            });
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    private function calculateIeoAmounts($userRegisteredIeo, $ieo)
    {
        if ($userRegisteredIeo->rating_win > 0) {
            $winAmount = ($userRegisteredIeo->rating_win * $userRegisteredIeo->quantity / 100);
            $refundAmount = $userRegisteredIeo->quantity - $winAmount;

            return [
                'win' => $winAmount * $ieo->value,
                'refund' => $refundAmount * $ieo->value,
                'total' => ($winAmount + $refundAmount) * $ieo->value
            ];
        }

        return [
            'win' => 0,
            'refund' => $userRegisteredIeo->quantity * $ieo->value,
            'total' => $userRegisteredIeo->quantity * $ieo->value
        ];
    }

    private function createTransactionLog($userId, $ieoId, $walletId, $amount, $note)
    {
        return LogTranferIeoCoin::create([
            'user_id' => $userId,
            'ieo_id' => $ieoId,
            'wallet_coin_id' => $walletId,
            'balance' => $amount,
            'note' => $note,
            'create_at' => now(),
        ]);
    }

    private function successResponse($message)
    {
        return ['success' => true, 'message' => $message];
    }

    private function errorResponse($message)
    {
        return ['success' => false, 'message' => $message];
    }

    public function getIeoTransactionHistory(Request $request)
    {
        try {
            $limit = $request->input('limit', 1);
            $page = $request->input('page', 1);
            $sort = $request->input('sort', 'create_at');
            $sort_order = $request->input('sort_order', 'desc');
            $search = $request->input('search', '');

            $user = auth()->user();

            $query = LogTranferIeoCoin::query()
                ->select([
                    'log_tranfer_ieo_coin.user_id',
                    'log_tranfer_ieo_coin.balance',
                    'log_tranfer_ieo_coin.note',
                    'log_tranfer_ieo_coin.create_at',
                    'ieo.name as ieo_name',
                    'ieo.ieo_icon',
                    'coins.name as coin_name',
                ])
                ->leftJoin('ieo', 'log_tranfer_ieo_coin.ieo_id', '=', 'ieo.id')
                ->leftJoin('coins', 'log_tranfer_ieo_coin.wallet_coin_id', '=', 'coins.id')
                ->where('log_tranfer_ieo_coin.user_id', $user->id);

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('ieo.name', 'like', "%{$search}%")
                    ->orWhere('coins.name', 'like', "%{$search}%")
                    ->orWhere('log_tranfer_ieo_coin.note', 'like', "%{$search}%");
                });
            }

            if ($sort) {
                $sortColumnMap = [
                    'ieo_name' => 'ieo.name',
                    'coin_name' => 'coins.name',
                    'balance' => 'log_tranfer_ieo_coin.balance',
                    'note' => 'log_tranfer_ieo_coin.note',
                    'create_at' => 'log_tranfer_ieo_coin.create_at'
                ];

                $sortColumn = $sortColumnMap[$sort] ?? 'log_tranfer_ieo_coin.create_at';
                $query->orderBy($sortColumn, $sort_order);
            }

            $transactions = $query->paginate($limit);

            return response()->json([
                'success' => true,
                'data' => [
                    'data' => $transactions->items(),
                    'pagination' => [
                        'current_page' => $transactions->currentPage(),
                        'per_page' => (int)$transactions->perPage(),
                        'total' => $transactions->total(),
                        'last_page' => $transactions->lastPage(),
                        'from' => $transactions->firstItem(),
                        'to' => $transactions->lastItem()
                    ]
                ]
            ]);

        } catch (Exception $e) {
            \Log::error('IEO Transaction History Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching transaction history'
            ], 500);
        }
    }
}
