<?php

namespace App\Http\Controllers;

use App\Dynamics\Decorators\CacheDecorator;
use App\Dynamics\District;
use App\Dynamics\Profile;
use Illuminate\Http\Request;

class DebugController extends Controller
{
    public function index()
    {

        $temp = new Profile();
        $cache = new CacheDecorator($temp);

        return ($cache->all());

    }


    public function fake()
    {

        return \App\Dynamics\Mock\Profile::all();


    }

}
