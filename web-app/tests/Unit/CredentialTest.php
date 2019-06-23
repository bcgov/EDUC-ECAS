<?php

namespace Tests\Feature;

use App\Dynamics\Credential;
use App\Dynamics\Cache\Credential as CacheCredentials;
use Tests\TestCase;

class CredentialTest extends TestCase
{
    /** @test */
    public function get_credentials()
    {
        $credentials = Credential::all();

        $this->assertIsArray($credentials);
        $this->assertIsArray($credentials[0]);
        $this->assertArrayHasKey('id', $credentials[0]);
        $this->assertArrayHasKey('name', $credentials[0]);
    }


    /** @test */
    public function get_cache_credentials()
    {
        $credentials = CacheCredentials::all();

        $this->assertIsArray($credentials);
        $this->assertIsArray($credentials[0]);
        $this->assertArrayHasKey('id', $credentials[0]);
        $this->assertArrayHasKey('name', $credentials[0]);
    }


}
