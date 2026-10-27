<?php

namespace Tests\Api;

use App\Dynamics\Interfaces\iDistrict;
use App\Dynamics\Interfaces\iSchool;
use App\Keycloak\KeycloakGuard;
use PHPUnit\Framework\Attributes\Test;
use Tests\BaseMigrations;

class SearchTest extends BaseMigrations
{

    public function setUp(): void
    {
        parent::setUp();

        $guard = \Mockery::mock(KeycloakGuard::class);
        $guard->shouldReceive('hasUser')->andReturn(true);
        $this->app->instance(KeycloakGuard::class, $guard);
    }


    #[Test]
    public function the_district_search_sends_the_query_to_dynamics()
    {
        $district = \Mockery::mock(iDistrict::class);
        $district->shouldReceive('filterContains')
            ->with(['name' => 'Vic'])
            ->once()
            ->andReturn(collect([['id' => 'district-61', 'name' => 'Victoria(61)']]));
        $this->app->instance(iDistrict::class, $district);

        $this->get('/api/districts?q=Vic')
            ->assertStatus(200)
            ->assertJsonFragment(['id' => 'district-61', 'name' => 'Victoria(61)']);
    }

    #[Test]
    public function the_school_search_sends_the_query_to_dynamics()
    {
        $school = \Mockery::mock(iSchool::class);
        $school->shouldReceive('filterContains')
            ->with(['name' => 'Oak'])
            ->once()
            ->andReturn(collect([['id' => 'school-1', 'name' => 'Oak Bay High', 'city' => 'Victoria']]));
        $this->app->instance(iSchool::class, $school);

        $this->get('/api/schools?q=Oak')
            ->assertStatus(200)
            ->assertJsonFragment(['id' => 'school-1', 'name' => 'Oak Bay High', 'city' => 'Victoria']);
    }

}
