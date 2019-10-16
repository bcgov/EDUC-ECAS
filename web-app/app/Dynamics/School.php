<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App\Dynamics;

use App\Dynamics\Interfaces\iModelRepository;


class School extends DynamicsRepository implements iModelRepository
{
    public static $table = 'educ_schoollists';

    public static $primary_key = 'educ_schoollistid';

    public static $data_bind = 'educ_CurrentSchoold';  // note '...Schoold' is not a typo


    public static $filter_quote = '\'';


    public static $fields = [
        'id'   => 'educ_schoollistid',
        'name' => 'educ_name',
        'city' => 'educ_schoolcity'
    ];

    public function all()
    {
        $collection = parent::all();
        return $collection->sortBy('name')->values();


    }


    /*
         * Read data from Dynamics
         */
    public function get($id)
    {

        $query = env('DYNAMICSBASEURL') . '/' . static::$api_verb . '?statement=' . static::$table . '&$select=' . implode(',', static::$fields);

        $query .= '&$filter=' . static::$primary_key . " eq " .  $id;

        $collection = $this->retrieveData($query);

        return current($collection)[0];

    }

}