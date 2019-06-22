<?php

namespace App\Dynamics;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

/*
 * Each data entity within Dynamics required by the Front End has a corresponding PHP class that extends this class
 * This parent class contains methods and properties to define a model
 * allowing us to build queries we can pass through to the API
 * to Create, Read, Update, and Destroy Dynamics information.
 * To add functionality to the application new Dynamics entities will be created
 * which will require new PHP classes extending this class.
 */
abstract class DynamicsRepository
{
    /*
     * What is the table in Dynamics that defines this model
     */
    public static $table;

    /*
     * What is the field name for the primary key in Dynamics table
     */
    public static $primary_key;

    /*
     * Field names in Dynamics are not user friendly and easily readable
     * We want to use nice easy to read names
     * To do this we map the dynamic fields to local variables
     * $fields maps the variables used locally to Dynamics field names
     * the array key is the local name, the value is the dynamics field
     * eg. $fields = ['sensible_local_field_name' => 'annoyingdynamicsFieldname']
     */
    public static $fields = [];

    /*
     * Models may have relationships to other models
     * for example an Assignment will be linked to a Session via a session_id field
     * $links allows us to define these relationships
     * the array key is the name of the local field, the value is the linked class
     * eg. $links = ['session_id' => Session::class]
     */
    public static $links = [];


    /*
     * There a multiple "types" of Dynamics entities
     * Some list or look-up type items require us to modify our queries to the api
     * It is not always clear which type is the right one, trial and error and discussions with your Dynamics developer are the best route
     * Options: 'operations', 'metadata'
     */
    public static $api_verb = 'operations';

    /*
     * Dynamics requires different case for field names when Reading vs when Writing!! So crazy! Microsoft, right!?
     * To allow this we sometimes must define a special field name to link this data
     */
    public static $data_bind;


    public static function delete($id)
    {
        $query = env('DYNAMICSBASEURL') . '/' . static::$api_verb . '?statement=' . static::$table . '(' . $id . ')';

        self::queryAPI('DELETE', $query);

        // Assuming the delete worked!
        return true;
    }

    /*
     * Read data from Dynamics, but filter by a specific field
     * This function takes an array as a filter, but only filter based on one field!!
     * You would need to refactor to make it work if more than one filter field is required.
     */
    public static function filter(array $filter)
    {
        $query = env('DYNAMICSBASEURL') . '/' . static::$api_verb . '?statement=' . static::$table .
                 '&$select=' . implode(',', static::$fields) .
                 '&$filter=' . static::$fields[key($filter)] . ' eq \'' . current($filter) . '\'';

        $response = self::queryAPI('GET', $query);

        $data = json_decode($response->getBody()->getContents())->value;

        $collection = self::convertDynamicsToLocalVariables($data);

        return $collection;
    }


    /*
    * Read data from Dynamics
    * if no $id is passed in the all records from the table are returned
    * Passing in and $id will return on specific record based on the table's primary key
    */
    public static function all()
    {

        Log::debug('Loading all() from Dynamics: ');
        $collection = self::loadCollection(null);

        return $collection;
    }


    /*
     * Read data from Dynamics
     * if no $id is passed in the all records from the table are returned
     * Passing in and $id will return on specific record based on the table's primary key
     */
    public static function get($id)
    {

        Log::debug('Loading from Dynamics: ' . $id);
        $collection = self::loadCollection($id);


        return current($collection);

    }



    public static function create($data)
    {
        $query = env('DYNAMICSBASEURL') . '/' . static::$api_verb . '?statement=' . static::$table;

        $response = self::queryAPI('POST', $query, $data);

        // Returns the id of the created record
        return $response->getBody()->getContents();
    }


    public static function update($id, $data)
    {
        $query = env('DYNAMICSBASEURL') . '/' . static::$api_verb . '?statement=' . static::$table . '(' . $id . ')';

        $response = self::queryAPI('PATCH', $query, $data);

        // Returns an array of the returned data
        $data = json_decode($response->getBody()->getContents());


        // TODO: This is handy for developing, but shouldn't be needed by Production
        Log::debug('data returned from PATCH call to Dynamics API');
        ob_start();
        var_dump($data);
        $data = ob_get_clean();
        Log::debug($data);

        return self::mapToLocal($data);
    }

