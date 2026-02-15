<?php

namespace App\Auth\Domain\Repository;

use App\Auth\Domain\Entity\User;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?User;
}
