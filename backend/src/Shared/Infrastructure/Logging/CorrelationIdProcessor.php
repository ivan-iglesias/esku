<?php

namespace App\Shared\Infrastructure\Logging;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CorrelationIdProcessor implements ProcessorInterface
{
    public function __construct(private readonly RequestStack $requestStack) {}

    // Añadimos el ID al contexto del log
    public function __invoke(LogRecord $record): LogRecord
    {
        $request = $this->requestStack->getCurrentRequest();

        $correlationId = '------------------------------------';

        if ($request && $request->attributes->has('correlation_id')) {
            $correlationId = $request->attributes->get('correlation_id');
        }

        // Si no hay ID, devolvemos el log tal cual
        if (!$correlationId) {
            return $record;
        }

        // En Monolog 3.x / Symfony 8, usamos 'with' para crear una copia con el dato extra
        return $record->with(
            extra: array_merge($record->extra, ['correlation_id' => $correlationId])
        );
    }
}
