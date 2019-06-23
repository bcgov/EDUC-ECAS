<?php

namespace Tests\Feature;

use App\Dynamics\Role;
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
