<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Activation;
use App\Subscriber;
use App\Service;
use Validator;
class UnSubscriberController extends Controller
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
        $unsubscribers = Activation::select('*','unsubscribers.id as subscribe_id')
                       ->join('unsubscribers','unsubscribers.activation_id','=','activation.id');
        $services = Service::pluck('service','title');
        $without_paginate = 0;

        if($request->has('msisdn') && $request->msisdn != ''){
            $unsubscribers = $unsubscribers->where('activation.msisdn',$request->msisdn);
            $without_paginate = 1;
        }

        if($request->has('plan') && $request->plan != ''){
            $unsubscribers = $unsubscribers->where('activation.plan',$request->plan);
            $without_paginate = 1;
        }

        if($request->has('serviceid') && $request->serviceid != ''){
            $unsubscribers = $unsubscribers->where('activation.serviceid',$request->serviceid);
            $without_paginate = 1;
        }

        if($request->has('activation_id') && $request->activation_id != ''){
            $unsubscribers = $unsubscribers->where('unsubscribers.activation_id',$request->activation_id);
            $without_paginate = 1;
        }

        if($without_paginate){
            $unsubscribers = $unsubscribers->get();
        }else{
            $unsubscribers = $unsubscribers->paginate(10);
        }
        return view('backend.unsubscribers.index',compact('unsubscribers','services','without_paginate'));
    }
}
