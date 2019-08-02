<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\iModelRepository;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

abstract class BaseController extends Controller
{

    protected $model;
    protected $api_token;


    public function __construct(iModelRepository $model)
    {
        $this->model            = $model;
    }


    protected function checkOwner(Request $request, $user_id)
    {
        $api_token = explode(' ', $request->headers->get('Authorization'));

        logger('API_TOKEN: ' . $api_token[1]);
        logger('USER_ID ' . $user_id);

        $user_count = User::where('id', $user_id)
            ->where('api_token', $api_token[1])
            ->count();


        if($user_count == 0) {
            abort(302, 'unauthorized');
        }

        return true;

    }


}