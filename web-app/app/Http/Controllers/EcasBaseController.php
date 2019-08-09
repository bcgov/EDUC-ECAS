<?php

namespace App\Http\Controllers;



use GuzzleHttp\Exception\ClientException;
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

        $user = $this->getUserFromKeycloak($bearer_token);

        return $user['sub'] == $federated_id;


    }


    protected function getUserByToken($token)
    {

        return $this->getUserFromKeycloak($token);

    }

    protected function getUser(Request $request)
    {

        $token = $this->getBearerToken($request);

        return $this->getUserFromKeycloak($token);

    }


    protected function getBearerToken(Request $request )
    {

        $authorization_array = explode(' ', $request->headers->get('Authorization'));

        return $authorization_array[1];

    }

    private function getUserFromKeycloak($token)
    {

        return Socialite::driver('keycloak')->getUserByToken($token);

    }



}
