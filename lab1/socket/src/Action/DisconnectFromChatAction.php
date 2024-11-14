<?php

namespace App\Action;

use App\Dto\ActionDto;
use App\Entity\ChatPool;
use App\Entity\ConnectedUserPool;
use Exception;

class DisconnectFromChatAction implements ActionInterface
{
    public function __construct(
        private readonly ConnectedUserPool $userPool,
        private readonly ChatPool $chatPool
    ) {
    }

    /**
     * @throws Exception
     */
    public function do(ActionDto $dto): array
    {
        $user = $this->userPool->getById($dto->senderId);

        $chat = $this->chatPool->disconnectUserIfIn($user);

        return ['chat_name' => $chat];
    }
}