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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['middleware' => ['auth:api']], function() {
// Read and write resources
    Route::resource('/assignments'                  , 'Api\AssignmentController');
    Route::resource('/profiles'                     , 'Api\ProfileController', ['except' => ['index']]);
    Route::resource('/profile-credentials'          , 'Api\ProfileCredentialController' );

});


