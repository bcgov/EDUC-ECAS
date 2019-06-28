<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\SessionType;
use App\Http\Controllers\Interfaces\iApiController;

class SessionTypeController extends BaseController implements iApiController
{

    public function __construct(SessionType $model)
    {
        $this->model = $model;
    }



}
