<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['cache.headers:private;max_age=300;etag'])->group(function () {
    Route::resource('/profiles'                               , 'Api\ProfileController', ['except' => ['index']]);
    Route::resource('/{profile_id}/profile-credentials'       , 'Api\ProfileCredentialController' );
    Route::resource('/{profile_id}/assignments'               , 'Api\AssignmentController');
    Route::get('/dashboard'                                   , 'Api\DashboardSetupController@index');

});


Route::middleware(['cache.headers:public;max_age=172800'])->group(function () {
    Route::get('/districts'                                   , 'Api\DistrictSearchController@index');
    Route::get('/schools'                                     , 'Api\SchoolSearchController@index');

});

Route::get('/keycloak_config'                             , 'Api\KeycloakConfigController@index'  )
    ->middleware(['cache.headers:public;immutable']);

