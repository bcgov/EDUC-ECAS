<?php

namespace App\Http\Controllers\Fictitious;


use App\MockEntities\Profile;


class ProfileController extends BaseController
{

    public function __construct(Profile $model)
    {
        $this->model = $model;
    }

    public function index()
    {
        return abort(404);
    }


}
