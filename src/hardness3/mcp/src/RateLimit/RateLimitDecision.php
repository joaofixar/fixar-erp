<?php

declare(strict_types=1);

namespace HardnessMcp\RateLimit;

final class RateLimitDecision
{
    private bool $allowed;
    private int $retryAfter;
    public function __construct(bool $allowed, int $retryAfter = 0) { $this->allowed = $allowed; $this->retryAfter = $retryAfter; }
    public function isAllowed(): bool { return $this->allowed; }
    public function getRetryAfter(): int { return $this->retryAfter; }
}
