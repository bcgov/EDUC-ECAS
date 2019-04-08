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

    public function test1()
    {
        $client = \AlexaCRM\WebAPI\ClientFactory::createOnlineClient(
            'http://ecas-pvpywj-dev.pathfinder.gov.bc.ca/',
            '96337d02-539f-4982-a89a-c1d25c78bd3b',
            'NltSOQMgJ2DwVbjw+6hpceiyGzGYWp6mDI6v5smQX+k='
        );
    }

    public function signIn()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Initialize the OAuth client
        $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId'                => env('OAUTH_APP_ID'),
            'clientSecret'            => env('OAUTH_APP_PASSWORD'),
            'redirectUri'             => env('OAUTH_REDIRECT_URI'),
            'urlAuthorize'            => env('OAUTH_AUTHORITY').env('OAUTH_AUTHORIZE_ENDPOINT'),
            'urlAccessToken'          => env('OAUTH_AUTHORITY').env('OAUTH_TOKEN_ENDPOINT'),
            'urlResourceOwnerDetails' => '',
            'scopes'                  => env('OAUTH_SCOPES')
        ]);

        // Generate the auth URL
        $authorizationUrl = $oauthClient->getAuthorizationUrl();

        // Save client state so we can validate in response
        $_SESSION['oauth_state'] = $oauthClient->getState();

        // Redirect to authorization endpoint
        header('Location: '.$authorizationUrl);
        exit();
    }
}
