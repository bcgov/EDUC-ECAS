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
    Route::resource('/{profile_id}/portalassignment'               , 'Api\PortalAssignmentController');
    Route::resource('/{profile_id}/listuploadedfiles'               , 'Api\ListUploadedFilesController');   
    Route::resource('/{profile_id}/contractfile'               , 'Api\ContractFileController'); 
    Route::resource('/{profile_id}/fileupload'               , 'Api\FileUploadController');   
    Route::resource('/{profile_id}/filedownload'               , 'Api\FileDownloadController'); 
                  

});

Route::middleware(['cache.headers:private;max_age=0'])->group(function () {
    Route::resource('/{profile_id}/portalassignment'               , 'Api\PortalAssignmentController');
    Route::resource('/{profile_id}/listuploadedfiles'               , 'Api\ListUploadedFilesController');  
    Route::delete('/{profile_id}/filedelete'               , 'Api\FileDeleteController@delete');        
    Route::patch('/{profile_id}/filesubmit'               , 'Api\FileSubmitController@update');
    Route::get('/dashboard'                                   , 'Api\DashboardSetupController@index');
});

Route::middleware(['cache.headers:public;max_age=172800'])->group(function () {
    Route::get('/districts'                                   , 'Api\DistrictSearchController@index');
    Route::get('/schools'                                     , 'Api\SchoolSearchController@index');

});
Route::post('/logout', 'Api\LogoutController@logout');

Route::get('/keycloak_config'                             , 'Api\KeycloakConfigController@index'  )
    ->middleware(['cache.headers:public;immutable']);

