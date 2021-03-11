<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\Inspire::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('inspire')
                 ->hourly();

                 /* charging from 3 AM to 12 PM  */
               //   $schedule->call('App\Http\Controllers\UrlController@make_today_charging')->dailyAt('01:00');  // charging
               //   $schedule->call('App\Http\Controllers\UrlController@make_today_charging')->dailyAt('02:00');  // charging
                //  $schedule->call('App\Http\Controllers\UrlController@make_today_charging')->dailyAt('03:00');  // charging
                //  $schedule->call('App\Http\Controllers\UrlController@make_today_charging')->dailyAt('04:00');  // charging
                  $schedule->call('App\Http\Controllers\UrlController@make_today_charging')->dailyAt('05:00');
                 $schedule->call('App\Http\Controllers\UrlController@make_today_charging')->dailyAt('06:00');

                 // failed charging for empty response
                 $schedule->call('App\Http\Controllers\UrlController@make_today_charging_for_failed')->dailyAt('07:00');
                 $schedule->call('App\Http\Controllers\UrlController@make_today_charging_for_failed')->dailyAt('08:00');
                 //  $schedule->call('App\Http\Controllers\UrlController@make_today_charging')->dailyAt('07:00');
                //  $schedule->call('App\Http\Controllers\UrlController@make_today_charging')->dailyAt('08:00');
                //  $schedule->call('App\Http\Controllers\UrlController@make_today_charging')->dailyAt('09:00');
              //   $schedule->call('App\Http\Controllers\UrlController@chargeSubs')->dailyAt('10:00');


                // SMS for failed messages 8.30 Egypt = 10.30 Emirate
                 $schedule->call('App\Http\Controllers\UrlController@sendTodaySubMessage')->dailyAt('06:30');
                 // SMS for failed messages 9.30 Egypt = 11.30 Emirate
               //  $schedule->call('App\Http\Controllers\UrlController@sendTodaySubMessageForFailed')->dailyAt('07:30');


                 // Egypt time  11.28 Am
                 $schedule->call('App\Http\Controllers\UrlController@todayMessagesStatus')->dailyAt('09:30');
                 $schedule->call('App\Http\Controllers\UrlController@tomorrowMessagesStatus')->dailyAt('08:31');


                 // send weekly reminder
                 $schedule->call('App\Http\Controllers\WeeklyReminderDateController@weekly_reminder_date')->dailyAt('11:00');
                 $schedule->call('App\Http\Controllers\WeeklyReminderDateController@weekly_reminder_date')->dailyAt('12:00');

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
