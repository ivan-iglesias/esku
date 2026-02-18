<?php

namespace App\Auth\Infrastructure\Controller;

use App\Auth\Application\Actions\PasswordlessLoginAction;
use App\Auth\Application\DTO\PasswordlessLoginInput;
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

#[Route('/api/auth/login-code', name: 'api_passwordless_login', methods: ['POST'])]
#[OA\Post(
    path: '/api/auth/login-code',
    summary: 'Solicita un código de acceso de 5 dígitos vía email',
    tags: ['Auth'],
    requestBody: new OA\RequestBody(
        description: 'Email del usuario para recibir el código OTP',
        content: new OA\JsonContent(ref: new Model(type: PasswordlessLoginInput::class))
    ),
    responses: [
        new OA\Response(
            response: 200,
            description: 'Solicitud procesada',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'message', type: 'string', example: 'Si el email existe...')
                ]
            )
        ),
        new OA\Response(
            response: 422,
            description: 'Error de validación',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'errors', type: 'object', example: ['email' => 'Email inválido'])
                ]
            )
        )
    ]
)]
class PasswordlessLoginController extends AbstractController
{
    public function __construct(
        private readonly PasswordlessLoginAction $action,
        private readonly LoggerInterface $logger
    ) {}

    public function __invoke(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];

        $input = new PasswordlessLoginInput($data);

        $errors = $validator->validate($input);

        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $messages], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $this->action->execute($input);

            return $this->json(['message' => 'Si el email existe en nuestro sistema, recibirás un código de acceso en breve.']);
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
