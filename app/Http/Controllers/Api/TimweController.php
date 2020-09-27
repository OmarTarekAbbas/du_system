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
    //http://localhost:8080/du_system/api/inquiry?AuthUser=IVAS&AuthPass=123456&Msisdn=971555802322

    //services list
    //http://localhost:8080/du_system/api/inquiry?AuthUser=IVAS&AuthPass=123456

    //from to date
    //http://localhost:8080/du_system/api/inquiry?AuthUser=IVAS&AuthPass=123456&Msisdn=971586799659&FromDate=5-May-2020%2008:16&ToDate=7-May-2020%2008:16

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
        $subscriber = Subscriber::select('subscribers.*', 'activation.msisdn', 'activation.plan', 'activation.serviceid', 'activation.price')->join('activation', 'activation.id', '=', 'subscribers.activation_id');
        $unsubscriber = Unsubscriber::select('unsubscribers.*', 'activation.msisdn', 'activation.plan', 'activation.serviceid', 'activation.price')->join('activation', 'activation.id', '=', 'unsubscribers.activation_id');
        if ($AuthUser == TIMWE_AuthUser && $AuthPass == TIMWE_AuthPass) {

            $param['RequestId'] = $request->RequestId;

            if ($request->has('Msisdn') && $request->Msisdn != '') {
                $param['Msisdn'] = $request->Msisdn;
                $response['msisdn'] = $request->Msisdn;

                $subscriber = $subscriber->where('msisdn', $request->Msisdn);
                $unsubscriber = $unsubscriber->where('msisdn', $request->Msisdn);

                if($request->has('FromDate') && $request->FromDate != ''){
                    $FromDate  = date("Y-m-d H:i:s",strtotime($request->FromDate)) ;
                    $subscriber->where('subscribe_date',">=", $FromDate) ;
                    $unsubscriber->where('unsubscribers.created_at',">=", $FromDate) ;
                }

                if($request->has('ToDate') && $request->ToDate != ''){
                    $ToDate = date("Y-m-d H:i:s",strtotime($request->ToDate)) ;
                    $subscriber->where('subscribe_date',"<=", $ToDate) ;
                    $unsubscriber->where('unsubscribers.created_at',"<=", $ToDate) ;
                }

                $subscriber = $subscriber->first();
                $unsubscriber = $unsubscriber->first();

                $param['OpId'] = $request->OpId ?? "268";
                $response['opId'] = $request->OpId ?? "268";

                if ($subscriber || $unsubscriber) {

                    $service_name = $subscriber->serviceid ?? $unsubscriber->serviceid;
                    $service_id = Service::where('title', 'LIKE', "%$service_name%")->first()->id;

                    $product['id'] = $service_id;
                    $product['type'] = "Brokerage"; // subscription
                    $product['name'] = $subscriber->serviceid ?? $unsubscriber->serviceid;
                    $product['la'] = "4971";
                    $product['subId'] = $subscriber->id ?? $unsubscriber->id;

                    if ($subscriber) {
                        $product['subStatus'] = "ACTIVE";
                        $product['subscriptionDate'] = $subscriber->created_at->format('d-M-Y h:i'); //"24-Jan-2019 12:20"
                    } elseif ($unsubscriber) {
                        $product['subStatus'] = "CANCELLED";
                        $product['subscriptionDate'] = $unsubscriber->activation->created_at->format('d-M-Y h:i'); //"24-Jan-2019 12:20"
                        $product['unsubscriptionDate'] = $unsubscriber->created_at->format('d-M-Y h:i'); //"24-Jan-2019 12:20"
                    }

                    $plan = $subscriber->plan ?? $unsubscriber->plan;
                    switch ($plan) {
                        case 'daily':
                            $product['billingPeriod'] = 1;
                            break;
                        case 'weekly':
                            $product['billingPeriod'] = 7;
                            break;
                    }
                    $product['billingAmount'] = $subscriber->price ?? $unsubscriber->price;
                    $product['messageMode'] = "DIRECT BILLING";
                    $product['serviceActivationMode'] = "SMS";
                    $product['additionalDetails'] = new stdClass();

                    $response['responseStatus']['code'] = "1";
                    $response['responseStatus']['description'] = "success";
                    $service['id'] = "1";
                    $service['name'] = "IVAS";
                    $service['product'] = [$product];

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

                foreach($services as $service){
                    $product[$i]['id'] = $service->id;
                    $product[$i]['name'] = $service->title;
                    $product[$i]['la'] = "4971";
                    $product[$i]['type'] = "Brokerage";
                    $product[$i]['billingPeriod'] = "1";
                    $product[$i]['billingAmount'] = "2";
                    $i++;
                }

                $response['responseStatus']['code'] = "1";
                $response['responseStatus']['description'] = "success";
                $service1['id'] = "1";
                $service1['name'] = "IVAS";
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

    //http://localhost:8080/du_system/api/inquiry?AuthUser=IVAS&AuthPass=123456&Msisdn=971555802322

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

                if($request->has('ProductId') && $request->ProductId != ''){

                    $response['ProductId'] = $request->ProductId;

                    $service_id = Service::find($response['ProductId'])->id;

                    $subscriber = $subscriber->where('serviceid', $service->title);

                }

                $subscriber = $subscriber->get();

                if ($subscriber->count() > 0) {
                    $i = 0;

                    $response['responseStatus']['code'] = "1";
                    $response['responseStatus']['description'] = "success";

                    $service['id'] = "1";
                    foreach($subscriber as $sub){
                        //unsub
                        $unsubscriber['activation_id'] = $sub->activation_id;

                        $services_id = Service::where('title', $sub->serviceid)->first();

                        $product[$i]['id'] = $services_id->id;
                        $product[$i]['name'] = $sub->serviceid;
                        $product[$i]['la'] = "4971";
                        $product[$i]['type'] = "Brokerage"; // subscription
                        $product[$i]['subId'] = $sub->id;
                        $product[$i]['subStatus'] = "CANCELLED";
                        $product[$i]['subscriptionDate'] = date("d-M-Y h:i",strtotime($sub->subscribe_date)); //"24-Jan-2019 12:20"
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
            if(isset($service))
            $responseObj['service'] = [$service];

            $actionName = 'CCT Unsubscribe';
            $URL = $request->fullUrl();

            $this->log($actionName, $URL, $responseObj);
            return json_encode($responseObj);
        }

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
