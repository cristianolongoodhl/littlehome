<?php
/*
 * Append post requests on a file
 */

$log=fopen("inbox.log","a+");

//headers

foreach($_SERVER as $key => $value) {
    if (substr($key, 0, 5) <> 'HTTP_') {
        continue;
    }
    $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
	fwrite($log, "header $header : $value\n");
    $headers[$header] = $value;
}

$postBody = file_get_contents("php://input");
fwrite($log, json_encode(json_decode($postBody), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)."\n\n");
fclose($log);
?>
