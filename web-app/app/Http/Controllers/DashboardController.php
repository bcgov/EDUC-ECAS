<?php

namespace App\Http\Controllers;

use App\Assignment;
use App\Credential;
use App\District;
use App\DynamicsRepository;
use App\Profile;
use App\School;
use App\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function dynamics()
    {
        $test_user_id = 'cf7837ae-0862-e911-a983-000d3af42a5a';

//        return Profile::get($test_user_id);

//        return District::get();

//        return Profile::create([
//            'first_name'  => 'Test',
//            'last_name'   => 'User',
//            'email'       => 'test2@example.com',
//            'phone'       => '2508123352',
//            'address_1'   => 'test address',
//            'city'        => 'Victoria',
//            'region'      => 'BC',
//            'postal_code' => 'V8V1J5',
//        ]);

        return Profile::update($test_user_id, [
            'first_name'  => 'Changed User',
            'last_name'   => 'User',
            'email'       => 'test2@example.com',
            'phone'       => '2508123352',
            'address_1'   => 'test address',
            'city'        => 'Victoria',
            'region'      => 'BC',
            'postal_code' => 'V8V1J5',
        ]);
    }

    public function index()
    {
        if ($this->userLoggedIn()) {
            $user = $this->loadUser();
        }
        else {
            $user = [
                'region'   => 'BC',
                'district' => '',
                'school'   => ''
            ];
        }

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

        //TODO: This is getting all assignments, just want this user's
        $assignments = $this->loadAssignments();

        // Load the Session Look Up fields with info
        foreach ($sessions as $index => $session) {

            // Default to Open status, if an assignment is present it will overwrite below
            $sessions[$index]['status'] = 'Open';

            $sessions[$index]['dates'] = Carbon::create($session['start_date'])->format('M d')
                                         .' - '.
                                         Carbon::create($session['end_date'])->format('M d');

            $key = array_search($session['activity_id'], array_column($activities, 'id'));
            $sessions[$index]['activity'] = $activities[$key]['name'];

            $key = array_search($session['type_id'], array_column($types, 'id'));
            $sessions[$index]['type'] = $types[$key]['name'];
        }

        // Load the Sessions with any assignment details
        foreach ($assignments as $assignment) {
            $session_key = array_search($assignment['session_id'], array_column($sessions, 'id'));
            $sessions[$session_key]['status'] = $assignment['status'];
        }

        return view('dashboard', [
            'user'        => json_encode($user),
            'credentials' => json_encode($credentials),
            'sessions'    => json_encode($sessions),
            'subjects'    => json_encode($subjects),
            'schools'     => json_encode($schools),
            'payments'    => json_encode($payments),
            'districts'   => json_encode($districts),
            'regions'     => json_encode($this->loadRegions()),
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
            $user = $this->loadUser();
            Session::put('user_id', $user['id']);
        }

        return redirect('/Dashboard');
    }

    public function storeCredential(Request $request)
    {
        // TODO: A useless stub, we are just returning the selected credential
        foreach ($this->loadCredentials() as $credential) {
            if ($credential['id'] == $request['credential_id']) {
                return json_encode($credential);
            }
        }
    }

    public function storeProfile(Request $request)
    {
        $request = $this->validateProfileRequest($request);

        $user_id = Profile::create($request->all());

        $user = Profile::get($user_id);

        return json_encode($user);
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
