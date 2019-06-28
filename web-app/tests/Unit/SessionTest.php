<?php

namespace Tests\Feature;

use App\Dynamics\Decorators\CacheDecorator;
use App\Dynamics\Session;
use Tests\TestCase;

class SessionTest extends TestCase
{
    
    public $api;
    public $fake;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = new Session();
        $this->fake = new \App\MockEntities\Repository\Session(new \App\MockEntities\Session());
    }


    /** @test */
    public function get_all_records_from_api()
    {
        $results = $this->api->all();
        $this->verifyCollection($results);
        $this->verifySingle($results[0]);

    }


    /** @test */
    public function get_all_records_from_api_via_the_cache()
    {
        $results = (new CacheDecorator($this->api))->all();
        $this->verifyCollection($results);
        $this->verifySingle($results[0]);

    }

    /** @test */
    public function get_all_fake_records()
    {
        $results = $this->fake->all()->toArray();
        $this->verifyCollection($results);
        $this->verifySingle($results[0]);

    }


    /** @test */
    public function get_all_fake_records_via_the_cache()
    {
        $results = (new CacheDecorator($this->fake))->all()->toArray();
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
        $this->assertArrayHasKey('activity_id', $result);
        $this->assertArrayHasKey('type_id', $result);
        $this->assertArrayHasKey('start_date', $result);
        $this->assertArrayHasKey('end_date', $result);
        $this->assertArrayHasKey('location', $result);
        $this->assertArrayHasKey('address', $result);
        $this->assertArrayHasKey('city', $result);

    }


}
