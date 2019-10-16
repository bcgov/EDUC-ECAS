<?php

namespace Tests\Api;


use App\Dynamics\Country;
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
        $mock_country       = 'some_id';

        $this->mockUserId($mock_federated_id);
        $this->mockGetProfile($mock_profile_id, $this->validProfileData([
            'district_id'       => $mock_district_id,
            'school_id'         => $mock_school_id,
            'region'            => $mock_region,
            'federated_id'      => $mock_federated_id,
            'country_id'        => $mock_country
        ]));
        $this->mockDistrict($mock_district_id);
        $this->mockSchool($mock_school_id);
        $this->mockRegion($mock_region);
        $this->mockCountry($mock_country);

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
        $mock_country       = 'some_id';

        $this->mockUserId($mock_federated_id);
        $this->mockGetProfile($mock_profile_id, $this->validProfileData([
            'district_id'       => $mock_district_id,
            'school_id'         => $mock_school_id,
            'region'            => $mock_region,
            'federated_id'      => $mock_federated_id,
            'country_id'        => $mock_country
        ]));
        $this->mockDistrict($mock_district_id);
        $this->mockSchool($mock_school_id);
        $this->mockRegion($mock_region);
        $this->mockCountry($mock_country);

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
        $mock_country       = 'some_id';

        $this->mockUserId($mock_federated_id);
        $this->mockGetProfile($mock_profile_id, $this->validProfileData([
            'district_id'               => $mock_district_id,
            'school_id'                 => $mock_school_id,
            'region'                    => $mock_region,
            'federated_id'              => $mock_federated_id,
            'country_id'                => $mock_country,
            'social_insurance_number'   => ''
        ]));
        $this->mockDistrict($mock_district_id);
        $this->mockSchool($mock_school_id);
        $this->mockRegion($mock_region);
        $this->mockCountry($mock_country);

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
        $mock_country       = 'some_id';

        $this->mockUserId($mock_federated_id);
        $this->mockGetProfile($mock_profile_id, $this->validProfileData([
            'district_id'       => $mock_district_id,
            'school_id'         => $mock_school_id,
            'region'            => $mock_region,
            'federated_id'      => $mock_federated_id,
            'country_id'        => $mock_country,
        ]));
        $this->mockDistrict($mock_district_id);
        $this->mockSchool($mock_school_id);
        $this->mockRegion($mock_region);
        $this->mockCountry($mock_country);

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
    public function an_authenticated_user_cannot_update_another_users_profile()
    {
        $this->withExceptionHandling();

        $mock_profile_id    = 'abc';
        $mock_district_id   = 'cfd';
        $mock_school_id     = 'myt';
        $mock_region        = 'BC';
        $mock_federated_id  = '123';
        $mock_country_id    = 'some_guid';


        $initial_profile    = $this->validProfilePostData([
            'id'                => $mock_profile_id,
            'district_id'       => $mock_district_id,
            'school_id'         => $mock_school_id,
            'region'            => $mock_region,
            'federated_id'      => 'bad_federated_id',
            'country_id'        => $mock_country_id
        ]);


        $this->mockUser($mock_federated_id);

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
        $this->withExceptionHandling();

        $response = $this->post('/api/profiles', $this->validProfilePostData());

        $response->assertSessionDoesntHaveErrors();
    }

    /** @test */
    public function an_authenticated_user_cannot_create_a_blank_profile()
    {

        $this->withExceptionHandling();

        $this->post('/api/profiles', $this->validProfilePostData([
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

        $this->post('/api/profiles', $this->validProfilePostData(['email' => 'notanemail']))
            ->assertSessionHasErrors('email');

    }

    /** @test */
    public function a_profile_can_include_a_postal_code_with_a_space()
    {
        $this->withExceptionHandling();

        $response = $this->post('/api/profiles', $this->validProfilePostData([
            'postal_code'   => 'V8R 5N5'
        ]));

        $response->assertSessionDoesntHaveErrors();
    }

    /** @test */
    public function a_new_profile_must_have_a_postal_code()
    {
        // Valid: Six alternating letters and numbers, spaces don't matter

        $this->withExceptionHandling();

        $this->post('/api/profiles', $this->validProfilePostData(['postal_code' => '']))
            ->assertSessionHasErrors('postal_code');

    }

    /** @test */
    public function usa_profile_can_have_a_numeric_postal_code()
    {
        // Valid: Six alternating letters and numbers, spaces don't matter

        $this->withExceptionHandling();

        $response = $this->post('/api/profiles', [
            'first_name'                        => 'required',
            'last_name'                         => 'required',
            'preferred_first_name'              => 'bob',
            'email'                             => 'test@example.com',
            'phone'                             => '2508123353',
            'address_1'                         => 'Address field 1',
            'address_2'                         => 'Address field 2',
            'city'                              => 'Victoria',
            'region'                            => 'BC',
            'postal_code'                       => '12345678',
            'professional_certificate_bc'       => 'Yes',
            'professional_certificate_yk'       => 'No',
            'country'   =>  [
                'id'            =>                  'some_country_guid',
                'name'          =>                  'United States'
            ]
        ]);
        $response->assertSessionDoesntHaveErrors();
    }


    /** @test */
    public function canadian_profile_cannot_have_a_numeric_postal_code()
    {
        // Valid: Six alternating letters and numbers, spaces don't matter

        $this->withExceptionHandling();

        $response = $this->post('/api/profiles', $this->validProfilePostData([
            'postal_code'   => '123456'
        ]));
        $response->assertSessionHasErrors('postal_code');
    }


    /** @test */
    public function new_profile_sin_cannot_have_fewer_than_9_digits()
    {

        $this->withExceptionHandling();

        $data = $this->validProfilePostData(['social_insurance_number' => '125 789']);
        $response = $this->post('/api/profiles', $data);
        $response->assertSessionHasErrors('social_insurance_number');

    }

    /** @test */
    public function new_profile_sin_can_be_blank()
    {

        $data = $this->validProfilePostData(['social_insurance_number' => '']);
        $response = $this->post('/api/profiles', $data);

        $response->assertSessionDoesntHaveErrors('social_insurance_number');

    }


    /** @test */
    public function new_profile_sin_cannot_have_alpha_characters()
    {

        $this->withExceptionHandling();

        $data = $this->validProfilePostData(['social_insurance_number' => '123w4w56a']);
        $response = $this->post('/api/profiles', $data);
        $response->assertSessionHasErrors('social_insurance_number');

    }


    /** @test */
    public function new_profile_sin_must_pass_the_luhn_algorithm()
    {

        // Use: http://id-check.artega.biz/pin-ca.php to generate a fictitious SIN
        $this->withExceptionHandling();

        $data = $this->validProfilePostData(['social_insurance_number' => '783302648']);
        $response = $this->post('/api/profiles', $data);

        $response->assertSessionHasErrors('social_insurance_number');


    }



    /** @test */
    public function new_profile_sin_may_have_dashes_between_numbers()
    {

        $this->withExceptionHandling();

        $data = $this->validProfilePostData(['social_insurance_number' => '883-302-645']);
        $response = $this->post('/api/profiles', $data);

        $response->assertStatus(302);  // passed validation

    }


    /** @test */
    public function new_profile_sin_may_have_blanks_between_triplets()
    {

        $this->withExceptionHandling();

        $data = $this->validProfilePostData(['social_insurance_number' => '883 302 645']);
        $response = $this->post('/api/profiles', $data);

        $response->assertStatus(302);  // passed validation

    }



    /** @test */
    public function new_profile_sin_cannot_be_any_9_digit_number()
    {

        $this->withExceptionHandling();

        $data = $this->validProfilePostData(['social_insurance_number' => '883302645']);
        $response = $this->post('/api/profiles', $data);

        $response->assertSessionHasErrors('social_insurance_number');

    }

    


    private function mockDistrict($district_id )
    {

        // mock the District
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

        // mock the School
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

        // mock the Region
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


    private function mockCountry( $country_id )
    {

        // mock the Country
        $repository = \Mockery::mock(Country::class);
        $repository->shouldReceive('get')
            ->with($country_id)
            ->once()
            ->andReturn([
                'name'  => 'Canada',
                'id'    => $country_id
            ]);

        // load the mock into the IoC container
        $this->app->instance(Country::class, $repository);

    }


}
