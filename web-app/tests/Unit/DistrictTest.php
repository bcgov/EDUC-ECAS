<?php

namespace Tests\Feature;

use App\Dynamics\District;
use Tests\TestCase;

class DistrictTest extends TestCase
{

    /** @test */
    public function get_districts()
    {
        $districts = District::all();

        $this->assertIsArray($districts);
        $this->assertIsArray($districts[0]);
        $this->assertArrayHasKey('id', $districts[0]);
        $this->assertArrayHasKey('name', $districts[0]);

    }


}
