<?php namespace App\Http\Controllers;

use App\Activation;
use App\Http\Controllers\Controller;
use App\Url;
use App\Subscriber;
use App\Service;
use App\Charge;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Validator;

class UrlController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        //return view('createurl');
        return view('createurl');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
        $Input = Request::capture();

        $URL = $Input->URL;
        $ExDate = date('Y-m-d', strtotime($Input->ExDate . '+1day'));
        $ExURL = $Input->ExURL;

        //var_dump($ExDate);
        //return $Input->all();
        $MaxVisits = (empty($Input->MaxVisits)) ? null : $Input->MaxVisits;
        $return = $this->store($URL, $ExDate, $MaxVisits, $ExURL);
        Response::create($return);
        $ShortenURL = url('', $return);

        return view('created')->with('ShortenURL', $ShortenURL);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store($URL, $ExDate, $ExVisits, $ExURL)
    {
        //
        $Insert = new Url();
        $Insert->URL = $URL;
        $Insert->ExDate = (!isset($ExDate) || empty($ExDate)) ? 0 : $ExDate;
        $Insert->ExVisits = (!isset($ExVisits) || empty($ExDate)) ? 0 : $ExVisits;
        $Insert->ExURL = $ExURL;
        $Insert->save();
        $ID = $Insert->id;
        $Update = Url::find($ID);
        $Update->save();
        $UID = rand(100, 999) . $ID;
        $Update->UID = $UID;
        $Update->save();

        return $UID;

    }
    public function APICreate()
    {
        // echo "ffff"; die;
        $Create = Request::capture();
        $URL = $Create->URL;
        $ExDate = $Create->ExDate;
        $MaxVists = $Create->MaxVisits;
        $ExURL = $Create->ExURL;
        $ID = $this->store($URL, $ExDate, $MaxVists, $ExURL);
        //  echo $ID ; die;

        return Response::create(url('', $ID));
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
        $NewID = substr($id, 3);
        $test = Url::find($NewID);

        $Visits = intval($test->Visits);

        $SessionKey = Session::get('Visit');
        if (empty($SessionKey)) {
            Session::put('Visit', str_random(16));
            $test->Visits = $Visits + 1;
            $test->save();
        } else {

        }
        //Session::put('key', str_random(16));
        //return Session::get('Visit') .'  '.$test->Visits;

        $TodayDate = date('Y-m-d', strtotime('now'));
        if (strtotime($TodayDate) > strtotime($test->ExDate)) {
            return Redirect::to($test->ExURL);
            //return 'Date';

        } else {
            if (intval($test->ExVisits) == 0) {
                return Redirect::to($test->URL);
                //return 'Unlimited Visits';

            } else {

                if (intval($test->Visits) > intval($test->ExVisits)) {
                    return Redirect::to($test->ExURL);
                    //return 'Maximum Visits';
                } else {
                    return Redirect::to($test->URL);
                    //return 'Workin URL';
                }
            }

        }

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
    }

    public function test2(Request $request)
    {

        $date = date("Y-m-d h:i:sa");
        $ip = $request->ip();

        $position = \Location::get($ip);

        if ($position) {
            $country = $position->countryName;
        } else {
            $country = $position;
        }

        $data = ['date' => $date, 'ip' => $ip, 'country' => $country];

        $validator = Validator::make($request->all(), [
            'trxid' => 'required',
            'msisdn' => 'required',
            'serviceid' => 'required',
            'plan' => 'required',
            'price' => 'required',
        ]);

        if ($validator->fails()) {

            $data = array_merge($data, (array) $request->all(), (array) $validator->errors()->all());

            $this->log('failed', url('/activation'), $data);

            return response()->json(["result" => "FAILED", 'error' => implode(', ', $validator->errors()->all())], 401);

        } else {

            $activation = new Activation;

            if ($request->filled('trxid')) {
                $activation->trxid = $request->trxid;
            }
            if ($request->filled('msisdn')) {
                $activation->msisdn = $request->msisdn;
            }
            if ($request->filled('serviceid')) {
                $activation->serviceid = $request->serviceid;
            }
            if ($request->filled('plan')) {
                $activation->plan = $request->plan;
            }
            if ($request->filled('price')) {
                $activation->price = $request->price;
            }

            $activation->save();

            $activation_id = $activation->id;

        }

        $array = ["result" => "SUCCESS", "reason" => "The user has been successfully activated"];

        $data = array_merge($data, (array) $request->all(), $array);

        $this->log('success', url('/activation'), $data);

        $du_response_success = '<?xml version="1.0" encoding="UTF-8"?>
                                <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                                    <soap:Body>
                                    <ns2:purchaseConsumeProductResponse xmlns:ns2="http://ws.api.sdp.ericsson.com/" xmlns:ns3="http://ws.drutt.com/msdp/commerce/v2" xmlns:ns4="http://ws.drutt.com/msdp/userprofile-v2" xmlns:ns5="http://ws.drutt.com/msdp/commerce/v7" xmlns:ns6="http://ws.drutt.com/msdp/commerce">
                                        <return>
                                            <purchaseId>68225090070.PRCH</purchaseId>
                                            <purchaseIsDone>false</purchaseIsDone>
                                            <statusCode>0</statusCode>
                                            <subscriptionUsed>true</subscriptionUsed>
                                            <ticketConsumed>false</ticketConsumed>
                                            <transactionId>68225090088.PRTR</transactionId>
                                        </return>
                                    </ns2:purchaseConsumeProductResponse>
                                    </soap:Body>
                                </soap:Envelope>
                                ';

        $du_already = '<?xml version="1.0" encoding="UTF-8"?>
                        <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                                <soap:Body>
                                <soap:Fault>
                                    <faultcode>soap:Server</faultcode>
                                    <faultstring>503 - product already purchased!</faultstring>
                                    <detail>503 - product already purchased!</detail>
                                </soap:Fault>
                                </soap:Body>
                            </soap:Envelope>';

        $du_insufficient = '<?xml version="1.0" encoding="UTF-8"?>
                            <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                                <soap:Body>
                                <soap:Fault>
                                    <faultcode>soap:Server</faultcode>
                                    <faultstring>24 - Insufficient funds.</faultstring>
                                    <detail>24 - Insufficient funds.</detail>
                                </soap:Fault>
                                </soap:Body>
                            </soap:Envelope>';

        // log Du result Code   0 = mean success

        $doc = new \DOMDocument('1.0', 'utf-8');
        $doc->loadXML($du_response_success);
        $statusCode = $doc->getElementsByTagName("statusCode"); // success
        $faultstring = $doc->getElementsByTagName("faultstring"); // insufficient or alreday subscribe

        if ($statusCode->length != 0) { // find results
            $status = $statusCode->item(0)->nodeValue;
        } elseif ($faultstring->length != 0) {
            $status = $faultstring->item(0)->nodeValue;
        } else {
            $status = "";
        }

        if( $status == 0){
            $this->successfulSubs($activation->id);
        }

        // log to DB + files
        $act = Activation::findOrFail($activation->id);
        $act->du_request = "Du reques";
        $act->du_response = $du_response_success;
        $act->status_code = $status;
        $act->save();
        $this->log('du Flatter Daily Billing', url('/activation'), $data);

    }

    public function successfulSubs($id){//activation id

// new activition
      $activation  = Activation::where('id', $id)->first();

// search if old for the same service and the same msisdn

         $old_subscriber = \DB::table('subscribers')
         ->join('activation', 'subscribers.activation_id', '=', 'activation.id')
         ->where('activation.serviceid', $activation->serviceid)
         ->where('activation.msisdn', $activation->msisdn)
         ->select('activation.msisdn','subscribers.id')
         ->first();


            if($old_subscriber){ // update
            $subscriber =   Subscriber::where('id',$old_subscriber->id )->first();
            $subscriber->activation_id =$id;
            }else{  // create new
            $subscriber = new Subscriber;
            $subscriber->activation_id = $id;
            }



        $today = Carbon::now()->format('Y-m-d');

        if($activation->plan == 'weekly'){
            $next_charging_date = Carbon::now()->addDays(7)->format('Y-m-d');
        }else{
            $next_charging_date = Carbon::now()->addDays(1)->format('Y-m-d');
        }
        $subscriber->next_charging_date = $next_charging_date;
        $subscriber->subscribe_date = $today;
        $subscriber->final_status = 1;
        $subscriber->charging_cron = 0;
        $subscriber->save();
        return $subscriber->id ;

        //$this->chargeSubs();

    }

    public function chargeSubs(){

        $timeout = 60000000000;

        $email =  "emad@ivas.com.eg" ;
        $subject = "Charging Cron Schedule for ".Carbon::now()->format('Y-m-d') ;
        $this->sendMail($subject, $email);

        $services = Activation::select('serviceid')->groupBy('serviceid')->get();

        $today = Carbon::now()->format('Y-m-d');

        foreach($services as $service){
            $subscribers = \DB::table('activation')
                        ->join('subscribers', 'subscribers.activation_id', '=', 'activation.id')
                        ->where('activation.serviceid', $service->serviceid)
                        ->where('subscribers.next_charging_date', $today)
                        ->select('subscribers.*')
                        ->get();
            //dd($subscribers);
            foreach($subscribers as $sub){
                $activation = Activation::findOrFail($sub->activation_id);
                $old_sub = Subscriber::findOrFail($sub->id);
                $serviceid = $activation->serviceid ;
                $msisdn =    $activation->msisdn ;


            //    if($activation->serviceid == 'flaterdaily' ||$activation->serviceid == 'flaterweekly' ){  // flatter daily , flater weekly
                            // Du First Billing or new billing
                            $serviceid =  $activation->serviceid ;
                            $msisdn =  $activation->msisdn ;

                            $charge_renew_result =   $this->du_charge_per_service($activation,$serviceid, $msisdn,$sub,$send_welcome_message=Null) ;

              //  }





                if($charge_renew_result  == 1 ){  // renew charge success

                    if($activation->plan == 'daily'){
                        $old_sub->next_charging_date = date('Y-m-d',strtotime($sub->next_charging_date  . "+1 day"));
                        $old_sub->save();
                    }
                    elseif($activation->plan == 'weekly'){
                        $old_sub->next_charging_date = date('Y-m-d',strtotime($sub->next_charging_date  . "+1 week"));
                        $old_sub->save();
                    }else{ // default is daily
                        $old_sub->next_charging_date = date('Y-m-d',strtotime($sub->next_charging_date  . "+1 day"));
                        $old_sub->save();
                    }

                }

            }
        }

        echo "Du Charging for toady ". $today. "Is Done" ;
    }


    public function sendMail($subject, $email, $Message = NULL) {

        // send mail
        $message = '<!DOCTYPE html>
					<html lang="en-US">
						<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
						</head>
						<body>
							<h2>' . $subject . '</h2>



						</body>
					</html>';

        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $headers .= 'From: DU SYSTEM';

        @mail($email, $subject, $message, $headers);
    }

    // get all subscriber with message
    public function sendTodaySubMessage()
    {

        $timeout = 60000000000;
        $result = 0 ;
        $email =  "emad@ivas.com.eg" ;
        $subject = "SMS Cron Schedule sending for ".Carbon::now()->format('Y-m-d') ;
        $this->sendMail($subject, $email);

        $all = [];
        $services = Service::all();
        $today = Carbon::now()->format('Y-m-d');
        $message_type = "Today_Messages_Schedule";
        foreach ($services as $key => $service) {
            $data['serviceId'] = $service->title;
            $subscribers =  Activation::join('subscribers', 'subscribers.activation_id', '=', 'activation.id')
                            ->where('activation.serviceid', $service->title)
                            ->select('activation.msisdn as msisdn','activation.serviceid as serviceid','subscribers.id as sub_id')
                            ->get();


            $data['msisdns'] = $subscribers;
            if($subscribers->count() > 0){
                $message = \App\Message::where('service_id',$service->id)->where('date',$today)->first();
                if($message){
                    $data['message'] = $message->MTBody.' '.$message->ShortnedURL;
                    array_push($all,$data);

                    foreach($subscribers  as $sub){
                           // Du sending welcome message
                           $serviceid =  $sub->serviceid ;
                           $msisdn =  $sub->msisdn ;
                           $mes =  $data['message'];


                  $result = $this->du_send_message($serviceid , $msisdn ,   $mes ,$message_type  );

                    }

                   // update today message status
                   if( $result == "1"){
                        $message->IsysResponse = 'OK' ;
                        $message->save() ;


                        $send_array["Date"] = Carbon::now()->format('Y-m-d H:i:s');
                        $send_array["DU_send_message_result"] = $result;
                        $send_array["message"] =  $data['message'];
                        $send_array["message_id"] = $message->id;
                        $send_array["service"] = $service->title ;
                        $this->log('Du Today Send Message for '. $service->title .' service', url('/sendTodaySubMessage'), $send_array);
                    }
                }
            }
        }
        return $message_type." Is Send";
    }

    /*****************/


    public function getMessage($id){
        $today = Carbon::now()->format('Y-m-d');
        $service = Service::where('title',$id)->first();
        $message = \App\Message::where('service_id',$service->service_id)->where('date',$today)->first();
        $today_message = '';
        if($message){
            $today_message = $message->MTBody.' '.$message->ShortnedURL;
        }
        return    $today_message ;
    }



    public function du_charge_per_service($activation,$serviceid, $msisdn,$sub=Null,$send_welcome_message=Null)
    {

        $charge_renew_result = 0 ;

        $date = date("Y-m-d h:i:sa");
        $today = date("Y-m-d");


            $activation_id = $activation->id;

            // here make Du billing
            // Config
            $client = new \nusoap_client('du_integration/du-domain.wsdl', 'wsdl');
            $client->soap_defencoding = 'UTF-8';
            $client->decode_utf8 = false;

            if(isset($activation) && isset($serviceid) && isset($msisdn)) {



            if ($serviceid == "flaterdaily") {

                $service_name = "Flatter Daily" ;
                // header authentication
                $username = "P-7SYBYFVSWA-@S-r5ZBYFVSWA-";
                $password = "P-7SYBYFVSWA-#1234";

                // service parameters
                //  $userId = "971529204634" ;
                $userId = $msisdn;
                $serviceId = "S-r5ZBYFVSWA-";
                $premiumResourceType = "MP-PRT-IVAS-Flater-B2-D-Sub";
                $productId = "Daily Flater B2 MP IVAS Sub";

                $client->setCredentials($username, $password);
                $error = $client->getError();

                $purchaseMetas = array(
                    "key" => "du:assetDescription",
                    "value" => "IVAS TEST",
                );

                $billingMetas = array(
                    array(
                        "key" => "du:assetID",
                        "value" => "A-cMShAk6_L13",

                    ),
                    array(

                        "key" => "du:contentType",
                        "value" => "mobileApp",

                    ),
                    array(

                        "key" => "du:channel",
                        "value" => "COMMERCE_API",

                    ),

                );

                $usageMetas = array(
                    "key" => "du:externalid",
                    "value" => "X12345",
                );

                $result = $client->call("purchaseConsumeProduct", array(
                    "userId" => $userId,
                    "serviceId" => $serviceId,
                    "premiumResourceType" => $premiumResourceType,
                    "productId" => $productId,
                    "purchaseMetas" => $purchaseMetas,
                    "billingMetas" => $billingMetas,
                    "usageMetas" => $usageMetas,

                ));



                $data["Date"] = Carbon::now()->format('Y-m-d H:i:s');
                $data["Request"] = $client->request;
                $data["Response"] = $client->responseData;

                $doc = new \DOMDocument('1.0', 'utf-8');
                $doc->loadXML($client->responseData);
                $statusCode = $doc->getElementsByTagName("statusCode"); // success
                $faultstring = $doc->getElementsByTagName("faultstring"); // insufficient or alreday subscribe

                if ($statusCode->length != 0) { // find results
                    $status = $statusCode->item(0)->nodeValue;
                    $charge_renew_result = 1 ;

                    // store new subscriber
                    if( $status == 0){
                        if($send_welcome_message != Null){ // billing for the first time so register new subscriber
                            $sub_id =  $this->successfulSubs( $activation_id );
                        }else{ // renew charging success
                            $charge_renew_result = 1 ;
                            $sub_id = "" ;
                        }

                    }else{
                        $sub_id = "" ;
                    }

                } elseif ($faultstring->length != 0) {
                    $status = $faultstring->item(0)->nodeValue ;
                    $charge_renew_result = 0 ;
                    if($status == "503 - product already purchased!"){  // aready subscribe
                        $Subscriber =   Subscriber::where('activation_id',$activation_id)->first() ;
                        if(  $Subscriber) {
                            $sub_id = $Subscriber->id;
                            $Subscriber->next_charging_date = date('Y-m-d',strtotime($Subscriber->next_charging_date  . "+1 day"));
                            $Subscriber->save();
                        }else{ // create new one
                            $sub_id =  $this->successfulSubs( $activation_id );
                        }

                    }else{
                        $sub_id = "" ;
                    }

                }else{
                    $charge_renew_result = 0 ;
                    $status = "Not Known Error" ;
                    $sub_id = "" ;
                }

                $data["statusCode"] =$status ;

                  // log billing
                  if($sub != Null ){
                   $billing_message = "Renew" ;
                  }else{
                    $billing_message = "FirstTime" ;
                  }
                $this->log('Du '.$serviceid.' Billing '.$billing_message .' Log', url('/du_charge_per_service'), $data);

            } elseif ($serviceid== "flaterweekly") {
                $service_name = "Flatter Weekly" ;
                // header authentication
                $username = "P-7SYBYFVSWA-@S-r5ZBYFVSWA-";
                $password = "P-7SYBYFVSWA-#1234";

                // service parameters
                //  $userId = "971529204634" ;
                $userId = $msisdn;
                $serviceId = "S-r5ZBYFVSWA-";
                $premiumResourceType = "MP-PRT-IVAS-Flater-B14-W-Sub";
                $productId = "Weekly Flater B14 MP IVAS Sub";

                $client->setCredentials($username, $password);
                $error = $client->getError();

                $purchaseMetas = array(
                    "key" => "du:assetDescription",
                    "value" => "IVAS TEST",
                );

                $billingMetas = array(
                    array(
                        "key" => "du:assetID",
                        "value" => "A-cMShAk6_L13",

                    ),
                    array(

                        "key" => "du:contentType",
                        "value" => "mobileApp",

                    ),
                    array(

                        "key" => "du:channel",
                        "value" => "COMMERCE_API",

                    ),

                );

                $usageMetas = array(
                    "key" => "du:externalid",
                    "value" => "X12345",
                );

                $result = $client->call("purchaseConsumeProduct", array(
                    "userId" => $userId,
                    "serviceId" => $serviceId,
                    "premiumResourceType" => $premiumResourceType,
                    "productId" => $productId,
                    "purchaseMetas" => $purchaseMetas,
                    "billingMetas" => $billingMetas,
                    "usageMetas" => $usageMetas,

                ));


                $data["Date"] = Carbon::now()->format('Y-m-d H:i:s');
                $data["Request"] = $client->request;
                $data["Response"] = $client->responseData;

                $doc = new \DOMDocument('1.0', 'utf-8');
                $doc->loadXML($client->responseData);
                $statusCode = $doc->getElementsByTagName("statusCode"); // success
                $faultstring = $doc->getElementsByTagName("faultstring"); // insufficient or alreday subscribe

                if ($statusCode->length != 0) { // find results
                    $status = $statusCode->item(0)->nodeValue;
                    $charge_renew_result = 1 ;


                     // store new subscriber
                     if( $status == 0){
                        if($send_welcome_message != Null){ // billing for the first time  so register new subscriber
                            $sub_id =  $this->successfulSubs($activation_id);
                        }else{ // renew charging success
                            $charge_renew_result = 1 ;
                            $sub_id = "" ;
                        }

                    }

                } elseif ($faultstring->length != 0) {
                    $status = $faultstring->item(0)->nodeValue ;
                    $charge_renew_result = 0 ;
                    if($status == "503 - product already purchased!"){  // aready subscribe
                        $Subscriber =   Subscriber::where('activation_id',$activation_id)->first() ;
                        if(  $Subscriber) {
                            $sub_id = $Subscriber->id;
                        }else{ // create new one
                            $sub_id =  $this->successfulSubs( $activation_id );
                        }

                    }else{
                        $sub_id = "" ;
                    }

                }else{
                    $charge_renew_result = 0 ;
                    $status = "Not Known Error" ;
                    $sub_id = "" ;
                }


                $data["statusCode"] =$status ;

                  // log billing
                  if($sub != Null ){
                   $billing_message = "Renew" ;
                  }else{
                    $billing_message = "FirstTime" ;
                  }
                $this->log('Du '.$serviceid.' Billing '.$billing_message .' Log', url('/du_charge_per_service'), $data);


            }



                // log new charge renew into DB
                if($sub != Null){  // renew charging
                $subscriber_id =$sub->id ;
                }elseif( $sub_id != ""){  // billing for the first time
                       $subscriber_id =  $sub_id ;

                    // edit activition for the first time of billing
                    $act = Activation::findOrFail($activation->id);
                    $act->du_request =  $client->request;;
                    $act->du_response = $client->responseData;
                    $act->status_code = $status;
                    $act->save();
                }else{
                    $subscriber_id = "" ;
                }



                // log charging for First Time Or renew
                if($subscriber_id != "" ){
                    $Charge = new Charge;
                    $Charge->subscriber_id =  $subscriber_id ;
                    $Charge->billing_request = $client->request;
                    $Charge->billing_response = $client->responseData;
                    $Charge->charging_date = $today ;
                    $Charge->status_code = $status  ;
                    $Charge->save() ;
                }


                  // renew charging log
                //   if($sub != Null ){
                //     $data["charging_du_result"] = $status;
                //     $data["serviceid"] = $serviceid;
                //     $data["msisdn"] = $msisdn;
                //     $data["charge_renew_result"] = $charge_renew_result;
                //     $this->log('Du '.$serviceid.' charging renew', url('/du_charge_per_service'), $data);
                //     }


                if($send_welcome_message != Null) {
                    // log to DB + files
                    $act = Activation::findOrFail($activation->id);
                    $act->du_request = $client->request;
                    $act->du_response = $client->responseData;
                    $act->status_code =  $status;
                    $act->save();
                    $this->log('Du '.$serviceid.'  First Billing', url('/activation'), $data);


                 //  welcomemessage for each service
                   $welcome_message = $this->getMessage($serviceid);


                     // Du sending welcome message
                     $du_welcome_message = "welcome to  ". $service_name ."  service ";
                     $du_welcome_message .= $welcome_message;
                     $message_type = "Welcome Message" ;
                    $this->du_send_message($service_name ,$msisdn,$du_welcome_message , $message_type);


                }

                return    $charge_renew_result  ;  // success
        }else{
                return 0   ;  // fail
        }

    }



    public function activation(Request $request)
    {

        $date = date("Y-m-d h:i:sa");
        $ip = $request->ip();

        $position = \Location::get($ip);

        if ($position) {
            $country = $position->countryName;
        } else {
            $country = $position;
        }

        $data = ['date' => $date, 'ip' => $ip, 'country' => $country];

        $validator = Validator::make($request->all(), [
            'trxid' => 'required',
            'msisdn' => 'required',
            'serviceid' => 'required',
            'plan' => 'required',
            'price' => 'required',
        ]);

        if ($validator->fails()) {

            $data = array_merge($data, (array) $request->all(), (array) $validator->errors()->all());

            $this->log('failed', url('/activation'), $data);

            return response()->json(["result" => "FAILED", 'error' => implode(', ', $validator->errors()->all())], 401);

        } else {

            $activation = new Activation;

            if ($request->filled('trxid')) {
                $activation->trxid = $request->trxid;
            }
            if ($request->filled('msisdn')) {
                $activation->msisdn = $request->msisdn;
            }
            if ($request->filled('serviceid')) {
                $activation->serviceid = $request->serviceid;
            }
            if ($request->filled('plan')) {
                $activation->plan = $request->plan;
            }
            if ($request->filled('price')) {
                $activation->price = $request->price;
            }

            $activation->save();

              $activation_id = $activation->id;
              $activation = Activation::findOrFail($activation->id);

            $array = ["result" => "SUCCESS", "reason" => "The user has been successfully activated"];

            $data = array_merge($data, (array) $request->all(), $array);

            $this->log('success', url('/activation'), $data);


            // Du First Billing or new billing
            if($activation ){
                $serviceid =  $activation->serviceid ;
                $msisdn =  $activation->msisdn ;
                $send_welcome_message = true ;

                $this->du_charge_per_service($activation,$serviceid, $msisdn,$sub=Null,$send_welcome_message) ;
            }


            return json_encode($array);
        }

    }




    public function du_send_message($service_name ,$msisdn,$message , $message_type)
    {
                    // Du sending welcome message
                    $URL = DU_SMS_SEND_MESSAGE;
                    $param = "phone_number=" . $msisdn . "&message=" .$message ;
                    $result = $this->get_content_post($URL, $param);
                    $send_array = array();

                    if ($result == "1") {
                    $message_mean = "Du message sent success";

                    } else {
                    $message_mean = "Du message sent fail";
                    }

                    $send_array["Date"] = Carbon::now()->format('Y-m-d H:i:s');
                    $send_array["du_sms_result"] = $result;
                    $send_array["du_message_mean"] = $message_mean;
                    $this->log('Du Send Message', url('/du_send_message'), $send_array);

                    return $result ;
    }



    public function get_content_post($URL, $param)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    public function test()
    { // test flatter Daily

// Config
        $client = new \nusoap_client('du_integration/du-domain.wsdl', 'wsdl');
        $client->soap_defencoding = 'UTF-8';
        $client->decode_utf8 = false;

// header authentication
        $username = "P-7SYBYFVSWA-@S-r5ZBYFVSWA-";
        $password = "P-7SYBYFVSWA-#1234";

// service parameters
        $userId = "971529204634";
        $serviceId = "S-r5ZBYFVSWA-";
        $premiumResourceType = "MP-PRT-IVAS-Flater-B2-D-Sub";
        $productId = "Daily Flater B2 MP IVAS Sub";

        $client->setCredentials($username, $password);
        $error = $client->getError();

        $purchaseMetas = array(
            "key" => "du:assetDescription",
            "value" => "IVAS TEST",
        );

        $billingMetas = array(
            array(
                "key" => "du:assetID",
                "value" => "A-cMShAk6_L13",

            ),
            array(

                "key" => "du:contentType",
                "value" => "mobileApp",

            ),
            array(

                "key" => "du:channel",
                "value" => "COMMERCE_API",

            ),

        );

        $usageMetas = array(
            "key" => "du:externalid",
            "value" => "X12345",
        );

        if ($error) {
            echo "<h2>Constructor error</h2><pre>" . $error . "</pre>";
        }

        $result = $client->call("purchaseConsumeProduct", array(
            "userId" => $userId,
            "serviceId" => $serviceId,
            "premiumResourceType" => $premiumResourceType,
            "productId" => $productId,
            "purchaseMetas" => $purchaseMetas,
            "billingMetas" => $billingMetas,
            "usageMetas" => $usageMetas,

        ));

        if ($client->fault) {
            echo "<h2>Fault</h2><pre>";
            print_r($result);
            echo "</pre>";
        } else {
            $error = $client->getError();
            if ($error) {
                echo "<h2>Error</h2><pre>" . $error . "</pre>";
            } else {
                echo "<h2>Main</h2>";
                print_r($result); // MOResult

            }
        }

// show soap request and response
        echo "<h2>Request</h2>";
        echo "<pre>" . $client->request . "</pre>";
        echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';

        echo "<h2>Response</h2>";
        echo "<pre>" . $client->response . "</pre>";
        echo '<h2>Response</h2><pre>' . htmlspecialchars($client->responseData, ENT_QUOTES) . '</pre>';

        $data["Date"] = Carbon::now()->format('Y-m-d H:i:s');
        $data["Request"] = $client->request;
        $data["Response"] = $client->responseData;

        $act = Activation::findOrFail(1);
        $act->du_request = $client->request;
        $act->du_response = $client->responseData;
        $act->save();

        $this->log('du Billing', url('/test'), $data);

    }

    public function log($actionName, $URL, $parameters_arr)
    {

        date_default_timezone_set("Africa/Cairo");
        $date = date("Y-m-d");
        $log = new Logger($actionName);
        // to create new folder with current date  // if folder is not found create new one
        if (!\File::exists(storage_path('logs/' . $date . '/' . $actionName))) {
            \File::makeDirectory(storage_path('logs/' . $date . '/' . $actionName), 0775, true, true);
        }

        $log->pushHandler(new StreamHandler(storage_path('logs/' . $date . '/' . $actionName . '/logFile.log', Logger::INFO)));
        $log->addInfo($URL, $parameters_arr);
    }




    /***************** */
}
