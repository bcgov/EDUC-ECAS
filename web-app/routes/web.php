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

// App Specific Routes

Route::middleware(['cache.headers:private;max_age=300;etag'])->group(function () {

    Route::get('/'                  , function () { return redirect('Dashboard'); });
    Route::get('/Dashboard'         , 'DashboardController@index')->name('Dashboard');
    Route::get('/{profile_id}/assignments'         , 'AssignmentController@index');
    //Route::get('/{profile_id}/portalassignment'         , 'PortalAssignmentController@inndex')->name('PortailAssignment');
    Route::get('/{profile_id}/portalassignment', ['middleware' => 'auth', 'uses' => 'PortalAssignmentController@index']);
    Route::get('/{profile_id}/listuploadedfiles'         , 'ListUploadedFilesController@index');
    Route::get('/{profile_id}/contractfile'         , 'ContractFileController@index');
    Route::post('/{profile_id}/fileupload'         , 'FileUploadController@store'); 
    Route::get('/{annotation_id}/filedownload'         , 'FileDownloadController@index');    
    Route::delete('/{annotation_id}/filedelete'         , 'FileDeleteController@delete');
    Route::post('/{annotation_id}/filesubmit'         , 'FileSubmitController@update');
    Route::post('/logout'         , 'LogoutController@logout');
});


