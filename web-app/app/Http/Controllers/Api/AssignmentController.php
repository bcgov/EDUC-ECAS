<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\Assignment;
use App\Dynamics\Interfaces\iAssignment;
use App\Dynamics\Interfaces\iAssignmentStatus;
use App\Dynamics\Interfaces\iContractStage;
use App\Dynamics\Interfaces\iProfile;
use App\Dynamics\Interfaces\iRole;
use App\Dynamics\Interfaces\iSession;
use App\Dynamics\Session;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssignmentUpdateRequest;
use App\Http\Resources\AssignmentResource;
use App\Keycloak\KeycloakGuard;
use Illuminate\Http\Request;


class AssignmentController extends Controller
{

    private $profile;
    private $assignment_status;
    private $contract_stage;
    private $role;
    private $assignment;
    private $session;
    private $authentication;


    public function __construct(iAssignment $assignment,
                                iProfile $profile,
                                iAssignmentStatus $assignment_status,
                                iRole $role,
                                iContractStage $stage,
                                iSession $session,
                                KeycloakGuard $auth)
    {

        $this->profile              = $profile;
        $this->assignment           = $assignment;
        $this->assignment_status    = $assignment_status;
        $this->contract_stage       = $stage;
        $this->role                 = $role;
        $this->session              = $session;
        $this->authentication       = $auth;

    }


    public function index($profile_id)
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

        // check that the user is attempting to create records under their profile
        $profile = $this->profile->get($profile_id);
        if( $profile['id'] <> $profile_id ) {
            abort(401, 'unauthorized');
        }

        $validatedData = $request->validate([
            'session_id' => ['required', 'string', 'max:50' ]
        ]);

        // check that the session is Open and Published
        $session = $this->session->get($validatedData['session_id']);
        if( ! ($session['is_published'] && $session['status'] == Session::$status['Open'])) {
            abort(401, 'unauthorized');
        }

        // check that user has no current assignments
        $check_no_assignments = $this->assignment->filter(['contact_id' => $profile_id]);
        if( count($check_no_assignments) > 0 ) {
            abort(401, 'unauthorized');
        }

        $validatedData['contact_id'] = $profile_id;

        $new_record_id = $this->assignment->create($validatedData);

        return new AssignmentResource($this->assignment->get($new_record_id));
    }




    public function update( $profile_id, $assignment_id, AssignmentUpdateRequest $request )
    {

        $federated_id = $this->authentication->id();

        // check user is updating their own profile
        $profile = $this->profile->get($profile_id);

        if($federated_id <> $profile['federated_id'])
        {
            abort(401, 'unauthorized');
        }

        $assignment = $this->assignment->get($assignment_id);

        // check user is not modifying someone else's assignment
        if($profile_id != $assignment['contact_id'])
        {
            abort(401, 'unauthorized');
        }

        // convert the requested action into a data array to be posted to the Dynamics api
        $data = $this->convertAction($this->assignment_status->all(), $request['action'], $assignment );


        $updated_assignment = $this->assignment->update($assignment_id, $data );

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


    /**
     * Generate post data from the user's requested action
     *
     * @param $assignment_statuses
     * @param $action
     * @param $current_assignment
     * @return array
     */
    protected function convertAction( $assignment_statuses, $action, $current_assignment )
    {

        $current_status = $assignment_statuses->firstWhere('id', $current_assignment['status_id']);

        $data = [];

        // Teachers can Decline only if the current assignment status is Applied, Selected, Invited or Accepted
        if($current_status['name'] == Assignment::APPLIED_STATUS ||
            $current_status['name'] == Assignment::SELECTED_STATUS ||
              $current_status['name'] == Assignment::ACCEPTED_STATUS ||
                $current_status['name'] == Assignment::INVITED_STATUS)   {

            if ($action == Assignment::DECLINED_STATUS) {

                $data['status_id']  = $assignment_statuses->firstWhere('name' , Assignment::DECLINED_STATUS)['id'];
                $data['state']      = Assignment::INACTIVE_STATE;

            }

        }


        // Teachers can only Accept if the assignment status is Invited
        if($current_status['name'] == Assignment::INVITED_STATUS )   {

            if ($action == Assignment::ACCEPTED_STATUS) {

                $data['status_id']  = $assignment_statuses->firstWhere('name' , Assignment::ACCEPTED_STATUS)['id'];

            }

        }


        return $data;


    }


}
