<?php

namespace App\Http\Controllers\admin;

use App\Model\Transaction;
use DB;
use Illuminate\Http\Request;
use App\Exports\OrderHistory;
use App\Exports\BuyOrderHistory;
use App\Exports\TradeTransaction;
use App\Model\TradeReferralHistory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Services\BuyOrderService;
use App\Http\Services\SellOrderService;
use App\Http\Services\StopLimitService;
use App\Http\Services\TransactionService;
use App\Http\Services\TradeReferralService;
use App\Http\Requests\Admin\TransactionExportRequest;
use App\Model\UserRegisteredIeo;
use App\Exports\IeoBuyOrderHistoryExport;
use App\Model\CurrencyDeposit;

class ReportController extends Controller
{
    /*
  *
  * All Stop Limit Orders History
  * adminAllOrdersHistoryStopLimit
  *
  * Show the list of specified resource.
  * @return \Illuminate\Http\Response
  *
  */
    public function adminAllOrdersHistoryStopLimit(Request $request)
    {
        $data['title'] = __('Stop Limit Order History');
        $service = new StopLimitService();
        $data['type'] = 'stop_limit';
        $data['sub_menu'] = 'stop_limit';

        if ($request->ajax()) {

            $data['items'] = $service->getOrders();

            return datatables($data['items'])
                ->addColumn('order_type', function ($item) {
                    return ucfirst($item->order_type);
                })
                ->editColumn('price', function ($item) {
                    return $item->price.' '.$item->base_coin;
                })
                ->editColumn('amount', function ($item) {
                    return $item->amount.' '.$item->trade_coin;
                })

                ->editColumn('created_at', function ($item) {
                    return $item->created_at;
                })
                ->editColumn('order_type', function ($item) {
                    if($item->order_type == 'Sell') {
                        return '<span class="text-success">'.__('Sell').' </span>';
                    } else {
                        return '<span class="text-danger">'.__('Buy').' </span>';
                    }
                })
                ->rawColumns(['order_type'])
                ->make(true);
        }

        return view('admin.exchange.report.stop_limit_order_report',$data);
    }
  /*
  *
  * All Buy Orders History
  * adminAllOrdersHistoryBuy
  *
  * Show the list of specified resource.
  * @return \Illuminate\Http\Response
  *
  */
    public function adminAllOrdersHistoryBuy(Request $request)
    {
        $data['title'] = __('Buy Order History');
        $buyService = new BuyOrderService();
        $data['type'] = 'buy';
        $data['sub_menu'] = 'buy_order';

        if ($request->ajax()) {

            $data['items'] = $buyService->getOrders();

            return datatables($data['items'])
                ->editColumn('is_market', function ($item) {
                    return $item->is_market ? 'Market' : 'Normal';
                })
                ->editColumn('price', function ($item) {
                    return $item->price.' '.$item->base_coin;
                })
                ->editColumn('amount', function ($item) {
                    return $item->amount.' '.$item->trade_coin;
                })
                ->editColumn('processed', function ($item) {
                    return $item->processed.' '.$item->trade_coin;
                })
                ->editColumn('remaining', function ($item) {
                    return $item->remaining.' '.$item->trade_coin;
                })
                ->editColumn('created_at', function ($item) {
                    return $item->created_at;
                })
                ->editColumn('status', function ($item) {
                    if($item->status == 1) {
                        return '<span class="text-success">'.__('Completed').' </span>';
                    } elseif($item->deleted_at != null) {
                        return '<span class="text-warning">'.__('Processing').' </span>';
                    } elseif($item->status == 0) {
                        return '<span class="text-warning">'.__('Pending').' </span>';
                    } else {
                        return '<span class="text-danger">'.__('Deleted').' </span>';
                    }
                })
                ->rawColumns(['status'])
                ->make(true);
        }

        return view('admin.exchange.report.buy_order_report',$data);
    }

    /*
   *
   * All Sell Orders History
   * adminAllOrdersHistorySell
   *
   * Show the list of specified resource.
   * @return \Illuminate\Http\Response
   *
   */
    public function adminAllOrdersHistorySell(Request $request)
    {
        $data['title'] = __('Sell Order History');
        $data['type'] = 'sell';
        $data['sub_menu'] = 'sell_order';
        $sellService = new SellOrderService();

        if ($request->ajax()) {
            $data['items'] = $sellService->getOrders();

            return datatables($data['items'])
                ->editColumn('is_market', function ($item) {
                    return $item->is_market ? 'Market' : 'Normal';
                })
                ->editColumn('price', function ($item) {
                    return $item->price.' '.$item->base_coin;
                })
                ->editColumn('amount', function ($item) {
                    return $item->amount.' '.$item->trade_coin;
                })
                ->editColumn('processed', function ($item) {
                    return $item->processed.' '.$item->trade_coin;
                })
                ->editColumn('remaining', function ($item) {
                    return $item->remaining.' '.$item->trade_coin;
                })
                ->editColumn('created_at', function ($item) {
                    return $item->created_at;
                })
                ->editColumn('status', function ($item) {
                    if($item->status == 1) {
                        return '<span class="text-success">'.__('Completed').' </span>';
                    } elseif($item->deleted_at != null) {
                        return '<span class="text-warning">'.__('Processing').' </span>';
                    } elseif($item->status == 0) {
                        return '<span class="text-warning">'.__('Pending').' </span>';
                    } else {
                        return '<span class="text-danger">'.__('Deleted').' </span>';
                    }
                })
                ->rawColumns(['status'])
                ->make(true);
        }

        return view('admin.exchange.report.sell_order_report',$data);
    }

