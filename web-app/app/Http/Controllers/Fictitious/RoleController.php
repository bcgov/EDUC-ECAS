<?php

namespace App\Http\Controllers\Fictitious;


use App\MockEntities\Role;


class RoleController extends BaseController
{

    public function __construct(Role $model)
    {
        $this->model = $model;
    }



}
