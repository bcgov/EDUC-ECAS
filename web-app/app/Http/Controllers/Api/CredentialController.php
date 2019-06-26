<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\Credential;


class CredentialController extends BaseController
{

    public function __construct(Credential $model)
    {
        $this->model = $model;
    }



}
