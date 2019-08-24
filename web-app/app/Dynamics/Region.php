<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App\Dynamics;

use App\Dynamics\Interfaces\iModelRepository;

class Region extends DynamicsRepository implements iModelRepository
{
    public static $table = 'educ_provincestatecodes';

    public static $primary_key = 'educ_provincestatecode';

    public static $cache = 480; // 8 Hours

    public static $filter_quote = '\'';

    public static $fields = [
        'id'   => 'educ_provincestatecode',
        'name' => 'educ_name'
    ];
}