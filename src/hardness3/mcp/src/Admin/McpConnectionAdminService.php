<?php

declare(strict_types=1);

namespace HardnessMcp\Admin;

use HardnessMcp\Audit\McpAuditService;
use HardnessMcp\Auth\ApiKeyGenerator;
use HardnessMcp\Auth\ApiKeyVerifier;
use HardnessMcp\Scopes\ScopeCatalog;
use DateTimeImmutable;
use DateTimeZone;
use PDO;
use RuntimeException;

/** Canonical service for a future CLI, ERP screen, or internal API; no interface is exposed in Etapa 2. */
final class McpConnectionAdminService
{
    private PDO $pdo;
    private ApiKeyGenerator $generator;
    private ApiKeyVerifier $verifier;
    private McpAuditService $audit;
    private string $currentPepperVersion;
    private int $rotationGraceSeconds;
    private string $auditHost;
    private string $auditIp;
    private ?int $actorUserId;
    private ScopeCatalog $scopeCatalog;

    /** @param array<string,mixed> $config */
    public function __construct(PDO $pdo, array $config, string $auditHost = 'admin', string $auditIp = '', ?int $actorUserId = null)
    {
        $this->pdo = $pdo;
        $this->generator = new ApiKeyGenerator();
        $this->verifier = new ApiKeyVerifier((array) ($config['auth']['credential_peppers'] ?? []));
        $this->audit = new McpAuditService($pdo);
        $this->currentPepperVersion = (string) ($config['auth']['credential_pepper_current_version'] ?? '');
        $this->rotationGraceSeconds = (int) ($config['auth']['credential_rotation_grace_seconds'] ?? 0);
        $this->auditHost = $auditHost;
        $this->auditIp = $auditIp;
        $this->actorUserId = $actorUserId !== null && $actorUserId > 0 ? $actorUserId : null;
        $this->scopeCatalog = new ScopeCatalog();
        if ($this->rotationGraceSeconds < 1) { throw new RuntimeException('Invalid credential rotation grace period.'); }
        $this->verifier->assertAvailable($this->currentPepperVersion);
    }

    /** @param string[] $scopes @return array{connection_id:int,api_key:string} */
    public function createConnection(string $name, int $companyId, ?int $responsibleUserId, array $scopes, ?string $expiresAt = null): array
    {
        $this->assertName($name);
        $this->assertScopes($scopes);
        if ($this->scopeCatalog->requiresErpActor($scopes) && $responsibleUserId === null) {
            throw new RuntimeException('A responsible user is required for these permissions.');
        }
        $expiresAt = $this->normalizeFutureExpiration($expiresAt);
        $this->assertCompanyExists($companyId);
        $this->assertResponsibleUser($responsibleUserId, $companyId);
        $publicId = $this->generatePublicId();
        $this->pdo->beginTransaction();
        try {
            $this->pdo->prepare("INSERT INTO MCP001 (MCP001_Nome,MCP001_C004_Id,MCP001_C007_Id,MCP001_Public_Id,MCP001_Scopes,MCP001_Status,MCP001_Data_Expiracao) VALUES (:name,:company,:responsible,:public_id,:scopes,'active',:expires)")
                ->execute(['name' => $name, 'company' => $companyId, 'responsible' => $responsibleUserId, 'public_id' => $publicId, 'scopes' => json_encode(array_values($scopes)), 'expires' => $expiresAt]);
            $connectionId = (int) $this->pdo->lastInsertId();
            $credential = $this->createCredential($connectionId, $expiresAt);
            $this->audit->recordAdministrative('connection_created', $connectionId, $credential['id'], $companyId, $this->auditHost, $this->auditIp, $this->actorUserId);
            $this->pdo->commit();
            return ['connection_id' => $connectionId, 'public_id' => $publicId, 'api_key' => $credential['key']];
        } catch (\Throwable $exception) {
            $this->rollback();
            throw $exception;
        }
    }

    public function rotateCredential(int $connectionId, ?string $newCredentialExpiresAt = null): string
    {
        $newCredentialExpiresAt = $this->normalizeFutureExpiration($newCredentialExpiresAt);
        return $this->rotateCredentialTransactionally($connectionId, null, $newCredentialExpiresAt);
    }

