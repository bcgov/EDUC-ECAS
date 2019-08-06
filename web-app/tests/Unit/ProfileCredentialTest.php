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
        $profile = (new Profile())->all()->first();
        $filtered = $this->api->filter([ 'contact_id' => $profile['id'] ]);

        $this->assertInstanceOf('Illuminate\Support\Collection', $filtered);
        $this->verifySingle($filtered->first());

    }


    /** @test */
    public function create_a_new_credential_via_the_api()
    {
        $profile = (new Profile())->all()->first();
        $credential = (new Credential())->all()->first();

        $new_record_id = $this->api->create([
            'contact_id'    => $profile['id'],
            'credential_id' => $credential['id'],
            'verified'      => '610410002'
        ]);

        $results = $this->api->get($new_record_id);

        $this->verifySingle($results);

    }



    public function delete_a_credential_via_the_api()
    {
        $credential = (new ProfileCredential())->all()->first();

        $results = $this->api->delete($credential['id']);

        // TODO - re-enable this test; don't want to run it as it deletes credentials from api
        // Better to add a new record and then delete it - but create isn't working at present

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
