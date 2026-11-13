<?php

declare(strict_types=1);

namespace HardnessMcp\Audit;

use HardnessMcp\Context\ExecutionContext;
use PDO;
use Throwable;

final class McpAuditService
{
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }
    public function record(string $requestId, string $host, string $ip, string $event, int $httpStatus, int $durationMs, ?ExecutionContext $context = null, ?string $safeCode = null): void
    {
        try {
            if ($context !== null && $context->getAuthMethod() === 'oauth') {
                $sql = 'INSERT INTO MCP003 (MCP003_Request_Id,MCP003_MCP001_Id,MCP003_Auth_Method,MCP003_MCP004_Id,MCP003_MCP005_Id,MCP003_MCP007_Id,MCP003_C004_Id,MCP003_C007_Id,MCP003_Host,MCP003_IP,MCP003_Evento,MCP003_Status_HTTP,MCP003_Codigo_Seguro,MCP003_Duracao_MS) VALUES (:request_id,:connection_id,\'oauth\',:client_id,:grant_id,:token_id,:company_id,:user_id,:host,:ip,:event,:status,:code,:duration)';
                $this->pdo->prepare($sql)->execute(['request_id' => $requestId, 'connection_id' => $context->getConnectionId(), 'client_id' => $context->getOAuthClientId(), 'grant_id' => $context->getOAuthGrantId(), 'token_id' => $context->getOAuthAccessTokenId(), 'company_id' => $context->getCompanyId(), 'user_id' => $context->getResponsibleUserId(), 'host' => substr($host, 0, 255), 'ip' => substr($ip, 0, 45), 'event' => substr($event, 0, 60), 'status' => $httpStatus, 'code' => $safeCode, 'duration' => $durationMs]);
                return;
            }
            $sql = 'INSERT INTO MCP003 (MCP003_Request_Id, MCP003_MCP001_Id, MCP003_MCP002_Id, MCP003_Auth_Method, MCP003_C004_Id, MCP003_C007_Id, MCP003_Host, MCP003_IP, MCP003_Evento, MCP003_Status_HTTP, MCP003_Codigo_Seguro, MCP003_Duracao_MS) VALUES (:request_id,:connection_id,:credential_id,\'api_key\',:company_id,:user_id,:host,:ip,:event,:status,:code,:duration)';
            $this->pdo->prepare($sql)->execute(['request_id' => $requestId, 'connection_id' => $context ? $context->getConnectionId() : null, 'credential_id' => $context ? $context->getCredentialId() : null, 'company_id' => $context ? $context->getCompanyId() : null, 'user_id' => $context ? $context->getResponsibleUserId() : null, 'host' => substr($host, 0, 255), 'ip' => substr($ip, 0, 45), 'event' => substr($event, 0, 60), 'status' => $httpStatus, 'code' => $safeCode, 'duration' => $durationMs]);
        } catch (Throwable $exception) { error_log('Hardness MCP audit write failed.'); }
    }

    public function recordAdministrative(string $event, ?int $connectionId, ?int $credentialId, int $companyId, string $host, string $ip, ?int $actorUserId = null): void
    {
        // Administrative callers execute this inside their transaction: a critical mutation
        // must not be committed if its MCP003 audit row cannot be persisted.
        $sql = 'INSERT INTO MCP003 (MCP003_Request_Id, MCP003_MCP001_Id, MCP003_MCP002_Id, MCP003_C004_Id, MCP003_C007_Id, MCP003_Host, MCP003_IP, MCP003_Evento, MCP003_Status_HTTP, MCP003_Duracao_MS) VALUES (:request_id,:connection_id,:credential_id,:company_id,:actor_user_id,:host,:ip,:event,200,0)';
        $this->pdo->prepare($sql)->execute(['request_id' => bin2hex(random_bytes(16)), 'connection_id' => $connectionId, 'credential_id' => $credentialId, 'company_id' => $companyId, 'actor_user_id' => $actorUserId, 'host' => substr($host, 0, 255), 'ip' => substr($ip, 0, 45), 'event' => substr($event, 0, 60)]);
    }

    public function recordOAuthAdministrative(string $event, ?int $connectionId, ?int $companyId, ?int $clientId, ?int $grantId, ?int $tokenId, string $host, string $ip, ?int $actorUserId = null): void
    {
        $sql = 'INSERT INTO MCP003 (MCP003_Request_Id,MCP003_MCP001_Id,MCP003_Auth_Method,MCP003_MCP004_Id,MCP003_MCP005_Id,MCP003_MCP007_Id,MCP003_C004_Id,MCP003_C007_Id,MCP003_Host,MCP003_IP,MCP003_Evento,MCP003_Status_HTTP,MCP003_Duracao_MS) VALUES (:request_id,:connection_id,\'oauth\',:client_id,:grant_id,:token_id,:company_id,:actor_user_id,:host,:ip,:event,200,0)';
        $this->pdo->prepare($sql)->execute(['request_id' => bin2hex(random_bytes(16)), 'connection_id' => $connectionId, 'client_id' => $clientId, 'grant_id' => $grantId, 'token_id' => $tokenId, 'company_id' => $companyId, 'actor_user_id' => $actorUserId, 'host' => substr($host, 0, 255), 'ip' => substr($ip, 0, 45), 'event' => substr($event, 0, 60)]);
    }
}
