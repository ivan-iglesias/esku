<?php

namespace App\Auth\Infrastructure\Controller;

use App\Auth\Application\Actions\PasswordlessLoginVerifyAction;
use App\Auth\Application\DTO\AuthResponse;
use App\Auth\Application\DTO\PasswordlessLoginVerifyInput;
use App\Shared\Domain\Exception\BusinessException;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PasswordlessLoginVerifyController extends AbstractController
{
    public function __construct(
        private readonly PasswordlessLoginVerifyAction $action,
        private readonly LoggerInterface $logger
    ) {}

    #[Route('/api/auth/login-code/verify', name: 'api_passwordless_login_verify', methods: ['POST'])]
    #[OA\Post(
        path: '/api/auth/login-code/verify',
        summary: 'Verifica el código de acceso y devuelve el token JWT',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            description: 'Credenciales de acceso (Email + Código de 5 dígitos)',
            required: true,
            content: new OA\JsonContent(ref: new Model(type: PasswordlessLoginVerifyInput::class))
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Autenticación exitosa',
                content: new OA\JsonContent(ref: new Model(type: AuthResponse::class))
            ),
            new OA\Response(
                response: 401,
                description: 'Error de negocio (Código inválido o expirado)',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'code', type: 'string', example: 'AUTH_INVALID_CODE'),
                        new OA\Property(property: 'message', type: 'string', example: 'El código es incorrecto o ha caducado.')
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Error de validación de datos',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'errors',
                            type: 'object',
                            example: ['code' => 'El código debe tener exactamente 5 dígitos']
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Error crítico del servidor',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Ha ocurrido un error inesperado.')
                    ]
                )
            )
        ]
    )]
    public function __invoke(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $input = new PasswordlessLoginVerifyInput($data);

        $errors = $validator->validate($input);

        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[$error->getPropertyPath()] = $error->getMessage();
            }

            return $this->json(['errors' => $messages], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $authResponse = $this->action->execute($input);
            return $this->json($authResponse);
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
