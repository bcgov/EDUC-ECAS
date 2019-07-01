<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App\MockEntities\Repository;

use App\Interfaces\iModelRepository;

class Assignment extends DynamicsRepository implements iModelRepository
{
    public function __construct(\App\MockEntities\Assignment $model)
    {
        $this->model = $model;

    }
}