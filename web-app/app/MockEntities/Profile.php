<?php

namespace App\MockEntities;


use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{

    public function district()
    {
        return $this->belongsTo(\App\Dynamics\District::class);
    }

    public function school()
    {
        // return $this->belongsTo(\App\Dynamics\Mock\School::class);

    }


}
