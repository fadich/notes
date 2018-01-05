<?php

require 'vendor/autoload.php';

require 'Repository.php';
require 'RequestHandler.php';
require 'routes.php';

use React\EventLoop\Factory;
use React\Http\Server as Http;
use React\Socket\Server as Socket;
use Psr\Http\Message\ServerRequestInterface;

$loop = Factory::create();

$rep = new Repository(2,2);
$http = new RequestHandler($rep);
$http->addRoute('/', 'get', 'search');
$http->addRoute('/', 'put', 'insert');
$http->addRoute('/{id}', 'put', 'update');
$http->addRoute('/{id}', 'delete', 'delete');

$server = new Http(function (ServerRequestInterface $request) use ($rep, $http) {
    return $http->getResponse($request);
});

$socket = new Socket(8010, $loop);
$server->listen($socket);

$loop->run();

