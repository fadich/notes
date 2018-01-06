<?php

require 'vendor/autoload.php';

$config = require 'config.php';

require 'Repository.php';
require 'RequestHandler.php';
require 'routes.php';

use React\EventLoop\Factory;
use React\Http\Server as Http;
use React\Socket\Server as Socket;
use Psr\Http\Message\ServerRequestInterface;

print 'Starting server..';
sleep(1);

$loop = Factory::create();

$rep = new Repository(2,2);
$http = new RequestHandler($rep);
$http->addRoute('/', 'get', 'search', ['repository' => $rep]);
$http->addRoute('/', 'post', 'insert', ['repository' => $rep]);
$http->addRoute('/{id}', 'post', 'update', ['repository' => $rep]);
$http->addRoute('/{id}', 'delete', 'delete', ['repository' => $rep]);

$server = new Http(function (ServerRequestInterface $request) use ($rep, $http) {
    return $http->getResponse($request);
});

try {
    $socket = new Socket($config['port'], $loop);
    $server->listen($socket);
} catch (Exception $e) {
    print "\rError: " . $e->getMessage();
    die;
}

print "\rListening to port " . $config['port'] . "...";

$loop->run();
