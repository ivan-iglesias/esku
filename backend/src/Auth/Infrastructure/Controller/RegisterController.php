<?php

namespace App\Auth\Infrastructure\Controller;

use App\Auth\Application\Actions\RegisterAction;
use App\Auth\Application\DTO\RegisterInput;
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

#[Route('/api/auth/register', name: 'api_auth_register', methods: ['POST'])]
#[OA\Post(
    path: '/api/auth/register',
    summary: 'Registra un nuevo usuario en la plataforma',
    tags: ['Auth'],
    requestBody: new OA\RequestBody(
        content: new OA\JsonContent(ref: new Model(type: RegisterInput::class))
    ),
    responses: [
        new OA\Response(
            response: 201,
            description: 'Usuario creado con éxito. Se ha enviado un email de confirmación.'
        ),
        new OA\Response(response: 422, description: 'Error de validación'),
        new OA\Response(response: 400, description: 'El email ya está registrado')
    ]
)]
class RegisterController extends AbstractController
{
    public function __construct(
        private readonly RegisterAction $registerAction,
        private readonly LoggerInterface $logger
    ) {}

    public function __invoke(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];

        $registerInput = new RegisterInput($data);

        $errors = $validator->validate($registerInput);

        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $messages], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $this->registerAction->execute($registerInput);

            return $this->json([
                'message' => 'Usuario registrado con éxito. Por favor, revisa tu email para activar la cuenta.'
            ], Response::HTTP_CREATED);
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
