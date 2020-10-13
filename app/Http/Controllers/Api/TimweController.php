<?php

namespace App\Http\Controllers\Api;

use stdClass;
use App\Service;
use App\Activation;
use App\LogMessage;
use App\DuMo;
use App\Subscriber;
use Monolog\Logger;
use App\Unsubscriber;
use App\Timwecct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Monolog\Handler\StreamHandler;

class TimweController
{

    public function OpId($request, $actionName)
    {
        if ($request->has('OpId') && $request->OpId != OpId) {
            $response['responseStatus']['code'] = "-2";
            $response['responseStatus']['description'] = "Invalid Operator";
            $responseObj['response'] = $response;

            $actionName = $actionName;
            $URL = $request->fullUrl();
            $this->log($actionName, $URL, $responseObj);
            $responseObj = json_encode($responseObj);
            return $responseObj;
        } // check OpId
    }

    public function ServiceId($request, $actionName)
    {
        if ($request->has('ServiceId') && $request->ServiceId != SERVICE_ID) {
            $response['responseStatus']['code'] = "-58";
            $response['responseStatus']['description'] = "Invalid Service";
            $responseObj['response'] = $response;

            $actionName = $actionName;
            $URL = $request->fullUrl();
            $this->log($actionName, $URL, $responseObj);
            $responseObj = json_encode($responseObj);
            return $responseObj;
        } // check OpId
    }

