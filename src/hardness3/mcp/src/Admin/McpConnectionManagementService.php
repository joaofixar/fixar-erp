<?php

declare(strict_types=1);

namespace HardnessMcp\Admin;

use HardnessMcp\Audit\McpAuditService;
use PDO;
use RuntimeException;
use Throwable;

/** Connection-only administration. Credentials are deliberately managed separately. */
final class McpConnectionManagementService
{
    private PDO $pdo;
    private McpAuditService $audit;
    private string $host;
    private string $ip;
    private ?int $actorUserId;

    public function __construct(PDO $pdo, string $host, string $ip, ?int $actorUserId = null)
    {
        $this->pdo = $pdo;
        $this->audit = new McpAuditService($pdo);
        $this->host = $host;
        $this->ip = $ip;
        $this->actorUserId = $actorUserId !== null && $actorUserId > 0 ? $actorUserId : null;
    }

    /** @return array{id:int,name:string,status:string} */
    public function create(int $companyId, string $name): array
    {
        $name = $this->normalizedName($name);
        $this->assertCompanyExists($companyId);
        $publicId = $this->generatePublicId();
        $this->pdo->beginTransaction();
        try {
            $this->pdo->prepare("INSERT INTO MCP001 (MCP001_Nome,MCP001_C004_Id,MCP001_Public_Id,MCP001_Scopes,MCP001_Status) VALUES (:name,:company,:public_id,'[]','disabled')")
                ->execute(['name' => $name, 'company' => $companyId, 'public_id' => $publicId]);
            $id = (int) $this->pdo->lastInsertId();
            $this->audit->recordAdministrative('connection_created', $id, null, $companyId, $this->host, $this->ip, $this->actorUserId);
            $this->pdo->commit();
            return ['id' => $id, 'public_id' => $publicId, 'name' => $name, 'status' => 'disabled'];
        } catch (Throwable $exception) {
            $this->rollback();
            throw $exception;
        }
    }

    public function setStatus(int $companyId, int $connectionId, string $status): void
    {
        if (!in_array($status, ['active', 'disabled'], true)) { throw new RuntimeException('Invalid connection status.'); }
        $this->pdo->beginTransaction();
        try {
            $statement = $this->pdo->prepare('SELECT MCP001_Id FROM MCP001 WHERE MCP001_Id=:id AND MCP001_C004_Id=:company FOR UPDATE');
            $statement->execute(['id' => $connectionId, 'company' => $companyId]);
            if (!is_array($statement->fetch())) { throw new RuntimeException('Connection not found.'); }
            $this->pdo->prepare('UPDATE MCP001 SET MCP001_Status=:status,MCP001_Data_Alteracao=CURRENT_TIMESTAMP WHERE MCP001_Id=:id AND MCP001_C004_Id=:company')
                ->execute(['status' => $status, 'id' => $connectionId, 'company' => $companyId]);
            $this->audit->recordAdministrative($status === 'active' ? 'connection_enabled' : 'connection_disabled', $connectionId, null, $companyId, $this->host, $this->ip, $this->actorUserId);
            $this->pdo->commit();
        } catch (Throwable $exception) {
            $this->rollback();
            throw $exception;
        }
    }

    /** Generates a stable exclusive URL identifier without changing credentials or OAuth artifacts. */
    public function ensurePublicId(int $companyId, int $connectionId): string
    {
        $this->pdo->beginTransaction();
        try {
            $statement = $this->pdo->prepare('SELECT MCP001_Id,MCP001_Public_Id FROM MCP001 WHERE MCP001_Id=:id AND MCP001_C004_Id=:company FOR UPDATE');
            $statement->execute(['id' => $connectionId, 'company' => $companyId]);
            $connection = $statement->fetch();
            if (!is_array($connection)) { throw new RuntimeException('Connection not found.'); }
            $existing = $connection['MCP001_Public_Id'] ?? null;
            if (is_string($existing) && preg_match('/\A[A-Za-z0-9_-]{24}\z/', $existing) === 1) {
                $this->pdo->commit();
                return $existing;
            }
            $publicId = $this->generatePublicId();
            $update = $this->pdo->prepare('UPDATE MCP001 SET MCP001_Public_Id=:public_id,MCP001_Data_Alteracao=UTC_TIMESTAMP() WHERE MCP001_Id=:id AND MCP001_C004_Id=:company AND MCP001_Public_Id IS NULL');
            $update->execute(['public_id' => $publicId, 'id' => $connectionId, 'company' => $companyId]);
            if ($update->rowCount() !== 1) { throw new RuntimeException('Connection public URL could not be created.'); }
            $this->audit->recordAdministrative('connection_public_id_created', $connectionId, null, $companyId, $this->host, $this->ip, $this->actorUserId);
            $this->pdo->commit();
            return $publicId;
        } catch (Throwable $exception) {
            $this->rollback();
            throw $exception;
        }
    }

