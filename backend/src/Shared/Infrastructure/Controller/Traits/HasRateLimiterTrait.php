<?php

namespace App\Shared\Infrastructure\Controller\Traits;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Contracts\Service\Attribute\Required;

trait HasRateLimiterTrait
{
    protected RateLimiterFactory $apiLimiter;

    #[Required]
    public function setApiLimiter(RateLimiterFactory $apiLimit): void
    {
        $this->apiLimiter = $apiLimit;
    }

    protected function checkRateLimit(Request $request): void
    {
        $limiter = $this->apiLimiter->create($request->getClientIp());

        if (!$limiter->consume(1)->isAccepted()) {
            throw new HttpException(Response::HTTP_TOO_MANY_REQUESTS, 'Rate limit exceeded');
        }
    }
}