    private function rotateCredentialTransactionally(int $connectionId, ?int $companyId, ?string $newCredentialExpiresAt): string
    {
        $this->pdo->beginTransaction();
        try {
            $connection = $companyId === null ? $this->lockConnection($connectionId) : $this->lockConnectionForCompany($connectionId, $companyId);
            $this->assertConnectionCanIssueCredentials($connection);
            $count = $this->pdo->prepare("SELECT COUNT(*) FROM MCP002 WHERE MCP002_MCP001_Id=:id AND MCP002_Status='active' AND (MCP002_Data_Expiracao IS NULL OR MCP002_Data_Expiracao>UTC_TIMESTAMP())");
            $count->execute(['id' => $connectionId]);
            if ((int) $count->fetchColumn() >= 2) { throw new RuntimeException('Maximum active credentials reached.'); }

            $credential = $this->createCredential($connectionId, $newCredentialExpiresAt);
            $old = $this->pdo->prepare("UPDATE MCP002 SET MCP002_Data_Expiracao=CASE WHEN MCP002_Data_Expiracao IS NULL OR MCP002_Data_Expiracao>UTC_TIMESTAMP()+INTERVAL {$this->rotationGraceSeconds} SECOND THEN UTC_TIMESTAMP()+INTERVAL {$this->rotationGraceSeconds} SECOND ELSE MCP002_Data_Expiracao END, MCP002_Substituida_Por_MCP002_Id=:new_set WHERE MCP002_MCP001_Id=:connection_id AND MCP002_Id<>:new_match AND MCP002_Status='active' AND (MCP002_Data_Expiracao IS NULL OR MCP002_Data_Expiracao>UTC_TIMESTAMP())");
            $old->execute(['new_set' => $credential['id'], 'connection_id' => $connectionId, 'new_match' => $credential['id']]);
            $this->audit->recordAdministrative('credential_rotated', $connectionId, $credential['id'], (int) $connection['MCP001_C004_Id'], $this->auditHost, $this->auditIp, $this->actorUserId);
            $this->pdo->commit();
            return $credential['key'];
        } catch (\Throwable $exception) {
            $this->rollback();
            throw $exception;
        }
    }

    public function generateFirstCredential(int $connectionId, int $companyId, ?string $expiresAt = null): string
    {
        $expiresAt = $this->normalizeFutureExpiration($expiresAt);
        $this->pdo->beginTransaction();
        try {
            $connection = $this->lockConnectionForCompany($connectionId, $companyId);
            $this->assertConnectionCanIssueCredentials($connection);
            $count = $this->pdo->prepare("SELECT COUNT(*) FROM MCP002 WHERE MCP002_MCP001_Id=:id AND MCP002_Status='active' AND (MCP002_Data_Expiracao IS NULL OR MCP002_Data_Expiracao>UTC_TIMESTAMP())");
            $count->execute(['id' => $connectionId]);
            if ((int) $count->fetchColumn() > 0) { throw new RuntimeException('Credential already exists.'); }
            $credential = $this->createCredential($connectionId, $expiresAt);
            $this->audit->recordAdministrative('credential_created', $connectionId, $credential['id'], $companyId, $this->auditHost, $this->auditIp, $this->actorUserId);
            $this->pdo->commit();
            return $credential['key'];
        } catch (\Throwable $exception) { $this->rollback(); throw $exception; }
    }

    public function rotateCredentialForCompany(int $connectionId, int $companyId, ?string $expiresAt = null): string
    {
        return $this->rotateCredentialTransactionally($connectionId, $companyId, $expiresAt);
    }

    public function revokeCredentialForCompany(int $credentialId, int $connectionId, int $companyId, string $reason): void
    {
        $reason = $this->normalizeRevocationReason($reason);
        $this->pdo->beginTransaction();
        try {
            $credential = $this->lockCredential($credentialId);
            if ((int) $credential['MCP001_Id'] !== $connectionId || (int) $credential['MCP001_C004_Id'] !== $companyId) { throw new RuntimeException('Credential not found.'); }
            $statement = $this->pdo->prepare("UPDATE MCP002 SET MCP002_Status='revoked',MCP002_Data_Revogacao=UTC_TIMESTAMP(),MCP002_Motivo_Revogacao=:reason WHERE MCP002_Id=:id AND MCP002_Status='active'");
            $statement->execute(['id' => $credentialId, 'reason' => $reason]);
            if ($statement->rowCount() !== 1) { throw new RuntimeException('Credential is not active.'); }
            $this->audit->recordAdministrative('credential_revoked', $connectionId, $credentialId, $companyId, $this->auditHost, $this->auditIp, $this->actorUserId);
            $this->pdo->commit();
        } catch (\Throwable $exception) { $this->rollback(); throw $exception; }
    }

