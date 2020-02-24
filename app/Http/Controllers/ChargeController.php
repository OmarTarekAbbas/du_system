<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Charge;
use App\Subscriber;
use App\Service;
use Validator;
class ChargeController extends Controller
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
        $charges = Charge::select('*','charges.id as charge_id','charges.status_code as charge_status_code')
                   ->join('subscribers','subscribers.id','=','charges.subscriber_id')
                   ->join('activation','subscribers.activation_id','=','activation.id');
        $services = Service::pluck('service','title');
        $without_paginate = 0;
        if($request->has('subscriber_id') && $request->subscriber_id != ''){
            $charges = $charges->where('charges.subscriber_id',$request->subscriber_id);
            //$without_paginate = 1;
        }

        if($request->has('from_date') && $request->from_date != ''){
            $charges = $charges->where('charges.charging_date','>=',$request->from_date);
            $without_paginate = 1;
        }

        if($request->has('to_date') && $request->to_date != ''){
            $charges = $charges->where('charges.charging_date','<=',$request->to_date);
            $without_paginate = 1;
        }

        if($request->has('msisdn') && $request->msisdn != ''){
            $charges = $charges->where('activation.msisdn',$request->msisdn);
            $without_paginate = 1;
        }

        if($request->has('plan') && $request->plan != ''){
            $charges = $charges->where('activation.plan',$request->plan);
            $without_paginate = 1;
        }

        if($request->has('serviceid') && $request->serviceid != ''){
            $charges = $charges->where('activation.serviceid',$request->serviceid);
            $without_paginate = 1;
        }

        if($without_paginate){
            $charges = $charges->get();
        }else{
            $charges = $charges->paginate(10);
        }
        return view('backend.charges.index',compact('charges','services','without_paginate'));
    }

}