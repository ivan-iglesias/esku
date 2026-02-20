<?php

namespace App\Shared\Infrastructure\EventListener;

use App\Shared\Domain\Exception\BusinessException;
use App\Shared\Infrastructure\Response\ApiResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ApiExceptionListener
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly string $environment
    ) {}

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // Errores de Negocio (BusinessException)
        if ($exception instanceof BusinessException) {
            $event->setResponse(ApiResponse::error(
                $exception->getBusinessCode(),
                $exception->getMessage(),
                $exception->getCode()
            ));
            return;
        }

        // Errores de Symfony/HTTP (ej: 404 Route Not Found o 405 Method Not Allowed)
        if ($exception instanceof HttpExceptionInterface) {
            $event->setResponse(ApiResponse::error(
                'HTTP_ERROR',
                $exception->getMessage(),
                $exception->getStatusCode()
            ));
            return;
        }

        // Error crítico no controlado (500)
        $this->logger->critical('Excepción no capturada: ' . $exception->getMessage(), [
            'trace' => $exception->getTraceAsString()
        ]);

        $message = ($this->environment === 'dev')
            ? 'DEBUG: ' . $exception->getMessage()
            : 'Error interno del servidor';

        $event->setResponse(ApiResponse::critical($message));
    }
}
