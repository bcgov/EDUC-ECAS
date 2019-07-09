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

        $districts  = ( new CacheDecorator(App::make('App\\' . $repository .'\District')))->all();
        $schools    = ( new CacheDecorator(App::make('App\\' . $repository .'\School')))->all();

        if(strlen($this['social_insurance_number']) == 9) {
            $maskedSIN  = substr_replace($this['social_insurance_number'],'[RECEIVED]',0,9);
        } else {
            $maskedSIN = '';
        }

        return [
          'id'                                     =>  $this['id'],
          'user_id'                                =>  $this['user_id'],
          'first_name'                             =>  $this['first_name'],
          'preferred_first_name'                   =>  $this['preferred_first_name'],
          'last_name'                              =>  $this['last_name'],
          'email'                                  =>  $this['email'],
          'phone'                                  =>  $this['phone'],
          'social_insurance_number'                =>  $maskedSIN,
          'address_1'                              =>  $this['address_1'],
          'address_2'                              =>  $this['address_2'],
          'city'                                   =>  $this['city'],
          'region'                                 =>  $this['region'],
          'postal_code'                            =>  $this['postal_code'],
          'district'                               =>  new SimpleResource($districts->firstWhere('id', $this['district_id'])),
          'school'                                 =>  new SchoolResource($schools->firstWhere('id', $this['school_id'])),
          'professional_certificate_bc'            =>  $this['professional_certificate_bc'],
          'professional_certificate_yk'            =>  $this['professional_certificate_yk'],
          'professional_certificate_other'         =>  $this['professional_certificate_other'],
        ];
    }
}
