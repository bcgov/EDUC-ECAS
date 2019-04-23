<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App;

class District extends DynamicsRepository
{
    public static $table = 'educ_districtcodes';

    public static $primary_key = 'educ_districtcodeid';

    public static $fields = [
        'id'   => 'educ_districtcodeid',
        'name' => 'educ_name'
    ];
}