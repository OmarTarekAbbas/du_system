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



    public function sendmt(Request $request)
    {

        if ($request->AuthUser == null || $request->AuthPass == null || $request->RequestId == null || $request->Msisdn == null || $request->CountryId == null || $request->OpId == null || $request->ServiceId == null || $request->ProductId == null || $request->La == null || $request->MtText == null || $request->Purpose == null) {
            $data['response'] = 'fail';
            $data['message']    = "Invalid Partner Product";
            return json_encode($data);
        } else {
            $data["AuthUser"] = TIMWE_AuthUser;
            $data["AuthPass"] = TIMWE_AuthPass;
            $data["RequestId"] = $request->RequestId;
            $data["msisdn"] = $request->Msisdn;
            $data["CountryId"] = $request->CountryId;
            $data["OpId"] = $request->OpId;
            $data["ServiceId"] = $request->ServiceId;
            $data["ProductId"] = $request->ProductId;
            $data["La"] = $request->La;
            $data["MtText"] = $request->MtText;
            $data["Purpose"] = $request->Purpose;


            $subscriber = Subscriber::select('subscribers.*', 'activation.msisdn', 'activation.plan', 'activation.serviceid', 'activation.price')
                ->join('activation', 'activation.id', '=', 'subscribers.activation_id');

            $subscriber = $subscriber->where('msisdn', $request->Msisdn);
            if ($request->has('ProductId') && $request->ProductId != '') {
                $response = [];
                $service = Service::find($request->ProductId);
                $subscriber = $subscriber->where('serviceid', $service->title);
            }
            $subscriber = $subscriber->first();
            if ($subscriber) {
                $product['id'] =    $service->id;
                $product['la'] =   TIMWE_SHORTCODE;
                $product['subId'] = $subscriber->id;
                $product['subStatus'] = "ACTIVE";
                $product['subscriptionDate'] = $subscriber->created_at->format('d-M-Y h:i'); //"24-Jan-2019 12:20"
            }
            $plan = $subscriber->plan;
            switch ($plan) {
                case 'daily':
                    $product['billingPeriod'] = 1;
                    break;
                case 'weekly':
                    $product['billingPeriod'] = 7;
                    break;
            }
            $product['billingAmount'] = $subscriber->price;
            $responseObj['response'] = $response;
            $response['msidn'] = $request->Msisdn;
            $response['opId'] = $request->OpId ?? "268";
            $response['responseStatus']['code'] = "1";
            $response['responseStatus']['description'] = "success";
            $service_arr['id'] = "1";
            $service_arr['product'] = [$product];
            $responseObj['response'] = $response;
            if (isset($service)) {
                $responseObj['service'] = [$service_arr];
            }
            
            $actionName = 'SendMt';
            $URL = $request->fullUrl();
            $this->log($actionName, $URL, $responseObj);
            return json_encode($responseObj);
        }
    }

    public function du_message_send($Msisdn,$MtText)
    {
        // Du sending welcome message
        //$URL = "http://41.33.167.14:2080/~smsdu/du_send_message";
        $URL = "";
        $param = "phone_number=$Msisdn&message=$MtText";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
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
