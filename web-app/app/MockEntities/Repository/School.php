<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App\MockEntities\Repository;

use App\Dynamics\Interfaces\iSchoolRepository;

class School extends DynamicsRepository implements iSchoolRepository
{

    public function __construct(\App\MockEntities\School $model)
    {
        $this->model = $model;

    }
}