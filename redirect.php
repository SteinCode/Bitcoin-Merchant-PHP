<?php

//redirect.php

session_start();

include_once('constants.php');
include_once('SCMerchantClient/SCMerchantClient.php');


$jsonData = file_get_contents('createOrder_data.json'); // Update the path to your JSON file
$data = json_decode($jsonData, true); // Convert JSON string to PHP array

$project_id = $data['project_id'];
$orderId = "Order" . rand(1, 10000);// "Orderxxx - order id must be unique";
$payCurrency = $data['payCurrency'];
$payAmount = $data['payAmount'];
$receiveCurrency = $data['receiveCurrency'];
$receiveAmount = $data['receiveAmount'];
$description = $data['description'];
$lang = $data['lang'];
$payNetworkName = $data['payNetworkName'];
$payerName = $data['payerName'];
$payerSurname = $data['payerSurname'];
$payerEmail = $data['payerEmail'];
$payerDateOfBirth = $data['payerDateOfBirth'];

$encryptionKey = 'your_secret_encryption_key';

if (isset($_SESSION['encryptedAccessTokenData'])) {
    $encryptedTokenData = $_SESSION['encryptedAccessTokenData'];
    $accessTokenData = decrypt($encryptedTokenData, $encryptionKey);
    $accessTokenData = json_decode($accessTokenData, true); // Assuming the data was stored as JSON
} else {
    $accessTokenData = null;
}

$scMerchantClient = new SCMerchantClient(SC_API_URL, $project_id, CLIENT_ID, CLIENT_SECRET, AUTH_URL, $accessTokenData);

$createOrderRequest = new CreateOrderRequest($orderId, $payCurrency, $payAmount, $receiveCurrency, $receiveAmount, $description, SC_MERCHANT_ORDER_CALLBACK_URL, SC_MERCHANT_ORDER_SUCCESS_URL, SC_MERCHANT_ORDER_FAILURE_URL, $lang, $payNetworkName, $payerName, $payerSurname, $payerEmail, $payerDateOfBirth);

$createOrderResponse = $scMerchantClient->createOrder($createOrderRequest);

if ($createOrderResponse instanceof ApiError) {
	writeToLog('Error occurred. ' . $createOrderResponse->getCode() . ': ' . $createOrderResponse->getMessage());
} else if ($createOrderResponse instanceof CreateOrderResponse) {
	header('Location: '.$createOrderResponse->getRedirectUrl());
	exit();
} else {
	writeToLog('Unknown error occurred.');
}

?>
