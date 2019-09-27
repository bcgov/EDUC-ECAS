<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// App Specific Routes
Route::get('/'                  , function () { return redirect('Dashboard'); });
Route::get('/Dashboard'         , 'DashboardController@index')->name('Dashboard');


