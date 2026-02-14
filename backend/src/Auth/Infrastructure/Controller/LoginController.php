<?php

namespace App\Auth\Infrastructure\Controller;

use App\Auth\Application\Actions\LoginAction;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/login', name: 'api_login', methods: ['POST'])]
#[OA\Post(
    path: '/api/login',
    summary: 'Inicia sesión para obtener el token JWT',
    requestBody: new OA\RequestBody(
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'email', type: 'string', example: 'admin@esku.com'),
                new OA\Property(property: 'password', type: 'string', example: '123456')
            ]
        )
    ),
    responses: [
        new OA\Response(
            response: 200,
            description: 'Token generado correctamente',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'token', type: 'string'),
                    new OA\Property(property: 'user', type: 'object')
                ]
            )
        ),
        new OA\Response(response: 401, description: 'Credenciales inválidas')
    ]
)]
class LoginController extends AbstractController
{
    public function __construct(
        private readonly LoginAction $loginAction,
        private readonly LoggerInterface $logger
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return $this->json(['error' => 'Email y password requeridos'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $result = $this->loginAction->execute($email, $password);
            return $this->json($result, Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->logger->error('Error en el login: ' . $e->getMessage());

            return $this->json(
                ['error' => 'Credenciales inválidas'],
                Response::HTTP_UNAUTHORIZED
            );
        }
    }
}
