<?php

namespace Tests\Feature;

use App\Dynamics\Credential;
use App\Dynamics\Decorators\CacheDecorator;
use Tests\TestCase;

class CredentialTest extends TestCase
{

    public $api;
    public $fake;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = new Credential();
        $this->fake = new \App\MockEntities\Repository\Credential(new \App\MockEntities\Credential());

    }

    /** @test */
    public function get_credentials()
    {
        $credentials = $this->api->all();

        $this->assertInstanceOf('Illuminate\Support\Collection', $credentials);
        $this->verifySingle($credentials->first());
        
    }


    /** @test */
    public function get_cache_credentials()
    {
        $credentials = (new CacheDecorator($this->api))->all();

        $this->assertInstanceOf('Illuminate\Support\Collection', $credentials);
        $this->verifySingle($credentials->first());
    }

    /** @test */
    public function get_fake_credentials()
    {
        $credentials = $this->fake->all();


        $this->assertInstanceOf('Illuminate\Support\Collection', $credentials);
        $this->verifySingle($credentials->first());

    }


    /** @test */
    public function get_fake_cache_credentials()
    {
        $credentials = (new CacheDecorator($this->fake))->all();

        $this->assertInstanceOf('Illuminate\Support\Collection', $credentials);
        $this->verifySingle($credentials->first());
    }
    

    
    private function verifySingle($result)
    {

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
    }


}
