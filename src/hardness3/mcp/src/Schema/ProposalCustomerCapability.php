<?php

declare(strict_types=1);

namespace HardnessMcp\Schema;

use PDO;

/**
 * Detects the IDE-only proposal-customer extension as one atomic capability.
 * The reports must never reference a partial installation.
 */
final class ProposalCustomerCapability
{
    public static function isAvailable(PDO $pdo): bool
    {
        try {
            $statement = @$pdo->query("SELECT TABLE_NAME,COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND ((TABLE_NAME='T003A' AND COLUMN_NAME='T003A_D024_Id_Cliente_Proposta') OR (TABLE_NAME='T005A' AND COLUMN_NAME='T005A_D024_Id_Cliente_Proposta') OR (TABLE_NAME='T007A' AND COLUMN_NAME='T007A_D024_Id_Cliente_Proposta'))");
            if ($statement === false || !is_object($statement)) { return false; }
            $found = [];
            foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $table = strtoupper((string) ($row['TABLE_NAME'] ?? ''));
                $column = strtoupper((string) ($row['COLUMN_NAME'] ?? ''));
                $found[$table . '.' . $column] = true;
            }
            return isset($found['T003A.T003A_D024_ID_CLIENTE_PROPOSTA'], $found['T005A.T005A_D024_ID_CLIENTE_PROPOSTA'], $found['T007A.T007A_D024_ID_CLIENTE_PROPOSTA']);
        } catch (\Throwable $exception) {
            // Schema introspection is an optional enhancement; preserve base reports.
            return false;
        }
    }
}
