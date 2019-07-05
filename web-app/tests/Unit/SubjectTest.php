<?php

namespace Tests\Unit;

use App\Dynamics\Decorators\CacheDecorator;
use App\Dynamics\Subject;
use Tests\BaseMigrations;


class SubjectTest extends BaseMigrations
{

    public $api;
    public $fake;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = new Subject();
        $this->fake = new \App\MockEntities\Repository\Subject(new \App\MockEntities\Subject());
    }


    /** @test */
    public function get_all_subjects_from_api()
    {
        $results = $this->api->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        // API is returning an empty array at present - $this->verifySingle($results->first());

    }


    /** @test */
    public function get_all_subjects_from_api_via_the_cache()
    {
        $results = (new CacheDecorator($this->api))->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        // API is returning an empty array at present - $this->verifySingle($results->first());

    }



    private function verifySingle($result)
    {
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);

    }

}
