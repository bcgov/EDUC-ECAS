<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\ProfileCredential;
use App\Http\Resources\ProfileCredentialResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;


class ProfileCredentialController extends BaseController
{

    public function index()
    {
        $credentials =  $this->model->filter(['user_id'=>Auth::id()]);

        $filtered = $credentials->filter( function ($credential) {
            return $credential['verified'] <> "No";
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
    public function store(Request $request)
    {
        Log::debug('STORE CREDENTIAL');

        $this->validate($request, [
            'credential_id' => 'required'
        ]);


        $profile_credential_id = $this->model->create([
            'user_id'       => Auth::id(),
            'credential_id' => $request['credential_id'],
            'verified'      => 'Unverified'
        ]);

        $new_record = $this->model->get($profile_credential_id);

        return Response::json(new ProfileCredentialResource($new_record), 200);
    }

    public function destroy($id)
    {
        Log::debug('DELETE CREDENTIAL');

        // check: does the user own this record?
        $record = $this->model->get($id);

        if($record['user_id'] <> Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $this->model->delete($id);

        return Response::json(['message' => 'success'], 204);
    }



}
