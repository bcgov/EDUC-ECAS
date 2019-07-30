<?php

namespace Tests\Unit;

use App\Dynamics\Decorators\CacheDecorator;
use App\Dynamics\School;
use Tests\BaseMigrations;

class SchoolTest extends BaseMigrations
{



    public $api;
    public $fake;
    public $schools;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = new School();
        $this->fake = new \App\MockEntities\Repository\School(new \App\MockEntities\School());

        $this->schools = factory(\App\MockEntities\School::class, 7)->create();
    }


    /** @test */
    public function get_all_schools_from_api()
    {
        $results = $this->api->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());

    }


    /** @test */
    public function the_api_should_return_more_than_50_schools()
    {
        $results = $this->api->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->assertTrue(count($results) > 50 );

    }


    /** @test */
    public function get_all_schools_from_api_via_the_cache()
    {
        $results = (new CacheDecorator($this->api))->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());

    }


    /** @test */
    public function get_all_fake_schools_from_api()
    {
        $results = $this->fake->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());

    }


    /** @test */
    public function get_all_fake_schools_from_api_via_the_cache()
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
        $this->assertArrayHasKey('city', $result);

    }

 }