    /*
     * Convert the local field names back into Dynamics friendly names
     * We will do this in preparation for Creating or Updating data
     */
    private static function mapToDynamics($data): array
    {
        Log::debug('original data:');
        Log::debug($data);

        $mapped_data = [];

        foreach (static::$fields as $our_name => $dynamics_name) {

            // if this field exists in our data
            if (array_key_exists($our_name, $data)) {

                // Special handling for fields that are linked to other models
                if (isset(static::$links[$our_name])) {

                    // What class is this field linked to?
                    $linked_class = static::$links[$our_name];

                    // We handle depending on what "type" of Dynamic model this is
                    if ($linked_class::$api_verb == 'metadata') {
                        $mapped_data[$linked_class::$data_bind] = $data[$our_name];
                    }
                    else {
                        $mapped_data[$linked_class::$data_bind . '@odata.bind'] = $linked_class::$table . '(' . $data[$our_name] . ')';
                    }
                }
                // Just a regular field, map it to the Dynamics field name
                else {
                    $mapped_data[$dynamics_name] = $data[$our_name];
                }
            }
        }

        Log::debug('data mapped to Dynamics variables:');
        Log::debug($mapped_data);

        return $mapped_data;
    }

    /*
     * Convert a single data record into local variable names
     * Expecting a single row of data
     */
    private static function mapToLocal($data)
    {
        $mapped_data = [];

        foreach (static::$fields as $our_name => $dynamics_name) {
            if (isset($data[$dynamics_name])) {
                $mapped_data[$our_name] = $data[$dynamics_name];
            }
        }

        return $mapped_data;
    }

    /*
     * Convert a collection of records into local variable names
     * Expecting an array filed with data objects
     */
    private static function convertDynamicsToLocalVariables($data): array
    {
        // Dynamics field names are unreadable junk!
        // We want to define our own easy readable variables and disconnect from Dynamics
        // Map Dynamics to local variables here
        // These are defined in each specific Dynamics Model Class

        $collection = [];

        foreach ($data as $index => $row) {
            foreach (static::$fields as $local_field_name => $data_source_field_name) {
                $collection[$index][$local_field_name] = $row->$data_source_field_name;
            }
        }

        return $collection;
    }

    /*
     * Here is the connection to the Dynamics API
     */
    protected static function connection()
    {
        // TODO: Security headers hardcoded! Testing only! Need to move these to an environment file
        return new Client([
            // Base URI is used with relative requests
            'base_uri' => env('DYNAMICSBASEURL'),
            'timeout'  => 30.0,
            'headers'  => [
                'Authorization' => 'Basic ZWNhc2FkbWluOkVjQHMyMDFwIQ=='
            ]
        ]);
    }

    /*
    * Read data from Dynamics
    *
    * @param $id
    * @return array
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    private static function loadCollection($id): array
    {
        if (static::$api_verb == 'operations') {
            $query = env('DYNAMICSBASEURL') . '/' . static::$api_verb . '?statement=' . static::$table . '&$select=' . implode(',', static::$fields);
        }
        elseif (static::$api_verb == 'metadata') {
            $query = env('DYNAMICSBASEURL') . '/' . static::$api_verb . '?entityName=' . static::$table . '&optionSetName=' . static::$primary_key;
        }

        if ($id) {
            $query .= '&$filter=' . static::$primary_key . ' eq \'' . $id . '\'';
        }

        $response = self::queryAPI('GET', $query);

        if (static::$api_verb == 'operations') {
            $data = json_decode($response->getBody()->getContents())->value;
        }
        elseif (static::$api_verb == 'metadata') {
            $data = json_decode($response->getBody()->getContents())->Options;
        }

        $collection = self::convertDynamicsToLocalVariables($data);

        return $collection;
    }

    /*
     * Perform a call to the API
     *
     * @param $query
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private static function queryAPI($method, $query, $data = null)
    {
        Log::debug(strtoupper($method) . ': ' . $query);

        // TODO: Should have failure handling, we assume this all works fine
        if ($method == 'GET' || $method == 'DELETE') {
            $response = self::connection()->request($method, $query);
        }
        else {
            $response = self::connection()->request($method, $query, [
                'json' => self::mapToDynamics($data)
            ]);
        }

        return $response;
    }
}