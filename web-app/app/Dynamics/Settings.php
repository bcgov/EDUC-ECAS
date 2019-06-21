<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App\Dynamics;

class Settings extends DynamicsRepository
{
    public function show() {


        $response = self::connection()->request('GET', env('DYNAMICSBASEURL') . '/EnvironmentInformation');

        return $response->getBody()->getContents();




    }
}