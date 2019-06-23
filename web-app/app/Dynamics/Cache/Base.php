<?php


namespace App\Dynamics\Cache;


use App\Dynamics\Interfaces\iFullCRUD;
use Illuminate\Support\Facades\Cache;

abstract class Base implements iFullCRUD
{

    protected static $duration;


    public static function all()
    {
        return Cache::remember(self::cacheKey(),static::$duration , function ()
        {
            return self::getModel()::all();
        });
    }

    public static function get($id)
    {
        return Cache::remember(self::cacheKey($id),static::$duration , function () use($id) {
            return self::getModel()::get($id);
        });
    }

    public static function filter(array $filter)
    {
        // TODO - should the filter method be cached?
        return self::getModel()::filter($filter);
    }

    public static function create($data)
    {
        Cache::forget(self::cacheKey());
        return self::getModel()::create($data);
    }

    public static function update($id, $data)
    {
        Cache::forget(self::cacheKey($id));
        Cache::forget(self::cacheKey());

        return self::getModel()::update($id, $data);
    }

    public static function delete($id)
    {
        Cache::forget(self::cacheKey());
        Cache::forget(self::cacheKey($id));

        return self::getModel()::delete($id);
    }


    public static function prebuildCache()
    {

        // TODO -

    }

    private static function getModel()
    {
        $pieces = explode('\\', get_called_class());

        // remove "Cache" from the model path
        unset($pieces[2]);

        return implode("\\", $pieces);

    }


    private static function cacheKey($id = null)
    {
        $pieces = explode('\\', get_called_class());

        $class_name = array_pop($pieces);

        return $id ? $class_name . '.' . $id : $class_name;
    }
}