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
Route::resource('/{federated_id}/assignments'              , 'Api\AssignmentController');
Route::resource('/profile'                                 , 'Api\ProfileController', ['except' => ['index']]);
Route::resource('/{federated_id}/profile-credentials'      , 'Api\ProfileCredentialController' );



// Read only resources
//Route::resource('/schools'                  , 'Api\SchoolController', ['only' => ['index']]);
//Route::resource('/districts'                , 'Api\DistrictController', ['only' => ['index']]);
//Route::resource('/roles'                    , 'Api\RoleController', ['only' => ['index']]);
//Route::resource('/assignment-statuses'      , 'Api\AssignmentStatusController', ['only' => ['index']]);
//Route::resource('/contract-stages'          , 'Api\ContractStageController', ['only' => ['index']]);
//Route::resource('/payment-types'            , 'Api\PaymentTypeController', ['only' => ['index']]);
//Route::resource('/regions'                  , 'Api\RegionController', ['only' => ['index']]);
//Route::resource('/session-activities'       , 'Api\SessionActivityController', ['only' => ['index']]);
//Route::resource('/session-types'            , 'Api\SessionTypeController', ['only' => ['index']]);
//Route::resource('/subjects'                 , 'Api\SubjectController', ['only' => ['index']]);
//Route::resource('/credential-codes'         , 'Api\CredentialController', ['only' => ['index']]);
//Route::resource('/sessions'                 , 'Api\SessionController', ['only' => ['index']]);

