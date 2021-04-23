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


class FileUploadController extends Controller
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

    public function store($assignment_id)
    {
        $file     = request('file');
        $fileName = $file->getClientOriginalName();
        $realPath = $file->getRealPath();
        $data = base64_encode(file_get_contents($realPath));

        $apiurl = \Config::get('env.DYNAMICSBASEURL');
        $apiusr = \Config::get('env.DYNAMICS_USERNAME');
        $apipwd = \Config::get('env.DYNAMICS_PASSWORD');
        $client = new \GuzzleHttp\Client();
        $url = $apiurl.'/ContractFiles/UploadFile';
                $res = $client->post($url, [
                    'headers' => ['Content-type' => 'application/json'],
                    'auth' => [
                        $apiusr, 
                        $apipwd
                    ],
                    'json' => [
                        "subject"   => "Upload to API",
                        "filename"  => $fileName,
                        "objectid_educ_assignment@odata.bind"   => "/educ_assignments(".$assignment_id.")",
                        "documentbody"   => $data
                    ], 
                ]);               

        $fileupload = json_decode($res->getBody()->getContents());

        return [
            'UploadFile'    => $fileupload
        ];        
    }   

}
