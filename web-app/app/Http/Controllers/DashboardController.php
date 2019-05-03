<?php

namespace App\Http\Controllers;

use App\Assignment;
use App\AssignmentStage;
use App\Credential;
use App\District;
use App\DynamicsRepository;
use App\Profile;
use App\ProfileCredential;
use App\School;
use App\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class DashboardController extends Controller
{
    private $test_user_id = 'cf7837ae-0862-e911-a983-000d3af42a5a';

    public function index()
    {
        $user = [];
//        if ($this->userLoggedIn()) {
        $user = $this->loadUser($this->test_user_id);
//        }
//        else {
//            $user = [
//                'region'   => 'BC',
//                'district' => '',
//                'school'   => ''
//            ];
//        }

        $payments = [
            ['id' => 1, 'name' => 'Electronic Transfer'],
            ['id' => 2, 'name' => 'Cheque']
        ];

        $subjects = $this->loadSubjects();

        $schools = $this->loadSchools();

        $credentials = $this->loadCredentials();

        $districts = $this->loadDistricts();

        $sessions = $this->loadSessions();

        $activities = $this->loadActivities();

        $types = $this->loadTypes();

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

        $assignment_statuses = AssignmentStage::get();

        // Not all Statuses should be displayed to the user
        $do_not_display = ['Selected'];

        $assignments = $this->loadAssignments($this->test_user_id);

        foreach ($assignments as $assignment) {
            $assignment_status_key = array_search($assignment['status'], array_column($assignment_statuses, 'id'));
            if ( ! in_array($assignment_statuses[$assignment_status_key]['name'], $do_not_display)) {

                $session_key = array_search($assignment['session_id'], array_column($sessions, 'id'));

                $sessions[$session_key]['status'] = $assignment_statuses[$assignment_status_key]['name'];
            }
        }

        $user_credentials = ProfileCredential::filter(['user_id' => $this->test_user_id]);
//        dump(json_encode($user_credentials));
//        dd(json_encode($credentials));
        // Need to inject the name into the applied credential
        foreach($user_credentials as $index => $user_credential) {
            $key = array_search($user_credential['credential_id'], array_column($credentials, 'id'));
            $user_credentials[$index]['name'] = $credentials[$key]['name'];

            // Also remove the applied credential from the list of possibles
            array_splice($credentials, $key, 1);
        }
//dump(json_encode($user_credentials));
//dd(json_encode($credentials));
        return view('dashboard', [
            'user'             => json_encode($user),
            'credentials'      => json_encode($credentials),
            'sessions'         => json_encode($sessions),
            'subjects'         => json_encode($subjects),
            'schools'          => json_encode($schools),
            'payments'         => json_encode($payments),
            'districts'        => json_encode($districts),
            'regions'          => json_encode($this->loadRegions()),
            'user_credentials' => json_encode($user_credentials)
        ]);
    }

    public function login()
    {
        return view('login');
    }

    public function postLogin(Request $request)
    {
        if ($request['email'] == 'new@example.com') {
            Session::forget('user_id');
        }
        else {
            $user = $this->loadUser($this->test_user_id);
            Session::put('user_id', $user['id']);
        }

        return redirect('/Dashboard');
    }

    public function storeCredential(Request $request)
    {
        $this->validate($request, [
            'user_id'       => 'required',
            'credential_id' => 'required'
        ]);

        $profile_credential_id = ProfileCredential::create([
            'user_id'       => $request['user_id'],
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

        $user = Profile::get($user_id);

        return json_encode($user);
    }

    public function updateProfile(Request $request)
    {
        $request = $this->validateProfileRequest($request);

        $user = Profile::update($this->test_user_id, $request->all());

        return json_encode($user);
    }

    public function storeAssignment(Request $request)
    {
        return $request->all();
    }

    /**
     * @return bool
     */
    private function userLoggedIn(): bool
    {
        return Session::has('user_id');
    }

    /**
     * @param Request $request
     * @return Request
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateProfileRequest(Request $request): Request
    {
        // Get rid of spaces
        $remove_spaces_from = ['postal_code', 'sin'];
        foreach ($remove_spaces_from as $field) {
            if (isset($request[$field])) {
                $request[$field] = preg_replace('/\s+/', '', $request[$field]);
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
}
