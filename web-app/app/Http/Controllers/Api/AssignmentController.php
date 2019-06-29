<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\Assignment;
use App\Http\Controllers\Interfaces\iApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AssignmentController extends BaseController implements iApiController
{

    public function __construct(Assignment $model)
    {
        $this->model = $model;
    }



    public function create(Request $request)
    {
        Log::debug('STORE ASSIGNMENT');
        Log::debug($request->all());

        $action = $request['action'];

        $this->assignment_statuses->all();

        if ($action == Assignment::APPLIED_STATUS) {

            $assignment_id = Assignment::create([
                'user_id'    => $this->userId(),
                'session_id' => $request['session_id']
            ]);

            Log::debug('created assignment id: ' . $assignment_id);
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
        }

        return json_encode([
            'session_id' => $request['session_id'],
            'status'     => $action
        ]);
    }




}
