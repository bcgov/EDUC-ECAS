<?php

namespace Tests\Feature;

use App\Assignment;
use App\Credential;
use App\District;
use App\Profile;
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

    /** @test */
    public function create_and_update_profile()
    {
        $user_id = Profile::create([
            'first_name'  => 'FirstName',
            'last_name'   => 'LastName',
            'email'       => 'test@example.com',
            'phone'       => '1234567890',
            'address_1'   => 'required',
            'city'        => 'required',
            'region'      => 'required',
            'postal_code' => 'H0H0H0'
        ]);

        $user = Profile::get($user_id);

        $this->assertEquals('FirstName', $user['first_name']);
        $this->assertEquals('LastName', $user['last_name']);

        // Update

        $updated_user = Profile::update($user_id, [
            'first_name'  => 'NewFirstName',
            'last_name'   => 'NewLastName',
            'email'       => 'new@example.com',
            'phone'       => '1234567890',
            'address_1'   => 'required',
            'city'        => 'required',
            'region'      => 'required',
            'postal_code' => 'H0H0H0'
        ]);

        $this->assertEquals('NewFirstName', $updated_user['first_name']);
        $this->assertEquals('NewLastName', $updated_user['last_name']);
    }
}
