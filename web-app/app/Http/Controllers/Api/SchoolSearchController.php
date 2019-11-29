<?php

namespace App\Http\Controllers\Api;

use App\Dynamics\Interfaces\iSchool;
use App\Http\Controllers\Controller;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\SchoolResource;



class SchoolSearchController extends Controller
{

    private $school;


    public function __construct(iSchool $school)
    {
        $this->school           = $school;

    }


    public function index(SearchRequest $request)
    {


        $search_results = $this->school->filterContains(['name'  => $request->get('q')]);
        return SchoolResource::collection($search_results);
        

    }

}
