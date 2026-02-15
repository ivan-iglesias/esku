<?php

namespace App\Auth\Infrastructure\Controller;

use App\Auth\Application\Actions\LoginAction;
use App\Auth\Application\DTO\AuthResponse;
use App\Auth\Application\DTO\LoginInput;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/login', name: 'api_login', methods: ['POST'])]
#[OA\Post(
    path: '/api/login',
    summary: 'Inicia sesión para obtener el token JWT',
    requestBody: new OA\RequestBody(
        description: 'Credenciales de acceso',
        content: new OA\JsonContent(ref: new Model(type: LoginInput::class))
    ),
    responses: [
        new OA\Response(
            response: 200,
            description: 'Autenticación exitosa',
            content: new OA\JsonContent(ref: new Model(type: AuthResponse::class))
        ),
        new OA\Response(
            response: 401,
            description: 'Credenciales inválidas',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'error', type: 'string', example: 'Credenciales inválidas')
                ]
            )
        ),
        new OA\Response(
            response: 422,
            description: 'Error de validación en los datos de entrada',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'errors',
                        type: 'object',
                        example: ['email' => 'El formato del email no es válido.']
                    )
                ]
            )
        )
    ]
)]
class LoginController extends AbstractController
{
    public function __construct(
        private readonly LoginAction $loginAction,
        private readonly LoggerInterface $logger
    ) {}

    public function __invoke(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];

        $loginInput = new LoginInput($data);

        $errors = $validator->validate($loginInput);

        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $messages], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $authResponse = $this->loginAction->execute($loginInput);
            return $this->json($authResponse);
        } catch (AuthenticationException $e) {
            return $this->json(['error' => 'Credenciales inválidas'], Response::HTTP_UNAUTHORIZED);
        } catch (\Throwable $e) {
            $this->logger->critical('Fallo crítico de sistema: ' . $e->getMessage());
            throw $e;
        }
    }
}
