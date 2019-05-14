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

class DashboardController extends Controller
{
    private $test_user_id = 'cf7837ae-0862-e911-a983-000d3af42a5a';

    // We only want to fetch the logged in user once
    protected $_user = [];

    public function index()
    {
//        $user = $this->user($this->test_user_id);
        $user = $this->user();

        $subjects = $this->loadSubjects();

        $schools = $this->loadSchools();

        $credentials = $this->loadCredentials();

        $districts = $this->loadDistricts();

        $sessions = $this->loadSessions();

        $activities = $this->loadActivities();

        $types = $this->loadTypes();

        // Add the District and School names to the selected
        if (isset($user['district_id'])) {
            $key = array_search($user['district_id'], array_column($districts, 'id'));
            $user['district'] = $districts[$key]['name'];
        }
        if (isset($user['school_id'])) {
            $key = array_search($user['school_id'], array_column($schools, 'id'));
            $user['school'] = $schools[$key]['name'];
        }

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

            $key = array_search($session['activity_id'], array_column($activities, 'id'));
            $sessions[$index]['activity'] = $activities[$key]['name'];

            $key = array_search($session['type_id'], array_column($types, 'id'));
            $sessions[$index]['type'] = $types[$key]['name'];
        }

        // Load the Sessions with any assignment details

        $assignment_statuses = AssignmentStatus::get();

        // Not all Statuses should be displayed to the user
        $do_not_display = ['Selected'];

        $assignments = $this->loadAssignments();

        foreach ($assignments as $assignment) {
            $assignment_status_key = array_search($assignment['status'], array_column($assignment_statuses, 'id'));
            if ( ! in_array($assignment_statuses[$assignment_status_key]['name'], $do_not_display)) {

                $session_key = array_search($assignment['session_id'], array_column($sessions, 'id'));

                $sessions[$session_key]['status'] = $assignment_statuses[$assignment_status_key]['name'];

                // if a Session has an Assignment store the assignment id
                $sessions[$session_key]['assignment_id'] = $assignment['id'];
            }
        }

        $user_credentials = $this->loadUserCredentials();

        // Need to inject the name into the applied credential
        foreach ($user_credentials as $index => $user_credential) {
            $key = array_search($user_credential['credential_id'], array_column($credentials, 'id'));
            $user_credentials[$index]['name'] = $credentials[$key]['name'];

            // Also remove the applied credential from the list of possibles
            array_splice($credentials, $key, 1);
        }

        return view('dashboard', [
            'user'             => json_encode($user),
            'credentials'      => json_encode($credentials),
            'sessions'         => json_encode($sessions),
            'subjects'         => json_encode($subjects),
            'schools'          => json_encode($schools),
            'districts'        => json_encode($districts),
            'regions'          => json_encode($this->loadRegions()),
            'user_credentials' => json_encode($user_credentials)
        ]);
    }

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

    public function storeCredential(Request $request)
    {
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
        ProfileCredential::delete($request['profile_credential_id']);

        return json_encode([
            'id' => $request['profile_credential_id']
        ]);
    }

    public function storeProfile(Request $request)
    {
        $request = $this->validateProfileRequest($request);

        $user_id = Profile::create($request->all());

        $user = $this->user($user_id);

        return json_encode($user);
    }

    public function updateProfile(Request $request)
    {
        $request = $this->validateProfileRequest($request);

        Profile::update($this->userId(), $request->all());

        return json_encode($this->user());
    }

    public function storeAssignment(Request $request)
    {
        Log::debug('STORE ASSIGNMENT');
        Log::debug($request->all());

        $action = $request['action'];

        $assignment_statuses = AssignmentStatus::get();

        if ($action == Assignment::APPLIED_STATUS) {

            $assignment_id = Assignment::create([
                'user_id'    => $this->userId(),
                'session_id' => $request['session_id']
            ]);

            Log::debug('created assignment id: ' . $assignment_id);
        }
        elseif ($action == Assignment::ACCEPTED_STATUS) {
            $assignment_status_key = array_search(Assignment::ACCEPTED_STATUS, array_column($assignment_statuses, 'name'));
            Assignment::update($request['assignment_id'], ['status' => $assignment_statuses[$assignment_status_key]['id']]);
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
        if ($id) {
            Session::put('user_id', $id);
        }

        if ( ! $this->_user && $user_id = $this->userId()) {
            $this->_user = Profile::get($user_id);
        }

        return $this->_user;
    }

    protected function userId()
    {
        if (Session::has('user_id')) {
            return Session::get('user_id');
        }

        return null;
    }

    protected function loadDistricts()
    {
        $districts = District::get();
        usort($districts, function ($a, $b) {
            return $a['name'] <=> $b['name'];
        });

        return $districts;
    }

    protected function loadSubjects()
    {
        $subjects = Subject::get();
        usort($subjects, function ($a, $b) {
            return $a['name'] <=> $b['name'];
        });

        return $subjects;
    }

    protected function loadSchools()
    {
        $schools = School::get();
        usort($schools, function ($a, $b) {
            return $a['name'] <=> $b['name'];
        });

        return $schools;
    }

    protected function loadActivities()
    {
        return SessionActivity::get();
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
            return ProfileCredential::filter(['user_id' => $this->userId()]);
        }

        return [];
    }

    protected function loadTypes()
    {
        return SessionType::get();
    }

    protected function loadCredentials()
    {
        $credentials = Credential::get();
        usort($credentials, function ($a, $b) {
            return $a['name'] <=> $b['name'];
        });

        return $credentials;
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
        usort($sessions, function ($a, $b) {
            return $a['start_date'] <=> $b['start_date'];
        });

        return $sessions;
    }
}
