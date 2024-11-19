<?php

use App\Router;
use App\UserController;
use OpenSwoole\Http\Request;
use OpenSwoole\Http\Response;
use OpenSwoole\Http\Server;

require_once __DIR__ . '/vendor/autoload.php';

$server = new Server('host.docker.internal', 80);

$router = new Router();

$dependencies = require __DIR__ . '/dependencies.php';

$router->add('/^\/user$/', 'POST', [$dependencies[UserController::class](), 'create']);
$router->add('/^\/user\/(?<id>.*)$/', 'GET', [$dependencies[UserController::class](), 'get']);
$router->add('/^\/user\/auth$/', 'POST', [$dependencies[UserController::class](), 'auth']);
$router->add('/^\/user\/(?<id>.*)$/', 'DELETE', [$dependencies[UserController::class](), 'delete']);

$server->on('Request', function (Request $request, Response $response) use ($router) {
    $response->header('Content-Type', 'application/json');
    $router->serve($request, $response);
});

$server->start();