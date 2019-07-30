<?php

namespace App\Http\Controllers;


use App\Dynamics\Decorators\CacheDecorator;
use App\Interfaces\iModelRepository;
use App\Http\Resources\DashboardResource;
use Illuminate\Support\Facades\Auth;

/*
 * Main Controller for the application
 */
class DashboardController extends Controller
{

    private $profile;

    /**
     * Create a new controller instance.
     *
     * @param iModelRepository $profile
     */
    public function __construct(iModelRepository $profile)
    {
        $this->middleware('auth');
        $this->profile = $profile;

    }


    /*
     * Main entry point for the single page Vue.js application
     */
    public function index()
    {

        $user       = Auth::user();

        $profile    = $this->profile->firstOrCreate($user->id, [
            // TODO - replace the values below with data from BCeID
            'first_name'  => 'required',
            'last_name'   => 'required',
            'email'       => 'test@example.com',
        ]);

        return view('dashboard', ['dashboard' => new DashboardResource($user)]);

    }

}
