<?php

namespace App;

use InvalidArgumentException;

class User
{
    private string $password;

    private array $roles = [];

    private ?string $id = null;


    public function __construct(
      private readonly string $name,
      string $password
    ) {
        $this->setPassword($password);
    }

    public function setPassword(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }

    public static function isValidPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function addRole(string $role): void
    {
        if (in_array($role, ['admin', 'user']) === false) {
            throw new InvalidArgumentException('Invalid role');
        }

        $this->roles []= $role;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }
}