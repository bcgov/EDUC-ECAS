<?php


namespace App\Http\Controllers\Interfaces;


interface iReadWriteController extends iReadController
{

    public function store($data);

    public function update($id, $data);

    public function delete($id);

}