<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    public function __construct(){
        $this->middleware('admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        $Users = User::all();
        return view('auth.userlist',compact('Users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
        return view('backend.adduser');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        //
        $req = Request::capture();
        $Items = $req->all();
        $Items['password'] = bcrypt($req->input('password'));
        User::create($Items);
        return redirect('admin/user');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
        $User = User::find($id);
        return view('auth.edit',compact('User'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        //
        $req = Request::capture();
        $Items = $req->all();
        $User = User::find($id);
        if($Items['password'] == $User->password){
            array_pull($Items,'password');
        }else{
            $Items['password'] = bcrypt($Items['password']);
        }

        $Items['admin'] = intval($Items["admin"]);
        //dd($Items);
        User::find($id)->update($Items);
        return redirect('admin/user');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
        User::destroy($id);
        return redirect('admin/user');
    }
}
