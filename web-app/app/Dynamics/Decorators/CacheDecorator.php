<?php


namespace App\Dynamics\Decorators;


use App\Dynamics\Interfaces\iAssignment;
use App\Dynamics\Interfaces\iPortalAssignment;
use App\Dynamics\Interfaces\iAssignmentStatus;
use App\Dynamics\Interfaces\iContractStage;
use App\Dynamics\Interfaces\iContract;
use App\Dynamics\Interfaces\iCountry;
use App\Dynamics\Interfaces\iCredential;
use App\Dynamics\Interfaces\iDistrict;
use App\Dynamics\Interfaces\iModelRepository;
use App\Dynamics\Interfaces\iPayment;
use App\Dynamics\Interfaces\iProfile;
use App\Dynamics\Interfaces\iProfileCredential;
use App\Dynamics\Interfaces\iRegion;
use App\Dynamics\Interfaces\iRole;
use App\Dynamics\Interfaces\iSchool;
use App\Dynamics\Interfaces\iSession;
use App\Dynamics\Interfaces\iSessionActivity;
use App\Dynamics\Interfaces\iSessionType;
use App\Dynamics\Interfaces\iSubject;
use Illuminate\Support\Facades\Cache;

class CacheDecorator implements
                                iAssignment,
                                iPortalAssignment,
                                iAssignmentStatus,
                                iContractStage,
                                iContract,
                                iCredential,
                                iDistrict,
                                iPayment,
                                iProfile,
                                iProfileCredential,
                                iRegion,
                                iCountry,
                                iRole,
                                iSchool,
                                iSession,
                                iSessionActivity,
                                iSessionType,
                                iSubject

{

    protected $model;
    protected $duration;

 
    public function __construct(iModelRepository $model)
    {
        $this->model = $model;
        $this->duration = config('dynamics.cache.seconds');
    }


    public function all()
    {
        return Cache::remember($this->cacheKey(),$this->duration , function ()
        {
            return $this->model->all();
        });
    }

    public function get($id)
    {
        return Cache::remember(self::cacheKey($id),$this->duration , function () use($id) {
            return $this->model->get($id);
        });
    }


    public function firstOrCreate($id, $data)
    {

        return $this->model->firstOrCreate($id, $data);

    }

    public function list($id)
    {

        return $this->model->list($id);

    }    


    public function filter(array $filter)
    {
        // TODO - should the filter method be cached?
        return $this->model->filter($filter);
    }

    public function filterContains(array $filter)
    {
        return $this->model->filterContains($filter);
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

        return Cache::remember(self::cacheKey($id),$this->duration , function () use($id, $data) {
            return $this->model->update($id, $data);
        });

    }


    public function delete($id)
    {
        Cache::forget($this->cacheKey());
        Cache::forget(self::cacheKey($id));

        return $this->model->delete($id);
    }


    private function cacheKey($id = null)
    {
        $pieces = explode('\\', get_class($this->model));

        $class_name = array_pop($pieces);

        return $id ? $class_name . '.' . $id : $class_name;
    }
}