    public function revokeCredential(int $credentialId, string $reason): void
    {
        $reason = $this->normalizeRevocationReason($reason);
        $this->pdo->beginTransaction();
        try {
            $credential = $this->lockCredential($credentialId);
            $statement = $this->pdo->prepare("UPDATE MCP002 SET MCP002_Status='revoked',MCP002_Data_Revogacao=UTC_TIMESTAMP(),MCP002_Motivo_Revogacao=:reason WHERE MCP002_Id=:id AND MCP002_Status='active'");
            $statement->execute(['id' => $credentialId, 'reason' => $reason]);
            if ($statement->rowCount() !== 1) { throw new RuntimeException('Credential is not active.'); }
            $this->audit->recordAdministrative('credential_revoked', (int) $credential['MCP001_Id'], $credentialId, (int) $credential['MCP001_C004_Id'], $this->auditHost, $this->auditIp, $this->actorUserId);
            $this->pdo->commit();
        } catch (\Throwable $exception) {
            $this->rollback();
            throw $exception;
        }
    }

    /** @param string[] $scopes */
    public function updateConnection(int $connectionId, string $name, array $scopes, string $status): void
    {
        $this->assertName($name);
        $this->assertScopes($scopes);
        if (!in_array($status, ['active', 'disabled', 'revoked'], true)) { throw new RuntimeException('Invalid connection status.'); }
        $this->pdo->beginTransaction();
        try {
            $connection = $this->lockConnection($connectionId);
            if ($this->requiresApiKeyResponsible($scopes, $connectionId) && !$this->hasValidResponsibleUser($connectionId, (int) $connection['MCP001_C004_Id'])) { throw new RuntimeException('A responsible user is required for API key permissions.'); }
            $this->pdo->prepare('UPDATE MCP001 SET MCP001_Nome=:name,MCP001_Scopes=:scopes,MCP001_Status=:status,MCP001_Data_Alteracao=CURRENT_TIMESTAMP WHERE MCP001_Id=:id')
                ->execute(['id' => $connectionId, 'name' => $name, 'scopes' => json_encode(array_values($scopes)), 'status' => $status]);
            $event = $status === 'active' ? 'connection_enabled' : ($status === 'disabled' ? 'connection_disabled' : 'connection_revoked');
            $this->audit->recordAdministrative($event, $connectionId, null, (int) $connection['MCP001_C004_Id'], $this->auditHost, $this->auditIp, $this->actorUserId);
            $this->pdo->commit();
        } catch (\Throwable $exception) {
            $this->rollback();
            throw $exception;
        }
    }

    /** @param mixed $scopes */
    public function updateScopesForCompany(int $connectionId, int $companyId, $scopes): array
    {
        $normalized = $this->scopeCatalog->normalize($scopes);
        $this->pdo->beginTransaction();
        try {
            $connection = $this->lockConnectionForCompany($connectionId, $companyId);
            if ($this->requiresApiKeyResponsible($normalized, $connectionId) && !$this->hasValidResponsibleUser($connectionId, $companyId)) { throw new RuntimeException('A responsible user is required for API key permissions.'); }
            $this->pdo->prepare('UPDATE MCP001 SET MCP001_Scopes=:scopes,MCP001_Data_Alteracao=UTC_TIMESTAMP() WHERE MCP001_Id=:id AND MCP001_C004_Id=:company')
                ->execute(['scopes' => json_encode($normalized, JSON_UNESCAPED_SLASHES), 'id' => $connectionId, 'company' => $companyId]);
            $this->audit->recordAdministrative('connection_scopes_updated', $connectionId, null, (int) $connection['MCP001_C004_Id'], $this->auditHost, $this->auditIp, $this->actorUserId);
            $this->pdo->commit();
            return $normalized;
        } catch (\Throwable $exception) { $this->rollback(); throw $exception; }
    }

    public function updateApiKeyResponsibleForCompany(int $connectionId, int $companyId, ?int $responsibleUserId): void
    {
        $this->pdo->beginTransaction();
        try {
            $connection = $this->lockConnectionForCompany($connectionId, $companyId);
            if ($responsibleUserId !== null) { $this->assertResponsibleUser($responsibleUserId, $companyId); }
            $scopes = json_decode((string) ($connection['MCP001_Scopes'] ?? '[]'), true);
            if (!is_array($scopes)) { throw new RuntimeException('Invalid MCP scopes.'); }
            if ($this->requiresApiKeyResponsible($scopes, $connectionId) && $responsibleUserId === null) { throw new RuntimeException('A responsible user is required for these permissions.'); }
            $this->pdo->prepare('UPDATE MCP001 SET MCP001_C007_Id=:responsible,MCP001_Data_Alteracao=UTC_TIMESTAMP() WHERE MCP001_Id=:id AND MCP001_C004_Id=:company')->execute(['responsible' => $responsibleUserId, 'id' => $connectionId, 'company' => $companyId]);
            $this->audit->recordAdministrative('connection_api_key_responsible_updated', $connectionId, null, $companyId, $this->auditHost, $this->auditIp, $this->actorUserId);
            $this->pdo->commit();
        } catch (\Throwable $exception) { $this->rollback(); throw $exception; }
    }

