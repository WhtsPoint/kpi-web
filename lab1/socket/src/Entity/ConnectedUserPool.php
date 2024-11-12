<?php

namespace App\Entity;

use Exception;

class ConnectedUserPool
{
    /** @var ConnectedUser[] */
    private array $users = [];

    /**
     * @throws Exception
     */
    public function add(ConnectedUser $user): void
    {
        if (in_array($user->getId(), $this->getIds(), true) === true) {
            throw new Exception('User with this id already set');
        }

        $this->users []= $user;
    }

    /**
     * @throws Exception
     */
    public function removeById(int $id): void
    {
        if (in_array($id, $this->getIds(), true) === true) {
            throw new Exception('User with this id already set');
        }

        $this->users = array_filter($this->users, fn (ConnectedUser $user) => $user->getId() !== $id);
    }

    /** @return string[] */
    public function getIds(): array
    {
        return array_map(fn (ConnectedUser $user) => $user->getId(), $this->users);
    }
}