<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App\Dynamics;

use App\Dynamics\Interfaces\iDynamicsRepository;

class School extends DynamicsRepository implements iDynamicsRepository
{
    public static $table = 'educ_schoollists';

    public static $primary_key = 'educ_schoolcode';

    public static $cache = 480; // 8 Hours

    public static $fields = [
        'id'   => 'educ_schoolcode',
        'name' => 'educ_name',
        'city' => 'educ_schoolcity'
    ];
}