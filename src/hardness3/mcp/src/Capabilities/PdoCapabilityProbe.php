<?php

declare(strict_types=1);

namespace HardnessMcp\Capabilities;

use PDO;
use Throwable;

/** Fail-closed PDO implementation. Identifiers are validated before interpolation. */
final class PdoCapabilityProbe implements CapabilityProbeInterface
{
    private PDO $pdo;
    private ?string $installationProfile;

    public function __construct(PDO $pdo, ?string $installationProfile = null)
    {
        $this->pdo = $pdo;
        $this->installationProfile = $installationProfile;
    }

    public function hasInstallationProfile(string $profile): bool
    {
        return $this->installationProfile !== null && hash_equals($profile, $this->installationProfile);
    }

    public function hasColumns(string $table, array $columns): bool
    {
        if (!$this->identifier($table) || $columns === []) { return false; }
        foreach ($columns as $column) { if (!is_string($column) || !$this->identifier($column)) { return false; } }
        try {
            $statement = @$this->pdo->prepare('SELECT UPPER(COLUMN_NAME) AS column_name FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND UPPER(TABLE_NAME)=UPPER(:table)');
            if ($statement === false || !@$statement->execute(['table' => $table])) { return false; }
            $found = [];
            $rows = @$statement->fetchAll(PDO::FETCH_ASSOC);
            if (!is_array($rows)) { return false; }
            foreach ($rows as $row) { $found[strtoupper((string) ($row['column_name'] ?? ''))] = true; }
            foreach ($columns as $column) { if (!isset($found[strtoupper($column)])) { return false; } }
            return true;
        } catch (Throwable $exception) { return false; }
    }

    public function hasRecord(string $table, string $column, $value): bool
    {
        if (!$this->identifier($table) || !$this->identifier($column) || (!is_scalar($value) && $value !== null)) { return false; }
        try {
            $statement = @$this->pdo->prepare('SELECT 1 FROM `' . $table . '` WHERE `' . $column . '`=:value LIMIT 1');
            if ($statement === false || !@$statement->execute(['value' => $value])) { return false; }
            return @$statement->fetchColumn() !== false;
        } catch (Throwable $exception) { return false; }
    }

    private function identifier(string $value): bool { return preg_match('/\A[A-Za-z][A-Za-z0-9_]*\z/', $value) === 1; }
}
