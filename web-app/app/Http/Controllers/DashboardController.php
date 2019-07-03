<?php

namespace App\Http\Controllers;



use App\Dynamics\Session as MarkerSession;
use App\Http\Resources\DashboardResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session; 

/*
 * Main Controller for the application
 */
class DashboardController extends Controller
{
    // We only want to fetch the logged in user once, store for the entire request lifecycle
    protected $_user = []; // default to a blank / new user


    /*
     * Main entry point for the single page Vue.js application
     */
    public function index(Request $federated_id)
    {
        // Much of the data we need are lists and options which do not change often
        // We want to use Caching to reduce the loading of repeated data
        // This must be done at the start, loading other data depends on this info

        // Load the specific User Information

        // TODO: Temporarily hardcoding a specific user for the demo. Remove!
        //$temporary_user_id = '8c266dae-5d7e-e911-a990-000d3af438b6';

        // We instantiate $profile and $credentials using App::make so we can
        // dynamically switch between fictitious data and the Dynamics API.

        return new DashboardResource($federated_id);

    }


    // TODO: This is a useless stub for testing and will be replaced by integration with SiteMinder / Keycloak
    public function login()
    {
        return view('login');
    }

   
    // TODO: This is a useless stub for testing and will be replaced by integration with SiteMinder / Keycloak
    public function postLogin(Request $request)
    {
        if ($request['email'] == 'new@example.com') {
            MarkerSession::forget('user_id');
        }
        else {
            $user = $this->user();
            MarkerSession::put('user_id', $user['id']);
        }

        return redirect('/Dashboard');
    }



    private function user($id = null)
    {
        // If an id is present, save it to the Session
        if ($id) {
            Session::put('user_id', $id);
        }

        // If we have not loaded the user this request and we have a logged in user, go get the user from Dynamics
        if ( ! $this->_user && $user_id = $this->userId()) {
            $this->_user = ( new Profile())->get($user_id);
        }

        return $this->_user;
    }

    private function userId()
    {
        // If we have a valid user there is a Session variable
        if (Session::has('user_id')) {
            return Session::get('user_id');
        }

        return null;
    }
    

}
