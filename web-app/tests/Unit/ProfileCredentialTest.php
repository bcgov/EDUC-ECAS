<?php

namespace Tests\Feature;

use App\Dynamics\Assignment;
use App\Dynamics\AssignmentStatus;
use App\Dynamics\Credential;
use App\Dynamics\District;
use App\Dynamics\Profile;
use App\Dynamics\ProfileCredential;
use App\Dynamics\School;
use App\Dynamics\Session;

use Tests\TestCase;

class ProfileCredentialTest extends TestCase
{

    /** @test */
    public function get_profile_credentials()
    {
        $results = ProfileCredential::all();

        $this->assertIsArray($results);
        $this->assertIsArray($results[0]);
        $this->assertArrayHasKey('id', $results[0]);
        $this->assertArrayHasKey('user_id', $results[0]);
        $this->assertArrayHasKey('credential_id', $results[0]);
        $this->assertArrayHasKey('verified', $results[0]);
    }

    /** @test */
    public function create_and_update_profile_credential_and_assignment()
    {
        $districts = District::all();
        $schools = School::all();

        // CREATE PROFILE
        $user_id = Profile::create([
            'first_name'                     => 'FirstName',
            'last_name'                      => 'LastName',
            'email'                          => 'test@example.com',
            'phone'                          => '1234567890',
            'address_1'                      => 'Address #1',
            'address_2'                      => 'Address #2',
            'city'                           => 'Victoria',
            'region'                         => 'BC',
            'postal_code'                    => 'H0H0H0',
            'social_insurance_number'        => '123456789',
            'professional_certificate_bc'    => 'bc_cert',
            'professional_certificate_yk'    => 'yk_cert',
            'professional_certificate_other' => 'other_cert',
            'district_id'                    => $districts[0]['id'], // Use the first District
            'school_id'                      => $schools[0]['id'],
        ]);

        $this->assertTrue(is_string($user_id));

        $user = Profile::get($user_id);

        $this->assertEquals('FirstName', $user['first_name']);
        $this->assertEquals('LastName', $user['last_name']);
        $this->assertEquals('test@example.com', $user['email']);
        $this->assertEquals('1234567890', $user['phone']);
        $this->assertEquals('Address #1', $user['address_1']);
        $this->assertEquals('Address #2', $user['address_2']);
        $this->assertEquals('Victoria', $user['city']);
        $this->assertEquals('BC', $user['region']);
        $this->assertEquals('H0H0H0', $user['postal_code']);
        $this->assertEquals('123456789', $user['social_insurance_number']);
        $this->assertEquals('bc_cert', $user['professional_certificate_bc']);
        $this->assertEquals('yk_cert', $user['professional_certificate_yk']);
        $this->assertEquals('other_cert', $user['professional_certificate_other']);
        $this->assertEquals($schools[0]['id'], $user['school_id']);
        $this->assertEquals($districts[0]['id'], $user['district_id']);

        // UPDATE PROFILE
        Profile::update($user_id, [
            'first_name'                     => 'new_FirstName',
            'last_name'                      => 'new_LastName',
            'email'                          => 'new_test@example.com',
            'phone'                          => '2345678901',
            'address_1'                      => 'new_Address #1',
            'address_2'                      => 'new_Address #2',
            'city'                           => 'new_Victoria',
            'region'                         => 'YK',
            'postal_code'                    => 'H1H1H1',
            'social_insurance_number'        => '987654321',
            'professional_certificate_bc'    => 'new_bc_cert',
            'professional_certificate_yk'    => 'new_yk_cert',
            'professional_certificate_other' => 'new_other_cert',
            'district_id'                    => $districts[1]['id'],
            'school_id'                      => $schools[1]['id'],
        ]);

        $updated_user = Profile::get($user_id);

        $this->assertEquals('new_FirstName', $updated_user['first_name']);
        $this->assertEquals('new_LastName', $updated_user['last_name']);
        $this->assertEquals('new_test@example.com', $updated_user['email']);
        $this->assertEquals('2345678901', $updated_user['phone']);
        $this->assertEquals('new_Address #1', $updated_user['address_1']);
        $this->assertEquals('new_Address #2', $updated_user['address_2']);
        $this->assertEquals('new_Victoria', $updated_user['city']);
        $this->assertEquals('YK', $updated_user['region']);
        $this->assertEquals('H1H1H1', $updated_user['postal_code']);
        $this->assertEquals('987654321', $updated_user['social_insurance_number']);
        $this->assertEquals('new_bc_cert', $updated_user['professional_certificate_bc']);
        $this->assertEquals('new_yk_cert', $updated_user['professional_certificate_yk']);
        $this->assertEquals('new_other_cert', $updated_user['professional_certificate_other']);
        $this->assertEquals($schools[1]['id'], $updated_user['school_id']);
        $this->assertEquals($districts[1]['id'], $updated_user['district_id']);

        // CREATE ASSIGNMENT
        $sessions = Session::all();
        $statuses = AssignmentStatus::all();

        $assignment_id = Assignment::create([
            'user_id'    => $user_id,
            'session_id' => $sessions[0]['id']
        ]);

        $this->assertTrue(is_string($assignment_id));

        // Get the assignments for this User
        $assignments = Assignment::filter(['user_id' => $user_id]);

        $this->assertEquals(1, count($assignments));
        $this->assertEquals($assignment_id, $assignments[0]['id']);
        $this->assertEquals($user_id, $assignments[0]['user_id']);

        // Assignment Status should be 'Applied'
        $assignment_status_key = array_search(Assignment::APPLIED_STATUS, array_column($statuses, 'name'));
        $applied_status_id = $statuses[$assignment_status_key]['id'];
        $this->assertEquals($applied_status_id, $assignments[0]['status']);

        // Update the Status of the Assignment to 'Accepted'
        // IMPORTANT: The user must have a SIN for status to be Accepted
        $assignment_status_key = array_search(Assignment::ACCEPTED_STATUS, array_column($statuses, 'name'));
        $accepted_status_id = $statuses[$assignment_status_key]['id'];

        Assignment::update($assignment_id, [
            'status' => $accepted_status_id
        ]);

        // Refresh the assignments for this
        $assignments = Assignment::filter(['user_id' => $user_id]);

        // We should see that the assignment status has been updated
        $this->assertEquals($accepted_status_id, $assignments[0]['status']);

        // Update the Status of the Assignment to 'Declined'
        $declined_status_key = array_search(Assignment::DECLINED_STATUS, array_column($statuses, 'name'));
        $declined_status_id = $statuses[$declined_status_key]['id'];

        // TODO: This test is failing! Assume because of Business Rule within Dynamics
        // We are not satisfying a precondition of setting status to Declined, but am unsure what that condition is
        Assignment::update($assignment_id, [
            'status' => $declined_status_id,
            'state' => Assignment::INACTIVE_STATE
        ]);

        // Refresh the assignments for this
        $assignments = Assignment::filter(['user_id' => $user_id]);

        // We should see that the assignment status has been updated
        $this->assertEquals($declined_status_id, $assignments[0]['status']);

        // CREATE PROFILE CREDENTIAL
        $credentials = Credential::all();

        $profile_credential_id = ProfileCredential::create([
            'user_id'       => $user_id,
            'credential_id' => $credentials[0]['id'] // pick whatever the first one is
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


}
