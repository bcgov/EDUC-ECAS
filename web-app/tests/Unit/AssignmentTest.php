<?php

namespace Tests\Feature;

use App\Dynamics\Assignment;
use App\Dynamics\AssignmentStatus;
use App\Dynamics\ContractStage;
use App\Dynamics\Credential;
use App\Dynamics\District;
use App\Dynamics\Payment;
use App\Dynamics\Profile;
use App\Dynamics\ProfileCredential;
use App\Dynamics\Region;
use App\Dynamics\Role;
use App\Dynamics\School;
use App\Dynamics\Session;
use App\Dynamics\SessionActivity;
use App\Dynamics\SessionType;
use App\Dynamics\Subject;
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
