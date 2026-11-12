<?php

declare(strict_types=1);

namespace HardnessMcp\OAuth;

use HardnessMcp\Scopes\ScopeCatalog;
use RuntimeException;

final class OAuthGrantService
{
    private OAuthGrantRepository $grants;
    private ScopeCatalog $scopeCatalog;

    public function __construct(OAuthGrantRepository $grants, ScopeCatalog $scopeCatalog)
    {
        $this->grants = $grants;
        $this->scopeCatalog = $scopeCatalog;
    }

    /** @param mixed $scopes */
    public function create(int $connectionId, int $clientId, int $userId, $scopes): int
    {
        if ($connectionId < 1 || $clientId < 1 || $userId < 1) { throw new RuntimeException('Invalid OAuth grant reference.'); }
        $connection = $this->grants->findConnection($connectionId);
        if ($connection === null || ($connection['MCP001_Status'] ?? null) !== 'active') { throw new RuntimeException('MCP connection is not available.'); }
        if (!$this->grants->hasActiveClient($clientId)) { throw new RuntimeException('OAuth client is not available.'); }
        $connectionScopes = json_decode((string) $connection['MCP001_Scopes'], true);
        $connectionScopes = $this->scopeCatalog->normalize($connectionScopes);
        $grantScopes = $this->scopeCatalog->normalize($scopes);
        foreach ($grantScopes as $scope) { if (!in_array($scope, $connectionScopes, true)) { throw new RuntimeException('OAuth grant scope exceeds MCP connection.'); } }
        return $this->grants->create($connectionId, $clientId, $userId, $grantScopes);
    }
}
