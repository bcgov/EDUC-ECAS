<?php

namespace Tests\Api;

use App\MockEntities\Profile;
use Tests\BaseMigrations;


class ProfileTest extends BaseMigrations
{

    private $profile;
    private $user;

    public function setUp() : void
    {
        parent::setUp();
        $this->user = factory(\App\User::class)->create();
        factory(\App\MockEntities\School::class, 50)->create();
        factory(\App\MockEntities\District::class, 50)->create();
        $this->profile = Factory(Profile::class)->create([
            'federated_id'   => $this->user->id
        ]);

    }

    /** @test */
    public function an_authenticated_user_can_get_their_own_profile()
    {
        $this->actingAs($this->user, 'api');
        $response = $this->get('/api/profiles/' . $this->user->id );
        $response->assertJsonFragment(['first_name' => $this->profile->first_name]);
        $response->assertJsonCount(1);
    }



    /** @test */
    public function an_authenticated_user_cannot_get_another_users_profile()
    {
        $this->withExceptionHandling();

        $this->actingAs($this->user, 'api');
        $other_user = factory(\App\User::class)->create();
        $response = $this->get('/api/profiles/' . $other_user->id );
        $response->assertStatus(302); // unauthorized

    }


    /** @test */
    public function a_valid_SIN_number_is_never_echoed_back_to_users()
    {


        $this->actingAs($this->user, 'api');
        $response = $this->get('/api/profiles/' . $this->user->id );
        $response->assertJsonMissing(['social_insurance_number']);

    }


    /** @test */
    public function an_empty_SIN_number_is_not_shown_as_received()
    {
        $temp_user = factory(\App\User::class)->create();
        Factory(Profile::class)->create([
            'federated_id'              => $temp_user->id,
            'social_insurance_number'   => ''
        ]);

        $this->actingAs($temp_user, 'api');

        $response = $this->get('/api/profiles/' . $temp_user->id );

        $response->assertJsonFragment(['is_SIN_on_file' => FALSE]);

    }


    /** @test */
    public function a_SIN_number_is_shown_as_received()
    {


        $this->actingAs($this->user, 'api');

        $response = $this->get('/api/profiles/' . $this->user->id );

       // dd($temp_profile, $response);

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
        $this->actingAs($this->user, 'api');



        $new_data = $this->profile;
        $new_data->first_name = 'newValue';
        $new_data->social_insurance_number = '';

        $response = $this->put('/api/profiles/' . $this->user->id, $new_data->toArray() );

        $response
            ->assertStatus(200)
            ->assertJsonFragment(['first_name' => 'newValue']);
    }


    /** @test */
    public function an_authenticated_user_cannot_update_another_users_profile()
    {
        $this->withExceptionHandling();

        $this->actingAs($this->user, 'api');
        $other_user = factory(\App\User::class)->create();

        $new_data = $this->profile;
        $new_data->social_insurance_number = '';

        $response = $this->put('/api/profiles/' . $other_user->id, $new_data->toArray() );

        $response->assertStatus(302);
    }


    /** @test */
    public function an_authenticated_user_cannot_delete_their_own_profile()
    {
        $this->actingAs($this->user, 'api');
        $response = $this->delete('/api/profiles/' . $this->user->id );

        $response->assertStatus(401);
    }


    /** @test */
    public function an_authenticated_user_can_create_profile()
    {
        $this->actingAs($this->user, 'api');

        $response = $this->post('/api/profiles', $this->validProfileData());

        $response->assertOk();
    }

    /** @test */
    public function an_authenticated_user_cannot_create_a_blank_profile()
    {
        $this->actingAs($this->user, 'api');

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
        $this->actingAs($this->user, 'api');
        $this->withExceptionHandling();

        $this->post('/api/profiles', $this->validProfileData(['email' => 'notanemail']))
            ->assertSessionHasErrors('email');

        $this->post('/api/profiles', $this->validProfileData(['email' => 'valid@test.com']))
            ->assertOk();
    }

