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

    public function connectUser(ConnectedUser $user, Chat $chat): void
    {
        if (in_array($chat, $this->chats, true) === false) {
            throw new InvalidArgumentException('No chat in the pool');
        }

        $this->disconnectUserIfIn($user);
        $chat->connectUser($user);
    }

    public function disconnectUser(ConnectedUser $user): void
    {
        foreach ($this->chats as $chat) {
            $chat->disconnectUser($user);
        }
    }

    public function disconnectUserIfIn(ConnectedUser $user): ?string
    {
        foreach ($this->chats as $chat) {
            $isDisconnected = $chat->disconnectUserIfIn($user);

            if ($isDisconnected) {
                return $chat->getName();
            }
        }

        return null;
    }

    /**
     * @throws Exception
     */
    public function getChatByName(string $name): Chat
    {
        foreach ($this->chats as $chat) {
            if ($chat->getName() === $name) {
                return $chat;
            }
        }

        throw new Exception('No chat with this name');
    }

    /**
     * @throws Exception
     */
    public function getChatByUser(int $id): Chat
    {
        foreach ($this->chats as $chat) {
            if ($chat->hasUserWithId($id)) {
                return $chat;
            }
        }

        throw new Exception('No user in any chat');
    }


    public function has(string $name): bool
    {
        return in_array($name, $this->chats);
    }

    /** @return  Chat[] */
    public function getAll(): array
    {
        return $this->chats;
    }
}