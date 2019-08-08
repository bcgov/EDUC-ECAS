<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\Assignment;
use App\Dynamics\Decorators\CacheDecorator;
use App\Http\Resources\AssignmentResource;
use App\Interfaces\iModelRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class AssignmentController extends ApiBaseController
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
            // The initial status automatically defaults to `Applied`
        ]);

        return new AssignmentResource($this->model->get($new_record_id));
    }

    public function update(Request $request, $profile_id, $assignment_id)
    {

        // check user is updating their own profile
        $profile = $this->profile->get($profile_id);
        $this->checkOwner($request, $profile['federated_id']);

        // TODO - validate record - there's too much logic in the Vue component. Javascript data can be manipulated by the user

        Log::debug('Assignment update - requested action: ' . $request['action'] );

        if ($request['action'] == Assignment::WITHDREW_STATUS) {
            $new_status = $this->assignment_statuses->firstWhere('name', Assignment::WITHDREW_STATUS);

            $updated_assignment = $this->model->update($assignment_id, [
                'status' => $new_status['id'],
                'state'  => Assignment::INACTIVE_STATE
            ]);
        }
        elseif ($request['action'] == Assignment::APPLIED_STATUS) {
            $assignment_status_key = array_search(Assignment::APPLIED_STATUS, array_column($this->assignment_statuses, 'name'));

            $updated_assignment = $this->model->update($assignment_id, [
                'status' => $this->assignment_statuses[$assignment_status_key]['id']
            ]);
        }
        elseif ($request['action'] == Assignment::ACCEPTED_STATUS) {
            $assignment_status_key = array_search(Assignment::ACCEPTED_STATUS, array_column($this->assignment_statuses, 'name'));

            $updated_assignment = $this->model->update($assignment_id, [
                'status' => $this->assignment_statuses[$assignment_status_key]['id']
            ]);
        }
        elseif ($request['action'] == Assignment::DECLINED_STATUS) {
            $assignment_status_key = array_search(Assignment::DECLINED_STATUS, array_column($this->assignment_statuses, 'name'));

            $updated_assignment = $this->model->update($assignment_id, [
                'status' => $this->assignment_statuses[$assignment_status_key]['id'],
                'state'  => Assignment::INACTIVE_STATE
            ]);
        }


        return new AssignmentResource($updated_assignment);
    }

    public function show($id)
    {
        abort(405, 'method not allowed');
    }


    public function destroy($id)
    {
        abort(405, 'method not allowed');
    }


}