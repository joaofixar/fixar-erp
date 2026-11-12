<?php

declare(strict_types=1);

namespace HardnessMcp\Auth;

final class ApiKeyGenerator
{
    private const BASE32 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /** @return array{key:string, public_id:string, secret:string} */
    public function generate(): array
    {
        $publicId = $this->base32(random_bytes(10));
        $secret = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
        return ['key' => 'hrd_mcp_v1_' . $publicId . '_' . $secret, 'public_id' => $publicId, 'secret' => $secret];
    }

    private function base32(string $bytes): string
    {
        $bits = '';
        foreach (str_split($bytes) as $byte) { $bits .= str_pad(decbin(ord($byte)), 8, '0', STR_PAD_LEFT); }
        $result = '';
        foreach (str_split($bits, 5) as $chunk) { $result .= self::BASE32[bindec(str_pad($chunk, 5, '0', STR_PAD_RIGHT))]; }
        return $result;
    }
}
