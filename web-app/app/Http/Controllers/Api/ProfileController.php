<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\Interfaces\iCountry;
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
    protected $country;


    public function __construct(iProfile $profile,
                                iSchool $school,
                                iDistrict $district,
                                iRegion $region,
                                iCountry $country,
                                KeycloakGuard $auth)
    {
        $this->profile              = $profile;
        $this->school               = $school;
        $this->district             = $district;
        $this->region               = $region;
        $this->country              = $country;
        $this->authentication       = $auth;

    }


    public function index()
    {

        abort(404, 'method not available');

    }


    public function show($profile_id)
    {

        $keycloak_user = $this->authentication->user();
        $unverified_profile = $this->profile->get($profile_id);
        
        $profile = parent::checkUserIsAuthorized($keycloak_user, $unverified_profile);


        return new ProfileResource($profile, $this->school, $this->district, $this->region, $this->country);
    }


    /*
 * Create the Profile record (Contact in Dynamics)
 */
    public function store(ProfileRequest $request)
    {
        $user = $this->authentication->user();

        $data                   = $request->validated();
        $data['federated_id']   = $user['sub'];
        $data['username']       = $this->removeSuffix($user['preferred_username']);

        $new_model_id = $this->profile->create($data);

        $profile = $this->profile->get($new_model_id);

        $resource = new ProfileResource($profile , $this->school, $this->district, $this->region, $this->country);

        return $resource;
    }

    /*
     * Update an existing user Profile (Contact in Dynamics)
     */
    public function update(ProfileRequest $request, $profile_id)
    {

        $keycloak_user = $this->authentication->user();
        $unverified_profile = $this->profile->get($profile_id);

        $profile = parent::checkUserIsAuthorized($keycloak_user, $unverified_profile);


        $data                   = $request->validated();
        $data['federated_id']   = $profile['federated_id'];
        $data['username']       = self::removeSuffix($keycloak_user['preferred_username']);


        $profile = $this->profile->update($profile['id'], $data);

        return new ProfileResource($profile , $this->school, $this->district, $this->region, $this->country);
    }


    public function destroy($id)
    {
        abort(404, 'method not available');

    }

    private static function removeSuffix($username)
    {
        // remove `@bceid` from user names
        return explode('@bceid', $username)[0];

    }
    

}
