<?php

namespace Tests\Feature;


use App\Dynamics\Decorators\CacheDecorator;
use App\Dynamics\Region;
use Tests\TestCase;

class RegionTest extends TestCase
{


    public $api;
    public $fake;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = new Region();
        $this->fake = new \App\MockEntities\Repository\Region(new \App\MockEntities\Region());
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

    }

}