    public function ProductId($request, $actionName)
    {
        if ($request->has('ProductId') && !in_array($request->ProductId, ProductId)) {
            $response['responseStatus']['code'] = "-6";
            $response['responseStatus']['description'] = "Invalid Partner Product";
            $responseObj['response'] = $response;

            $actionName = $actionName;
            $URL = $request->fullUrl();
            $this->log($actionName, $URL, $responseObj);
            $responseObj = json_encode($responseObj);
            return $responseObj;
        } // check ProductId
    }


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
        if ($AuthUser == TIMWE_AuthUser && $AuthPass == TIMWE_AuthPass) { // check auth

            $check_op = $this->OpId($request, 'CCT Inquery');
            if($check_op != ""){
                return $check_op;
            }
            $check_service = $this->ServiceId($request, 'CCT Inquery');
            if($check_service != ""){
                return $check_service;
            }
            $check_product = $this->ProductId($request, 'CCT Inquery');
            if($check_product != ""){
                return $check_product;
            }

            if ($request->has('Msisdn') && $request->Msisdn != '') { // check if msisdn

                $i = 0;
                $s = 0;

                $services_arr = ACTIVE_SERVICES;

                if ($request->has('ProductId') && $request->ProductId != '') {

                    if(in_array( $request->ProductId, ProductId )){
                        $service_id = Service::where('id', $request->ProductId)->first()->title;
                        $services_arr = array($service_id) ;
                    }
                }

                foreach($services_arr as $key => $value){
                    $subscribers = Subscriber::select('subscribers.*', 'activation.msisdn', 'activation.plan', 'activation.serviceid', 'activation.price','activation.created_at AS act_created')->join('activation', 'activation.id', '=', 'subscribers.activation_id');
                    $unsubscribers = Unsubscriber::select('unsubscribers.*', 'activation.msisdn', 'activation.plan', 'activation.serviceid', 'activation.price')->join('activation', 'activation.id', '=', 'unsubscribers.activation_id');

                    $response['msisdn'] = $request->Msisdn;
                    $response['opId'] = $request->OpId ?? "268";

                    $subscribers = $subscribers->where('msisdn', $request->Msisdn);
                    $unsubscribers = $unsubscribers->where('msisdn', $request->Msisdn);

                    if ($request->has('FromDate') && $request->FromDate != '') {
                        $FromDate = date("Y-m-d H:i:s", strtotime($request->FromDate));
                        $subscribers = $subscribers->where('subscribe_date', ">=", $FromDate);
                        $unsubscribers = $unsubscribers->where('unsubscribers.created_at', ">=", $FromDate);
                    } // check from date

                    if ($request->has('ToDate') && $request->ToDate != '') {
                        $ToDate = date("Y-m-d H:i:s", strtotime($request->ToDate));
                        $subscribers = $subscribers->where('subscribe_date', "<=", $ToDate);
                        $unsubscribers = $unsubscribers->where('unsubscribers.created_at', "<=", $ToDate);
                    } // check to date



                    $subscriber = $subscribers->where('serviceid', $value)->latest()->first();
                    $unsubscriber = $unsubscribers->where('serviceid', $value)->latest()->first();


                    if ($subscriber || $unsubscriber) {
                        $response['responseStatus']['code'] = "1";
                        $response['responseStatus']['description'] = "success";
                        $service['id'] = SERVICE_ID;
                        $service['name'] = SERVICE_NAME;

                        if ($subscriber) {
                            $service_name = $subscriber->serviceid;
                            $service_id = Service::where('title', 'LIKE', "%$service_name%")->first()->id;

                            $product[$i]['id'] = (string)$service_id;
                            $product[$i]['type'] = PRODUCT_TYPE; // subscription
                            $product[$i]['name'] = $subscriber->serviceid;
                            $product[$i]['la'] = TIMWE_SHORTCODE;
                            $product[$i]['subId'] = (string)$subscriber->activation_id;

                            $product[$i]['subStatus'] = "ACTIVE";
                            $product[$i]['subscriptionDate'] =   date("d-M-Y h:i",strtotime($subscriber->act_created))     ; //   $subscriber->act_created->format('d-M-Y h:i'); //"24-Jan-2019 12:20"

                            $plan = $subscriber->plan;
                            switch ($plan) {
                                case 'daily':
                                    $product[$i]['billingPeriod'] = (string)1;
                                    break;
                                case 'weekly':
                                    $product[$i]['billingPeriod'] = (string)7;
                                    break;
                            }
                            $product[$i]['billingAmount'] = $subscriber->price;
                            $product[$i]['messageMode'] = "DIRECT BILLING";
                            $product[$i]['serviceActivationMode'] = "SMS";
                            $product[$i]['additionalDetails'] = new stdClass();

                            $i++;
                        } // sub foreach

                        if ($unsubscriber) {
                            $service_name = $unsubscriber->serviceid;
                            $service_id = Service::where('title', 'LIKE', "%$service_name%")->first()->id;

                            $product[$i]['id'] = (string)$service_id;
                            $product[$i]['type'] = PRODUCT_TYPE; // subscription
                            $product[$i]['name'] = $unsubscriber->serviceid;
                            $product[$i]['la'] = TIMWE_SHORTCODE;
                            $product[$i]['subId'] = (string)$unsubscriber->activation_id;

                            $product[$i]['subStatus'] = "CANCELED";
                            $product[$i]['subscriptionDate'] = $unsubscriber->activation->created_at->format('d-M-Y h:i'); //"24-Jan-2019 12:20"
                            $product[$i]['unsubscriptionDate'] = $unsubscriber->created_at->format('d-M-Y h:i'); //"24-Jan-2019 12:20"

                            $plan = $unsubscriber->plan;
                            switch ($plan) {
                                case 'daily':
                                    $product[$i]['billingPeriod'] = (string)1;
                                    break;
                                case 'weekly':
                                    $product[$i]['billingPeriod'] = (string)7;
                                    break;
                            }
                            $product[$i]['billingAmount'] = $unsubscriber->price;
                            $product[$i]['messageMode'] = "DIRECT BILLING";
                            $product[$i]['serviceActivationMode'] = "SMS";
                            $product[$i]['additionalDetails'] = new stdClass();

                            $i++;
                        } // unsub foreach
                        // $s++;
                    } else { // check sub or unsub found
                        $response['responseStatus']['code'] = "-80";
                        $response['responseStatus']['description'] = "customer not exists";
                    }

                        // echo($service_sn);
                }

            } else { // check if msisdn not null
                $i = 0;

                $services = Service::whereIn('title', ACTIVE_SERVICES)->get();

                foreach ($services as $ser) {
                    $product[$i]['id'] = $ser->id;
                    $product[$i]['name'] = $ser->title;
                    $product[$i]['la'] = TIMWE_SHORTCODE;
                    $product[$i]['type'] = PRODUCT_TYPE;
                    $product[$i]['billingPeriod'] = "1";
                    $product[$i]['billingAmount'] = "2";
                    $i++;
                } // service foreach

                $response['responseStatus']['code'] = "1";
                $response['responseStatus']['description'] = "success";
                $service['id'] = SERVICE_ID;
                $service['name'] = SERVICE_NAME;

            }

        } else { // Auth check
            $response['responseStatus']['code'] = "-3";
            $response['responseStatus']['description'] = "Invalid Credentials";
        }
        $responseObj['response'] = $response;
        if (isset($product)) // check if product is set
        {
            $service['product'] = $product;
        }

