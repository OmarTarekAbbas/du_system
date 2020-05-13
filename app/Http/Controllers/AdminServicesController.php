<?php

namespace App\Http\Controllers;

use App\Activation;
use App\Charge;
use App\Subscriber;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Service;
use App\Operator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;

class AdminServicesController extends Controller
{

    private $rules;

    public function __construct()
    {
        $this->middleware('auth');
        $this->rules = [
            'title' => 'required',
            'service' => 'required',
            'lang' => 'required',
            'type' => 'required',
            'operator_id' => 'required|numeric',
            'size' => 'numeric|min:0',
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $services = Service::paginate(20);
        return view('backend.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $service = NULL;
        $operators = Operator::all();
        return view('backend.services.form', compact('service', 'operators'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $service = new Service($request->all());
        $service->save();
        $request->session()->flash('success', 'Service Added successfully');
        return redirect('admin/services');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $service = Service::findOrFail($id);

        $activations = Activation::where("serviceid", $service->title)->count();

        $subscribers = Activation::select('*','subscribers.id as subscribe_id')
            ->join('subscribers','subscribers.activation_id','=','activation.id')
            ->where('activation.serviceid', $service->title)->count();

        $unsubscribers = Activation::select('*','unsubscribers.id as subscribe_id')
            ->join('unsubscribers','unsubscribers.activation_id','=','activation.id')
            ->where('activation.serviceid', $service->title)->count();

        /*$charges = Charge::select('*','charges.id as charge_id','charges.status_code as charge_status_code')
            ->join('subscribers','subscribers.id','=','charges.subscriber_id')
            ->join('activation','subscribers.activation_id','=','activation.id')
            ->where('activation.serviceid',$service->title)->count();*/

        $charge_date = Charge::select('*','charges.id as charge_id','charges.status_code as charge_status_code')
            ->join('subscribers','subscribers.id','=','charges.subscriber_id')
            ->join('activation','subscribers.activation_id','=','activation.id')
            ->where('charges.charging_date','=',Carbon::now()->toDateString())
            ->where('activation.serviceid',$service->title)->groupBy('charges.subscriber_id')->count();

        $charge_status_0 = Charge::select('*','charges.id as charge_id','charges.status_code as charge_status_code')
            ->join('subscribers','subscribers.id','=','charges.subscriber_id')
            ->join('activation','subscribers.activation_id','=','activation.id')
            ->where('charges.status_code',0)
            ->where('charges.charging_date','=',Carbon::now()->toDateString())
            ->where('activation.serviceid',$service->title)->groupBy('charges.subscriber_id')->count();

        $charge_status_503 = Charge::select('*','charges.id as charge_id','charges.status_code as charge_status_code')
            ->join('subscribers','subscribers.id','=','charges.subscriber_id')
            ->join('activation','subscribers.activation_id','=','activation.id')
            ->where('charges.status_code','503 - product already purchased!')
            ->where('charges.charging_date','=',Carbon::now()->toDateString())
            ->where('activation.serviceid',$service->title)->groupBy('charges.subscriber_id')->count();

        $charge_status_24 = Charge::select('*','charges.id as charge_id','charges.status_code as charge_status_code')
            ->join('subscribers','subscribers.id','=','charges.subscriber_id')
            ->join('activation','subscribers.activation_id','=','activation.id')
            ->where('charges.status_code','24 - Insufficient funds.')
            ->where('charges.charging_date','=',Carbon::now()->toDateString())
            ->where('activation.serviceid',$service->title)->groupBy('charges.subscriber_id')->count();

        $failed = Charge::select('*','charges.id as charge_id','charges.status_code as charge_status_code')
            ->join('subscribers','subscribers.id','=','charges.subscriber_id')
            ->join('activation','subscribers.activation_id','=','activation.id')
            ->whereNotIn('charges.status_code',['503 - product already purchased!','0','24 - Insufficient funds.'])
            ->where('charges.charging_date','=',Carbon::now()->toDateString())
            ->where('activation.serviceid',$service->title)->groupBy('charges.subscriber_id')->count();



        return view('backend.services.show', compact('service', 'activations','subscribers','unsubscribers','charge_date','charge_status_0','charge_status_503','charge_status_24','failed'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $service = Service::findOrFail($id);
        $operators = Operator::all();

        return view('backend.services.form', compact('service', 'operators'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $oldService = Service::findOrFail($id);
        $service = $request->all();
        // print_r($service) ; die;
        $oldService->update($service);
        session()->flash('success', 'Service Updated successfully');
        return redirect('admin/services');
    }

}
