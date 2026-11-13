<?php

declare(strict_types=1);

namespace HardnessWebhooks\Security;

use HardnessWebhooks\Exception\MasterKeyException;

/** Loads the Webhooks-only AES-256 key without persisting it in the module. */
final class MasterKeyProvider
{
    public static function fromEnvironment(): string
    {
        $key = getenv('WEBHOOKS_MASTER_KEY');
        if (!is_string($key) || $key === '') {
            $key = self::developmentFallback();
        }
        if (!is_string($key) || $key === '') {
            throw new MasterKeyException('Webhook master key is unavailable.');
        }

        $decoded = self::base64UrlDecode($key);
        if ($decoded === null || strlen($decoded) !== 32) {
            throw new MasterKeyException('Webhook master key format is invalid.');
        }

        return $decoded;
    }

    private static function developmentFallback(): string
    {
        $environment = (string) (getenv('WEBHOOKS_ENVIRONMENT') ?: '');
        if ($environment !== '' && $environment !== 'dev') {
            return '';
        }

        $path = (string) (getenv('WEBHOOKS_DEV_LOCAL_CONFIG_PATH') ?: '/www/.local-secrets/webhooks.local.php');
        if (!is_file($path) || is_link($path)) {
            return '';
        }
        $permissions = fileperms($path);
        if ($permissions === false || (($permissions & 0777) !== 0600)) {
            throw new MasterKeyException('Webhook local development configuration permissions are invalid.');
        }

        $config = require $path;
        if (!is_array($config) || ($config['environment'] ?? null) !== 'dev') {
            throw new MasterKeyException('Webhook local development configuration is invalid.');
        }
        $key = $config['WEBHOOKS_MASTER_KEY'] ?? '';

        return is_string($key) ? $key : '';
    }

    private static function base64UrlDecode(string $value): ?string
    {
        if (!preg_match('/^[A-Za-z0-9_-]+$/', $value)) {
            return null;
        }
        $decoded = base64_decode(strtr($value, '-_', '+/') . str_repeat('=', (4 - strlen($value) % 4) % 4), true);

        return $decoded === false ? null : $decoded;
    }
}
