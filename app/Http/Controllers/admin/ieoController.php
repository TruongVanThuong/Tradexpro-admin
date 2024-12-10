<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Model\ieoModel;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\IeoSaveRequest;
use Nwidart\Modules\Facades\Module;
use App\Http\Services\IeoService;

class ieoController extends Controller
{
    private $ieoService;

    public function __construct()
    {
        $this->ieoService = new IeoService();
    }
    public function getIeo(Request $request)
    {
        if ($request->ajax()) {
            // Truy vấn dữ liệu IEO
            $ieos = ieoModel::select([
                'id',
                'name',
                'value',
                'symbol',
                'total_supply',
                'max_rate',
                'start_date',
                'end_date'
            ]);

            return datatables()->of($ieos)
                ->addColumn('actions', function ($ieo) {
                    return view('admin.ieo.partials.actions', compact('ieo'))->render();
                })
                ->editColumn('start_date', function ($ieo) {
                    return $ieo->start_date ? $ieo->start_date->format('Y-m-d H:i:s') : 'N/A';
                })
                ->editColumn('end_date', function ($ieo) {
                    return $ieo->end_date ? $ieo->end_date->format('Y-m-d H:i:s') : 'N/A';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('admin.ieo.ieo');
    }

    // add ieo page
    public function adminAddIeo()
    {
        $data['title'] = __('Add New IEO');
        $data['button_title'] = __('Save');
        return view('admin.ieo.add_ieo', $data);
    }

    // admin new ieo save process
    public function adminSaveIeo(IeoSaveRequest $request)
    {
        if (empty($request->max_rate)) {
            $request->max_rate = 5;
        }
        try {
            $data = [
                'name'          => $request->name,
                'value'         => $request->value,
                'symbol'        => $request->symbol,
                'total_supply'  => $request->total_supply,
                'max_rate'      => $request->max_rate,
                'start_date'    => $request->start_date,
                'end_date'      => $request->end_date,
            ];

            $save = ieoModel::create($data);
            if ($save) {
                return redirect()->route('adminIeoList')->with('dismiss', __('New ieo added successfully'));
            }
            return redirect()->back()->with('dismiss', __('Something went wrong'));
        } catch (\Exception $e) {
            storeException('adminSaveIeo : ',$e->getMessage());
            return redirect()->back()->with('dismiss', __('Something went wrong'));
        }
    }

    public function adminIeoEdit($id)
    {
        $ieoId = decryptId($id);
        $data['module'] = Module::allEnabled();

        if (is_array($ieoId)) {
            return redirect()->back()->with(['dismiss' => __('IEO not found')]);
        }

        $item = $this->ieoService->getIeoDetailsById($ieoId);

        if (isset($item) && $item['success'] == false) {
            return redirect()->back()->with(['dismiss' => $item['message']]);
        }

        $data['item'] = $item['data'];
        $data['title'] = __('Update IEO');
        $data['button_title'] = __('Update');

        return view('admin.ieo.edit_ieo', $data);
    }

    public function adminIeoSaveProcess(IeoSaveRequest $request)
    {
        try {
            $id = $request->ieo_id;
            $ieo = ieoModel::find($id);

            if (!$ieo) {
                return redirect()->route('adminIeoList')->with('dismiss', __('IEO not found.'));
            }

            $ieo->name = $request->name;
            $ieo->value = $request->value;
            $ieo->symbol = $request->symbol;
            $ieo->total_supply = $request->total_supply;
            $ieo->max_rate = $request->max_rate;
            $ieo->start_date = $request->start_date;
            $ieo->end_date = $request->end_date;

            $update = $ieo->save();

            if ($update) {
                return redirect()->route('adminIeoList')->with('dismiss', __('Update ieo successfully.'));
            }
            return redirect()->back()->with('dismiss', __('No changes were made.'));
        } catch (\Exception $e) {
            storeException('ieo_price', $e->getMessage());
            return redirect()->back()->with('dismiss', __('Something went wrong.'));
        }

    }

    public function adminIeoDelete($id) {
        try {
            $ieoId = decryptId($id);
            if(is_array($ieoId)) {
                return redirect()->back()->with(['dismiss' => __('Ieo not found')]);
            }
            $response = $this->ieoService->adminIeoDeleteProcess($ieoId);

            if ($response) {
                return redirect()->route('adminIeoList')->with('success', 'IEO deleted successfully.');
            } else {
                return redirect()->route('adminIeoList')->with('error', 'Failed to delete IEO.');
            }
        } catch (\Exception $e) {
            storeException('adminIeoDelete', $e->getMessage());
            return redirect()->back()->with('dismiss', __('Something went wrong'));
        }
    }

}
