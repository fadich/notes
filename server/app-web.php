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

$loop = Factory::create();

$rep = new Repository(2,2);
$http = new RequestHandler($rep);
$http->addRoute('/', 'get', 'search', ['repository' => $rep]);
$http->addRoute('/', 'put', 'insert', ['repository' => $rep]);
$http->addRoute('/{id}', 'put', 'update', ['repository' => $rep]);
$http->addRoute('/{id}', 'delete', 'delete', ['repository' => $rep]);

$server = new Http(function (ServerRequestInterface $request) use ($rep, $http) {
    return $http->getResponse($request);
});

$socket = new Socket($config['port'], $loop);
$server->listen($socket);

$loop->run();
