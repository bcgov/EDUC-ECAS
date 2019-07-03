<?php

namespace App\Http\Resources;

use App\Dynamics\Decorators\CacheDecorator;
use App\Http\Controllers\Interfaces\iModelRepository;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class SessionResource extends JsonResource
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

        $sessionActivities  = ( new CacheDecorator(App::make('App\\' . $repository .'\SessionActivity')))->all();
        $sessionTypes       = ( new CacheDecorator(App::make('App\\' . $repository .'\SessionType')))->all();


        return [
            'type'          => 'sessions',
            'id'            => $this['id'],
            'attributes'    => [
                'activity'          => $sessionActivities->firstWhere('id',$this['activity_id']),
                'type'              => $sessionTypes->firstWhere('id',$this['type_id']),
                'start_date'        => new Carbon($this['start_date']),
                'end_date'          => new Carbon($this['end_date']),
                'location'          => $this['location'],
                'address'           => $this['address'],
                'city'              => $this['city'],

            ]
        ];
    }
}
