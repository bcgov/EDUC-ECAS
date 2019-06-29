<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\Profile;

class ProfileController extends BaseController
{

    public function __construct(Profile $model)
    {
        $this->model = $model;
    }

    public function index()
    {

        // override parent - disable the ability to return all users profiles
        abort(404);

    }



}
