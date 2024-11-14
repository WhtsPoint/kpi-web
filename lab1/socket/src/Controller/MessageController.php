<?php

namespace App\Controller;

use App\Dto\ActionDto;
use Exception;
use Swoole\WebSocket\Server;
use Swoole\WebSocket\Frame;

class MessageController
{
    public function __construct(
        private readonly array $actions
    ) {
    }

    public function runActionByMessage(Server $server, Frame $frame): void
    {
       $content = $frame->data;
       $body = is_string($content) ? json_decode($content, true) : false;

       if ($body === false || is_string($body['action'] ?? null) === false || is_array($body['message'] ?? null) === false) {
           $server->push($frame->fd, json_encode(['error' => 'Invalid payload format']));

           return;
       }

       if (isset($this->actions[$body['action']]) === false) {
           $server->push($frame->fd, json_encode(['error' => 'Invalid message type']));

           return;
       }

       try {
           $response = $this->actions[$body['action']]->do(
               new ActionDto(
                   $body['message'],
                   $frame->fd
               )
           );

           $server->push($frame->fd, json_encode(['action' => $body['action'], 'message' => $response]));
       } catch (Exception $exception) {
           $server->push($frame->fd, json_encode(['action' => $body['action'], 'error' => $exception->getMessage()]));
       }
    }
}