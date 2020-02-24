<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Activation;
use App\Subscriber;
use App\Service;
use Validator;
class SubscriberController extends Controller
{
    public function __construct()
    {
      $this->middleware('admin');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $subscribers = Activation::select('*','subscribers.id as subscribe_id')
                       ->join('subscribers','subscribers.activation_id','=','activation.id');
        $services = Service::pluck('service','title');
        $without_paginate = 0;
        if($request->has('next_charging_date') && $request->next_charging_date != ''){
            $subscribers = $subscribers->where('subscribers.next_charging_date',$request->next_charging_date);
            $without_paginate = 1;
        }

        if($request->has('msisdn') && $request->msisdn != ''){
            $subscribers = $subscribers->where('activation.msisdn',$request->msisdn);
            $without_paginate = 1;
        }

        if($request->has('plan') && $request->plan != ''){
            $subscribers = $subscribers->where('activation.plan',$request->plan);
            $without_paginate = 1;
        }

        if($request->has('serviceid') && $request->serviceid != ''){
            $subscribers = $subscribers->where('activation.serviceid',$request->serviceid);
            $without_paginate = 1;
        }

        if($without_paginate){
            $subscribers = $subscribers->get();
        }else{
            $subscribers = $subscribers->paginate(10);
        }
        return view('backend.subscribers.index',compact('subscribers','services','without_paginate'));
    }
}
