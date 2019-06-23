<?php

namespace Tests\Feature;

use App\Dynamics\Assignment;
use Tests\TestCase;

class AssignmentTest extends TestCase
{

    /** @test */
    public function get_assignments()
    {
        $result = Assignment::all();

        $this->assertTrue(is_array($result));

        $this->assertIsArray($result);
        $this->assertIsArray($result[0]);
        $this->assertArrayHasKey('id', $result[0]);
        $this->assertArrayHasKey('session_id', $result[0]);
        $this->assertArrayHasKey('user_id', $result[0]);
        $this->assertArrayHasKey('role_id', $result[0]);
        $this->assertArrayHasKey('contract_stage', $result[0]);
        $this->assertArrayHasKey('status', $result[0]);
        $this->assertArrayHasKey('state', $result[0]);

    }

}
