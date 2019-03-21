<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = $this->loadUser();

        $schools = $this->loadSchools();

        return view('profile', [
            'user'    => $user,
            'schools' => $schools
        ]);
    }
}
