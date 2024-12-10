<?php

namespace App\Http\Repositories;


use App\Model\Coin;

class AdminIeoRepository extends CommonRepository
{
    public $model;
    public function __construct($model)
    {
        $this->model = $model;
    }

    public function getIeoDetailsById($ieoId)
    {
        return $this->model->find($ieoId);
    }

}
