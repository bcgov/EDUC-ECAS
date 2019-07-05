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
        $this->model            = $model;
    }


    abstract public function index();

    abstract public function show($id);

    abstract public function update($id, Request $request);

    abstract public function store(Request $request);

    abstract public function destroy($id);




}