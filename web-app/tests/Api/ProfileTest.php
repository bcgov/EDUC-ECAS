<?php

namespace Tests\Api;


use App\Dynamics\District;
use App\Dynamics\Profile;
use App\Dynamics\Region;
use App\Dynamics\School;
use Tests\BaseMigrations;


class ProfileTest extends BaseMigrations
{


    /** @test */
    public function an_authenticated_user_can_get_their_own_profile()
    {
        $mock_profile_id    = 'abc';
        $mock_district_id   = 'cfd';
        $mock_school_id     = 'myt';
        $mock_region        = 'BC';
        $mock_federated_id  = '123';

        $this->mockUserId($mock_federated_id);
        $this->mockGetProfile($mock_profile_id, $this->validProfileData([
            'district_id'       => $mock_district_id,
            'school_id'         => $mock_school_id,
            'region'            => $mock_region,
            'federated_id'      => $mock_federated_id
        ]));
        $this->mockDistrict($mock_district_id);
        $this->mockSchool($mock_school_id);
        $this->mockRegion($mock_region);

        $response = $this->get('/api/profiles/' . $mock_profile_id);
        $response->assertJsonFragment(['first_name' => $this->validProfileData()['first_name']]);
        $response->assertJsonCount(1);
    }



    /** @test */
    public function an_unauthenticated_user_cannot_get_a_profile()
    {
        $this->withExceptionHandling();

        $this->mockUserId(null);

        $response = $this->get('/api/profiles/' . 'does_not_matter' );
        $response->assertStatus(401); // unauthorized

    }


    /** @test */
    public function a_valid_SIN_number_is_never_echoed_back_to_users()
    {


        $mock_profile_id    = 'abc';
        $mock_district_id   = 'cfd';
        $mock_school_id     = 'myt';
        $mock_region        = 'BC';
        $mock_federated_id  = '123';

        $this->mockUserId($mock_federated_id);
        $this->mockGetProfile($mock_profile_id, $this->validProfileData([
            'district_id'       => $mock_district_id,
            'school_id'         => $mock_school_id,
            'region'            => $mock_region,
            'federated_id'      => $mock_federated_id
        ]));
        $this->mockDistrict($mock_district_id);
        $this->mockSchool($mock_school_id);
        $this->mockRegion($mock_region);

        $response = $this->get('/api/profiles/' . $mock_profile_id);
        $response->assertJsonMissing(['social_insurance_number']);

    }


    /** @test */
    public function an_empty_SIN_number_is_not_shown_as_received()
    {
        $mock_profile_id    = 'abc';
        $mock_district_id   = 'cfd';
        $mock_school_id     = 'myt';
        $mock_region        = 'BC';
        $mock_federated_id  = '123';

        $this->mockUserId($mock_federated_id);
        $this->mockGetProfile($mock_profile_id, $this->validProfileData([
            'district_id'               => $mock_district_id,
            'school_id'                 => $mock_school_id,
            'region'                    => $mock_region,
            'federated_id'              => $mock_federated_id,
            'social_insurance_number'   => ''
        ]));
        $this->mockDistrict($mock_district_id);
        $this->mockSchool($mock_school_id);
        $this->mockRegion($mock_region);

        $response = $this->get('/api/profiles/' . $mock_profile_id);

        $response->assertJsonFragment(['is_SIN_on_file' => FALSE]);

    }


    /** @test */
    public function a_SIN_number_is_shown_as_received()
    {

        $mock_profile_id    = 'abc';
        $mock_district_id   = 'cfd';
        $mock_school_id     = 'myt';
        $mock_region        = 'BC';
        $mock_federated_id  = '123';

        $this->mockUserId($mock_federated_id);
        $this->mockGetProfile($mock_profile_id, $this->validProfileData([
            'district_id'       => $mock_district_id,
            'school_id'         => $mock_school_id,
            'region'            => $mock_region,
            'federated_id'      => $mock_federated_id
        ]));
        $this->mockDistrict($mock_district_id);
        $this->mockSchool($mock_school_id);
        $this->mockRegion($mock_region);

        $response = $this->get('/api/profiles/' . $mock_profile_id);

        $response->assertJsonFragment(['is_SIN_on_file' => TRUE]);

    }


