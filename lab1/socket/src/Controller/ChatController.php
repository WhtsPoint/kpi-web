<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\ChatPool;
use Exception;
use OpenSwoole\WebSocket\Server;
use Swoole\Http\Request;

class ChatController
{
    public function __construct(
        private readonly ChatPool $chatPool
    ) {
    }

    /**
     * @throws Exception
     */
    public function createChats(): void
    {
        $this->createChat('room1');
        $this->createChat('room2');
    }

    /**
     * @throws Exception
     */
    public function createChat(string $name): void
    {
        $this->chatPool->addChat(new Chat($name));
    }
}