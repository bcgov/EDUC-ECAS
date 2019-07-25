<?php

namespace App\Http\Resources;

use App\Dynamics\Decorators\CacheDecorator;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class DashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $repository             = env('DATASET') == 'Dynamics' ? 'Dynamics' : 'MockEntities\Repository';
        $sessions               = ( new CacheDecorator(App::make('App\\' . $repository .'\Session')))->all();
        $districts              = ( new CacheDecorator(App::make('App\\' . $repository .'\District')))->all();
        $credentials            = ( new CacheDecorator(App::make('App\\' . $repository .'\Credential')))->all();
        $regions                = ( new CacheDecorator(App::make('App\\' . $repository .'\Region')))->all();
        $schools                = ( new CacheDecorator(App::make('App\\' . $repository .'\School')))->all();
        $subjects               = ( new CacheDecorator(App::make('App\\' . $repository .'\Subject')))->all();


        return [
            'sessions'              => SessionResource::collection($sessions),
            'subjects'              => SimpleResource::collection($subjects),
            'districts'             => SimpleResource::collection($districts),
            'regions'               => SimpleResource::collection($regions),
            'credentials'           => SimpleResource::collection($credentials),
            'schools'               => SchoolResource::collection($schools),
        ];
    }
}
