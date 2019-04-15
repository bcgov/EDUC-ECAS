<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function disclaimer()
    {
        return view('boilerplate.disclaimer');
    }

    public function privacy()
    {
        return view('boilerplate.privacy');
    }

    public function accessibility()
    {
        return view('boilerplate.accessibility');
    }

    public function contact()
    {
        return view('boilerplate.contact');
    }

    public function copyright()
    {
        return view('boilerplate.copyright');
    }
}
