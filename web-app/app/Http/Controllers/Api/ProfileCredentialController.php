<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\Interfaces\iCredential;
use App\Dynamics\Interfaces\iProfile;
use App\Dynamics\Interfaces\iProfileCredential;
use App\Dynamics\ProfileCredential;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileCredentialResource;
use App\Keycloak\KeycloakGuard;
use App\Rules\ValidProfileCredential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;


class ProfileCredentialController extends Controller
{

    private $profile;
    private $profile_credential;
    private $authentication;
    private $credential;

    public function __construct(iProfileCredential $profile_credential, iProfile $profile, iCredential $credential, KeycloakGuard $auth)
    {

        $this->profile_credential       = $profile_credential;
        $this->profile                  = $profile;
        $this->credential               = $credential;
        $this->authentication           = $auth;

    }

    public function index($profile_id)
    {

        $keycloak_user = $this->authentication->user();
        $unverified_profile = $this->profile->get($profile_id);

        $profile = parent::checkUserIsAuthorized($keycloak_user, $unverified_profile);

        $credentials =  $this->profile_credential->filter(['contact_id' => $profile['id']]);

        $filtered = $credentials->filter( function ($credential) {
            return $credential['verified'] <> ProfileCredential::$status['No'];
        });

        return $filtered;

    }

    public function show($id)
    {
        abort(405, 'method not allowed');
    }

    public function update($id)
    {
        abort(405, 'method not allowed');
    }

    /*
 * Attach a credential to a User
 */
    public function store(Request $request, $profile_id)
    {

        $keycloak_user = $this->authentication->user();
        $unverified_profile = $this->profile->get($profile_id);

        $profile = parent::checkUserIsAuthorized($keycloak_user, $unverified_profile);

        $this->validate($request, [
            'credential_id' => [ 'required','string','max:50', new ValidProfileCredential($this->credential) ],
            'year' => [ 'integer','min:2019','max:2050','nullable' ]
        ]);


        $profile_credential_id = $this->profile_credential->create([
            'contact_id'    => $profile['id'],
            'credential_id' => $request['credential_id'],
            'verified'      => ProfileCredential::$status['Unverified'],  // all new profile credentials are 'Unverified'
            'year'          => $request['year']
        ]);

        $new_record = $this->profile_credential->get($profile_credential_id);

        $new_record['credential'] = $this->credential->get($new_record['credential_id']);

        return new ProfileCredentialResource($new_record);
    }

    public function destroy($profile_id, $credential_id)
    {

        $keycloak_user = $this->authentication->user();
        $unverified_profile = $this->profile->get($profile_id);

        $profile = parent::checkUserIsAuthorized($keycloak_user, $unverified_profile);

        $record_for_deletion = $this->profile_credential->get($credential_id);

        if( $record_for_deletion['contact_id'] <> $profile['id'] ) {
            abort(401, 'unauthorized');
        }

        $this->profile_credential->delete($credential_id);

        return Response::json(['message' => 'success'], 204);
    }



}
