<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App\Dynamics;

use App\Dynamics\Interfaces\iDynamicsRepository;

class SessionType extends DynamicsRepository implements iDynamicsRepository
{
    public static $table = 'educ_sessiontypecodes';

    public static $primary_key = 'educ_sessiontypecodeid';

    public static $cache = 480; // 8 Hours

    public static $fields = [
        'id'   => 'educ_sessiontypecodeid',
        'code' => 'educ_sessiontypecode',
        'name' => 'educ_name'
    ];
}