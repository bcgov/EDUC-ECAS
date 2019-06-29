<?php

namespace App\Http\Controllers;

use App\Dynamics\Assignment;
use App\Dynamics\AssignmentStatus;
use App\Dynamics\Credential;
use App\Dynamics\Decorators\CacheDecorator;
use App\Dynamics\District;
use App\Dynamics\Profile;
use App\Dynamics\ProfileCredential;
use App\Dynamics\Region;
use App\Dynamics\School;
use App\Dynamics\Subject;
use App\Dynamics\Session as MarkerSession;
use App\Dynamics\SessionActivity;
use App\Dynamics\SessionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

/*
 * Main Controller for the application
 */
class DashboardController extends Controller
{
    // We only want to fetch the logged in user once, store for the entire request lifecycle
    protected $_user = []; // default to a blank / new user

    // These are cached objects, we want to load them once per session
    protected $subjects;
    protected $schools;
    protected $credentials;
    protected $districts;
    protected $activities;
    protected $types;
    protected $assignment_statuses;
    protected $region;
    protected $session;
    protected $assignment;


    public function __construct(School $school,
                                Subject $subject,
                                Credential $credential,
                                District $distict,
                                SessionActivity $activity,
                                SessionType $type,
                                AssignmentStatus $assignment_statuses,
                                Region $region,
                                MarkerSession $session,
                                Assignment $assignment)
    {

        $this->schools = $school;
        $this->subjects = $subject;
        $this->credentials = $credential;
        $this->districts = $distict;
        $this->activities = $activity;
        $this->types = $type;
        $this->region = $region;
        $this->assignment_statuses = $assignment_statuses;
        $this->session = $session;
        $this->assignment = $assignment;

    }

    /*
     * Main entry point for the single page Vue.js application
     */
    public function index()
    {
        // Much of the data we need are lists and options which do not change often
        // We want to use Caching to reduce the loading of repeated data
        // This must be done at the start, loading other data depends on this info
        //$this->loadCachedObjects();

        // Load the specific User Information

        // TODO: Temporarily hardcoding a specific user for the demo. Remove!
        $temporary_user_id = '8c266dae-5d7e-e911-a990-000d3af438b6';

        $user = $this->user($temporary_user_id);

//        $user = $this->loadDistrictAndSchoolNames($user);

        // Session will also include Assignment information for this User
//        $sessions = $this->loadSessions();

//        $user_credentials = $this->loadUserCredentials();

        return view('dashboard', [
            'user'             => json_encode($user),
            'credentials'      => json_encode($this->credentials),
  //          'sessions'         => json_encode($sessions),
            'subjects'         => json_encode($this->subjects->all()),
            'schools'          => json_encode($this->schools->all()),
            'districts'        => json_encode($this->districts->all()),
            'regions'          => json_encode($this->region->all()),
  //          'user_credentials' => json_encode($user_credentials)
        ]);
    }

    // TODO: This is a useless stub for testing and will be replaced by integration with SiteMinder / Keycloak
    public function login()
    {
        return view('login');
    }

   
    // TODO: This is a useless stub for testing and will be replaced by integration with SiteMinder / Keycloak
    public function postLogin(Request $request)
    {
        if ($request['email'] == 'new@example.com') {
            MarkerSession::forget('user_id');
        }
        else {
            $user = $this->user();
            MarkerSession::put('user_id', $user['id']);
        }

        return redirect('/Dashboard');
    }



    protected function user($id = null)
    {
        // If an id is present, save it to the Session
        if ($id) {
            Session::put('user_id', $id);
        }

        // If we have not loaded the user this request and we have a logged in user, go get the user from Dynamics
        if ( ! $this->_user && $user_id = $this->userId()) {
            $this->_user = ( new Profile())->get($user_id);
        }

        return $this->_user;
    }

    protected function userId()
    {
        // If we have a valid user there is a Session variable
        if (Session::has('user_id')) {
            return Session::get('user_id');
        }

        return null;
    }


    private function loadUserCredentials()
    {
        if ($this->userId()) {

            $user_credentials = (new ProfileCredential())->get(['user_id' => $this->userId()]);

            // Need to inject the name into the applied credential
            foreach ($user_credentials as $index => $user_credential) {
                $key = array_search($user_credential['credential_id'], array_column($this->credentials->all(), 'id'));
                $user_credentials[$index]['name'] = $this->credentials[$key]['name'];

                // Also remove the applied credential from the list of possibles
                array_splice($this->credentials->all(), $key, 1);
            }

            return $user_credentials;
        }

        return [];
    }




    private function loadSessions()
    {
        $sessions = $this->session->all();

        // Sort the Sessions by their start date
        usort($sessions, function ($a, $b) {
            return $a['start_date'] <=> $b['start_date'];
        });

        // Load the Session Look Up fields with info
        foreach ($sessions as $index => $session) {

            // Default to Open status, if an assignment is present it will overwrite below
            $sessions[$index]['status'] = 'Open';

            // Display a nicely formated date string
            $start_carbon = Carbon::create($session['start_date']);
            $end_carbon = Carbon::create($session['end_date']);
            $date_string = $start_carbon->format('M j') . ' - ';
            if ($start_carbon->format('M') == $end_carbon->format('M')) {
                $date_string .= $end_carbon->format('j');
            }
            else {
                $date_string .= $end_carbon->format('M j');
            }

            $sessions[$index]['dates'] = $date_string;

            $key = array_search($session['activity_id'], array_column($this->activities->all(), 'id'));
            $sessions[$index]['activity'] = $this->activities[$key]['name'];

            $key = array_search($session['type_id'], array_column($this->types->all(), 'id'));
            $sessions[$index]['type'] = $this->types[$key]['name'];
        }

        // Load the Sessions with any assignment details
        $assignments = $this->assignment->get($this->userId());

        // Not all Statuses should be displayed to the user
        $do_not_display = ['Selected'];

        foreach ($assignments as $assignment) {
            $assignment_status_key = array_search($assignment['status'], array_column($this->assignment_statuses, 'id'));
            if ( ! in_array($this->assignment_statuses[$assignment_status_key]['name'], $do_not_display)) {

                $session_key = array_search($assignment['session_id'], array_column($sessions, 'id'));

                $sessions[$session_key]['status'] = $this->assignment_statuses[$assignment_status_key]['name'];

                // if a Session has an Assignment store the assignment id
                $sessions[$session_key]['assignment_id'] = $assignment['id'];
            }
        }

        return $sessions;
    }



//    /**
//     * @param $user
//     * @return array
//     */
//    private function loadDistrictAndSchoolNames($user)
//    {
//        if (isset($user['district_id'])) {
//            $key = array_search($user['district_id'], array_column($this->districts->all(), 'id'));
//            $user['district'] = $this->districts[$key]['name'];
//        }
//        if (isset($user['school_id'])) {
//            $key = array_search($user['school_id'], array_column($this->schools->all(), 'id'));
//            $user['school'] = $this->schools[$key]['name'];
//        }
//
//        return $user;
//    }
}
