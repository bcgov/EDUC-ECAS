<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\Region;
use App\Http\Controllers\Interfaces\iApiController;

class RegionController extends BaseController implements iApiController
{

    public function __construct(Region $model)
    {
        $this->model = $model;
    }



}
