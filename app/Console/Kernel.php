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


                 $schedule->call('App\Http\Controllers\UrlController@chargeSubs')->dailyAt('08:00');  // our time is 8+2 = 10  // charging scheduling
                 $schedule->call('App\Http\Controllers\UrlController@sendTodaySubMessage')->dailyAt('12:30');  // SMS


/*
        $schedule->call('App\Http\Controllers\ServicesController@MTSchedule')->dailyAt('08:00');  // our time is 8+2 = 10
        $schedule->call('App\Http\Controllers\ServicesController@MTSchedule')->dailyAt('08:30');
        $schedule->call('App\Http\Controllers\ServicesController@MTSchedule')->dailyAt('09:00');
        $schedule->call('App\Http\Controllers\ServicesController@MTSchedule')->dailyAt('09:30');
        $schedule->call('App\Http\Controllers\ServicesController@MTSchedule')->dailyAt('10:00');
        $schedule->call('App\Http\Controllers\ServicesController@MTSchedule')->dailyAt('10:30');
        $schedule->call('App\Http\Controllers\ServicesController@MTSchedule')->dailyAt('11:00');
        $schedule->call('App\Http\Controllers\ServicesController@MTSchedule')->dailyAt('11:30');
        $schedule->call('App\Http\Controllers\ServicesController@MTSchedule')->dailyAt('12:00');
        $schedule->call('App\Http\Controllers\ServicesController@MTSchedule')->dailyAt('12:30');
        $schedule->call('App\Http\Controllers\ServicesController@MTSchedule')->dailyAt('13:00');
        $schedule->call('App\Http\Controllers\ServicesController@MTSchedule')->dailyAt('13:30');
        $schedule->call('App\Http\Controllers\ServicesController@MTSchedule')->dailyAt('14:00');

        // test



    // $schedule->call('App\Http\Controllers\ServicesController@TodayMessagesStatus')->dailyAt('09:10');
        $schedule->call('App\Http\Controllers\ServicesController@toSendTomorrow')->dailyAt('09:15');
        $schedule->call('App\Http\Controllers\ServicesController@notSendTomrrow')->dailyAt('09:20');
        $schedule->call('App\Http\Controllers\ServicesController@MTfailResend')->hourly();



        // scheduling at 12.00 pm   --- 6.00 pm  --- 9.00 pm according to kuwait time  that is +1hour fro egypt time
        $schedule->call('App\Http\Controllers\ServicesController@MTSchedule12')->dailyAt('09:00');  // 9+2 = 11    11 + 1 = 12
        $schedule->call('App\Http\Controllers\ServicesController@MTSchedule6')->dailyAt('15:00');  //  3+2= 5   5+1=6
        $schedule->call('App\Http\Controllers\ServicesController@MTSchedule9')->dailyAt('18:00');  //  6+2=8   8 +1 = 9

        */

      //  $schedule->call('App\Http\Controllers\ServicesController@TodayMessagesStatus')->dailyAt('08:40');  // our time is 10+2 = 12
      //  $schedule->call('App\Http\Controllers\ServicesController@toSendTomorrow')->dailyAt('08:45');
      //  $schedule->call('App\Http\Controllers\ServicesController@notSendTomrrow')->dailyAt('09:20');


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
