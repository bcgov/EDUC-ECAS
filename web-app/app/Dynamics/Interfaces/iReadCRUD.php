<?php


namespace App\Dynamics\Interfaces;


interface iReadCRUD extends iIndexOnlyCRUD
{

    public static function show($id);


}