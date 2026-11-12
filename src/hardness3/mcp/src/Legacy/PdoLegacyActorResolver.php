<?php

declare(strict_types=1);

namespace HardnessMcp\Legacy;

use PDO;
use Throwable;

/**
 * Resolves the legacy actor from the already-open MCP tenant connection.
 * It never accepts company, user, profile or approval data from a Tool.
 */
final class PdoLegacyActorResolver implements LegacyActorResolverInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function resolve(int $companyId, int $userId): ?LegacyActorContext
    {
        try {
            $statement = $this->pdo->prepare(
                "SELECT c019.C019_C004_Id AS company_id, c007.C007_Id AS user_id, c019.C019_C021_Id AS profile_id, c007.C007_Flag_Vendedor AS approval_flag, COALESCE(NULLIF(c004.C004_Fuso_Horario, ''), 'UTC') AS time_zone\n"
                . "FROM C007 c007\n"
                . "INNER JOIN C019 c019 ON c019.C019_C007_Id = c007.C007_Id\n"
                . "INNER JOIN C004 c004 ON c004.C004_Id = c019.C019_C004_Id AND c004.C004_Flag_Ativo = 'S'\n"
                . "WHERE c019.C019_C004_Id = :company_id AND c007.C007_Id = :user_id AND c007.C007_Ativo = 'S'"
            );
            $statement->execute(['company_id' => $companyId, 'user_id' => $userId]);
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $exception) {
            return null;
        }

        if (count($rows) !== 1) {
            return null;
        }

        $row = $rows[0];
        $profileId = isset($row['profile_id']) ? (int) $row['profile_id'] : 0;
        if ($profileId <= 0) {
            return null;
        }

        $approvalLevel = 1;
        if (($row['approval_flag'] ?? '') === 'S') {
            $approvalLevel = 2;
        } elseif (($row['approval_flag'] ?? '') === 'D') {
            $approvalLevel = 4;
        }

        try {
            return new LegacyActorContext(
                (int) ($row['company_id'] ?? 0),
                (int) ($row['user_id'] ?? 0),
                $profileId,
                $approvalLevel,
                (string) ($row['time_zone'] ?? '')
            );
        } catch (LegacyContextResolutionException $exception) {
            return null;
        }
    }
}