    /** @test */
    public function an_authenticated_user_can_create_a_profile_with_postal_code_that_includes_a_space()
    {
        // Valid: Six alternating letters and numbers, spaces don't matter
        $this->actingAs($this->user, 'api');

        $this->post('/api/profiles', $this->validProfileData(['postal_code' => 'V8V 1J6']))
            ->assertOk();

    }

    /** @test */
    public function new_profile_needs_valid_postal_code()
    {
        // Valid: Six alternating letters and numbers, spaces don't matter
        $this->actingAs($this->user, 'api');

        $this->post('/api/profiles', $this->validProfileData(['postal_code' => 'V8V1J6']))
            ->assertOk();
    }


    /** @test */
    public function new_profile_cannot_have_a_numeric_postal_code()
    {
        // Valid: Six alternating letters and numbers, spaces don't matter
        $this->actingAs($this->user, 'api');

        $this->withExceptionHandling();

        $this->post('/api/profiles', $this->validProfileData(['postal_code' => '123456']))
            ->assertSessionHasErrors('postal_code');
    }


    /** @test */
    public function new_profile_sin_cannot_have_only_6_digits()
    {
        $this->actingAs($this->user, 'api');

        $this->withExceptionHandling();

        $data = $this->validProfileData(['social_insurance_number' => '125 789']);
        $response = $this->post('/api/profiles', $data);
        $response->assertSessionHasErrors('social_insurance_number');

    }

    /** @test */
    public function new_profile_sin_can_be_blank()
    {
        $this->actingAs($this->user, 'api');

        $data = $this->validProfileData(['social_insurance_number' => '']);
        $response = $this->post('/api/profiles', $data);
        $response->assertOk();

    }


    /** @test */
    public function new_profile_sin_cannot_have_alpha_characters()
    {

        $this->actingAs($this->user, 'api');

        $this->withExceptionHandling();

        $data = $this->validProfileData(['social_insurance_number' => '123w4w56a']);
        $response = $this->post('/api/profiles', $data);
        $response->assertSessionHasErrors('social_insurance_number');

    }


    /** @test */
    public function new_profile_sin_must_pass_the_luhn_algorithm()
    {

        // Use: http://id-check.artega.biz/pin-ca.php to generate a fictitious SIN
        $this->actingAs($this->user, 'api');

        $this->withExceptionHandling();

        $data = $this->validProfileData(['social_insurance_number' => '783302649']);
        $response = $this->post('/api/profiles', $data);
        $response->assertOk();

    }



    /** @test */
    public function new_profile_sin_may_not_have_dashes_between_numbers()
    {

        $this->actingAs($this->user, 'api');

        $this->withExceptionHandling();

        $data = $this->validProfileData(['social_insurance_number' => '783-302-649']);
        $response = $this->post('/api/profiles', $data);
        $response->assertSessionHasErrors();

    }


    /** @test */
    public function new_profile_sin_may_have_blanks_between_triplets()
    {

        $this->actingAs($this->user, 'api');

        $data = $this->validProfileData(['social_insurance_number' => '783 302 649']);
        $response = $this->post('/api/profiles', $data);
        $response->assertOk();

    }



    /** @test */
    public function new_profile_sin_cannot_be_any_9_digit_number()
    {
        $this->actingAs($this->user, 'api');

        $this->withExceptionHandling();

        $data = $this->validProfileData(['social_insurance_number' => '883302649']);
        $response = $this->post('/api/profiles', $data);
        $response->assertSessionHasErrors();

    }


    /**
     * @return array
     */
    private function validProfileData($replace = []): array
    {
        $valid = [
            'first_name'  => 'required',
            'last_name'   => 'required',
            'email'       => 'test@example.com',
            'phone'       => '2508123353',
            'address_1'   => 'Address field 1',
            'city'        => 'Victoria',
            'region'      => 'BC',
            'postal_code' => 'H0H0H0'
        ];

        return array_merge($valid, $replace);
    }

}
