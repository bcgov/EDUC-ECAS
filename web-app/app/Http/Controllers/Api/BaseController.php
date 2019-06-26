<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\Decorators\CacheDecorator;
use App\Dynamics\Interfaces\iDynamicsRepository;
use App\Http\Controllers\Controller;

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



    public function update($id, $data)
    {

        // TODO

    }

    public function create($data)
    {

        // TODO
    }

    public function delete($id)
    {
        // TODO
    }



}