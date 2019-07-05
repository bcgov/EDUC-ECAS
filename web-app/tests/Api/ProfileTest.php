<?php

namespace Tests\Api;

use App\MockEntities\Profile;
use Tests\TestCase;


class ProfileTest extends TestCase
{

    private $profile;

    public function setUp() : void
    {
        parent::setUp();
        factory(\App\MockEntities\School::class, 50)->create();
        factory(\App\MockEntities\District::class, 50)->create();
        $this->profile = Factory(Profile::class)->create();

    }

    /** @test */
    public function this_can_get_a_profile()
    {
        $response = $this->get('/api/profiles/' . $this->profile->id );
        $response->assertJsonFragment(['first_name' => $this->profile->first_name]);
    }


    /** @test */
    public function this_can_update_a_profile()
    {
        $new_data = $this->profile;
        $new_data->first_name = 'newValue';
        $new_data->social_insurance_number = '';

        $response = $this->put('/api/profiles/' . $this->profile->id, $new_data->toArray() );

        $response
            ->assertStatus(200)
            ->assertJsonFragment(['first_name' => 'newValue']);
    }


    /** @test */
    public function this_cannot_delete_a_profile()
    {
        $new_data = $this->profile;
        $new_data->first_name = 'newValue';
        $new_data->social_insurance_number = '';

        $response = $this->delete('/api/profiles/' . $this->profile->id );

        $response->assertStatus(401);
    }


    /** @test */
    public function create_profile()
    {
        $this->withExceptionHandling();

        $response = $this->post('/api/profiles', $this->validProfileData());
        $response->assertOk();
    }

    /** @test */
    public function new_profile_requires_data()
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
    public function new_profile_needs_valid_email()
    {
        $this->withExceptionHandling();

        $this->post('/api/profiles', $this->validProfileData(['email' => 'notanemail']))
            ->assertSessionHasErrors('email');

        $this->post('/api/profiles', $this->validProfileData(['email' => 'valid@test.com']))
            ->assertOk();
    }

    /** @test */
    public function new_profile_can_have_a_postal_code_with_a_space()
    {
        // Valid: Six alternating letters and numbers, spaces don't matter

        $this->post('/api/profiles', $this->validProfileData(['postal_code' => 'V8V 1J6']))
            ->assertOk();

    }

    /** @test */
    public function new_profile_needs_valid_postal_code()
    {
        // Valid: Six alternating letters and numbers, spaces don't matter

        $this->post('/api/profiles', $this->validProfileData(['postal_code' => 'V8V1J6']))
            ->assertOk();
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
    public function new_profile_sin_cannot_have_only_6_digits()
    {

        $this->withExceptionHandling();

        $data = $this->validProfileData(['social_insurance_number' => '125 789']);
        $response = $this->post('/api/profiles', $data);
        $response->assertSessionHasErrors('social_insurance_number');

    }

    /** @test */
    public function new_profile_sin_can_be_blank()
    {

        $data = $this->validProfileData(['social_insurance_number' => '']);
        $response = $this->post('/api/profiles', $data);
        $response->assertOk();

    }


    /** @test */
    public function new_profile_sin_cannot_have_alpha_characters()
    {

        $this->withExceptionHandling();

        $data = $this->validProfileData(['social_insurance_number' => '123w4w56']);
        $response = $this->post('/api/profiles', $data);
        $response->assertSessionHasErrors('social_insurance_number');

    }


    /** @test */
    public function new_profile_sin_must_pass_the_luhn_algorithm()
    {

        // Use: http://id-check.artega.biz/pin-ca.php to generate a fictitious SIN

        $this->withExceptionHandling();

        $data = $this->validProfileData(['social_insurance_number' => '783302649']);
        $response = $this->post('/api/profiles', $data);
        $response->assertOk();

    }



    /** @test */
    public function new_profile_sin_may_not_have_dashes_between_numbers()
    {

        $this->withExceptionHandling();

        $data = $this->validProfileData(['social_insurance_number' => '783-302-649']);
        $response = $this->post('/api/profiles', $data);
        $response->assertSessionHasErrors();

    }


    /** @test */
    public function new_profile_sin_may_have_blanks_between_triplets()
    {


        $data = $this->validProfileData(['social_insurance_number' => '783 302 649']);
        $response = $this->post('/api/profiles', $data);
        $response->assertOk();

    }



    /** @test */
    public function new_profile_sin_cannot_be_any_9_digit_number()
    {

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
