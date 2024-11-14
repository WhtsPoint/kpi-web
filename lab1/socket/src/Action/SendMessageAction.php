<?php

namespace App\Action;

use App\Dto\ActionDto;
use App\Entity\ChatPool;
use App\Entity\ConnectedUserPool;
use App\Entity\Message;
use DateTimeImmutable;
use Exception;

class SendMessageAction implements ActionInterface
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
        if (is_string($dto->data['text'] ?? null) === false) {
            throw new Exception('Invalid payload format');
        }

        $user = $this->userPool->getById($dto->senderId);
        $chat = $this->chatPool->getChatByUser($user->getId());

        $chat->sendMessage(new Message($user, $dto->data['text'], new DateTimeImmutable()));

        return [];
    }
}