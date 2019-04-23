<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App;

class Assignment extends DynamicsRepository
{
    public static $table = 'educ_assignments';

    public static $primary_key = 'educ_name';

    public static $fields = [
        'id'      => 'educ_name',
        'session' => 'educ_session',
        'user'    => 'educ_contact'
    ];
}