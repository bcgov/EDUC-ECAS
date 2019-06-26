<?php


namespace App\Http\Controllers\Interfaces;


interface iApiController
{
    public function index();

    public function show($id);

    public function create($data);

    public function update($id, $data);

    public function delete($id);

}