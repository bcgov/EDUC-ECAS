<?php

namespace App\Http\Controllers\Api;



use App\Dynamics\Interfaces\iDistrict;
use App\Http\Controllers\Controller;
use App\Http\Resources\SimpleResource;
use Illuminate\Http\Request;


class DistrictSearchController extends Controller
{

    private $district;

    public function __construct(iDistrict $district)
    {
        $this->district = $district;

    }


    public function index(Request $request)
    {

        // There's no need to validate user requests -- public data

        $query = $request->get('q');

        if($query) {
            $search_results = $this->district->filterContains(['name'  => $request->get('q')]);

            return SimpleResource::collection($search_results);
        }

        return null;

    }



}
