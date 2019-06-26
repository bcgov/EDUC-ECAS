<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\Assignment;
use App\Http\Controllers\Interfaces\iApiController;

class AssignmentController extends BaseController implements iApiController
{

    public function __construct(Assignment $model)
    {
        $this->model = $model;
    }



}
