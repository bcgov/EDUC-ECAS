<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\Decorators\CacheDecorator;
use App\Interfaces\iModelRepository;
use App\Http\Resources\ProfileCredentialResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;


class ProfileCredentialController extends BaseController
{

    private $profile;

    public function __construct(iModelRepository $model)
    {

        parent::__construct($model);

        $repository                     = env('DATASET') == 'Dynamics' ? 'Dynamics' : 'MockEntities\Repository';
        $this->profile                  = ( new CacheDecorator(App::make('App\\' . $repository .'\Profile')));

    }

    public function index($profile_id)
    {

        $profile = $this->profile->get($profile_id);

        if($this->user->id <> $profile['federated_id']) {
            abort(401, 'unauthorized');
        }

        $credentials =  $this->model->filter(['contact_id' => $profile['id']]);

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
    public function store(Request $request, $profile_id )
    {

        // check user is updating their own profile
        $profile = $this->profile->get($profile_id);
        $this->checkOwner($request, $profile['federated_id']);


        $this->validate($request, [
            'credential_id' => 'required'
        ]);


        $profile_credential_id = $this->model->create([
            'contact_id'    => $profile['id'],
            'credential_id' => $request['credential_id'],
            'verified'      => 'Unverified'
        ]);

        return 'success: ' . $profile_credential_id;

        //$new_record = $this->model->all();

        //return Response::json(new ProfileCredentialResource($new_record), 200);
    }

    public function destroy($profile_id, $id)
    {
        $profile = $this->profile->get($profile_id);

        if($this->user->id <> $profile['federated_id']) {
            abort(401, 'unauthorized');
        }

        $this->model->delete($id);

        return Response::json(['message' => 'success'], 204);
    }



}
