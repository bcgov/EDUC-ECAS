<?php

namespace Tests\Api;


use App\MockEntities\Profile;
use App\MockEntities\ProfileCredential;
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
            'user_id'       => $this->user->id,
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
    public function an_unauthenticated_user_cannot_get_all_profile_credentials()
    {
        $this->withExceptionHandling();

        $response = $this->get('/api/profile-credentials' );
        $response->assertStatus(302); // unauthorised
    }

    /** @test */
    public function a_user_can_delete_a_profile_credential()
    {
        $this->actingAs($this->user, 'api');

        $record_2b_deleted = $this->profile_credentials->last();

        $response = $this->delete('/api/profile-credentials/' . $record_2b_deleted->id  );

        $response->assertStatus(204 ); // success
    }


    /** @test */
    public function a_user_cannot_delete_another_users_profile_credential()
    {
        $another_user = Factory(\App\User::class)->create();
        $this->actingAs($another_user, 'api');

        $record_2b_deleted = $this->profile_credentials->last();

        $response = $this->delete('/api/profile-credentials/' . $record_2b_deleted->id  );

        $response->assertStatus(403 ); // unauthorized
    }



    /** @test */
    public function a_user_can_create_a_profile_credentials()
    {
        $this->actingAs($this->user, 'api');

        $response = $this->post('/api/profile-credentials', [
            'credential_id'       => 2
        ] );

        $response
            ->assertJsonFragment(['user_id'         => (string) $this->user->id])
            ->assertJsonFragment(['credential_id'   => '2']);
    }





}
