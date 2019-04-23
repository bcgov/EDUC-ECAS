<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App;

class SessionType extends DynamicsRepository
{
    public static $table = 'educ_sessiontypecodes';

    public static $primary_key = 'educ_name';

    public static $fields = [
        'id'   => 'educ_sessiontypecode',
        'name' => 'educ_name'
    ];
}