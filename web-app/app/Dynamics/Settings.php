<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App\Dynamics;

use App\Dynamics\Interfaces\iIndexOnlyCRUD;
use App\Dynamics\Interfaces\iReadCRUD;

class Settings extends DynamicsRepository implements iIndexOnlyCRUD
{
    public static function index(array $filter = null) {


        $response = self::connection()->request('GET', env('DYNAMICSBASEURL') . '/EnvironmentInformation');

        return $response->getBody()->getContents();




    }
}