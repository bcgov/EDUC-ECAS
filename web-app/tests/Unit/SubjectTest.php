<?php

namespace Tests\Feature;

use App\Dynamics\Subject;
use Tests\TestCase;


class SubjectTest extends TestCase
{

    /** @test */
    public function get_subjects()
    {
        $results = Subject::all();

        $this->assertIsArray($results);
        $this->assertIsArray($results[0]);
        $this->assertArrayHasKey('id', $results[0]);
        $this->assertArrayHasKey('name', $results[0]);
    }

}
