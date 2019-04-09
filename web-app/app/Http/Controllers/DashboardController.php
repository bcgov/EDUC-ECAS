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

        return view('dashboard', [
            'user'        => json_encode($user),
            'credentials' => json_encode($credentials),
            'sessions'    => json_encode($sessions),
            'subjects'    => json_encode($subjects),
            'schools'     => json_encode($schools),
            'regions'     => json_encode($this->loadRegions()),
        ]);
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
        // Load the existing user record
        $user = $this->loadUser();

        // TODO: Another useless stub, update the dummy user and return
        foreach ($request->all() as $key => $value) {
            if (isset($user[$key])) {
                $user[$key] = $value;
            }
        }

        return json_encode($user);
    }

    public function post(Request $request)
    {
        return $request->all();
    }
}
