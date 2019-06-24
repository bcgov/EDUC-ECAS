<?php


namespace App\Dynamics\Interfaces;


interface iFullCRUD extends iAllOnlyCRUD
{
    public function get($id);

    public function filter(array $filter);

    public function create($data);

    public function update($id, $data);

    public function delete($id);

}