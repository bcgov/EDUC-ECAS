<?php

namespace Tests;


use App\Dynamics\Profile;
use App\Keycloak\KeycloakGuard;
use Illuminate\Foundation\Testing\DatabaseMigrations;



abstract class BaseMigrations extends TestCase
{

    use DatabaseMigrations;


    protected function mockUserId($user_id)
    {

        // mock the Keycloak Guard
        $guard = \Mockery::mock(KeycloakGuard::class);
        $guard->shouldReceive('id')
            ->once()
            ->andReturn($user_id);

        // load the mock into the IoC container
        $this->app->instance(KeycloakGuard::class, $guard);

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
     * @return array
     */
    protected function validProfileData($replace = []): array
    {
        $valid = [
            'first_name'            => 'required',
            'last_name'             => 'required',
            'preferred_first_name'  => 'bob',
            'email'                 => 'test@example.com',
            'phone'                 => '2508123353',
            'address_1'             => 'Address field 1',
            'address_2'             => 'Address field 2',
            'city'                  => 'Victoria',
            'region'                => 'BC',
            'postal_code'           => 'H0H0H0',
            'social_insurance_number'   => '123456789',
            'professional_certificate_bc'       => 'mmc-12345',
            'professional_certificate_other'    => 'qwr-12345',
            'professional_certificate_yk'       => 'yt-123456'

        ];

        return array_merge($valid, $replace);
    }



}
