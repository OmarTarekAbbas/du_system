<?php
namespace App\Http\services;

class CheckMsisdnServices
{
    public function checksub(Type $var = null)
    {
        $subscriber = Subscriber::join('activation', 'activation.id', '=', 'subscribers.activation_id');

        $subscriber = $subscriber->where('msisdn', $request->Msisdn);

        $subscriber = $subscriber->first();

        $service_name = $subscriber->serviceid ?? $unsubscriber->serviceid;

        $product['subStatus'] = "ACTIVE";
        $product['subscriptionDate'] = $subscriber->created_at->format('d-M-Y h:i'); //"24-Jan-2019 12:20"

    }
}
