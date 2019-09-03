<?php

namespace App\Http\Resources;

use App\Dynamics\Decorators\CacheDecorator;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;


class SessionResource extends JsonResource
{


    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {


        // Display a nicely formatted date string
        $start_carbon = Carbon::create($this['start_date']);
        $end_carbon = Carbon::create($this['end_date']);
        $date_string = $start_carbon->format('M j') . ' - ';
        if ($start_carbon->format('M') == $end_carbon->format('M'))
        {
            $date_string .= $end_carbon->format('j');
        }
        else
        {
            $date_string .= $end_carbon->format('M j');
        }


        return [
                'id'                => $this['id'],
                'activity'          => $this['activity'],
                'type'              => $this['type'],
                'date'              => $date_string,
                'location'          => $this['location'],
                'address'           => $this['address'],
                'city'              => $this['city'],
                'status'            => $this['status'],
                'assignment'        => $this['assignment']

        ];
    }
}
