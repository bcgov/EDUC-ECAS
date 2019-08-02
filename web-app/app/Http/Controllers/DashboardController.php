<?php

namespace App\Http\Controllers;


use App\Dynamics\Decorators\CacheDecorator;
use App\Http\Resources\AssignmentResource;
use App\Http\Resources\ProfileCredentialResource;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\SchoolResource;
use App\Http\Resources\SessionResource;
use App\Http\Resources\SimpleResource;
use App\Interfaces\iModelRepository;
use Illuminate\Support\Facades\App;
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
            'first_name'  => 'BCeID_first',
            'last_name'   => 'BCeID_last',
            'email'       => 'bceid@example.com',
        ]);

        // TODO -----------------------  move this mess of code to a service provider ---------------------

        $repository             = env('DATASET') == 'Dynamics' ? 'Dynamics' : 'MockEntities\Repository';

        if($profile['id']) {
            $profile_credentials    = ( new CacheDecorator(App::make('App\\' . $repository .'\ProfileCredential')))->filter(['contact_id'=> $profile['id']]);
            $assignments            = ( new CacheDecorator(App::make('App\\' . $repository .'\Assignment')))->filter(['contact_id'=> $profile['id']]);
        } else {
            $profile_credentials    = collect([]);
            $assignments            = collect([]);
        }

        $sessions               = ( new CacheDecorator(App::make('App\\' . $repository .'\Session')))->all();

        $sessions_with_assignments = collect([]);

        $sessions->each( function ($session, $index) use($assignments, $profile, $sessions_with_assignments) {

            // filter for any assignments with the same assignment_id AND contact_id
            $filtered_assignments = $assignments->filter( function($assignment) use($session, $profile) {
                return $assignment['session_id'] == $session['id'] AND $assignment['contact_id'] == $profile['id'];
            });

            // if found, add them to $session
            $session['assignment']    = $filtered_assignments->count() > 0 ? $filtered_assignments->first() : null;

            $sessions_with_assignments->push($session);
        });

        $districts              = ( new CacheDecorator(App::make('App\\' . $repository .'\District')))->all();
        $credentials            = ( new CacheDecorator(App::make('App\\' . $repository .'\Credential')))->all();
        $regions                = ( new CacheDecorator(App::make('App\\' . $repository .'\Region')))->all();
        $schools                = ( new CacheDecorator(App::make('App\\' . $repository .'\School')))->all();
        $subjects               = ( new CacheDecorator(App::make('App\\' . $repository .'\Subject')))->all();


        // TODO ------------------------------------ end of mess -------------------------------------


        return view('dashboard', [
            'user'                  => new ProfileResource($profile),
            'user_credentials'      => ProfileCredentialResource::collection($profile_credentials),
            'sessions'              => SessionResource::collection($sessions_with_assignments),
            'subjects'              => SimpleResource::collection($subjects),
            'districts'             => SimpleResource::collection($districts),
            'regions'               => SimpleResource::collection($regions),
            'credentials'           => SimpleResource::collection($credentials),
            'schools'               => SchoolResource::collection($schools),

            'api_token' => $user->api_token
        ]);

    }

}
