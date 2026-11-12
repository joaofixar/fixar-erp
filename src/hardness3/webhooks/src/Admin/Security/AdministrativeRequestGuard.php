<?php

declare(strict_types=1);

namespace HardnessWebhooks\Admin\Security;

/** Separates read-only GET endpoints from future CSRF-protected POST mutations. */
final class AdministrativeRequestGuard
{
    /** @param array<string,mixed>|null $query */
    public function assertReadRequest(?string $method = null, ?array $query = null): void
    {
        $method = strtoupper($method ?? (string) ($_SERVER['REQUEST_METHOD'] ?? ''));
        $query = $query ?? $_GET;
        if ($method !== 'GET' || isset($query['mutation']) || (isset($query['action']) && !in_array($query['action'], array('list', 'detail', 'catalog'), true))) {
            throw new AdministrativeRequestException('Invalid administrative request.');
        }
    }

    /** @param array<string,mixed>|null $post */
    public function assertMutationRequest(?string $method = null, ?array $post = null): void
    {
        $method = strtoupper($method ?? (string) ($_SERVER['REQUEST_METHOD'] ?? ''));
        $post = $post ?? $_POST;
        if ($method !== 'POST') {
            throw new AdministrativeRequestException('Invalid administrative request.');
        }

        (new CsrfGuard())->assertValid($post['csrf'] ?? null);
    }
}
