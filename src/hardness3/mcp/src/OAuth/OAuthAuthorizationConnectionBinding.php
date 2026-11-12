<?php

declare(strict_types=1);

namespace HardnessMcp\OAuth;

use RuntimeException;

/** Keeps a connection-specific OAuth transaction bound to its original MCP001 row. */
final class OAuthAuthorizationConnectionBinding
{
    /** @param array<int,array<string,mixed>> $connections @return array<int,array<string,mixed>> */
    public function eligibleConnections(?int $boundConnectionId, array $connections): array
    {
        if ($boundConnectionId === null) {
            return $connections;
        }
        return array_values(array_filter($connections, static function (array $connection) use ($boundConnectionId): bool {
            return (int) ($connection['id'] ?? 0) === $boundConnectionId;
        }));
    }

    public function confirm(?int $boundConnectionId, int $requestedConnectionId): int
    {
        if ($requestedConnectionId < 1 || ($boundConnectionId !== null && $boundConnectionId !== $requestedConnectionId)) {
            throw new RuntimeException('Connection is not eligible.');
        }
        return $requestedConnectionId;
    }
}
