<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App\MockEntities\Repository;

use App\Dynamics\Interfaces\iDynamicsRepository;

class Profile extends DynamicsRepository implements iDynamicsRepository
{
    public function __construct(\App\MockEntities\Profile $model)
    {
        $this->model = $model;

    }

    public function all()
    {

        // override parent - disable the ability to return all users profiles
        return null;

    }
}