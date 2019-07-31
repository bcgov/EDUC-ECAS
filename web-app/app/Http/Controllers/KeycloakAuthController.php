<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

/**
 * Class KeycloakAuthController
 * @package App\Http\Controllers
 */
class KeycloakAuthController extends Controller
{

    const PROVIDER = 'keycloak';


    /**
     * Redirect to keycloak server.
     * @provider
     * @return
     */
    public function redirect()
    {

        return Socialite::driver(self::PROVIDER)
            ->stateless()
            ->scopes([]) // Array ex : name
            ->redirect();
    }


    /**
     * retrieve user information which is located at keycloak serve.
     * @provider
     * @return
     */
    public function callback()
    {

        $userData = Socialite::driver(self::PROVIDER)
            ->stateless()
            ->user();
        /* Note : */
        /* 1) Callback url is same for login and logout request. so this function executed twice. */
        /* 2) Must add below code, Because user data not retrieved while logout calls is requested. */
        if(!isset($userData->id)){
            return redirect('/logout');
        }

        $user = User::updateOrCreate(
            [
                'id'    => $userData->id
            ],
            [
                'name'          =>  $userData->name,
                'api_token'     =>  Str::random(60)
            ]
        );

        Auth::login($user, true);
        return redirect()->to('/Dashboard'); // Redirect to a secure page

    }


    /**
     * Log the user out of the application.
     */
    public function logout()
    {

        /* logout from laravel auth */
        Auth::logout();
        /* redirect to keycloak logout url */
        return $this->redirect(
            Socialite::driver(self::PROVIDER)->getLogoutUrl()
        );


    }
}