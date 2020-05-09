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

Route::post('activation', 'UrlController@activation');
Route::post('test2','UrlController@test2');

Route::get('sendTodaySubMessage','UrlController@sendTodaySubMessage');
Route::get('chargeSubs','UrlController@chargeSubs');
// Route::get('make_today_charging','UrlController@make_today_charging');

Route::get('getTodayMessage/{id}','UrlController@getMessage');

Route::post('unsub', 'Api\HomeController@msisdn');
Route::post('checkSub', 'Api\HomeController@checkSub');

// welcome message --- here must get by ip
define('DU_Flatter_Link','https://filters.digizone.com.kw/newdesignv4/6928153');

Route::get('logmessage','UrlController@logMessage');

 Route::get('make_insert_sub','UrlController@make_insert_sub');  // to add susbcribers that not have balance in the first time

  Route::get('sub_excel','UrlController@sub_excel');  // to make manule subscribe from excel  (becarful)

define('secureD_Failed',0);
define('secureD_Success',1);
define('secureD_product_already_purchased',2);
define('secureD_Insufficient_funds',3);

