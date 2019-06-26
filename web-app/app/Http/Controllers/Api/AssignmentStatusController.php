<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\AssignmentStatus;
use App\Http\Controllers\Interfaces\iApiController;

class AssignmentStatusController extends BaseController implements iApiController
{

    public function __construct(AssignmentStatus $model)
    {
        $this->model = $model;
    }



}
