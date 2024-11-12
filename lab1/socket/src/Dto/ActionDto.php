<?php

namespace App\Dto;

class ActionDto
{
    public function __construct(
        public readonly string $action,
        public readonly array $data,
        public readonly int $senderId
    ) {
    }
}