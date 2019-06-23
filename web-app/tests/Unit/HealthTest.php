<?php

namespace Tests\Feature;

use App\Dynamics\Health;

use Tests\TestCase;

class HealthTest extends TestCase
{

    /** @test */
    public function all_health()
    {
        $health = Health::all();

        // not sure what health should return -- not working (I think)
        $this->assertIsArray($health);
    }


}
