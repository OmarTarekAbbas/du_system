<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Subscriber;
use App\Activation;
use App\Unsubscriber;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    public function msisdn(Request $request)
    {
        $result = Activation :: where("msisdn",$request->msisdn)->where("serviceid",$request->serviceid)->where("status_code",0)->orderBy("created_at", "desc")->first(['id','msisdn','serviceid']);
        $sub = Subscriber :: where("activation_id",$result->id)->first();
        $unsub = new Unsubscriber();
        $unsub->activation_id = $sub->activation_id;
        $unsub->save();
        $sub->delete();
    }
}
