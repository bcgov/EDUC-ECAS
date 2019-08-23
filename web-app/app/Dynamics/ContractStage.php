<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App\Dynamics;

use App\Interfaces\iModelRepository;

class ContractStage extends DynamicsMetaRepository implements iModelRepository
{
    public static $table = 'educ_assignment';

    public static $primary_key = 'educ_contractstage';

    public static $fields = [
        'id'   => 'Id',
        'name' => 'Label'
    ];

}