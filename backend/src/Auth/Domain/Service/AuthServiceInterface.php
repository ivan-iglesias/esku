<?php

namespace App\Auth\Domain\Service;

use App\Auth\Domain\Entity\User;

interface AuthServiceInterface
{
    public function authenticate(string $email, string $password): User;
}
