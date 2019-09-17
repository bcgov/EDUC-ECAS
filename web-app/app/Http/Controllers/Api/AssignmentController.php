<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\Assignment;
use App\Dynamics\Interfaces\iAssignment;
use App\Dynamics\Interfaces\iAssignmentStatus;
use App\Dynamics\Interfaces\iContractStage;
use App\Dynamics\Interfaces\iProfile;
use App\Dynamics\Interfaces\iRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\AssignmentResource;
use App\Keycloak\KeycloakGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class AssignmentController extends Controller
{

    private $profile;
    private $assignment_status;
    private $contract_stage;
    private $role;
    private $assignment;
    private $authentication;

    public function __construct(iAssignment $assignment,
                                iProfile $profile,
                                iAssignmentStatus $assignment_status,
                                iRole $role,
                                iContractStage $stage,
                                KeycloakGuard $auth)
    {

        $this->profile              = $profile;
        $this->assignment           = $assignment;
        $this->assignment_status    = $assignment_status;
        $this->contract_stage       = $stage;
        $this->role                 = $role;
        $this->authentication       = $auth;

    }


    public function index($profile_id, Request $request)
    {
        $user_id = $this->authentication->id();

        if( ! $user_id ) {
            abort(401, 'unauthorized');
        }

        $profile = $this->profile->get($profile_id);


        if( $profile['id'] <> $profile_id ) {
            abort(401, 'unauthorized');
        }


        return $this->assignment->filter(['contact_id' => $profile['id']]);
    }



    public function store( $profile_id, Request $request)
    {

        $user_id = $this->authentication->id();

        if( ! $user_id ) {
            abort(401, 'unauthorized');
        }

        $profile = $this->profile->get($profile_id);


        if( $profile['id'] <> $profile_id ) {
            abort(401, 'unauthorized');
        }

        // TODO - validate record
        $data                   = $request->all();
        $data['contact_id']     = $profile_id;

        $new_record_id = $this->assignment->create($data);

        $assignment                         = $this->assignment->get($new_record_id);


        return new AssignmentResource($assignment);
    }

    public function update( $profile_id, $assignment_id, Request $request )
    {

        $federated_id = $this->authentication->id();

        // check user is updating their own profile
        $profile = $this->profile->get($profile_id);

        if($federated_id <> $profile['federated_id'])
        {
            abort(401, 'unauthorized');
        }

        $assignment_statuses = $this->assignment_status->all();

        // TODO - validate record - there's too much logic in the Vue component. Javascript data can be manipulated by the user

        Log::debug('Assignment update - requested action: ' . $request['action'] );

        if ($request['action'] == Assignment::WITHDREW_STATUS) {
            $new_status = $assignment_statuses->firstWhere('name', Assignment::WITHDREW_STATUS);

            $updated_assignment = $this->assignment->update($assignment_id, [
                'status_id' => $new_status['id'],
                'state'  => Assignment::INACTIVE_STATE
            ]);
        }
        elseif ($request['action'] == Assignment::APPLIED_STATUS) {
            $assignment_status = $assignment_statuses->firstWhere('name' , Assignment::APPLIED_STATUS);


            $updated_assignment = $this->assignment->update($assignment_id, [
                'status_id'            => $assignment_status['id']
            ]);
        }
        elseif ($request['action'] == Assignment::ACCEPTED_STATUS) {
            $assignment_status = $assignment_statuses->firstWhere('name' , Assignment::ACCEPTED_STATUS);

            $updated_assignment = $this->assignment->update($assignment_id, [
                'status_id' => $assignment_status['id']
            ]);
        }
        elseif ($request['action'] == Assignment::DECLINED_STATUS) {
            $assignment_status = $assignment_statuses->firstWhere('name' , Assignment::DECLINED_STATUS);


            $updated_assignment = $this->assignment->update($assignment_id, [
                'status_id' => $assignment_status['id'],
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
