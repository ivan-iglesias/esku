<?php

namespace App\Auth\Infrastructure\Controller;

use App\Auth\Application\Actions\ConfirmAction;
use App\Shared\Infrastructure\Controller\BaseApiController;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ConfirmAccountController extends BaseApiController
{
    public function __construct(
        private readonly ConfirmAction $confirmAction,
        LoggerInterface $logger,
        ValidatorInterface $validator
    ) {
        parent::__construct($logger, $validator);
    }

    #[Route('/api/auth/confirm/{token}', name: 'api_auth_confirm', methods: ['GET'])]
    #[OA\Get(
        path: '/api/auth/confirm/{token}',
        summary: 'Confirma la cuenta de un usuario mediante un token de email',
        tags: ['Auth'],
        parameters: [
            new OA\Parameter(
                name: 'token',
                in: 'path',
                description: 'El token recibido por email',
                required: true,
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Cuenta activada correctamente'
            ),
            new OA\Response(
                response: 404,
                description: 'Token inválido o expirado'
            )
        ]
    )]
    public function __invoke(string $token): JsonResponse
    {
        return $this->handleSimpleAction(function () use ($token) {
            $this->confirmAction->execute($token);

            return [
                'message' => '¡Cuenta confirmada con éxito! Ya puedes iniciar sesión.'
            ];
        });
    }
}
