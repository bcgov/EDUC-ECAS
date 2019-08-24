<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\Decorators\CacheDecorator;
use App\Dynamics\Profile;
use App\Dynamics\ProfileCredential;
use App\Http\Controllers\EcasBaseController;
use App\Dynamics\Interfaces\iModelRepository;
use App\Http\Resources\ProfileCredentialResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;


class ProfileCredentialController extends EcasBaseController
{

    private $profile;
    private $profile_credential;

    public function __construct(ProfileCredential $profile_credential, Profile $profile)
    {

        $this->profile_credential       = $profile_credential;
        $this->profile                  = $profile;

    }

    public function index($profile_id)
    {

        $profile = $this->profile->get($profile_id);

        if($this->user->id <> $profile['federated_id']) {
            abort(401, 'unauthorized');
        }

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

    public function update($id, Request $request)
    {
        abort(405, 'method not allowed');
    }

    /*
 * Attach a credential to a User
 */
    public function store(Request $request, $profile_id )
    {

        // check user is updating their own profile
        $profile = $this->profile->get($profile_id);
        $this->checkOwner($request, $profile['federated_id']);


        $this->validate($request, [
            'credential_id' => 'required'
        ]);


        $profile_credential_id = $this->profile_credential->create([
            'contact_id'    => $profile['id'],
            'credential_id' => $request['credential_id'],
            'verified'      => ProfileCredential::$status['Unverified']
        ]);

        $new_record = $this->profile_credential->get($profile_credential_id);

        return new ProfileCredentialResource($new_record);
    }

    public function destroy(Request $request, $profile_id, $id)
    {
        // check user is updating their own profile
        $profile = $this->profile->get($profile_id);
        $this->checkOwner($request, $profile['federated_id']);


        $this->profile_credential->delete($id);

        return Response::json(['message' => 'success'], 204);
    }



}
