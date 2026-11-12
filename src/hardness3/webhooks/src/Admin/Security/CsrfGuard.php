<?php

declare(strict_types=1);

namespace HardnessWebhooks\Admin\Security;

/** CSRF token held in the ERP PHP session; no Webhooks session or cookie exists. */
final class CsrfGuard
{
    private const KEY = 'hardness_webhooks_admin_csrf';

    public function token(): string
    {
        $this->ensureSessionIsActive();
        if (!isset($_SESSION[self::KEY]) || !is_string($_SESSION[self::KEY])) {
            $_SESSION[self::KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::KEY];
    }

    /** @param mixed $token */
    public function assertValid($token): void
    {
        $this->ensureSessionIsActive();
        $expected = $_SESSION[self::KEY] ?? null;
        if (!is_string($token) || !is_string($expected) || !hash_equals($expected, $token)) {
            throw new AdministrativeRequestException('Invalid administrative request.');
        }
    }

    private function ensureSessionIsActive(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE && (headers_sent() || !session_start())) {
            throw new AdministrativeRequestException('Administrative session is unavailable.');
        }
    }
}
