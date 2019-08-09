<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\EcasBaseController;
use App\Interfaces\iModelRepository;


abstract class ApiBaseController extends EcasBaseController
{

    protected $model;


    public function __construct(iModelRepository $model)
    {
        $this->model            = $model;

    }





}