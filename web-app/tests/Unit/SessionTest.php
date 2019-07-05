<?php

namespace Tests\Unit;

use App\Dynamics\Decorators\CacheDecorator;
use App\Dynamics\Session;
use Tests\BaseMigrations;

class SessionTest extends BaseMigrations
{
    
    public $api;
    public $fake;
    public $sessions;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = new Session();
        $this->fake = new \App\MockEntities\Repository\Session(new \App\MockEntities\Session());

        factory(\App\MockEntities\SessionType::class, 3)->create();
        factory(\App\MockEntities\SessionActivity::class, 3)->create();
        $this->sessions = factory(\App\MockEntities\Session::class, 7)->create();
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
        $this->assertArrayHasKey('activity_id', $result);
        $this->assertArrayHasKey('type_id', $result);
        $this->assertArrayHasKey('start_date', $result);
        $this->assertArrayHasKey('end_date', $result);
        $this->assertArrayHasKey('location', $result);
        $this->assertArrayHasKey('address', $result);
        $this->assertArrayHasKey('city', $result);

    }


}
