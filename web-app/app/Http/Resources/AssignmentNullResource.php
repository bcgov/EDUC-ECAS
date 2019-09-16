<?php

namespace App\Http\Resources;

use App\Dynamics\AssignmentStatus;
use Illuminate\Http\Resources\Json\JsonResource;


class AssignmentNullResource extends JsonResource
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
            'id'            => 0,
            'status'        => [
                'name'              => 'Open'
            ],
        ];





    }
}
