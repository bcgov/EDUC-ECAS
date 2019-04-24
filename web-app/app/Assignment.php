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

    public static $primary_key = 'educ_assignmentid';

    public static $fields = [
        'id'      => 'educ_assignmentid',
        'session' => '_educ_session_value',
        'user'    => '_educ_contact_value',
        'role'    => '_educ_role_value'
    ];
}