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
Route::get('test_du_purchaseConsumeProduct','UrlController@test_du_purchaseConsumeProduct');

Route::get('todayMessagesStatus','UrlController@todayMessagesStatus');
Route::get('tomorrowMessagesStatus','UrlController@tomorrowMessagesStatus');

Route::get('sendTodaySubMessage','UrlController@sendTodaySubMessage');
Route::get('sendTodaySubMessageForFailed','UrlController@sendTodaySubMessageForFailed');
Route::post('du_send_pincode','UrlController@du_send_pincode');
Route::get('test_du_send','UrlController@test_du_send');
Route::get('chargeSubs','UrlController@chargeSubs');
Route::get('chargeSubs_for_failed','UrlController@chargeSubs_for_failed')->name('admin.chargeSubs2');

 Route::get('make_today_charging','UrlController@make_today_charging');
 Route::get('make_today_charging_for_failed','UrlController@make_today_charging_for_failed');



Route::get('getTodayMessage/{id}','UrlController@getMessage');

Route::post('unsub', 'Api\HomeController@msisdn');
Route::post('checkSub', 'Api\HomeController@checkSub');

// welcome message --- here must get by ip
define('DU_Flatter_Link','https://filters.digizone.com.kw/newdesignv4/6928153');

Route::get('logmessage','UrlController@logMessage');

Route::get('make_insert_sub','UrlController@make_insert_sub');  // to add susbcribers that not have balance in the first time

Route::get('sub_excel','UrlController@sub_excel');  // to make manule subscribe from excel  (becarful)

Route::get('du_kannel_send_messages_log','UrlController@du_kannel_send_messages_log');

define('secureD_Failed',0);
define('secureD_Success',1);
define('secureD_product_already_purchased',2);
define('secureD_Insufficient_funds',3);

Route::get('test_mbc','UrlController@test_mbc');
/***************start timwe api*************/

define('PAGINATION', 30);
define('TIMWE_AuthUser', 'IVAS_CCT');
define('TIMWE_AuthPass', 'CCT_2020_981');
define('OpId', '268');
define('SERVICE_ID', '1');
define('CountryId', '971');

define('SERVICE_NAME', 'IVAS');
define('PRODUCT_TYPE', 'Brokerage');
define('TIMWE_SHORTCODE', '4971');
define('ProductId', ['5', '7']);
define('ACTIVE_SERVICES', ['liveqarankhatma', 'flaterrotanadaily']);
define('ACTIVE_SERVICES_Array', [
                            'flaterrotanadaily' => '5' ,
                            'liveqarankhatma'=>'7'

]);

define('ACTIVE_SERVICES_Array_good_names', [
    'flaterrotanadaily' => 'Flatter Rotana' ,
    'liveqarankhatma'=>'Live Qaran Khatma'

]);


define('WEEKLY_REMINDER_MESSAGE', [
    'flaterrotanadaily' => "Flater Rotana service Daily charges(2/-AED) to unsubscribe you can send stopr to 4971" ,
    'liveqarankhatma'=>"Alafasy Quran service  Daily charges(2/-AED) to unsubscribe you can send Stopq to 4971"

]) ;



Route::get('inquiry','Api\TimweController@inquiry');
Route::get('unsubscribe','Api\TimweController@unsubscribe');
Route::get('userhistory','Api\TimweController@userhistory');
Route::get('sendmt', 'Api\TimweController@sendmt');

/********************************************/


/*********************start weekly_reminder_date***********************/
Route::get('weekly_reminder_date', 'WeeklyReminderDateController@weekly_reminder_date');
Route::get('weekly_statistics', 'WeeklyReminderDateController@weekly_statistics');
Route::get('weekly_statistics_excel', 'WeeklyReminderDateController@weekly_statistics_excel');
/***********************end weekly_reminder_date*********************/
/*

1- weekly reminder :
- for subscribers table : add new colum "weekly_reminder_date"  nullable
- make cron when today = weekly_reminder_date in subscribers table =>  send weekly reminder by service and how to unsub
=> make insert into log_messages table  [ message_type = "Weekly_Reminder"]
=> add week to weekly_reminder_date column on susbcribers table  if send is  success by kannel
=> you can get weekly reminder message from   WEEKLY_REMINDER_MESSAGE['flaterrotanadaily']  OR  WEEKLY_REMINDER_MESSAGE['liveqarankhatma']


2-make grace peroid is 30 days :
- on subscribers table  add new column : grace_days  nullable  default 0  int
- on charging table :  statu_code when not equal = 0   =>  grace_days + 1
 when make daily charging =   make_today_charging  and make_today_charging_for_failed
 - when grace_days == 31  make force unsub  :  here in unsubscriber table add new column grace nullable
 and when this case accur make grace = 1


 3-weekly report template :
 - # of charged users today
 - # of active users (30 days or less)  = all subscribers
 - SMS reminders sent  [ it  should be   active users  * 7 ]



*/
