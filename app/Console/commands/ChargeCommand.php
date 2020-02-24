<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ChargeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'charge:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cron Job for Charge';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        //echo url('du_system/api/chargeSubs') ; die;
        // Get cURL resource
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => url('api/chargeSubs'),
            CURLOPT_USERAGENT => 'Codular Sample cURL Request'
        ]);
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);
        $this->comment($resp);


        /*

        $URL = "http://localhost/du_system/api/chargeSubs" ;  // local
        //   $URL = "http://10.2.10.239:2080/~smrtlink/campain/schedule" ;

           $ch = curl_init();
                   $timeout = 60000000000;
                   curl_setopt($ch, CURLOPT_URL, $URL);
                   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                   curl_setopt($ch,CURLOPT_TIMEOUT, $timeout);

                   $data = curl_exec($ch);
                   curl_close($ch);

                   print_r($data);
                   // return $data;

           $this->info('cron checker ran successfuly');
           */


    }
}
