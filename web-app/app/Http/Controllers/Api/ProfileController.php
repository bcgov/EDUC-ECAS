<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\Interfaces\iDistrict;
use App\Dynamics\Interfaces\iProfile;
use App\Dynamics\Interfaces\iRegion;
use App\Dynamics\Interfaces\iSchool;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Keycloak\KeycloakGuard;


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

        abort(404, 'method not available');

    }


    public function show($profile_id)
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
    public function store(ProfileRequest $request)
    {
        $user_id = $this->authentication->id();

        $data = $request->all();
        $data['federated_id'] = $user_id;

        $new_model_id = $this->profile->create($data);

        $profile = $this->profile->get($new_model_id);

        $resource = new ProfileResource($profile , $this->school, $this->district, $this->region);

        return $resource;
    }

    /*
     * Update an existing user Profile (Contact in Dynamics)
     */
    public function update($id, ProfileRequest $request)
    {

        $federated_id = $this->authentication->id();

        // check user is updating their own profile
        $profile = $this->profile->get($id);

        if($federated_id <> $profile['federated_id'])
        {
            abort(401, 'unauthorized');
        }

        $data = $request->all();
        $data['federated_id'] = $profile['federated_id'];


        $profile = $this->profile->update($profile['id'], $data);

        return new ProfileResource($profile , $this->school, $this->district, $this->region);
    }


    public function destroy($id)
    {
        abort(404, 'method not available');

    }









}
