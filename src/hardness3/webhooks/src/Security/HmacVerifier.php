<?php

declare(strict_types=1);

namespace HardnessWebhooks\Security;

final class HmacVerifier
{
    public static function verify(string $secret, int $timestamp, string $rawBody, string $signature, int $now, int $replayWindowSeconds = 300): bool
    {
        if ($replayWindowSeconds < 0 || abs($now - $timestamp) > $replayWindowSeconds) {
            return false;
        }
        if (!preg_match('/^v1=[a-f0-9]{64}$/D', $signature)) {
            return false;
        }

        return hash_equals(HmacSigner::sign($secret, $timestamp, $rawBody), $signature);
    }
}
