<?php

declare(strict_types=1);

namespace HardnessWebhooks\Secrets;

use HardnessWebhooks\Exception\SecretUnavailableException;
use HardnessWebhooks\Security\SecretCipher;
use HardnessWebhooks\Security\SecretGenerator;

/** Transactional, internal API for immutable WHK007 versions. */
final class SecretVersionService
{
    /** @var SecretCipher */
    private $cipher;

    public function __construct(SecretCipher $cipher)
    {
        $this->cipher = $cipher;
    }

    public function createVersion(\mysqli $connection, int $endpointId, ?callable $assertEndpoint = null): CreatedSecretVersion
    {
        if ($endpointId <= 0) {
            throw new \InvalidArgumentException('Webhook endpoint id is invalid.');
        }
        $started = false;
        try {
            if (!$connection->begin_transaction()) {
                throw new \RuntimeException('Could not start webhook secret transaction.');
            }
            $started = true;
            $this->lockEndpoint($connection, $endpointId);
            if ($assertEndpoint !== null) {
                $assertEndpoint();
            }
            $version = $this->nextVersion($connection, $endpointId);
            $secret = SecretGenerator::generate();
            $encrypted = $this->cipher->encrypt($secret);
            $secretId = $this->insertVersion($connection, $endpointId, $version, $encrypted);
            $this->setCurrentVersion($connection, $endpointId, $secretId);
            if (!$connection->commit()) {
                throw new \RuntimeException('Could not commit webhook secret transaction.');
            }
            $started = false;

            return new CreatedSecretVersion($secretId, $version, $secret);
        } catch (\Throwable $exception) {
            if ($started) {
                $connection->rollback();
            }
            throw $exception;
        }
    }

    public function revokeVersion(\mysqli $connection, int $secretId): void
    {
        if ($secretId <= 0) {
            throw new \InvalidArgumentException('Webhook secret id is invalid.');
        }
        $started = false;
        try {
            if (!$connection->begin_transaction()) {
                throw new \RuntimeException('Could not start webhook secret transaction.');
            }
            $started = true;
            $endpointId = $this->lockVersion($connection, $secretId);
            $this->revokeLockedVersion($connection, $endpointId, $secretId);
            if (!$connection->commit()) { throw new \RuntimeException('Could not commit webhook secret transaction.'); }
            $started = false;
        } catch (\Throwable $exception) {
            if ($started) { $connection->rollback(); }
            throw $exception;
        }
    }

    /** Revokes the current version after the caller safely stops the endpoint. */
    public function revokeCurrentVersion(\mysqli $connection, int $endpointId, callable $deactivateEndpoint): void
    {
        if ($endpointId <= 0) { throw new \InvalidArgumentException('Webhook endpoint id is invalid.'); }
        $started = false;
        try {
            if (!$connection->begin_transaction()) { throw new \RuntimeException('Could not start webhook secret transaction.'); }
            $started = true;
            $this->lockEndpoint($connection, $endpointId);
            $secretId = $this->currentVersionId($connection, $endpointId);
            $deactivateEndpoint();
            $this->lockVersion($connection, $secretId);
            $this->revokeLockedVersion($connection, $endpointId, $secretId);
            if (!$connection->commit()) { throw new \RuntimeException('Could not commit webhook secret transaction.'); }
            $started = false;
        } catch (\Throwable $exception) {
            if ($started) { $connection->rollback(); }
            throw $exception;
        }
    }

    public function loadActiveSecret(\mysqli $connection, int $secretId): string
    {
        $statement = $connection->prepare("SELECT WHK007_Secret_Criptografado FROM WHK007 WHERE WHK007_Id = ? AND WHK007_Status = 'A' LIMIT 1");
        if ($statement === false) { throw new \RuntimeException('Could not prepare webhook secret lookup.'); }
        try {
            $statement->bind_param('i', $secretId);
            if (!$statement->execute()) { throw new \RuntimeException('Could not execute webhook secret lookup.'); }
            $statement->bind_result($encrypted);
            if (!$statement->fetch()) { throw new SecretUnavailableException('Webhook secret is unavailable.'); }
            return $this->cipher->decrypt((string) $encrypted);
        } finally { $statement->close(); }
    }

    public function loadActiveSecretForDelivery(\mysqli $connection, int $deliveryId): string
    {
        $statement = $connection->prepare("SELECT v.WHK007_Secret_Criptografado FROM WHK004 d INNER JOIN WHK007 v ON v.WHK007_Id = d.WHK004_WHK007_Id AND v.WHK007_Status = 'A' WHERE d.WHK004_Id = ? LIMIT 1");
        if ($statement === false) { throw new \RuntimeException('Could not prepare webhook delivery secret lookup.'); }
        try {
            $statement->bind_param('i', $deliveryId);
            if (!$statement->execute()) { throw new \RuntimeException('Could not execute webhook delivery secret lookup.'); }
            $statement->bind_result($encrypted);
            if (!$statement->fetch()) { throw new SecretUnavailableException('Webhook delivery secret is unavailable.'); }
            return $this->cipher->decrypt((string) $encrypted);
        } finally { $statement->close(); }
    }

