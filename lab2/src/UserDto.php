<?php

namespace App;

readonly class UserDto
{
    public function __construct(
        public readonly string $name,
        public readonly string $password,
        public readonly array $roles
    ) {
    }
}