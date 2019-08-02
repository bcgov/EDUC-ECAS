<?php

namespace App\Http\Controllers\Api;



use App\Http\Resources\SimpleResource;
use Illuminate\Http\Request;


class DistrictSearchController extends BaseController
{



    public function index(Request $request)
    {

        // TODO - Validate user

        $query = $request->get('q');

        if($query) {
            $search_results = $this->model->filterContains(['name'  => $request->get('q')]);

            return SimpleResource::collection($search_results);
        }

        return null;



    }



}
