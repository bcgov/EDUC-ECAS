<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;

class KeycloakConfigController extends Controller
{
    public function index()
    {

        return [
            'realm'     => env('KEYCLOAK_REALM'),
            'url'       => env('KEYCLOAK_AUTHSERVERURL'),
            'clientId'  => env('KEYCLOAK_CLIENTID')
        ];



    }
}
