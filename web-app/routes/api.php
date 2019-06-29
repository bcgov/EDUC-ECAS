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
Route::resource('/profiles'                 , 'Api\ProfileController', ['except' => ['index','delete']]);
Route::resource('/profile-credentials'      , 'Api\CredentialController', ['except' => ['index']]);
Route::resource('/credentials'              , 'Api\CredentialController');
Route::resource('/sessions'                 , 'Api\SessionController', ['only' => ['index','show','store']]);

// Read only resources
// TODO - remove show methods where not needed below
Route::resource('/schools'                  , 'Api\SchoolController', ['only' => ['index','show']]);
Route::resource('/roles'                    , 'Api\RoleController', ['only' => ['index','show']]);
Route::resource('/assignment-statuses'      , 'Api\AssignmentStatusController', ['only' => ['index','show']]);
Route::resource('/contract-stages'          , 'Api\ContractStageController', ['only' => ['index','show']]);
Route::resource('/payment-types'            , 'Api\PaymentTypeController', ['only' => ['index','show']]);
Route::resource('/regions'                  , 'Api\RegionController', ['only' => ['index','show']]);
Route::resource('/session-activities'       , 'Api\SessionActivityController', ['only' => ['index','show']]);
Route::resource('/session-types'            , 'Api\SessionTypeController', ['only' => ['index','show']]);


// Local fictitious data for development and testing purposes
// These routes and the underlying db were developed because there were
// significant delays getting access to the API
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
    Route::resource('/contract-stages'     , 'Fictitious\ContractStageController', ['only' => ['index','show']]);
    Route::resource('/payment-types'       , 'Fictitious\PaymentTypeController', ['only' => ['index','show']]);
    Route::resource('/regions'             , 'Fictitious\RegionController', ['only' => ['index','show']]);
    Route::resource('/session-activities'  , 'Fictitious\SessionActivityController', ['only' => ['index','show']]);
    Route::resource('/sessions'            , 'Fictitious\SessionController', ['only' => ['index','show']]);
    Route::resource('/session-types'       , 'Fictitious\SessionTypeController', ['only' => ['index','show']]);


});
