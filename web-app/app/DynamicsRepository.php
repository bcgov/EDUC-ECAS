<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-16
 * Time: 12:33 PM
 */

namespace App;

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

    public static $base_url = 'https://ecaswebapi.azurewebsites.net/api';

    // operations, metadata
    public static $api_verb = 'operations';

    public static function filter(array $filter)
    {
        $query = static::$base_url . '/' . static::$api_verb . '?statement=' . static::$table .
                 '&$select=' . implode(',', static::$fields) .
                 '&$filter=' . static::$fields[key($filter)] . ' eq \'' . current($filter) . '\'';

        $response = self::queryAPI('GET', $query);

        $data = json_decode($response->getBody()->getContents())->value;

        $collection = [];

        foreach ($data as $index => $row) {
            foreach (static::$fields as $local_field_name => $data_source_field_name) {
                $collection[$index][$local_field_name] = $row->$data_source_field_name;
            }
        }

        return $collection;
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
        $query = static::$base_url . '/' . static::$api_verb . '?statement=' . static::$table;

        $response = self::queryAPI('POST', $query, $data);

        // Returns the id of the created record
        return $response->getBody()->getContents();
    }

    public static function update($id, $data)
    {
        $query = static::$base_url . '/' . static::$api_verb . '?statement=' . static::$table . '(' . $id . ')';

        $response = self::queryAPI('PATCH', $query, $data);

        // Returns an array of the returned data
        return self::mapToLocal(json_decode($response->getBody()->getContents(), true));
    }

    private static function mapToDynamics($data): array
    {
        $mapped_data = [];

        foreach (static::$fields as $our_name => $dynamics_name) {
            if (isset($data[$our_name])) {
                if (isset(static::$links[$our_name])) {
                    $linked_class = static::$links[$our_name];
                    $mapped_data[$linked_class::$data_bind . '@odata.bind'] = $linked_class::$table . '(' . $data[$our_name] . ')';
                }
                else {
                    $mapped_data[$dynamics_name] = $data[$our_name];
                }
            }
        }
        Log::debug($mapped_data);
//        foreach (static::links as $our_name => $linked_class) {
//            if (isset($mapped_data[$our_name])) {
//                $mapped_data[$dynamics_name] = $data[$our_name];
//            }
//        }

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
            'base_uri' => self::$base_url,
            // You can set any number of default request options.
            'timeout'  => 5.0,
        ]);
    }

    /**
     * @param $query
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private static function queryAPI($method, $query, $data = null)
    {
        if ($method == 'GET') {
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
            $query = static::$base_url . '/' . static::$api_verb . '?statement=' . static::$table . '&$select=' . implode(',', static::$fields);
        }
        elseif(static::$api_verb == 'metadata') {
            $query = static::$base_url . '/' . static::$api_verb . '?entityName=' . static::$table . '&optionSetName=' . static::$primary_key;
        }

        if ($id) {
            $query .= '&$filter=' . static::$primary_key . ' eq \'' . $id . '\'';
        }

        Log::debug($query);

        $response = self::queryAPI('GET', $query);

        if (static::$api_verb == 'operations') {
            $data = json_decode($response->getBody()->getContents())->value;
        }
        elseif(static::$api_verb == 'metadata') {
            $data = json_decode($response->getBody()->getContents())->Options;
        }

        $collection = [];

        foreach ($data as $index => $row) {
            foreach (static::$fields as $local_field_name => $data_source_field_name) {
                $collection[$index][$local_field_name] = $row->$data_source_field_name;
            }
        }

        return $collection;
    }
}