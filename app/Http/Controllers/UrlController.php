<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Url;
use App\Activation;
use Validator;
use Carbon\Carbon;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class UrlController extends Controller {

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
        $ExDate = date('Y-m-d', strtotime($Input->ExDate .'+1day'));
        $ExURL = $Input->ExURL;


        //var_dump($ExDate);
        //return $Input->all();
        $MaxVisits = (empty($Input->MaxVisits))? Null : $Input->MaxVisits;
        $return = $this->store($URL,$ExDate,$MaxVisits,$ExURL);
        Response::create($return);
        $ShortenURL = url('',$return);

        return view('created')->with('ShortenURL',$ShortenURL);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store($URL,$ExDate,$ExVisits,$ExURL)
	{
		//
        $Insert = new Url();
        $Insert->URL = $URL;
        $Insert->ExDate = (!isset($ExDate) || empty($ExDate))? 0 : $ExDate;
        $Insert->ExVisits = (!isset($ExVisits) || empty($ExDate))? 0 : $ExVisits;
        $Insert->ExURL = $ExURL;
        $Insert->save();
        $ID = $Insert->id;
        $Update = Url::find($ID);
        $Update->save();
        $UID = rand(100,999).$ID;
        $Update->UID = $UID;
        $Update->save();

        return $UID;

	}
    public function APICreate(){
       // echo "ffff"; die;
        $Create = Request::capture();
        $URL = $Create->URL;
        $ExDate = $Create->ExDate;
        $MaxVists = $Create->MaxVisits;
        $ExURL = $Create->ExURL;
        $ID = $this->store($URL,$ExDate,$MaxVists,$ExURL);
      //  echo $ID ; die;

        return  Response::create(url('',$ID));
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
        $NewID = substr($id,3);
        $test = Url::find($NewID);


        $Visits = intval($test->Visits);

        $SessionKey = Session::get('Visit');
        if (empty($SessionKey)){
            Session::put('Visit', str_random(16));
            $test->Visits = $Visits + 1;
            $test->save();
        }else{

        }
        //Session::put('key', str_random(16));
        //return Session::get('Visit') .'  '.$test->Visits;

        $TodayDate = date('Y-m-d', strtotime('now'));
        if (strtotime($TodayDate) > strtotime($test->ExDate)){
            return Redirect::to($test->ExURL);
            //return 'Date';

        }else{
            if(intval($test->ExVisits) == 0){
                return Redirect::to($test->URL);
                //return 'Unlimited Visits';

            }else{

                if (intval($test->Visits) > intval($test->ExVisits)){
                    return Redirect::to($test->ExURL);
                    //return 'Maximum Visits';
                }else{
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

    public function test2(Request $request){

 $du_response_success ='<?xml version="1.0" encoding="UTF-8"?>
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


 $du_already='<?xml version="1.0" encoding="UTF-8"?>
 <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Body>
       <soap:Fault>
          <faultcode>soap:Server</faultcode>
          <faultstring>503 - product already purchased!</faultstring>
          <detail>503 - product already purchased!</detail>
       </soap:Fault>
    </soap:Body>
 </soap:Envelope>';


 $du_insufficient='<?xml version="1.0" encoding="UTF-8"?>
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
                $doc->loadXML(  $du_insufficient  );
                $statusCode     = $doc->getElementsByTagName("statusCode");  // success
                $faultstring     = $doc->getElementsByTagName("faultstring");  // insufficient or alreday subscribe





                if ( $statusCode->length !=0 ) { // find results
                   $status = $statusCode->item(0)->nodeValue;
                 }elseif($faultstring->length !=0 ){
                    $status = $faultstring ->item(0)->nodeValue;
                 }

                 echo  $status ; die;



    }

    /**************** */

    public function activation(Request $request){

        $date = date("Y-m-d h:i:sa");
        $ip = $request->ip();

        $position = \Location::get($ip);

        if($position){
            $country = $position->countryName;
        }else{
            $country = $position;
        }

        $data = ['date' => $date, 'ip' => $ip, 'country' => $country];

        $validator = Validator::make($request->all(), [
            'trxid' => 'required',
            'msisdn' => 'required',
            'serviceid' => 'required',
            'plan' => 'required',
            'price' => 'required'
        ]);


        if ($validator->fails()) {

            $data = array_merge($data, (array)$request->all(), (array)$validator->errors()->all());

            $this->log('failed', url('/activation'), $data);

            return response()->json(["result" => "FAILED",'error' => implode(', ',$validator->errors()->all())], 401);

        }else{

            $activation = new Activation;

            if($request->filled('trxid')){
                $activation->trxid = $request->trxid;
            }
            if($request->filled('msisdn')){
                $activation->msisdn = $request->msisdn;
            }
            if($request->filled('serviceid')){
                $activation->serviceid = $request->serviceid;
            }
            if($request->filled('plan')){
                $activation->plan = $request->plan;
            }
            if($request->filled('price')){
                $activation->price = $request->price;
            }

            $activation->save();

            $activation_id =$activation->id ;



            $array = ["result" => "SUCCESS", "reason" => "The user has been successfully activated"];

            $data = array_merge($data, (array)$request->all(), $array);

            $this->log('success', url('/activation'), $data);

 // here make Du billing
// Config
$client = new \nusoap_client('du_integration/du-domain.wsdl', 'wsdl');
$client->soap_defencoding = 'UTF-8';
$client->decode_utf8 = FALSE;


            if($request->serviceid == "flaterdaily"){

                                // header authentication
                        $username = "P-7SYBYFVSWA-@S-r5ZBYFVSWA-";
                        $password = "P-7SYBYFVSWA-#1234" ;


                        // service parameters
                      //  $userId = "971529204634" ;
                        $userId =  $request->msisdn ;
                        $serviceId = "S-r5ZBYFVSWA-" ;
                        $premiumResourceType = "MP-PRT-IVAS-Flater-B2-D-Sub" ;
                        $productId = "Daily Flater B2 MP IVAS Sub";



                        $client->setCredentials($username, $password);
                        $error  = $client->getError();

                        $purchaseMetas = array(
                        "key" => "du:assetDescription" ,
                        "value" => "IVAS TEST" ,
                        ) ;


                        $billingMetas = array(
                        array(
                        "key" => "du:assetID" ,
                        "value" => "A-cMShAk6_L13"

                        ),
                        array(

                        "key" => "du:contentType" ,
                        "value" => "mobileApp"

                        ),
                        array(

                        "key" => "du:channel" ,
                        "value" => "COMMERCE_API"

                        )

                        ) ;

                        $usageMetas = array(
                        "key" => "du:externalid" ,
                        "value" => "X12345" ,
                        ) ;



                        $result = $client->call("purchaseConsumeProduct", array(
                        "userId" => $userId ,
                        "serviceId" => $serviceId,
                        "premiumResourceType" => $premiumResourceType,
                        "productId" => $productId  ,
                        "purchaseMetas" => $purchaseMetas ,
                        "billingMetas" => $billingMetas ,
                        "usageMetas" => $usageMetas

                        ));



                        // show soap request and response
                        // echo "<h2>Request</h2>";
                        // echo "<pre>" .$client->request. "</pre>";
                        // echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';


                        // echo "<h2>Response</h2>";
                        // echo "<pre>" .$client->response. "</pre>";
                        // echo '<h2>Response</h2><pre>' . htmlspecialchars($client->responseData, ENT_QUOTES) . '</pre>';


                        $data["Date"] =Carbon::now()->format('Y-m-d H:i:s');
                        $data["Request"] =     $client->request;
                        $data["Response"] =  $client->responseData;


                // log Du result Code   0 = mean success
                /*
                $doc = new \DOMDocument('1.0', 'utf-8');
                $doc->loadXML( $client->responseData );
                $XMLresults     = $doc->getElementsByTagName("statusCode");
                $statusCode = $XMLresults->item(0)->nodeValue;
                $data["statusCode"] =  $statusCode;

              $act =   Activation::findOrFail($activation_id)  ;
              $act->status_code =$statusCode ;
              $act->save();
*/



                    $doc = new \DOMDocument('1.0', 'utf-8');
                    $doc->loadXML(  $client->responseData  );
                    $statusCode     = $doc->getElementsByTagName("statusCode");  // success
                    $faultstring     = $doc->getElementsByTagName("faultstring");  // insufficient or alreday subscribe






                    if ( $statusCode->length !=0 ) { // find results
                    $status = $statusCode->item(0)->nodeValue;
                    }elseif($faultstring->length !=0 ){
                    $status = $faultstring ->item(0)->nodeValue;
                    }

                    $data["statusCode"] =  $status;



                        // log to DB + files
                        $act =Activation::findOrFail(  $activation->id) ;
                        $act->du_request = $client->request ;
                        $act->du_response =  $client->responseData ;
                        $act->save() ;
                        $this->log('du Flatter Daily Billing', url('/activation'), $data);

                         // Du sending welcome message
                         $du_welcome_message = "welcome to daily flatter service";
                         $du_welcome_message .=" Enjoy from   ".DU_Flatter_Link ;
                         $URL = DU_SMS_SEND_MESSAGE;
                         $param = "phone_number=" . $userId . "&message=" . $du_welcome_message;
                         $result = $this->get_content_post($URL, $param);
                         $send_array= array() ;

                         if ($result == "1") {
                             $message_mean= "Du message sent success" ;

                         }else{
                             $message_mean= "Du message sent fail" ;
                         }

                         $send_array["Date"] =Carbon::now()->format('Y-m-d H:i:s');
                         $send_array["du_sms_result"] = $result ;
                         $send_array["du_message_mean"] = $message_mean ;
                         $this->log('du Flatter daily sending welcome message', url('/activation'), $send_array);




            }elseif($request->serviceid == "flaterweekly"){
                // header authentication
                $username = "P-7SYBYFVSWA-@S-r5ZBYFVSWA-";
                $password = "P-7SYBYFVSWA-#1234" ;


                // service parameters
              //  $userId = "971529204634" ;
                  $userId =  $request->msisdn ;
                $serviceId = "S-r5ZBYFVSWA-" ;
                $premiumResourceType = "MP-PRT-IVAS-Flater-B14-W-Sub" ;
                $productId = "Weekly Flater B14 MP IVAS Sub";


                $client->setCredentials($username, $password);
                $error  = $client->getError();

                $purchaseMetas = array(
                    "key" => "du:assetDescription" ,
                    "value" => "IVAS TEST" ,
                ) ;


                $billingMetas = array(
                    array(
                        "key" => "du:assetID" ,
                        "value" => "A-cMShAk6_L13"

                    ),
                    array(

                        "key" => "du:contentType" ,
                        "value" => "mobileApp"

                    ),
                    array(

                        "key" => "du:channel" ,
                        "value" => "COMMERCE_API"

                    )

                ) ;

                $usageMetas = array(
                    "key" => "du:externalid" ,
                    "value" => "X12345" ,
                ) ;




                    $result = $client->call("purchaseConsumeProduct", array(
                        "userId" => $userId ,
                        "serviceId" => $serviceId,
                        "premiumResourceType" => $premiumResourceType,
                        "productId" => $productId  ,
                        "purchaseMetas" => $purchaseMetas ,
                        "billingMetas" => $billingMetas ,
                        "usageMetas" => $usageMetas

                        ));




                // show soap request and response
                // echo "<h2>Request</h2>";
                // echo "<pre>" .$client->request. "</pre>";
                // echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';


                // echo "<h2>Response</h2>";
                // echo "<pre>" .$client->response. "</pre>";
                // echo '<h2>Response</h2><pre>' . htmlspecialchars($client->responseData, ENT_QUOTES) . '</pre>';

                  // log Du result Code   0 = mean success
                  /*
                $doc = new \DOMDocument('1.0', 'utf-8');
                $doc->loadXML( $client->responseData );
                $XMLresults     = $doc->getElementsByTagName("statusCode");
                $statusCode = $XMLresults->item(0)->nodeValue;
                $data["statusCode"] =  $statusCode;

                $act =   Activation::findOrFail($activation_id)  ;
                $act->status_code =$statusCode ;
                $act->save();
*/

                        // log to DB + files
                        $act =Activation::findOrFail(  $activation->id) ;
                        $act->du_request = $client->request ;
                        $act->du_response =  $client->responseData ;
                        $act->save() ;
                        $this->log('du Flatter Weekly Billing ', url('/activation'), $data);

                        // Du sending welcome message
                        $du_welcome_message = "welcome to weekly flatter service";
                        $du_welcome_message .=" Enjoy from   ".DU_Flatter_Link ;
                        $URL = DU_SMS_SEND_MESSAGE;
                        $param = "phone_number=" . $userId . "&message=" . $du_welcome_message;
                        $result = $this->get_content_post($URL, $param);
                        $send_array= array() ;

                        if ($result == "1") {
                            $message_mean= "Du message sent success" ;

                        }else{
                            $message_mean= "Du message sent fail" ;
                        }

                        $send_array["Date"] =Carbon::now()->format('Y-m-d H:i:s');
                        $send_array["du_sms_result"] = $result ;
                        $send_array["du_message_mean"] = $message_mean ;
                        $this->log('du Flatter weely sending welcome message', url('/activation'), $send_array);

            }




            return json_encode($array);
        }


    }


    public function get_content_post($URL, $param) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }


    public function test() {  // test flatter Daily


// Config
$client = new \nusoap_client('du_integration/du-domain.wsdl', 'wsdl');
$client->soap_defencoding = 'UTF-8';
$client->decode_utf8 = FALSE;

// header authentication
$username = "P-7SYBYFVSWA-@S-r5ZBYFVSWA-";
$password = "P-7SYBYFVSWA-#1234" ;


// service parameters
$userId = "971529204634" ;
$serviceId = "S-r5ZBYFVSWA-" ;
$premiumResourceType = "MP-PRT-IVAS-Flater-B2-D-Sub" ;
$productId = "Daily Flater B2 MP IVAS Sub";



$client->setCredentials($username, $password);
$error  = $client->getError();

$purchaseMetas = array(
    "key" => "du:assetDescription" ,
    "value" => "IVAS TEST" ,
) ;


$billingMetas = array(
    array(
        "key" => "du:assetID" ,
        "value" => "A-cMShAk6_L13"

    ),
    array(

        "key" => "du:contentType" ,
        "value" => "mobileApp"

    ),
    array(

        "key" => "du:channel" ,
        "value" => "COMMERCE_API"

    )

) ;

$usageMetas = array(
    "key" => "du:externalid" ,
    "value" => "X12345" ,
) ;


if ($error) {
    echo "<h2>Constructor error</h2><pre>" . $error . "</pre>";
}

    $result = $client->call("purchaseConsumeProduct", array(
        "userId" => $userId ,
        "serviceId" => $serviceId,
        "premiumResourceType" => $premiumResourceType,
        "productId" => $productId  ,
        "purchaseMetas" => $purchaseMetas ,
        "billingMetas" => $billingMetas ,
        "usageMetas" => $usageMetas

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
        print_r($result) ;   // MOResult

    }
}

// show soap request and response
echo "<h2>Request</h2>";
 echo "<pre>" .$client->request. "</pre>";
 echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';


 echo "<h2>Response</h2>";
 echo "<pre>" .$client->response. "</pre>";
  echo '<h2>Response</h2><pre>' . htmlspecialchars($client->responseData, ENT_QUOTES) . '</pre>';




  $data["Date"] =Carbon::now()->format('Y-m-d H:i:s');
  $data["Request"] =     $client->request;
  $data["Response"] =  $client->responseData;


  $act =Activation::findOrFail( 1) ;
  $act->du_request = $client->request ;
  $act->du_response =  $client->responseData ;
  $act->save() ;


  $this->log('du Billing', url('/test'), $data);

    }

    public function log($actionName, $URL, $parameters_arr) {
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
