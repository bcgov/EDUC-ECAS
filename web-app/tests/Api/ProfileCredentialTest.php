<?php

namespace Tests\Api;


use App\Http\Resources\SimpleResource;
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
        $this->profile_credentials = Factory(ProfileCredential::class,2)->create([
            'verified'      => "Yes"
        ]);


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
    public function a_user_does_not_receive_credentials_verified_as_no()
    {
        $this->actingAs($this->user, 'api');

        $no_credentials = Factory(ProfileCredential::class,2)->create([
            'verified'      => "No"
        ]);

        $response = $this->get('/api/profile-credentials' );

        $response->assertJsonCount(2);
    }


    /** @test */
    public function an_unauthenticated_user_cannot_get_any_profile_credentials()
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

        $credential = Factory(\App\MockEntities\Credential::class)->create();

        $response = $this->post('/api/profile-credentials', [
            'credential_id'       => $credential->id
        ] );

        $response
            ->assertJsonFragment(['user_id'         => (string) $this->user->id])
            ->assertJsonFragment(['credential'      => new SimpleResource($credential)]);
    }


    /** @test */
    public function a_user_can_create_two_identical_profile_credentials()
    {

        $this->actingAs($this->user, 'api');

        $this->post('/api/profile-credentials', [
            'credential_id'       => 7
        ] );

        $response = $this->post('/api/profile-credentials', [
            'credential_id'       => 7
        ] );

        $response->assertStatus(200 ); // okay
    }


    /** @test */
    public function a_user_cannot_create_a_profile_credential_with_a_verified_status()
    {

        $this->actingAs($this->user, 'api');

        $response = $this->post('/api/profile-credentials', [
            'credential_id'       => 7,
            'verified'            => "Yes"  // using "Yes" here as that's how Dynamics records it
        ] );

        $response
            ->assertJsonFragment(['user_id'         => (string) $this->user->id])
            ->assertJsonFragment(['verified'        => false]);
    }




}
