<?php

namespace App\Shared\Infrastructure\Logging;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CorrelationIdProcessor implements ProcessorInterface
{
    public function __construct(private RequestStack $requestStack) {}

    // Añadimos el ID al contexto del log
    public function __invoke(LogRecord $record): LogRecord
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request && $request->attributes->has('correlation_id')) {
            $record->extra['correlation_id'] = $request->attributes->get('correlation_id');
        }

        return $record;
    }
}
