<?php namespace App\Http\Controllers;

use App\Activation;
use App\Charge;
use App\Http\Controllers\Controller;
use App\Service;
use App\Subscriber;
use App\Message;
use App\Url;
use App\DuMo;
use App\LogMessage;
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

        if ($status == 0) {
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

    public function successfulSubs($id)
    { //activation id

// new activition
        $activation = Activation::where('id', $id)->first();

// search if old for the same service and the same msisdn

        $old_subscriber = \DB::table('subscribers')
            ->join('activation', 'subscribers.activation_id', '=', 'activation.id')
            ->where('activation.serviceid', $activation->serviceid)
            ->where('activation.msisdn', $activation->msisdn)
            ->select('activation.msisdn', 'subscribers.id')
            ->first();

        if ($old_subscriber) { // update
            $subscriber = Subscriber::where('id', $old_subscriber->id)->first();
            $subscriber->activation_id = $id;
        } else { // create new
            $subscriber = new Subscriber;
            $subscriber->activation_id = $id;
        }

        $today = Carbon::now()->format('Y-m-d');

        if ($activation->plan == 'weekly') {
            $next_charging_date = Carbon::now()->addDays(7)->format('Y-m-d');
        } else {
            $next_charging_date = Carbon::now()->addDays(1)->format('Y-m-d');
        }
        $subscriber->next_charging_date = $next_charging_date;
        $subscriber->subscribe_date = $today;
        $subscriber->final_status = 1;
        $subscriber->charging_cron = 0;
        $subscriber->save();
        return $subscriber->id;

        //$this->chargeSubs();

    }

    public function chargeSubs()
    {

        $today = Carbon::now()->format('Y-m-d');
        $subscribers = Subscriber::where('subscribers.next_charging_date', $today)
        ->select('subscribers.*')
        ->orderBy('id', 'ASC')
        ->get();


        $subscribers_count = Subscriber::where('subscribers.next_charging_date', $today)
        ->orderBy('id', 'ASC')
        ->count();


        if( $subscribers_count > 0 ){  // run

            $email = "emad@ivas.com.eg";
            $subject = "Charging Cron Run Schedule for " . Carbon::now()->format('Y-m-d');
            $this->sendMail($subject, $email);

            foreach ($subscribers as $sub) {
                $activation = Activation::findOrFail($sub->activation_id);
                $old_sub = Subscriber::findOrFail($sub->id);
                $serviceid = $activation->serviceid;
                $msisdn = $activation->msisdn;

                $charge_renew_result = $this->du_charge_per_service($activation, $serviceid, $msisdn, $sub, $send_welcome_message = null);

            }

            echo "Du Charging for toady " . $today . " Is Done";

        }else{
            echo "There is no  Charging for toady " . $today ;

        }



    }

    public function chargeSubs_for_failed()
    {

        $today = Carbon::now()->format('Y-m-d');
        $subscriber_ids = Charge::where('charging_date',date('Y-m-d'))->groupBy('subscriber_id')->pluck('subscriber_id')->toArray();
        $subscribers = Subscriber::whereNotIN('id',$subscriber_ids)
        ->where('next_charging_date',date('Y-m-d'))
        ->orderBy('id', 'ASC')
        ->get();

        $subscribers_count = Subscriber::whereNotIN('id',$subscriber_ids)
        ->where('next_charging_date',date('Y-m-d'))
        ->orderBy('id', 'ASC')
        ->count();


        if( $subscribers_count > 0 ){  // run

            $email = "emad@ivas.com.eg";
            $subject = "Charging Cron Run Schedule for " . Carbon::now()->format('Y-m-d');
            $this->sendMail($subject, $email);


            foreach ($subscribers as $sub) {
                $activation = Activation::findOrFail($sub->activation_id);
                $old_sub = Subscriber::findOrFail($sub->id);
                $serviceid = $activation->serviceid;
                $msisdn = $activation->msisdn;


                $charge_renew_result = $this->du_charge_per_service($activation, $serviceid, $msisdn, $sub, $send_welcome_message = null);

                $today_charging = Charge::where("subscriber_id",$sub->id)->where("charging_date",$today)->first();

            }

            echo "Du Charging for toady " . $today . " Is Done";


        }else{
            echo "There is no  Charging for toady " . $today;

        }


    }


    public function sendMail($subject, $email, $Message = null)
    {

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
      // call manuel because some cases there is many failes
      public function sendTodaySubMessageForFailed()
      {
        $today=date("Y-m-d") ;
        $failed_messages =  LogMessage::where('created_at','LIKE', $today."%")->where('status',0)->get();



      if ($failed_messages->count() > 0) {
        $email = "emad@ivas.com.eg";
        $subject = "SMS Cron Schedule sending for Failed Du Kannel" . Carbon::now()->format('Y-m-d');
        $this->sendMail($subject, $email);

        foreach($failed_messages as $failed_message){
            $serviceid =  $failed_message->service;
            $msisdn =  $failed_message->msisdn;
            $mes =  $failed_message->message;
            $message_type =  $failed_message->message_type;
            $log_message_id =   $failed_message->id ;
            $result = $this->du_send_message($serviceid, $msisdn, $mes, $message_type,$log_message_id);

        }
      }

      echo "sendTodaySubMessageForFailed Done" ;

      }




      public function todayMessagesStatus(){


      // send mail group for today messages for all services
        $messages = \App\Message::where('status', '=', true)->where('date', '=', Carbon::now()->format('Y-m-d'))->get();

        $message = "" ;
        foreach($messages as $mes){
            $status =    $mes->IsysResponse == "OK" ? "Yes":"NO"  ;
            $message .= '<tr>
            <td>'.$mes->MTBody .'</td>
            <td><a href="'. $mes->ShortnedURL.'"> '. $mes->ShortnedURL .'</a></td>
            <td>'. $mes->service->service . '|'. $mes->service->operator->title.' -'. $mes->service->operator->country->name. '</td>
            <td>'.$status.'
            </td>
            </tr>' ;
        }


        $subject2 = 'Du Today Messages Status';
        $message2 = '<!DOCTYPE html>
        <html lang="en">
            <head>

            </head>
                <style>
        table {
          font-family: arial, sans-serif;
          border-collapse: collapse;
          width: 100%;
        }

        td, th {
          border: 1px solid #dddddd;
          text-align: left;
          padding: 8px;
        }

        tr:nth-child(even) {
          background-color: #dddddd;
        }


        </style>
            <body>
                <p><strong>Dears,</strong> <br>Kindly find Today Messages Status</p>
                <table cellpadding="10" >
                    <thead>
                        <tr>
                            <th>Message Body</th>
                            <th>Shorten URL</th>
                            <th>Service</th>
                            <th>Sent</th>
                        </tr>
                    </thead>
                    '.$message.'

            </table>

        </body>
        </html>';


        $recipients = array(
            "emad@ivas.com.eg",
            "dalia.soliman@ivas.com.eg",
            "sayed@ivas.com.eg",
            "raafat.ahmed@ivas.com.eg",
            "cr@ivas.com.eg",
            "saad@ivas.com.eg"
        );


$email = implode(',', $recipients);
        $headers2 = 'MIME-Version: 1.0' . "\r\n";
        $headers2 .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $headers2 .= 'From: DU';

        @mail($email, $subject2, $message2, $headers2);

        echo "todayMessagesStatus Done" ;

    }


    public function tomorrowMessagesStatus(){

        $messages = Message::where('date', '=', Carbon::tomorrow()->format('Y-m-d'))->where('status', '=', true)->get();

        $message = "" ;
        foreach($messages as $mes){
            $status =    $mes->status == 1 ? "Yes":"NO"  ;
            $message .= '<tr>
            <td>'.$mes->MTBody .'</td>
            <td><a href="'. $mes->ShortnedURL.'"> '. $mes->ShortnedURL .'</a></td>
            <td>'. $mes->service->service . '|'. $mes->service->operator->title.' -'. $mes->service->operator->country->name. '</td>
            <td>'.$status.'
            </td>
            </tr>' ;
        }


        $subject2 = 'DU Messages  that will sent tomorrow';
        $message2 = '<!DOCTYPE html>
        <html lang="en">
            <head>

            </head>
                <style>
        table {
          font-family: arial, sans-serif;
          border-collapse: collapse;
          width: 100%;
        }

        td, th {
          border: 1px solid #dddddd;
          text-align: left;
          padding: 8px;
        }

        tr:nth-child(even) {
          background-color: #dddddd;
        }


        </style>
            <body>
                <p><strong>Dears,</strong> <br>Kindly find Tommorrow Messages </p>
                <table cellpadding="10" >
                    <thead>
                        <tr>
                            <th>Message Body</th>
                            <th>Shorten URL</th>
                            <th>Service</th>
                            <th>Approved</th>
                        </tr>
                    </thead>
                    '.$message.'

            </table>

        </body>
        </html>';


        $recipients = array(
            "emad@ivas.com.eg",
            "dalia.soliman@ivas.com.eg",
            "sayed@ivas.com.eg",
            "raafat.ahmed@ivas.com.eg",
            "cr@ivas.com.eg",
            "saad@ivas.com.eg"
        );


$email = implode(',', $recipients);
        $headers2 = 'MIME-Version: 1.0' . "\r\n";
        $headers2 .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $headers2 .= 'From: DU';

        @mail($email, $subject2, $message2, $headers2);

        echo "tomorrowMessagesStatus Done" ;

    }



    // get all subscriber with message
    public function sendTodaySubMessage()
    {
        $result = 0;
        $email = "emad@ivas.com.eg";
        $subject = "SMS Cron Schedule sending for " . Carbon::now()->format('Y-m-d');
        $this->sendMail($subject, $email);

        $all = [];
        $services = Service::all();
        $today = Carbon::now()->format('Y-m-d');
        $message_type = "Today_Messages_Schedule";
        foreach ($services as $key => $service) {
            $data['serviceId'] = $service->title;

                $subscribers = \DB::table('subscribers')->join('activation', 'subscribers.activation_id', '=', 'activation.id')
                ->where('activation.serviceid', $service->title)
                ->select('activation.msisdn as msisdn', 'activation.serviceid as serviceid', 'subscribers.id as sub_id')
                ->get();

            $data['msisdns'] = $subscribers;
            if ($subscribers->count() > 0) {
                $message = \App\Message::where('service_id', $service->id)->where('date', $today)->whereNull('IsysResponse')->first();

                if ($message) {
                    $data['message'] = $message->MTBody . ' ' . $message->ShortnedURL;
                    array_push($all, $data);

                    foreach ($subscribers as $sub) {
                        // Du sending welcome message
                        $serviceid = $sub->serviceid;
                        $msisdn = $sub->msisdn;
                        $mes = $data['message'];

                        $result = $this->du_send_message($serviceid, $msisdn, $mes, $message_type,$log_message_id="");

                    }

                    // update today message status
                    if ($result == "1") {
                        $message->IsysResponse = 'OK';
                        $message->save();

                        $send_array["Date"] = Carbon::now()->format('Y-m-d H:i:s');
                        $send_array["DU_send_message_result"] = $result;
                        $send_array["message"] = $data['message'];
                        $send_array["message_id"] = $message->id;
                        $send_array["service"] = $service->title;
                        $this->log('Du Today Send Message for ' . $service->title . ' service', url('/sendTodaySubMessage'), $send_array);
                    }else{  // send email that today messages are failure
                        $email = "emad@ivas.com.eg";
                        $subject = "SMS Du Cron Schedule Failed for  " . Carbon::now()->format('Y-m-d');
                        $this->sendMail($subject, $email);
                    }
                }
            }
        }
        return $message_type . " Is Send";
    }

    /*****************/

    public function getMessage($id)
    {

        $today = Carbon::now()->format('Y-m-d');
        $service = Service::where('title', $id)->first();
        $message = Message::where('service_id', $service->id)->where('date', $today)->first();
        $today_message = '';
        if ($message) {
            $today_message = $message->MTBody . ' ' . $message->ShortnedURL;
        }
        return $today_message;
    }

    public function du_charge_per_service($activation, $serviceid, $msisdn, $sub = null, $send_welcome_message = null)
    {
        $secure_D_Pincode_success = secureD_Failed;
        $charge_renew_result = 0;
        $date = date("Y-m-d h:i:sa");
        $today = date("Y-m-d");

        $activation_id = $activation->id;

        // here make Du billing
        // Config
        $client = new \nusoap_client('du_integration/du-domain.wsdl', 'wsdl');
        $client->soap_defencoding = 'UTF-8';
        $client->decode_utf8 = false;

        if (isset($activation) && isset($serviceid) && isset($msisdn)) {

            if ($serviceid == "flaterdaily") {

                $service_name = "Flatter";
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
                    "value" => "Flatter",
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
                if(isset($client->responseData)   &&  $client->responseData !="" ){  // as sometimes xml load emty
                    $doc->loadXML($client->responseData);
                    $statusCode = $doc->getElementsByTagName("statusCode"); // success
                    $faultstring = $doc->getElementsByTagName("faultstring"); // insufficient or alreday subscribe

                    if ($statusCode->length != 0) { // find results
                        $status = $statusCode->item(0)->nodeValue;
                        $charge_renew_result = 1;

                        // store new subscriber
                        if ($status == 0) {
                            if ($send_welcome_message != null) { // billing for the first time so register new subscriber
                                $sub_id = $this->successfulSubs($activation_id);
                                $secure_D_Pincode_success = secureD_Success;
                            } else { // renew charging success
                                $charge_renew_result = 1;
                                $sub_id = "";
                            }

                        } else {
                            $sub_id = "";
                        }

                    } elseif ($faultstring->length != 0) {
                        $status = $faultstring->item(0)->nodeValue;
                        $charge_renew_result = 0;
                        if ($status == "503 - product already purchased!") { // aready subscribe
                            $secure_D_Pincode_success = secureD_product_already_purchased;

                            if ($send_welcome_message != null) {
                                $sub_id = $this->successfulSubs($activation_id);
                            } else {
                                $sub_id = "";
                            }



                        } elseif ($status == "24 - Insufficient funds.") {
                            $secure_D_Pincode_success = secureD_Insufficient_funds;

                            // insert in sub for the first time of susbcribe
                            if ($send_welcome_message != null) { // billing for the first time so register new subscriber
                                $sub_id = $this->successfulSubs($activation_id);
                            } else { // renew charging success
                                $charge_renew_result = 1;
                                $sub_id = "";
                            }

                        } else {
                            $sub_id = "";
                        }

                    } else {
                        $charge_renew_result = 0;
                        $status = "Not Known Error";
                        $sub_id = "";
                    }

                    $data["statusCode"] = $status;

                    // log billing
                    if ($sub != null) {
                        $billing_message = "Renew";
                    } else {
                        $billing_message = "FirstTime";
                    }
                    $this->log('Du ' . $serviceid . ' Billing ' . $billing_message . ' Log', url('/du_charge_per_service'), $data);
                }else {
                    $charge_renew_result = 0;
                    $status = "Not Known Error";
                    $sub_id = "";

                     // Billing Empty Response Lo
                     $this->log('Du ' . $serviceid . ' Billing Empty Response', url('/du_charge_per_service'), $data);
                }

            } elseif ($serviceid == "flaterweekly") {
                $service_name = "Flatter";
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
                    "value" => "Flatter Weekly",
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
                if(isset($client->responseData)   &&  $client->responseData !="" ){  // as sometimes xml load emty
                    $doc->loadXML($client->responseData);
                    $statusCode = $doc->getElementsByTagName("statusCode"); // success
                    $faultstring = $doc->getElementsByTagName("faultstring"); // insufficient or alreday subscribe

                    if ($statusCode->length != 0) { // find results
                        $status = $statusCode->item(0)->nodeValue;
                        $charge_renew_result = 1;

                        // store new subscriber
                        if ($status == 0) {
                            if ($send_welcome_message != null) { // billing for the first time  so register new subscriber
                                $sub_id = $this->successfulSubs($activation_id);
                                $secure_D_Pincode_success = secureD_Success;
                            } else { // renew charging success
                                $charge_renew_result = 1;
                                $sub_id = "";
                            }

                        }

                    } elseif ($faultstring->length != 0) {
                        $status = $faultstring->item(0)->nodeValue;
                        $charge_renew_result = 0;
                        if ($status == "503 - product already purchased!") { // aready subscribe
                            $secure_D_Pincode_success = secureD_product_already_purchased;

                            if ($send_welcome_message != null) {
                                $sub_id = $this->successfulSubs($activation_id);
                            } else {
                                $sub_id = "";
                            }

                        } elseif ($status == "24 - Insufficient funds.") {
                            $secure_D_Pincode_success = secureD_Insufficient_funds;
                            // insert in sub for the first time of susbcribe
                            if ($send_welcome_message != null) { // billing for the first time so register new subscriber
                                $sub_id = $this->successfulSubs($activation_id);
                            } else { // renew charging success
                                $charge_renew_result = 1;
                                $sub_id = "";
                            }
                        } else {
                            $sub_id = "";
                        }

                    } else {
                        $charge_renew_result = 0;
                        $status = "Not Known Error";
                        $sub_id = "";
                    }

                    $data["statusCode"] = $status;

                    // log billing
                    if ($sub != null) {
                        $billing_message = "Renew";
                    } else {
                        $billing_message = "FirstTime";
                    }
                    $this->log('Du ' . $serviceid . ' Billing ' . $billing_message . ' Log', url('/du_charge_per_service'), $data);
                }else{
                    $charge_renew_result = 0;
                    $status = "Not Known Error";
                    $sub_id = "";

                     // Billing Empty Response Lo
                     $this->log('Du ' . $serviceid . ' Billing Empty Response', url('/du_charge_per_service'), $data);
                }

            } elseif ($serviceid == "greetingsdaily") {

                $service_name = "Greeting";
                // header authentication
                $username = "P-7SYBYFVSWA-@S-r5ZBYFVSWA-";
                $password = "P-7SYBYFVSWA-#1234";

                // service parameters
                //  $userId = "971529204634" ;
                $userId = $msisdn;
                $serviceId = "S-r5ZBYFVSWA-";
                $premiumResourceType = "MP-PRT-IVAS-Greetings-B2-D-Sub";
                $productId = "Daily Greetings B2 MP IVAS Sub";

                $client->setCredentials($username, $password);
                $error = $client->getError();

                $purchaseMetas = array(
                    "key" => "du:assetDescription",
                    "value" => "Greeting",
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
                if(isset($client->responseData)   &&  $client->responseData !="" ){  // as sometimes xml load emty
                    $doc->loadXML($client->responseData);
                    $statusCode = $doc->getElementsByTagName("statusCode"); // success
                    $faultstring = $doc->getElementsByTagName("faultstring"); // insufficient or alreday subscribe

                    if ($statusCode->length != 0) { // find results
                        $status = $statusCode->item(0)->nodeValue;
                        $charge_renew_result = 1;

                        // store new subscriber
                        if ($status == 0) {
                            if ($send_welcome_message != null) { // billing for the first time so register new subscriber
                                $sub_id = $this->successfulSubs($activation_id);
                                $secure_D_Pincode_success = secureD_Success;
                            } else { // renew charging success
                                $charge_renew_result = 1;
                                $sub_id = "";
                            }

                        } else {
                            $sub_id = "";
                        }

                    } elseif ($faultstring->length != 0) {
                        $status = $faultstring->item(0)->nodeValue;
                        $charge_renew_result = 0;
                        if ($status == "503 - product already purchased!") { // aready subscribe
                            $secure_D_Pincode_success = secureD_product_already_purchased;

                            if ($send_welcome_message != null) {
                                $sub_id = $this->successfulSubs($activation_id);
                            } else {
                                $sub_id = "";
                            }

                        } elseif ($status == "24 - Insufficient funds.") {
                            $secure_D_Pincode_success = secureD_Insufficient_funds;
                            // insert in sub for the first time of susbcribe
                            if ($send_welcome_message != null) { // billing for the first time so register new subscriber
                                $sub_id = $this->successfulSubs($activation_id);
                            } else { // renew charging success
                                $charge_renew_result = 1;
                                $sub_id = "";
                            }
                        } else {
                            $sub_id = "";
                        }

                    } else {
                        $charge_renew_result = 0;
                        $status = "Not Known Error";
                        $sub_id = "";
                    }

                    $data["statusCode"] = $status;

                    // log billing
                    if ($sub != null) {
                        $billing_message = "Renew";
                    } else {
                        $billing_message = "FirstTime";
                    }
                    $this->log('Du ' . $serviceid . ' Billing ' . $billing_message . ' Log', url('/du_charge_per_service'), $data);
                }else{
                    $charge_renew_result = 0;
                    $status = "Not Known Error";
                    $sub_id = "";

                     // Billing Empty Response Lo
                     $this->log('Du ' . $serviceid . ' Billing Empty Response', url('/du_charge_per_service'), $data);
                }

            } elseif ($serviceid == "waffarlydaily") {

                $service_name = "Waffarly";
                // header authentication
                $username = "P-7SYBYFVSWA-@S-r5ZBYFVSWA-";
                $password = "P-7SYBYFVSWA-#1234";

                // service parameters
                //  $userId = "971529204634" ;
                $userId = $msisdn;
                $serviceId = "S-r5ZBYFVSWA-";
                $premiumResourceType = "MP-PRT-IVAS-waffarly-B2-D-Sub";
                $productId = "Daily waffarly B2 MP IVAS Sub";

                $client->setCredentials($username, $password);
                $error = $client->getError();

                $purchaseMetas = array(
                    "key" => "du:assetDescription",
                    "value" => "Waffarly",
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
                if(isset($client->responseData)   &&  $client->responseData !="" ){  // as sometimes xml load emty
                    $doc->loadXML($client->responseData);
                    $statusCode = $doc->getElementsByTagName("statusCode"); // success
                    $faultstring = $doc->getElementsByTagName("faultstring"); // insufficient or alreday subscribe

                    if ($statusCode->length != 0) { // find results
                        $status = $statusCode->item(0)->nodeValue;
                        $charge_renew_result = 1;

                        // store new subscriber
                        if ($status == 0) {
                            if ($send_welcome_message != null) { // billing for the first time so register new subscriber
                                $sub_id = $this->successfulSubs($activation_id);
                                $secure_D_Pincode_success = secureD_Success;
                            } else { // renew charging success
                                $charge_renew_result = 1;
                                $sub_id = "";
                            }

                        } else {
                            $sub_id = "";
                        }

                    } elseif ($faultstring->length != 0) {
                        $status = $faultstring->item(0)->nodeValue;
                        $charge_renew_result = 0;
                        if ($status == "503 - product already purchased!") { // aready subscribe
                            $secure_D_Pincode_success = secureD_product_already_purchased;

                            if ($send_welcome_message != null) {
                                $sub_id = $this->successfulSubs($activation_id);
                            } else {
                                $sub_id = "";
                            }

                        } elseif ($status == "24 - Insufficient funds.") {
                            $secure_D_Pincode_success = secureD_Insufficient_funds;
                            // insert in sub for the first time of susbcribe
                            if ($send_welcome_message != null) { // billing for the first time so register new subscriber
                                $sub_id = $this->successfulSubs($activation_id);
                            } else { // renew charging success
                                $charge_renew_result = 1;
                                $sub_id = "";
                            }
                        } else {
                            $sub_id = "";
                        }

                    } else {
                        $charge_renew_result = 0;
                        $status = "Not Known Error";
                        $sub_id = "";
                    }

                    $data["statusCode"] = $status;

                    // log billing
                    if ($sub != null) {
                        $billing_message = "Renew";
                    } else {
                        $billing_message = "FirstTime";
                    }
                    $this->log('Du ' . $serviceid . ' Billing ' . $billing_message . ' Log', url('/du_charge_per_service'), $data);
                }else{
                    $charge_renew_result = 0;
                    $status = "Not Known Error";
                    $sub_id = "";

                     // Billing Empty Response Lo
                     $this->log('Du ' . $serviceid . ' Billing Empty Response', url('/du_charge_per_service'), $data);
                }

            } elseif ($serviceid == "3laweindaily") {

                $service_name = "3lawein";
                // header authentication
                $username = "P-7SYBYFVSWA-@S-r5ZBYFVSWA-";
                $password = "P-7SYBYFVSWA-#1234";

                // service parameters
                //  $userId = "971529204634" ;
                $userId = $msisdn;
                $serviceId = "S-r5ZBYFVSWA-";
                $premiumResourceType = "MP-PRT-IVAS-3laWein-B2-D-Sub";
                $productId = "Daily 3laWein B2 MP IVAS Sub";

                $client->setCredentials($username, $password);
                $error = $client->getError();

                $purchaseMetas = array(
                    "key" => "du:assetDescription",
                    "value" => "3lawein",
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
                if(isset($client->responseData) && $client->responseData != ''){
                    $doc->loadXML($client->responseData);
                    $statusCode = $doc->getElementsByTagName("statusCode"); // success
                    $faultstring = $doc->getElementsByTagName("faultstring"); // insufficient or alreday subscribe

                    if ($statusCode->length != 0) { // find results
                        $status = $statusCode->item(0)->nodeValue;
                        $charge_renew_result = 1;

                        // store new subscriber
                        if ($status == 0) {
                            if ($send_welcome_message != null) { // billing for the first time so register new subscriber
                                $sub_id = $this->successfulSubs($activation_id);
                                $secure_D_Pincode_success = secureD_Success;
                            } else { // renew charging success
                                $charge_renew_result = 1;
                                $sub_id = "";
                            }

                        } else {
                            $sub_id = "";
                        }

                    } elseif ($faultstring->length != 0) {
                        $status = $faultstring->item(0)->nodeValue;
                        $charge_renew_result = 0;
                        if ($status == "503 - product already purchased!") { // aready subscribe
                            $secure_D_Pincode_success = secureD_product_already_purchased;

                            if ($send_welcome_message != null) {
                                $sub_id = $this->successfulSubs($activation_id);
                            } else {
                                $sub_id = "";
                            }

                        }elseif ($status == "24 - Insufficient funds.") {
                            $secure_D_Pincode_success = secureD_Insufficient_funds;
                            // insert in sub for the first time of susbcribe
                            if ($send_welcome_message != null) { // billing for the first time so register new subscriber
                                $sub_id = $this->successfulSubs($activation_id);
                            } else { // renew charging success
                                $charge_renew_result = 1;
                                $sub_id = "";
                            }
                        } else {
                            $sub_id = "";
                        }

                    } else {
                        $charge_renew_result = 0;
                        $status = "Not Known Error";
                        $sub_id = "";
                    }

                    $data["statusCode"] = $status;

                    // log billing
                    if ($sub != null) {
                        $billing_message = "Renew";
                    } else {
                        $billing_message = "FirstTime";
                    }
                    $this->log('Du ' . $serviceid . ' Billing ' . $billing_message . ' Log', url('/du_charge_per_service'), $data);
                }else{
                    $charge_renew_result = 0;
                    $status = "Not Known Error";
                    $sub_id = "";

                     // Billing Empty Response Lo
                     $this->log('Du ' . $serviceid . ' Billing Empty Response', url('/du_charge_per_service'), $data);
                }

            } elseif ($serviceid == "flaterrotanadaily") {

                $service_name = "Flater Rotana";
                // header authentication
                $username = "P-7SYBYFVSWA-@S-r5ZBYFVSWA-";
                $password = "P-7SYBYFVSWA-#1234";

                // service parameters
                //  $userId = "971529204634" ;
                $userId = $msisdn;
                $serviceId = "S-r5ZBYFVSWA-";
                $premiumResourceType = "MP-PRT-IVAS-Rotana-Flater-B2-D-Sub";
                $productId = "Daily Rotana Flater B2 MP IVAS Sub";

                $client->setCredentials($username, $password);
                $error = $client->getError();

                $purchaseMetas = array(
                    "key" => "du:assetDescription",
                    "value" => "Rotana Flater",
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
                if(isset($client->responseData)   &&  $client->responseData !="" ){  // as sometimes xml load emty
                    $doc->loadXML($client->responseData);
                    $statusCode = $doc->getElementsByTagName("statusCode"); // success
                    $faultstring = $doc->getElementsByTagName("faultstring"); // insufficient or alreday subscribe

                    if ($statusCode->length != 0) { // find results
                        $status = $statusCode->item(0)->nodeValue;
                        $charge_renew_result = 1;

                        // store new subscriber
                        if ($status == 0) {
                            if ($send_welcome_message != null) { // billing for the first time so register new subscriber
                                $sub_id = $this->successfulSubs($activation_id);
                                $secure_D_Pincode_success = secureD_Success;

                            } else { // renew charging success
                                $charge_renew_result = 1;
                                $sub_id = "";

                            }

                        } else {
                            $sub_id = "";
                        }

                    } elseif ($faultstring->length != 0) {
                        $status = $faultstring->item(0)->nodeValue;
                        $charge_renew_result = 0;
                        if ($status == "503 - product already purchased!") { // aready subscribe
                            $secure_D_Pincode_success = secureD_product_already_purchased;

                            if ($send_welcome_message != null) {
                                $sub_id = $this->successfulSubs($activation_id);
                            } else {
                                $sub_id = "";
                            }

                        } elseif ($status == "24 - Insufficient funds.") {
                            $secure_D_Pincode_success = secureD_Insufficient_funds;
                            // insert in sub for the first time of susbcribe
                            if ($send_welcome_message != null) { // billing for the first time so register new subscriber
                                $sub_id = $this->successfulSubs($activation_id);
                            } else { // renew charging success
                                $charge_renew_result = 1;
                                $sub_id = "";
                            }
                        } else {
                            $sub_id = "";
                        }

                    } else {
                        $charge_renew_result = 0;
                        $status = "Not Known Error";
                        $sub_id = "";
                    }

                    $data["statusCode"] = $status;

                    // log billing
                    if ($sub != null) {
                        $billing_message = "Renew";
                    } else {
                        $billing_message = "FirstTime";
                    }
                    $this->log('Du ' . $serviceid . ' Billing ' . $billing_message . ' Log', url('/du_charge_per_service'), $data);
                }else{
                    $charge_renew_result = 0;
                    $status = "Not Known Error";
                    $sub_id = "";
                     // Billing Empty Response Lo
                     $this->log('Du ' . $serviceid . ' Billing Empty Response', url('/du_charge_per_service'), $data);
                }

            } elseif ($serviceid == "liveqarankhatma") {

                $service_name = "Live Quran Khatma";
                // header authentication
                $username = "P-wSYBYFVSWA-@S-b6ZBYFVSWA-";
                $password = "P-wSYBYFVSWA-#1234";

                // service parameters
                //  $userId = "971529204634" ;
                $userId = $msisdn;
                $serviceId = "S-b6ZBYFVSWA-";
                $premiumResourceType = "MP-PRT-IVAS-Khatma-B2-D-Sub";
                $productId = "Daily Khatma B2 MP IVAS Sub";

                $client->setCredentials($username, $password);
                $error = $client->getError();

                $purchaseMetas = array(
                    "key" => "du:assetDescription",
                    "value" => "Live Quran",
                );

                $billingMetas = array(
                    array(
                        "key" => "du:assetID",
                        "value" => "A-V_ShAk6_L13",

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
                if(isset($client->responseData)   &&  $client->responseData !="" ){  // as sometimes xml load emty

                    $doc->loadXML($client->responseData);
                    $statusCode = $doc->getElementsByTagName("statusCode"); // success
                    $faultstring = $doc->getElementsByTagName("faultstring"); // insufficient or alreday subscribe

                    if ($statusCode->length != 0) { // find results
                        $status = $statusCode->item(0)->nodeValue;
                        $charge_renew_result = 1;
                        $secure_D_Pincode_success = secureD_Success;


                        // store new subscriber
                        if ($status == 0) {
                            if ($send_welcome_message != null) { // billing for the first time so register new subscriber
                                $sub_id = $this->successfulSubs($activation_id);
                                $secure_D_Pincode_success = secureD_Success;

                            } else { // renew charging success
                                $charge_renew_result = 1;
                                $sub_id = "";

                            }

                        } else {
                            $sub_id = "";
                        }

                    } elseif ($faultstring->length != 0) {
                        $status = $faultstring->item(0)->nodeValue;
                        $charge_renew_result = 0;
                        if ($status == "503 - product already purchased!") { // aready subscribe
                            $secure_D_Pincode_success = secureD_product_already_purchased;

                            if ($send_welcome_message != null) {
                                $sub_id = $this->successfulSubs($activation_id);
                            } else {
                                $sub_id = "";
                            }

                        } elseif ($status == "24 - Insufficient funds.") {
                            $secure_D_Pincode_success = secureD_Insufficient_funds;

                            // insert in sub for the first time of susbcribe
                            if ($send_welcome_message != null) { // billing for the first time so register new subscriber
                                $sub_id = $this->successfulSubs($activation_id);
                            } else { // renew charging success
                                $charge_renew_result = 1;
                                $sub_id = "";
                            }


                        } else {
                            $sub_id = "";
                        }

                    } else {
                        $charge_renew_result = 0;
                        $status = "Not Known Error";
                        $sub_id = "";
                    }

                    $data["statusCode"] = $status;

                    // log billing
                    if ($sub != null) {
                        $billing_message = "Renew";
                    } else {
                        $billing_message = "FirstTime";
                    }
                    $this->log('Du ' . $serviceid . ' Billing ' . $billing_message . ' Log', url('/du_charge_per_service'), $data);



                }else {
                    $charge_renew_result = 0;
                    $status = "Not Known Error";
                    $sub_id = "";
                      // Billing Empty Response Lo
                    $this->log('Du ' . $serviceid . ' Billing Empty Response', url('/du_charge_per_service'), $data);

                }




            }

            // log new charge renew into DB
            if ($sub != null) { // renew charging
                $subscriber_id = $sub->id;
            } elseif ($sub_id != "") { // billing for the first time
                $subscriber_id = $sub_id;

                // edit activition for the first time of billing
                $act = Activation::findOrFail($activation->id);
                $act->du_request = $client->request;
                $act->du_response = $client->responseData;
                $act->status_code = $status;
                $act->save();
            } else {
                $subscriber_id = "";
            }

            // log charging for First Time Or renew
            if ($subscriber_id != "") {

        $today_charging = Charge::where("subscriber_id",$subscriber_id)->where("charging_date",$today)->first();

        //  $status = "Not Known Error" // mean not each to du servers
        if(empty($today_charging )  &&   $status != "Not Known Error"  ){  // insert today charging
            $Charge = new Charge;
                $Charge->subscriber_id = $subscriber_id;
                $Charge->billing_request = $client->request;
                $Charge->billing_response = $client->responseData;
                $Charge->charging_date = $today;
                $Charge->status_code = $status;
                $Charge->save();


                // update subscriber after charging today for Renew

                if ($sub != null) {

                    $old_sub = Subscriber::findOrFail($subscriber_id );

                    if ($activation->plan == 'daily' ) {
                        $old_sub->next_charging_date = date('Y-m-d', strtotime($sub->next_charging_date . "+1 day"));
                        $old_sub->save();
                    } elseif ($activation->plan == 'weekly') {
                        $old_sub->next_charging_date = date('Y-m-d', strtotime($sub->next_charging_date . "+1 week"));
                        $old_sub->save();
                    } else { // default is daily
                        $old_sub->next_charging_date = date('Y-m-d', strtotime($sub->next_charging_date . "+1 day"));
                        $old_sub->save();
                    }

                }

        }


            }



            if ($send_welcome_message != null) {
                // log to DB + files
                $act = Activation::findOrFail($activation->id);
                $act->du_request = $client->request;
                $act->du_response = $client->responseData;
                $act->status_code = $status;
                $act->save();
                $this->log('Du ' . $serviceid . '  First Billing', url('/activation'), $data);

                //  welcomemessage for each service
                $welcome_message = $this->getMessage($serviceid);

                // Du sending welcome message
                $du_welcome_message = "Welcome To " . $service_name . "  Service ";
                $du_welcome_message .= $welcome_message;
                if ($serviceid == "flaterdaily") {
                    $du_welcome_message = "Welcome To " . $service_name ." For Unsubcribe please send stopf to 4971 or click this link https://bit.ly/2XawRXY";
                } elseif ($serviceid == "greetingsdaily") {
                    $du_welcome_message .= " For Unsubcribe  https://bit.ly/2V9MtbZ";
                } elseif ($serviceid == "flaterrotanadaily") {
                    $du_welcome_message = "Welcome To " . $service_name ." Enjoy with filters from this link   https://bit.ly/2C3Y5Hj  For Unsubcribe please send stopr to 4971 or click this link https://bit.ly/2UB9wfs";
                }elseif ($serviceid == "liveqarankhatma") {

                  //  $du_welcome_message = "Hi,  Wishing you a very blessed Ramadan." ;
                    $du_welcome_message  = "Welcome to Alafasy Quran streaming service Your link to listen him live is here" ;
                    $du_welcome_message .= " https://bit.ly/2XX83Dc " ;
                    $du_welcome_message .= "Daily charges(2/-AED),to unsubscribe send Stop to 4971 " ;
                    $du_welcome_message .= " " ;

                    $du_welcome_message .= "?????????? ?????????? ???? ???? ?????? ?????????? ????????." ;
                    $du_welcome_message .= "?????????? ?????? ???? ???????? ?????????????? ?????? ???????????? ????????????" ;
                    $du_welcome_message .= " ???????? ?????? ???????????? ???????????? ??????????" ;
                    $du_welcome_message .= " https://bit.ly/2XX83Dc " ;
                    $du_welcome_message .= "???????????????? (??????????/????????????) " ;
                    $du_welcome_message .= "  ???????????? ???????????????? ???????? Stop  ?????? 4971" ;


                }


                $message_type = "Welcome Message";
                $this->du_send_message($service_name, $msisdn, $du_welcome_message, $message_type,$log_message_id="");

            }

        } else {
            // fail
            $secure_D_Pincode_success = secureD_Failed;

        }

        return $secure_D_Pincode_success;

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

            // Du First Billing or new billing
            if ($activation) {
                $serviceid = $activation->serviceid;
                $msisdn = $activation->msisdn;
                $send_welcome_message = true;

                $secure_D_Pincode_success = $this->du_charge_per_service($activation, $serviceid, $msisdn, $sub = null, $send_welcome_message);
            }

            if ($secure_D_Pincode_success == secureD_Failed) {
                $array = ["result" => "FAIL", "reason" => "subscription Failed"];
            } elseif ($secure_D_Pincode_success == secureD_Success) {
                $array = ["result" => "SUCCESS", "reason" => "The user has been successfully activated"];
            } elseif ($secure_D_Pincode_success == secureD_product_already_purchased) {
                $array = ["result" => "FAIL", "reason" => "ALREADY SUBSCRIBED USER"];
            } elseif ($secure_D_Pincode_success == secureD_Insufficient_funds) {
                $array = ["result" => "FAIL", "reason" => "The user has insufficient funds"];
            } else {
                $array = ["result" => "FAIL", "reason" => "subscription Failed"];
            }

            $data = array_merge($data, (array) $request->all(), $array);

            $this->log('SecureD Activaition Result', url('/activation'), $data);

            return json_encode($array);
        }

    }

    public function du_send_message($service_name, $msisdn, $message, $message_type,$log_message_id)
    {
        // Du sending welcome message
        $URL = DU_SMS_SEND_MESSAGE;
        $param = "phone_number=" . $msisdn . "&message=" . $message;
        $result = $this->get_content_post($URL, $param);
        $send_array = array();

        if ($result == "1") {
            $message_mean = "Du message sent success";
            $status = 1;
        } else {
            $message_mean = "Du message sent fail";
            $status = 0;
        }

        $send_array["Date"] = Carbon::now()->format('Y-m-d H:i:s');
        $send_array["du_sms_result"] = $result;
        $send_array["du_message_mean"] = $message_mean;
        $send_array["message"] = $message;
        $send_array["msisdn"] = $msisdn;
        $this->log('Du Kannel Send Message '.$service_name, url('/du_send_message'), $send_array);

        // save log to DB
        $this->saveLogMessage($service_name, $msisdn, $message, $message_type, $status,$log_message_id);

        return $result;
    }


    public function du_send_pincode (Request $request)
    {
        // Du sending welcome message
        $URL = DU_SMS_SEND_MESSAGE;
        $message = $request->message ;
        $msisdn = $request->msisdn ;
        $service_name = $request->service_name ;

        if(isset($message) && $message  !="" && isset($msisdn)  && $msisdn  !="" && isset($service_name)  && $service_name  !=""){

          $param = "phone_number=" . $msisdn . "&message=" . $message;
        $result = $this->get_content_post($URL, $param);
        $send_array = array();

        if ($result == "1") {
            $message_mean = "Du message sent success";
            $status = 1;

        } else {
            $message_mean = "Du message sent fail";
            $status = 0 ;
        }

        $send_array["Date"] = Carbon::now()->format('Y-m-d H:i:s');
        $send_array["du_sms_result"] = $result;
        $send_array["du_message_mean"] = $message_mean;
        $send_array["message"] = $message ;
        $send_array["msisdn"] =  $msisdn ;
        $this->log('Du Kannel Send pincode for '.$service_name, url('/du_send_pincode'), $send_array);

        // save log to DB
        $message_type = "pincode";

        $this->saveLogMessage($service_name, $msisdn, $message, $message_type,$status,$log_message_id="" );

        }else{
            $result = "0" ;
        }

        return $result;
    }


    public function test_du_send()
    {
        // Du sending welcome message
        $URL = "http://41.33.167.14:2080/~smsdu/du_send_message";
        $param = "phone_number=971555802322&message=test";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        var_dump( $result);
    }

    /**
     * unsub_du_message_send , send message to told him that he unsub successfully
     *
     * @param  string $phoneNumber
     * @return void
     */
    public function unsub_du_message_send($phoneNumber)
    {
        // Du sending welcome message
        $URL = "http://41.33.167.14:2080/~smsdu/du_send_message";
        $param = "phone_number=$phoneNumber&message=You SuccessFully UnSubscribe";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
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

    public function test_du_purchaseConsumeProduct()
    { // test flatter Daily

// Config
        $client = new \nusoap_client('du_integration/du-domain.wsdl', 'wsdl');
        $client->soap_defencoding = 'UTF-8';
        $client->decode_utf8 = false;

// header authentication
$username = "P-wSYBYFVSWA-@S-b6ZBYFVSWA-";
$password = "P-wSYBYFVSWA-#1234";
// service parameters
        $userId = "971529204634";
        $serviceId = "S-b6ZBYFVSWA-";
        $premiumResourceType = "MP-PRT-IVAS-Khatma-B2-D-Sub";
        $productId = "Daily Khatma B2 MP IVAS Sub";

        $client->setCredentials($username, $password);
        $error = $client->getError();

        $purchaseMetas = array(
            "key" => "du:assetDescription",
            "value" => "Live Quran",
        );

        $billingMetas = array(
            array(
                "key" => "du:assetID",
                "value" => "A-V_ShAk6_L13",

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


    public function test_mbc()
    { // test flatter Daily

// Config
        $client = new \nusoap_client('http://mbc.mobc.com:8030/SourceSmsOut/SmsIN.asmx', 'wsdl');
        $client->soap_defencoding = 'UTF-8';
        $client->decode_utf8 = false;

// header authentication
        $username = "webSourceOut";
        $password = "2015Source@SMS_mbc";

       // $client->setCredentials($username, $password);
        $error = $client->getError();

        $Xmldoc = array(
            "Packet" => array(
            "SMS" => array(
                array(
                    "SmsID" => 3,
                    "MobileNo" => "966535550107",
                    "Country" => "KSA",
                    "Operator" => "STC",
                    "Shortcode" => "88888",
                    "Msgtxt" => "text 3",
                    "ServiceID" => 2,

                )

             )
            )
        );



        if ($error) {
            echo "<h2>Constructor error</h2><pre>" . $error . "</pre>";
        }

        $result = $client->call("GetSmsIN", array(
           "UserName" => $username,
            "UserPass" => $password,
            "Xmldoc" => $Xmldoc

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


        $this->log('MBC Sent Mt Api', url('/test_mbc'), $data);

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

    /**
     * logMessage log message unsub and remove number from subscribe
     *
     * @param  mixed $request
     *
     * @return void
     */
    public function logMessage(Request $request)
    {
        // sample link to subscribe by Mo :  By reply 1  to our shortcode 4971
        // https://du.notifications.digizone.com.kw/api/logmessage?msisdn=971555802322&message=1
        $data['msisdn'] = $request->msisdn;
        $data['msisdn'] = str_replace('+', '', $request->msisdn); // remove +
        $request->msisdn =  $data['msisdn'];

        $data['message'] = $request->message;
        $data['message'] = str_replace("???", '', $request->message); // remove ???
        $data['message'] = str_replace("???", '',  $data['message'] ); // remove ???
        $request->message =  $data['message'];

        $result = Activation::where("msisdn", $data['msisdn']);

        if ($request->message ==  "1" ||  $request->message == "A"   ||  $request->message == "Alafasy"
        ||  $request->message == "alafasy" ||  $request->message == "AFASY"  ||  $request->message == "Afasy"    ||  $request->message == "??????????????" ||  $request->message == "??????????"
     ||    $request->message == "Afasi"  ||    $request->message == "afasi" ||    $request->message == ""  ||  $request->message == " " ||  $request->message == "Afacy"
     ||  $request->message == '" Afasy"'   ||  $request->message == 'Afsay' ||  $request->message == 'afasy' ) {//sub to quran live
            require('uuid/UUID.php');
            $trxid = \UUID::v4();
            $URL = url('api/activation');
            $param = "msisdn=" . $request->msisdn . "&trxid=$trxid&serviceid=liveqarankhatma&plan=daily&price=2";
            $result = $this->get_content_post($URL, $param);
            $this->log('DU MO Quran Live Subscription Notification', $request->fullUrl(), (array)$result);
            return $result;
        } else if ($request->message == 'Stop' ||  $request->message == 'stop'  ||  $request->message == 'STOP'   ) {// unsub for all

            $this->unsubAllService($result, $request, $data); //unsub all service function

        } else if ($request->message == 'F' ||  $request->message == 'f' ) {// Sub Keywords for Flatter
                require('uuid/UUID.php');
                $trxid = \UUID::v4();
                $URL = url('api/activation');
                $param = "msisdn=" . $request->msisdn . "&trxid=$trxid&serviceid=flaterdaily&plan=daily&price=2";
                $result = $this->get_content_post($URL, $param);
                $this->log('DU MO Flatter Daily Subscription Notification', $request->fullUrl(), (array)$result);
                return $result;
        } else if ($request->message == 'R' ||  $request->message == 'r'  ||  $request->message == '2' ) {// Sub to Rotana Flatter
            require('uuid/UUID.php');
            $trxid = \UUID::v4();
            $URL = url('api/activation');
            $param = "msisdn=" . $request->msisdn . "&trxid=$trxid&serviceid=flaterrotanadaily&plan=daily&price=2";
            $result = $this->get_content_post($URL, $param);
            $this->log('DU MO Rotana Flatter  Subscription Notification', $request->fullUrl(), (array)$result);
            return $result;
        } else if ($request->message == 'm' ||  $request->message == 'M') {// subscribe to man elkeal
            /*
                        $this->log('DU MO Man Elkeal Sub Notification', $request->fullUrl(), $data);

                        $URL2 = "https://meenelkael.digizone.com.kw/api/du_mo_forward_binary";

                        $vars = array() ;
                        $vars["msisdn"] =  $data['msisdn'] ;
                        $vars["message"] = $data['message'] ;
                        $JSON = json_encode($vars);
                        $result = $this->SendRequestPost($URL2,$vars);
                        echo $result ;
            */


        }



/*
 else if ($request->message == 'Stop1' ||  $request->message == 'stop1'  ||  $request->message == 'stop'  ||  $request->message == 'Stop' ) {// unsub from quran live
            $result = $result->where('serviceid', 'liveqarankhatma');
            $result = $result->latest("created_at")->first(['id', 'msisdn', 'serviceid']);
            if ($result) {
                $sub = Subscriber::where("activation_id", $result->id)->first();
                if ($sub) {
                    $unsub = new \App\Unsubscriber();
                    $unsub->activation_id = $sub->activation_id;
                    $unsub->save();
                    $sub->delete();
                    $data['unsub_id'] = $unsub->id;
                    $this->log('DU MO Quran Live UNSUB Notification', $request->fullUrl(), $data);
                }
            }
        }




        else if ($request->message == 'StopF' ||  $request->message == 'stopf') {// unsub from Flatter
            $result = $result->where('serviceid', 'flaterdaily');
            $result = $result->latest("created_at")->first(['id', 'msisdn', 'serviceid']);
            if ($result) {
                $sub = Subscriber::where("activation_id", $result->id)->first();
                if ($sub) {
                    $unsub = new \App\Unsubscriber();
                    $unsub->activation_id = $sub->activation_id;
                    $unsub->save();
                    $sub->delete();
                    $data['unsub_id'] = $unsub->id;
                    $this->log('DU MO Flatter Daily UNSUB Notification', $request->fullUrl(), $data);
                }
            }
        }



        else if ($request->message == 'StopR' ||  $request->message == 'stopr'   ||  $request->message == 'Stopr' ||  $request->message == 'STOPR') {// unsub from Rotana Flatter
            $result = $result->where('serviceid', 'flaterrotanadaily');
            $result = $result->latest("created_at")->first(['id', 'msisdn', 'serviceid']);
            if ($result) {
                $sub = Subscriber::where("activation_id", $result->id)->first();
                if ($sub) {
                    $unsub = new \App\Unsubscriber();
                    $unsub->activation_id = $sub->activation_id;
                    $unsub->save();
                    $sub->delete();
                    $data['unsub_id'] = $unsub->id;
                    $this->log('DU MO Rotana Flatter UNSUB Notification', $request->fullUrl(), $data);
                }
            }
        }

        */

         // Log all Mo Notification
        $this->log('DU MO All Notifications', $request->fullUrl(), $data);

        $DuMo = DuMo::create([
            'link' => $request->fullUrl(),
            'msisdn' => $request->msisdn,
            'message' => $request->message
        ]);
    }

    /**
     * unsubAllService ,function to unsub user from all service
     *
     * @param  Activation $result
     * @param  Request $request
     * @param  array $data
     * @return void
     */
    public function unsubAllService($result, $request, $data)
    {
        $results = $result->latest("created_at")->get();
        foreach ($results as $result) {
            if ($result) {
                $sub = Subscriber::where("activation_id", $result->id)->first();
                if ($sub) {
                    $unsub = new \App\Unsubscriber();
                    $unsub->activation_id = $sub->activation_id;
                    $unsub->save();
                    $sub->delete();
                    $data['unsub_id'] = $unsub->id;
                    $data['service_name'] = $result->serviceid;
                    $this->log('DU UNSUB Notification', $request->fullUrl(), $data);
                }
            }
        }
        $this->unsub_du_message_send($data['msisdn']);
    }



    public function SendRequestPost($URL, $JSON)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 100);
        curl_setopt($ch, CURLOPT_TIMEOUT, 100);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $JSON);
        $sOutput = curl_exec($ch);
        curl_close($ch);



        return $sOutput;
    }



    public function sub_excel()
    {
        $data = [];
        \Excel::filter('chunk')->load(base_path().'/du_integration/Book_7_5.xlsx')->chunk(100, function($results) use(&$data)
        {
            foreach ($results as $row) {
                array_push($data,$row->msisdn);
                $ch = curl_init();
                $getUrl = "https://du.notifications.digizone.com.kw/api/logmessage?msisdn=971".$row->msisdn."&message=1";
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_URL, $getUrl);
                curl_setopt($ch, CURLOPT_TIMEOUT, 80);
                $response = curl_exec($ch);
                curl_close($ch);
            }
        },false);
        return $data;
    }
    /***************** */


    public function make_insert_sub(Request $request)
    {

       $activations = Activation::where('serviceid', "liveqarankhatma")->where('status_code', "24 - Insufficient funds.")->where('created_at',"LIKE" ,"2020-05-05%")->get();

       foreach( $activations  as  $act ){

        $activation = Activation::where('id', $act->id)->first();

        // search if old for the same service and the same msisdn

                $old_subscriber = \DB::table('subscribers')
                    ->join('activation', 'subscribers.activation_id', '=', 'activation.id')
                    ->where('activation.serviceid', $activation->serviceid)
                    ->where('activation.msisdn', $activation->msisdn)
                    ->select('activation.msisdn', 'subscribers.id')
                    ->first();

                if ($old_subscriber) { // update
                    $subscriber = Subscriber::where('id', $old_subscriber->id)->first();
                    $subscriber->activation_id = $act->id;
                } else { // create new
                    $subscriber = new Subscriber;
                    $subscriber->activation_id = $act->id;
                }

                $today = Carbon::now()->format('Y-m-d');

                if ($activation->plan == 'weekly') {
                    $next_charging_date = Carbon::now()->addDays(7)->format('Y-m-d');
                } else {
                    $next_charging_date = Carbon::now()->addDays(1)->format('Y-m-d');
                }
                $subscriber->next_charging_date =$next_charging_date ;
                $subscriber->subscribe_date =$today;
                $subscriber->final_status = 1;
                $subscriber->charging_cron = 0;
                $subscriber->save();



       }

    }


    public function make_today_charging() {
        // $today = Carbon::now()->format('Y-m-d');
        // $email = "emad@ivas.com.eg";
        // $subject = "Charging Cron By Curl Schedule for " . Carbon::now()->format('Y-m-d');
        // $this->sendMail($subject, $email);

        $ch = curl_init();
        $getUrl = "https://du.notifications.digizone.com.kw/api/chargeSubs";
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $getUrl);
        curl_setopt($ch, CURLOPT_TIMEOUT, 800000);
        $response = curl_exec($ch);
        curl_close($ch);

        echo "Du Charging By Curl exec for  toady " . $today . "Is Done";

    }



    public function make_today_charging_for_failed() {
        // $today = Carbon::now()->format('Y-m-d');
        // $email = "emad@ivas.com.eg";
        // $subject = "Charging Cron By Curl Schedule for " . Carbon::now()->format('Y-m-d');
        // $this->sendMail($subject, $email);

        $ch = curl_init();
        $getUrl = "https://du.notifications.digizone.com.kw/api/chargeSubs_for_failed";
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $getUrl);
        curl_setopt($ch, CURLOPT_TIMEOUT, 800000);
        $response = curl_exec($ch);
        curl_close($ch);

        echo "Du Charging for failed  By Curl exec for  today " . $today . "Is Done";

    }

    public function du_kannel_send_messages_log()
    {
        $serviceid    = 'flatterdaily' ;
        $msisdn       = '967552121212';
        $mes          = 'hello';
        $message_type = 'daily';
        $status = 1;
        $log_message_id = "";
        return $this->saveLogMessage($serviceid, $msisdn, $mes, $message_type,$status,$log_message_id);
    }

    public function saveLogMessage($serviceid, $msisdn, $mes, $message_type, $status,$log_message_id)
    {
        if($log_message_id == "" ){ // new
            $logmes = new LogMessage();
            $logmes->service       = $serviceid;
            $logmes->msisdn        = $msisdn;
            $logmes->message       = $mes;
            $logmes->message_type  = $message_type;
            $logmes->status  = $status;
            $logmes->save();
        }else{ // Update failed
            $logmes  =  LogMessage::where('id',$log_message_id)->first() ;
            if($logmes){
                $logmes->status  = $status;
                $logmes->save();
            }


        }


    }
}
