<?php

namespace App\Keycloak;


use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

/*
 * Main Controller for the application
 */
class KeycloakGuard implements Guard
{

    private $user;
    private $request;


    public function __construct(Request $request)
    {
        $this->user = null;
        $this->request = $request;
    }


    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check()
    {
        return !is_null($this->user());
    }

    /**
     * Determine if the guard has a user instance.
     *
     * @return bool
     */
    public function hasUser()
    {
        return !is_null($this->user());
    }

    /**
     * Determine if the current user is a guest.
     *
     * @return bool
     */
    public function guest()
    {
        return !$this->check();
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        $this->authenticate();
        if (is_null($this->user)) {
            return null;
        }

        return $this->user;
    }

    /**
     * Get the Federated ID for the currently authenticated user.
     *
     * @return int|null
     */
    public function id()
    {
        if ($this->user()) {
            return $this->user()->sub;
        }

        return null;
    }


    public function validate(array $credentials = [])
    {
        // This guard will not implement the validate method
        // an outside Keycloak server handles authentication
        return null;
    }


    /**
     * Set the current user.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return void
     */
    public function setUser(Authenticatable $user)
    {
        $this->user = $user;
        return;
    }


    private function authenticate()
    {

        $this->user = Socialite::driver('keycloak')->getUserByToken($this->request->bearerToken());

    }

}
