<?php

namespace App\Http\Controllers\Api;

use App\Dynamics\Interfaces\iSchool;
use App\Http\Controllers\Controller;
use App\Keycloak\KeycloakGuard;
use App\Http\Resources\SchoolResource;
use Illuminate\Http\Request;

class SchoolSearchController extends Controller
{

    private $school;


    public function __construct(iSchool $school)
    {
        $this->school           = $school;

    }


    public function index(Request $request)
    {

        // There's no need to validate user requests -- public data

        $query = $request->get('q');

        if($query) {
            $search_results = $this->school->filterContains(['name'  => $request->get('q')]);

            return SchoolResource::collection($search_results);
        }

        return null;



    }

}
