<?php

declare(strict_types=1);

namespace HardnessMcp\RateLimit;

use RuntimeException;

final class RateLimitPolicy
{
    private RateLimiterInterface $limiter;
    /** @var array<string, mixed> */ private array $config;
    private string $keyPepper;

    /** @param array<string, mixed> $config */
    public function __construct(RateLimiterInterface $limiter, array $config)
    {
        $this->limiter = $limiter; $this->config = $config; $this->keyPepper = (string) ($config['rate_limit']['key_pepper'] ?? '');
        if ($this->keyPepper === '') { throw new RuntimeException('Rate limit key pepper is unavailable.'); }
    }

    public function preAuthentication(string $host, string $ip): RateLimitDecision { return $this->consume('preauth', $host . '|' . $ip); }
    public function authenticationFailure(string $host, string $publicId): RateLimitDecision { return $this->consume('authentication_failure', $host . '|' . $publicId); }
    public function connection(string $host, int $connectionId): RateLimitDecision { return $this->consume('connection', $host . '|' . $connectionId); }
    public function oauthAccessToken(string $host, int $connectionId, int $grantId, int $accessTokenId): RateLimitDecision { return $this->consume('oauth_access_token', $host . '|' . $connectionId . '|' . $grantId . '|' . $accessTokenId); }
    public function dcrRegistration(string $host, string $ip): RateLimitDecision { return $this->consume('dcr_registration', $host . '|' . $ip); }
    public function oauthToken(string $host, string $ip): RateLimitDecision { return $this->consume('oauth_token', $host . '|' . $ip); }
    private function consume(string $policy, string $subject): RateLimitDecision
    {
        $settings = (array) ($this->config['rate_limit'][$policy] ?? []);
        return $this->limiter->consume($policy . ':' . hash_hmac('sha256', $subject, $this->keyPepper), (int) ($settings['limit'] ?? 0), (int) ($settings['window_seconds'] ?? 0));
    }
}
