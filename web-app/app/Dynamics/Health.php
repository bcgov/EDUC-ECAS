<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App\Dynamics;


use App\Dynamics\Interfaces\iAllOnlyCRUD;


class Health extends DynamicsRepository implements iAllOnlyCRUD
{
    public static function all() {


        $response = parent::connection()->request('GET', env('DYNAMICSBASEURL') . '/health');

        return $response->getStatusCode();


    }
}