<?php

namespace App\Http\Resources;

use App\Dynamics\Decorators\CacheDecorator;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class AggregatedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type'          => 'aggregated',
            'attributes'    => [
                'sessions'      => ( new CacheDecorator(App::make('App\\' . env('DATASET') .'\Session')))->all(),
                'subjects'      => ( new CacheDecorator(App::make('App\\' . env('DATASET') .'\Subject')))->all(),
                'districts'     => ( new CacheDecorator(App::make('App\\' . env('DATASET') .'\District')))->all(),
                'regions'       => ( new CacheDecorator(App::make('App\\' . env('DATASET') .'\Region')))->all(),
            ]
        ];
    }


}



