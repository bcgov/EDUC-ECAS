<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\ProfileCredential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class ProfileCredentialController extends BaseController
{

    public function index()
    {
        // TODO - use filter() to return only those records associated with the user
        return $this->model->all();
        //return $this->model->filter(['user_id'=>$federated_id]);
    }

    public function show($id)
    {
        // TODO: Implement show() method.
    }

    public function update($id, Request $request)
    {
        // TODO: Implement update() method.
    }

    public function create(Request $request)
    {
        dd('this method has been replaced with store()');
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

        $user = $this->user();

        $profile_credential_id = ( new ProfileCredential())->create([
            'user_id'       => $user['id'],
            'credential_id' => $request['credential_id']
        ]);

        return json_encode([
            'id'            => $profile_credential_id,
            'credential_id' => $request['credential_id']
        ]);
    }

    public function destroy($id)
    {
        Log::debug('DELETE CREDENTIAL');

        ( new ProfileCredential())->delete($id);

        return true;  // TODO - return appropriate response
    }



}