    /** @test */
    public function the_index_method_on_the_profile_controller_is_blocked()
    {
        $this->withExceptionHandling();



        $response = $this->get('/api/profiles');
        $response->assertStatus(405); // route not available
    }


    /** @test */
    public function an_authenticated_user_can_update_their_own_profile()
    {

        $mock_profile_id    = 'abc';
        $mock_district_id   = 'cfd';
        $mock_school_id     = 'myt';
        $mock_region        = 'BC';
        $mock_federated_id  = '123';


        $initial_profile    = $this->validProfileData([
            'id'                => $mock_profile_id,
            'district_id'       => $mock_district_id,
            'school_id'         => $mock_school_id,
            'region'            => $mock_region,
            'federated_id'      => $mock_federated_id
        ]);

        $modified_profile   = $this->validProfileData([
            'first_name'    => 'New Name',
            'id'                => $mock_profile_id,
            'district_id'       => $mock_district_id,
            'school_id'         => $mock_school_id,
            'region'            => $mock_region,
            'federated_id'      => $mock_federated_id
        ]);


        $this->mockUserId($mock_federated_id);

        $this->mockUpdateProfile($mock_profile_id, $initial_profile, $modified_profile );


        $response = $this->patch('/api/profiles/' . $mock_profile_id, $modified_profile );

        $response
            ->assertStatus(200)
            ->assertJsonFragment([
                'first_name'    => "New Name"
            ]);
    }


    /** @test */
    public function an_authenticated_user_cannot_update_another_users_profile()
    {
        $this->withExceptionHandling();

        $mock_profile_id    = 'abc';
        $mock_district_id   = 'cfd';
        $mock_school_id     = 'myt';
        $mock_region        = 'BC';
        $mock_federated_id  = '123';


        $initial_profile    = $this->validProfileData([
            'id'                => $mock_profile_id,
            'district_id'       => $mock_district_id,
            'school_id'         => $mock_school_id,
            'region'            => $mock_region,
            'federated_id'      => 'bad_federated_id'
        ]);


        $this->mockUserId($mock_federated_id);

        $this->mockGetProfile($mock_profile_id, $initial_profile );

        $initial_profile['federated_id'] = $mock_federated_id;


        $response = $this->patch('/api/profiles/' . $mock_profile_id, $initial_profile );


        $response->assertStatus(401);
    }


    /** @test */
    public function the_delete_profile_method_is_disabled()
    {

        $response = $this->delete('/api/profiles/' . 'any_profile_id' );

        $response->assertStatus(404);
    }


    /** @test */
    public function an_authenticated_user_can_create_profile()
    {
        $mock_profile_id    = 'abc';
        $mock_district_id   = 'cfd';
        $mock_school_id     = 'myt';
        $mock_region        = 'BC';
        $mock_federated_id  = '123';


        $valid_profile          = $this->validProfileData([
            'id'                => $mock_profile_id,
            'district_id'       => $mock_district_id,
            'school_id'         => $mock_school_id,
            'region'            => $mock_region,
            'federated_id'      => $mock_federated_id
        ]);


        $this->mockUserId($mock_federated_id);

        $this->mockCreateProfile( $valid_profile );

        $this->mockDistrict($mock_district_id);
        $this->mockSchool($mock_school_id);
        $this->mockRegion($mock_region);


        $valid_profile['federated_id'] = $mock_federated_id;

        $response = $this->post('/api/profiles', $valid_profile );

        $response->assertOk();
    }

    /** @test */
    public function an_authenticated_user_cannot_create_a_blank_profile()
    {

        $this->withExceptionHandling();

        $this->post('/api/profiles', $this->validProfileData([
            'first_name'  => '',
            'last_name'   => '',
            'email'       => '',
            'phone'       => '',
            'address_1'   => '',
            'city'        => '',
            'region'      => '',
            'postal_code' => ''
        ]))
            ->assertSessionHasErrors('first_name')
            ->assertSessionHasErrors('last_name')
            ->assertSessionHasErrors('email')
            ->assertSessionHasErrors('phone')
            ->assertSessionHasErrors('address_1')
            ->assertSessionHasErrors('city')
            ->assertSessionHasErrors('region')
            ->assertSessionHasErrors('postal_code');
    }

    /** @test */
    public function an_authenticated_user_cannot_create_a_profile_without_a_valid_email()
    {

        $this->withExceptionHandling();

        $this->post('/api/profiles', $this->validProfileData(['email' => 'notanemail']))
            ->assertSessionHasErrors('email');

    }

