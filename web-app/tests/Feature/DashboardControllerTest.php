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

    /**
     * @return array
     */
    private function validProfileData($replace = []): array
    {
        $valid = [
            'first_name'  => 'required',
            'last_name'   => 'required',
            'email'       => 'test@example.com',
            'phone'       => 'required',
            'address_1'   => 'required',
            'city'        => 'required',
            'region'      => 'required',
            'postal_code' => 'required'
        ];

        return array_merge($valid, $replace);
    }

}
