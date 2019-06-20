<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DistrictCollection extends ResourceCollection
{
    public $collects = DistrictResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection
                ->map
                ->toArray($request)
                ->all(),
            ];
    }
}
