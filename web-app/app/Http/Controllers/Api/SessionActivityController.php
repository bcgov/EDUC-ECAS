<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\SessionActivity;
use App\Http\Controllers\Interfaces\iApiController;

class SessionActivityController extends BaseController implements iApiController
{

    public function __construct(SessionActivity $model)
    {
        $this->model = $model;
    }



}
