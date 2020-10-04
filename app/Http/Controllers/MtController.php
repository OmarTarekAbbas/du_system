<?php

namespace App\Http\Controllers;

use App\Message;
use App\Operator;
use App\Service;
use App\Upload;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Mail;  // add this

class MtController extends Controller
{
    /**
     * for Auth request before do anything
     */
    public function __construct()
    {
        $this->middleware('auth',['except' => ['Download']]);
    }

    public function adminindex()
    {
        return redirect('admin/mt');
    }


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        if (!is_null(session('OpID'))) {
            if (Auth::user()->admin == true) {
                if (Session::has('approved')){
                    $Messages = Message::orderBy('id', 'asc')->paginate(20);
                }else{
                    return redirect('admin/allmts');
                }
            } else {
                $Messages = Message::where('service_id', '=', Session::get('OpID'))->orderBy('id', 'asc')->paginate(20);
            }
            return view('backend.allmts', compact('Messages'));
        } else {
            if (Auth::user()->admin == true) {
                if (Session::has('approved')){
                    $Messages = Message::orderBy('id', 'asc')->paginate(20);
                }else{
                    return redirect('admin/allmts');
                }
                return view('backend.allmts', compact('Messages'));
            } else {
                return redirect('service');
            }
        }
    }

    public function allmts(){
        $Messages = Message::where('status','=',false)->orderBy('id', 'asc')->paginate(20);
        session(['approved'=>true]);
		 // echo  $Messages->count() ; die;
        return view('backend.allmts', compact('Messages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
        if (is_null(session('OpID'))) {
            return redirect('service');
        }
        $ServicesFetch = Service::all();
        $Services = array();
        foreach ($ServicesFetch as $Service) {
            $Services[$Service->id] = $Service->title.' | '.$Service->operator->title.' - '.$Service->operator->country->name;
        }
        return view('backend.send_mt', compact('Services'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */


       public function store(Requests\MtRequest $request) {
        //
        $time = $request->input('time');
        if ($time == "") {
            $time = NULL;
        }

        if ($request->input('date') < Carbon::now()->format('Y-m-d')) {
            $errors['dateerror'] = 'You selected a previous date, Please select right date';
            //  echo $request->fullUrl() ;    // echo the action of form  if you make inspect  action="http://localhost:8000/admin/mt"
            return redirect($request->fullUrl() . '/create')->withErrors($errors)->withInput();
        }

        if (is_null($request->file('file'))) {   //As URL    //   if message  is External Link
            $OpId = Session::get('OpID');
            $GetServiceDetails = Service::find($OpId);
            // we get the messages that has the same date and same service_id
            //  and if found not create and show error message that that message found before
            // else it create new message and make message->ShortnedURL by curl   and also if user is admin the status is 1 (approved)  else the message status is 0 (not approved)


            $Messages = Message::where('date', '=', $request->input('date'))->where('service_id', '=', $OpId)->whereNull('time')->get();
            if ($Messages->isEmpty()) {
                $status = (Auth::user()->admin == true) ? 1 : 0;
                if (Auth::user()->admin == 0) {
                    $status = ENABLE;
                }
                Message::create(['MTBody' => rtrim($request->input('MTBody'))  , 'date' => $request->input('date'), 'MTURL' => $request->input('MTURL'), 'ShortnedURL' => $this->ShortURL(trim($request->input('MTURL')), $request->input('date'), $GetServiceDetails->ExURL), 'service_id' => $OpId, 'status' => $status,
                'user_id' => Auth::user()->id, 'time' => $time ]);
                return redirect('admin/mt');
            } else {
                if ($time == NULL) {
                    $ServicesFetch = Service::all();
                    $Services = array();
                    foreach ($ServicesFetch as $Service) {
                        $Services[$Service->id] = $Service->title . ' | ' . $Service->operator->title . ' - ' . $Service->operator->country->name;
                    }
                    $Messages = Message::where('date', '=', $request->input('date'))->where('service_id', '=', $OpId)->paginate(15);
                    $errors['samedate' . $OpId] = 'Please select another date for ' . $Services[$OpId];
                    return view('backend.allmts', compact('Messages'))->withErrors($errors);
                } else { // add time
                    $Messages = Message::where('date', '=', $request->input('date'))->where('service_id', '=', $OpId)->where('time', '=', $time)->get();
                    if ($Messages->isEmpty()) {
                        $status = (Auth::user()->admin == true) ? 1 : 0;
                        if (Auth::user()->admin == 0) {
                            $status = ENABLE;
                        }
                        Message::create(['MTBody' => rtrim($request->input('MTBody')) , 'date' => $request->input('date'), 'MTURL' => $request->input('MTURL'), 'ShortnedURL' => $this->ShortURL(trim($request->input('MTURL')), $request->input('date'), $GetServiceDetails->ExURL), 'service_id' => $OpId, 'status' => $status, 'user_id' => Auth::user()->id
                            , 'time' => $time]);
                        return redirect('admin/mt');
                    } else {
                        $ServicesFetch = Service::all();
                        $Services = array();
                        foreach ($ServicesFetch as $Service) {
                            $Services[$Service->id] = $Service->title . ' | ' . $Service->operator->title . ' - ' . $Service->operator->country->name;
                        }
                        $Messages = Message::where('date', '=', $request->input('date'))->where('service_id', '=', $OpId)->where('time', '=', $time)->paginate(15);
                        $errors['samedate' . $OpId] = 'Please select another time for ' . $Services[$OpId];
                        return view('backend.allmts', compact('Messages'))->withErrors($errors);
                    }
                }
            }
        } else {  //As File  //  //if message  is  upload file
            $OpId = Session::get('OpID');
            $GetServiceDetails = Service::find($OpId);
            $file = $request->file('file');
            $AllowedEx = array('mp4', 'mp3', 'wav', 'jpg', 'gif', 'png', '3gp');
            if (in_array($file->getClientOriginalExtension(), $AllowedEx)) { // to upload only these extensions
                if ($file->getClientSize() > intval($GetServiceDetails->size) * 1024) {  // validation to file size
                    $errors['maxerror'] = 'You are not allowed to upload size (Maximum size is :' . $GetServiceDetails->size . ' KB).';
                    return redirect($request->fullUrl() . '/create')->withErrors($errors)->withInput();
                }

                // we get the messages that has the same date and same service_id
                //  and if found not create and show error message that that message found before
                // else it create new message and make message->ShortnedURL by curl   and also if user is admin the status is 1 (approved)  else the message status is 0 (not approved)
                // and also we create new record in uploads table and set upload->path and   upload->Fid
                $Messages = Message::where('date', '=', $request->input('date'))->where('service_id', '=', $OpId)->whereNull('time')->get();
                if ($Messages->isEmpty()) {
                    $FID = $this->UploadContent($request->file('file'), $OpId);
                    $status = (Auth::user()->admin == true) ? 1 : 0;
                    if (Auth::user()->admin == 0) {
                        $status = ENABLE;
                    }
                    Message::create(['MTBody' =>rtrim($request->input('MTBody')) , 'date' => $request->input('date'), 'MTURL' => url('get/' . $FID), 'ShortnedURL' => $this->ShortURL(url('get/' . $FID), $request->input('date'), $GetServiceDetails->ExURL), 'service_id' => $OpId, 'status' => $status, 'user_id' => Auth::user()->id]);
                    return redirect('admin/mt');
                } else {
                    if ($time == NULL) {
                        $ServicesFetch = Service::all();
                        $Services = array();
                        foreach ($ServicesFetch as $Service) {
                            $Services[$Service->id] = $Service->title . ' | ' . $Service->operator->title . ' - ' . $Service->operator->country->name;
                        }
                        $errors['samedate' . $OpId] = 'Please select another date for ' . $Services[$OpId];
                        $Messages = Message::where('date', '=', $request->input('date'))->where('service_id', '=', $OpId)->paginate(15);
                        return view('backend.allmts', compact('Messages'))->withErrors($errors);
                    } else {

                        $Messages = Message::where('date', '=', $request->input('date'))->where('service_id', '=', $OpId)->where('time', '=', $time)->get();
                        if ($Messages->isEmpty()) {
                            $FID = $this->UploadContent($request->file('file'), $OpId);
                            $status = (Auth::user()->admin == true) ? 1 : 0;
                            if (Auth::user()->admin == 0) {
                                $status = ENABLE;
                            }
                            Message::create(['MTBody' =>rtrim($request->input('MTBody')) , 'date' => $request->input('date'), 'MTURL' => url('get/' . $FID), 'ShortnedURL' => $this->ShortURL(url('get/' . $FID), $request->input('date'), $GetServiceDetails->ExURL), 'service_id' => $OpId, 'status' => $status, 'user_id' => Auth::user()->id
                                , 'time' => $time]);
                            return redirect('admin/mt');
                        } else {
                            $ServicesFetch = Service::all();
                            $Services = array();
                            foreach ($ServicesFetch as $Service) {
                                $Services[$Service->id] = $Service->title . ' | ' . $Service->operator->title . ' - ' . $Service->operator->country->name;
                            }
                            $errors['samedate' . $OpId] = 'Please select another time for ' . $Services[$OpId];
                            $Messages = Message::where('date', '=', $request->input('date'))->where('service_id', '=', $OpId)->where('time', '=', $time)->paginate(15);
                            return view('backend.allmts', compact('Messages'))->withErrors($errors);
                        }
                    }
                }
            } else {
                $errors['exerror'] = 'You are not allowed to upload this extension';
                return redirect($request->fullUrl() . '/create')->withErrors($errors)->withInput();
            }
        }
    }


    /**
     * This function uploads Content to server and generates Id for file to be downloaded.
     * @param $File
     * @param $ServiceId
     * @return int (File ID)
     */
    public function UploadContent($File, $ServiceId)
    {
        $Name = $File->getClientOriginalName();
        $NewName = rand(100, 999).' - '.$Name;
        $File->move(public_path('Contents/'.$ServiceId.'/'.Carbon::now()->format('Y-m-d')), $NewName);
        $upload = Upload::create(['path'=>'Contents/'.$ServiceId.'/'.Carbon::now()->format('Y-m-d').'/'.$NewName]);
        $FID = rand(1000, 9999).$upload->id.rand(10000000, 99999999);
        $upload->update(['fid'=>$FID]);
        return $FID;
    }

    /**
     * This function checks if there is any message sent in this date.
     */
    public function checkmessage()
    {
        $array = array();
        $request = Request::capture();
        //print_r($request);
        $servicesValue = explode(',', $request->get('ids'));
        $notEntered = array();
        foreach ($servicesValue as $service) {
            $checkMessage = Message::where('service_id', '=', $service)->where('date', '=', Carbon::createFromFormat('d/m/Y', $request->get('date'))->format('Y-m-d'))->get();
            if ($checkMessage->isEmpty()) {
                //var_dump($request->get('ids'));
            } else {
                array_push($notEntered, $service);
            }
        }
        $ServicesFetch = Service::all();
        $Services = array();
        foreach ($ServicesFetch as $Service) {
            $Services[$Service->id] = $Service->title.' | '.$Service->operator->title.' - '.$Service->operator->country->name;
        }
        foreach ($notEntered as $not) {
            echo $Services[$not].' Already has message on this date'."<br/>";
        }

        //return json_encode($notEntered);
    }


    // Redirect function to select service page
    public function select_service()
    {
        $ServicesFetch = Service::all();
        return view('backend.service', ['services' => $ServicesFetch]);
    }

    //store service to session to remember it
    public function selectServiceCache()
    {
        $request = Request::capture();
        session(['OpID'=>$request->input('service')]);
        return redirect('admin/mt');
    }


    /**
     * This function shorts URL to reduce size of URL and set expiry date after 2 days.
     *
     * @param $URL
     * @param $date
     * @return string (Shorted URL)
     */
    public function ShortURL($URL, $date,$EURL)
    {
      //   $ToShortURL = "http://s.ivas.info/API/C?URL=".urlencode($URL)."&ExDate=".Carbon::createFromFormat('Y-m-d', $date)->addDays(3)->format('Y-m-d')."&ExURL=".urlencode($EURL);
        $ToShortURL = "https://short.digizone.com.kw//API/C?URL=".urlencode($URL)."&ExDate=".Carbon::createFromFormat('Y-m-d', $date)->addDays(3)->format('Y-m-d')."&ExURL=".urlencode($EURL);
       // echo  $ToShortURL ; die;
        $ShortnedURL = ServicesController::GetPageData($ToShortURL);
        return $ShortnedURL;
    }

    /**
     * This function pushes file as WAP Push.
     * @param $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function Download($id)
    {
        $File = Upload::where('fid', '=', $id)->first();
        return response()->download(public_path($File->path));
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
        $Mt = Message::find($id);

        $ServicesFetch = Service::all();
        $Services = array();
        foreach ($ServicesFetch as $Service) {
            $Services[$Service->id] = $Service->title.' | '.$Service->operator->title.' - '.$Service->operator->country->name;
        }
        return view('backend.edit_mt', compact('Mt', 'Services'));
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
        $Mt = Message::find($id);
        $ServicesFetch = Service::all();
        $Services = array();
        foreach ($ServicesFetch as $Service) {
            $Services[$Service->id] = $Service->title.' | '.$Service->operator->title.' - '.$Service->operator->country->name;
        }
        return view('backend.edit_mt', compact('Mt', 'Services'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */


    public function update(Requests\MtRequest $request, $id) {

        //
        $time = $request->input('time');
        if ($time == "") {
            $time = NULL;
        }

        if (is_null($request->file('file'))) { //AS URL
            $Message = Message::find($id);
            $OpId = $Message->service_id;
            $GetServiceDetails = Service::find($OpId);
            $status = (Auth::user()->admin == true) ? 1 : 0;
            if (Auth::user()->admin == 0) {
                $status = ENABLE;
            }
            Message::find($id)->update(['MTBody' => rtrim($request->input('MTBody')) , 'date' => $request->input('date'), 'MTURL' => $request->input('MTURL'), 'ShortnedURL' => $this->ShortURL(trim($request->input('MTURL')), $request->input('date'), $GetServiceDetails->ExURL), 'service_id' => $OpId, 'status' => $status, 'user_id' => Auth::user()->id
                , 'time' => $time]);
            return redirect('admin/mt');
        } else {  //As File
            $Message = Message::find($id);
            $OpId = $Message->service_id;
            $GetServiceDetails = Service::find($OpId);
            $FID = $this->UploadContent($request->file('file'), $OpId);
            $status = (Auth::user()->admin == true) ? 1 : 0;
            if (Auth::user()->admin == 0) {
                $status = ENABLE;
            }
            Message::find($id)->update(['MTBody' => rtrim($request->input('MTBody')) , 'date' => $request->input('date'), 'MTURL' => url('get/' . $FID), 'ShortnedURL' => $this->ShortURL(url('get/' . $FID), $request->input('date'), $GetServiceDetails->ExURL), 'service_id' => $OpId, 'status' => $status, 'user_id' => Auth::user()->id
                , 'time' => $time]);
            return redirect('admin/mt');
        }
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
        $Message = Message::find($id);
        if (is_null($Message->IsysURL)) {
            $URL = url('get');
            if(strpos($Message->MTURL,$URL) !== 0){
                $ID = explode('/',$Message->MTURL);
                $Filecheck = Upload::where('fid','=',end($ID))->get();
                if($Filecheck->isEmpty()){

                }else{
                    $File = Upload::where('fid','=',end($ID))->first();
                    File::delete(public_path($File->path));
                    Upload::destroy($File->id);
                }
            }else{

            }
            Message::destroy($id);
        }
        return redirect()->back();
    }

    /**
     * This function approves Message to be sent
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve($id)
    {

        Message::find($id)->update(['status'=>true]);
        return redirect()->back();
    }

    /**
     * filter messages
     * @return \Illuminate\View\View
     */
    public function filter()
    {

        $Req = Request::capture();
        $Service = $Req->get('service_id');
        $date = $Req->get('date');
        $status = $Req->get('status');


        $Messages = Message::where('date', '=', Carbon::now()->format('Y-m-d'))->where('IsysURL', '=', null)->whereNull('time')->orWhere('date', '=', Carbon::now()->format('Y-m-d'))->where('IsysResponse', '!=', 'OK')->whereNull('time')->get();



     if (is_null(Session::get('OpID')) || Auth::user()->admin == true) {
            if($Service == '0' && empty($date) && $status == '2'){ // get all
                $Messages = Message::paginate(20);
            }elseif($Service == '0' && $status == '2' && !empty($date)){ // search by date
                $Messages = Message::where('date','=',$date)->paginate(20);
            }elseif($Service !== '0' && empty($date) && $status == '2'){   // search by service_id
                $Messages = Message::where('service_id','=',$Service)->paginate(20);
            }elseif($Service == '0' && empty($date) && $status !== '2'){  // search by status
                $Messages = Message::where('status','=',$status)->paginate(20);
            }elseif($Service !== '0' && empty($date) && $status !== '2'){   // search by service_id + status
                $Messages = Message::where('status','=',$status)->where('service_id','=',$Service)->paginate(20);
            }elseif($Service == '0' && !empty($date) && $status !== '2'){  // search by date +status
                $Messages = Message::where('status','=',$status)->where('date','=',$date)->paginate(20);
            }elseif($Service !== '0' && !empty($date) && $status == '2'){ // search by service+date
                $Messages = Message::where('service_id','=',$Service)->where('date','=',$date)->paginate(20);
            }elseif($Service !== '0' && !empty($date) && $status !== '2'){ // search by service+date+status
                $Messages = Message::where('service_id','=',$Service)->where('date','=',$date)->where('status','=',$status)->paginate(20);
            }
        } else {  // this query not correct as it make condition  date or status
            $Messages = Message::where('service_id', '=', Session::get('OpID'))->where(function ($q) use ($date,$status){
                $q->where('date', 'LIKE', $date)->orWhere('status', '=', $status);
            })->paginate(20);
        }
        //var_dump($date);
        return view('backend.filter', compact('Messages'));
    }

    /**
     * Search messages with Body or URL or shorted URL
     * @return \Illuminate\View\View
     */
    public function search()
    {
        $Req = Request::capture();
        $Search =trim($Req->input('search')) ;
        if (is_null(Session::get('OpID')) || Auth::user()->admin == true) {
            $Messages = Message::where('MTBody', 'LIKE', '%'.$Search.'%')->orWhere('MTURL', 'LIKE', '%'.$Search.'%')->orWhere('ShortnedURL', 'LIKE', '%'.$Search.'%')->paginate(20);
            //echo 'Bla7';
        } else {
            $Messages = Message::where('service_id', '=', Session::get('OpID'))->where(function ($q) use ($Search){
                $q->where('MTBody', 'LIKE', '%'.$Search.'%')->orWhere('MTURL', 'LIKE', '%'.$Search.'%')->orWhere('ShortnedURL', 'LIKE', '%'.$Search.'%');
            })->paginate(20);
        }
        return view('backend.search', compact('Messages'));
    }

    public function sync()
    {
        $Req = Request::capture();
        Message::create($Req->all());
        return 'Done !!';
    }

    public function shortURLs(){
     //   $Messages = Message::where('date','>',Carbon::now()->format('2017-02-26'))->get();
		$Messages = Message::where('date','>',Carbon::now()->format('Y-m-d'))->get();
        foreach ($Messages as $Message){
            $Service = Service::find($Message->service_id);
            $MessageDetails = Message::find($Message->id);
            $ShortnedURL = $this->ShortURL(trim($Message->MTURL),$Message->date,$Service->ExURL);
            $MessageDetails->update(['ShortnedURL'=>$ShortnedURL]);
        }
        echo 'Done !!';

    }


}
