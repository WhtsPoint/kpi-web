<?php

namespace App\Entity;

class ConnectedUser
{
    private ?User $chatUser = null;

    public function __construct(
        private readonly int $id
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getChatUser(): ?User
    {
        return $this->chatUser;
    }

    public function setChatUser(?User $chatUser): void
    {
        $this->chatUser = $chatUser;
    }
}