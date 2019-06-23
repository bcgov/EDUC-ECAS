<?php

namespace Tests\Feature;


use App\Dynamics\SessionActivity;
use Tests\TestCase;

class SessionActivitiesTest extends TestCase
{

    /** @test */
    public function get_session_activities()
    {
        $results = SessionActivity::all();

        $this->assertIsArray($results);
        $this->assertIsArray($results[0]);
        $this->assertArrayHasKey('id', $results[0]);
        $this->assertArrayHasKey('name', $results[0]);
        $this->assertArrayHasKey('code', $results[0]);
    }

}
