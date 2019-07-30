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

        // TODO - move this mess of logic to the controller with use of a service provider

        $repository             = env('DATASET') == 'Dynamics' ? 'Dynamics' : 'MockEntities\Repository';

        if($this['id']) {
            $profile_credentials    = ( new CacheDecorator(App::make('App\\' . $repository .'\ProfileCredential')))->filter(['contact_id'=> $this['id']]);
            $assignments            = ( new CacheDecorator(App::make('App\\' . $repository .'\Assignment')))->filter(['contact_id'=> $this['id']]);
        } else {
            $profile_credentials    = collect([]);
            $assignments            = collect([]);
        }

        $sessions               = ( new CacheDecorator(App::make('App\\' . $repository .'\Session')))->all();
        $districts              = ( new CacheDecorator(App::make('App\\' . $repository .'\District')))->all();
        $credentials            = ( new CacheDecorator(App::make('App\\' . $repository .'\Credential')))->all();
        $regions                = ( new CacheDecorator(App::make('App\\' . $repository .'\Region')))->all();
        $schools                = ( new CacheDecorator(App::make('App\\' . $repository .'\School')))->all();
        $subjects               = ( new CacheDecorator(App::make('App\\' . $repository .'\Subject')))->all();


        return [
            'user'                  => new ProfileResource($this),
            'user_credentials'      => ProfileCredentialResource::collection($profile_credentials),
            'assignments'           => AssignmentResource::collection($assignments),
            'sessions'              => SessionResource::collection($sessions),
            'subjects'              => SimpleResource::collection($subjects),
            'districts'             => SimpleResource::collection($districts),
            'regions'               => SimpleResource::collection($regions),
            'credentials'           => SimpleResource::collection($credentials),
            'schools'               => SchoolResource::collection($schools),
        ];
    }
}
