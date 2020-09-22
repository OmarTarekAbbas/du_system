<?php

namespace App\Http\Controllers\Api;

use App\Service;
use App\Subscriber;
use App\Unsubscriber;
use Illuminate\Http\Request;
use stdClass;

class TimweController
{
    public function inquiry(Request $request)
    {
        $AuthUser = $request->AuthUser;
        $AuthPass = $request->AuthPass;
        if ($AuthUser == TIMWE_AuthUser && $AuthPass == TIMWE_AuthPass) {
            $subscriber = Subscriber::join('activation', 'activation.id', '=', 'subscribers.activation_id');
            $unsubscriber = Unsubscriber::join('activation', 'activation.id', '=', 'unsubscribers.activation_id');

            $param['RequestId'] = $request->RequestId;

            if ($request->has('Msisdn') && $request->Msisdn != '') {
                $param['Msisdn'] = $request->Msisdn;
                $response['msisdn'] = $request->Msisdn;

                $subscriber = $subscriber->where('msisdn', $request->Msisdn);
                $unsubscriber = $unsubscriber->where('msisdn', $request->Msisdn);
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
                    $product['subscriptionDate'] = $subscriber->updated_at->format('d-M-Y h:i'); //"24-Jan-2019 12:20"
                } elseif ($unsubscriber) {
                    $product['subStatus'] = "CANCELLED";
                    $product['subscriptionDate'] = $unsubscriber->activation->created_at->format('d-M-Y h:i'); //"24-Jan-2019 12:20"
                    $product['unsubscriptionDate'] = $unsubscriber->updated_at->format('d-M-Y h:i'); //"24-Jan-2019 12:20"
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

            return json_encode($responseObj);

            if ($request->has('ServiceId') && $request->ServiceId != '') {
                $param['ServiceId'] = $request->ServiceId;
                $response['ServiceId'] = $request->ServiceId;

            }

            if ($request->has('ProductId') && $request->ProductId != '') {
                $param['ProductId'] = $request->ProductId;
            }

            if ($request->has('CountryId') && $request->CountryId != '') {
                $param['CountryId'] = $request->CountryId;
            }

            if ($request->has('La') && $request->La != '') {
                $param['La'] = $request->La;
            }

            if ($request->has('ActiveOnly') && $request->ActiveOnly != '') {
                $param['ActiveOnly'] = $request->ActiveOnly;
            }

            if ($request->has('FromDate') && $request->FromDate != '') {
                $param['FromDate'] = $request->FromDate;
            }

            if ($request->has('ToDate') && $request->ToDate != '') {
                $param['ToDate'] = $request->ToDate;
            }
        }
    }
}
