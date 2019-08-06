<?php

namespace App\MockEntities;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    // For MockEntities, all fields are assignable
    protected $guarded = [];
}
