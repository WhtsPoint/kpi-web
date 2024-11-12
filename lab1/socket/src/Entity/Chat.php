<?php

namespace App\Entity;

use InvalidArgumentException;

class Chat
{
    /** @var User[] */
    private array $connectedUsers = [];

    public function __construct(private readonly string $name) {
    }

    public function connectUser(User $user): void
    {
        if (in_array(
                $user->getId(),
                array_map(fn ($user) => $user->getId(), $this->connectedUsers),
                true
            ) === true) {
            throw new InvalidArgumentException('User with this id is already connected');
        }

        if (in_array(
            $user->getName(),
            array_map(fn ($user) => $user->getName(), $this->connectedUsers),
            true
        ) === true) {
            throw new InvalidArgumentException('User with this nick is already connected');
        }

        $this->connectedUsers []= $user;
    }

    public function disconnectUser(User $user): void
    {
        if (in_array($user, $this->connectedUsers, true) === false) {
            throw new InvalidArgumentException('User is not connected');
        }

        $this->connectedUsers = array_filter($this->connectedUsers, function ($connectedUser) use ($user) {
            return $connectedUser->getId() !== $user->getId();
        });
    }

    public function getName(): string
    {
        return $this->name;
    }
}