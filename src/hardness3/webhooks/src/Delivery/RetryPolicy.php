<?php

declare(strict_types=1);

namespace HardnessWebhooks\Delivery;

/** The Task 09 retry contract. Delays are seconds after a failed attempt. */
final class RetryPolicy
{
    /** @var array<int,int> */ private $delays;
    /** @var float */ private $jitterFraction;
    /** @var int */ private $retryAfterCap;
    /** @var callable|null */ private $random;

    /** @param callable|null $random Receives min/max integers; tests may return a deterministic value. */
    public function __construct(array $delays = array(60, 300, 900, 3600, 21600), float $jitterFraction = 0.10, int $retryAfterCap = 21600, ?callable $random = null)
    {
        if ($delays === array() || $jitterFraction < 0 || $jitterFraction > 1 || $retryAfterCap < 1) { throw new \InvalidArgumentException('Webhook retry policy is invalid.'); }
        foreach ($delays as $delay) { if (!is_int($delay) || $delay < 1) { throw new \InvalidArgumentException('Webhook retry delays are invalid.'); } }
        $this->delays = array_values($delays); $this->jitterFraction = $jitterFraction; $this->retryAfterCap = $retryAfterCap; $this->random = $random;
    }

    public function maxAttempts(): int { return count($this->delays) + 1; }
    public function isRetryable(?int $status, ?string $error): bool
    { return $error !== null || $status === 408 || $status === 425 || $status === 429 || ($status !== null && $status >= 500 && $status <= 599); }
    public function resultName(?int $status, ?string $error): string
    {
        if ($error !== null) { return 'transport_error'; }
        if ($status !== null && $status >= 200 && $status < 300) { return 'http_success'; }
        if ($status !== null && $status >= 300 && $status < 400) { return 'http_redirect'; }
        if ($status === 408 || $status === 425 || $status === 429) { return 'http_retryable'; }
        if ($status !== null && $status >= 500) { return 'http_server_error'; }
        return 'http_client_error';
    }
    public function retryDelay(int $attemptNumber, ?string $retryAfter): ?int
    {
        if ($attemptNumber >= $this->maxAttempts()) { return null; }
        $delay = $this->delays[$attemptNumber - 1];
        $hint = $this->parseRetryAfter($retryAfter);
        if ($hint !== null) { $delay = min($hint, $this->retryAfterCap); }
        return $this->jitter($delay);
    }
    public function parseRetryAfter(?string $value): ?int
    {
        if ($value === null || !preg_match('/^\s*([0-9]{1,8})\s*$/', $value, $match)) { return null; }
        $seconds = (int) $match[1]; return $seconds > 0 ? $seconds : null;
    }
    private function jitter(int $delay): int
    {
        if ($this->jitterFraction == 0.0) { return $delay; }
        $spread = (int) floor($delay * $this->jitterFraction);
        if ($spread < 1) { return $delay; }
        $random = $this->random; $offset = $random === null ? random_int(-$spread, $spread) : (int) $random(-$spread, $spread);
        return max(1, $delay + max(-$spread, min($spread, $offset)));
    }
}
