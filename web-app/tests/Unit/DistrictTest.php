<?php

namespace Tests\Unit;

use App\Dynamics\Decorators\CacheDecorator;
use App\Dynamics\District;
use Tests\BaseMigrations;

class DistrictTest extends BaseMigrations
{


    public $api;
    public $fake;
    public $districts;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = new District();
        $this->fake = new \App\MockEntities\Repository\District(new \App\MockEntities\District());
        $this->districts = factory(\App\MockEntities\District::class, 7)->create();
    }


    /** @test */
    public function get_all_districts_from_api()
    {
        $results = $this->api->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());

    }


    /** @test */
    public function get_all_districts_from_api_via_the_cache()
    {
        $results = (new CacheDecorator($this->api))->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());

    }

    /** @test */
    public function get_all_fake_districts_from_api()
    {
        $results = $this->fake->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());

    }


    /** @test */
    public function get_all_fake_districts_from_api_via_the_cache()
    {
        $results = (new CacheDecorator($this->fake))->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());

    }




    private function verifySingle($result)
    {
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);

    }

}
