<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\Decorators\CacheDecorator;
use App\Dynamics\Interfaces\iDynamicsRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

abstract class BaseController extends Controller
{

    protected $model;


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