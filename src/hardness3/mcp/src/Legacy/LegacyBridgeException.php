<?php

declare(strict_types=1);

namespace HardnessMcp\Legacy;

use RuntimeException;

/** Safe, protocol-neutral failure raised by the isolated legacy runtime. */
final class LegacyBridgeException extends RuntimeException
{
    /** @var array<string,int|string|bool> */
    private array $publicDetails;

    /** @param array<string,int|string|bool> $publicDetails */
    public function __construct(?string $safeCode = null, array $publicDetails = [])
    {
        parent::__construct($safeCode ?? 'Legacy execution is unavailable.');
        $this->publicDetails = $publicDetails;
    }

    /** @return array<string,int|string|bool> */
    public function getPublicDetails(): array { return $this->publicDetails; }
}
