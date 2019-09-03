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

        return [
            'id'                => $this['id'],
            'session_id'        => $this['session_id'],
            'contact_id'        => $this['contact_id'],
            'role'              => new RoleResource($this['role']),
            'contract_stage'    => new SimpleResource($this['stage']),
            'status'            => new SimpleResource($this['assignment_status']),
            'state'             => (Boolean) $this['state']

        ];
    }
}
