<?php


/**
 * Prints data to browser console
 * @param $data
 */
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

/**
 * Logs messages log.txt file in plugin directory
 * @param $message
 * @param string $messageType | INFO(default), DEBUG, WARNING, NOTICE, ERROR
*/
function writeToLog($message, $messageType = 'INFO') {
    $logFile = __DIR__ . '/log.txt';

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
    $formattedMessage = PHP_EOL . "[$timestamp] [$messagePrefix] ";

    if (is_array($message)) {
        $encodedMessage = json_encode($message);
        $formattedMessage .= "Array (printed as JSON): $encodedMessage" . PHP_EOL;
    } else {
        $formattedMessage .= $message . PHP_EOL;
    }

    file_put_contents($logFile, $formattedMessage, FILE_APPEND | LOCK_EX);
}

function readLogFile() {
    $logFile = __DIR__ . '/log.txt';
    
    if (file_exists($logFile)) {
        $logContents = file_get_contents($logFile);
        if (empty($logContents)) {
            return "No log messages.";
        }
        return $logContents;
    } else {
        return "Log file not found.";
    }
}