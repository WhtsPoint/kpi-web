<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\ConnectedUser;
use App\Entity\Message;
use App\Entity\SendMessageInterface;
use Swoole\WebSocket\Server;

class SendMessageController implements SendMessageInterface
{
    public function __construct(
        private readonly Server $server
    ) {
    }

    public function sendMessage(Chat $chat, Message $message): void
    {
       foreach ($chat->getConnectedUsers() as $user) {
           $author = $message->getAuthor();

          $body = [
              'action' => 'new_message',
              'message' => [
                  'text' => $message->getMessage(),
                  'author' => ['name' => $author->getName(), 'id' => $author->getId()],
                  'created_at' => $message->getCreatedAt()->format('H:i:s')
              ]
          ];

           $this->server->push($user->getId(), json_encode($body));
       }
    }

    public function sendConnectedMessage(Chat $chat, ConnectedUser $connectedUser): void
    {
        foreach ($chat->getConnectedUsers() as $user) {
            if ($user === $connectedUser) {
                continue;
            }

            $this->server->push($user->getId(), json_encode([
                'action' => 'new_connected_user',
                'message' => [
                    'user' => ['name' => $connectedUser->getName(), 'id' => $connectedUser->getId()]
                ]
            ]));
        }
    }

    public function sendDisconnectedMessage(Chat $chat, ConnectedUser $disconnectedUser): void
    {
        foreach ($chat->getConnectedUsers() as $user) {
            if ($user === $disconnectedUser) {
                continue;
            }

            $this->server->push($user->getId(), json_encode([
                'action' => 'disconnected_user',
                'message' => [
                    'user_id' => $disconnectedUser->getId()
                ]
            ]));
        }
    }
}