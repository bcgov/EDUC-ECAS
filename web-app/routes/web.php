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

Route::get('/', 'DashboardController@login');
Route::post('/login', 'DashboardController@postLogin');
Route::get('/Profile', 'DashboardController@profile');
Route::get('/Dashboard', 'DashboardController@index');
Route::post('/Dashboard/credential', 'DashboardController@storeCredential');
Route::post('/Dashboard/profile', 'DashboardController@storeProfile');
Route::post('/Dashboard/post', 'DashboardController@post');