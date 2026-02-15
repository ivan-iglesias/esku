<?php

namespace App\Auth\Application\DTO;

use App\Auth\Domain\Entity\User;
use Symfony\Component\Serializer\Attribute\SerializedName;

final class AuthResponse
{
    public function __construct(
        #[SerializedName('access_token')]
        public readonly string $token,
        public readonly string $email,
        public readonly array $roles
    ) {}

    public static function fromUser(string $token, User $user): self
    {
        return new self($token, $user->getEmail(), $user->getRoles());
    }
}
