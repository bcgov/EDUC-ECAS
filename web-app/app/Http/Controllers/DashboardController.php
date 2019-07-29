<?php

namespace App\Http\Controllers;



use App\Dynamics\Session as MarkerSession;
use App\Http\Resources\DashboardResource;
use App\Http\Resources\ProfileResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/*
 * Main Controller for the application
 */
class DashboardController extends Controller
{


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    /*
     * Main entry point for the single page Vue.js application
     */
    public function index()
    {

        $user = Auth::user();

        return new DashboardResource($user);

        return view('dashboard', ['dashboard' => new DashboardResource($user)]);

    }

}
