<?php

declare(strict_types=1);

namespace HardnessMcp\Auth;

use RuntimeException;

final class ApiKeyVerifier
{
    /** @var array<string, string> */
    private array $peppers;

    /** @param array<string, string> $peppers */
    public function __construct(array $peppers) { $this->peppers = $peppers; }

    public function assertAvailable(string $version): void
    {
        if (!isset($this->peppers[$version]) || $this->peppers[$version] === '') { throw new RuntimeException('Credential pepper is unavailable.'); }
    }

    public function hash(string $secret, string $version): string
    {
        $this->assertAvailable($version);
        return hash_hmac('sha256', $secret, $this->peppers[$version], true);
    }

    public function verify(string $secret, string $hash, string $version): bool
    {
        return hash_equals($hash, $this->hash($secret, $version));
    }
}
