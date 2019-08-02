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
        $roles                  = ( new CacheDecorator(App::make('App\\' . $repository .'\Role')))->all();
        $contract_stages        = ( new CacheDecorator(App::make('App\\' . $repository .'\ContractStage')))->all();
        $assignment_statuses    = ( new CacheDecorator(App::make('App\\' . $repository .'\AssignmentStatus')))->all();

        return [
            'id'                => $this['id'],
            'session_id'        => $this['session_id'],
            'contact_id'        => $this['contact_id'],
            'role'              => new RoleResource($roles->firstWhere('id', $this['role_id'])),
            'contract_stage'    => new SimpleResource($contract_stages->firstWhere('id', $this['contract_stage'])),
            'status'            => new SimpleResource($assignment_statuses->firstWhere('id', $this['status'])),
            'state'             => (Boolean) $this['state']

        ];
    }
}