    public function renameForCompany(int $connectionId, int $companyId, string $name): void
    {
        $this->assertName($name);
        $name = trim($name);
        $this->pdo->beginTransaction();
        try {
            $connection = $this->lockConnectionForCompany($connectionId, $companyId);
            $this->pdo->prepare('UPDATE MCP001 SET MCP001_Nome=:name,MCP001_Data_Alteracao=UTC_TIMESTAMP() WHERE MCP001_Id=:id AND MCP001_C004_Id=:company')
                ->execute(['name' => $name, 'id' => $connectionId, 'company' => $companyId]);
            $this->audit->recordAdministrative('connection_renamed', $connectionId, null, (int) $connection['MCP001_C004_Id'], $this->auditHost, $this->auditIp, $this->actorUserId);
            $this->pdo->commit();
        } catch (\Throwable $exception) { $this->rollback(); throw $exception; }
    }

    /** @return array{id:int,key:string} */
    private function createCredential(int $connectionId, ?string $expiresAt): array
    {
        $expiresAt = $this->normalizeFutureExpiration($expiresAt);
        $generated = $this->generator->generate();
        $hash = $this->verifier->hash($generated['secret'], $this->currentPepperVersion);
        $this->pdo->prepare("INSERT INTO MCP002 (MCP002_MCP001_Id,MCP002_Public_Id,MCP002_Secret_Hash,MCP002_Pepper_Version,MCP002_Status,MCP002_Data_Expiracao) VALUES (:connection,:public,:hash,:version,'active',:expires)")
            ->execute(['connection' => $connectionId, 'public' => $generated['public_id'], 'hash' => $hash, 'version' => $this->currentPepperVersion, 'expires' => $expiresAt]);
        return ['id' => (int) $this->pdo->lastInsertId(), 'key' => $generated['key']];
    }

    /** @return array<string,mixed> */
    private function lockConnection(int $connectionId): array
    {
        $statement = $this->pdo->prepare('SELECT MCP001_Id,MCP001_C004_Id,MCP001_C007_Id,MCP001_Scopes,MCP001_Status FROM MCP001 WHERE MCP001_Id=:id FOR UPDATE');
        $statement->execute(['id' => $connectionId]);
        $row = $statement->fetch();
        if (!is_array($row)) { throw new RuntimeException('Connection not found.'); }
        return $row;
    }

    private function lockConnectionForCompany(int $connectionId, int $companyId): array
    {
        $statement = $this->pdo->prepare('SELECT MCP001_Id,MCP001_C004_Id,MCP001_C007_Id,MCP001_Scopes,MCP001_Status FROM MCP001 WHERE MCP001_Id=:id AND MCP001_C004_Id=:company FOR UPDATE');
        $statement->execute(['id' => $connectionId, 'company' => $companyId]);
        $row = $statement->fetch();
        if (!is_array($row)) { throw new RuntimeException('Connection not found.'); }
        return $row;
    }

    /** @return array<string,mixed> */
    private function lockCredential(int $credentialId): array
    {
        $statement = $this->pdo->prepare('SELECT k.MCP002_Id,c.MCP001_Id,c.MCP001_C004_Id FROM MCP002 k INNER JOIN MCP001 c ON c.MCP001_Id=k.MCP002_MCP001_Id WHERE k.MCP002_Id=:id FOR UPDATE');
        $statement->execute(['id' => $credentialId]);
        $row = $statement->fetch();
        if (!is_array($row)) { throw new RuntimeException('Credential not found.'); }
        return $row;
    }

    private function assertCompanyExists(int $companyId): void
    {
        $statement = $this->pdo->prepare('SELECT C004_Id FROM C004 WHERE C004_Id=:id LIMIT 1');
        $statement->execute(['id' => $companyId]);
        if (!$statement->fetch()) { throw new RuntimeException('Company not found.'); }
    }

