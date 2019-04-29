<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App;

class Role extends DynamicsRepository
{
    public static $table = 'educ_rolecodes';

    public static $primary_key = 'educ_name';

    public static $data_bind = 'educ_Role';

    public static $cache = 480; // 8 Hours

    public static $fields = [
        'id'   => 'educ_rolecodeid',
        'name' => 'educ_name',
        'rate' => 'educ_rolerate'
    ];
}