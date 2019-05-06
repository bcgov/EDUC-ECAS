<?php

namespace Tests\Feature;

use App\Assignment;
use App\AssignmentStage;
use App\ContractStage;
use App\Credential;
use App\District;
use App\Payment;
use App\Profile;
use App\ProfileCredential;
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
    public function get_profile_credentials()
    {
        $result = ProfileCredential::get();

        $this->assertTrue(is_array($result));
    }

    /** @test */
    public function get_payment_option_list()
    {
        $result = Payment::get();

        $this->assertTrue(is_array($result));
    }

    /** @test */
    public function get_contract_stage_list()
    {
        $result = ContractStage::get();

        $this->assertTrue(is_array($result));
    }

    /** @test */
    public function get_assignment_stage_list()
    {
        $result = AssignmentStage::get();

        $this->assertTrue(is_array($result));
    }

    /** @test */
    public function create_and_update_profile_credential_and_assignment()
    {
        $this->districts = District::get();

        // CREATE PROFILE
        $user_id = Profile::create([
            'first_name'  => 'FirstName',
            'last_name'   => 'LastName',
            'email'       => 'test@example.com',
            'phone'       => '1234567890',
            'address_1'   => 'required',
            'city'        => 'required',
            'region'      => 'required',
            'postal_code' => 'H0H0H0',
            'district_id' => $this->districts[0]['id'] // Use the first District
        ]);

        $this->assertTrue(is_string($user_id));

        // GET PROFILE
        $user = Profile::get($user_id);

        $this->assertEquals('FirstName', $user['first_name']);
        $this->assertEquals('LastName', $user['last_name']);
        $this->assertEquals($this->districts[0]['id'], $user['district_id']);

        // UPDATE PROFILE
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

        $updated_user = Profile::get($user_id);

        $this->assertEquals('NewFirstName', $updated_user['first_name']);
        $this->assertEquals('NewLastName', $updated_user['last_name']);

        // CREATE ASSIGNMENT
        $sessions = Session::get();
        $roles = Role::get();
        $stages = ContractStage::get();
        $statuses = AssignmentStage::get();

        $assignment_id = Assignment::create([
            'user_id'        => $user_id,
            'session_id'     => $sessions[0]['id'],
            'role_id'        => $roles[0]['id'],
            'contract_stage' => $stages[0]['id'],
            'status'         => $statuses[0]['id']
        ]);

        $this->assertTrue(is_string($assignment_id));

        // Get the assignments for this
        $assignments = Assignment::filter(['user_id' => $user_id]);

        $this->assertEquals(1, count($assignments));
        $this->assertEquals($assignment_id, $assignments[0]['id']);
        $this->assertEquals($user_id, $assignments[0]['user_id']);

        // CREATE PROFILE CREDENTIAL
        $credentials = Credential::get();

        $profile_credential_id = ProfileCredential::create([
            'user_id'       => $user_id,
            'credential_id' => $credentials[0]['id']
        ]);

        $user_credentials = ProfileCredential::filter(['user_id' => $user_id]);

        $this->assertEquals(1, count($user_credentials));
        $this->assertEquals($credentials[0]['id'], $user_credentials[0]['credential_id']);
        $this->assertEquals($user_id, $user_credentials[0]['user_id']);

        // Delete Profile Credential
        ProfileCredential::delete($profile_credential_id);

        $user_credentials = ProfileCredential::filter(['user_id' => $user_id]);

        $this->assertEquals(0, count($user_credentials));
    }

    private function validProfileData($replace = []): array
    {
        $valid = [
            'first_name'  => 'required',
            'last_name'   => 'required',
            'email'       => 'test@example.com',
            'phone'       => 'required',
            'address_1'   => 'required',
            'city'        => 'required',
            'region'      => 'required',
            'postal_code' => 'H0H0H0'
        ];

        return array_merge($valid, $replace);
    }

}
