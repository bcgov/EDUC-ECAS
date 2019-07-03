<?php

namespace App\Http\Resources;

use App\Dynamics\Decorators\CacheDecorator;
use App\Http\Controllers\Interfaces\iModelRepository;
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

        $repository             = env('DATASET') == 'Dynamics' ? 'Dynamics' : 'MockEntities\Repository';

        $sessionActivities      = ( new CacheDecorator(App::make('App\\' . $repository .'\SessionActivity')))->all();
        $sessionTypes           = ( new CacheDecorator(App::make('App\\' . $repository .'\SessionType')))->all();
        $assignments            = ( new CacheDecorator(App::make('App\\' . $repository .'\Assignment')))->all();
        $assignment_statuses    = ( new CacheDecorator(App::make('App\\' . $repository .'\AssignmentStatus')))->all();

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

        // Create a status field and populate by looking up related records in the assignment resource (if exists)
        // and display the status from the assignment_status resource
        $assignment_id = $assignments->firstWhere('session_id', $this['id']);
        $status = $assignment_statuses->firstWhere('id',$assignment_id['status'])['name'];

        return [
                'id'                => $this['id'],
                'activity'          => $sessionActivities->firstWhere('id',$this['activity_id']),
                'type'              => $sessionTypes->firstWhere('id',$this['type_id']),
                'date'              => $date_string,
                'location'          => $this['location'],
                'address'           => $this['address'],
                'city'              => $this['city'],
                'status'            => $status,
                'assignment_id'     => $assignment_id,
        ];
    }
}
