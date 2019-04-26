<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App;

class Payment extends DynamicsRepository
{
    public static $table = 'contact';

    public static $api_verb = 'metadata';

    public static $primary_key = 'educ_methodofpayment';

    public static $fields = [
        'id'   => 'Id',
        'name' => 'Label'
    ];

}