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
Auth::routes();
Route::get('/', function () {
    //return view('welcome');
    return redirect('admin/mt');
});
Route::get('admin/allmts','MtController@allmts');
Route::get('home',function() {
    return redirect('admin/allmts');
});
Route::get('checkDuplicated','ServicesController@checkDuplicated');
Route::get('sch_force','ServicesController@MTSchedule');
Route::get('sch_approve','ServicesController@ApproveAllTodayContent');
Route::get('sch12','ServicesController@MTSchedule12');
Route::get('sch6','ServicesController@MTSchedule6');
Route::get('sch9','ServicesController@MTSchedule9');
Route::get('sch_coming','ServicesController@toSendTomorrow');
Route::get('sch_coming_not_approved','ServicesController@notSendTomrrow');
Route::get('sch_TodayMessagesStatus','ServicesController@TodayMessagesStatus');
Route::get('TodayMessagesStatus','ServicesController@TodayMessagesStatus');
// update new service links
Route::get('replaceURLsService','ServicesController@replaceURLsService');
Route::get('syncy','MtController@shortURLs');

Route::get('sch2', 'ServicesController@MTfailResend');
//Route::get('allmts','MtController@index');
Route::get('admin','MtController@adminindex');
Route::resource('admin/user','UsersController');
Route::get('admin/mt/filter','MtController@filter');
Route::get('admin/mt/search','MtController@search');
Route::resource('admin/mt','MtController');

Route::get('admin/mt/approve/{id}','MtController@approve');
Route::get('admin/mt/search','MtController@search');
Route::get('check','MtController@checkmessage');
Route::get('service','MtController@select_service');
Route::post('service','MtController@selectServiceCache');
Route::get('sync','BackendController@sync');

Route::get('get/{id}', 'MtController@Download');
Route::get('Bla7','ServicesController@replaceURLS');
Route::get('removeSpaces','ServicesController@removeSpaces');
Route::get('approveAllComing','ServicesController@approveAllComing');


Route::get('admin/toSendTomorrow','ServicesController@toSendTomorrow');
Route::get('admin/notSendTomrrow','ServicesController@notSendTomrrow');


Route::resource('admin/services','AdminServicesController');

Route::resource('admin/country','AdminCountryController');
Route::resource('admin/operator','AdminOperatorController');
Route::get('sendContentDaily','ServicesController@checkDailyMessages');
Route::get('zainKuwaitDailyMessages','ServicesController@zainKuwaitDailyMessages');


Route::get('testyousef','HomeController@test');



