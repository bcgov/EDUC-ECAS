<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-16
 * Time: 12:33 PM
 */

namespace App;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DynamicsRepository
{
    public static $table;
    public static $primary_key;
    public static $fields = [];
    public static $cache = 0; // Cache the model for x minutes, or 0 don't cache

    public static $base_url = 'https://ecaswebapi.azurewebsites.net/api/operations';

    public static function get($id = null)
    {
        $cache_key = static::cacheKey($id);

        Log::debug($cache_key);

        if (static::$cache > 0 && Cache::has($cache_key)) {
            $collection = Cache::get($cache_key);
            Log::debug('Loading from Cache');
        }
        else {
            $collection = self::loadCollection($id);
            Log::debug('Loading from Dynamics');

            if (static::$cache > 0) {
                Cache::put($cache_key, $collection, static::$cache);
                Log::debug('Caching collection ' . $cache_key);
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
        $query = static::$base_url . '?statement=' . static::$table;

        $response = self::queryAPI('POST', $query, $data);

        // Returns the id of the created record
        return $response->getBody()->getContents();
    }

    public static function update($id, $data)
    {
        $query = static::$base_url . '?statement=' . static::$table . '(' . $id . ')';

        $response = self::queryAPI('PATCH', $query, $data);

        // Returns an array of the returned data
        return self::mapToLocal(json_decode($response->getBody()->getContents(), true));
    }

    private static function mapToDynamics($data): array
    {
        $mapped_data = [];

        foreach (static::$fields as $our_name => $dynamics_name) {
            if (isset($data[$our_name])) {
                $mapped_data[$dynamics_name] = $data[$our_name];
            }
        }

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

        if (static::$cache > 0) {
            Cache::put($query, $response, static::$cache);
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
        $query = static::$base_url . '?statement=' . static::$table . '&$select=' . implode(',', static::$fields);

        if ($id) {
            $query .= '&$filter=' . static::$primary_key . ' eq \'' . $id . '\'';
        }

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
}