<?php

declare(strict_types=1);

namespace HardnessMcp\OAuth;

use PDO;

/** Reserved persistence seam for the next stage; it does not issue or validate tokens. */
final class OAuthTokenRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function revokeAccessTokensForGrant(int $grantId, string $reason): void
    {
        $statement = $this->pdo->prepare("UPDATE MCP007 SET MCP007_Status='revoked',MCP007_Data_Revogacao=UTC_TIMESTAMP(),MCP007_Motivo_Revogacao=:reason WHERE MCP007_MCP005_Id=:grant_id AND MCP007_Status='active'");
        $statement->execute(['grant_id' => $grantId, 'reason' => substr($reason, 0, 255)]);
    }
}
