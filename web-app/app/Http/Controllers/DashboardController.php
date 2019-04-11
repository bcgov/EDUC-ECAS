<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index()
    {
        if ($this->userLoggedIn()) {
            $user = $this->loadUser();
        }
        else {
            $user = [];
        }

        $subjects = $this->loadSubjects();

        $schools = $this->loadSchools();

        $credentials = $this->loadCredentials();

        $sessions = $this->loadSessions();

        $districts = [
            ['id' => 1, 'name' => 'District Number 1'],
            ['id' => 2, 'name' => 'Another District']
        ];

        $payments = [
            ['id' => 1, 'name' => 'Electronic Transfer'],
            ['id' => 2, 'name' => 'Cheque']
        ];

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
        $this->validate($request, [
            'first_name'  => 'required',
            'last_name'   => 'required',
            'email'       => 'required|email',
            'phone'       => 'required',
            'address_1'   => 'required',
            'city'        => 'required',
            'region'      => 'required',
            'postal_code' => 'required'

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
                'postal_code.required' => 'Required'
            ]);

        if ($this->userLoggedIn()) {
            $user = $this->loadUser();
        }

        // TODO: Another useless stub, update the dummy user and return
        foreach ($request->all() as $key => $value) {
            $user[$key] = $value;
        }

        return json_encode($user);
    }

    public function post(Request $request)
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
}
