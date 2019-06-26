<?php

namespace Tests\Feature;

use App\Dynamics\Decorators\CacheDecorator;
use App\Dynamics\Role;
use Tests\TestCase;

class RoleTest extends TestCase
{


    public $api;
    public $fake;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = new Role();
        $this->fake = new \App\MockEntities\Repository\Role(new \App\MockEntities\Role());
    }


    /** @test */
    public function get_all_contracts_from_api()
    {
        $results = $this->api->all();
        $this->verifyCollection($results);
        $this->verifySingle($results[0]);

    }


    /** @test */
    public function get_all_contracts_from_api_via_the_cache()
    {
        $results = (new CacheDecorator($this->api))->all();
        $this->verifyCollection($results);
        $this->verifySingle($results[0]);

    }


    private function verifyCollection($results)
    {
        $this->assertIsArray($results);

    }

    private function verifySingle($result)
    {
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('rate', $result);

    }


}
