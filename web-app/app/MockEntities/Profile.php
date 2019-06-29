<?php

namespace App\MockEntities;


use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{

    public function district()
    {
        return $this->belongsTo(\App\MockEntities\District::class);
    }

    public function school()
    {
        return $this->belongsTo(\App\MockEntities\School::class);

    }


}
