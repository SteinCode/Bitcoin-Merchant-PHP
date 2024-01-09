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


