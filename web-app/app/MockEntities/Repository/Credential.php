<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App\MockEntities\Repository;

use App\Interfaces\iModelRepository;


class Credential extends DynamicsRepository implements iModelRepository
{

    public function __construct(\App\MockEntities\Credential $model)
    {
        $this->model = $model;

    }

}