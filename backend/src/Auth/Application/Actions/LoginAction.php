<?php

namespace App\Auth\Application\Actions;

use App\Auth\Application\DTO\AuthResponse;
use App\Auth\Application\DTO\LoginInput;
use App\Auth\Domain\Service\AuthServiceInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Psr\Log\LoggerInterface;

class LoginAction
{
    public function __construct(
        private AuthServiceInterface $authService,
        private JWTTokenManagerInterface $jwtManager,
        private LoggerInterface $logger
    ) {}

    public function execute(LoginInput $input): AuthResponse
    {
        $user = $this->authService->authenticate($input->email, $input->password);

        $token = $this->jwtManager->create($user);

        $email = $user->getEmail();

        $this->logger->info("Login realizado: {$email}");

        // if ($userDto->isBanned()) { throw new \Exception("Acceso denegado"); }

        return AuthResponse::fromUser($token, $user);
    }
}
