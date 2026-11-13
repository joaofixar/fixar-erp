<?php

declare(strict_types=1);

namespace HardnessMcp\RateLimit;

final class ApcuRateLimiter implements RateLimiterInterface
{
    private static bool $validated = false;

    public function __construct()
    {
        $this->assertAvailable();
    }

    public function consume(string $key, int $limit, int $windowSeconds): RateLimitDecision
    {
        if ($limit < 1 || $windowSeconds < 1) { throw new RateLimiterUnavailableException('Invalid rate limit configuration.'); }
        $bucket = 'hardness:mcp:rl:v1:' . $key . ':' . (string) floor(time() / $windowSeconds);
        if (!apcu_add($bucket, 1, $windowSeconds)) {
            $value = apcu_inc($bucket, 1, $success);
            if (!$success || $value === false) {
                // A bucket can expire between add and inc. One retry keeps that race from becoming a 503.
                if (!apcu_add($bucket, 1, $windowSeconds)) { throw new RateLimiterUnavailableException('APCu increment failed.'); }
                $value = 1;
            }
            if ((int) $value > $limit) { return new RateLimitDecision(false, max(1, $windowSeconds - (time() % $windowSeconds))); }
        }
        return new RateLimitDecision(true);
    }

    private function assertAvailable(): void
    {
        if (self::$validated) { return; }
        if (!extension_loaded('apcu') || !function_exists('apcu_enabled') || !function_exists('apcu_add') || !function_exists('apcu_inc') || !function_exists('apcu_delete') || apcu_enabled() !== true) {
            throw new RateLimiterUnavailableException('APCu is not available.');
        }
        $key = 'hardness:mcp:probe:' . bin2hex(random_bytes(16));
        if (!apcu_add($key, 1, 5)) { throw new RateLimiterUnavailableException('APCu probe add failed.'); }
        $value = apcu_inc($key, 1, $success);
        $deleted = apcu_delete($key);
        if (!$success || $value !== 2 || !$deleted) { throw new RateLimiterUnavailableException('APCu probe failed.'); }
        self::$validated = true;
    }
}
