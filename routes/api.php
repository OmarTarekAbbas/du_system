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

// Route::get('messages', 'MessageController@index');
// Route::get('messages/{id}', 'MessageController@show');
// Route::post('messages', 'MessageController@store');
Route::get('messages/{id}', 'MessageController@update');
// Route::delete('messages/{id}', 'MessageController@delete');


//=======================  Du routes  ======================//
define('DU_SMS_SEND_MESSAGE','http://41.33.167.14:2080/~smsdu/du_send_message');
define('DU_Flatter_Link','https://filters.digizone.com.kw/newdesignv4/6928153');
Route::post('activation', 'UrlController@activation');
Route::post('test2','UrlController@test2');

Route::group([ 'namespace' => 'Api'], function () {
    Route::post('test', 'HomeController@msisdn');
   });

