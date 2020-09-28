<?php

namespace App\Http\Controllers\Api;

use App\Service;
use App\Subscriber;
use App\Unsubscriber;
use Illuminate\Http\Request;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use stdClass;

class TimweController
{

    // misidn sub and unsub
    //localhost:8080/du_system/api/inquiry?AuthUser=IVAS_CCT&AuthPass=CCT_2020_981&Msisdn=971555802322

    //services list
    //localhost:8080/du_system/api/inquiry?AuthUser=IVAS_CCT&AuthPass=CCT_2020_981

    //from to date
    //localhost:8080/du_system/api/inquiry?AuthUser=IVAS_CCT&AuthPass=CCT_2020_981&Msisdn=971586799659&FromDate=5-May-2020%2008:16&ToDate=7-May-2020%2008:16

    public function inquiry(Request $request)
    {

        /*
        checkMsisdnrequest() {
            checkfromdate()
            checktodate()
            checksub()
            checkunsub()
            returnnotexist()
        }
        all_services()
        */

        $AuthUser = $request->AuthUser;
        $AuthPass = $request->AuthPass;
        $subscribers = Subscriber::select('subscribers.*', 'activation.msisdn', 'activation.plan', 'activation.serviceid', 'activation.price')->join('activation', 'activation.id', '=', 'subscribers.activation_id');
        $unsubscribers = Unsubscriber::select('unsubscribers.*', 'activation.msisdn', 'activation.plan', 'activation.serviceid', 'activation.price')->join('activation', 'activation.id', '=', 'unsubscribers.activation_id');
        if ($AuthUser == TIMWE_AuthUser && $AuthPass == TIMWE_AuthPass) {

            $param['RequestId'] = $request->RequestId;

            if ($request->has('Msisdn') && $request->Msisdn != '') {
                $param['Msisdn'] = $request->Msisdn;
                $response['msisdn'] = $request->Msisdn;

                $subscribers = $subscribers->where('msisdn', $request->Msisdn);
                $unsubscribers = $unsubscribers->where('msisdn', $request->Msisdn);

                if ($request->has('FromDate') && $request->FromDate != '') {
                    $FromDate = date("Y-m-d H:i:s", strtotime($request->FromDate));
                    $subscribers = $subscribers->where('subscribe_date', ">=", $FromDate);
                    $unsubscribers = $unsubscribers->where('unsubscribers.created_at', ">=", $FromDate);
                }

                if ($request->has('ToDate') && $request->ToDate != '') {
                    $ToDate = date("Y-m-d H:i:s", strtotime($request->ToDate));
                    $subscribers = $subscribers->where('subscribe_date', "<=", $ToDate);
                    $unsubscribers = $unsubscribers->where('unsubscribers.created_at', "<=", $ToDate);
                }

                $subscribers = $subscribers->get();
                $unsubscribers = $unsubscribers->get();

                $param['OpId'] = $request->OpId ?? "268";
                $response['opId'] = $request->OpId ?? "268";
                $i = 0;
                if ($subscribers->count() > 0 || $unsubscribers->count() > 0) {
                    $response['responseStatus']['code'] = "1";
                    $response['responseStatus']['description'] = "success";
                    $service['id'] = SERVICE_ID;
                    $service['name'] = SERVICE_NAME;

                    foreach ($subscribers as $subscriber) {
                        $service_name = $subscriber->serviceid;
                        $service_id = Service::where('title', 'LIKE', "%$service_name%")->first()->id;

                        $product[$i]['id'] = $service_id;
                        $product[$i]['type'] = PRODUCT_TYPE; // subscription
                        $product[$i]['name'] = $subscriber->serviceid;
                        $product[$i]['la'] = TIMWE_SHORTCODE;
                        $product[$i]['subId'] = $subscriber->id;

                        $product[$i]['subStatus'] = "ACTIVE";
                        $product[$i]['subscriptionDate'] = $subscriber->created_at->format('d-M-Y h:i'); //"24-Jan-2019 12:20"

                        $plan = $subscriber->plan;
                        switch ($plan) {
                            case 'daily':
                                $product[$i]['billingPeriod'] = 1;
                                break;
                            case 'weekly':
                                $product[$i]['billingPeriod'] = 7;
                                break;
                        }
                        $product[$i]['billingAmount'] = $subscriber->price;
                        $product[$i]['messageMode'] = "DIRECT BILLING";
                        $product[$i]['serviceActivationMode'] = "SMS";
                        $product[$i]['additionalDetails'] = new stdClass();

                        $i++;
                    }

                    foreach ($unsubscribers as $unsubscriber) {
                        $service_name = $unsubscriber->serviceid;
                        $service_id = Service::where('title', 'LIKE', "%$service_name%")->first()->id;

                        $product[$i]['id'] = $service_id;
                        $product[$i]['type'] = PRODUCT_TYPE; // subscription
                        $product[$i]['name'] = $unsubscriber->serviceid;
                        $product[$i]['la'] = TIMWE_SHORTCODE;
                        $product[$i]['subId'] = $unsubscriber->id;

                        $product[$i]['subStatus'] = "CANCELLED";
                        $product[$i]['subscriptionDate'] = $unsubscriber->activation->created_at->format('d-M-Y h:i'); //"24-Jan-2019 12:20"
                        $product[$i]['unsubscriptionDate'] = $unsubscriber->created_at->format('d-M-Y h:i'); //"24-Jan-2019 12:20"

                        $plan = $unsubscriber->plan;
                        switch ($plan) {
                            case 'daily':
                                $product[$i]['billingPeriod'] = 1;
                                break;
                            case 'weekly':
                                $product[$i]['billingPeriod'] = 7;
                                break;
                        }
                        $product[$i]['billingAmount'] = $unsubscriber->price;
                        $product[$i]['messageMode'] = "DIRECT BILLING";
                        $product[$i]['serviceActivationMode'] = "SMS";
                        $product[$i]    ['additionalDetails'] = new stdClass();

                        $i++;
                    }
                    $service['product'] = $product;

                } else {
                    $response['responseStatus']['code'] = "-80";
                    $response['responseStatus']['description'] = "customer not exists";
                }

                $responseObj['response'] = $response;
                if (isset($service)) {
                    $responseObj['service'] = [$service];
                }

            } else {
                $i = 0;

                $services = Service::whereIn('title', ACTIVE_SERVICES)->get();

                foreach ($services as $service) {
                    $product[$i]['id'] = $service->id;
                    $product[$i]['name'] = $service->title;
                    $product[$i]['la'] = TIMWE_SHORTCODE;
                    $product[$i]['type'] = PRODUCT_TYPE;
                    $product[$i]['billingPeriod'] = "1";
                    $product[$i]['billingAmount'] = "2";
                    $i++;
                }

                $response['responseStatus']['code'] = "1";
                $response['responseStatus']['description'] = "success";
                $service1['id'] = SERVICE_ID;
                $service1['name'] = SERVICE_NAME;
                $service1['product'] = [$product];

                $responseObj['response'] = $response;
                $responseObj['service'] = [$service1];

            }

            $actionName = 'CCT Inquery';
            $URL = $request->fullUrl();

            $this->log($actionName, $URL, $responseObj);
            return json_encode($responseObj);

        }
    }

