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


// Read and write resources
Route::resource('/assignments'              , 'Api\AssignmentController');
Route::resource('/profiles'                 , 'Api\ProfileController');
Route::resource('/profile-credentials'      , 'Api\CredentialController');

// Read only resources
Route::resource('/credentials'              , 'Api\CredentialController', ['only' => ['index','show']]);
Route::resource('/schools'                  , 'Api\SchoolController', ['only' => ['index','show']]);
Route::resource('/roles'                    , 'Api\RoleController', ['only' => ['index','show']]);
Route::resource('/assignment-statuses'      , 'Api\AssignmentStatusController', ['only' => ['index','show']]);
Route::resource('/contract-stages'          , 'Api\ContractStageController', ['only' => ['index','show']]);



// Local fictitious data for development purposes
Route::prefix('fake')->group(function () {

    // Read and write resources
    Route::resource('/profile'             , 'Fictitious\ProfileController');
    Route::resource('/profile-credentials' , 'Fictitious\ProfileCredentialsController');


    // Read only resources
    Route::resource('/credentials'         , 'Fictitious\CredentialController', ['only' => ['index','show']]);
    Route::resource('/schools'             , 'Fictitious\SchoolController', ['only' => ['index','show']]);
    Route::resource('/roles'               , 'Fictitious\RoleController', ['only' => ['index','show']]);
    Route::resource('/districts'           , 'Fictitious\DistrictController', ['only' => ['index','show']]);
    Route::resource('/assignment-statuses' , 'Fictitious\AssignmentStatusController', ['only' => ['index','show']]);





});
