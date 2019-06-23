<?php

namespace Tests\Feature;

use App\Dynamics\Cache\AssignmentStatus as CacheAssignmentStatus;
use App\Dynamics\AssignmentStatus;

use Tests\TestCase;

class AssignmentStatusTest extends TestCase
{

    /** @test */
    public function get_assignment_stage_list()
    {
        $results = AssignmentStatus::all();

        $this->assertIsArray($results);
        $this->assertIsArray($results[0]);
        $this->assertArrayHasKey('id', $results[0]);
        $this->assertArrayHasKey('name', $results[0]);
    }

    /** @test */
    public function get_cache_assignment_stage_list()
    {
        $results = CacheAssignmentStatus::all();

        $this->assertIsArray($results);
        $this->assertIsArray($results[0]);
        $this->assertArrayHasKey('id', $results[0]);
        $this->assertArrayHasKey('name', $results[0]);
    }

    /** @test */
    public function get_cache_update_assignment_stage_list()
    {
        // cache is set
        $results = CacheAssignmentStatus::all();

        // make small modification


        $this->assertIsArray($results);
        $this->assertIsArray($results[0]);
        $this->assertArrayHasKey('id', $results[0]);
        $this->assertArrayHasKey('name', $results[0]);
    }


}
