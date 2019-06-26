<?php

namespace App\Http\Controllers\Fictitious;



use App\MockEntities\Repository\AssignmentStatus;


class AssignmentStatusController extends BaseController
{

    public function __construct(AssignmentStatus $model)
    {
        $this->model = $model;
    }



}
