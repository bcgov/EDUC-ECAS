<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\Assignment;
use App\Dynamics\Decorators\CacheDecorator;
use App\Http\Resources\AssignmentResource;
use App\Http\Resources\SchoolResource;
use App\Interfaces\iModelRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class SchoolSearchController extends ApiBaseController
{



    public function index(Request $request)
    {

        // TODO - Validate user

        $query = $request->get('q');

        if($query) {
            $search_results = $this->model->filterContains(['name'  => $request->get('q')]);

            return SchoolResource::collection($search_results);
        }

        return null;



    }



}
