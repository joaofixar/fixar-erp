<?php

declare(strict_types=1);

namespace HardnessWebhooks\Security;

use HardnessWebhooks\Exception\SecretCipherException;

final class SecretCipher
{
    private const CIPHER = 'aes-256-gcm';
    private const FORMAT_VERSION = 1;

    /** @var string */
    private $key;

    public function __construct(string $key)
    {
        if (strlen($key) !== 32) {
            throw new SecretCipherException('Webhook encryption key is invalid.');
        }
        $this->key = $key;
    }

    public function encrypt(string $secret): string
    {
        if ($secret === '') {
            throw new SecretCipherException('Webhook secret cannot be empty.');
        }
        $iv = random_bytes(12);
        $tag = '';
        $ciphertext = openssl_encrypt($secret, self::CIPHER, $this->key, OPENSSL_RAW_DATA, $iv, $tag, '', 16);
        if ($ciphertext === false || strlen($tag) !== 16) {
            throw new SecretCipherException('Webhook secret encryption failed.');
        }

        $envelope = array(
            'v' => self::FORMAT_VERSION,
            'iv' => self::base64UrlEncode($iv),
            'tag' => self::base64UrlEncode($tag),
            'ciphertext' => self::base64UrlEncode($ciphertext),
        );
        $encoded = json_encode($envelope, JSON_UNESCAPED_SLASHES);
        if ($encoded === false) {
            throw new SecretCipherException('Webhook secret encryption failed.');
        }

        return $encoded;
    }

    public function decrypt(string $envelope): string
    {
        $decoded = json_decode($envelope, true);
        if (!is_array($decoded) || count($decoded) !== 4 || ($decoded['v'] ?? null) !== self::FORMAT_VERSION
            || !isset($decoded['iv'], $decoded['tag'], $decoded['ciphertext'])
            || !is_string($decoded['iv']) || !is_string($decoded['tag']) || !is_string($decoded['ciphertext'])) {
            throw new SecretCipherException('Webhook secret ciphertext format is invalid.');
        }
        $iv = self::base64UrlDecode($decoded['iv']);
        $tag = self::base64UrlDecode($decoded['tag']);
        $ciphertext = self::base64UrlDecode($decoded['ciphertext']);
        if ($iv === null || strlen($iv) !== 12 || $tag === null || strlen($tag) !== 16 || $ciphertext === null || $ciphertext === '') {
            throw new SecretCipherException('Webhook secret ciphertext format is invalid.');
        }
        $secret = openssl_decrypt($ciphertext, self::CIPHER, $this->key, OPENSSL_RAW_DATA, $iv, $tag);
        if ($secret === false || $secret === '') {
            throw new SecretCipherException('Webhook secret ciphertext authentication failed.');
        }

        return $secret;
    }

    private static function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $value): ?string
    {
        if ($value === '' || !preg_match('/^[A-Za-z0-9_-]+$/', $value)) {
            return null;
        }
        $decoded = base64_decode(strtr($value, '-_', '+/') . str_repeat('=', (4 - strlen($value) % 4) % 4), true);

        return $decoded === false ? null : $decoded;
    }
}
