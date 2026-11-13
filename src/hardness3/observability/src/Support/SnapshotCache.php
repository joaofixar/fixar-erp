<?php
declare(strict_types=1);

namespace Hardness\Observability\Support;

/** Cache curto para impedir uma varredura de todos os tenants a cada polling. */
final class SnapshotCache
{
    public function get(string $key): ?array
    {
        if (!$this->useApcu()) {
            return null;
        }

        $value = apcu_fetch($key, $found);
        return $found && is_array($value) ? $value : null;
    }

    public function put(string $key, array $snapshot, int $ttl): void
    {
        if ($this->useApcu()) {
            apcu_store($key, $snapshot, $ttl);
        }
    }

    private function useApcu(): bool
    {
        return function_exists('apcu_enabled') && apcu_enabled();
    }
}
