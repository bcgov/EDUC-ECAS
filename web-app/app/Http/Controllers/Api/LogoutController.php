<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        $url =  $request-> input('url');
        return redirect($url);



    }
}
