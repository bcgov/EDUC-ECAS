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
abstract class DynamicsMetaRepository extends DynamicsRepository
{

    /*
     * There a multiple "types" of Dynamics entities
     * Some list or look-up type items require us to modify our queries to the api
     * It is not always clear which type is the right one, trial and error and discussions
     * with your Dynamics developer are the best route
     * Options: 'operations', 'metadata'
     */
    public static $api_verb = 'metadata';


    /*
    * Read data from Dynamics
    * if no $id is passed in the all records from the table are returned
    * Passing in and $id will return on specific record based on the table's primary key
    */
    public function all()
    {
        $query = env('DYNAMICSBASEURL') . '/' . static::$api_verb . '?entityName=' . static::$table . '&optionSetName=' . static::$primary_key;
 
		 $response = $this->guzzle_client->request('GET', $query);

        $data = json_decode($response->getBody()->getContents())->Options;

        $collection = parent::convertDynamicsToLocalVariables($data);

        return $collection;
    }



}
