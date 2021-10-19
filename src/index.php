<?php

include_once('Pesepay.php');

$pesepay = new Pesepay('0cb23465-8499-4742-80b4-f1f9120bb805', '06771393cafd45d9bb9c26becb378bb1');

$pesepay->returnUrl = "http://example.com/gateway/return";
$pesepay->resultUrl = "http://example.com/gateway/return";

$payment = $pesepay->createPayment('ZWL', 'PZW201', 'sean@quatrohaus.com');

$response = $pesepay->makeSeamlessPayment($payment, 'Online Transaction', 1, ['customerPhoneNumber'=>'0773806130']);

if ($response->success()) {
    $referenceNumber = $response->referenceNumber();
    $pollUrl = $response->pollUrl();

} else {
    $errorMsg = $response->message();
    echo $errorMsg;
}

?>