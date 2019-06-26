<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\ContractStage;
use App\Http\Controllers\Interfaces\iApiController;

class ContractStageController extends BaseController implements iApiController
{

    public function __construct(ContractStage $model)
    {
        $this->model = $model;
    }



}
