<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\Credential;
use App\Dynamics\Decorators\CacheDecorator;
use App\Dynamics\ProfileCredential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class CredentialController extends BaseController
{

    public function __construct(Credential $model)
    {
        $this->model = $model;
    }

    public function index()
    {
        $credentials = (new CacheDecorator($this->model))->all();
        return $credentials->sort('name');

    }


    /*
 * Attach a credential to a User
 */
    public function create(Request $request)
    {
        Log::debug('STORE CREDENTIAL');
        Log::debug($request->all());

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

    public function delete(Request $request)
    {
        Log::debug('DELETE CREDENTIAL');
        Log::debug($request->all());

        ( new ProfileCredential())->delete($request['profile_credential_id']);

        return json_encode([
            'id' => $request['profile_credential_id']
        ]);
    }




}
