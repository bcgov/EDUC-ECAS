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

    public static $filter_quote = '\'';


    /*
     * Read data from Dynamics
     * if no $id is passed in the all records from the table are returned
     * Passing in and $id will return on specific record based on the table's primary key
     */
    public function get($id)
    {

        $collection = self::loadCollection($id, '\'');

        return current($collection)[0];

    }

}