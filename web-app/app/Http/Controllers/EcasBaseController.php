<?php

namespace App\Http\Controllers;



use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

/*
 * Main Controller for the application
 */
class EcasBaseController extends Controller
{



    protected function checkOwner(Request $request, $federated_id )
    {

        $bearer_token = $this->getBearerToken($request);

        logger('API_TOKEN: ' . $bearer_token);
        logger('FEDERATED_ID ' . $federated_id);

        // TODO - We need try and catch logic here - in case the token isn't valid

        $user = Socialite::driver('keycloak')->getUserByToken($bearer_token);

        return $user['sub'] == $federated_id;


    }


    protected function getUserByToken($token)
    {

        // TODO - We need try and catch logic here - in case the token isn't valid

        return Socialite::driver('keycloak')->getUserByToken($token);

    }

    protected function getUser(Request $request)
    {

        $token = $this->getBearerToken($request);

        // TODO - We need try and catch logic here - in case the token isn't valid

        return Socialite::driver('keycloak')->getUserByToken($token);

    }


    private function getBearerToken(Request $request )
    {

        $authorization_array = explode(' ', $request->headers->get('Authorization'));

        return $authorization_array[1];

    }



}
