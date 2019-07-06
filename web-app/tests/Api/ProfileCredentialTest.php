<?php

namespace Tests\Api;


use App\MockEntities\Profile;
use App\MockEntities\ProfileCredential;
use Faker\Factory;
use Tests\BaseMigrations;


class ProfileCredentialTest extends BaseMigrations
{

    private $profile_credentials;
    private $user;

    public function setUp() : void
    {
        parent::setUp();
        Factory(\App\MockEntities\Credential::class, 5)->create();
        $this->user = Factory(\App\User::class)->create();
        Factory(Profile::class)->create([
            'school_id'     => 1,
            'district_id'   => 1
        ]);
        $this->profile_credentials = Factory(ProfileCredential::class,2)->create();


    }

    /** @test */
    public function a_user_can_get_all_profile_credentials()
    {
        $this->actingAs($this->user, 'api');

        $response = $this->get('/api/profile-credentials' );
        $response
            ->assertJsonFragment(['user_id' => (string) $this->user->id])
            ->assertJsonCount(2);
    }

    /** @test */
    public function a_user_can_create_a_profile_credentials()
    {
        $this->actingAs($this->user, 'api');

        $response = $this->get('/api/profile-credentials' );
        $response
            ->assertJsonFragment(['user_id' => (string) $this->user->id])
            ->assertJsonCount(2);
    }





}
