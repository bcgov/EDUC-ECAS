<?php

namespace App\Http\Controllers\Fictitious;


use App\MockEntities\SessionActivity;


class SessionActivityController extends BaseController
{

    public function __construct(SessionActivity $model)
    {
        $this->model = $model;
    }



}
