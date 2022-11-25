<?php

namespace App\Keycloak;


use Illuminate\Support\Arr;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;


class KeycloakProvider extends AbstractProvider
{

    /**
     * Unique Provider Identifier.
     */
    const IDENTIFIER = 'KEYCLOAK';

    /**
     * {@inheritdoc}
     */
    protected $scopes = ['openid', 'profile', 'email'];

    /**
     * {@inheritdoc}
     */
    protected $scopeSeparator = ' ';



    /**
     * Get the authentication URL for the provider.
     *
     * @param  string $state
     * @return string
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(self::getBaseUrlWithRealm() . '/auth&kc_idp_hint=basic-bceid', $state);

    }

    /**
     * Get the token URL for the provider.
     *
     * @return string
     */
    protected function getTokenUrl()
    {
        return self::getBaseUrlWithRealm() . '/token';
    }

    /**
     * Get the POST fields for the token request.
     *
     * @param  string  $code
     * @return array
     */
    protected function getTokenFields($code)
    {
        return Arr::add(
            parent::getTokenFields($code), 'grant_type', 'authorization_code'
        );
    }


    /**
     * {@inheritdoc}
     */
    public function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(self::getBaseUrlWithRealm() . '/userinfo', [
            'query' => [
                'prettyPrint' => 'false',
            ],
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$token,
            ],
        ]);


        return json_decode($response->getBody(), true);
    }

    /**
     * Map the raw user array to a Socialite User instance.
     *
     * @param  array $user
     * @return \Laravel\Socialite\Two\User
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->map([
            'id'            => $user['sub'],
            'first_name'    => $user['given_name'],
            'last_name'     => $user['family_name'],
            'email'         => $user['email'] ? $user['email'] : null,
        ]);
    }


    private static function getBaseUrlWithRealm()
    {

        return  env('KEYCLOAK_AUTHSERVERURL').'/realms/'.env('KEYCLOAK_REALM') . '/protocol/openid-connect' ;

    }
}
