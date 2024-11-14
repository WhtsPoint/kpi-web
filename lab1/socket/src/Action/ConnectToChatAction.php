<?php

namespace App\Action;

use App\Dto\ActionDto;
use App\Entity\ChatPool;
use App\Entity\ConnectedUser;
use App\Entity\ConnectedUserPool;
use Exception;

class ConnectToChatAction implements ActionInterface
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
        if (is_string($dto->data['chat_name'] ?? null) === false) {
            throw new Exception('Invalid payload format');
        }

        $chat = $this->chatPool->getChatByName($dto->data['chat_name']);
        $user = $this->userPool->getById($dto->senderId);

        $this->chatPool->connectUser($user, $chat);

        return ['chat_state' => [
            'users' => array_map(fn (ConnectedUser $user) => [
                'name' => $user->getName(),
                'id' => $user->getId()
            ], $chat->getConnectedUsers())
        ]];
    }
}