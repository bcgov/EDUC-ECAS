<?php

namespace App\Http\Controllers\Fictitious;


use App\MockEntities\ProfileCredential;


class ProfileCredentialsController extends BaseController
{

    public function __construct(ProfileCredential $model)
    {
        $this->model = $model;
    }

    public function index()
    {
        // override parent - disable the ability to return credentials for all users
        return abort(404);
    }



}
