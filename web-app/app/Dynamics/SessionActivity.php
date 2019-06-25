<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App\Dynamics;

use App\Dynamics\Interfaces\iDynamicsRepository;

class SessionActivity extends DynamicsRepository implements iDynamicsRepository
{
    public static $table = 'educ_sessionactivitycodes';

    public static $primary_key = 'educ_sessionactivitycodeid';

    public static $cache = 480; // 8 Hours

    public static $fields = [
        'id'   => 'educ_sessionactivitycodeid',
        'code' => 'educ_sessionactivitycode',
        'name' => 'educ_name'
    ];
}