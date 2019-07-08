<?php


namespace App\Dynamics\Decorators;


use App\Interfaces\iModelRepository;
use Illuminate\Support\Facades\Cache;

class CacheDecorator implements iModelRepository
{
    
    const CACHE_DURATION = 28800; // 8 hours in seconds
    protected $model;

 
    public function __construct(iModelRepository $model)
    {
        $this->model = $model;
    }


    public function all()
    {
        return Cache::remember($this->cacheKey(),self::CACHE_DURATION , function ()
        {
            return $this->model->all();
        });
    }

    public function get($id)
    {
        return Cache::remember(self::cacheKey($id),self::CACHE_DURATION , function () use($id) {
            return $this->model->get($id);
        });
    }

    public function filter(array $filter)
    {
        // TODO - should the filter method be cached?
        return $this->model->filter($filter);
    }

    public function create($data)
    {
        Cache::forget($this->cacheKey());
        return $this->model->create($data);
    }

    public function update($id, $data)
    {
        Cache::forget(self::cacheKey($id));
        Cache::forget($this->cacheKey());

        return $this->model->update($id, $data);
    }

    public function delete($id)
    {
        Cache::forget($this->cacheKey());
        Cache::forget(self::cacheKey($id));

        return $this->model->delete($id);
    }


    public function prebuildCache()
    {

        // TODO -

    }

    private function cacheKey($id = null)
    {
        $pieces = explode('\\', get_class($this->model));

        $class_name = array_pop($pieces);

        return $id ? $class_name . '.' . $id : $class_name;
    }
    
}