    /*
   *
   * All Sell buy transaction Orders History
   * adminAllTransactionHistory
   *
   * Show the list of specified resource.
   * @return \Illuminate\Http\Response
   *
   */
    public function adminAllTransactionHistory(Request $request)
    {
        $data['title'] = __('Transaction History');
        $data['sub_menu'] = 'transaction';
        try{
            $sellService = new TransactionService();
            if ($request->ajax()) {
                $type = $request->columns[9]["search"]["value"] ?? false;
                $data['items'] = $sellService->getOrdersQueryReport($type ?? 'all');

                return datatables($data['items'])
                    ->filterColumn('transaction_id', function ($query, $keyword) {
                        $query->where('transactions.transaction_id', 'LIKE', "%$keyword%");
                    })
                    ->filterColumn('base_coin', function ($query, $keyword) {
                        $query->where('base_coin_table.coin_type', 'LIKE', "%$keyword%");
                    })
                    ->filterColumn('trade_coin', function ($query, $keyword) {
                        $query->where('trade_coin_table.coin_type', 'LIKE', "%$keyword%");
                    })
                    ->filterColumn('sell_user_email', function ($query, $keyword) {
                        $query->where('sell_user.email', 'LIKE', "%$keyword%");
                    })
                    ->filterColumn('buy_user_email', function ($query, $keyword) {
                        $query->where('buy_user.email', 'LIKE', "%$keyword%");
                    })
                    ->editColumn('price', function ($item) {
                        return $item->price.' '.$item->base_coin;
                    })
                    ->editColumn('total', function ($item) {
                        return number_format($item->total,8).' '.$item->base_coin;
                    })
                    ->editColumn('amount', function ($item) {
                        return $item->amount.' '.$item->trade_coin;
                    })

                    ->editColumn('created_at', function ($item) {
                        return $item->created_at;
                    })
                    ->editColumn('type', function ($item) {
                        return 1;
                    })
                    ->make(true);
            }
        }catch(\Exception $e){
            storeException('adminAllTransactionHistory', $e->getMessage());
        }
        return view('admin.exchange.report.transaction_report',$data);
    }

    public function adminAllTradeReferralHistory(Request $request)
    {
        $data['title'] = __('Trade Referral Distribution History');
        $data['sub_menu'] = 'referral';

        if ($request->ajax()) {
            $referral_history_list = TradeReferralHistory::join('transactions', 'transactions.id','=','trade_referral_histories.transaction_id')
                                                    ->join('users as reference_user', 'reference_user.id','=','trade_referral_histories.user_id')
                                                    ->join('users as referral_user', 'referral_user.id','=','trade_referral_histories.trade_by')
                                                    ->latest()->select('trade_referral_histories.*','transactions.transaction_id as transaction_ref',
                                                        'reference_user.email as reference_user_email','referral_user.email as referral_user_email' );

            return datatables($referral_history_list)
                ->editColumn('created_at', function ($item){
                    return $item->created_at;
                })
                ->editColumn('amount', function ($item) {
                    return $item->amount.' '.$item->coin_type;
                })
                ->make(true);
        }
        return view('admin.exchange.report.trade_referral_history', $data);
    }

    public function adminAllOrdersHistoryBuyExport(TransactionExportRequest $request)
    {
        try{
            return Excel::download(new BuyOrderHistory($request), 'BuyTrade'.($request->export_to ?? '.csv'));
        }catch(\Exception $e){
            storeException('adminAllOrdersHistoryBuyExport', $e->getMessage());
            return redirect()->back()->with('dismiss', __('Something went wrong'));
        }
    }
    public function adminAllOrdersHistorySellExport(TransactionExportRequest $request)
    {
        try{
            return Excel::download(new BuyOrderHistory($request), 'SellTrade'.($request->export_to ?? '.csv'));
        }catch(\Exception $e){
            storeException('adminAllOrdersHistoryBuyExport', $e->getMessage());
            return redirect()->back()->with('dismiss', __('Something went wrong'));
        }
    }

    public function adminAllTransactionHistoryExport(TransactionExportRequest $request)
    {
        try{
            return Excel::download(new TradeTransaction($request), 'TransactionHistory'.($request->export_to ?? '.csv'));
        }catch(\Exception $e){
            storeException('adminAllOrdersHistoryBuyExport', $e->getMessage());
            return redirect()->back()->with('dismiss', __('Something went wrong'));
        }
    }

