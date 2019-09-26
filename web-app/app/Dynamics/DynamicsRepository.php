<?php

namespace App\Dynamics;

use GuzzleHttp\ClientInterface;
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

    protected $guzzle_client;

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


    /*
     * Go figure, Dynamics requires quotes around the filter requests for a Profile, but not around the filter requests
     * for Assignments and Credentials
     */
    public static $filter_quote = '';



    public function __construct(ClientInterface $guzzle_client)
    {
        $this->guzzle_client = $guzzle_client;
    }


    public function delete($id)
    {
        $query = env('DYNAMICSBASEURL') . '/' . static::$api_verb . '?statement=' . static::$table . '(' . $id . ')';

        $response = $this->guzzle_client->request('DELETE', $query);


        // Assuming the delete worked!
        return $response;
    }

    /*
     * Read data from Dynamics, but filter by a specific field
     * This function takes an array as a filter, but only filter based on one field!!
     * You would need to refactor to make it work if more than one filter field is required.
     */
    public function filter(array $filter)
    {
        $query = env('DYNAMICSBASEURL') . '/' . static::$api_verb . '?statement=' . static::$table .
                 '&$select=' . implode(',', static::$fields) .
                 '&$filter=' . static::$fields[key($filter)] . ' eq ' .
                static::$filter_quote . current($filter) . static::$filter_quote;

        Log::debug('Filter query: ' . $query);

        return $this->retrieveData($query);

    }


    /*
     * Read data from Dynamics, but filter by a specific field
     * This function takes an array as a filter, but only filter based on one field!!
     * You would need to refactor to make it work if more than one filter field is required.
     */
    public function filterContains(array $filter)
    {
        $query = env('DYNAMICSBASEURL') . '/' . static::$api_verb . '?statement=' . static::$table .
            '&$select=' . implode(',', static::$fields) .
            '&$filter=contains(' . static::$fields[key($filter)] . ',' .
            static::$filter_quote . current($filter) . static::$filter_quote . ')';

        return  $this->retrieveData($query);

    }


    /*
    * Read data from Dynamics
    * if no $id is passed in the all records from the table are returned
    * Passing in and $id will return on specific record based on the table's primary key
    */
    public function all()
    {

        $query = env('DYNAMICSBASEURL') . '/' . static::$api_verb . '?statement=' . static::$table . '&$select=' . implode(',', static::$fields);

        Log::debug('Loading all() from Dynamics ' .static::$table. ' : ' .$query);

        return $this->retrieveData($query);

    }


    /*
     * Read data from Dynamics
     */
    public function get($id)
    {

        $query = env('DYNAMICSBASEURL') . '/' . static::$api_verb . '?statement=' . static::$table . '&$select=' . implode(',', static::$fields);

        $query .= '&$filter=' . static::$primary_key . " eq " . static::$filter_quote . $id . static::$filter_quote;

        $collection = $this->retrieveData($query);

        return current($collection)[0];

    }


    public function firstOrCreate($id, $data)
    {
        return 'method not implemented';
        // overridden by Profile - none others

    }



    public function create($data)
    {

        $query = env('DYNAMICSBASEURL') . '/' . static::$api_verb . '?statement=' . static::$table;

        $response = $this->guzzle_client->request('POST', $query, [
            'json' => self::mapToDynamics($data)
        ]);

        // Returns the id of the created record
        return $response->getBody()->getContents();
    }


    public function update($id, $data)
    {
        $query = env('DYNAMICSBASEURL') . '/' . static::$api_verb . '?statement=' . static::$table . '(' . $id . ')';

        Log::debug('Update ' . static::$table . ' query: ' . $query);

        $response = $this->guzzle_client->request('PATCH', $query, [
            'json' => self::mapToDynamics($data)
        ]);

        // Returns an array of the returned data
        $data_returned = json_decode($response->getBody()->getContents());

        // Unbelievably, the field names returned from an update request differ
        // from the field names returned from a get request.  For consistency,
        // we request a fresh copy of the record from the API.

        return $this->get($id);

    }

    protected function retrieveData(String $query){

        $response = $this->guzzle_client->request('GET', $query);

        $data = json_decode($response->getBody()->getContents())->value;

        return self::convertDynamicsToLocalVariables($data);

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
     * Convert a collection of records into local variable names
     * Expecting an array filed with data objects
     */
    protected static function convertDynamicsToLocalVariables($data)
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

        return collect($collection);
    }




}