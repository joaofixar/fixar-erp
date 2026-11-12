<?php

declare(strict_types=1);

namespace HardnessMcp\RateLimit;

interface RateLimiterInterface
{
    public function consume(string $key, int $limit, int $windowSeconds): RateLimitDecision;
}
