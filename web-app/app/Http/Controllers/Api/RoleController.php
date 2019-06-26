<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\Role;
use App\Http\Controllers\Interfaces\iApiController;

class RoleController extends BaseController implements iApiController
{

    public function __construct(Role $model)
    {
        $this->model = $model;
    }



}
