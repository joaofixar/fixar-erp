<?php

declare(strict_types=1);

namespace HardnessWebhooks\Security;

final class SecretGenerator
{
    public static function generate(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}
