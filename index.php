<?php

include_once('includes/php/utilities.php');

$jsonFilePath = 'includes/json/createOrder_data.json';
$jsonData = file_get_contents($jsonFilePath);

if (isset($_GET['action']) && $_GET['action'] == 'getFormData') {
    header('Content-Type: application/json');
    $jsonData = file_get_contents($jsonFilePath);
    echo $jsonData;
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $formData = [
        'project_id' => $_POST['project_id'] ?? '',
        'payCurrency' => $_POST['payCurrency'] ?? '',
        'payAmount' => $_POST['payAmount'] ?? '',
        'receiveCurrency' => $_POST['receiveCurrency'] ?? '',
        'receiveAmount' => $_POST['receiveAmount'] ?? '',
        'description' => $_POST['description'] ?? '',
        'lang' => $_POST['lang'] ?? '',
        'payNetworkName' => $_POST['payNetworkName'] ?? '',
        'payerName' => $_POST['payerName'] ?? '',
        'payerSurname' => $_POST['payerSurname'] ?? '',
        'payerEmail' => $_POST['payerEmail'] ?? '',
        'payerDateOfBirth' => $_POST['payerDateOfBirth'] ?? ''
    ];

    file_put_contents($jsonFilePath, json_encode($formData));
    
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

if (isset($_GET['action']) && $_GET['action'] == 'getLogContents') {
    echo readLogFile();
    exit();
}

?>

<html>
<head>
    <title>Sample Merchant client</title>
    <link rel="stylesheet" type="text/css" href="includes/css/style.css">
    <script src="includes/js/script.js" defer></script>
</head>
<body>
    <header class="header-container">
        <nav class="navigation-menu">
            <ul class="header-nav-list header-nav-list-left">
                <li class="header-list-item"><a class="header-link" href="debug.php">Debug log</a></li>
            </ul>
            <div class="logo-container">
                <a class="logo-url" href="https://spectrocoin.com" target="_blank">
                    <img class="logo-img" src="includes/img/spectrocoin-logo.svg">
                </a>
            </div>
            <ul class="header-nav-list header-nav-list-right">
                <li class="header-list-item"><a class="header-link" href="orders.php">Orders</a></li>
            </ul>
        </nav>
    </header>
    <!-- Debug Log Window -->
    <div id="debugLogWindow" class="side-window left-window">
        <!-- Content for Debug Log Window -->
    </div>

    <!-- Orders Window -->
    <div id="ordersWindow" class="side-window right-window">
        <!-- Content for Orders Window -->
    </div>
    <div class="main-section">
        <p>This is a sample of <a href="https://spectrocoin.com" target="_blank">SpectroCoin.com</a> Merchant API usage.</p>
        <form method="post" class="form-container">
            <div class="form-row">
                <label class="form-label" for="project_id">Project ID:</label>
                <input type="text" id="project_id" name="project_id" class="form-input">
            </div>
            <div class="form-row">
                <label class="form-label" for="payCurrency">Pay Currency:</label>
                <select id="payCurrency" name="payCurrency" class="form-input">
                    <option value="BTC">BTC</option>
                    <option value="ETH">ETH</option>
                    <!-- Add more options as needed -->
                </select>
            </div>
            <div class="form-row">
                <label class="form-label" for="payAmount">Pay Amount:</label>
                <input id="payAmount" name="payAmount" class="form-input">
            </div>
            <div class="form-row">
                <label class="form-label" for="receiveCurrency">Receive Currency:</label>
                <select id="receiveCurrency" name="receiveCurrency" class="form-input">
                    <option value="GBP">GBP</option>
                    <option value="USD">USD</option>
                    <!-- Add more options as needed -->
                </select>
            </div>
            <div class="form-row">
                <label class="form-label" for="receiveAmount">Receive Amount:</label>
                <input id="receiveAmount" name="receiveAmount" class="form-input">
            </div>
            <div class="form-row">
                <label class="form-label" for="description">Description:</label>
                <textarea id="description" name="description" class="form-input"></textarea>
            </div>
            <div class="form-row">
                <label class="form-label" for="lang">Language:</label>
                <select id="lang" name="lang" class="form-input">
                    <option value="en">English</option>
                    <option value="es">Spanish</option>
                    <!-- Add more options as needed -->
                </select>
            </div>
            <div class="form-row">
                <label class="form-label" for="payNetworkName">Pay Network Name:</label>
                <select id="payNetworkName" name="payNetworkName" class="form-input">
                    <option value="bitcoin">Bitcoin</option>
                    <option value="ethereum">Ethereum</option>
                    <!-- Add more options as needed -->
                </select>
            </div>
            <div class="form-row">
                <label class="form-label">For Verified Payers Only:</label>
                <input type="checkbox" id="verifiedPayersOnly" name="verifiedPayersOnly" onchange="toggleVerifiedPayerFields()">
            </div>
            <div id="verifiedPayerFields" style="display: none;">
                <div class="form-row">
                    <label class="form-label" for="payerName">Payer Name:</label>
                    <input type="text" id="payerName" name="payerName" class="form-input">
                </div>
                <div class="form-row">
                    <label class="form-label" for="payerSurname">Payer Surname:</label>
                    <input type="text" id="payerSurname" name="payerSurname" class="form-input">
                </div>
                <div class="form-row">
                    <label class="form-label" for="payerEmail">Payer Email:</label>
                    <input type="email" id="payerEmail" name="payerEmail" class="form-input">
                </div>
                <div class="form-row">
                    <label class="form-label" for="payerDateOfBirth">Payer Date of Birth:</label>
                    <input type="date" id="payerDateOfBirth" name="payerDateOfBirth" class="form-input">
                </div>
            </div>
            <div class="form-row save-button-row">
                <input class="save-button" type="submit" value="Save">
            </div>
        </form>
        <ol>
            <li><a href="includes/php/direct.php">Create merchant order (direct API result usage)</a></li>
            <li><a href="includes/php/redirect.php">Create merchant order (redirect to payment page)</a></li>
        </ol>
    </div>
</body>
</html>