    private function lockEndpoint(\mysqli $connection, int $endpointId): void
    {
        $statement = $connection->prepare('SELECT WHK001_Id FROM WHK001 WHERE WHK001_Id = ? FOR UPDATE');
        if ($statement === false) { throw new \RuntimeException('Could not prepare webhook endpoint lock.'); }
        try {
            $statement->bind_param('i', $endpointId);
            if (!$statement->execute()) { throw new \RuntimeException('Could not execute webhook endpoint lock.'); }
            $statement->bind_result($id);
            if (!$statement->fetch()) { throw new SecretUnavailableException('Webhook endpoint is unavailable.'); }
        } finally { $statement->close(); }
    }

    private function lockVersion(\mysqli $connection, int $secretId): int
    {
        $statement = $connection->prepare('SELECT WHK007_WHK001_Id FROM WHK007 WHERE WHK007_Id = ? FOR UPDATE');
        if ($statement === false) { throw new \RuntimeException('Could not prepare webhook secret lock.'); }
        try {
            $statement->bind_param('i', $secretId);
            if (!$statement->execute()) { throw new \RuntimeException('Could not execute webhook secret lock.'); }
            $statement->bind_result($endpointId);
            if (!$statement->fetch()) { throw new SecretUnavailableException('Webhook secret is unavailable.'); }
            return (int) $endpointId;
        } finally { $statement->close(); }
    }

    private function currentVersionId(\mysqli $connection, int $endpointId): int
    {
        $statement = $connection->prepare('SELECT WHK001_WHK007_Id_Atual FROM WHK001 WHERE WHK001_Id = ? FOR UPDATE');
        if ($statement === false) { throw new \RuntimeException('Could not prepare current webhook secret lookup.'); }
        try {
            $statement->bind_param('i', $endpointId);
            if (!$statement->execute()) { throw new \RuntimeException('Could not execute current webhook secret lookup.'); }
            $statement->bind_result($secretId);
            if (!$statement->fetch() || (int) $secretId <= 0) { throw new SecretUnavailableException('Webhook secret is unavailable.'); }
            return (int) $secretId;
        } finally { $statement->close(); }
    }

    private function revokeLockedVersion(\mysqli $connection, int $endpointId, int $secretId): void
    {
        $statement = $connection->prepare("UPDATE WHK007 SET WHK007_Status = 'R', WHK007_Data_Alteracao = UTC_TIMESTAMP() WHERE WHK007_Id = ? AND WHK007_WHK001_Id = ? AND WHK007_Status = 'A'");
        if ($statement === false) { throw new \RuntimeException('Could not prepare webhook secret revocation.'); }
        try { $statement->bind_param('ii', $secretId, $endpointId); if (!$statement->execute() || $statement->affected_rows !== 1) { throw new SecretUnavailableException('Webhook secret is unavailable.'); } } finally { $statement->close(); }
        $statement = $connection->prepare('UPDATE WHK001 SET WHK001_WHK007_Id_Atual = NULL, WHK001_Data_Alteracao = UTC_TIMESTAMP() WHERE WHK001_Id = ? AND WHK001_WHK007_Id_Atual = ?');
        if ($statement === false) { throw new \RuntimeException('Could not prepare webhook endpoint update.'); }
        try { $statement->bind_param('ii', $endpointId, $secretId); if (!$statement->execute() || $statement->affected_rows !== 1) { throw new \RuntimeException('Could not update webhook endpoint.'); } } finally { $statement->close(); }
    }

    private function nextVersion(\mysqli $connection, int $endpointId): int
    {
        $statement = $connection->prepare('SELECT COALESCE(MAX(WHK007_Versao), 0) FROM WHK007 WHERE WHK007_WHK001_Id = ?');
        if ($statement === false) { throw new \RuntimeException('Could not prepare webhook secret version lookup.'); }
        try {
            $statement->bind_param('i', $endpointId);
            if (!$statement->execute()) { throw new \RuntimeException('Could not execute webhook secret version lookup.'); }
            $statement->bind_result($version);
            if (!$statement->fetch()) { throw new \RuntimeException('Could not determine webhook secret version.'); }
            return (int) $version + 1;
        } finally { $statement->close(); }
    }

    private function insertVersion(\mysqli $connection, int $endpointId, int $version, string $encrypted): int
    {
        $statement = $connection->prepare("INSERT INTO WHK007 (WHK007_WHK001_Id, WHK007_Versao, WHK007_Secret_Criptografado, WHK007_Status) VALUES (?, ?, ?, 'A')");
        if ($statement === false) { throw new \RuntimeException('Could not prepare webhook secret insert.'); }
        try {
            $statement->bind_param('iis', $endpointId, $version, $encrypted);
            if (!$statement->execute()) { throw new \RuntimeException('Could not insert webhook secret: ' . $statement->error); }
            return (int) $connection->insert_id;
        } finally { $statement->close(); }
    }

    private function setCurrentVersion(\mysqli $connection, int $endpointId, int $secretId): void
    {
        $statement = $connection->prepare('UPDATE WHK001 SET WHK001_WHK007_Id_Atual = ?, WHK001_Data_Alteracao = UTC_TIMESTAMP() WHERE WHK001_Id = ?');
        if ($statement === false) { throw new \RuntimeException('Could not prepare webhook endpoint secret update.'); }
        try {
            $statement->bind_param('ii', $secretId, $endpointId);
            if (!$statement->execute() || $statement->affected_rows !== 1) { throw new \RuntimeException('Could not set current webhook secret.'); }
        } finally { $statement->close(); }
    }
}
