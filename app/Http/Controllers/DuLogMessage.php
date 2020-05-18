<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DuMo;

class DuLogMessage extends Controller
{
    public function index(Request $request)
    {
        $messages = DuMo::query();
        $without_paginate = 0;

        if($request->has('msisdn') && $request->msisdn != ''){
            $messages = $messages->where('activation.msisdn',$request->msisdn);
            $without_paginate = 1;
        }

        if($request->has('plan') && $request->plan != ''){
            $messages = $messages->where('activation.plan',$request->plan);
            $without_paginate = 1;
        }

        if($request->has('serviceid') && $request->serviceid != ''){
            $messages = $messages->where('activation.serviceid',$request->serviceid);
            $without_paginate = 1;
        }

        if($request->has('status') && $request->status != '' ){
            if($request->status == 'fail'){
                $messages = $messages->whereNotIn('activation.status_code',['503 - product already purchased!','0','24 - Insufficient funds.']);
            }else{
               // if($request->status == "0")   $request->status = 0 ;  //  as it read "0"
                $messages = $messages->where('activation.status_code',$request->status);
            }
            $without_paginate = 1;
        }

        if($request->has('created') && $request->created != ''){
            $messages = $messages->whereDate('activation.created_at',$request->created);
            $without_paginate = 1;
        }

        if($without_paginate){
            $messages = $messages->get();
        }else{
            $messages = $messages->paginate(10);
        }
        return view('backend.logMessage.index',compact('messages','without_paginate'));
    }
}
