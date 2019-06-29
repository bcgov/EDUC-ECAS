<?php

namespace App\Http\Controllers\Fictitious;



use App\MockEntities\Repository\ContractStage;


class ContractStageController extends BaseController
{

    public function __construct(ContractStage $model)
    {
        $this->model = $model;
    }



}
