<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Service;
use App\Operator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;

class AdminServicesController extends Controller {

    private $rules;

    public function __construct() {
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
    public function index() {
        $services = Service::paginate(20);
        return view('backend.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $service = NULL;
        $operators = Operator::all();
        return view('backend.services.form', compact('service', 'operators'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $service = Service::findOrFail($id);
        $operators = Operator::all();

        return view('backend.services.form', compact('service', 'operators'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
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
