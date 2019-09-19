<?php

namespace App\Http\Controllers\Api;



use App\Dynamics\Interfaces\iDistrict;
use App\Http\Controllers\Controller;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\SimpleResource;


class DistrictSearchController extends Controller
{

    private $district;

    public function __construct(iDistrict $district)
    {
        $this->district         = $district;

    }


    public function index(SearchRequest $request)
    {


        $query = $request->get('q');

        if($query) {
            $search_results = $this->district->filterContains(['name'  => $request->get('q')]);

            return SimpleResource::collection($search_results);
        }

        return null;

    }



}
