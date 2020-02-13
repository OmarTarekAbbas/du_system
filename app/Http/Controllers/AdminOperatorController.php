<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Operator;
use App\Country;
use Validator;
class AdminOperatorController extends Controller
{
  public function __construct() {
      $this->middleware('admin');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $operators = Operator::all();
        return view('backend.operator.index',compact('operators'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countrys = Country::all();
        $operator = NULL;
        return view('backend.operator.form',compact('countrys','operator'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $validator = Validator::make($request->all(), [
                  'title' => 'required|string|unique:operators,title,null,id,country_id,'.$request->country_id,
                  'channel' => 'required',
                  'country_id' => 'required'
          ]);

      if ($validator->fails()) {
          return back()->withErrors($validator)->withInput();
      }

      $operator = Operator::create($request->all());

      \Session::flash('success', 'Operator Created Successfully');
      return redirect('admin/operator');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $operator = Operator::findOrFail($id);
      $countrys = Country::all();
      return view('backend.operator.form',compact('operator','countrys'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
      //dd($request->all());
      $validator = Validator::make($request->all(), [
                  'title' => 'required|string|unique:operators,title,'.$id.',id,country_id,'.$request->country_id,
                  'channel' => 'required',
                  'country_id' => 'required'
          ]);

      if ($validator->fails()) {
          return back()->withErrors($validator)->withInput();
      }

      $operator = Operator::findOrFail($id);


      $operator->update($request->all());
      \Session::flash('success', 'Operator Update Successfully');
      return redirect('admin/operator');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $operator = Operator::findOrFail($id);
      if(env('clear')){
        $operator->services()->delete();
      }
      $operator->delete();

      \Session::flash('success', 'Operator Delete Successfully');
      return back();
    }
}
