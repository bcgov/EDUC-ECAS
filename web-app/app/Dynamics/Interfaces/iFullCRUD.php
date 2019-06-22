<?php


namespace App\Dynamics\Interfaces;


interface iFullCRUD extends iAllOnlyCRUD
{
    public static function get($id);

    public static function filter(array $filter);

    public static function create($data);

    public static function update($id, $data);

    public static function delete($id);

}