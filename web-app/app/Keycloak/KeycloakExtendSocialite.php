<?php

namespace App\Keycloak;

use SocialiteProviders\Manager\SocialiteWasCalled;

class KeycloakExtendSocialite
{
    /**
     * Register the provider.
     *
     * @param \SocialiteProviders\Manager\SocialiteWasCalled $socialiteWasCalled
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {

        $socialiteWasCalled->extendSocialite(
            'keycloak', KeycloakProvider::class
        );
    }
}