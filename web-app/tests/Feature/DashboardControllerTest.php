<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardControllerTest extends TestCase
{
    /** @test */
    public function create_profile()
    {
        $this->withExceptionHandling();

        $this->post('/Dashboard/profile', $this->validProfileData())->assertOk();
    }

    /** @test */
    public function profile_requires_data()
    {
        $this->withExceptionHandling();

        $this->post('/Dashboard/profile', $this->validProfileData([
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
    public function profile_needs_valid_email()
    {
        $this->withExceptionHandling();

        $this->post('/Dashboard/profile', $this->validProfileData(['email' => 'notanemail']))
            ->assertSessionHasErrors('email');

        $this->post('/Dashboard/profile', $this->validProfileData(['email' => 'valid@test.com']))
            ->assertOk();
    }

    /** @test */
    public function profile_needs_valid_postal_code()
    {
        // Valid: Six alternating letters and numbers, spaces don't matter

        $this->withExceptionHandling();

        $this->post('/Dashboard/profile', $this->validProfileData(['postal_code' => '123456']))
            ->assertSessionHasErrors('postal_code');

        $this->post('/Dashboard/profile', $this->validProfileData(['postal_code' => 'V8V 1J6']))
            ->assertOk();

        $this->post('/Dashboard/profile', $this->validProfileData(['postal_code' => 'V8V1J6']))
            ->assertOk();
    }

    /** @test */
    public function profile_needs_valid_sin()
    {
        // Valid: Any 9 digits, spaces don't matter

        $this->withExceptionHandling();

        $this->post('/Dashboard/profile', $this->validProfileData(['sin' => '123456']))
            ->assertSessionHasErrors('sin');

        $this->post('/Dashboard/profile', $this->validProfileData(['sin' => '1234567ww']))
            ->assertSessionHasErrors('sin');

        // Is not required
        $this->post('/Dashboard/profile', $this->validProfileData(['sin' => '']))
            ->assertOk();

        $this->post('/Dashboard/profile', $this->validProfileData(['sin' => '123456789']))
            ->assertOk();

        $this->post('/Dashboard/profile', $this->validProfileData(['sin' => '123 456 789']))
            ->assertOk();
    }

    /** @test */
    public function apply_to_session()
    {
        $this->post('/Dashboard/session', [
            'session_id' => '',
            'action' => 'apply'
        ])->assertOk();
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
