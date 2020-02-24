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
    }
}
