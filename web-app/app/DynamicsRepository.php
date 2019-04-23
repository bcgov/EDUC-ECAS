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

class DynamicsRepository
{
    public static $table;
    public static $primary_key;
    public static $fields = [];

    public static $base_url = 'https://ecaswebapi.azurewebsites.net/api/operations';

    public static function get($id = null)
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

        // Just looking for a single record?
        if ($id) {
            return current($collection);
        }

        return $collection;
    }

    public static function create($data)
    {
        $query = static::$base_url . '?statement=' . static::$table;

        $response = self::connection()->request('POST', $query, [
            'json' => self::mapToDynamics($data)
        ]);

        // Returns the id of the created record
        return $response->getBody()->getContents();
    }

    public static function update($id, $data)
    {
        $query = static::$base_url . '?statement=' . static::$table.'('.$id.')';

        $response = self::connection()->request('PATCH', $query, [
            'json' => self::mapToDynamics($data)
        ]);

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
        try {
            $response = self::connection()->request($method, $query);
        }
        catch (ClientException $exception) {
            report($exception);
        }
////dd($response);
        return $response;
    }
}