    //localhost:8080/du_system/api/unsubscribe?AuthUser=IVAS_CCT&AuthPass=CCT_2020_981&Msisdn=971555802322

    public function unsubscribe(Request $request)
    {
        $AuthUser = $request->AuthUser;
        $AuthPass = $request->AuthPass;
        if ($AuthUser == TIMWE_AuthUser && $AuthPass == TIMWE_AuthPass) {

            $param['RequestId'] = $request->RequestId;

            if ($request->has('Msisdn') && $request->Msisdn != '') {

                $subscriber = Subscriber::select('subscribers.*', 'activation.msisdn', 'activation.plan', 'activation.serviceid', 'activation.price')->join('activation', 'activation.id', '=', 'subscribers.activation_id');

                $response['msisdn'] = $request->Msisdn;
                $response['opId'] = $request->OpId ?? "268";

                $subscriber = $subscriber->where('msisdn', $request->Msisdn);

                if ($request->has('ProductId') && $request->ProductId != '') {

                    $service_id = Service::find($request->ProductId)->id;

                    $subscriber = $subscriber->where('serviceid', $service->title);

                }

                $subscriber = $subscriber->get();

                if ($subscriber->count() > 0) {
                    $i = 0;

                    $response['responseStatus']['code'] = "1";
                    $response['responseStatus']['description'] = "success";

                    $service['id'] = SERVICE_ID;
                    foreach ($subscriber as $sub) {
                        //unsub
                        $unsubscriber['activation_id'] = $sub->activation_id;

                        $services_id = Service::where('title', $sub->serviceid)->first();

                        $product[$i]['id'] = $services_id->id;
                        $product[$i]['name'] = $sub->serviceid;
                        $product[$i]['la'] = TIMWE_SHORTCODE;
                        $product[$i]['type'] = PRODUCT_TYPE; // subscription
                        $product[$i]['subId'] = $sub->id;
                        $product[$i]['subStatus'] = "CANCELLED";
                        $product[$i]['subscriptionDate'] = date("d-M-Y h:i", strtotime($sub->subscribe_date)); //"24-Jan-2019 12:20"
                        $sub->delete();
                        $unsubscriber_id = Unsubscriber::create($unsubscriber);
                        $product[$i]['unsubscriptionDate'] = $unsubscriber_id->created_at->format('d-M-Y h:i'); //"24-Jan-2019 12:20"
                        $product[$i]['serviceActivationMode'] = "SMS";
                        $i++;

                    }
                    $service['product'] = $product;
                } else {

                    $response['responseStatus']['code'] = "-77";
                    $response['responseStatus']['description'] = "sub not active";

                }

            }

            $responseObj['response'] = $response;
            if (isset($service)) {
                $responseObj['service'] = [$service];
            }

            $actionName = 'CCT Unsubscribe';
            $URL = $request->fullUrl();

            $this->log($actionName, $URL, $responseObj);
            return json_encode($responseObj);
        }

    }
    //localhost:8080/du_system/api/userhistory?AuthUser=IVAS_CCT&AuthPass=CCT_2020_981&Msisdn=971555802322

