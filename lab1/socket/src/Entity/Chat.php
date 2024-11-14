<?php

namespace App\Entity;

use Exception;
use InvalidArgumentException;

class Chat
{
    /** @var ConnectedUser[] */
    private array $connectedUsers = [];

    /** @var Message[] */
    private array $messages = [];

    private int $userCounter = 0;

    public function __construct(
        private readonly string $name,
        private readonly SendMessageInterface $sendMessage
    ) {
    }

    public function connectUser(ConnectedUser $user): void
    {
        if ($this->hasUserWithId($user->getId())) {
            throw new InvalidArgumentException('User with this id is already connected');
        }

        $this->connectedUsers []= $user;

        $user->setName('User #' . ++$this->userCounter);

        $this->sendMessage->sendConnectedMessage($this, $user);
    }

    public function disconnectUser(ConnectedUser $user): void
    {
        if (in_array($user, $this->connectedUsers, true) === false) {
            throw new InvalidArgumentException('User is not connected');
        }

        $this->connectedUsers = array_values(
            array_filter($this->connectedUsers, function ($connectedUser) use ($user) {
                return $connectedUser->getId() !== $user->getId();
            })
        );

        $this->sendMessage->sendDisconnectedMessage($this, $user);
    }

    /**
     * @throws Exception
     */
    public function sendMessage(Message $message): void
    {
        if (in_array($message->getAuthor(), $this->connectedUsers, true) === false) {
            throw new Exception('User is not connected');
        }

        if (in_array($message, $this->messages, true) === true) {
            throw new Exception('This message has already been sent');
        }

        $this->messages []= $message;

        $this->sendMessage->sendMessage($this, $message);
    }

    public function disconnectUserIfIn(ConnectedUser $user): bool
    {
        try {
            $this->disconnectUser($user);

            return true;
        } catch (InvalidArgumentException) {
            return false;
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getConnectedUsers(): array
    {
        return $this->connectedUsers;
    }

    public function hasUserWithId(int $id): bool
    {
        return in_array(
            $id,
            array_map(fn (ConnectedUser $user) => $user->getId(), $this->connectedUsers),
            true
        ) === true;
    }
}