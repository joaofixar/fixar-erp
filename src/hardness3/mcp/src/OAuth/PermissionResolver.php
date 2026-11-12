<?php

declare(strict_types=1);

namespace HardnessMcp\OAuth;

use RuntimeException;

/**
 * OAuth-independent permission boundary. Future RBAC/ABAC layers supply only more
 * restrictive scope limits here; Tools continue to consume ExecutionContext unchanged.
 */
final class PermissionResolver
{
    /** @param string[] $connectionScopes @param string[] $grantScopes @param array<int,string[]> $futureScopeLimits @return string[] */
    public function resolve(array $connectionScopes, array $grantScopes, array $futureScopeLimits = []): array
    {
        $effective = $this->normalizedSet($connectionScopes);
        $effective = array_values(array_intersect($effective, $this->normalizedSet($grantScopes)));
        foreach ($futureScopeLimits as $limit) { $effective = array_values(array_intersect($effective, $this->normalizedSet($limit))); }
        sort($effective, SORT_STRING);
        return $effective;
    }

    /** @param string[] $scopes @return string[] */
    private function normalizedSet(array $scopes): array
    {
        $set = [];
        foreach ($scopes as $scope) {
            if (!is_string($scope) || preg_match('/\\A[a-z][a-z0-9_.:-]{0,99}\\z/', $scope) !== 1) { throw new RuntimeException('Invalid MCP scope.'); }
            $set[$scope] = true;
        }
        return array_keys($set);
    }
}
