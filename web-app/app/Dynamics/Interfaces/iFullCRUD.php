<?php


namespace App\Dynamics\Interfaces;


interface iFullCRUD extends iReadCRUD
{

    public static function store($data);

    public static function update($id, $data);

    public static function delete($id);

}