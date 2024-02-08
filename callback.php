<?php

include_once('constants.php');
include_once('SCMerchantClient/SCMerchantClient.php');
include_once('debug_helpers.php');

// Function to process $_POST data
function processPostData($postData) {
    $expectedFields = [
        'merchantId', 'apiId', 'userId', 'merchantApiId', 'orderId', 
        'payCurrency', 'payAmount', 'receiveCurrency', 'receiveAmount', 
        'receivedAmount', 'description', 'orderRequestId', 'status', 'sign'
    ];

    $processedData = [];
    foreach ($expectedFields as $field) {
        if (isset($postData[$field])) {
            $processedData[$field] = $postData[$field];
        } else {
            // Handle missing expected field, e.g., log an error or set a default value
            // For now, we'll just log that the field is missing
            writeToLog("Missing field: " . $field, 'ERROR');
        }
    }
    return $processedData;
}

// Process the POST data
// $filteredPostData = processPostData($_POST);

// Debugging: Log and print the filtered data
// writeToLog($filteredPostData, 'DEBUG');
writeToLog($_POST, 'DEBUG');
printToBrowserConsole($filteredPostData);

// if ($callback != null){

// 	switch ($callback->getStatus()) {
// 		case OrderStatusEnum::$Test:
// 			processTestCallback($callback);
// 			break;
// 		case OrderStatusEnum::$New:
// 			processNewCallback($callback);
// 			break;
// 		case OrderStatusEnum::$Pending:
// 			processPendingCallback($callback);
// 			break;
// 		case OrderStatusEnum::$Expired:
// 			processExpiredCallback($callback);
// 			break;
// 		case OrderStatusEnum::$Failed:
// 			processFailedCallback($callback);
// 			break;
// 		case OrderStatusEnum::$Paid:
// 			processPaidCallback($callback);
// 			break;
// 		default:
// 			echo 'Unknown order status: '.$callback->getStatus();
// 			break;
// 	}

// 	echo '*ok*';

// } else {
// 	echo 'Invalid callback!';
// }

// function processTestCallback(OrderCallback $callback) {
// 	echo 'Test callback received!';
// }
// function processNewCallback(OrderCallback $callback) {
// 	echo 'New callback received!';
// }
// function processPendingCallback(OrderCallback $callback) {
// 	echo 'Pending callback received!';
// }
// function processExpiredCallback(OrderCallback $callback) {
// 	echo 'Expired callback received!';
// }
// function processFailedCallback(OrderCallback $callback) {
// 	echo 'Failed callback received!';
// }
// function processPaidCallback(OrderCallback $callback) {
// 	echo 'Paid callback received!';
// }