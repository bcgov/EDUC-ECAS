<?php

namespace App\Http\Controllers;

use App\Dynamics\Decorators\CacheDecorator;
use App\Dynamics\District;
use Illuminate\Http\Request;

class DebugController extends Controller
{
    public function index()
    {

        $temp = new District();
        $cache = new CacheDecorator($temp);

        return ($cache->all());

    }
}
