<?php

namespace App\Http\Controllers;

use Hamcrest\Core\Set;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Setting;

/**
 * Class SettingController.
 *
 * @author  The scaffold-interface created at 2017-04-02 02:44:30pm
 * @link  https://github.com/amranidev/scaffold-interface
 */
class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return  \Illuminate\Http\Response
     */

    public function index()
    {
        $settings = Setting::all();
        return view('backend.setting.index',compact('settings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Approve  $approve
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Approve  $approve
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $setting = Setting::findOrFail($id);
        return view('backend.setting.form',compact('setting'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Approve  $approve
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $setting = Setting::findOrFail($id);
        $setting->value = $request->value;
        $setting->save();
        return redirect('admin/setting');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Approve  $approve
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        //
    }


}
