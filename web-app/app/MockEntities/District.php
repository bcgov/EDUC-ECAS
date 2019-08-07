<?php

namespace App\MockEntities;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    public function profiles()
    {

        return $this->hasMany(Profile::class);
    }
}
