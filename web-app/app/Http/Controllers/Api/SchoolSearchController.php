<?php

namespace App\Http\Controllers\Api;

use App\Dynamics\Interfaces\iSchool;
use App\Http\Controllers\EcasBaseController;
use App\Http\Resources\SchoolResource;
use Illuminate\Http\Request;

class SchoolSearchController extends EcasBaseController
{

    private $school;

    public function __construct(iSchool $school)
    {
        $this->school = $school;

    }



    public function index(Request $request)
    {

        // TODO - Validate user

        $query = $request->get('q');

        if($query) {
            $search_results = $this->school->filterContains(['name'  => $request->get('q')]);

            return SchoolResource::collection($search_results);
        }

        return null;



    }

}
