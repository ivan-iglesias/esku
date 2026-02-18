<?php

namespace App\Auth\Domain\Service;

use App\Auth\Domain\Entity\User;

interface UserRegistrationNotifierInterface
{
    public function sendConfirmationLink(User $user, string $confirmationUrl): void;

    public function sendLoginCode(User $user, string $code): void;
}
