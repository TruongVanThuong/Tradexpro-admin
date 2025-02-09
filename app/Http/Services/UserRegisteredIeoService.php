<?php
namespace App\Http\Services;

use App\Model\IeoModel;
use App\Model\UserRegisteredIeo;
use Exception;

class UserRegisteredIeoService extends BaseService
{
    protected $model;

    public function __construct()
    {
        $this->model = new UserRegisteredIeo();
    }

    public function getUserRegisteredIeoDetailsById($registeredIeoId)
    {
        try {
            $UserRegisteredIeo = UserRegisteredIeo::leftJoin('users', 'user_registered_ieo.user_id', '=', 'users.id')
            ->leftJoin('ieo', 'user_registered_ieo.ieo_id', '=', 'ieo.id')
            ->select('user_registered_ieo.id','user_registered_ieo.rating_win', 'users.email as email', 'ieo.name as ieo_name')
            ->where('user_registered_ieo.id', $registeredIeoId)
            ->first();

            $UserRegisteredIeo->rating_win = rtrim(number_format($UserRegisteredIeo->rating_win, 6), '0');
            $UserRegisteredIeo->rating_win = rtrim($UserRegisteredIeo->rating_win, '.');

            if ($UserRegisteredIeo) {
                return [
                    'success' => true,
                    'data' => $UserRegisteredIeo,
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

}
