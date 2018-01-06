<?php

$config = require '../config.php';

$server = $config['protocol'] . '://'. $config['host'] . ':' . $config['port'];
$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => $method,
        'content' => http_build_query($_REQUEST),
    ],
];

$context  = stream_context_create($options);

header('Content-Type: application/json');

$data = file_get_contents($server . $uri, false, $context);

//if ($data) {
    echo $data;
//} else {
//    http_response_code(404);
//    echo "Error";
//}
