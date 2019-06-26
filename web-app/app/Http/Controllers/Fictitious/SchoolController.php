<?php

namespace App\Http\Controllers\Fictitious;


use App\MockEntities\School;


class SchoolController extends BaseController
{

    public function __construct(School $model)
    {
        $this->model = $model;
    }



}
