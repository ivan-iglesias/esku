<?php

namespace App\Auth\Domain\Repository;

use App\Auth\Domain\Entity\SecurityToken;
use App\Auth\Domain\Entity\User;
use App\Auth\Domain\Enum\SecurityTokenType;

interface SecurityTokenRepositoryInterface
{
    public function findByValue(string $token): ?SecurityToken;

    public function findByTokenAndUser(string $token, User $user, SecurityTokenType $type): ?SecurityToken;

    public function save(SecurityToken $token): void;

    public function delete(SecurityToken $token): void;

    public function deleteExpiredTokens(): int;
}