    public function userhistory(Request $request)
    {

        /*
        get subscriber() => get charging by sub_id  (join sub on activation )
        get Mo by msisdn
        all messages by msidn()  = MT
        */

        $AuthUser = $request->AuthUser;
        $AuthPass = $request->AuthPass;
        if ($AuthUser == TIMWE_AuthUser && $AuthPass == TIMWE_AuthPass) {

            $subscribers = Subscriber::select('subscribers.*', 'activation.msisdn', 'activation.plan', 'activation.serviceid', 'activation.price')
                ->join('activation', 'activation.id', '=', 'subscribers.activation_id');

            if ($request->has('Msisdn') && $request->Msisdn != '') {

                $service['id'] = SERVICE_ID;

                if ($request->has('FromDate') && $request->FromDate != '') {
                    $FromDate = date("Y-m-d H:i:s", strtotime($request->FromDate));
                    $subscribers = $subscribers->where('subscribe_date', ">=", $FromDate);
                }

                if ($request->has('ToDate') && $request->ToDate != '') {
                    $ToDate = date("Y-m-d H:i:s", strtotime($request->ToDate));
                    $subscribers = $subscribers->where('subscribe_date', "<=", $ToDate);
                }

                $subscribers = $subscribers->where('msisdn', $request->Msisdn)->get();

                if ($subscribers->count() > 0) {
                    $i = 0;
                    foreach ($subscribers as $subscriber) {
                        $services_id = Service::where('title', $subscriber->serviceid)->first();

                        $mos = $subscriber->mos; //date
                        $mts = $subscriber->mts;
                        $charges = $subscriber->charges; //filter date

                        if ($request->has('FromDate') && $request->FromDate != '') {
                            $FromDate = date("Y-m-d H:i:s", strtotime($request->FromDate));
                            $mos = $mos->where('created_at', ">=", $FromDate)->take(100);
                            $mts = $mts->where('created_at', ">=", $FromDate)->take(100);
                            $charges = $charges->where('created_at', ">=", $FromDate)->take(100);
                        }

                        if ($request->has('ToDate') && $request->ToDate != '') {
                            $ToDate = date("Y-m-d H:i:s", strtotime($request->ToDate));
                            $mos = $mos->where('created_at', "<=", $ToDate)->take(100);
                            $mts = $mts->where('created_at', "<=", $ToDate)->take(100);
                            $charges = $charges->where('created_at', "<=", $ToDate)->take(100);

                        }

                        foreach ($mos as $mo) {

                            $product[$i]['productId'] = $services_id->id;
                            $product[$i]['productName'] = $subscriber->serviceid;
                            $product[$i]['userLa'] = TIMWE_SHORTCODE;
                            $product[$i]['userMessage'] = $mo->message;
                            $product[$i]['systemResponse'] = "";
                            $product[$i]['systemLa'] = TIMWE_SHORTCODE;
                            $product[$i]['billableAction'] = "no";
                            $product[$i]['billingAmount'] = "";
                            $product[$i]['userMessageDate'] = $mo->created_at->format('d-M-Y h:i'); //"24-Jan-2019 12:20"
                            $product[$i]['systemResponseDate'] = ""; //"24-Jan-2019 12:20"
                            $product[$i]['direction'] = "MO";

                            $i++;

                        }

                        foreach ($mts as $mt) {

                            $product[$i]['productId'] = $services_id->id;
                            $product[$i]['productName'] = $subscriber->serviceid;
                            $product[$i]['userLa'] = TIMWE_SHORTCODE;
                            $product[$i]['userMessage'] = "";
                            $product[$i]['systemResponse'] = $mt->message;
                            $product[$i]['systemLa'] = TIMWE_SHORTCODE;
                            $product[$i]['billableAction'] = "no";
                            $product[$i]['billingAmount'] = "";
                            $product[$i]['userMessageDate'] = ""; //"24-Jan-2019 12:20"
                            $product[$i]['systemResponseDate'] = $mt->created_at->format('d-M-Y h:i'); //"24-Jan-2019 12:20"
                            $product[$i]['direction'] = "MT";

                            $i++;
                        }

                        foreach ($charges as $charge) {

                            $product[$i]['productId'] = $services_id->id;
                            $product[$i]['productName'] = $subscriber->serviceid;
                            $product[$i]['userLa'] = TIMWE_SHORTCODE;
                            $product[$i]['userMessage'] = "";
                            $product[$i]['systemResponse'] = "";
                            $product[$i]['systemLa'] = TIMWE_SHORTCODE;
                            $product[$i]['billableAction'] = "Yes";
                            $product[$i]['billingAmount'] = "2";
                            $product[$i]['userMessageDate'] = ""; //"24-Jan-2019 12:20"
                            $product[$i]['systemResponseDate'] = $charge->created_at->format('d-M-Y h:i'); //"24-Jan-2019 12:20"
                            $product[$i]['direction'] = "DIRECT BILLING";

                            $i++;
                        }

                    }
                    if (isset($product)) {
                        $service['product'] = $product;
                    }

                    //if found
                    $response['msisdn'] = $request->Msisdn;
                    $response['opId'] = $request->OpId ?? "268";

                    $response['responseStatus']['code'] = "1";
                    $response['responseStatus']['description'] = "success";
                    $responseObj['response'] = $response;
                    if (isset($service)) {
                        $responseObj['service'] = [$service];
                    }
                } else {
                    $response['msisdn'] = $request->Msisdn;
                    $response['opId'] = $request->OpId ?? "268";

                    $response['responseStatus']['code'] = "1";
                    $response['responseStatus']['description'] = "success";
                    $responseObj['response'] = $response;
                }

            }

            return json_encode($responseObj);
        }

    }

