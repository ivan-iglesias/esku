<?php

namespace App\Auth\Infrastructure\Controller;

use App\Auth\Application\Actions\ConfirmAction;
use App\Shared\Domain\Exception\BusinessException;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
class ConfirmAccountController extends AbstractController
{
    public function __construct(
        private readonly ConfirmAction $confirmAction,
        private readonly LoggerInterface $logger
    ) {}

    public function __invoke(string $token): JsonResponse
    {
        try {
            $this->confirmAction->execute($token);

            return new JsonResponse([
                'message' => '¡Cuenta confirmada con éxito! Ya puedes iniciar sesión.'
            ], Response::HTTP_OK);
        } catch (BusinessException $error) {
            return $this->json([
                'code' => $error->getBusinessCode(),
                'message' => $error->getMessage()
            ], $error->getCode());
        } catch (\Exception $error) {
            $this->logger->critical('Fallo crítico de sistema: ' . $error->getMessage());
            return $this->json(['error' => 'Ha ocurrido un error inesperado.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
