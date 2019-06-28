<?php

namespace App\MockEntities;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    public function sessionActivity()
    {
        return $this->belongsTo(SessionActivity::class);
    }


    public function sessionType()
    {
        return $this->belongsTo(SessionType::class);
    }
}
