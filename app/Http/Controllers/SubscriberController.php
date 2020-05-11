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

        if($request->has('activation_id') && $request->activation_id != ''){
            $subscribers = $subscribers->where('subscribers.activation_id',$request->activation_id);
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

    public function getExcel()
    {
        return view('backend.subscribers.form');
    }

    public function subscribe_excel(Request $request)
    {
        ini_set('max_execution_time', 60000000000);
        ini_set('memory_limit', -1);
        $data = [];
        if ($request->hasFile('file')) {
            $ext =  $request->file('file')->getClientOriginalExtension();
            if ($ext != 'xls' && $ext != 'xlsx' && $ext != 'csv') {
                $request->session()->flash('failed', 'File must be excel');
                return back();
            }

            $file = $request->file('file');
            $filename = time().'_'.$file->getClientOriginalName();
            if(!$file->move(base_path().'/du_integration/'.date('Y-m-d').'/excel',  $filename) ){
                return back();
            }
        }
        \Excel::filter('chunk')->load(base_path().'/du_integration/'.date('Y-m-d').'/excel/'.$filename)->chunk(100, function($results) use(&$data)
        {
            foreach ($results as $row) {
                array_push($data,$row->msisdn);
                // $ch = curl_init();
                // $getUrl = "https://du.notifications.digizone.com.kw/api/logmessage?msisdn=971".$row->msisdn."&message=1";
                // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
                // curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                // curl_setopt($ch, CURLOPT_URL, $getUrl);
                // curl_setopt($ch, CURLOPT_TIMEOUT, 80);
                // $response = curl_exec($ch);
                // curl_close($ch);
            }
        },false);
        return $data;
    }
}
