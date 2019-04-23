<?php

namespace Tests\Feature;

use App\Assignment;
use App\Credential;
use App\District;
use App\Region;
use App\Role;
use App\School;
use App\Session;
use App\SessionActivity;
use App\SessionType;
use App\Subject;
use Tests\TestCase;

class DynamicsApiTest extends TestCase
{
    /** @test */
    public function get_credentials()
    {
        $credentials = Credential::get();

        $this->assertTrue(is_array($credentials));
    }

    /** @test */
    public function get_schools()
    {
        $schools = School::get();

        $this->assertTrue(is_array($schools));
    }

    /** @test */
    public function get_districts()
    {
        $districts = District::get();

        $this->assertTrue(is_array($districts));
    }

    /** @test */
    public function get_subjects()
    {
        $subjects = Subject::get();

        $this->assertTrue(is_array($subjects));
    }

    /** @test */
    public function get_regions()
    {
        $regions = Region::get();

        $this->assertTrue(is_array($regions));
    }

    /** @test */
    public function get_session_types()
    {
        $types = SessionType::get();

        $this->assertTrue(is_array($types));
    }

    /** @test */
    public function get_session_activities()
    {
        $result = SessionActivity::get();

        $this->assertTrue(is_array($result));
    }

    /** @test */
    public function get_roles()
    {
        $result = Role::get();

        $this->assertTrue(is_array($result));
    }

    /** @test */
    public function get_sessions()
    {
        $result = Session::get();

        $this->assertTrue(is_array($result));
    }

    /** @test */
    public function get_assignments()
    {
        $result = Assignment::get();

        $this->assertTrue(is_array($result));
    }
}
