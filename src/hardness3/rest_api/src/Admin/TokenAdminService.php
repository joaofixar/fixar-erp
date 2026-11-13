<?php

declare(strict_types=1);

namespace RestApi\Admin;

use PDO;
use PDOException;
use RestApi\Database\ConnectionFactory;

class TokenAdminService
{
    private ConnectionFactory $connectionFactory;

    /** @var array<string, mixed> */
    private array $appConfig;

    /** @var array<string, mixed> */
    private array $tenantContext = [];

    /**
     * @param array<string, mixed> $appConfig
     */
    public function __construct(ConnectionFactory $connectionFactory, array $appConfig)
    {
        $this->connectionFactory = $connectionFactory;
        $this->appConfig = $appConfig;
    }

    /**
     * @param array<string, mixed> $tenantContext
     */
    public function forTenant(array $tenantContext): self
    {
        $clone = clone $this;
        $clone->tenantContext = $tenantContext;

        return $clone;
    }

    /**
     * @return array<string, mixed>
     */
    public function getTokenStatus(): array
    {
        $tokens = $this->listTokenRows();
        $activeCount = 0;
        foreach ($tokens as $token) {
            if (($token['status'] ?? '') === 'active') {
                $activeCount++;
            }
        }

        return [
            'total_tokens' => count($tokens),
            'active_tokens' => $activeCount,
            'tokens' => $tokens,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function createToken(string $name, string $observation = ''): array
    {
        $normalizedName = trim($name);
        if ($normalizedName === '') {
            throw new \RuntimeException('Informe um nome para o token.');
        }

        $tokenData = $this->generateTokenData();
        $connection = $this->getConnection();

        try {
            $connection->beginTransaction();

            $deactivateStatement = $connection->prepare(
                'UPDATE A001
                    SET A001_Status = :inactive_status,
                        A001_Data_Revogacao = NOW()
                  WHERE A001_Status = :active_status'
            );
            $deactivateStatement->execute([
                'inactive_status' => 'inactive',
                'active_status' => 'active',
            ]);

            $statement = $connection->prepare(
                'INSERT INTO A001 (
                    A001_Nome,
                    A001_Token_Hash,
                    A001_Token_Prefixo,
                    A001_Status,
                    A001_Data_Criacao,
                    A001_Data_Revogacao,
                    A001_Data_Ultimo_Uso,
                    A001_IP_Ultimo_Uso,
                    A001_Observacao
                ) VALUES (
                    :nome,
                    :token_hash,
                    :token_prefixo,
                    :status,
                    NOW(),
                    NULL,
                    NULL,
                    NULL,
                    :observacao
                )'
            );
            $statement->execute([
                'nome' => $normalizedName,
                'token_hash' => $tokenData['hash'],
                'token_prefixo' => $tokenData['prefix'],
                'status' => 'active',
                'observacao' => trim($observation),
            ]);
            $tokenId = (int) $connection->lastInsertId();
            $connection->commit();
        } catch (PDOException $exception) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }
            $this->throwDatabaseException($exception);
        } catch (\Throwable $throwable) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }
            throw $throwable;
        }

        return [
            'token_id' => $tokenId,
            'token' => $tokenData['token'],
            'prefix' => $tokenData['prefix'],
            'hash' => $tokenData['hash'],
            'status' => 'active',
            'name' => $normalizedName,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function rotateToken(int $tokenId): array
    {
        if ($tokenId <= 0) {
            throw new \RuntimeException('Token inválido para rotação.');
        }

        $existing = $this->fetchTokenRow($tokenId);
        $tokenData = $this->generateTokenData();
        $connection = $this->getConnection();

        try {
            $statement = $connection->prepare(
                'UPDATE A001
                    SET A001_Token_Hash = :token_hash,
                        A001_Token_Prefixo = :token_prefixo,
                        A001_Status = :status,
                        A001_Data_Revogacao = NULL
                  WHERE A001_Id = :id
                  LIMIT 1'
            );
            $statement->execute([
                'token_hash' => $tokenData['hash'],
                'token_prefixo' => $tokenData['prefix'],
                'status' => 'active',
                'id' => $tokenId,
            ]);
        } catch (PDOException $exception) {
            $this->throwDatabaseException($exception);
        }

        return [
            'token_id' => $tokenId,
            'token' => $tokenData['token'],
            'prefix' => $tokenData['prefix'],
            'hash' => $tokenData['hash'],
            'status' => 'active',
            'name' => $existing['name'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function updateTokenStatus(int $tokenId, string $status): array
    {
        if ($tokenId <= 0) {
            throw new \RuntimeException('Token inválido.');
        }

        $normalizedStatus = strtolower(trim($status));
        if (!in_array($normalizedStatus, ['active', 'inactive'], true)) {
            throw new \RuntimeException('Status de token inválido.');
        }

        $this->fetchTokenRow($tokenId);
        $connection = $this->getConnection();

        try {
            $statement = $connection->prepare(
                'UPDATE A001
                    SET A001_Status = :status,
                        A001_Data_Revogacao = :data_revogacao
                  WHERE A001_Id = :id
                  LIMIT 1'
            );
            $statement->execute([
                'status' => $normalizedStatus,
                'data_revogacao' => $normalizedStatus === 'inactive' ? date('Y-m-d H:i:s') : null,
                'id' => $tokenId,
            ]);
        } catch (PDOException $exception) {
            $this->throwDatabaseException($exception);
        }

        return [
            'token_id' => $tokenId,
            'status' => $normalizedStatus,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function listTokenRows(): array
    {
        $connection = $this->getConnection();

        try {
            $statement = $connection->prepare(
                'SELECT
                    A001_Id,
                    A001_Nome,
                    A001_Token_Prefixo,
                    A001_Status,
                    A001_Data_Criacao,
                    A001_Data_Revogacao,
                    A001_Data_Ultimo_Uso,
                    A001_IP_Ultimo_Uso,
                    A001_Observacao
                 FROM A001
                 ORDER BY A001_Id DESC'
            );
            $statement->execute();
            $rows = $statement->fetchAll();
        } catch (PDOException $exception) {
            $this->throwDatabaseException($exception);
        }

        $tokens = [];
        foreach ((array) $rows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $tokens[] = [
                'id' => (int) ($row['A001_Id'] ?? 0),
                'name' => (string) ($row['A001_Nome'] ?? ''),
                'prefix' => (string) ($row['A001_Token_Prefixo'] ?? ''),
                'status' => (string) ($row['A001_Status'] ?? ''),
                'created_at' => $row['A001_Data_Criacao'] ?? null,
                'revoked_at' => $row['A001_Data_Revogacao'] ?? null,
                'last_used_at' => $row['A001_Data_Ultimo_Uso'] ?? null,
                'last_used_ip' => $row['A001_IP_Ultimo_Uso'] ?? null,
                'observation' => (string) ($row['A001_Observacao'] ?? ''),
            ];
        }

        return $tokens;
    }

    /**
     * @return array<string, mixed>
     */
    private function fetchTokenRow(int $tokenId): array
    {
        $connection = $this->getConnection();

        try {
            $statement = $connection->prepare(
                'SELECT
                    A001_Id,
                    A001_Nome,
                    A001_Token_Prefixo,
                    A001_Status,
                    A001_Observacao
                 FROM A001
                 WHERE A001_Id = :id
                 LIMIT 1'
            );
            $statement->execute(['id' => $tokenId]);
            $row = $statement->fetch();
        } catch (PDOException $exception) {
            $this->throwDatabaseException($exception);
        }

        if (!is_array($row) || $row === []) {
            throw new \RuntimeException('Token não encontrado.');
        }

        return [
            'id' => (int) ($row['A001_Id'] ?? 0),
            'name' => (string) ($row['A001_Nome'] ?? ''),
            'prefix' => (string) ($row['A001_Token_Prefixo'] ?? ''),
            'status' => (string) ($row['A001_Status'] ?? ''),
            'observation' => (string) ($row['A001_Observacao'] ?? ''),
        ];
    }

    /**
     * @return array{token:string,prefix:string,hash:string}
     */
    private function generateTokenData(): array
    {
        $appKey = (string) ($this->appConfig['app_key'] ?? '');
        if ($appKey === '') {
            throw new \RuntimeException('APP key não configurada para gerar token.');
        }

        $environment = getenv('REST_API_TOKEN_ENV') ?: 'live';
        $prefixBase = $environment === 'test' ? 'hrd_test_' : 'hrd_live_';
        $token = $prefixBase . rtrim(strtr(base64_encode(random_bytes(24)), '+/', '-_'), '=');
        $hash = hash_hmac('sha256', $token, $appKey);
        $prefixLength = (int) (($this->appConfig['auth']['token_prefix_length'] ?? 12));
        $tokenPrefix = substr($token, 0, $prefixLength);

        return [
            'token' => $token,
            'prefix' => $tokenPrefix,
            'hash' => $hash,
        ];
    }

    private function getConnection(): PDO
    {
        static $connections = [];

        $context = $this->tenantContext;
        $key = md5(json_encode([
            $context['dbHost'] ?? '',
            $context['dbDatabase'] ?? '',
            $context['dbUser'] ?? '',
        ]));

        if (!isset($connections[$key])) {
            $connections[$key] = $this->connectionFactory->create($context);
        }

        return $connections[$key];
    }

    private function throwDatabaseException(PDOException $exception): void
    {
        if ($exception->getCode() === '42S02') {
            throw new \RuntimeException('Tabela A001 não encontrada.');
        }

        throw new \RuntimeException('Erro ao manipular tokens da REST API.');
    }
}
