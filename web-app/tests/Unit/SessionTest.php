<?php

namespace Tests\Feature;

use App\Dynamics\Session;
use Tests\TestCase;

class SessionTest extends TestCase
{

    /** @test */
    public function get_sessions()
    {
        $results = Session::all();

        $this->assertIsArray($results);
        $this->assertIsArray($results[0]);
        $this->assertArrayHasKey('id', $results[0]);
        $this->assertArrayHasKey('activity_id', $results[0]);
        $this->assertArrayHasKey('type_id', $results[0]);
        $this->assertArrayHasKey('start_date', $results[0]);
        $this->assertArrayHasKey('end_date', $results[0]);
        $this->assertArrayHasKey('location', $results[0]);
        $this->assertArrayHasKey('address', $results[0]);
        $this->assertArrayHasKey('city', $results[0]);
    }


}
