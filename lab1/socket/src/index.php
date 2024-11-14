<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Action\ConnectToChatAction;
use App\Action\DisconnectFromChatAction;
use App\Action\PingChatsAction;
use App\Action\SendMessageAction;
use App\Controller\ChatController;
use App\Controller\ConnectedUserController;
use App\Controller\MessageController;
use App\Controller\SendMessageController;
use App\Dto\ActionDto;
use App\Entity\ChatPool;
use App\Entity\ConnectedUserPool;
use Swoole\Http\Request;
use Swoole\Timer;
use Swoole\Websocket\Server;
use Swoole\WebSocket\Frame;

$server = new Server("host.docker.internal", 9502);

$server->set(['worker_num' => 1]);

$chatPool = new ChatPool();
$sendMessageController = new SendMessageController($server);
$chatController = new ChatController($chatPool, $sendMessageController);
$connectedUserPool = new ConnectedUserPool();
$connectedUsersController = new ConnectedUserController($server, $connectedUserPool);
$disconnectAction = new DisconnectFromChatAction($connectedUserPool, $chatPool);
$messageController = new MessageController([
    'ping_chats' => new PingChatsAction($chatPool),
    'connect_to_chat' => new ConnectToChatAction($connectedUserPool, $chatPool),
    'disconnect_from_chat' => $disconnectAction,
    'send_message' => new SendMessageAction($connectedUserPool, $chatPool),
]);
$chatController->createChats();

$server->on("Start", function () use ($chatController) {
    echo "Websocket server started!\n";
});

$server->on('Open', function(Server $server, Request $request) use ($connectedUsersController) {
    $connectedUsersController->connectById($request);
});

$server->on('Message', function(Server $server, Frame $frame) use ($messageController, $chatController) {
    $messageController->runActionByMessage($server, $frame);
});

$server->on('Close', function(Server $server, int $fd) use ($disconnectAction) {
    $disconnectAction->do(new ActionDto([], $fd));
});

$server->on('Disconnect', function(Server $server, int $fd) {
});

$server->start();