<?php

namespace Tests\Feature;

use App\Dynamics\AssignmentStatus;

use App\Dynamics\Decorators\CacheDecorator;
use Tests\TestCase;

class AssignmentStatusTest extends TestCase
{

    public $api;
    public $fake;

    public function setUp(): void
    {
        parent::setUp(); 
        $this->api              = new AssignmentStatus();
        $this->fake             = new \App\MockEntities\Repository\AssignmentStatus(new \App\MockEntities\AssignmentStatus());
        
    }

    /** @test */
    public function get_all_assignments_from_api()
    {
        $results = $this->api->all();

        $this->verifyCollection($results);
        $this->verifySingle($results[0]);
    }

    /** @test */
    public function get_all_assignments_from_api_cached()
    {
        $results = (new CacheDecorator($this->api))->all();

        $this->verifyCollection($results);
        $this->verifySingle($results[0]);
    }

    /** @test */
    public function get_all_assignments_from_fake_local_database()
    {
        // TODO - remove `toArray()` - we should be returning a collection
        $results = $this->fake->all()->toArray();


        $this->verifyCollection($results);
        $this->verifySingle($results[0]);
    }


    /** @test */
    public function get_all_assignments_from_fake_local_database_then_cached()
    {
        // TODO - remove `toArray()` - we should be returning a collection
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
        $this->assertArrayHasKey('name', $result);

    }




}
