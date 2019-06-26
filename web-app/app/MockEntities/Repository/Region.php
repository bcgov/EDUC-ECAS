<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App\MockEntities\Repository;

use App\Dynamics\Interfaces\iDynamicsRepository;

class Region extends DynamicsRepository implements iDynamicsRepository
{
    public function __construct(\App\MockEntities\Region $model)
    {
        $this->model = $model;

    }
}