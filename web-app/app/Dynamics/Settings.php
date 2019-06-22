<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App\Dynamics;

use App\Dynamics\Interfaces\iAllOnlyCrud;

class Settings extends DynamicsRepository implements iAllOnlyCrud
{
    public static function all() {


        $response = parent::connection()->request('GET', env('DYNAMICSBASEURL') . '/EnvironmentInformation');

        return $response->getBody()->getContents();

    }
}