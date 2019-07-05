<?php

namespace Tests\Unit;

use App\Dynamics\Decorators\CacheDecorator;
use App\Dynamics\Profile;
use Tests\BaseMigrations;

class ProfileTest extends BaseMigrations
{

    public $api;
    public $fake;
    public $profile;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = new Profile();
        $this->fake = new \App\MockEntities\Repository\Profile(new \App\MockEntities\Profile());
        factory(\App\MockEntities\School::class, 1)->create();
        factory(\App\MockEntities\District::class, 1)->create();
        $this->profile = Factory(\App\MockEntities\Profile::class)->create();

    }



    /** @test */
    public function get_all_records_from_api()
    {
        $results = $this->api->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());

    }


    /** @test */
    public function get_all_records_from_api_via_the_cache()
    {
        $results = (new CacheDecorator($this->api))->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());

    }

    /** @test */
    public function get_all_fake_records_from_api()
    {
        $results = $this->fake->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());

    }


    /** @test */
    public function get_all_fake_records_from_api_via_the_cache()
    {
        $results = (new CacheDecorator($this->fake))->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());

    }



    private function verifySingle($result)
    {
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('first_name', $result);
        $this->assertArrayHasKey('preferred_first_name', $result);
        $this->assertArrayHasKey('last_name', $result);
        $this->assertArrayHasKey('email', $result);
        $this->assertArrayHasKey('phone', $result);
        $this->assertArrayHasKey('social_insurance_number', $result);
        $this->assertArrayHasKey('address_1', $result);
        $this->assertArrayHasKey('address_2', $result);
        $this->assertArrayHasKey('city', $result);
        $this->assertArrayHasKey('region', $result);
        $this->assertArrayHasKey('postal_code', $result);
        $this->assertArrayHasKey('district_id', $result);
        $this->assertArrayHasKey('school_id', $result);
        $this->assertArrayHasKey('professional_certificate_bc', $result);
        $this->assertArrayHasKey('professional_certificate_yk', $result);
        $this->assertArrayHasKey('professional_certificate_other', $result);

    }

}
