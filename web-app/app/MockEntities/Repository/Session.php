<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App\MockEntities\Repository;

use App\Interfaces\iModelRepository;

class Session extends MockRepository implements iModelRepository
{
    public function __construct(\App\MockEntities\Session $model)
    {
        $this->model = $model;

    }

    public function all()
    {
        // override parent method to sort collection by name
        $collection = $this->model->all();
        $collection->sortBy('start_date')->values();

        $collection_of_arrays = $collection->map( function ($item) {

            return $item->toArray();
        });

        return $collection_of_arrays;

    }
}