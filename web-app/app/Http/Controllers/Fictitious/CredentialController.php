<?php

namespace App\Http\Controllers\Fictitious;



use App\MockEntities\Repository\Credential;


class CredentialController extends BaseController
{

    public function __construct(Credential $model)
    {
        $this->model = $model;
    }



}
