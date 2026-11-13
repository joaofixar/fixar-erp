<?php

declare(strict_types=1);

namespace HardnessMcp\Auth;

final class ApiKey
{
    private string $publicId;
    private string $secret;

    public function __construct(string $publicId, string $secret)
    {
        $this->publicId = $publicId;
        $this->secret = $secret;
    }

    public function getPublicId(): string { return $this->publicId; }
    public function getSecret(): string { return $this->secret; }
}
