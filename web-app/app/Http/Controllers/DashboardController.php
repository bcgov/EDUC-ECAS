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

        $sessions = $this->loadSessions();

        $assignments = [
            [
                'id' => '1',
                'status' => 'Scheduled'
            ],
            [
                'id' => '22',
                'status' => 'Assigned'
            ],
        ];

        return view('dashboard', [
            'user'        => json_encode($user),
            'credentials' => json_encode($credentials),
            'sessions'    => json_encode($sessions),
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
