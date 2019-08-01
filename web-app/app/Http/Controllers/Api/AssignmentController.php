<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\Decorators\CacheDecorator;
use App\Interfaces\iModelRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Response;

class AssignmentController extends BaseController
{

    private $profile;
    private $assignment_statuses;

    public function __construct(iModelRepository $model)
    {

        parent::__construct($model);

        $repository                     = env('DATASET') == 'Dynamics' ? 'Dynamics' : 'MockEntities\Repository';
        $this->assignment_statuses      = ( new CacheDecorator(App::make('App\\' . $repository .'\AssignmentStatus')))->all();
        $this->profile                  = ( new CacheDecorator(App::make('App\\' . $repository .'\Profile')));

    }


    public function index($profile_id)
    {

        $profile = $this->profile->get($profile_id);

        if($this->user->id <> $profile['federated_id']) {
            abort(401, 'unauthorized');
        }

        return $this->model->filter(['contact_id' => $profile['id']]);
    }


    public function store(Request $request, $profile_id)
    {

        // check user is updating their own profile
        $profile = $this->profile->get($profile_id);
        $this->checkOwner($request, $profile['federated_id']);

        // TODO - validate record



        $new_record_id = $this->model->create([
            'contact_id' => $profile['id'],
            'session_id' => $request['session_id']
        ]);


        // TODO - Remove before flight
        // TODO - Not sure why we're updating records in the store() method -- ask Dirk
/*
        if ($action == Assignment::APPLIED_STATUS) {

        }
        elseif ($action == Assignment::ACCEPTED_STATUS) {
            $assignment_status_key = array_search(Assignment::ACCEPTED_STATUS, array_column($this->assignment_statuses, 'name'));
            Assignment::update($request['assignment_id'], ['status' => $this->assignment_statuses[$assignment_status_key]['id']]);
        }
        elseif ($action == Assignment::DECLINED_STATUS) {
            $assignment_status_key = array_search(Assignment::DECLINED_STATUS, array_column($this->assignment_statuses, 'name'));
            Assignment::update($request['assignment_id'], [
                'status' => $this->assignment_statuses[$assignment_status_key]['id'],
                'state'  => Assignment::INACTIVE_STATE
            ]);
        }*/

        return Response::json($this->model->get($new_record_id), 200);
    }

    public function show($id)
    {
        abort(405, 'method not allowed');
    }

    public function update($id, Request $request)
    {
        abort(405, 'method not allowed');
    }


    public function destroy($id)
    {
        abort(405, 'method not allowed');
    }


}
