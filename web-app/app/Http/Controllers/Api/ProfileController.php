<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\Profile;

class ProfileController extends BaseController
{

    public function __construct(Profile $model)
    {
        $this->model = $model;
    }



}
