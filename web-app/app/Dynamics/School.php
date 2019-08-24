<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App\Dynamics;

use App\Interfaces\iModelRepository;


class School extends DynamicsRepository implements iModelRepository
{
    public static $table = 'educ_schoollists';

    public static $primary_key = 'educ_schoolcode';

    public static $cache = 480; // 8 Hours


    public static $filter_quote = '\'';


    public static $fields = [
        'id'   => 'educ_schoolcode',
        'name' => 'educ_name',
        'city' => 'educ_schoolcity'
    ];

    public function all()
    {
        $collection = parent::all();
        return $collection->sortBy('name')->values();


    }




}