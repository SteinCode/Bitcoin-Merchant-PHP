<?php


function printToBrowserConsole($data) {
    if (is_array($data)) {
        $encodedData = json_encode($data);
        $script = "<script>console.log('Array (printed as JSON):', $encodedData);</script>";
    } else {
        $encodedData = json_encode((string)$data);
        $script = "<script>console.log($encodedData);</script>";
    }

    echo $script;
}

function writeToLog($message, $messageType = 'ERROR') {
    $logFile = __DIR__ . '/app_log.txt';

    switch ($messageType) {
        case 'INFO':
            $messagePrefix = "INFO";
            break;
        case 'DEBUG':
            $messagePrefix = "DEBUG";
            break;
        case 'WARNING':
            $messagePrefix = "WARNING";
            break;
        case 'NOTICE':
            $messagePrefix = "NOTICE";
            break;
        default:
            $messagePrefix = "ERROR";
            break;
    }

    $timestamp = date('Y-m-d H:i:s');
    $formattedMessage = PHP_EOL . "[$timestamp] [$messagePrefix] $message" . PHP_EOL;
    file_put_contents($logFile, $formattedMessage, FILE_APPEND | LOCK_EX);
}