        if (isset($service)) // check if service is set
        {
            $responseObj['response']['service'] = [$service];
        }

        $actionName = 'CCT Inquery';
        $URL = $request->fullUrl();
        $this->log($actionName, $URL, $responseObj);
        $timwecct = new Timwecct();
        $timwecct->request_link = $URL;
        $timwecct->response = json_encode($responseObj);
        $timwecct->save();
        return json_encode($responseObj);
    }

    //localhost:8080/du_system/api/unsubscribe?AuthUser=IVAS_CCT&AuthPass=CCT_2020_981&Msisdn=971555802322

    public function unsubscribe(Request $request)
    {
        $AuthUser = $request->AuthUser;
        $AuthPass = $request->AuthPass;
        if ($AuthUser == TIMWE_AuthUser && $AuthPass == TIMWE_AuthPass) {

            $check_op = $this->OpId($request, 'CCT Unsubscribe');
            if($check_op != ""){
                return $check_op;
            }
            $check_service = $this->ServiceId($request, 'CCT Unsubscribe');
            if($check_service != ""){
                return $check_service;
            }
            $check_product = $this->ProductId($request, 'CCT Unsubscribe');
            if($check_product != ""){
                return $check_product;
            }

            if ($request->has('Msisdn') && $request->Msisdn != '') {

                $subscriber = Subscriber::select('subscribers.*', 'activation.msisdn', 'activation.plan', 'activation.serviceid', 'activation.price')->join('activation', 'activation.id', '=', 'subscribers.activation_id');

                $response['msisdn'] = $request->Msisdn;
                $response['opId'] = $request->OpId ?? "268";

                $subscriber = $subscriber->where('msisdn', $request->Msisdn);

                if ($request->has('ProductId') && $request->ProductId != '') {

                    $service_id = Service::find($request->ProductId)->title;

                    $subscriber = $subscriber->where('serviceid', $service_id);

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
                        $product[$i]['subId'] = $sub->activation_id;
                        $product[$i]['subStatus'] = "CANCELED";
                        $product[$i]['subscriptionDate'] = $sub->created_at->format('d-M-Y h:i'); //"24-Jan-2019 12:20"
                        $sub->delete();
                        $unsubscriber_id = Unsubscriber::create($unsubscriber);
                        $product[$i]['unsubscriptionDate'] = $unsubscriber_id->created_at->format('d-M-Y h:i'); //"24-Jan-2019 12:20"
                        $product[$i]['serviceActivationMode'] = "SMS";
                        $i++;

                        // here send unsub message to the user
                      $unsub_message = "You are unsub successfully from ".  $services_id->service ;
                      $message_type="cct_unsub_mt";
                     $LogMessage_id =      $this->du_message_send( $request->Msisdn,  $unsub_message, $sub->serviceid, $message_type);


                    }
                } else {

                    $response['responseStatus']['code'] = "-77";
                    $response['responseStatus']['description'] = "sub not active";

                }

            }

        } else { // Auth check
            $response['responseStatus']['code'] = "-3";
            $response['responseStatus']['description'] = "Invalid Credentials";
        }

        $responseObj['response'] = $response;
        if (isset($product)) // check if product is set
        {
            $service['product'] = $product;
        }

        if (isset($service)) // check if service is set
        {
            $responseObj['response']['service'] = [$service];
        }

        $actionName = 'CCT Unsubscribe';
        $URL = $request->fullUrl();
        $this->log($actionName, $URL, $responseObj);
        $timwecct = new Timwecct();
        $timwecct->request_link = $URL;
        $timwecct->response = json_encode($responseObj);
        $timwecct->save();
        return json_encode($responseObj);
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

            $check_op = $this->OpId($request, 'CCT User History');
            if($check_op != ""){
                return $check_op;
            }
            $check_service = $this->ServiceId($request, 'CCT User History');
            if($check_service != ""){
                return $check_service;
            }
            $check_product = $this->ProductId($request, 'CCT User History');
            if($check_product != ""){
                return $check_product;
            }

            $subscribers = Subscriber::select('subscribers.*', 'activation.msisdn', 'activation.plan', 'activation.serviceid', 'activation.price')
                ->join('activation', 'activation.id', '=', 'subscribers.activation_id');

            if ($request->has('Msisdn') && $request->Msisdn != '') {

                $service['id'] = SERVICE_ID;
                $service['name'] = SERVICE_NAME;



                if ($request->has('FromDate') && $request->FromDate != '') {
                    $FromDate = date("Y-m-d H:i:s", strtotime($request->FromDate));
                    $subscribers = $subscribers->where('subscribe_date', ">=", $FromDate);
                }

                if ($request->has('ToDate') && $request->ToDate != '') {
                    $ToDate = date("Y-m-d H:i:s", strtotime($request->ToDate));
                    $subscribers = $subscribers->where('subscribe_date', "<=", $ToDate);
                }

                $subscribers = $subscribers->where('msisdn', $request->Msisdn)->get();

                // limit sunscribers by specific service
                if ($request->has('ProductId') && $request->ProductId != '') {
                    if (in_array($request->ProductId , ProductId)) {
                        $service_fetch = Service::where('id', $request->ProductId)->first();
                        // $mts = $mts ->where('service', $service->title);
                        $subscribers = $subscribers->where('serviceid', $service_fetch->title) ;
                        $filter_by_product = 1 ;
                    }else{
                        $filter_by_product = 0 ;
                    }
                }else{
                    $filter_by_product = 0 ;
                }



                if ($subscribers->count() > 0) {
                    $i = 0;
                    foreach ($subscribers as $subscriber) {
                        $services_id = Service::where('title', $subscriber->serviceid)->first();

                        $mos = $subscriber->mos; //date
                        $mts = $subscriber->mts->where('service', $subscriber->serviceid);
                        $charges = $subscriber->charges; //filter date

                        // filetr MT
                        if( $filter_by_product){

                               $mts = $mts ->where('service', $service_fetch->title);
                        }

                        if ($request->has('FromDate') && $request->FromDate != '') {
                            $FromDate = date("Y-m-d H:i:s", strtotime($request->FromDate));
                            $mos = $mos->where('created_at', ">=", $FromDate);
                            $mts = $mts->where('created_at', ">=", $FromDate);
                            $charges = $charges->where('created_at', ">=", $FromDate);
                        }

                        if ($request->has('ToDate') && $request->ToDate != '') {
                            $ToDate = date("Y-m-d H:i:s", strtotime($request->ToDate));
                            $mos = $mos->where('created_at', "<=", $ToDate);
                            $mts = $mts->where('created_at', "<=", $ToDate);
                            $charges = $charges->where('created_at', "<=", $ToDate);
                        }

                        $mts = $mts->sortByDesc('created_at')->take(PAGINATION);
                        $mos = $mos->sortByDesc('created_at')->take(PAGINATION);
                        $charges = $charges->sortByDesc('created_at')->take(PAGINATION);







                        foreach ($mos as $mo) {

                            $product[$i]['productId'] = (string)$services_id->id;
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

                            $product[$i]['productId'] = (string)$services_id->id;
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

                            $product[$i]['productId'] = (string)$services_id->id;
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
                        $service['userRequest '] = $product;
                    }

                    //if found
                    $response['msisdn'] = $request->Msisdn;
                    $response['opId'] = $request->OpId ?? "268";

                    $response['responseStatus']['code'] = "1";
                    $response['responseStatus']['description'] = "success";
                    $responseObj['response'] = $response;
                    if (isset($service)) {
                        $responseObj['response']['service'] = [$service];
                    }
                } else {
                      $i = 0;
                       // check for insufficient balance for MO and charging history
                       $mos =    DuMo::where('msisdn', $request->Msisdn) ;
                       $mts =    LogMessage::where('msisdn', $request->Msisdn) ;
                       $activations =    Activation::where('msisdn', $request->Msisdn) ;


                       if ($request->has('ProductId') && $request->ProductId != '') {
                        if (in_array($request->ProductId , ProductId)) {
                            $service_fetch = Service::where('id', $request->ProductId)->first();
                            $mts = $mts ->where('service', $service_fetch->title);
                            $activations = $activations ->where('serviceid', $service_fetch->title);
                            $productId = $service_fetch->id ;
                            $productName =  $service_fetch->title ;
                        }else{
                            $productId = $request->ProductId ;
                        }
                    }


                       if ($request->has('FromDate') && $request->FromDate != '') {
                        $FromDate = date("Y-m-d H:i:s", strtotime($request->FromDate));
                        $mos = $mos->where('created_at', ">=", $FromDate);
                        $mts = $mts->where('created_at', ">=", $FromDate);
                        $activations = $activations->where('created_at', ">=", $FromDate);
                    }

                    if ($request->has('ToDate') && $request->ToDate != '') {
                        $ToDate = date("Y-m-d H:i:s", strtotime($request->ToDate));
                        $mos = $mos->where('created_at', "<=", $ToDate);
                        $mts = $mts->where('created_at', "<=", $ToDate);
                        $activations = $activations->where('created_at', "<=", $ToDate);
                    }

                    $mts = $mts->orderBy('created_at','Desc')->take(PAGINATION)->get();
                    $mos = $mos->orderBy('created_at','Desc')->take(PAGINATION)->get();
                    $activations = $activations->orderBy('created_at','Desc')->take(PAGINATION)->get();


                    foreach ($mos as $mo) {
                        $product[$i]['productId'] = (string) $productId;
                        $product[$i]['productName'] = $productName;
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
                        $product[$i]['productId'] =  (string)($productId??ACTIVE_SERVICES_Array[$mt->service]??'');
                        $product[$i]['productName'] = $productName??array_search ( $product[$i]['productId'] , ACTIVE_SERVICES_Array)??'';
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


                    foreach ($activations as $activation) {

                        $product[$i]['productId'] =  (string) ACTIVE_SERVICES_Array[$activation->serviceid];
                        $product[$i]['productName'] = $activation->serviceid;
                        $product[$i]['userLa'] = TIMWE_SHORTCODE;
                        $product[$i]['userMessage'] = "";
                        $product[$i]['systemResponse'] = "";
                        $product[$i]['systemLa'] = TIMWE_SHORTCODE;
                        $product[$i]['billableAction'] = "Yes";
                        $product[$i]['billingAmount'] = "2";
                        $product[$i]['userMessageDate'] = ""; //"24-Jan-2019 12:20"
                        $product[$i]['systemResponseDate'] = $activation->created_at->format('d-M-Y h:i'); //"24-Jan-2019 12:20"
                        $product[$i]['direction'] = "DIRECT BILLING";

                        $i++;
                    }




                    if (isset($product)) {
                        $service['userRequest '] = $product;
                    }



                    //if found
                    $response['msisdn'] = $request->Msisdn;
                    $response['opId'] = $request->OpId ?? "268";

                    $response['responseStatus']['code'] = "1";
                    $response['responseStatus']['description'] = "success";
                    $responseObj['response'] = $response;
                    if (isset($service)) {
                        $responseObj['response']['service'] = [$service];
                    }






                }

            }

        } else { // Auth check
            $response['responseStatus']['code'] = "-3";
            $response['responseStatus']['description'] = "Invalid Credentials";
            $responseObj['response'] = $response;
        }

                $actionName = 'User History';
                $URL = $request->fullUrl();
                $this->log($actionName, $URL, $responseObj);

                $timwecct = new Timwecct();
                $timwecct->request_link = $URL;
                $timwecct->response = json_encode($responseObj);
                $timwecct->save();

        return json_encode($responseObj);

    }

    //localhost:8080/du_system/api/sendmt?AuthUser=IVAS_CCT&AuthPass=CCT_2020_981&Msisdn=971554350230&MtText=test&ProductId=5

    public function sendmt(Request $request)
    {

        $AuthUser = $request->AuthUser;
        $AuthPass = $request->AuthPass;

        if ($AuthUser == TIMWE_AuthUser && $AuthPass == TIMWE_AuthPass) {

            $check_op = $this->OpId($request, 'CCT Send Mt');
            if($check_op != ""){
                return $check_op;
            }
            $check_service = $this->ServiceId($request, 'CCT Send MT');
            if($check_service != ""){
                return $check_service;
            }
            $check_product = $this->ProductId($request, 'CCT Send Mt');
            if($check_product != ""){
                return $check_product;
            }

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

                                $message_type = "cct_sent_mt";
                                $LogMessage_id =      $this->du_message_send($request->Msisdn, $request->MtText,$subscriber->serviceid, $message_type);

                                $response['responseStatus']['code'] = "1";
                                $response['responseStatus']['description'] = "success";

                                $service_id = Service::where('title', $subscriber->serviceid)->first();

                                $product['id'] = $service_id->id;
                                $product['la'] = TIMWE_SHORTCODE;
                                $product['subId'] = $subscriber->activation_id;
                                $product['mtId'] = $LogMessage_id;
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



                            } else {

                                $response['responseStatus']['code'] = "-77";
                                $response['responseStatus']['description'] = "sub not active";

                            }
                        }
                    }
                }
                $responseObj['response'] = $response;

                if (isset($service_arr)) {
                    $responseObj['response']['service'] = [$service_arr];
                }
                $actionName = 'SendMt';
                $URL = $request->fullUrl();
                $this->log($actionName, $URL, $responseObj);

                $timwecct = new Timwecct();
                $timwecct->request_link = $URL;
                $timwecct->response = json_encode($responseObj);
                $timwecct->save();
                return json_encode($responseObj);
            }
        } else { // Auth check
            $response['responseStatus']['code'] = "-3";
            $response['responseStatus']['description'] = "Invalid Credentials";
            $responseObj['response'] = $response;
        }
        $actionName = 'SendMt';
        $URL = $request->fullUrl();
        $this->log($actionName, $URL, $responseObj);

        $timwecct = new Timwecct();
        $timwecct->request_link = $URL;
        $timwecct->response = json_encode($responseObj);
        $timwecct->save();
        return json_encode($responseObj);
    }

    public function du_message_send($Msisdn, $MtText,$service,$message_type)
    {
        // Du sending welcome message
        $URL = DU_SMS_SEND_MESSAGE;
       // $URL = "";
        $param = "phone_number=$Msisdn&message=$MtText";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        if ($result == "1") {
            $message_mean = "CCT Sent Mt Success";
            $status = 1;
        } else {
            $message_mean = "CCT Sent Mt fail";
            $status = 0;
        }

        // log CCT Sent Mt
        $send_array["Date"] = Carbon::now()->format('Y-m-d H:i:s');
        $send_array["du_sms_result"] = $result;
        $send_array["du_message_mean"] = $message_mean;
        $send_array["message"] = $MtText;
        $send_array["msisdn"] = $Msisdn;
        $this->log('CCT Kannel Sent Mt '.$service, url('/du_message_send'), $send_array);


        $logmes = new LogMessage();
        $logmes->service       = $service;
        $logmes->msisdn        = $Msisdn;
        $logmes->message       = $MtText;
        $logmes->message_type  =  $message_type;
        $logmes->status  = $status;
        $logmes->save();

        return  $logmes->id ;
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
