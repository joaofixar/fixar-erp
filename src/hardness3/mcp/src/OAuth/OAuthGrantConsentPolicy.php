<?php

declare(strict_types=1);

namespace HardnessMcp\OAuth;

/**
 * Decides whether a fresh, explicit OAuth consent can keep the active grant.
 *
 * Grant scopes are a consent snapshot, not a live view of MCP001.  A different
 * effective scope set therefore needs a new grant so no existing token or
 * refresh-token family can silently acquire (or retain after a reduction) a
 * different permission set.
 */
final class OAuthGrantConsentPolicy
{
    /** @param string[] $activeScopes @param string[] $consentedScopes */
    public function mustReplace(array $activeScopes, array $consentedScopes): bool
    {
        return $this->normalized($activeScopes) !== $this->normalized($consentedScopes);
    }

    /** @param string[] $scopes @return string[] */
    private function normalized(array $scopes): array
    {
        $set = [];
        foreach ($scopes as $scope) {
            if (!is_string($scope)) continue;
            $set[$scope] = true;
        }
        $result = array_keys($set);
        sort($result, SORT_STRING);
        return $result;
    }
}
