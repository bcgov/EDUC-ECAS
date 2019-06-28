<?php

namespace App\Http\Controllers\Fictitious;


use App\MockEntities\SessionType;


class SessionTypeController extends BaseController
{

    public function __construct(SessionType $model)
    {
        $this->model = $model;
    }



}
