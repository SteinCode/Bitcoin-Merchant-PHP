<html>
<head>
	<title>Sample Merchant client</title>
	<script src="js/countdown.min.js" type="application/javascript"></script>
	<script src="js/script.js" type="application/javascript"></script>
</head>
<body>
<?php
//direct.php
include_once('constants.php');
include_once('SCMerchantClient/SCMerchantClient.php');

$project_id = isset($_POST['project_id']) ? $_POST['project_id'] : PROJECT_ID; // Use default PROJECT_ID if not set

$orderId = "Order" . rand(1, 10000);// "Orderxxx";
$payCurrency = 'BTC'; // Customer pay amount calculation currency
$payAmount = 9.99;//0.00025; // Customer pay amount in calculation currency
$receiveCurrency = 'GBP'; // Merchant receive amount calculation currency
$receiveAmount = 9.99; // Merchant receive amount in calculation currency
$description = "Order 'Order001' at www.merchant.com"; // Description of the order.
$lang = "en"; // Language or culture setting (e.g., English).
$payNetworkName = "bitcoin"; // 
$payerName = "Name"; // OPTIONAL - First name of the payer/customer.
$payerSurname = "Surname"; //OPTIONAL - Last name of the payer/customer.
$payerEmail = "your-email@gmail.com"; // OPTIONAL - Email address of the payer/customer.
$payerDateOfBirth = "1980-01-01"; // OPTIONAL - Date of birth of the payer/customer.

$scMerchantClient = new SCMerchantClient(SC_API_URL, $project_id);

$createOrderRequest = new CreateOrderRequest($orderId, $payCurrency, $payAmount, $receiveCurrency, $receiveAmount, $description, SC_MERCHANT_ORDER_CALLBACK_URL, SC_MERCHANT_ORDER_SUCCESS_URL, SC_MERCHANT_ORDER_FAILURE_URL, $lang, $payNetworkName, $payerName, $payerSurname, $payerEmail, $payerDateOfBirth);

$createOrderResponse = $scMerchantClient->createOrder($createOrderRequest);

if ($createOrderResponse instanceof ApiError) {
    echo 'Error occurred. ' . $createOrderResponse->getCode() . ': ' . $createOrderResponse->getMessage();
} else if ($createOrderResponse instanceof CreateOrderResponse) {
    echo 'Order Id: ' . $createOrderResponse->getOrderId() . '<br/>';
    echo 'Pre Order Id: ' . $createOrderResponse->getPreOrderId() . '<br/>';
    echo 'Deposit address: ' . $createOrderResponse->getDepositAddress() . '<br/>';
    echo 'Pay: ' . $createOrderResponse->getPayAmount() . ' ' . $createOrderResponse->getPayCurrency() . '<br/>';
    echo '<img src="//chart.googleapis.com/chart?chs=160x160&chld=M|0&cht=qr&chl=' . strtolower($createOrderResponse->getPayCurrency()) . ':' . $createOrderResponse->getDepositAddress() . '?amount=' . $createOrderResponse->getPayAmount() . '" alt="QR Code"/><br/>';
    echo 'Receive: ' . $createOrderResponse->getReceiveAmount() . ' ' . $createOrderResponse->getReceiveCurrency() . '<br/>';
    echo 'Valid until: ' . $createOrderResponse->getValidUntil() . '<br/>';
    // Assuming you have a JavaScript function 'countdown' to handle the countdown timer
    echo '<script type="application/javascript">countdown("' . $createOrderResponse->getValidUntil() . '")</script><br/>';
} else {
    echo 'Error';
}


?>

</body>
</html>
