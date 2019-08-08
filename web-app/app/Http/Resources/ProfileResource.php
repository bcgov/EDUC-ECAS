<?php

namespace App\Http\Resources;

use App\Dynamics\Decorators\CacheDecorator;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $repository         = env('DATASET') == 'Dynamics' ? 'Dynamics' : 'MockEntities\Repository';

        $district  = ( new CacheDecorator(App::make('App\\' . $repository .'\District')))->get($this['district_id']);
        $school    = ( new CacheDecorator(App::make('App\\' . $repository .'\School')))->get($this['school_id']);

        $regions    = ( new CacheDecorator(App::make('App\\' . $repository .'\Region')))->all();

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
          'region'                                 =>  new SimpleResource($regions->firstWhere('id', $this['region'])),
          'postal_code'                            =>  $this['postal_code'],
          'district'                               =>  $this['district_id'] ? new SimpleResource($district) : null,
          'school'                                 =>  $this['school_id'] ? new SchoolResource($school) : null,
          'professional_certificate_bc'            =>  $this['professional_certificate_bc'],
          'professional_certificate_yk'            =>  $this['professional_certificate_yk'],
          'professional_certificate_other'         =>  $this['professional_certificate_other'],
        ];
    }
}
