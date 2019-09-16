<?php

namespace App\Http\Resources;

use App\Dynamics\AssignmentStatus;
use Illuminate\Http\Resources\Json\JsonResource;


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

        // TODO - Move this to the controller so it's more transparent
        $assignment_statuses = resolve(AssignmentStatus::class);
        $statuses = $assignment_statuses->all();

        if (! $this['id']) {

            return [
                'id'            => 0,
                'status'        => [
                    'name'              => 'Open'
                ],
            ];

        }


        return [
            'id'                => $this['id'],
            'session_id'        => $this['session_id'],
            'contact_id'        => $this['contact_id'],
            'role'              => $this['role_id'],
            'contract_stage'    => $this['contract_stage'],
            'status'            => $statuses->firstWhere('id', $this['status_id']),
            'state'             => (Boolean) $this['state']

        ];
    }
}
