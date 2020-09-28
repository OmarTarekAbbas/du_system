<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Subscriber;
use App\Activation;
use App\Unsubscriber;
use App\Service;
use Illuminate\Support\Facades\Session;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function msisdn(Request $request)
    {
        $output = 0;
        $result = Activation::where("msisdn", $request->msisdn)->where("serviceid", $request->serviceid)->orderBy("created_at", "desc")->first(['id', 'msisdn', 'serviceid']);
        if ($request->msisdn == $result["msisdn"]) {
            $sub    = Subscriber::where("activation_id", $result->id)->first();
            if ($sub) {
                $unsub  = new Unsubscriber();
                $unsub->activation_id = $sub->activation_id;
                $unsub->save();
                $sub->delete();
                $output = 1;
            } else {
                $output = 0;
            }
        } else {
            $output = 0;
        }



        $data["msisdn"] = $request->msisdn;
        $data["serviceid"] = $request->serviceid;
        $data["output"] = $output;
        $this->log('DU Unsub Notification', $request->fullUrl(), $data);


        return $output;
    }




    public function checkSub(Request $request)
    {
        $output = 0;
        $result = Activation::where("msisdn", $request->msisdn)->where("serviceid", $request->serviceid)->orderBy("created_at", "Desc")->first(['id', 'msisdn', 'serviceid']);
        if ($request->msisdn == $result["msisdn"]) {
            $sub    = Subscriber::where("activation_id", $result->id)->first();
            if ($sub) {
                $output = 1;
            } else {
                $output = 0;
            }
        } else {
            $output = 0;
        }



        $data["msisdn"] = $request->msisdn;
        $data["serviceid"] = $request->serviceid;
        $data["output"] = $output;
        $this->log('DU CheckSub Notification', $request->fullUrl(), $data);

        return $output;
    }




    public function log($actionName, $URL, $parameters_arr)
    {

        date_default_timezone_set("Africa/Cairo");
        $date = date("Y-m-d");
        $log = new Logger($actionName);
        // to create new folder with current date  // if folder is not found create new one
        if (!\File::exists(storage_path('logs/' . $date . '/' . $actionName))) {
            \File::makeDirectory(storage_path('logs/' . $date . '/' . $actionName), 0775, true, true);
        }

        $log->pushHandler(new StreamHandler(storage_path('logs/' . $date . '/' . $actionName . '/logFile.log', Logger::INFO)));
        $log->addInfo($URL, $parameters_arr);
    }
}
