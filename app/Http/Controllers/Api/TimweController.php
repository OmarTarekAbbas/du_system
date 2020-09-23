<?php

namespace App\Http\Controllers\Api;

use App\Service;
use App\Subscriber;
use App\Unsubscriber;
use Illuminate\Http\Request;
use stdClass;

class TimweController
{

    //http://localhost:8080/du_system/api/inquiry?AuthUser=IVAS&AuthPass=123456&Msisdn=971555802322

    //http://localhost:8080/du_system/api/inquiry?AuthUser=IVAS&AuthPass=123456

    public function inquiry(Request $request)
    {

        /*
        checkMsisdnrequest() {
            checksub()
            checkunsub()
            returnnotexist()
        }
        all_services()
        */

        $AuthUser = $request->AuthUser;
        $AuthPass = $request->AuthPass;
        $subscriber = Subscriber::join('activation', 'activation.id', '=', 'subscribers.activation_id');
        $unsubscriber = Unsubscriber::join('activation', 'activation.id', '=', 'unsubscribers.activation_id');
        if ($AuthUser == TIMWE_AuthUser && $AuthPass == TIMWE_AuthPass) {

            $param['RequestId'] = $request->RequestId;

            if ($request->has('Msisdn') && $request->Msisdn != '') {
                $param['Msisdn'] = $request->Msisdn;
                $response['msisdn'] = $request->Msisdn;

                $subscriber = $subscriber->where('msisdn', $request->Msisdn);
                $unsubscriber = $unsubscriber->where('msisdn', $request->Msisdn);

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

            return json_encode($responseObj);

        }
    }
}
