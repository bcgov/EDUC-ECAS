<?php

namespace Tests\Api;


use App\MockEntities\ProfileCredential;



class ProfileCredentialTest extends BaseApiTest
{

    private $profile_credentials;
    private $user;

    public function setUp() : void
    {
        parent::setUp();
        Factory(\App\MockEntities\Credential::class, 5)->create();
        $this->user = Factory(\App\MockEntities\Profile::class)->create([
            'school_id'     => 1,
            'district_id'   => 1
        ]);
        $this->profile_credentials = Factory(ProfileCredential::class,2)->create();


    }

    /** @test */
    public function a_user_can_get_all_profile_credentials()
    {
        $response = $this->get('/api/' . $this->user->id . '/profile-credentials' );
        $response
            ->assertJsonFragment(['user_id' => (string) $this->user->id])
            ->assertJsonCount(2);
    }


}
