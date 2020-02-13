<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Country;
use App\Operator;
use Validator;
class AdminCountryController extends Controller
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
        $countrys = Country::all();
        return view('backend.country.index',compact('countrys'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $country = NULL;
        return view('backend.country.form',compact('country'));
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
                    'name' => 'required|string|unique:countries',
            ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $country = Country::create($request->all());
        \Session::flash('success', 'Country Created Successfully');
        return redirect('admin/country');
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
        $country = Country::findOrFail($id);
        return view('backend.country.form',compact('country'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      $validator = Validator::make($request->all(), [
                  'name' => 'required|string|unique:countries,name,'.$id,
          ]);

      if ($validator->fails()) {
          return back()->withErrors($validator)->withInput();
      }

      $country = Country::findOrFail($id)->update($request->all());

      \Session::flash('success', 'Country Update Successfully');
      return redirect('admin/country');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $country = Country::findOrFail($id)->delete();
      $operators = Operator::where('country_id',$id)->get();
      if(env('clear')){
        foreach($operators as $op){
            $op->services()->delete();
            $op->delete();
        }
      }
      \Session::flash('success', 'Country Delete Successfully');
      return back();
    }
}
