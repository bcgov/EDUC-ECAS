<?php

namespace App\Http\Controllers\Fictitious;


use App\MockEntities\Session;


class SessionController extends BaseController
{

    public function __construct(Session $model)
    {
        $this->model = $model;
    }



}