    //localhost:8080/du_system/api/sendmt?AuthUser=IVAS_CCT&AuthPass=CCT_2020_981&Msisdn=971554350230&MtText=test&ProductId=5

    public function sendmt(Request $request)
    {

        $AuthUser = $request->AuthUser;
        $AuthPass = $request->AuthPass;

        if ($AuthUser == TIMWE_AuthUser && $AuthPass == TIMWE_AuthPass) {

            $subscriber = Subscriber::select('subscribers.*', 'activation.msisdn', 'activation.plan', 'activation.serviceid', 'activation.price')
                ->join('activation', 'activation.id', '=', 'subscribers.activation_id');

            $response['msisdn'] = $request->Msisdn;
            $response['opId'] = $request->OpId ?? "268";

            if ($request->has('Msisdn') && $request->Msisdn != '' && $request->has('MtText') && $request->MtText != '') {

                $subscriber = $subscriber->where('msisdn', $request->Msisdn);

                if ($request->has('ProductId') && $request->ProductId != '') {

                    $service = Service::find($request->ProductId);

                    if ($service) {

                        if (in_array($service->title, ACTIVE_SERVICES)) {
                            $subscriber = $subscriber->where('serviceid', $service->title);
                            $subscriber = $subscriber->first();
                            if ($subscriber) {

                                $response['responseStatus']['code'] = "1";
                                $response['responseStatus']['description'] = "success";

                                $service_id = Service::where('title', $subscriber->serviceid)->first();

                                $product['id'] = $service_id->id;
                                $product['la'] = TIMWE_SHORTCODE;
                                $product['subId'] = $subscriber->id;
                                $product['mtId'] = $subscriber->mts->first()->id;
                                $product['subStatus'] = "ACTIVE";
                                $product['subscriptionDate'] = $subscriber->created_at->format('d-M-Y h:i'); //"24-Jan-2019 12:20"

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
                                $service_arr['id'] = SERVICE_ID;
                                $service_arr['product'] = [$product];

                                // $this->du_message_send($request->Msisdn, $request->MtText);

                            } else {

                                $response['responseStatus']['code'] = "-77";
                                $response['responseStatus']['description'] = "sub not active";

                            }
                        }
                    } else {

                        $response['responseStatus']['code'] = "-77";
                        $response['responseStatus']['description'] = "sub not active";

                    }
                }
                $responseObj['response'] = $response;

                if (isset($service_arr)) {
                    $responseObj['service'] = [$service_arr];
                }

                $actionName = 'SendMt';
                $URL = $request->fullUrl();
                $this->log($actionName, $URL, $responseObj);

                return json_encode($responseObj);
            }
        }
    }

    public function du_message_send($Msisdn, $MtText)
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
        $year = date("Y");
        $month = date("m");
        $day = date("d");
        $log = new Logger($actionName);
        $path = storage_path("logs/$year/$month/$day/$actionName");
        // to create new folder with current date  // if folder is not found create new one
        if (!\File::exists($path)) {
            \File::makeDirectory($path, 0775, true, true);
        }

        $log->pushHandler(new StreamHandler(storage_path("logs/$year/$month/$day/$actionName/logFile.log", Logger::INFO)));
        $log->addInfo($URL, $parameters_arr);
    }

}