    public function adminAllIEOBuyOrderHistory(Request $request)
    {
        $data['title'] = __('User IEO Purchase List');
        $data['sub_menu'] = 'ieo_buy_order';

        if ($request->ajax()) {
            return $this->handleAjaxRequest($request);
        }

        return view('admin.exchange.report.ieo_buy_order_history', $data);
    }

    private function handleAjaxRequest(Request $request)
    {
        if (!$request->has('user_id') || empty($request->get('user_id'))) {
            return response()->json([
                'data' => [],
                'message' => __('Please select a user first.')
            ]);
        }

        $userId = $request->get('user_id');
        $typeHistory = $request->get('type_history', 'ieo');

        return match($typeHistory) {
            'trade' => $this->getTradeHistory($userId),
            'fiat' => $this->getFiatDepositHistory($userId),
            default => $this->getIEOPurchaseHistory($userId)
        };
    }

    private function getTradeHistory($userId)
    {
        $where['buy_user_id'] = $userId;
        $orWhere = ['sell_user_id' => $userId];
        $query = Transaction::join('coins as bc', 'bc.id', '=', 'transactions.base_coin_id')
            ->join('coins as tc', 'tc.id', '=', 'transactions.trade_coin_id')
            ->select([
                'transaction_id',
                DB::raw("CASE WHEN buy_user_id = $userId THEN buy_fees WHEN sell_user_id = $userId THEN sell_fees END as fees"),
                DB::raw("visualNumberFormat(amount) as amount"),
                DB::raw("bc.coin_type as base_coin"),
                DB::raw("tc.coin_type as trade_coin"),
                DB::raw("visualNumberFormat(price) as price"),
                DB::raw("visualNumberFormat(last_price) as last_price"),
                'price_order_type',
                DB::raw("visualNumberFormat(total) as total"),
                DB::raw("transactions.created_at as time")
            ])
            ->where($where)
            ->when(isset($orWhere) ,function($query) use ($orWhere){
                $query->where($orWhere);
            })
            ->orderBy('transactions.id', 'DESC')
            ->get();

        return datatables()->of($query)
            ->editColumn('time', fn($trade) =>
                $trade->time ? Carbon::parse($trade->time)->format('Y-m-d H:i:s') : 'N/A'
            )
            ->make(true);
    }

    private function getFiatDepositHistory($userId)
    {
        $lists = CurrencyDeposit::with(['bank'])
            ->where('user_id', $userId)
            ->orderBy('id', 'DESC')
            ->get();

        return datatables()->of($lists)
            ->editColumn('currency_amount', fn($list) => $list->currency_amount . " VND")
            ->editColumn('coin_amount', fn($list) => $list->coin_amount . " " . $list->coin_type)
            ->editColumn('id', fn($list) => $list->id)
            ->editColumn('rate', fn($list) => $list->rate . " " . $list->coin_type)
            ->editColumn('status', function ($list) {
                return match($list->status) {
                    1 => '<span class="text-success">Success</span>',
                    2 => '<span class="text-danger">Failed</span>',
                    default => 'N/A'
                };
            })
            ->editColumn('created_at', fn($list) =>
                $list->created_at ? Carbon::parse($list->created_at)->format('Y-m-d H:i:s') : 'N/A'
            )
            ->rawColumns(['status'])
            ->make(true);
    }

    private function getIEOPurchaseHistory($userId)
    {
        $ieoPurchases = UserRegisteredIeo::select([
            'user_registered_ieo.id',
            'users.email as email',
            'ieo.name as ieo_name',
            'user_registered_ieo.quantity',
            'user_registered_ieo.created_at',
            'ieo.value as ieo_value',
        ])
        ->join('users', 'user_registered_ieo.user_id', '=', 'users.id')
        ->join('ieo', 'user_registered_ieo.ieo_id', '=', 'ieo.id')
        ->where('user_registered_ieo.quantity', '>', 0)
        ->where('users.id', $userId);

        return datatables()->of($ieoPurchases)
            ->editColumn('created_at', fn($purchase) =>
                $purchase->created_at ? Carbon::parse($purchase->created_at)->format('Y-m-d H:i:s') : 'N/A'
            )
            ->addColumn('totalAmount', function ($purchase) {
                $amount = !empty($purchase->ieo_value) && $purchase->quantity > 0
                    ? $purchase->ieo_value * $purchase->quantity
                    : 0;

                $totalAmount = number_format($amount, 6);
                $totalAmount = rtrim(rtrim($totalAmount, '0'), '.');

                return $totalAmount ?: '0';
            })
            ->make(true);
    }

    public function adminAllIEOBuyOrderHistoryExport(TransactionExportRequest $request)
    {
        try{
            return Excel::download(new IeoBuyOrderHistoryExport($request), 'IeoBuyOrderTrade'.($request->export_to ?? '.csv'));
        }catch(\Exception $e){
            storeException('adminAllOrdersHistoryBuyExport', $e->getMessage());
            return redirect()->back()->with('dismiss', __('Something went wrong'));
        }
    }
}
