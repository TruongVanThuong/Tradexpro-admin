<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Services\IeoService;
use App\Model\IeoModel;

class IeoController extends Controller
{
    private $service;

    public function __construct()
    {
        $this->service = new IeoService();
    }

    public function getIeoList(){
        $coins = $this->service->getIeo();
        return response()->json(['success'=>true,'data'=>$coins,'message'=>__('All Coins')]);
    }

    public function getIeoUserRegistered() {
        return $this->service->getIeoUserRegistered();
    }

    public function registerIeo(Request $request)
    {
        $result = $this->service->registerIeo($request['ieo_id'], $request['amount']);

        if ($result['success']) {
            return response()->json(['message' => $result['message'], 'success' => true]);
        } else {
            return response()->json(['message' => $result['message'], 'success' => false]);
        }
    }

    public function receiveIeo(Request $request)
    {
        $result = $this->service->receiveIeo($request['itemId']);

        if ($result['success']) {
            return response()->json([
                'message' => $result['message'],
                'success' => true
            ]);
        } else {
            return response()->json([
                'message' => $result['message'],
                'success' => false,
            ]);
        }
    }

    public function receiveIeoWallet(Request $request) {
        $result = $this->service->receiveIeoWallet($request['itemId']);

        if ($result['success']) {
            return response()->json([
                'message' => $result['message'],
                'success' => true
            ]);
        } else {
            return response()->json([
                'message' => $result['message'],
                'success' => false,
            ]);
        }
    }

    public function getIeoTransactionHistory(Request $request) {
        return $this->service->getIeoTransactionHistory($request);
    }

}
