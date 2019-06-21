<?php


namespace App\Dynamics\Cache;


use App\Dynamics\Interfaces\iFullCRUD;

abstract class CacheBase implements iFullCRUD
{

    /*
 * Generate a key for caching this model based on the class name and the primary key
 * This allows us to cache a specific record based on $id
 * Or cache and entire collection
 */
    public static function index(array $filter)
    {
        // TODO: Implement index() method.
    }

    public static function store($data)
    {
        // TODO: Implement store() method.
    }

    public static function update($id, $data)
    {
        // TODO: Implement update() method.
    }

    public static function delete($id)
    {
        // TODO: Implement delete() method.
    }

    public static function show($id)
    {
        // TODO: Implement show() method.
    }

    private static function cacheKey($id)
    {
        $pieces = explode('\\', get_called_class());

        $name = array_pop($pieces);

        return $id ? $name . '.' . $id : $name;
    }

}