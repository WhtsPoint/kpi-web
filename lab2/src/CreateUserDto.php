<?php

namespace App;

readonly class CreateUserDto
{
    public function __construct(
        public readonly string $name,
        public readonly string $password
    ) {
    }
}