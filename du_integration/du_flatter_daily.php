<?php
require_once('nusoap/lib/nusoap.php');

// header authentication
$username = "P-7SYBYFVSWA-@S-r5ZBYFVSWA-";
$password = "P-7SYBYFVSWA-#1234" ;


// service parameters
$userId = "971529204634" ;
$serviceId = "S-r5ZBYFVSWA-" ;
$premiumResourceType = "MP-PRT-IVAS-Flater-B2-D-Sub" ;
$productId = "Daily Flater B2 MP IVAS Sub";



//$client = new nusoap_client("http://localhost/zain_jordon_api/zain_jordon.wsdl", true);
$client = new nusoap_client("du-domain.wsdl", true);
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


?>

