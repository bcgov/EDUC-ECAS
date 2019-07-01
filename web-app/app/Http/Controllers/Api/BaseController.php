<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\Decorators\CacheDecorator;
use App\Http\Controllers\Controller;
use App\Interfaces\iModelRepository;
use Illuminate\Http\Request;

abstract class BaseController extends Controller
{

    protected $model;

    public function __construct(iModelRepository $model)
    {
        $this->model = $model;
    }


    public function index()
    {

        return (new CacheDecorator($this->model))->all();


    }


    public function show($id)
    {

        return (new CacheDecorator($this->model))->get($id);

    }



    public function update(Request $request)
    {

        // TODO

    }

    public function store(Request $request)
    {

        // TODO
    }

    public function delete(Request $request)
    {
        // TODO
    }



}