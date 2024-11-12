<?php

use App\Controller\ChatController;
use OpenSwoole\WebSocket\Server;
use Swoole\Http\Request;

return function (ChatController $chatController) {
    return [
        'ping_rooms' => fn (Server $server, Request $request) => $chatController->createChat($server, $request)
    ];
};