    /** @test */
    public function an_authenticated_user_can_create_a_profile_with_postal_code_that_includes_a_space()
    {
        // Valid: Six alternating letters and numbers, spaces don't matter
        $mock_profile_id    = 'abc';
        $mock_district_id   = 'cfd';
        $mock_school_id     = 'myt';
        $mock_region        = 'BC';
        $mock_federated_id  = '123';


        $valid_profile          = $this->validProfileData([
            'id'                => $mock_profile_id,
            'district_id'       => $mock_district_id,
            'school_id'         => $mock_school_id,
            'region'            => $mock_region,
            'federated_id'      => $mock_federated_id,
            'postal_code'       => 'V7W 1J7'
        ]);


        $this->mockUserId($mock_federated_id);

        $this->mockCreateProfile( $valid_profile );

        $this->mockDistrict($mock_district_id);
        $this->mockSchool($mock_school_id);
        $this->mockRegion($mock_region);


        $valid_profile['federated_id'] = $mock_federated_id;

        $response = $this->post('/api/profiles', $valid_profile );

        $response->assertOk();
    }

    /** @test */
    public function a_new_profile_must_have_a_postal_code()
    {
        // Valid: Six alternating letters and numbers, spaces don't matter

        $this->withExceptionHandling();

        $this->post('/api/profiles', $this->validProfileData(['postal_code' => '']))
            ->assertSessionHasErrors('postal_code');

    }


    /** @test */
    public function new_profile_cannot_have_a_numeric_postal_code()
    {
        // Valid: Six alternating letters and numbers, spaces don't matter

        $this->withExceptionHandling();

        $this->post('/api/profiles', $this->validProfileData(['postal_code' => '123456']))
            ->assertSessionHasErrors('postal_code');
    }


    /** @test */
    public function new_profile_sin_cannot_have_fewer_than_9_digits()
    {

        $this->withExceptionHandling();

        $data = $this->validProfileData(['social_insurance_number' => '125 789']);
        $response = $this->post('/api/profiles', $data);
        $response->assertSessionHasErrors('social_insurance_number');

    }

    /** @test */
    public function new_profile_sin_can_be_blank()
    {
        $mock_profile_id    = 'abc';
        $mock_district_id   = 'cfd';
        $mock_school_id     = 'myt';
        $mock_region        = 'BC';
        $mock_federated_id  = '123';


        $valid_profile          = $this->validProfileData([
            'id'                            => $mock_profile_id,
            'district_id'                   => $mock_district_id,
            'school_id'                     => $mock_school_id,
            'region'                        => $mock_region,
            'federated_id'                  => $mock_federated_id,
            'social_insurance_number'       => ''
        ]);


        $this->mockUserId($mock_federated_id);

        $this->mockCreateProfile( $valid_profile );

        $this->mockDistrict($mock_district_id);
        $this->mockSchool($mock_school_id);
        $this->mockRegion($mock_region);


        $valid_profile['federated_id'] = $mock_federated_id;

        $response = $this->post('/api/profiles', $valid_profile);
        $response->assertOk();

    }


    /** @test */
    public function new_profile_sin_cannot_have_alpha_characters()
    {

        $this->withExceptionHandling();

        $data = $this->validProfileData(['social_insurance_number' => '123w4w56a']);
        $response = $this->post('/api/profiles', $data);
        $response->assertSessionHasErrors('social_insurance_number');

    }


    /** @test */
    public function new_profile_sin_must_pass_the_luhn_algorithm()
    {

        // Use: http://id-check.artega.biz/pin-ca.php to generate a fictitious SIN
        $mock_profile_id    = 'abc';
        $mock_district_id   = 'cfd';
        $mock_school_id     = 'myt';
        $mock_region        = 'BC';
        $mock_federated_id  = '123';


        $valid_profile          = $this->validProfileData([
            'id'                            => $mock_profile_id,
            'district_id'                   => $mock_district_id,
            'school_id'                     => $mock_school_id,
            'region'                        => $mock_region,
            'federated_id'                  => $mock_federated_id,
            'social_insurance_number'       => '783302649'
        ]);


        $this->mockUserId($mock_federated_id);

        $this->mockCreateProfile( $valid_profile );

        $this->mockDistrict($mock_district_id);
        $this->mockSchool($mock_school_id);
        $this->mockRegion($mock_region);


        $valid_profile['federated_id'] = $mock_federated_id;

        $response = $this->post('/api/profiles', $valid_profile);
        $response->assertOk();

    }



