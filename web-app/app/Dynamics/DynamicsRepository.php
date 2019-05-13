<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-16
 * Time: 12:33 PM
 */

namespace App\Dynamics;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DynamicsRepository
{
    public static $table;

    public static $primary_key;

    // Fields maps the variables used locally to Dynamics field names
    // the array key is the local name, the value is the dynamics field
    // $fields = ['local_field_name' => 'dynamicsfieldname']
    public static $fields = [];

    // Links are to other items
    public static $links = [];

    // Cache the model for x minutes, or 0 don't cache
    public static $cache = 0;

    // operations, metadata
    public static $api_verb = 'operations';

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

    public static function delete($id)
    {
        $query = env('DYNAMICSBASEURL') . '/' . static::$api_verb . '?statement=' . static::$table . '(' . $id . ')';

        $response = self::queryAPI('DELETE', $query);

        // Returns an array of the returned data
        return self::mapToLocal(json_decode($response->getBody()->getContents(), true));
    }

    public static function get($id = null)
    {
        $cache_key = static::cacheKey($id);

        // Are we caching this model and does a cached copy exist
        if (static::$cache > 0 && Cache::has($cache_key)) {
            Log::debug('Loading from Cache: ' . $cache_key);
            $collection = Cache::get($cache_key);
        }
        // Need to get from Dynamics
        else {
            Log::debug('Loading from Dynamics: ' . $cache_key);
            $collection = self::loadCollection($id);

            // Cache for future reference
            if (static::$cache > 0) {
                Log::debug('Caching collection ' . $cache_key);
                Cache::put($cache_key, $collection, static::$cache);
            }
        }

        // Just looking for a single record?
        if ($id) {
            return current($collection);
        }

        return $collection;
    }

    public static function cacheKey($id)
    {
        $pieces = explode('\\', get_called_class());

        $name = array_pop($pieces);

        return $id ? $name . '.' . $id : $name;
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

        Log::debug('data returned from PATCH call to Dynamics API');
        ob_start();
        var_dump($data);
        $data = ob_get_clean();
        Log::debug($data);

        return self::mapToLocal($data);
    }

    private static function mapToDynamics($data): array
    {
        $mapped_data = [];
        Log::debug('original data:');
        Log::debug($data);
        foreach (static::$fields as $our_name => $dynamics_name) {
            if (array_key_exists($our_name, $data)) {
                if (isset(static::$links[$our_name])) {
                    $linked_class = static::$links[$our_name];
                    if ($linked_class::$api_verb == 'metadata') {
                        $mapped_data[$linked_class::$data_bind] = $data[$our_name];
                    }
                    else {
                        $mapped_data[$linked_class::$data_bind . '@odata.bind'] = $linked_class::$table . '(' . $data[$our_name] . ')';
                    }
                }
                else {
                    $mapped_data[$dynamics_name] = $data[$our_name];
                }
            }
        }

        Log::debug('data mapped to Dynamics variables:');
        Log::debug($mapped_data);

        return $mapped_data;
    }

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

    protected static function connection()
    {

        return new Client([
            // Base URI is used with relative requests
            'base_uri' => env('DYNAMICSBASEURL'),
            'timeout'  => 10.0,
            'headers'  => [
                'UserName' => 'ecasadmin',
                'Password' => 'Ec@s201p!'
            ]
        ]);
    }

    /**
     * @param $query
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private static function queryAPI($method, $query, $data = null)
    {
        Log::debug(strtoupper($method));
        Log::debug($query);
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

    /**
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

    /**
     * @param $data
     * @return array
     */
    private static function convertDynamicsToLocalVariables($data): array
    {
        $collection = [];

        foreach ($data as $index => $row) {
            foreach (static::$fields as $local_field_name => $data_source_field_name) {
                $collection[$index][$local_field_name] = $row->$data_source_field_name;
            }
        }

        return $collection;
    }
}