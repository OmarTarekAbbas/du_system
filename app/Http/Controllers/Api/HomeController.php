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
        $output = 0 ;
        $result = Activation :: where("msisdn",$request->msisdn)->where("serviceid",$request->serviceid)->orderBy("created_at", "desc")->first(['id','msisdn','serviceid']);
        if($request->msisdn == $result["msisdn"]){
            $sub    = Subscriber :: where("activation_id",$result->id)->first();
            if($sub ){
                $unsub  = new Unsubscriber();
                $unsub->activation_id = $sub->activation_id;
                $unsub->save();
                $sub->delete();
                $output = 1 ;
            }else{
                $output = 0 ;
            }

        }else{
            $output = 0 ;
        }
        return $output ;

    }
}
