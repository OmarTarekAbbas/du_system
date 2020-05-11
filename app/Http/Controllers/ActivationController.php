<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Activation;
use App\Subscriber;
use App\Service;
use Validator;
class ActivationController extends Controller
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
        $activations = Activation::query();
        $services = Service::pluck('service','title');
        $without_paginate = 0;

        if($request->has('msisdn') && $request->msisdn != ''){
            $activations = $activations->where('activation.msisdn',$request->msisdn);
            $without_paginate = 1;
        }

        if($request->has('plan') && $request->plan != ''){
            $activations = $activations->where('activation.plan',$request->plan);
            $without_paginate = 1;
        }

        if($request->has('serviceid') && $request->serviceid != ''){
            $activations = $activations->where('activation.serviceid',$request->serviceid);
            $without_paginate = 1;
        }

        if($request->has('status') && $request->status != '' ){
            if($request->status == 'fail'){
                $activations = $activations->whereNotIn('activation.status_code',['503 - product already purchased!','0','24 - Insufficient funds.']);
            }else{
                $activations = $activations->where('activation.status_code',$request->status);
            }
            $without_paginate = 1;
        }

        if($request->has('created') && $request->created != ''){
            $activations = $activations->whereDate('activation.created_at',$request->created);
            $without_paginate = 1;
        }

        if($without_paginate){
            $activations = $activations->get();
        }else{
            $activations = $activations->paginate(10);
        }
        return view('backend.activations.index',compact('activations','services','without_paginate'));
    }
}
