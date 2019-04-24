<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App;

class Region extends DynamicsRepository
{
    public static $table = 'educ_provincestatecodes';

    public static $primary_key = 'educ_provincestatecode';

    public static $cache = 480; // 8 Hours

    public static $fields = [
        'id'   => 'educ_provincestatecode',
        'name' => 'educ_name'
    ];
}