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


class FileDownloadController extends Controller
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

    public function index($annotation_id)
    { 
        $temp_file = tempnam(sys_get_temp_dir(), 'Etmp');
        $resource = fopen($temp_file, 'w');
        $apiurl = \Config::get('env.DYNAMICSBASEURL');
        $apiusr = \Config::get('env.DYNAMICS_USERNAME');
        $apipwd = \Config::get('env.DYNAMICS_PASSWORD');
        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', $apiurl.'/ContractFiles/GetFile?annotationid='.$annotation_id, ['auth' => [$apiusr, $apipwd]]);

        $downloadfile = json_decode($res->getBody()->getContents())->value;
        $filename = $downloadfile[0]->filename;
        $content = $downloadfile[0]->documentbody;

        $tmpName = tempnam(sys_get_temp_dir(), 'data');
        $file = fopen($tmpName, 'w');

        fwrite($file, base64_decode($content);
        fclose($file);

        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename='.$filename);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($tmpName));

        ob_clean();
        flush();
        readfile($tmpName);

        unlink($tmpName);
    }   

}
