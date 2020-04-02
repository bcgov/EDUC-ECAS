<?php

namespace App\Http\Resources;


use App\Dynamics\Session;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;


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

        $now = Carbon::now();

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

        $diff_in_days = $now->diffInDays($end_carbon, false);


        return [
                'id'                => $this['id'],
                'activity'          => $this['activity'],
                'type'              => $this['type'],
                'diff_in_days'      => $diff_in_days,
                'isPast'            => (boolean) ($diff_in_days < 0),
                'date'              => $date_string,
                'location'          => $this['location'],
                'address'           => $this['address'],
                'city'              => $this['city'],
                'is_published'      => (Boolean) $this['is_published'],
                'session_status'    => array_keys(Session::$status,$this['status'])[0],
                'assignment'        => $this->when($this['assignment'], new AssignmentResource($this['assignment']) , new AssignmentNullResource([]))

        ];
    }
}
