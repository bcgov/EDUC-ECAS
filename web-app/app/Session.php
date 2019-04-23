<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App;

class Session extends DynamicsRepository
{
    public static $table = 'educ_sessions';

    public static $primary_key = 'educ_sessionid';

    public static $fields = [
//        'id'         => 'educ_sessionid',
//        'activity'   => 'educ_sessionActivity',
//        'type'       => 'educ_sessionType',
//        'start_date' => 'educ_startdate',
//        'end_date'   => 'educ_enddate',
        'location'   => 'educ_locationName',
//        'address'    => 'educ_locationAddress',
//        'city'       => 'educ_locationCity',
    ];
}