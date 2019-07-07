<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\iModelRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

abstract class BaseController extends Controller
{

    protected $model;
    protected $user;


    public function __construct(iModelRepository $model)
    {
        $this->model            = $model;
        $this->user             = Auth::user();
    }


    abstract public function index();

    abstract public function show($id);

    abstract public function update($id, Request $request);

    abstract public function store(Request $request);

    abstract public function destroy($id);




}