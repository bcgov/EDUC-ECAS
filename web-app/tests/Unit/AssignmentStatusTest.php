<?php

namespace Tests\Unit;

use App\Dynamics\AssignmentStatus;

use App\Dynamics\Decorators\CacheDecorator;
use Faker\Factory;
use Tests\BaseMigrations;


class AssignmentStatusTest extends BaseMigrations
{

    public $api;
    public $fake;

    public function setUp(): void
    {

        parent::setUp();
        factory(\App\MockEntities\AssignmentStatus::class, 10)->create();
        $this->api              = new AssignmentStatus();
        $this->fake             = new \App\MockEntities\Repository\AssignmentStatus(new \App\MockEntities\AssignmentStatus());

    }

    /** @test */
    public function get_all_assignment_statuses_from_api()
    {
        $results = $this->api->all();

        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());
    }

    /** @test */
    public function get_all_assignment_statuses_from_api_cached()
    {
        $results = (new CacheDecorator($this->api))->all();

        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());
    }

    /** @test */
    public function get_all_assignment_statuses_from_fake_local_database()
    {
     
        $results = $this->fake->all();


        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());
    }


    /** @test */
    public function get_all_assignment_statuses_from_fake_local_database_then_cached()
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
