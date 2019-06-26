<?php

namespace App\Http\Controllers\Fictitious;


use App\MockEntities\ProfileCredential;


class ProfileCredentialsController extends BaseController
{

    public function __construct(ProfileCredential $model)
    {
        $this->model = $model;
    }



}
