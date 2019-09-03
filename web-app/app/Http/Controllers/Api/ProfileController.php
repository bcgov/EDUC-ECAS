<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\Interfaces\iDistrict;
use App\Dynamics\Interfaces\iProfile;
use App\Dynamics\Interfaces\iRegion;
use App\Dynamics\Interfaces\iSchool;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResource;
use App\Keycloak\KeycloakGuard;
use App\Rules\SocialInsuranceNumberRule;
use Illuminate\Http\Request;



class ProfileController extends Controller
{

    protected $profile;
    protected $school;
    protected $district;
    protected $region;
    protected $authentication;


    public function __construct(iProfile $profile, iSchool $school, iDistrict $district, iRegion $region, KeycloakGuard $auth)
    {
        $this->profile              = $profile;
        $this->school               = $school;
        $this->district             = $district;
        $this->region               = $region;
        $this->authentication       = $auth;

    }


    public function index()
    {

        abort('405');  // disabled

    }


    public function show($profile_id, Request $request)
    {

        $user_id = $this->authentication->id();

        if( ! $user_id ) {
            abort(401, 'unauthorized');
        }

        $profile = $this->profile->get($profile_id);


        if( $profile['id'] <> $profile_id ) {
            abort(401, 'unauthorized');
        }


        return new ProfileResource($profile, $this->school, $this->district, $this->region);
    }


    /*
 * Create the Profile record (Contact in Dynamics)
 */
    public function store(Request $request)
    {
        $user_id = $this->authentication->id();

        $this->validateProfileRequest($request);
        $data = $request->all();  // TODO - Remove before flight - dangerous
        $data['federated_id'] = $user_id;

        $new_model_id = $this->profile->create($data);

        $profile = $this->profile->get($new_model_id);

        $resource = new ProfileResource($profile , $this->school, $this->district, $this->region);

        return $resource;
    }

    /*
     * Update an existing user Profile (Contact in Dynamics)
     */
    public function update($id, Request $request)
    {

        $federated_id = $this->authentication->id();

        // check user is updating their own profile
        $profile = $this->profile->get($id);

        if($federated_id <> $profile['federated_id'])
        {
            abort(401, 'unauthorized');
        }

        $this->validateProfileRequest($request);

        // TODO - Remove before flight - map specific fields
        $data = $request->all();
        $data['federated_id'] = $profile['federated_id'];


        // TODO - swap $id with $profile['id'] below
        $response = $this->profile->update($profile['id'], $data);

        return $response;
    }


    public function destroy($federated_id)
    {
        abort(404, 'method not available');

    }


    /*
     * Create a new Assignment
     * Or change the status of an existing Assignment
     */
    private function validateProfileRequest(Request $request): Request
    {
        // Get rid of spaces
        $remove_spaces_from = ['sin'];
        foreach ($remove_spaces_from as $field) {
            if (isset($request[$field])) {
                $request[$field] = preg_replace('/\s+/', '', $request[$field]);
            }
        }

        // Sanitize phone numbers, remove everything that isn't a number
        $sanitize_to_integer = ['phone'];
        foreach ($sanitize_to_integer as $field) {
            $request[$field] = preg_replace('/[^0-9.]/', '', $request[$field]);
        }

        // If we pass in blank look-up ids Dynamics freaks out
        // remove options that are blank
        $remove_blank_options = ['district_id', 'school_id'];
        foreach ($remove_blank_options as $field) {
            if ( ! $request[$field]) {
                unset($request[$field]);
            }
        }

        $this->validate($request, [
            'first_name'                    => 'required',
            'last_name'                     => 'required',
            'email'                         => 'required|email',
            'phone'                         => 'required',
            'address_1'                     => 'required',
            'city'                          => 'required',
            'region'                        => 'required',
            'postal_code'                   => 'required|regex:/^\D\d\D\s?\d\D\d$/i',
            'social_insurance_number'       => [ new SocialInsuranceNumberRule() ]
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

        return $request;
    }




}
