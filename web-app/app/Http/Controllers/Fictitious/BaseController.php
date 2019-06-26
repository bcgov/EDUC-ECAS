<?php

namespace App\Http\Controllers\Fictitious;


use App\Http\Controllers\Controller;

abstract class BaseController extends Controller
{

    protected $model;


    public function index()
    {

        return $this->model->all();


    }


    public function show($id)
    {

        return $this->model->get($id);

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