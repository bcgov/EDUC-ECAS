<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App\Dynamics;

use App\Dynamics\Interfaces\iModelRepository;

class AssignmentStatus extends DynamicsMetaRepository implements iModelRepository
{
    public static $table = 'educ_assignment';

    public static $primary_key = 'statuscode';

    public static $data_bind = 'statuscode';

    public static $fields = [
        'id'   => 'Id',
        'name' => 'Label'
    ];
    

}