<?php

namespace Tests\Feature;


use App\Dynamics\SessionType;
use Tests\TestCase;

class SessionTypeTest extends TestCase
{

    /** @test */
    public function get_session_types()
    {
        $results = SessionType::all();

        $this->assertIsArray($results);
        $this->assertIsArray($results[0]);
        $this->assertArrayHasKey('id', $results[0]);
        $this->assertArrayHasKey('name', $results[0]);
        $this->assertArrayHasKey('code', $results[0]);
    }


}
