<?php

namespace App\Http\Resources;

use App\Dynamics\Decorators\CacheDecorator;
use App\Dynamics\District;
use App\Dynamics\Region;
use App\Dynamics\School;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class ProfileResource extends JsonResource
{

    private $school;
    private $district;
    private $region;

    public function __construct($resource, School $school, District $district, Region $region)
    {
        parent::__construct($resource);
        $this->school   = $school;
        $this->district = $district;
        $this->region   = $region;

    }


    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $district   = $this->district->get($this['district_id']);
        $school     = $this->school->get($this['school_id']);
        $region     = $this->region->get($this['region']);

        return [
          'id'                                     =>  $this['id'],
          'federated_id'                           =>  $this['federated_id'],
          'first_name'                             =>  $this['first_name'],
          'preferred_first_name'                   =>  $this['preferred_first_name'],
          'last_name'                              =>  $this['last_name'],
          'email'                                  =>  $this['email'],
          'phone'                                  =>  $this['phone'],
          'is_SIN_on_file'                         =>  (boolean) ($this['social_insurance_number'] > 0),
          'address_1'                              =>  $this['address_1'],
          'address_2'                              =>  $this['address_2'],
          'city'                                   =>  $this['city'],
          'region'                                 =>  new SimpleResource($region),
          'postal_code'                            =>  $this['postal_code'],
          'district'                               =>  $this['district_id'] ? new SimpleResource($district) : null,
          'school'                                 =>  $this['school_id'] ? new SchoolResource($school) : null,
          'professional_certificate_bc'            =>  $this['professional_certificate_bc'],
          'professional_certificate_yk'            =>  $this['professional_certificate_yk'],
          'professional_certificate_other'         =>  $this['professional_certificate_other'],
        ];
    }
}
