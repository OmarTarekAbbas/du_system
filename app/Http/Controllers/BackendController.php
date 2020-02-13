<?php

namespace App\Http\Controllers;

use App\Message;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class BackendController extends Controller
{
    public function index()
    {
        return view('backend.index');
    }

    public function send_mt()
    {
        return view('backend.send_mt');
    }

    public function sync()
    {
        $Req = Request::capture();
        Message::create($Req->all());
        return 'Done !!';
    }

    public function shortURLs(){
        $Messages = Message::where('date','>',Carbon::now()->format('Y-m-d'))->get();
        foreach ($Messages as $Message){
            echo $Message->MTURL.'|' .$Message->service_id.'<br>';
        }

    }

}
