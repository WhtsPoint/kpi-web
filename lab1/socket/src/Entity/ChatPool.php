<?php

namespace App\Entity;

use Exception;
use InvalidArgumentException;

class ChatPool
{
    /** @var Chat[] */
    private array $chats = [];

    /** @throws Exception */
    public function addChat(Chat $chat): void
    {
        if (in_array(
            $chat->getName(),
            array_map(fn (Chat $chat) => $chat->getName(), $this->chats)
        ) === true) {
            throw new InvalidArgumentException('Chat already in the pool');
        }

        $this->chats [] = $chat;
    }

    /** @throws Exception */
    public function getChatByName(string $name): Chat
    {
        foreach ($this->chats as $chat) {
            if ($chat->getName() === $name) {
                return $chat;
            }
        }

        throw new Exception('No chat with this name');
    }
}