    /** @test */
    public function new_profile_sin_may_not_have_dashes_between_numbers()
    {

        $this->withExceptionHandling();

        $data = $this->validProfileData(['social_insurance_number' => '783-302-649']);
        $response = $this->post('/api/profiles', $data);
        $response->assertSessionHasErrors('social_insurance_number');

    }


    /** @test */
    public function new_profile_sin_may_have_blanks_between_triplets()
    {

        $mock_profile_id    = 'abc';
        $mock_district_id   = 'cfd';
        $mock_school_id     = 'myt';
        $mock_region        = 'BC';
        $mock_federated_id  = '123';


        $valid_profile          = $this->validProfileData([
            'id'                            => $mock_profile_id,
            'district_id'                   => $mock_district_id,
            'school_id'                     => $mock_school_id,
            'region'                        => $mock_region,
            'federated_id'                  => $mock_federated_id,
            'social_insurance_number'       => '783 302 649'
        ]);


        $this->mockUserId($mock_federated_id);

        $this->mockCreateProfile( $valid_profile );

        $this->mockDistrict($mock_district_id);
        $this->mockSchool($mock_school_id);
        $this->mockRegion($mock_region);


        $valid_profile['federated_id'] = $mock_federated_id;

        $response = $this->post('/api/profiles', $valid_profile);
        $response->assertOk();

    }



    /** @test */
    public function new_profile_sin_cannot_be_any_9_digit_number()
    {
        $mock_federated_id  = '123';
        $this->mockUserId($mock_federated_id);

        $this->withExceptionHandling();

        $data = $this->validProfileData(['social_insurance_number' => '883302649']);
        $response = $this->post('/api/profiles', $data);
        $response->assertSessionHasErrors('social_insurance_number');

    }



    private function mockUpdateProfile($profile_id, Array $initial_data, Array $modified_data )
    {

        // mock the Profile
        $repository = \Mockery::mock(Profile::class);

        $repository->shouldReceive('get')
            ->with($profile_id)
            ->andReturn($initial_data)
            ->once()
            ->ordered();

        $repository->shouldReceive('update')
            ->with($profile_id, $modified_data)
            ->andReturn($modified_data)
            ->once()
            ->ordered();


        // load the mock into the IoC container
        $this->app->instance(Profile::class, $repository);

    }

    private function mockCreateProfile(Array $data )
    {

        $new_data       = $data;
        $new_data['id'] = 'some_new_id';

        // mock the Profile
        $repository = \Mockery::mock(Profile::class);

        $repository->shouldReceive('create')
            ->with($data)
            ->andReturn($new_data['id'])
            ->once()
            ->ordered();

        $repository->shouldReceive('get')
            ->with($new_data['id'])
            ->andReturn($new_data)
            ->once()
            ->ordered();

        // load the mock into the IoC container
        $this->app->instance(Profile::class, $repository);

    }


    private function mockDistrict($district_id )
    {

        // mock the Profile
        $repository = \Mockery::mock(District::class);
        $repository->shouldReceive('get')
            ->with($district_id)
            ->once()
            ->andReturn([
                'name'  => 'District',
                'id'    => $district_id
            ]);

        // load the mock into the IoC container
        $this->app->instance(District::class, $repository);

    }

    private function mockSchool( $school_id )
    {

        // mock the Profile
        $repository = \Mockery::mock(School::class);
        $repository->shouldReceive('get')
            ->with($school_id)
            ->once()
            ->andReturn([
                'name'  => 'School',
                'id'    => $school_id,
                'city'  => 'City'
            ]);

        // load the mock into the IoC container
        $this->app->instance(School::class, $repository);

    }

    private function mockRegion( $region )
    {

        // mock the Profile
        $repository = \Mockery::mock(Region::class);
        $repository->shouldReceive('get')
            ->with($region)
            ->once()
            ->andReturn([
                'name'  => 'region',
                'id'    => $region
            ]);

        // load the mock into the IoC container
        $this->app->instance(Region::class, $repository);

    }


}
