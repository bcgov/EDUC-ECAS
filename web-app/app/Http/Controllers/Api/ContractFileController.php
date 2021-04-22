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


class ContractFileController extends Controller
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
        $apiurl = \Config::get('env.DYNAMICSBASEURL');
        $apiusr = \Config::get('env.DYNAMICS_USERNAME');
        $apipwd = \Config::get('env.DYNAMICS_PASSWORD');
        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', $apiurl.'/ContractFiles/ContractFile?assignmentId='.$profile_id, ['auth' => [$apiusr, $apipwd]]);

        $contractfile = json_decode($res->getBody()->getContents())->value;$pa = json_encode($contractfile);$pa2 = json_decode($pa,true);

        $newarr = $this->recursive_change_key($pa2, array(
                '@odata.etag' => 'Etag',
                'filesize' => 'FileSize',
                'filename' => 'FileName',
                'filesize@OData.Community.Display.V1.FormattedValue' => 'FileSizeVal',
                'notetext' => 'NoteText',
                'annotationid' => 'AnnotationId'
        ));

        return [
            'ContractFile'    => $newarr
        ];          
    }   

}
