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
Route::resource('/profiles'                 , 'Api\ProfileController', ['except' => ['index']]);
Route::resource('/profile-credentials'      , 'Api\ProfileCredentialController', ['except' => ['index']] );
Route::resource('/sessions'                 , 'Api\SessionController', ['only' => ['index','show','store']]);

// Read only resources
Route::resource('/schools'                  , 'Api\SchoolController', ['only' => ['index','show']]);
Route::resource('/districts'                , 'Api\DistrictController', ['only' => ['index','show']]);
Route::resource('/roles'                    , 'Api\RoleController', ['only' => ['index','show']]);
Route::resource('/assignment-statuses'      , 'Api\AssignmentStatusController', ['only' => ['index','show']]);
Route::resource('/contract-stages'          , 'Api\ContractStageController', ['only' => ['index','show']]);
Route::resource('/payment-types'            , 'Api\PaymentTypeController', ['only' => ['index','show']]);
Route::resource('/regions'                  , 'Api\RegionController', ['only' => ['index','show']]);
Route::resource('/session-activities'       , 'Api\SessionActivityController', ['only' => ['index','show']]);
Route::resource('/session-types'            , 'Api\SessionTypeController', ['only' => ['index','show']]);
Route::resource('/subjects'                 , 'Api\SubjectController', ['only' => ['index','show']]);

// TODO - this needs fixing
Route::resource('/credential-codes'         , 'Api\CredentialController', ['only' => ['index']]);


