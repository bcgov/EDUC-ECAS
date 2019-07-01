<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App\Dynamics;

use App\Interfaces\iModelRepository;

class Session extends DynamicsRepository implements iModelRepository
{
    public static $table = 'educ_sessions';

    public static $primary_key = 'educ_sessionid';

    public static $data_bind = 'educ_Session';

    public static $cache = 480; // 8 Hours

    public static $fields = [
        'id'          => 'educ_sessionid',
        'activity_id' => '_educ_sessionactivity_value',
        'type_id'     => '_educ_sessiontype_value',
        'start_date'  => 'educ_startdate',
        'end_date'    => 'educ_enddate',
        'location'    => 'educ_locationname',
        'address'     => 'educ_locationaddress',
        'city'        => 'educ_locationcity',
    ];
}