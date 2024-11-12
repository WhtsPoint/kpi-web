<?php

namespace App\Entity;

class Message
{
    public function __construct(
        private readonly User $author,
        private readonly string $message,
        private readonly \DateTimeImmutable $createdAt
    ) {
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}