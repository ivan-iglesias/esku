<?php

namespace App\Shared\Infrastructure\EventListener;

use App\Shared\Infrastructure\Response\ApiResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Uid\Uuid;

class CorrelationIdListener
{
    public const HEADER_NAME = 'X-Correlation-ID';

    public function __construct(private RequestStack $requestStack) {}

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) return;

        $request = $event->getRequest();

        $correlationId = $request->headers->get(self::HEADER_NAME) ?? Uuid::v4()->toRfc4122();

        $request->attributes->set('correlation_id', $correlationId);

        // Configuramos ApiResponse con el stack de peticiones
        ApiResponse::init($this->requestStack);
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        if ($id = $request->attributes->get('correlation_id')) {
            $event->getResponse()->headers->set(self::HEADER_NAME, $id);
        }
    }
}
