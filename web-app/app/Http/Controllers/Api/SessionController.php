<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\Session;
use App\Http\Controllers\Interfaces\iApiController;

class SessionController extends BaseController implements iApiController
{

    public function __construct(Session $model)
    {
        $this->model = $model;
    }



}
