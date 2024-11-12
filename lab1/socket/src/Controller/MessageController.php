<?php

namespace App\Controller;

use App\Dto\ActionDto;
use OpenSwoole\Http\Request;
use OpenSwoole\WebSocket\Server;

class MessageController
{
    public function __construct(
        private readonly array $actions
    ) {
    }

    public function runActionByMessage(Server $server, Request $request): void
    {
       $content = $request->getContent();
       $body = is_string($content) ? json_decode($content, true) : false;

       if ($body === false || is_string($body['type']) === false || is_array($body['message']) === false) {
           $server->push($request->fd, json_encode(['error' => 'Invalid payload format']));

           return;
       }

       if (isset($this->actions[$body['type']]) === false) {
           $server->push($request->fd, json_encode(['error' => 'Invalid message type']));

           return;
       }

       $this->actions[$body['type']]->do(
           new ActionDto(
               $body['type'],
               $body['message'],
               $request->fd
           )
       );
    }
}