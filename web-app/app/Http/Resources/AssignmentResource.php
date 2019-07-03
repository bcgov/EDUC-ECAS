<?php

namespace App\Http\Resources;

use App\Dynamics\Decorators\CacheDecorator;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class AssignmentResource extends JsonResource
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
        $roles                  = ( new CacheDecorator(App::make('App\\' . $repository .'\Role')))->all();
        $contract_stages        = ( new CacheDecorator(App::make('App\\' . $repository .'\ContractStage')))->all();
        $assignment_statuses    = ( new CacheDecorator(App::make('App\\' . $repository .'\AssignmentStatus')))->all();

        return [
            'id'                => $this['id'],
            'session'           => new SessionResource($sessions->firstWhere('id', $this['session_id'])),
            'user_id'           => $this['user_id'],
            'role'              => $roles->firstWhere('id', $this['role_id']),
            'contract_stage'    => $contract_stages->firstWhere('id', $this['contract_stage']),
            'status'            => $assignment_statuses->firstWhere('id', $this['status']),
            'state'             => (Boolean) $this['state']

        ];
    }
}
