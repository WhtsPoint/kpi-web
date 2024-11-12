<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\ChatController;
use App\Controller\ConnectedUserController;
use App\Entity\ChatPool;
use App\Entity\ConnectedUserPool;
use OpenSwoole\Websocket\Server;
use OpenSwoole\WebSocket\Frame;

$chatController = new ChatController(new ChatPool());
$connectedUsersController = new ConnectedUserController(new ConnectedUserPool());

$server = new Server("host.docker.internal", 9502);

$server->on("Start", function(Server $server) use ($chatController) {
    echo "Websocket server started!\n";

    $chatController->createChats();
});

$server->on('Open', function(Server $server, OpenSwoole\Http\Request $request) use ($connectedUsersController) {
    $connectedUsersController->connectById($server, $request);
});

$server->on('Message', function(Server $server, Frame $frame) {
});

$server->on('Close', function(Server $server, int $fd) {
});

$server->on('Disconnect', function(Server $server, int $fd) {
});

$server->start();