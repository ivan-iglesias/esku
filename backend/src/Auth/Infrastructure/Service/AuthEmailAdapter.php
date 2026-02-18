<?php

namespace App\Auth\Infrastructure\Service;

use App\Auth\Domain\Entity\User;
use App\Auth\Domain\Service\UserRegistrationNotifierInterface;
use App\Shared\Infrastructure\Email\SymfonyEmailSender;

class AuthEmailAdapter implements UserRegistrationNotifierInterface
{
    public function __construct(private SymfonyEmailSender $emailSender) {}

    public function sendConfirmationLink(User $user, string $confirmationUrl): void
    {
        $this->emailSender->sendTemplate(
            $user->getEmail(),
            'Confirma tu cuenta',
            'emails/confirmation.html.twig',
            [
                'user' => $user,
                'url' => $confirmationUrl,
            ]
        );
    }

    public function sendLoginCode(User $user, string $code): void
    {
        $this->emailSender->sendTemplate(
            $user->getEmail(),
            'Código de acceso',
            'emails/login_code.html.twig',
            [
                'user' => $user,
                'code' => $code,
            ]
        );
    }
}
