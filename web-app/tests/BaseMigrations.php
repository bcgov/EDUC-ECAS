<?php

namespace Tests;


use App\Dynamics\Profile;
use App\Keycloak\KeycloakGuard;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Socialite\Facades\Socialite;



abstract class BaseMigrations extends TestCase
{

    use DatabaseMigrations;

    protected $ecas_api_requests = [];



    protected function mockUser($user_id)
    {

        // mock the Keycloak Guard
        $guard = \Mockery::mock(KeycloakGuard::class);
        $guard->shouldReceive('user')
            ->once()
            ->andReturn([
                'sub'                   => $user_id,
                'federated_id'          => $user_id,
                'username'              => 'some_bceid',
                'preferred_username'    => 'some_bceid'
            ]);

        // load the mock into the IoC container
        $this->app->instance(KeycloakGuard::class, $guard);

    }


    protected function mockUnauthenticatedUser()
    {

        // mock the Keycloak Guard
        $guard = \Mockery::mock(KeycloakGuard::class);
        $guard->shouldReceive('user')
            ->once()
            ->andReturnNull();

        // load the mock into the IoC container
        $this->app->instance(KeycloakGuard::class, $guard);

    }


    /*
     * Keycloak's userinfo endpoint rejects a missing or expired token, which the real guard handles
     */
    protected function mockKeycloakRejectingToken()
    {
        Socialite::shouldReceive('driver->getUserByToken')
            ->andThrow(new ClientException('401 Unauthorized', new Request('GET', 'userinfo'), new Response(401)));
    }


    /*
     * Replace the Guzzle clients used to call ecas-api, recording each request
     */
    protected function mockEcasApi(array $responses = [])
    {
        $handler = HandlerStack::create(new MockHandler($responses));
        $handler->push(Middleware::history($this->ecas_api_requests));

        $client = new Client(['handler' => $handler]);

        $this->app->instance(Client::class, $client);
        $this->app->instance(ClientInterface::class, $client);
    }



    protected function mockGetProfile($profile_id, Array $data = [] )
    {

        $data['id']     = $profile_id;

        // mock the Profile
        $repository = \Mockery::mock(Profile::class);
        $repository->shouldReceive('get')
            ->with($profile_id)
            ->once()
            ->andReturn($data);

        // load the mock into the IoC container
        $this->app->instance(Profile::class, $repository);

    }


    /**
     * @param array $replace
     * @return array
     */
    protected function validProfileData($replace = []): array
    {
        $valid = [
            'first_name'                        => 'required',
            'last_name'                         => 'required',
            'preferred_first_name'              => 'bob',
            'email'                             => 'test@example.com',
            'phone'                             => '2508123353',
            'address_1'                         => 'Address field 1',
            'address_2'                         => 'Address field 2',
            'city'                              => 'Victoria',
            'region'                            => 'BC',
            'postal_code'                       => 'H0H0H0',
            'social_insurance_number'           => '766 417 596',
            'professional_certificate_bc'       => 'Yes',
            'professional_certificate_yk'       => 'No',
            'country_id'                        => 'some_guid'

        ];

        return array_merge($valid, $replace);
    }

    /**
     * @param array $replace
     * @return array
     */
    protected function validProfilePostData($replace = []): array
    {
        $valid = [
            'first_name'                        => 'required',
            'last_name'                         => 'required',
            'preferred_first_name'              => 'bob',
            'email'                             => 'test@example.com',
            'phone'                             => '2508123353',
            'address_1'                         => 'Address field 1',
            'address_2'                         => 'Address field 2',
            'city'                              => 'Victoria',
            'region'                            => 'BC',
            'postal_code'                       => 'H0H0H0',
            'social_insurance_number'           => '766 417 596',
            'professional_certificate_bc'       => 'Yes',
            'professional_certificate_yk'       => 'No',
            'country'                           => [
                'id'        =>  'some_guid',
                'name'      =>  'Canada'
            ]

        ];

        return array_merge($valid, $replace);
    }


}
