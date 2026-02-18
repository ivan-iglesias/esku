<?php

namespace App\Shared\Domain\Exception;

use Exception;

class BusinessException extends Exception
{
    public function __construct(
        private readonly BusinessErrorCode $businessCode,
        ?string $message = null,
        ?int $code = null
    ) {
        parent::__construct(
            $message ?? $this->businessCode->defaultMessage(),
            $code ?? $this->businessCode->httpCode()
        );
    }

    public function getBusinessCode(): string
    {
        return $this->businessCode->value;
    }
}
