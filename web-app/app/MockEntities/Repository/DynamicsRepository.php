<?php

namespace App\MockEntities\Repository;


use App\MockEntities\Credential;


abstract class DynamicsRepository
{

    protected $model;


    /*
     * Read data from Dynamics, but filter by a specific field
     * This function takes an array as a filter, but only filter based on one field!!
     * You would need to refactor to make it work if more than one filter field is required.
     */

    /*
    * Read data from Dynamics
    * if no $id is passed in the all records from the table are returned
    * Passing in and $id will return on specific record based on the table's primary key
    */
    public function all()
    {

        return $this->model->all();


    }


    public function filter(array $filter)
    {
        // TODO
    }
    /*
     * Read data from Dynamics
     * if no $id is passed in the all records from the table are returned
     * Passing in and $id will return on specific record based on the table's primary key
     */


    public function get($id)
    {


        return $this->model->find($id);

    }
    public function create($data)
    {
        // TODO
    }



    public function update($id, $data)
    {
        // TODO
    }


    public function delete($id)
    {
        // TODO
    }
}