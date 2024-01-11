<?php

//redirect.php

include_once('constants.php');
include_once('SCMerchantClient/SCMerchantClient.php');


$jsonData = file_get_contents('createOrder_data.json'); // Update the path to your JSON file
$data = json_decode($jsonData, true); // Convert JSON string to PHP array

$project_id = PROJECT_ID;
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

$scMerchantClient = new SCMerchantClient(SC_API_URL, $project_id);

$createOrderRequest = new CreateOrderRequest($orderId, $payCurrency, $payAmount, $receiveCurrency, $receiveAmount, $description, SC_MERCHANT_ORDER_CALLBACK_URL, SC_MERCHANT_ORDER_SUCCESS_URL, SC_MERCHANT_ORDER_FAILURE_URL, $lang, $payNetworkName, $payerName, $payerSurname, $payerEmail, $payerDateOfBirth);

$createOrderResponse = $scMerchantClient->createOrder($createOrderRequest);

if ($createOrderResponse instanceof ApiError) {
	echo 'Error occurred. ' . $createOrderResponse->getCode() . ': ' . $createOrderResponse->getMessage();
} else if ($createOrderResponse instanceof CreateOrderResponse) {
	header('Location: '.$createOrderResponse->getRedirectUrl());
	exit();
} else {
	echo 'error';
}

?>
