<?php


namespace App\Interfaces;


interface iModelRepository
{

    public function all();

    public function get($id);

    public function filter(array $filter);

    public function create($data);

    public function update($id, $data);

    public function delete($id);

}