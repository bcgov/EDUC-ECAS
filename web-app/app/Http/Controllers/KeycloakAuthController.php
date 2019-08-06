<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
    public function callback(Request $request)
    {

        $userData = Socialite::driver(self::PROVIDER)
            ->stateless()
            ->user();

        // store the user object with token in a session
        $request->session()->put('token', $userData->token);

        return redirect()->to('/Dashboard');

    }


    /**
     * Log the user out of the application.
     */
    public function logout()
    {

        // TODO - destroy the session

        /* redirect to keycloak logout url */
        return $this->redirect(
            Socialite::driver(self::PROVIDER)->getLogoutUrl()
        );


    }
}