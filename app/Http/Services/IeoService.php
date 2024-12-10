<?php
namespace App\Http\Services;

use App\Model\IeoModel;
use App\Http\Repositories\AdminIeoRepository;
use Exception;

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

}
