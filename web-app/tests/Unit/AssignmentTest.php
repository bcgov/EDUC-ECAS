<?php

namespace Tests\Unit;

use App\Dynamics\Assignment;
use App\Dynamics\Decorators\CacheDecorator;
use Tests\TestCase;

class AssignmentTest extends TestCase
{

    public $api;
    public $fake;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = new Assignment();
        $this->fake = new \App\MockEntities\Repository\Assignment(new \App\MockEntities\Assignment());
    }


    /** @test */
    public function get_all_assignments_from_api()
    {
        $results = $this->api->all();

        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());
    }

    /** @test */
    public function get_all_assignments_from_api_cached()
    {
        $results = (new CacheDecorator($this->api))->all();

        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());
    }

    /** @test */
    public function get_all_assignments_from_fake_local_database()
    {

        $results = $this->fake->all();


        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());
    }


    /** @test */
    public function get_all_assignments_from_fake_local_database_then_cached()
    {

        $results = (new CacheDecorator($this->fake))->all();

        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());
    }


    private function verifySingle($result)
    {

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('session_id', $result);
        $this->assertArrayHasKey('user_id', $result);
        $this->assertArrayHasKey('role_id', $result);
        $this->assertArrayHasKey('contract_stage', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('state', $result);

    }


}
