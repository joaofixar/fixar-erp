<?php

declare(strict_types=1);

namespace RestApi\Auth;

use PDOException;
use RestApi\Core\ApiException;
use RestApi\Core\Request;

class TokenService
{
    private string $appKey;

    /**
     * @param array<string, mixed> $appConfig
     */
    public function __construct(array $appConfig)
    {
        $this->appKey = (string) ($appConfig['app_key'] ?? '');
    }

    public function extractToken(Request $request): string
    {
        $authorization = $request->getHeader('authorization');
        if (is_string($authorization) && trim($authorization) !== '') {
            if (preg_match('/^Bearer\s+(.+)$/i', $authorization, $matches)) {
                $token = trim($matches[1]);
                if ($token !== '') {
                    return $token;
                }
            }
        }

        $headerToken = $request->getHeader('x-api-token');
        if (is_string($headerToken)) {
            $headerToken = trim($headerToken);
            if ($headerToken !== '') {
                return $headerToken;
            }
        }

        throw new ApiException('TOKEN_MISSING', 'Token não informado.', 401);
    }

    public function extractBearerToken(Request $request): string
    {
        return $this->extractToken($request);
    }

    public function createTokenHash(string $token): string
    {
        $this->assertAppKeyConfigured();
        return hash_hmac('sha256', $token, $this->appKey);
    }

    /**
     * @param object $connection
     * @return array<string, int|string>
     */
    public function authenticateWithToken(string $token, Request $request, $connection): array
    {
        $tokenHash = $this->createTokenHash($token);

        try {
            $statement = $connection->prepare(
                'SELECT A001_Id, A001_Nome, A001_Token_Prefixo, A001_Status
                   FROM A001
                  WHERE A001_Token_Hash = :token_hash
                  LIMIT 1'
            );
            $statement->execute(['token_hash' => $tokenHash]);
            $tokenRow = $statement->fetch();
        } catch (PDOException $exception) {
            $this->throwDatabaseException($exception);
        }

        if (!is_array($tokenRow) || $tokenRow === []) {
            throw new ApiException('TOKEN_INVALID', 'Token inválido.', 401);
        }

        if (($tokenRow['A001_Status'] ?? '') !== 'active') {
            throw new ApiException('TOKEN_INACTIVE', 'Token inativo ou revogado.', 403);
        }

        try {
            $updateStatement = $connection->prepare(
                'UPDATE A001
                    SET A001_Data_Ultimo_Uso = NOW(),
                        A001_IP_Ultimo_Uso = :ip
                  WHERE A001_Id = :id'
            );
            $updateStatement->execute([
                'ip' => $request->getIp(),
                'id' => $tokenRow['A001_Id'],
            ]);
        } catch (PDOException $exception) {
            $this->throwDatabaseException($exception);
        }

        return [
            'A001_Id' => (int) $tokenRow['A001_Id'],
            'A001_Nome' => (string) $tokenRow['A001_Nome'],
            'A001_Token_Prefixo' => (string) $tokenRow['A001_Token_Prefixo'],
            'A001_Status' => (string) $tokenRow['A001_Status'],
        ];
    }

    private function throwDatabaseException(PDOException $exception): void
    {
        if ($exception->getCode() === '42S02') {
            throw new ApiException('AUTH_TABLE_MISSING', 'Tabela A001 não encontrada.', 500);
        }

        throw new ApiException('AUTH_DB_ERROR', 'Erro ao validar token.', 500);
    }

    private function assertAppKeyConfigured(): void
    {
        if ($this->appKey === '') {
            throw new ApiException('AUTH_CONFIG_ERROR', 'Chave de aplicação inválida.', 500);
        }
    }
}
