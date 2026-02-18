<?php

namespace App\Auth\Domain\Event;

class UserRegisteredEvent
{
    public function __construct(
        public readonly string $userId,
        public readonly string $email,
        public readonly string $name
    ) {}
}
