<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\Profile;
use App\Interfaces\iModelRepository;
use Illuminate\Http\Request;


class ProfileCredentialController extends BaseController
{


    public function index()
    {

        // override parent - disable the ability to return all users profiles
        abort(404);

    }



}
