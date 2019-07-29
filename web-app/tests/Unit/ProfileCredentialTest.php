<?php

namespace Tests\Unit;

use App\Dynamics\Assignment;
use App\Dynamics\AssignmentStatus;
use App\Dynamics\Credential;
use App\Dynamics\Decorators\CacheDecorator;
use App\Dynamics\District;
use App\Dynamics\Profile;
use App\Dynamics\ProfileCredential;
use App\Dynamics\School;
use App\Dynamics\Session;

use Tests\BaseMigrations;

class ProfileCredentialTest extends BaseMigrations
{

    public $api;
    public $fake;
    public $profile_credentials;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = new ProfileCredential();
        $this->fake = new \App\MockEntities\Repository\ProfileCredential(new \App\MockEntities\ProfileCredential());

        factory(\App\MockEntities\Credential::class, 4)->create();
        $this->profile_credentials = factory(\App\MockEntities\ProfileCredential::class, 3)->create([
            'contact_id'   => 1
        ]);
    }


    /** @test */
    public function get_all_credentials_from_api()
    {
        $results = $this->api->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());

    }


    /** @test */
    public function get_filtered_set_of_credentials_via_the_api()
    {
        $filtered = $this->api->filter([ 'contact_id' => '1a2abe23-3d64-e911-b80a-005056833c5b' ]);


        $this->assertInstanceOf('Illuminate\Support\Collection', $filtered);
        $this->verifySingle($filtered->first());

    }


    /** @test */
    public function get_all_credentials_from_api_via_the_cache()
    {
        $results = (new CacheDecorator($this->api))->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());

    }


    /** @test */
    public function get_all_fake_credentials()
    {
        $results = $this->fake->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());

    }


    /** @test */
    public function get_all_fake_credentials_via_the_cache()
    {
        $results = (new CacheDecorator($this->fake))->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());

    }

    private function verifySingle($result)
    {

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('contact_id', $result);
        $this->assertArrayHasKey('credential_id', $result);
        $this->assertArrayHasKey('verified', $result);


    }



}
