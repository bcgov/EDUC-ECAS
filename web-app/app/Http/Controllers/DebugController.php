<?php

namespace App\Http\Controllers;

use App\MockEntities\Credential;


class DebugController
{

    private $model;

    public function __construct(Credential $model)
    {
        $this->model = $model;
    }


    public function index()
    {
        return $this->model->all();
    }


}
