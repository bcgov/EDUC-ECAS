<?php

namespace App\MockEntities\Repository;




abstract class MockRepository
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
        $collection = $this->model->where(key($filter), current($filter))->get();
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

        $model = $this->model->find($id);

        if( ! $model ) {
            return null;
        }

        return $model->toArray();


    }



    public function firstOrCreate($federated_id, $data)
    {
        $existing = $this->model->filter([ 'federated_id' => $federated_id ]);

        if (count($existing) == 0) {

            return [
                'federated_id'                   => $federated_id,
                'id'                             => null,
                'preferred_first_name'           => null,
                'first_name'                     => $data['first_name'],
                'last_name'                      => $data['last_name'],
                'email'                          => $data['email'],
                'phone'                          => null,
                'social_insurance_number'        => null,
                'address_1'                      => null,
                'address_2'                      => null,
                'city'                           => null,
                'region'                         => null,
                'postal_code'                    => null,
                'district_id'                    => null,
                'school_id'                      => null,
                'professional_certificate_bc'    => null,
                'professional_certificate_yk'    => null,
                'professional_certificate_other' => null,
            ];

        }

        return $existing;

    }




    public function create($data)
    {

        // For consistency with Dynamics, return only the new key id
        // not the entire model
        $new_model = $this->model->create($data);
        return $new_model->id;

    }



    public function update($id, $data)
    {

        $record = $this->model->find($id);
        $record->fill($data);

        return $record->toArray();

    }


    public function delete($id)
    {
        $record = $this->model->find($id);
        return $record->delete();
    }
}