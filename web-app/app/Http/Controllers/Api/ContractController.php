<?php

namespace App\Http\Controllers\Api;



use App\Dynamics\Interfaces\iContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\SimpleResource;


class ContractSearchController extends Controller
{

    private $contract;

    public function __construct(iContract $contract)
    {
        $this->contract         = $contract;

    }


    public function index(SearchRequest $request)
    {


        $search_results = $this->district->filterContains(['name'  => $request->get('q')]);

        return SimpleResource::collection($search_results);


    }  

}
