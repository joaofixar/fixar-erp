<?php

declare(strict_types=1);

namespace HardnessMcp\OAuth;

/**
 * Deliberately contains no issuance, refresh, parser or HTTP behavior in foundation stage.
 * Those operations are introduced only with the OAuth authorization/token endpoints.
 */
final class OAuthTokenService
{
    private OAuthTokenRepository $tokens;

    public function __construct(OAuthTokenRepository $tokens) { $this->tokens = $tokens; }

    public function revokeAccessTokensForGrant(int $grantId, string $reason): void
    {
        $this->tokens->revokeAccessTokensForGrant($grantId, $reason);
    }
}
