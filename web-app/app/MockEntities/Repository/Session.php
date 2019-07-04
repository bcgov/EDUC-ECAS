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
        $collection = $this->model->all();
        return $collection->sortBy('start_date')->values();
    }
}