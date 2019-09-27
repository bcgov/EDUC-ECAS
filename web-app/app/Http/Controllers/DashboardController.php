<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;


/*
 * Main Controller for the application
 */

/**
 * Class DashboardController
 * @package App\Http\Controllers
 */
class DashboardController extends Controller
{


    /*
     * Main entry point for the single page Vue.js application
     */
    public function index(Request $request)
    {


        return view('dashboard');



    }

}
