<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = $this->loadUser();

        $subjects = $this->loadSubjects();

        $schools = $this->loadSchools();

        $credentials = $this->loadCredentials();

        $available_sessions = [
            '1'  => [
                'activity' => 'Exemplar',
                'type'     => 'LIT 10 I',
                'dates'    => 'August 1-2',
                'location' => 'Vancouver'
            ],
            '22' => [
                'activity' => 'Marking',
                'type'     => 'LIT 10 I',
                'dates'    => 'August 3-4',
                'location' => 'Vancouver'
            ],
            '33' => [
                'activity' => 'Marking',
                'type'     => 'LIT 20 E',
                'dates'    => 'July 3-4',
                'location' => 'Victoria'
            ]
        ];

        $assignments = [
            '1'  => [
                'status' => 'Scheduled'
            ],
            '22' => [
                'status' => 'Assigned'
            ],
        ];



        return view('dashboard', [
            'user'        => json_encode($user),
            'credentials' => json_encode($credentials),
            'sessions'    => json_encode($available_sessions),
            'assignments' => json_encode($assignments),
            'subjects'    => json_encode($subjects),
            'schools'     => json_encode($schools),
        ]);
    }

    public function storeCredential(Request $request)
    {
        foreach ($this->loadCredentials() as $credential) {
            if ($credential['id'] == $request['credential_id']) {
                return json_encode($credential);
            }
        }
    }

    public function post(Request $request)
    {
        return $request->all();
    }
}
