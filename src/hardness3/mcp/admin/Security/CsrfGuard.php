<?php

declare(strict_types=1);

namespace HardnessMcp\AdminUi\Security;

use RuntimeException;

final class CsrfGuard
{
    private const KEY = 'hardness_mcp_admin_csrf';

    public function token(): string
    {
        $this->ensureSessionIsActive();
        if (empty($_SESSION[self::KEY]) || !is_string($_SESSION[self::KEY])) {
            $_SESSION[self::KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::KEY];
    }

    /** Regenerate after a successful authentication or other identity change. */
    public static function regenerate(): void
    {
        self::ensureActiveSession();
        $_SESSION[self::KEY] = bin2hex(random_bytes(32));
    }

    /** Remove the token before a session is discarded. */
    public static function invalidate(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            unset($_SESSION[self::KEY]);
        }
    }

    public function assertValid($token): void
    {
        $this->ensureSessionIsActive();
        $expected = $_SESSION[self::KEY] ?? null;
        if (!is_string($token) || !is_string($expected) || !hash_equals($expected, $token)) {
            throw new RuntimeException('Invalid CSRF token.');
        }
    }

    private function ensureSessionIsActive(): void
    {
        self::ensureActiveSession();
    }

    private static function ensureActiveSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE && (headers_sent() || !session_start())) {
            throw new RuntimeException('MCP administrative session is unavailable.');
        }
    }
}
