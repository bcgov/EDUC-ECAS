<?php

namespace App\Http\Controllers\Api;

use App\Dynamics\Interfaces\iAssignment;
use App\Dynamics\Interfaces\iAssignmentStatus;
use App\Dynamics\Interfaces\iCountry;
use App\Dynamics\Interfaces\iCredential;
use App\Dynamics\Interfaces\iDistrict;
use App\Dynamics\Interfaces\iProfile;
use App\Dynamics\Interfaces\iProfileCredential;
use App\Dynamics\Interfaces\iRegion;
use App\Dynamics\Interfaces\iSchool;
use App\Dynamics\Interfaces\iSession;
use App\Dynamics\Interfaces\iSessionActivity;
use App\Dynamics\Interfaces\iSessionType;
use App\Dynamics\Interfaces\iSubject;
use App\Http\Controllers\Controller;
use App\Http\Resources\AssignmentResource;
use App\Http\Resources\ProfileCredentialResource;
use App\Http\Resources\ProfileNewResource;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\SessionResource;
use App\Http\Resources\SimpleResource;
use App\Keycloak\KeycloakGuard;


/*
 * Main Controller for the application
 */

/**
 * Class DashboardController
 * @package App\Http\Controllers
 */
class DashboardSetupController extends Controller
{


    private $profile;
    private $profile_credential;
    private $assignment;
    private $assignment_status;
    private $session;
    private $session_activity;
    private $session_type;
    private $credential;
    private $region;
    private $country;
    private $subject;
    private $school;
    private $district;
    private $authentication;


    /**
     * Create a new controller instance.
     *
     * @param iProfile $profile   
     * @param iProfileCredential $profile_credential
     * @param iAssignment $assignment
     * @param iAssignmentStatus $assignment_status
     * @param iSession $session
     * @param iSessionActivity $session_activity
     * @param iSessionType $session_type
     * @param iCredential $credential
     * @param iRegion $region
     * @param iCountry $country
     * @param iSubject $subject
     * @param iSchool $school
     * @param iDistrict $district   
     * @param KeycloakGuard $authentication     
     */
    public function __construct(
                                iProfile $profile,
                                iProfileCredential $profile_credential,
                                iAssignment $assignment,
                                iAssignmentStatus $assignment_status,
                                iSession $session,
                                iSessionActivity $session_activity,
                                iSessionType $session_type,
                                iCredential $credential,
                                iRegion $region,
                                iCountry $country,
                                iSubject $subject,
                                iSchool $school,
                                iDistrict $district,                                
                                KeycloakGuard $authentication)
    {
        $this->profile              = $profile;
        $this->profile_credential   = $profile_credential;
        $this->assignment           = $assignment;
        $this->assignment_status    = $assignment_status;
        $this->session              = $session;
        $this->session_activity     = $session_activity;
        $this->session_type         = $session_type;
        $this->credential           = $credential;
        $this->region               = $region;
        $this->country              = $country;
        $this->subject              = $subject;
        $this->school               = $school;
        $this->district             = $district;
        $this->authentication       = $authentication;       
    }


    /*
     * Main entry point for the single page Vue.js application
     */
    public function index()
    {

        $user   = $this->authentication->user();
        $profile    = $this->profile->firstOrCreate($user['sub'], [
            //'first_name'  => $user['given_name'],
            //'last_name'   => $user['family_name'],
            'username'    => $user['username'],
            'email'       => $user['email']
        ]);

        if($profile['id']) {
            $profile_credentials    = $this->profile_credential->filter(['contact_id'=> $profile['id']]);
            $assignments            = $this->assignment->filter(['contact_id'=> $profile['id']]);
        } else {
            $profile_credentials    = collect([]);
            $assignments            = collect([]);
        }


        $sessions                   = $this->session->all();
        $session_activities         = $this->session_activity->all();
        $session_types              = $this->session_type->all();



        $sessions_with_assignments = $sessions->map( function ($session) use($assignments, $profile, $session_activities, $session_types) {

            // filter for any assignments with the same assignment_id AND contact_id
            $filtered_assignments = $assignments->filter( function($assignment) use($session, $profile) {
                return $assignment['session_id'] == $session['id'] AND $assignment['contact_id'] == $profile['id'];
            });

            $session['assignment']  = $filtered_assignments->first();

            $session['activity']    = $session_activities->firstWhere('id',$session['activity_id']);

            $session['type']        = $session_types->firstWhere('id',$session['type_id']);

            return $session;
        });


        $credentials = $this->credential->all();

        $modified_profile_credentials = $profile_credentials->map( function ($credential) use($credentials) {
            $credential['credential'] = $credentials->firstWhere('id', $credential['credential_id']);
            return $credential;
        });  

        return [
            'user'                  => $profile['id'] ? new ProfileResource($profile, $this->school, $this->district, $this->region, $this->country ) :
                new ProfileNewResource($profile, $this->region, $this->country ),
            'user_credentials'      => ProfileCredentialResource::collection($modified_profile_credentials),
            'sessions'              => SessionResource::collection($sessions_with_assignments),
            'subjects'              => SimpleResource::collection($this->subject->all()),
            'regions'               => SimpleResource::collection($this->region->all()),
            'countries'             => SimpleResource::collection($this->country->all()),
            'credentials'           => SimpleResource::collection($this->credential->all()),

        ];



    }

}
