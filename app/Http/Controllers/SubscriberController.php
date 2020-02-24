<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Activation;
use App\Subscriber;
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
    public function index()
    {
        $subscribers = Activation::select('*','subscribers.id as subscribe_id')
                       ->join('subscribers','subscribers.activation_id','=','activation.id');
        $subscribers = $subscribers->paginate(10);
        return view('backend.subscribers.index',compact('subscribers'));
    }
}
