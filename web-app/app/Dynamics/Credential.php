<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App\Dynamics;

use App\Dynamics\Interfaces\iFullCRUD;

class Credential extends DynamicsRepository implements iFullCRUD
{
    public static $table = 'educ_credentialcodes';

    public static $primary_key = 'educ_credentialcodeid';

    public static $data_bind = 'educ_Credential';

    public static $cache = 480; // 8 Hours

    public static $fields = [
        'id'   => 'educ_credentialcodeid',
        'name' => 'educ_name'
    ];
}