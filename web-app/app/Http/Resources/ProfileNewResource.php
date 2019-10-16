<?php

namespace App\Http\Resources;


use App\Dynamics\Interfaces\iCountry;
use App\Dynamics\Interfaces\iDistrict;
use App\Dynamics\Interfaces\iRegion;
use App\Dynamics\Interfaces\iSchool;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileNewResource extends JsonResource
{


    private $region;
    private $country;

    public function __construct($resource, iRegion $region, iCountry $country)
    {
        parent::__construct($resource);
        $this->region   = $region;
        $this->country  = $country;

    }


    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {


        return [
          'id'                                     =>  $this['id'],
          'federated_id'                           =>  $this['federated_id'],
          'first_name'                             =>  $this['first_name'],
          'preferred_first_name'                   =>  null,
          'last_name'                              =>  $this['last_name'],
          'email'                                  =>  $this['email'],
          'phone'                                  =>  null,
          'is_SIN_on_file'                         =>  (boolean) FALSE ,
          'address_1'                              =>  null,
          'address_2'                              =>  null,
          'city'                                   =>  null,
          'region'                                 =>  new SimpleResource($this->region->get('BC')),
          'postal_code'                            =>  null,
          'district'                               =>  null,
          'school'                                 =>  null,
          'country'                                =>  new SimpleResource($this->country->filter([ 'name' => 'Canada' ])[0]),
          'professional_certificate_bc'            =>  null,
          'professional_certificate_yk'            =>  null,
        ];
    }
}
