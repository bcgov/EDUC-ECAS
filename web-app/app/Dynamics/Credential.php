<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App\Dynamics;

use App\Dynamics\Interfaces\iModelRepository;

class Credential extends DynamicsRepository implements iModelRepository
{
    public static $table = 'educ_credentialcodes';

    public static $primary_key = 'educ_credentialcodeid';

    public static $data_bind = 'educ_Credential';

    public static $fields = [
        'id'   => 'educ_credentialcodeid',
        'name' => 'educ_name'
    ];

    public function all()
    {
        $collection = parent::all();
        return $collection->sortBy('name')->values();


    }
}