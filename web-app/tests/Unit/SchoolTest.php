<?php

namespace Tests\Feature;

use App\Dynamics\School;
use Tests\TestCase;

class SchoolTest extends TestCase
{


    /** @test */
    public function get_schools()
    {
        $results = School::all();

        $this->assertIsArray($results);
        $this->assertIsArray($results[0]);
        $this->assertArrayHasKey('id', $results[0]);
        $this->assertArrayHasKey('name', $results[0]);
        $this->assertArrayHasKey('city', $results[0]);
    }

 }
