<?php

declare(strict_types=1);

namespace HardnessMcp\AdminUi\Security;

use RuntimeException;

/** Single boundary for every state-changing ERP MCP administration request. */
final class AdministrativeRequestGuard
{
    /** @param array<string,mixed>|null $post */
    public function assertMutationRequest(?string $method = null, ?array $post = null): void
    {
        $method = $method ?? (string) ($_SERVER['REQUEST_METHOD'] ?? '');
        $post = $post ?? $_POST;

        if (strtoupper($method) !== 'POST') {
            throw new RuntimeException('Invalid administrative request.');
        }

        // Deliberately read only POST: a query-string token is never valid.
        (new CsrfGuard())->assertValid($post['csrf'] ?? null);
    }
}