    private function assertResponsibleUser(?int $responsibleUserId, int $companyId): void
    {
        if ($responsibleUserId === null) { return; }
        $statement = $this->pdo->prepare("SELECT c007.C007_Id FROM C007 c007 INNER JOIN C019 c019 ON c019.C019_C007_Id=c007.C007_Id INNER JOIN C004 c004 ON c004.C004_Id=c019.C019_C004_Id AND c004.C004_Flag_Ativo='S' WHERE c019.C019_C004_Id=:company AND c007.C007_Id=:id AND c007.C007_Ativo='S' AND c019.C019_C021_Id>0 GROUP BY c007.C007_Id HAVING COUNT(*)=1");
        $statement->execute(['id' => $responsibleUserId, 'company' => $companyId]);
        if (!$statement->fetch()) { throw new RuntimeException('Responsible user not found.'); }
    }

    private function hasValidResponsibleUser(int $connectionId, int $companyId): bool
    {
        $statement = $this->pdo->prepare("SELECT 1 FROM MCP001 connection_row INNER JOIN C007 c007 ON c007.C007_Id=connection_row.MCP001_C007_Id AND c007.C007_Ativo='S' INNER JOIN C019 c019 ON c019.C019_C007_Id=c007.C007_Id INNER JOIN C004 c004 ON c004.C004_Id=c019.C019_C004_Id AND c004.C004_Flag_Ativo='S' WHERE connection_row.MCP001_Id=:connection AND connection_row.MCP001_C004_Id=:company AND c019.C019_C004_Id=connection_row.MCP001_C004_Id AND c019.C019_C021_Id>0 GROUP BY connection_row.MCP001_Id HAVING COUNT(*)=1");
        $statement->execute(['connection' => $connectionId, 'company' => $companyId]);
        return (bool) $statement->fetch();
    }

    private function hasActiveApiKeyCredentials(int $connectionId): bool
    {
        $statement = $this->pdo->prepare("SELECT 1 FROM MCP002 WHERE MCP002_MCP001_Id=:connection AND MCP002_Status='active' AND (MCP002_Data_Expiracao IS NULL OR MCP002_Data_Expiracao>UTC_TIMESTAMP()) LIMIT 1");
        $statement->execute(['connection' => $connectionId]);
        return (bool) $statement->fetch();
    }

    /** @param string[] $scopes */
    private function requiresApiKeyResponsible(array $scopes, int $connectionId): bool
    {
        return $this->scopeCatalog->requiresErpActor($scopes) && $this->hasActiveApiKeyCredentials($connectionId);
    }

    private function assertName(string $name): void
    {
        if (trim($name) === '' || strlen($name) > 100) { throw new RuntimeException('Invalid connection name.'); }
    }

    private function normalizeRevocationReason(string $reason): string
    {
        $reason = trim($reason);
        if ($reason === '' || strlen($reason) > 255) { throw new RuntimeException('Invalid revocation reason.'); }
        return $reason;
    }

    /** @param array<string,mixed> $connection */
    private function assertConnectionCanIssueCredentials(array $connection): void
    {
        if (($connection['MCP001_Status'] ?? null) !== 'active') {
            throw new RuntimeException('Connection must be active to issue credentials.');
        }
        $scopes = json_decode((string) ($connection['MCP001_Scopes'] ?? '[]'), true);
        if (!is_array($scopes)) { throw new RuntimeException('Invalid MCP scopes.'); }
        if ($this->scopeCatalog->requiresErpActor($scopes) && !$this->hasValidResponsibleUser((int) $connection['MCP001_Id'], (int) $connection['MCP001_C004_Id'])) {
            throw new RuntimeException('A responsible user is required for API key permissions.');
        }
    }

    private function normalizeFutureExpiration(?string $value): ?string
    {
        if ($value === null) { return null; }
        $timezone = new DateTimeZone('UTC');
        $date = DateTimeImmutable::createFromFormat('!Y-m-d H:i:s', $value, $timezone);
        $errors = DateTimeImmutable::getLastErrors();
        if ($date === false || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) || $date->format('Y-m-d H:i:s') !== $value || $date <= new DateTimeImmutable('now', $timezone)) {
            throw new RuntimeException('Invalid expiration date.');
        }
        return $date->format('Y-m-d H:i:s');
    }

    /** @param string[] $scopes */
    private function assertScopes(array $scopes): void
    {
        foreach ($scopes as $scope) {
            if (!is_string($scope) || preg_match('/\A[a-z][a-z0-9_.:-]{0,99}\z/', $scope) !== 1) { throw new RuntimeException('Invalid scope.'); }
        }
    }

    private function generatePublicId(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(18)), "+/", "-_"), "=");
    }

    private function rollback(): void
    {
        if ($this->pdo->inTransaction()) { $this->pdo->rollBack(); }
    }
}
