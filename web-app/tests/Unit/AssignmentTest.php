<?php

namespace Tests\Unit;

use App\Dynamics\Assignment;
use App\Dynamics\Decorators\CacheDecorator;
use Tests\BaseMigrations;

class AssignmentTest extends BaseMigrations
{

    public $api;
    public $fake;
    public $assignments;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = new Assignment();
        $this->fake = new \App\MockEntities\Repository\Assignment(new \App\MockEntities\Assignment());

        $this->assignments = factory(\App\MockEntities\Assignment::class, 5)->create([
            'contact_id'        => 1,
            'role_id'           => 1,
            'session_id'        => 1,
            'contract_stage'    => 1,
            'status'            => 1
        ]);
    }


    /** @test */
    public function get_all_assignments_from_api()
    {
        $results = $this->api->all();

        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());
    }



    /** @test */
    public function get_filtered_set_of_assignments_via_the_api()
    {
        $filtered = $this->api->filter([ 'contact_id' => '1a2abe23-3d64-e911-b80a-005056833c5b' ]);


        $this->assertInstanceOf('Illuminate\Support\Collection', $filtered);
        $this->verifySingle($filtered->first());

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
        $this->assertArrayHasKey('contact_id', $result);
        $this->assertArrayHasKey('role_id', $result);
        $this->assertArrayHasKey('contract_stage', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('state', $result);

    }


}
