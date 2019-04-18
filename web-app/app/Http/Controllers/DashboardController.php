<?php

namespace App\Http\Controllers;

use App\DynamicsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function dynamics()
    {
        $test_user_id = 'cf7837ae-0862-e911-a983-000d3af42a5a';

        $dynamics = new DynamicsRepository();
        return $dynamics->createProfile();
        return $dynamics->getProfile('69ec756c-6060-e911-a99d-000d3af4ae4d');
        return $dynamics->getDistricts();
    }

    public function index()
    {
        if ($this->userLoggedIn()) {
            $user = $this->loadUser();
        }
        else {
            $user = [
                'region'   => 'BC',
                'district' => '',
                'school'   => ''
            ];
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
        // Massage the data before we validate

        // Get rid of spaces
        $remove_spaces_from = ['postal_code', 'sin'];
        foreach ($remove_spaces_from as $field) {
            if (isset($request[$field])) {
                $request[$field] = preg_replace('/\s+/', '', $request[$field]);
            }
        }

        $this->validate($request, [
            'first_name'  => 'required',
            'last_name'   => 'required',
            'email'       => 'required|email',
            'phone'       => 'required',
            'address_1'   => 'required',
            'city'        => 'required',
            'region'      => 'required',
            'postal_code' => 'required|regex:/^\D\d\D\s?\d\D\d$/i',
            'sin'         => 'regex:/^\d{9}$/i'
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
                'postal_code.required' => 'Required',
                'postal_code.regex'    => 'Invalid Postal Code',
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

    /**
     * @return bool
     */
    private function userLoggedIn(): bool
    {
        return Session::has('user_id');
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
