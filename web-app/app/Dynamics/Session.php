<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App\Dynamics;

use App\Dynamics\Interfaces\iModelRepository;
use Illuminate\Support\Facades\Log;

class Session extends DynamicsRepository implements iModelRepository
{
    public static $status          = [
        'Draft'         =>     1,
        'Cancelled'     =>     610410005,
        'Cancelled & Active'     =>     610410006,
        'Completed'     =>     2,
        'Open'          =>     610410000
        ];



    public static $table = 'educ_sessions';

    public static $primary_key = 'educ_sessionid';

    public static $data_bind = 'educ_Session';

    // The number of months of session history that should be shown to the user.
    // The session history is further restricted in Vue
    public static $max_history_months = 16;


    public static $fields = [
        'id'            => 'educ_sessionid',
        'activity_id'   => '_educ_sessionactivity_value',
        'type_id'       => '_educ_sessiontype_value',
        'start_date'    => 'educ_startdate',
        'end_date'      => 'educ_enddate',
        'location'      => 'educ_locationname',
        'address'       => 'educ_locationaddress',
        'city'          => 'educ_locationcity',
        'state'         => 'statecode',
        'status'        => 'statuscode',
        'is_published'  => 'educ_publishsession'
    ];



    public function all()
    {


        // The number of sessions listed in Dynamics is expected to grow over time.  Since the Dynamics API
        // will only return a maximum of 50 records we restricted the number of sessions by session start_date

        $oldest_date_shown = (date_modify(new \DateTime(), '-'. self::$max_history_months . ' months'))->format('Y-m-d');


        // This query string has been modified so it filters session based on the business rules
        $query = env('DYNAMICSBASEURL') . '/' . static::$api_verb . '?statement=' . static::$table .
            '&$select=' . implode(',', static::$fields) .
            '&$filter=' . 'educ_startdate' . ' gt ' . $oldest_date_shown . ' and ' .
            ' statuscode ne ' . self::$status['Draft']  . ' and ' .
            ' statuscode ne ' . self::$status['Cancelled']  .
            '&$orderby=educ_startdate desc';

        Log::debug('Session filter query: ' . $query);

        return $this->retrieveData($query);



    }
}