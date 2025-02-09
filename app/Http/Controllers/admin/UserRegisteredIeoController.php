<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Services\UserRegisteredIeoService;
use App\Model\UserRegisteredIeo;
use App\Model\IeoWallet;
use Nwidart\Modules\Facades\Module;

class userRegisteredIeoController extends Controller
{
    private $userRegisteredIeoService;

    public function __construct()
    {
        $this->userRegisteredIeoService = new UserRegisteredIeoService();
    }

    public function adminUserRegisteredIeoList(Request $request)
    {
        $data['title'] = __('Registered IEO List');

        if ($request->ajax()) {
            $registeredIeos = UserRegisteredIeo::join('users', 'user_registered_ieo.user_id', '=', 'users.id')
                ->join('ieo', 'user_registered_ieo.ieo_id', '=', 'ieo.id')
                ->select(
                    'user_registered_ieo.id',
                    'user_registered_ieo.user_id',
                    'user_registered_ieo.ieo_id',
                    'user_registered_ieo.rating_win',
                    'users.email as email',
                    'ieo.name as ieo_name',
                    'ieo.value as value',
                    'user_registered_ieo.quantity'
                )
                ->get();
            return datatables()->of($registeredIeos)
                ->addColumn('actions', function ($registeredIeo) {
                    $hasWallet = IeoWallet::where('user_id', $registeredIeo->user_id)
                    ->where('coin_id', $registeredIeo->ieo_id)
                    ->exists();
                    return view('admin.user-register-ieo.partials.actions', compact('registeredIeo', 'hasWallet'))->render();
                })
                ->editColumn('rating_win', function ($registeredIeo) {
                    $formattedRating = number_format($registeredIeo->rating_win, 6);
                    $formattedRating = rtrim($formattedRating, '0');
                    $formattedRating = rtrim($formattedRating, '.');
                    return $formattedRating . '%';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('admin.user-register-ieo.userRegisterIeo', $data);
    }

    public function adminUserRegisteredIeoEdit($id)
    {
        $decrypted = decryptId($id);
        $item = $this->userRegisteredIeoService->getUserRegisteredIeoDetailsById($decrypted);

        if (isset($item) && $item['success'] == false) {
            return redirect()->back()->with(['dismiss' => $item['message']]);
        }

        $data['item'] = $item['data'];
        $data['title'] = __('Update IEO');
        $data['button_title'] = __('Update');

        return view('admin.user-register-ieo.editUserRegisterIeo', $data);
    }

    public function adminUserRegisteredIeoSaveProcess(Request $request)
    {
        try {
            $id = $request->ieo_id;
            $userRegisteredIeo = UserRegisteredIeo::find($id);

            if (!$userRegisteredIeo) {
                return redirect()->route('adminIeoList')->with('dismiss', __('IEO not found.'));
            }

            $userRegisteredIeo->rating_win = str_replace(',', '.', $request->rating_win);

            $update = $userRegisteredIeo->save();

            if ($update) {
                return redirect()->route('adminUserRegisteredIeoList')->with('dismiss', __('Update rating win successfully.'));
            }
            return redirect()->back()->with('dismiss', __('No changes were made.'));
        } catch (\Exception $e) {
            storeException('ieo_price', $e->getMessage());
            return redirect()->back()->with('dismiss', __('Something went wrong.'));
        }

    }
}
