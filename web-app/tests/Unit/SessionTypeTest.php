<?php

namespace Tests\Unit;


use App\Dynamics\Decorators\CacheDecorator;
use App\Dynamics\SessionType;
use Tests\BaseMigrations;

class SessionTypeTest extends BaseMigrations
{


    public $api;
    public $fake;
    public $types;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = new SessionType();
        $this->fake = new \App\MockEntities\Repository\SessionType(new \App\MockEntities\SessionType());

        $this->types = factory(\App\MockEntities\SessionType::class, 7)->create();
    }


    /** @test */
    public function get_all_records_from_api()
    {
        $results = $this->api->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());

    }


    /** @test */
    public function get_all_records_from_api_via_the_cache()
    {
        $results = (new CacheDecorator($this->api))->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());

    }

    /** @test */
    public function get_all_fake_records()
    {
        $results = $this->fake->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());

    }


    /** @test */
    public function get_all_fake_records_via_the_cache()
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
        $this->assertArrayHasKey('code', $result);

    }


}
