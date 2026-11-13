<?php

declare(strict_types=1);

namespace HardnessMcp\Schema;

use PDO;
use Throwable;

/**
 * Detects whether the current company enabled the IDE SMT order flow.
 *
 * This is a tenant/company setting, not a schema capability. Any missing or
 * unreadable configuration must keep the MCP feature unavailable.
 */
final class IdeSmtOrderGenerationCapability
{
    private const SETTING = 'SMT_habilitaMudarGerarPedido';

    public static function isEnabled(PDO $pdo, int $companyId): bool
    {
        if ($companyId <= 0) {
            return false;
        }

        try {
            $statement = @$pdo->prepare(
                'SELECT C031_Valor FROM C031 '
                . 'WHERE C031_C004_Id=:company_id AND C031_Campo=:setting '
                . 'ORDER BY C031_Id DESC LIMIT 1'
            );
            if ($statement === false || !is_object($statement)) {
                return false;
            }
            if (!$statement->execute([
                'company_id' => $companyId,
                'setting' => self::SETTING,
            ])) {
                return false;
            }

            $row = $statement->fetch(PDO::FETCH_ASSOC);
            return is_array($row) && ($row['C031_Valor'] ?? null) === 'S';
        } catch (Throwable $exception) {
            return false;
        }
    }
}
