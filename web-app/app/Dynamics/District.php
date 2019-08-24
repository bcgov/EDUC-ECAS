<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App\Dynamics;

use App\Interfaces\iModelRepository;

class District extends DynamicsRepository implements iModelRepository
{
    public static $table = 'educ_districtcodes';

    public static $primary_key = 'educ_districtcodeid';

    public static $cache = 480; // 8 Hours

    public static $data_bind = 'educ_District';

    public static $filter_quote = '\'';

    public static $fields = [
        'id'   => 'educ_districtcodeid',
        'name' => 'educ_districtnamenumber'
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