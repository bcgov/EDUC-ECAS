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

        // Since this repository attempts to mimic the Dynamics API
        // we convert each object in the collection to an array so
        // they appear identical

        $collection = $this->model->all();

        $collection_of_arrays = $collection->map( function ($item) {

            return $item->toArray();
        });

        return $collection_of_arrays;

    }


    public function filter(array $filter)
    {
        $collection = $this->model->where($filter[0], $filter[1])->get();
        $collection_of_arrays = $collection->map( function ($item) {
            return $item->toArray();
        });

        return $collection_of_arrays;
    }
    /*
     * Read data from Dynamics
     * if no $id is passed in the all records from the table are returned
     * Passing in and $id will return on specific record based on the table's primary key
     */


    public function get($id)
    {

        // Since this repository attempts to mimic the Dynamics API
        // we convert the Laravel object retrieved to an array so
        // it appears identical to the Dynamics object

        return $this->model->find($id)->toArray();

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