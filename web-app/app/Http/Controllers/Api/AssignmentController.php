<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\Assignment;
use App\Dynamics\Interfaces\iAssignment;
use App\Dynamics\Interfaces\iAssignmentStatus;
use App\Dynamics\Interfaces\iContractStage;
use App\Dynamics\Interfaces\iProfile;
use App\Dynamics\Interfaces\iRole;
use App\Dynamics\Interfaces\iSession;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssignmentUpdateRequest;
use App\Http\Resources\AssignmentResource;
use App\Keycloak\KeycloakGuard;
use Illuminate\Http\Request;


class AsignmentController extends Controller
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
        $keycloak_user = $this->authentication->user();
        $unverified_profile = $this->profile->get($profile_id);

        $profile = parent::checkUserIsAuthorized($keycloak_user, $unverified_profile);

        return $this->assignment->filter(['contact_id' => $profile['id']]);
    }



    public function store(Request $request, $profile_id)
    {

        $keycloak_user = $this->authentication->user();
        $unverified_profile = $this->profile->get($profile_id);

        $profile = parent::checkUserIsAuthorized($keycloak_user, $unverified_profile);

        $validatedData = $request->validate([
            'session_id' => ['required', 'string', 'max:50' ]
        ]);

        $validatedData['contact_id'] = $profile['id'];

        $new_record_id = $this->assignment->create($validatedData);

        return new AssignmentResource($this->assignment->get($new_record_id));
    }




    public function update(AssignmentUpdateRequest $request, $profile_id, $assignment_id )
    {

        $keycloak_user = $this->authentication->user();
        $unverified_profile = $this->profile->get($profile_id);

        $profile = parent::checkUserIsAuthorized($keycloak_user, $unverified_profile);

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

            if ( $action == Assignment::DECLINED_STATUS  ) {

                $data['status_id']  = $assignment_statuses->firstWhere('name' , Assignment::DECLINED_STATUS)['id'];
                $data['state']      = Assignment::INACTIVE_STATE;

            }

        }

        // Teachers can Decline only if the current assignment status is Applied, Selected, Invited or Accepted
        if($current_status['name'] == Assignment::APPLIED_STATUS ||
            $current_status['name'] == Assignment::SELECTED_STATUS ||
              $current_status['name'] == Assignment::ACCEPTED_STATUS ||
                $current_status['name'] == Assignment::INVITED_STATUS)   {

            if (   $action == Assignment::WITHDREW_STATUS ) {

                $data['status_id']  = $assignment_statuses->firstWhere('name' , Assignment::WITHDREW_STATUS )['id'];
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
