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
        $profile                = ( new CacheDecorator(App::make('App\\' . $repository .'\Profile')))->filter(['id'=> $this->federated_id])->first();
        $profile_credentials    = ( new CacheDecorator(App::make('App\\' . $repository .'\ProfileCredential')))->filter(['user_id'=> $this->federated_id]);
        $assignments            = ( new CacheDecorator(App::make('App\\' . $repository .'\Assignment')))->filter(['user_id'=> $this->federated_id]);
        $districts              = ( new CacheDecorator(App::make('App\\' . $repository .'\District')))->all();
        $credentials            = ( new CacheDecorator(App::make('App\\' . $repository .'\Credential')))->all();
        $regions                = ( new CacheDecorator(App::make('App\\' . $repository .'\Region')))->all();
        $schools                = ( new CacheDecorator(App::make('App\\' . $repository .'\School')))->all();


        return [
            'user'                  => new ProfileResource($profile),
            'user_credentials'      => ProfileCredentialResource::collection($profile_credentials),
            'assignments'           => AssignmentResource::collection($assignments),
            'sessions'              => SessionResource::collection($sessions),
            'subjects'              => ( new CacheDecorator(App::make('App\\' . $repository .'\Subject')))->all(),
            'districts'             => SimpleResource::collection($districts),
            'regions'               => SimpleResource::collection($regions),
            'credentials'           => SimpleResource::collection($credentials),
            'schools'               => SchoolResource::collection($schools),
        ];
    }
}

/*
 *
 * // TODO - move the instantiation code below to RepositoryServiceProvider


        $user                   = App::make('App\\' . $repository .'\Profile');
        $user_credentials       = App::make('App\\' . $repository .'\ProfileCredential');
        $assignments            = App::make('App\\' . $repository .'\Assignment');

        return $this->loadDistrictAndSchoolNames($user);

        return view('dashboard', [
            'user'                   => (new CacheDecorator($user))->get($temporary_user_id),
            'user_credentials'       => (new CacheDecorator($user_credentials))->filter(['id' => $temporary_user_id]),
            'assignments'            => $assignments->filter(['id' => $temporary_user_id]),

        ]);
 *
 */
