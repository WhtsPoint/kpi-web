<?php

namespace App\Action;

use App\Dto\ActionDto;
use App\Entity\Chat;
use App\Entity\ChatPool;

class PingChatsAction implements ActionInterface
{
    public function __construct(
        private readonly ChatPool $chatPool
    ) {
    }

    public function do(ActionDto $dto): array
    {
        return ['chats' => array_map(
            fn (Chat $chat) => $chat->getName(),
            $this->chatPool->getAll()
        )];
    }
}