    /**
     * Removes a connection that was configured but never used. Administrative
     * audit rows are retained at company level, with the removed connection
     * reference cleared, so they cannot be mistaken for a live connection.
     */
    public function deleteUnused(int $companyId, int $connectionId): void
    {
        $this->pdo->beginTransaction();
        try {
            $statement = $this->pdo->prepare('SELECT MCP001_Id FROM MCP001 WHERE MCP001_Id=:id AND MCP001_C004_Id=:company FOR UPDATE');
            $statement->execute(['id' => $connectionId, 'company' => $companyId]);
            if (!is_array($statement->fetch())) { throw new RuntimeException('Connection not found.'); }

            foreach (['MCP002' => 'MCP002_MCP001_Id', 'MCP005' => 'MCP005_MCP001_Id', 'MCP018' => 'MCP018_MCP001_Id'] as $table => $column) {
                $used = $this->pdo->prepare("SELECT 1 FROM {$table} WHERE {$column}=:connection LIMIT 1");
                $used->execute(['connection' => $connectionId]);
                if ($used->fetch()) { throw new RuntimeException('Only a connection that has never been used can be deleted.'); }
            }
            $usage = $this->pdo->prepare('SELECT 1 FROM MCP003 WHERE MCP003_MCP001_Id=:connection AND (MCP003_Auth_Method IS NOT NULL OR MCP003_MCP002_Id IS NOT NULL OR MCP003_MCP004_Id IS NOT NULL OR MCP003_MCP005_Id IS NOT NULL OR MCP003_MCP007_Id IS NOT NULL) LIMIT 1');
            $usage->execute(['connection' => $connectionId]);
            if ($usage->fetch()) { throw new RuntimeException('Only a connection that has never been used can be deleted.'); }

            $this->audit->recordAdministrative('connection_deleted_unused', $connectionId, null, $companyId, $this->host, $this->ip, $this->actorUserId);
            foreach (['MCP015', 'MCP016', 'MCP017'] as $table) {
                $this->pdo->prepare("DELETE FROM {$table} WHERE {$table}_MCP001_Id=:connection")->execute(['connection' => $connectionId]);
            }
            $this->pdo->prepare('UPDATE MCP003 SET MCP003_MCP001_Id=NULL WHERE MCP003_MCP001_Id=:connection')->execute(['connection' => $connectionId]);
            $this->pdo->prepare('DELETE FROM MCP001 WHERE MCP001_Id=:id AND MCP001_C004_Id=:company')->execute(['id' => $connectionId, 'company' => $companyId]);
            $this->pdo->commit();
        } catch (Throwable $exception) {
            $this->rollback();
            throw $exception;
        }
    }

    private function assertCompanyExists(int $companyId): void
    {
        if ($companyId < 1) { throw new RuntimeException('Company not found.'); }
        $statement = $this->pdo->prepare('SELECT C004_Id FROM C004 WHERE C004_Id=:id LIMIT 1');
        $statement->execute(['id' => $companyId]);
        if (!$statement->fetch()) { throw new RuntimeException('Company not found.'); }
    }

    private function normalizedName(string $name): string
    {
        $name = trim($name);
        if ($name === '' || strlen($name) > 100) { throw new RuntimeException('Invalid connection name.'); }
        return $name;
    }

    private function generatePublicId(): string { return rtrim(strtr(base64_encode(random_bytes(18)), "+/", "-_"), "="); }

    private function rollback(): void { if ($this->pdo->inTransaction()) { $this->pdo->rollBack(); } }
}
