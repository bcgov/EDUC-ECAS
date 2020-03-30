<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected static function checkUserIsAuthorized( $keycloak_user, Array $profile)
    {

        if( ! $keycloak_user)
        {
            abort(401, 'unauthorized');
        }

        if($keycloak_user['sub'] <> $profile['federated_id'])
        {
            abort(401, 'unauthorized');
        }

        return $profile;

    }

}
