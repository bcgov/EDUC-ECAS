<?php

namespace Tests\Unit;

use App\Dynamics\Decorators\CacheDecorator;
use App\Dynamics\Role;
use Tests\BaseMigrations;

class RoleTest extends BaseMigrations
{


    public $api;
    public $fake;
    public $roles;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = new Role();
        $this->fake = new \App\MockEntities\Repository\Role(new \App\MockEntities\Role());

        $this->roles = factory(\App\MockEntities\Role::class, 7)->create();
    }


    /** @test */
    public function get_all_role_records_from_api()
    {
        $results = $this->api->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());

    }


    /** @test */
    public function get_all_role_records_from_api_via_the_cache()
    {
        $results = (new CacheDecorator($this->api))->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());

    }



    /** @test */
    public function get_all_fake_role_records()
    {
        $results = $this->fake->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());

    }


    /** @test */
    public function get_all_fake_role_records_via_the_cache()
    {
        $results = (new CacheDecorator($this->fake))->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());

    }
    

    private function verifySingle($result)
    {
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('rate', $result);

    }


}
