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

// BC Government standard Footer
Route::get('/disclaimer', 'PageController@disclaimer');
Route::get('/privacy', 'PageController@privacy');
Route::get('/accessibility', 'PageController@accessibility');
Route::get('/copyright', 'PageController@copyright');
Route::get('/contact', 'PageController@contact');

// App Specific Routes
Route::get('/', 'DashboardController@login');
Route::post('/login', 'DashboardController@postLogin');

// For testing purposes it's handy to expose the id in the URL
Route::get('/{federated_id}/Dashboard', 'DashboardController@index');



