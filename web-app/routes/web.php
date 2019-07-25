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
Route::get('/disclaimer',       'PageController@disclaimer');
Route::get('/privacy',          'PageController@privacy');
Route::get('/accessibility',    'PageController@accessibility');
Route::get('/copyright',        'PageController@copyright');
Route::get('/contact',          'PageController@contact');


// Keycloak routes
Route::get('/redirect',         'KeycloakAuthController@redirect')->name('login');
Route::get('/logout',           'KeycloakAuthController@logout');
Route::get('/callback',         'KeycloakAuthController@callback');

// App Specific Routes
Route::get('/'                  , function () { return redirect('Dashboard'); });
Route::get('/Dashboard'         , 'DashboardController@index')->name('Dashboard');

