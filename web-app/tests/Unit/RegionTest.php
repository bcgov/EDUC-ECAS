<?php

namespace Tests\Feature;


use App\Dynamics\Region;
use Tests\TestCase;

class RegionTest extends TestCase
{


    /** @test */
    public function get_regions()
    {
        $results = Region::all();


        $this->assertIsArray($results);
        $this->assertIsArray($results[0]);
        $this->assertArrayHasKey('id', $results[0]);
        $this->assertArrayHasKey('name', $results[0]);
    }


}