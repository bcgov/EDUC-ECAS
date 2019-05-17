<?php

namespace App\Http\Controllers;

use App\Dynamics\Assignment;
use App\Dynamics\AssignmentStatus;
use App\Dynamics\Credential;
use App\Dynamics\District;
use App\Dynamics\Profile;
use App\Dynamics\ProfileCredential;
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

    /*
     * Main entry point for the single page Vue.js application
     */
    public function index()
    {
        // Much of the data we need are lists and options which do not change often
        // We want to use Caching to reduce the loading of repeated data
        // This must be done at the start, loading other data depends on this info
        $this->loadCachedObjects();

        // Load the specific User Information

        $user = $this->user();

        $user = $this->loadDistrictAndSchoolNames($user);

        // Session will also include Assignment information for this User
        $sessions = $this->loadSessions();

        $user_credentials = $this->loadUserCredentials();

        return view('dashboard', [
            'user'             => json_encode($user),
            'credentials'      => json_encode($this->credentials),
            'sessions'         => json_encode($sessions),
            'subjects'         => json_encode($this->subjects),
            'schools'          => json_encode($this->schools),
            'districts'        => json_encode($this->districts),
            'regions'          => json_encode($this->loadRegions()),
            'user_credentials' => json_encode($user_credentials)
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

    /*
     * Attach a credential to a User
     */
    public function storeCredential(Request $request)
    {
        Log::debug('STORE CREDENTIAL');
        Log::debug($request->all());

        $this->validate($request, [
            'credential_id' => 'required'
        ]);

        $user = $this->user();

        $profile_credential_id = ProfileCredential::create([
            'user_id'       => $user['id'],
            'credential_id' => $request['credential_id']
        ]);

        return json_encode([
            'id'            => $profile_credential_id,
            'credential_id' => $request['credential_id']
        ]);
    }

    public function deleteCredential(Request $request)
    {
        Log::debug('DELETE CREDENTIAL');
        Log::debug($request->all());

        ProfileCredential::delete($request['profile_credential_id']);

        return json_encode([
            'id' => $request['profile_credential_id']
        ]);
    }

    public function storeProfile(Request $request)
    {
        Log::debug('STORE PROFILE');
        Log::debug($request->all());

        $request = $this->validateProfileRequest($request);

        $user_id = Profile::create($request->all());

        // TODO: if we can get the .NET API to return the stored profile from the create method above we could eliminate this call
        $user = $this->user($user_id);

        return json_encode($user);
    }

    public function updateProfile(Request $request)
    {
        Log::debug('UPDATE PROFILE');
        Log::debug($request->all());

        $request = $this->validateProfileRequest($request);

        Profile::update($this->userId(), $request->all());

        return json_encode($this->user());
    }

    public function storeAssignment(Request $request)
    {
        Log::debug('STORE ASSIGNMENT');
        Log::debug($request->all());

        $action = $request['action'];

        $this->assignment_statuses = $this->loadAssignmentStatuses();

        if ($action == Assignment::APPLIED_STATUS) {

            $assignment_id = Assignment::create([
                'user_id'    => $this->userId(),
                'session_id' => $request['session_id']
            ]);

            Log::debug('created assignment id: ' . $assignment_id);
        }
        elseif ($action == Assignment::ACCEPTED_STATUS) {
            $assignment_status_key = array_search(Assignment::ACCEPTED_STATUS, array_column($this->assignment_statuses, 'name'));
            Assignment::update($request['assignment_id'], ['status' => $this->assignment_statuses[$assignment_status_key]['id']]);
        }
        elseif ($action == Assignment::DECLINED_STATUS) {
            $assignment_status_key = array_search(Assignment::DECLINED_STATUS, array_column($this->assignment_statuses, 'name'));
            Assignment::update($request['assignment_id'], [
                'status' => $this->assignment_statuses[$assignment_status_key]['id'],
                'state'  => Assignment::INACTIVE_STATE
            ]);
        }

        return json_encode([
            'session_id' => $request['session_id'],
            'status'     => $action
        ]);
    }

    private function validateProfileRequest(Request $request): Request
    {
        // Get rid of spaces
        $remove_spaces_from = ['postal_code', 'sin'];
        foreach ($remove_spaces_from as $field) {
            if (isset($request[$field])) {
                $request[$field] = preg_replace('/\s+/', '', $request[$field]);
            }
        }

        // Sanitize phone numbers, remove everything that isn't a number
        $sanitize_to_integer = ['phone'];
        foreach ($sanitize_to_integer as $field) {
            $request[$field] = preg_replace('/[^0-9.]/', '', $request[$field]);
        }

        // If we pass in blank look-up ids Dynamics freaks out
        // remove options that are blank
        $remove_blank_options = ['district_id', 'school_id'];
        foreach ($remove_blank_options as $field) {
            if ( ! $request[$field]) {
                unset($request[$field]);
            }
        }

        $this->validate($request, [
            'first_name'  => 'required',
            'last_name'   => 'required',
            'email'       => 'required|email',
            'phone'       => 'required',
            'address_1'   => 'required',
            'city'        => 'required',
            'region'      => 'required',
            'postal_code' => 'required|regex:/^\D\d\D\s?\d\D\d$/i',
            'sin'         => 'regex:/^\d{9}$/i'
        ],
            [
                'first_name.required'  => 'Required',
                'last_name.required'   => 'Required',
                'email.required'       => 'Required',
                'email.email'          => 'Invalid email',
                'phone.required'       => 'Required',
                'address_1.required'   => 'Required',
                'city.required'        => 'Required',
                'region.required'      => 'Required',
                'postal_code.required' => 'Required',
                'postal_code.regex'    => 'Invalid Postal Code',
            ]);

        return $request;
    }

    protected function user($id = null)
    {
        // If an id is present, save it to the Session
        if ($id) {
            Session::put('user_id', $id);
        }

        // If we have not loaded the user this request and we have a logged in user, go get the user from Dynamics
        if ( ! $this->_user && $user_id = $this->userId()) {
            $this->_user = Profile::get($user_id);
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

    protected function loadDistricts()
    {
        $this->districts = District::get();
        usort($this->districts, function ($a, $b) {
            return $a['name'] <=> $b['name'];
        });

        return $this->districts;
    }

    protected function loadSubjects()
    {
        $this->subjects = Subject::get();
        usort($this->subjects, function ($a, $b) {
            return $a['name'] <=> $b['name'];
        });

        return $this->subjects;
    }

    protected function loadSchools()
    {
        $this->schools = School::get();
        usort($this->schools, function ($a, $b) {
            return $a['name'] <=> $b['name'];
        });

        return $this->schools;
    }

    protected function loadActivities()
    {
        return $this->activities = SessionActivity::get();
    }

    protected function loadAssignments()
    {
        if ($this->userId()) {
            return Assignment::filter(['user_id' => $this->userId()]);
        }

        return [];
    }

    protected function loadUserCredentials()
    {
        if ($this->userId()) {

            $user_credentials = ProfileCredential::filter(['user_id' => $this->userId()]);

            // Need to inject the name into the applied credential
            foreach ($user_credentials as $index => $user_credential) {
                $key = array_search($user_credential['credential_id'], array_column($this->credentials, 'id'));
                $user_credentials[$index]['name'] = $this->credentials[$key]['name'];

                // Also remove the applied credential from the list of possibles
                array_splice($this->credentials, $key, 1);
            }

            return $user_credentials;
        }

        return [];
    }

    protected function loadTypes()
    {
        return $this->types = SessionType::get();
    }

    protected function loadCredentials()
    {
        $this->credentials = Credential::get();
        usort($this->credentials, function ($a, $b) {
            return $a['name'] <=> $b['name'];
        });

        return $this->credentials;
    }

    protected function loadRegions()
    {
        // TODO: This is available in a Dynamics table
        // Leaving hardcoded for now to improve page loading speeds during testing
        return [
            ['code' => 'BC', 'name' => 'British Columbia'],
            ['code' => 'YK', 'name' => 'Yukon']
        ];
    }

    protected function loadSessions()
    {
        $sessions = MarkerSession::get();

        // Sort the Sessions by their start date
        usort($sessions, function ($a, $b) {
            return $a['start_date'] <=> $b['start_date'];
        });

        // Load the Session Look Up fields with info
        foreach ($sessions as $index => $session) {

            // Default to Open status, if an assignment is present it will overwrite below
            $sessions[$index]['status'] = 'Open';

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

            $key = array_search($session['activity_id'], array_column($this->activities, 'id'));
            $sessions[$index]['activity'] = $this->activities[$key]['name'];

            $key = array_search($session['type_id'], array_column($this->types, 'id'));
            $sessions[$index]['type'] = $this->types[$key]['name'];
        }

        // Load the Sessions with any assignment details
        $assignments = $this->loadAssignments();

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

    /**
     * @return array|mixed
     */
    private function loadAssignmentStatuses()
    {
        return $this->assignment_statuses = AssignmentStatus::get();
    }

    private function loadCachedObjects(): void
    {
        Log::debug('LOADING BASE DATA / CACHED OBJECTS');

        $this->loadSubjects();

        $this->loadSchools();

        $this->loadCredentials();

        $this->loadDistricts();

        $this->loadActivities();

        $this->loadTypes();

        $this->loadAssignmentStatuses();
    }

    /**
     * @param $user
     * @return array
     */
    private function loadDistrictAndSchoolNames($user)
    {
        if (isset($user['district_id'])) {
            $key = array_search($user['district_id'], array_column($this->districts, 'id'));
            $user['district'] = $this->districts[$key]['name'];
        }
        if (isset($user['school_id'])) {
            $key = array_search($user['school_id'], array_column($this->schools, 'id'));
            $user['school'] = $this->schools[$key]['name'];
        }

        return $user;
    }
}
