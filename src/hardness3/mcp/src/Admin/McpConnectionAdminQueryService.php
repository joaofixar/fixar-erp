<?php

declare(strict_types=1);

namespace HardnessMcp\Admin;

use HardnessMcp\AdminUi\Context\SelectedCompanyContext;
use HardnessMcp\Schema\IdeSmtOrderGenerationCapability;
use PDO;
use RuntimeException;

/** Read-only administrative queries. It never returns credentials, hashes, peppers or secrets. */
final class McpConnectionAdminQueryService
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** @return array<int,array{id:int,legal_name:string,short_name:string,document:string,status:string,connection_count:int,active_connection_count:int,last_mcp_usage:?string}> */
    public function listAvailableCompanies(): array
    {
        $rows = $this->pdo->query($this->companySummarySql("WHERE c.C004_Flag_Ativo = 'S'"))->fetchAll();
        $companies = [];
        foreach ($rows as $row) {
            $companies[] = $this->companyDto($row);
        }
        return $companies;
    }

    public function isIdeSmtOrderGenerationEnabled(SelectedCompanyContext $company): bool
    {
        return IdeSmtOrderGenerationCapability::isEnabled($this->pdo, $company->getCompanyId());
    }

    /** @return array{id:int,legal_name:string,short_name:string,document:string,status:string,connection_count:int,active_connection_count:int,last_mcp_usage:?string} */
    public function getCompanySummary(SelectedCompanyContext $company): array
    {
        // Do not reuse companySummarySql() here.  That query is appropriate for the
        // installation overview, but its derived tables aggregate every MCP
        // connection and credential before the selected company can be displayed.
        // This endpoint is reached on every company click, so keep all joins scoped
        // to the one company from the outset.
        $sql = "SELECT c.C004_Id, c.C004_Nome_Empresa, c.C004_Nome_Abreviado, c.C004_Cnpj, c.C004_Flag_Ativo,\n"
            . "       COUNT(DISTINCT connection_rows.MCP001_Id) AS connection_count,\n"
            . "       COUNT(DISTINCT CASE WHEN connection_rows.MCP001_Status = 'active' THEN connection_rows.MCP001_Id END) AS active_connection_count,\n"
            . "       MAX(credential_usage.MCP002_Data_Ultimo_Uso) AS last_mcp_usage\n"
            . "FROM C004 c\n"
            . "LEFT JOIN MCP001 connection_rows ON connection_rows.MCP001_C004_Id = c.C004_Id\n"
            . "LEFT JOIN MCP002 credential_usage ON credential_usage.MCP002_MCP001_Id = connection_rows.MCP001_Id\n"
            . "WHERE c.C004_Id=:company_id AND c.C004_Flag_Ativo = 'S'\n"
            . "GROUP BY c.C004_Id, c.C004_Nome_Empresa, c.C004_Nome_Abreviado, c.C004_Cnpj, c.C004_Flag_Ativo";
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['company_id' => $company->getCompanyId()]);
        $row = $statement->fetch();
        if (!is_array($row)) {
            throw new RuntimeException('Selected company was not found.');
        }
        return $this->companyDto($row);
    }

    /** @return array<int,array{id:int,name:string,status:string,scopes:array<int,string>,created_at:string,updated_at:?string,last_usage_at:?string,credential_count:int,active_credential_count:int}> */
    public function listConnectionsForCompany(SelectedCompanyContext $company): array
    {
        $sql = "SELECT c.MCP001_Id, c.MCP001_Nome, c.MCP001_Status, c.MCP001_Public_Id, c.MCP001_Scopes, c.MCP001_C007_Id, u.C007_Nome AS responsible_user_name, u.C007_Login AS responsible_user_login, c.MCP001_Data_Criacao, c.MCP001_Data_Alteracao,\n"
            . "       COUNT(k.MCP002_Id) AS credential_count,\n"
            . "       COALESCE(SUM(k.MCP002_Status = 'active' AND (k.MCP002_Data_Expiracao IS NULL OR k.MCP002_Data_Expiracao > UTC_TIMESTAMP())), 0) AS active_credential_count,\n"
            . "       MAX(k.MCP002_Data_Ultimo_Uso) AS last_usage_at\n"
            . "FROM MCP001 c\n"
            . "LEFT JOIN MCP002 k ON k.MCP002_MCP001_Id = c.MCP001_Id\nLEFT JOIN C007 u ON u.C007_Id=c.MCP001_C007_Id\n"
            . "WHERE c.MCP001_C004_Id=:company_id\n"
            . "GROUP BY c.MCP001_Id, c.MCP001_Nome, c.MCP001_Status, c.MCP001_Public_Id, c.MCP001_Scopes, c.MCP001_C007_Id, u.C007_Nome, u.C007_Login, c.MCP001_Data_Criacao, c.MCP001_Data_Alteracao\n"
            . "ORDER BY c.MCP001_Nome, c.MCP001_Id";
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['company_id' => $company->getCompanyId()]);
        $connections = [];
        foreach ($statement->fetchAll() as $row) {
            $connections[] = $this->connectionDto($row);
        }
        return $connections;
    }

    /** @return array<int,array{id:int,public_id:string,status:string,created_at:string,expires_at:?string,last_usage_at:?string,revoked_at:?string,rotation_pending:bool,grace_expires_at:?string}> */
    public function listCredentialsForConnection(SelectedCompanyContext $company, int $connectionId): array
    {
        $credentials = $this->listCredentialsForConnections($company, [$connectionId]);
        return $credentials[$connectionId] ?? [];
    }

    /**
     * Loads credentials for a company's connections in one query.  The company
     * detail screen used to call listCredentialsForConnection() once per card;
     * with many connections that became an N+1 query pattern.
     *
     * @param array<int,int> $connectionIds
     * @return array<int,array<int,array{id:int,public_id:string,status:string,created_at:string,expires_at:?string,last_usage_at:?string,revoked_at:?string,rotation_pending:bool,grace_expires_at:?string}>>
     */
    public function listCredentialsForConnections(SelectedCompanyContext $company, array $connectionIds): array
    {
        $connectionIds = array_values(array_unique(array_filter($connectionIds, static function ($id): bool { return is_int($id) && $id > 0; })));
        $itemsByConnection = [];
        foreach ($connectionIds as $connectionId) {
            $itemsByConnection[$connectionId] = [];
        }
        if ($connectionIds === []) {
            return $itemsByConnection;
        }

        $placeholders = [];
        $parameters = ['company' => $company->getCompanyId()];
        foreach ($connectionIds as $position => $connectionId) {
            $placeholder = 'connection_' . $position;
            $placeholders[] = ':' . $placeholder;
            $parameters[$placeholder] = $connectionId;
        }
        $sql = 'SELECT k.MCP002_MCP001_Id,k.MCP002_Id,k.MCP002_Public_Id,k.MCP002_Status,k.MCP002_Data_Criacao,k.MCP002_Data_Expiracao,k.MCP002_Data_Ultimo_Uso,k.MCP002_Data_Revogacao,k.MCP002_Substituida_Por_MCP002_Id '
            . 'FROM MCP002 k INNER JOIN MCP001 c ON c.MCP001_Id=k.MCP002_MCP001_Id WHERE c.MCP001_C004_Id=:company AND c.MCP001_Id IN (' . implode(',', $placeholders) . ') ORDER BY k.MCP002_MCP001_Id,k.MCP002_Id DESC';
        $statement = $this->pdo->prepare($sql);
        $statement->execute($parameters);
        foreach ($statement->fetchAll() as $row) {
            $connectionId = (int) $row['MCP002_MCP001_Id'];
            $publicId = (string) $row['MCP002_Public_Id'];
            $rotationPending = $row['MCP002_Substituida_Por_MCP002_Id'] !== null;
            $expiresAt = $row['MCP002_Data_Expiracao'] === null ? null : (string) $row['MCP002_Data_Expiracao'];
            $itemsByConnection[$connectionId][] = ['id' => (int) $row['MCP002_Id'], 'public_id' => substr($publicId, 0, 4) . '••••' . substr($publicId, -4), 'status' => (string) $row['MCP002_Status'], 'created_at' => (string) $row['MCP002_Data_Criacao'], 'expires_at' => $expiresAt, 'last_usage_at' => $row['MCP002_Data_Ultimo_Uso'] === null ? null : (string) $row['MCP002_Data_Ultimo_Uso'], 'revoked_at' => $row['MCP002_Data_Revogacao'] === null ? null : (string) $row['MCP002_Data_Revogacao'], 'rotation_pending' => $rotationPending, 'grace_expires_at' => $rotationPending ? $expiresAt : null];
        }
        return $itemsByConnection;
    }

    /** @return array{id:int,name:string,status:string,scopes:array<int,string>,created_at:string,updated_at:?string,last_usage_at:?string,credential_count:int,active_credential_count:int,rotation_pending:bool} */
    public function getConnectionForCompany(SelectedCompanyContext $company, int $connectionId): array
    {
        foreach ($this->listConnectionsForCompany($company) as $connection) {
            if ($connection['id'] === $connectionId) {
                $connection['rotation_pending'] = false;
                foreach ($this->listCredentialsForConnection($company, $connectionId) as $credential) { if ($credential['rotation_pending']) { $connection['rotation_pending'] = true; break; } }
                return $connection;
            }
        }
        throw new RuntimeException('Connection not found.');
    }

    /** @return array<int,array{created_at:string,event:string,http_status:int,request_id:string,duration_ms:int,actor_user_id:?int,actor_name:?string,actor_login:?string,client_name:?string,safe_code:?string,credential_public_id:?string}> */
    public function listAuditForConnection(SelectedCompanyContext $company, int $connectionId, int $limit = 50, int $offset = 0): array
    {
        $this->getConnectionForCompany($company, $connectionId);
        $limit = max(1, min(100, $limit));
        $offset = max(0, $offset);
        $sql = 'SELECT a.MCP003_Data_Criacao,a.MCP003_Evento,a.MCP003_Status_HTTP,a.MCP003_Request_Id,a.MCP003_Duracao_MS,a.MCP003_C007_Id,a.MCP003_Codigo_Seguro,k.MCP002_Public_Id,u.C007_Nome AS actor_name,u.C007_Login AS actor_login,c.MCP004_Nome AS client_name FROM MCP003 a LEFT JOIN MCP002 k ON k.MCP002_Id=a.MCP003_MCP002_Id LEFT JOIN C007 u ON u.C007_Id=a.MCP003_C007_Id LEFT JOIN MCP004 c ON c.MCP004_Id=a.MCP003_MCP004_Id WHERE a.MCP003_C004_Id=:company AND a.MCP003_MCP001_Id=:connection ORDER BY a.MCP003_Id DESC LIMIT ' . $limit . ' OFFSET ' . $offset;
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['company' => $company->getCompanyId(), 'connection' => $connectionId]);
        $items = [];
        foreach ($statement->fetchAll() as $row) {
            $publicId = $row['MCP002_Public_Id'] ?? null;
            $items[] = ['created_at' => (string) $row['MCP003_Data_Criacao'], 'event' => (string) $row['MCP003_Evento'], 'http_status' => (int) $row['MCP003_Status_HTTP'], 'request_id' => (string) $row['MCP003_Request_Id'], 'duration_ms' => (int) $row['MCP003_Duracao_MS'], 'actor_user_id' => $row['MCP003_C007_Id'] === null ? null : (int) $row['MCP003_C007_Id'], 'actor_name' => isset($row['actor_name']) ? (string) $row['actor_name'] : null, 'actor_login' => isset($row['actor_login']) ? (string) $row['actor_login'] : null, 'client_name' => isset($row['client_name']) ? (string) $row['client_name'] : null, 'safe_code' => isset($row['MCP003_Codigo_Seguro']) ? (string) $row['MCP003_Codigo_Seguro'] : null, 'credential_public_id' => is_string($publicId) ? substr($publicId, 0, 4) . '••••' . substr($publicId, -4) : null];
        }
        return $items;
    }

    /** @return array<int,array<string,mixed>> */
    public function listOAuthClients(): array
    {
        return $this->pdo->query('SELECT c.MCP004_Id AS id,c.MCP004_Client_Id AS client_id,c.MCP004_Nome AS name,c.MCP004_Origem AS origin,c.MCP004_Tipo AS type,c.MCP004_Status AS status,c.MCP004_Data_Criacao AS created_at,COUNT(u.MCP009_Id) AS redirect_uri_count FROM MCP004 c LEFT JOIN MCP009 u ON u.MCP009_MCP004_Id=c.MCP004_Id AND u.MCP009_Status=\'active\' GROUP BY c.MCP004_Id,c.MCP004_Client_Id,c.MCP004_Nome,c.MCP004_Origem,c.MCP004_Tipo,c.MCP004_Status,c.MCP004_Data_Criacao ORDER BY c.MCP004_Nome,c.MCP004_Id')->fetchAll();
    }

    /** @return array<int,array<string,mixed>> */
    public function listOAuthRedirectUris(): array
    {
        return $this->pdo->query('SELECT MCP009_Id AS id,MCP009_MCP004_Id AS client_id,MCP009_Redirect_URI AS redirect_uri,MCP009_Status AS status,MCP009_Data_Criacao AS created_at FROM MCP009 ORDER BY MCP009_MCP004_Id,MCP009_Id')->fetchAll();
    }

    /** @return array<int,array{id:int,name:string,login:string}> */
    public function listActiveUsersForCompany(SelectedCompanyContext $company): array
    {
        $statement = $this->pdo->prepare("SELECT c007.C007_Id AS id,c007.C007_Nome AS name,c007.C007_Login AS login FROM C007 c007 INNER JOIN C019 c019 ON c019.C019_C007_Id=c007.C007_Id WHERE c019.C019_C004_Id=:company AND c007.C007_Ativo='S' AND c019.C019_C021_Id>0 GROUP BY c007.C007_Id,c007.C007_Nome,c007.C007_Login HAVING COUNT(*)=1 ORDER BY c007.C007_Nome,c007.C007_Login,c007.C007_Id");
        $statement->execute(['company' => $company->getCompanyId()]);
        $items = [];
        foreach ($statement->fetchAll() as $row) { $items[] = ['id' => (int) $row['id'], 'name' => (string) $row['name'], 'login' => (string) $row['login']]; }
        return $items;
    }

    /** @return array<int,array<string,mixed>> */
    public function listOAuthGrantsForConnection(SelectedCompanyContext $company, int $connectionId): array
    {
        $connection = $this->getConnectionForCompany($company, $connectionId);
        // Scope updates apply to the connection, not to an individual grant.
        // Fetch it once.  The previous correlated subquery was evaluated for
        // every grouped grant row and, on the affected tenant, scanned the
        // connection audit 229 times (about 12.7 seconds in total).
        $scopeUpdateStatement = $this->pdo->prepare("SELECT MAX(MCP003_Data_Criacao) FROM MCP003 WHERE MCP003_MCP001_Id=:connection AND MCP003_Evento='connection_scopes_updated'");
        $scopeUpdateStatement->execute(['connection' => $connectionId]);
        $scopeUpdatedAt = $scopeUpdateStatement->fetchColumn();
        $scopeUpdatedAt = is_string($scopeUpdatedAt) ? $scopeUpdatedAt : null;

        $statement = $this->pdo->prepare('SELECT g.MCP005_Id AS id,g.MCP005_C007_Id AS user_id,g.MCP005_Status AS status,g.MCP005_Data_Consentimento AS consented_at,c.MCP004_Nome AS client_name,GROUP_CONCAT(s.MCP010_Scope ORDER BY s.MCP010_Scope SEPARATOR \' \' ) AS scopes FROM MCP005 g INNER JOIN MCP004 c ON c.MCP004_Id=g.MCP005_MCP004_Id LEFT JOIN MCP010 s ON s.MCP010_MCP005_Id=g.MCP005_Id WHERE g.MCP005_MCP001_Id=:connection GROUP BY g.MCP005_Id,g.MCP005_C007_Id,g.MCP005_Status,g.MCP005_Data_Consentimento,c.MCP004_Nome ORDER BY g.MCP005_Id DESC');
        $statement->execute(['connection' => $connectionId]);
        $connectionScopes = array_fill_keys($connection['scopes'], true);
        $items = [];
        foreach ($statement->fetchAll() as $row) {
            $grantScopes = array_filter(explode(' ', (string) ($row['scopes'] ?? '')));
            $hasMissingScope = array_diff_key($connectionScopes, array_fill_keys($grantScopes, true)) !== [];
            $updatedAt = $scopeUpdatedAt;
            $consentedAt = $row['consented_at'] ?? null;
            $row['requires_scope_reauthorization'] = ($row['status'] ?? '') === 'active'
                && $hasMissingScope
                && is_string($updatedAt)
                && is_string($consentedAt)
                && strtotime($updatedAt) > strtotime($consentedAt);
            $items[] = $row;
        }
        return $items;
    }

    /** @return array<string,mixed> */
    public function eligibilityForConnection(SelectedCompanyContext $company, int $connectionId): array
    {
        $this->getConnectionForCompany($company, $connectionId);
        $companyId = $company->getCompanyId();
        $direct=$this->pdo->prepare("SELECT u.C007_Id AS id,u.C007_Nome AS name,u.C007_Login AS login FROM MCP015 d INNER JOIN C007 u ON u.C007_Id=d.MCP015_C007_Id AND u.C007_Ativo<>'N' WHERE d.MCP015_MCP001_Id=:connection AND d.MCP015_Status='active' ORDER BY u.C007_Nome,u.C007_Login");$direct->execute(['connection'=>$connectionId]);
        $profiles=$this->pdo->prepare("SELECT p.C021_Id AS id,p.C021_Nome_Perfil AS name,COUNT(DISTINCT u.C007_Id) AS member_count FROM MCP016 x INNER JOIN C021 p ON p.C021_Id=x.MCP016_C021_Id LEFT JOIN (SELECT C019_C021_Id,C019_C007_Id FROM C019 WHERE C019_C004_Id=:company) m ON m.C019_C021_Id=p.C021_Id LEFT JOIN C007 u ON u.C007_Id=m.C019_C007_Id AND u.C007_Ativo<>'N' WHERE x.MCP016_MCP001_Id=:connection AND x.MCP016_Status='active' GROUP BY p.C021_Id,p.C021_Nome_Perfil ORDER BY p.C021_Nome_Perfil");$profiles->execute(['connection'=>$connectionId,'company'=>$companyId]);
        $all=$this->pdo->prepare("SELECT DISTINCT p.C021_Id AS id,p.C021_Nome_Perfil AS name FROM C021 p INNER JOIN C019 m ON m.C019_C021_Id=p.C021_Id INNER JOIN C007 u ON u.C007_Id=m.C019_C007_Id AND u.C007_Ativo<>'N' WHERE m.C019_C004_Id=:company ORDER BY p.C021_Nome_Perfil");$all->execute(['company'=>$companyId]);
        $members=$this->pdo->prepare("SELECT DISTINCT m.C019_C021_Id AS profile_id,u.C007_Id AS id,u.C007_Nome AS name,u.C007_Login AS login FROM C019 m INNER JOIN C007 u ON u.C007_Id=m.C019_C007_Id AND u.C007_Ativo<>'N' WHERE m.C019_C004_Id=:company ORDER BY u.C007_Nome,u.C007_Login,u.C007_Id");$members->execute(['company'=>$companyId]);
        $policy=$this->pdo->prepare("SELECT MCP017_Status AS status FROM MCP017 WHERE MCP017_MCP001_Id=:connection LIMIT 1");$policy->execute(['connection'=>$connectionId]); $policyRow=$policy->fetch();
        return ['all_users'=>(is_array($policyRow) && $policyRow['status']==='active'),'direct_users'=>$direct->fetchAll(),'connection_profiles'=>$profiles->fetchAll(),'profiles'=>$all->fetchAll(),'profile_members'=>$members->fetchAll()];
    }

    private function companySummarySql(string $where = ''): string
    {
        return "SELECT c.C004_Id, c.C004_Nome_Empresa, c.C004_Nome_Abreviado, c.C004_Cnpj, c.C004_Flag_Ativo,\n"
            . "       COALESCE(connections.connection_count, 0) AS connection_count,\n"
            . "       COALESCE(connections.active_connection_count, 0) AS active_connection_count,\n"
            . "       connections.last_mcp_usage\n"
            . "FROM C004 c\n"
            . "LEFT JOIN (\n"
            . "    SELECT connection_rows.MCP001_C004_Id,\n"
            . "           COUNT(*) AS connection_count,\n"
            . "           SUM(connection_rows.MCP001_Status = 'active') AS active_connection_count,\n"
            . "           MAX(credential_usage.last_mcp_usage) AS last_mcp_usage\n"
            . "    FROM MCP001 connection_rows\n"
            . "    LEFT JOIN (\n"
            . "        SELECT MCP002_MCP001_Id, MAX(MCP002_Data_Ultimo_Uso) AS last_mcp_usage\n"
            . "        FROM MCP002\n"
            . "        GROUP BY MCP002_MCP001_Id\n"
            . "    ) credential_usage ON credential_usage.MCP002_MCP001_Id = connection_rows.MCP001_Id\n"
            . "    GROUP BY connection_rows.MCP001_C004_Id\n"
            . ") connections ON connections.MCP001_C004_Id = c.C004_Id\n"
            . $where . "\n"
            . "ORDER BY COALESCE(NULLIF(c.C004_Nome_Abreviado, ''), c.C004_Nome_Empresa), c.C004_Id";
    }

    public function validateSelectedCompany(int $companyId): SelectedCompanyContext
    {
        if ($companyId <= 0) {
            throw new RuntimeException('Selected company is invalid.');
        }
        $statement = $this->pdo->prepare("SELECT C004_Id,C004_Nome_Empresa,C004_Nome_Abreviado,C004_Flag_Ativo FROM C004 WHERE C004_Id=:id AND C004_Flag_Ativo = 'S' LIMIT 1");
        $statement->execute(['id' => $companyId]);
        $row = $statement->fetch();
        if (!is_array($row)) {
            throw new RuntimeException('Selected company was not found.');
        }
        return new SelectedCompanyContext(
            (int) $row['C004_Id'],
            (string) ($row['C004_Nome_Empresa'] ?? ''),
            (string) ($row['C004_Nome_Abreviado'] ?? ''),
            (string) ($row['C004_Flag_Ativo'] ?? '')
        );
    }

    /** @param array<string,mixed> $row @return array{id:int,legal_name:string,short_name:string,document:string,status:string,connection_count:int,active_connection_count:int,last_mcp_usage:?string} */
    private function companyDto(array $row): array
    {
        return [
            'id' => (int) $row['C004_Id'],
            'legal_name' => (string) ($row['C004_Nome_Empresa'] ?? ''),
            'short_name' => (string) ($row['C004_Nome_Abreviado'] ?? ''),
            'document' => (string) ($row['C004_Cnpj'] ?? ''),
            'status' => (string) ($row['C004_Flag_Ativo'] ?? ''),
            'connection_count' => (int) ($row['connection_count'] ?? 0),
            'active_connection_count' => (int) ($row['active_connection_count'] ?? 0),
            'last_mcp_usage' => isset($row['last_mcp_usage']) && $row['last_mcp_usage'] !== null ? (string) $row['last_mcp_usage'] : null,
        ];
    }

    /** @param array<string,mixed> $row @return array{id:int,name:string,status:string,scopes:array<int,string>,created_at:string,updated_at:?string,last_usage_at:?string,credential_count:int,active_credential_count:int} */
    private function connectionDto(array $row): array
    {
        $decodedScopes = json_decode((string) ($row['MCP001_Scopes'] ?? '[]'), true);
        $scopes = [];
        if (is_array($decodedScopes)) {
            foreach ($decodedScopes as $scope) {
                if (is_string($scope)) { $scopes[] = $scope; }
            }
        }
        return [
            'id' => (int) $row['MCP001_Id'],
            'name' => (string) ($row['MCP001_Nome'] ?? ''),
            'status' => (string) ($row['MCP001_Status'] ?? ''),
            'public_id' => isset($row['MCP001_Public_Id']) && is_string($row['MCP001_Public_Id']) && preg_match('/\A[A-Za-z0-9_-]{24}\z/', $row['MCP001_Public_Id']) === 1 ? $row['MCP001_Public_Id'] : null,
            'scopes' => $scopes,
            'api_key_responsible_user_id' => isset($row['MCP001_C007_Id']) && $row['MCP001_C007_Id'] !== null ? (int) $row['MCP001_C007_Id'] : null,
            'api_key_responsible_user_name' => isset($row['responsible_user_name']) ? (string) $row['responsible_user_name'] : null,
            'api_key_responsible_user_login' => isset($row['responsible_user_login']) ? (string) $row['responsible_user_login'] : null,
            'created_at' => (string) ($row['MCP001_Data_Criacao'] ?? ''),
            'updated_at' => isset($row['MCP001_Data_Alteracao']) && $row['MCP001_Data_Alteracao'] !== null ? (string) $row['MCP001_Data_Alteracao'] : null,
            'last_usage_at' => isset($row['last_usage_at']) && $row['last_usage_at'] !== null ? (string) $row['last_usage_at'] : null,
            'credential_count' => (int) ($row['credential_count'] ?? 0),
            'active_credential_count' => (int) ($row['active_credential_count'] ?? 0),
        ];
    }
}
