<?php

namespace App\Http\Controllers\Api;

use App\Dynamics\Assignment;
use App\Dynamics\Interfaces\iAssignment;
use App\Dynamics\Interfaces\iAssignmentStatus;
use App\Dynamics\Interfaces\iContractStage;
use App\Dynamics\Interfaces\iContract;
use App\Dynamics\Interfaces\iProfile;
use App\Dynamics\Interfaces\iRole;
use App\Dynamics\Interfaces\iSession;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssignmentUpdateRequest;
use App\Http\Resources\AssignmentResource;
use App\Keycloak\KeycloakGuard;
use Illuminate\Http\Request;
use Carbon\Carbon;


class PortalAssignmentController extends Controller
{

    private $profile;
    private $assignment_status;
    private $contract_stage;
    private $contract;
    private $role;
    private $assignment;
    private $session;
    private $authentication;


    public function __construct(iAssignment $assignment,
                                iProfile $profile,
                                iAssignmentStatus $assignment_status,
                                iRole $role,
                                iContractStage $stage,
                                iContract $contract,
                                iSession $session,
                                KeycloakGuard $auth)
    {
        $this->profile              = $profile;
        $this->assignment           = $assignment;
        $this->assignment_status    = $assignment_status;
        $this->contract_stage       = $stage;
        $this->contract             = $contract;
        $this->role                 = $role;
        $this->session              = $session;
        $this->authentication       = $auth;

    }

    public function recursive_change_key($arr, $set) {
        if (is_array($arr) && is_array($set)) {
            $newArr = array();
            foreach ($arr as $k => $v) {
                $key = array_key_exists( $k, $set) ? $set[$k] : $k;
                $newArr[$key] = is_array($v) ? $this->recursive_change_key($v, $set) : $v;
            }
            return $newArr;
        }
        return $arr;    
    }

    public function index($profile_id)
    {
        //$keycloak_user = $this->authentication->user();
        //$unverified_profile = $this->profile->get($profile_id);

        //$profile = parent::checkUserIsAuthorized($keycloak_user, $unverified_profile);

        $apiurl = \Config::get('env.DYNAMICSBASEURL');
        $apiusr = \Config::get('env.DYNAMICS_USERNAME');
        $apipwd = \Config::get('env.DYNAMICS_PASSWORD');
        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', $apiurl.'/EcasPortalAssignments?contactId='.$profile_id, ['auth' => [$apiusr, $apipwd]]);

        $portalassignment = json_decode($res->getBody()->getContents())->value;$pa = json_encode($portalassignment);$pa2 = json_decode($pa,true);

        for($i=0;$i<count($pa2);$i++) { 
            $NewStartDate = date("M j", strtotime($pa2[$i]['startDate@OData.Community.Display.V1.FormattedValue']));  
            $NewStartYear = date("Y", strtotime($pa2[$i]['startDate@OData.Community.Display.V1.FormattedValue']));  
            $NewEndDate = date("M j", strtotime($pa2[$i]['endDate@OData.Community.Display.V1.FormattedValue'])); 
            $NewEndYear = date("Y", strtotime($pa2[$i]['endDate@OData.Community.Display.V1.FormattedValue']));  
            $NewEndYear != $NewStartYear ? $NewStartYear = ', '.$NewStartYear : $NewStartYear = '';
            $Date = $NewStartDate.$NewStartYear.' - '.$NewEndDate.', '.$NewEndYear;
            $pa2[$i]['Date'] = $Date;             
        }

        $newarr = $this->recursive_change_key($pa2, array(
                '@odata.etag' => 'Etag',
                'educ_contractstage@OData.Community.Display.V1.FormattedValue' => 'EducContractStage',
                'educ_contractstage' => 'EducContractStageVal',
                'educ_assignmentid'  => 'EducAssignmentId',
                'endDate@OData.Community.Display.V1.FormattedValue' => 'EndDate',
                'endDate' => 'EndDateVal',
                'startDate@OData.Community.Display.V1.FormattedValue' => 'StartDate',
                'startDate' => 'StartDateVal',
                'sessionType@OData.Community.Display.V1.FormattedValue' => 'SessionType',
                'sessionType' => 'SessionTypeVal'
        ));
        return [
            'PortalAssignment'    => $newarr
        ];
       //return $this->assignment->filter(['contact_id' => $profile['id']]);
       
    }

}
