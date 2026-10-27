<?php

namespace App\Http\Controllers;

use App\Keycloak\KeycloakGuard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected static function loggedInUser(KeycloakGuard $authentication)
    {
        $keycloak_user = $authentication->user();

        if( ! $keycloak_user)
        {
            abort(401, 'unauthorized');
        }

        return $keycloak_user;
    }

    protected static function checkUserIsAuthorized( $keycloak_user, Array $profile)
    {

        if( ! $keycloak_user)
        {
            abort(401, 'unauthorized');
        }

        $user_federated_id      = $keycloak_user['federated_id'] ?? null;
        $profile_federated_id   = $profile['federated_id'] ?? null;

        // an empty or missing id must never match, e.g. a profile created without one
        if( ! is_string($user_federated_id) || $user_federated_id === '' || $user_federated_id !== $profile_federated_id)
        {
            abort(401, 'unauthorized');
        }

        return $profile;

    }

}
