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



                 /*  SMS at 2.30 Egy  = 4.30 Emirates times */
                 $schedule->call('App\Http\Controllers\UrlController@sendTodaySubMessage')->dailyAt('06:30');  // SMS sending at 2.30 Egypt time   // 8.30 Egypt = 10.30 Emirate

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
