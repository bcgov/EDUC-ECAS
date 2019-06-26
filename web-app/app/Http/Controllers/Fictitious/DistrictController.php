<?php

namespace App\Http\Controllers\Fictitious;


use App\MockEntities\District;


class DistrictController extends BaseController
{

    public function __construct(District $model)
    {
        $this->model = $model;
    }



}
