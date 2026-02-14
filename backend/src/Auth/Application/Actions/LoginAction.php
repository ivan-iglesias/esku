<?php

namespace App\Auth\Application\Actions;

use App\Auth\Infrastructure\AuthService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Psr\Log\LoggerInterface;

class LoginAction
{
    public function __construct(
        private AuthService $authService,
        private JWTTokenManagerInterface $jwtManager,
        private LoggerInterface $logger
    ) {}

    public function execute(string $email, string $password): array
    {
        $user = $this->authService->verifyCredentials($email, $password);

        $this->logger->info("Login de negocio procesado para: $email");

        // if ($user->isBanned()) { throw new \Exception("Acceso denegado"); }

        $token = $this->jwtManager->create($user);

        return [
            'token' => $token,
            'user' => [
                'email' => $user->getUserIdentifier(),
                'roles' => $user->getRoles(),
            ]
        ];
    }
}
