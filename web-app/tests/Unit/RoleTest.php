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

class RoleTest extends TestCase
{

    /** @test */
    public function get_roles()
    {
        $results = Role::all();

        $this->assertIsArray($results);
        $this->assertIsArray($results[0]);
        $this->assertArrayHasKey('id', $results[0]);
        $this->assertArrayHasKey('name', $results[0]);
        $this->assertArrayHasKey('rate', $results[0]);
    }


}
