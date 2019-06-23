<?php

namespace Tests\Feature;

use App\Dynamics\Profile;
use Tests\TestCase;

class ProfileTest extends TestCase
{

    /** @test */
    public function get_assignments()
    {
        $results = Profile::all();

        $this->assertIsArray($results);
        $this->assertIsArray($results[0]);
        $this->assertArrayHasKey('id', $results[0]);
        $this->assertArrayHasKey('first_name', $results[0]);
        $this->assertArrayHasKey('preferred_first_name', $results[0]);
        $this->assertArrayHasKey('last_name', $results[0]);
        $this->assertArrayHasKey('email', $results[0]);
        $this->assertArrayHasKey('phone', $results[0]);
        $this->assertArrayHasKey('social_insurance_number', $results[0]);
        $this->assertArrayHasKey('address_1', $results[0]);
        $this->assertArrayHasKey('address_2', $results[0]);
        $this->assertArrayHasKey('city', $results[0]);
        $this->assertArrayHasKey('region', $results[0]);
        $this->assertArrayHasKey('postal_code', $results[0]);
        $this->assertArrayHasKey('district_id', $results[0]);
        $this->assertArrayHasKey('school_id', $results[0]);
        $this->assertArrayHasKey('professional_certificate_bc', $results[0]);
        $this->assertArrayHasKey('professional_certificate_yk', $results[0]);
        $this->assertArrayHasKey('professional_certificate_other', $results[0]);
    }

}
