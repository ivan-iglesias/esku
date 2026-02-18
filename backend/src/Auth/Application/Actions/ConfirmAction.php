<?php

namespace App\Auth\Application\Actions;

use App\Auth\Domain\Repository\SecurityTokenRepositoryInterface;
use App\Auth\Domain\Repository\UserRepositoryInterface;
use App\Shared\Domain\Exception\BusinessErrorCode;
use App\Shared\Domain\Exception\BusinessException;

class ConfirmAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private SecurityTokenRepositoryInterface $tokenRepository
    ) {}

    public function execute(string $tokenValue): void
    {
        $token = $this->tokenRepository->findByValue($tokenValue);

        if (!$token || !$token->isValid()) {
            throw new BusinessException(BusinessErrorCode::AUTH_INVALID_TOKEN);
        }

        $user = $token->getUser();
        $user->activate();

        $this->userRepository->save($user);
        $this->tokenRepository->delete($token);
    }
}
