<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\School;

class SchoolController extends BaseController
{

    public function __construct(School $model)
    {
        $this->model = $model;
    }



}
