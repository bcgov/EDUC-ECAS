<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\Interfaces\iCredential;
use App\Dynamics\Interfaces\iProfile;
use App\Dynamics\Interfaces\iProfileCredential;
use App\Dynamics\ProfileCredential;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileCredentialResource;
use App\Keycloak\KeycloakGuard;
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

        // check user is updating their own profile

        $user_id = $this->authentication->id();

        if( ! $user_id ) {
            abort(401, 'unauthorized');
        }

        $profile = $this->profile->get($profile_id);


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

    public function update(Request $request, $id)
    {
        abort(405, 'method not allowed');
    }

    /*
 * Attach a credential to a User
 */
    public function store(Request $request, $profile_id )
    {

        $user_id = $this->authentication->id();

        if( ! $user_id ) {
            abort(401, 'unauthorized');
        }

        // check user is updating their own profile
        $profile = $this->profile->get($profile_id);


        $this->validate($request, [
            'credential_id' => 'required',
        ]);


        $profile_credential_id = $this->profile_credential->create([
            'contact_id'    => $profile['id'],
            'credential_id' => $request['credential_id'],
            'verified'      => ProfileCredential::$status['Unverified']  // all new profile credentials are 'Unverified'
        ]);

        $new_record = $this->profile_credential->get($profile_credential_id);

        $new_record['credential'] = $this->credential->get($new_record['credential_id']);

        return new ProfileCredentialResource($new_record);
    }

    public function destroy(Request $request, $profile_id, $id)
    {

        $user_id = $this->authentication->id();

        if( ! $user_id ) {
            abort(401, 'unauthorized');
        }

        // check user is deleting their own profile credential record
        $profile = $this->profile->get($profile_id);

        $record_for_deletion = $this->profile_credential->get($id);

        if( $record_for_deletion['contact_id'] <> $profile['id'] ) {
            abort(401, 'unauthorized');
        }

        $this->profile_credential->delete($id);

        return Response::json(['message' => 'success'], 204);
    }



}
