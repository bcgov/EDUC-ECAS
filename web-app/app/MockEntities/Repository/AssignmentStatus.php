<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App\MockEntities\Repository;

use App\Dynamics\Interfaces\iDynamicsRepository;

class AssignmentStatus extends DynamicsRepository implements iDynamicsRepository
{

    public function __construct(\App\MockEntities\AssignmentStatus $model)
    {
        $this->model = $model;

    }

}