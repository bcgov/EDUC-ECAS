<?php


namespace App\Http\Controllers\Interfaces;


use Illuminate\Http\Request;

interface iApiController
{
    public function index();

    public function show($id);

    public function store(Request $request);

    public function update(Request $request);

    public function delete(Request $request);

}