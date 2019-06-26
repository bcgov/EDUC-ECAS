<?php

namespace App\Http\Controllers\Fictitious;


use App\MockEntities\Region;


class RegionController extends BaseController
{

    public function __construct(Region $model)
    {
        $this->model = $model;
    }



}
