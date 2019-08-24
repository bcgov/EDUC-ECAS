<?php

namespace App\Http\Controllers;

use App\Dynamics\Interfaces\iAssignment;
use App\Dynamics\Interfaces\iCredential;
use App\Dynamics\Interfaces\iDistrict;
use App\Dynamics\Interfaces\iProfile;
use App\Dynamics\Interfaces\iProfileCredential;
use App\Dynamics\Interfaces\iRegion;
use App\Dynamics\Interfaces\iSchool;
use App\Dynamics\Interfaces\iSession;
use App\Dynamics\Interfaces\iSubject;
use App\Http\Resources\ProfileCredentialResource;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\SessionResource;
use App\Http\Resources\SimpleResource;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;


/*
 * Main Controller for the application
 */

/**
 * Class DashboardController
 * @package App\Http\Controllers
 */
class DashboardController extends EcasBaseController
{


    private $profile;
    private $profile_credential;
    private $assignment;
    private $session;
    private $credential;
    private $region;
    private $subject;
    private $school;
    private $district;


    /**
     * Create a new controller instance.
     *
     * @param iProfile $profile
     * @param iProfileCredential $profile_credential
     * @param iAssignment $assignment
     * @param iSession $session
     * @param iCredential $credential
     * @param iRegion $region
     * @param iSubject $subject
     * @param iSchool $school
     * @param iDistrict $district
     */
    public function __construct(
                                iProfile $profile,
                                iProfileCredential $profile_credential,
                                iAssignment $assignment,
                                iSession $session,
                                iCredential $credential,
                                iRegion $region,
                                iSubject $subject,
                                iSchool $school,
                                iDistrict $district)
    {
        $this->profile              = $profile;
        $this->profile_credential   = $profile_credential;
        $this->assignment           = $assignment;
        $this->session              = $session;
        $this->credential           = $credential;
        $this->region               = $region;
        $this->subject              = $subject;
        $this->school               = $school;
        $this->district             = $district;
    }


    /*
     * Main entry point for the single page Vue.js application
     */
    public function index(Request $request)
    {

        $token = $request->session()->get('token');

        try {
            $user = $this->getUserByToken($token);
        } catch(ClientException $e) {
            return redirect('/redirect')->with(['err'=>['Your session has expired. Please login again']]);
        }

        $profile    = $this->profile->firstOrCreate($user['sub'], [
            'first_name'  => $user['given_name'],
            'last_name'   => $user['family_name'],
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

        $sessions_with_assignments  = collect([]);

        $sessions->each( function ($session, $index) use($assignments, $profile, $sessions_with_assignments) {

            // filter for any assignments with the same assignment_id AND contact_id
            $filtered_assignments = $assignments->filter( function($assignment) use($session, $profile) {
                return $assignment['session_id'] == $session['id'] AND $assignment['contact_id'] == $profile['id'];
            });

            // if found, add them to $session
            $session['assignment']  = $filtered_assignments->count() > 0 ? $filtered_assignments->first() : null;

            $sessions_with_assignments->push($session);
        });


        return view('dashboard', [
            'user'                  => new ProfileResource($profile, $this->school, $this->district, $this->region ),
            'user_credentials'      => ProfileCredentialResource::collection($profile_credentials),
            'sessions'              => SessionResource::collection($sessions_with_assignments),
            'subjects'              => SimpleResource::collection($this->subject->all()),
            'regions'               => SimpleResource::collection($this->region->all()),
            'credentials'           => SimpleResource::collection($this->credential->all()),

            'api_token'             => $token

        ]);



    }

}
