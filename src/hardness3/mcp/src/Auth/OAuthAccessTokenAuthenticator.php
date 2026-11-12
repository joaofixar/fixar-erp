<?php

declare(strict_types=1);

namespace HardnessMcp\Auth;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Core\OperationalTrace;
use HardnessMcp\Eligibility\McpConnectionEligibilityResolver;
use HardnessMcp\OAuth\PermissionResolver;
use PDO;
use RuntimeException;
use Throwable;

/** Validates only opaque MCP007 access tokens. MCP008 refresh tokens have no path here. */
final class OAuthAccessTokenAuthenticator
{
    /** @var array<string,string> */
    private array $peppers;
    private string $currentPepperVersion;

    /** @param array<string,mixed> $config */
    public function __construct(array $config)
    {
        $this->peppers = (array) ($config['oauth']['access_token_peppers'] ?? []);
        $this->currentPepperVersion = (string) ($config['oauth']['access_token_pepper_current_version'] ?? '');
    }

    public function authenticate(PDO $pdo, string $credential, string $canonicalResource, string $ip): ?ExecutionContext
    {
        try {
            $parts = explode('.', $credential, 2);
            if (count($parts) !== 2 || preg_match('/\A[A-Za-z0-9_-]{16}\z/', $parts[0]) !== 1 || preg_match('/\A[A-Za-z0-9_-]{32,64}\z/', $parts[1]) !== 1) return null;
            OperationalTrace::reach('oauth_token_lookup');
            $statement = $pdo->prepare("SELECT t.MCP007_Id,t.MCP007_Secret_Hash,t.MCP007_Pepper_Version,t.MCP007_Resource,t.MCP007_Status,t.MCP007_Data_Expiracao,g.MCP005_Id,g.MCP005_Status,g.MCP005_Data_Expiracao,g.MCP005_MCP001_Id,g.MCP005_C007_Id,g.MCP005_MCP004_Id,c.MCP001_C004_Id,c.MCP001_Scopes,c.MCP001_Status,c.MCP001_Data_Expiracao,u.C007_Ativo,cl.MCP004_Status,((SELECT COUNT(*) FROM C007 actor INNER JOIN C019 membership ON membership.C019_C007_Id=actor.C007_Id INNER JOIN C004 actor_company ON actor_company.C004_Id=membership.C019_C004_Id AND actor_company.C004_Flag_Ativo='S' WHERE actor.C007_Id=g.MCP005_C007_Id AND actor.C007_Ativo='S' AND membership.C019_C021_Id>0 AND membership.C019_C004_Id=c.MCP001_C004_Id)=1) AS MCP005_OAuth_Actor_Valid FROM MCP007 t INNER JOIN MCP005 g ON g.MCP005_Id=t.MCP007_MCP005_Id INNER JOIN MCP001 c ON c.MCP001_Id=g.MCP005_MCP001_Id INNER JOIN C007 u ON u.C007_Id=g.MCP005_C007_Id INNER JOIN MCP004 cl ON cl.MCP004_Id=g.MCP005_MCP004_Id WHERE t.MCP007_Selector=:selector LIMIT 1");
            $statement->execute(['selector' => $parts[0]]);
            $row = $statement->fetch();
            if (!is_array($row)) {
                $this->hashWithCurrentPepper($parts[1]);
                return null;
            }
            OperationalTrace::reach('oauth_token_hash_validation');
            if (!$this->validSecret($parts[1], (string) $row['MCP007_Secret_Hash'], (string) $row['MCP007_Pepper_Version'])) return null;
            OperationalTrace::reach('grant_validation');
            if ($row['MCP005_Status'] !== 'active' || $this->expired($row['MCP005_Data_Expiracao'])) return null;
            OperationalTrace::reach('connection_validation');
            if ($row['MCP007_Status'] !== 'active' || $this->expired($row['MCP007_Data_Expiracao']) || $row['MCP001_Status'] !== 'active' || $this->expired($row['MCP001_Data_Expiracao']) || !hash_equals($canonicalResource, (string) $row['MCP007_Resource']) || $row['MCP004_Status'] !== 'active') return null;
            OperationalTrace::reach('user_validation');
            if ($row['C007_Ativo'] !== 'S') return null;
            $connectionId = (int) $row['MCP005_MCP001_Id'];
            $userId = (int) $row['MCP005_C007_Id'];
            OperationalTrace::reach('scope_resolution');
            $connectionScopes = $this->scopes((string) $row['MCP001_Scopes']);
            $grantScopes = $this->scopeRows($pdo, 'MCP010', 'MCP010_MCP005_Id', (int) $row['MCP005_Id']);
            $tokenScopes = $this->scopeRows($pdo, 'MCP012', 'MCP012_MCP007_Id', (int) $row['MCP007_Id']);
            $scopes = (new PermissionResolver())->resolve($connectionScopes, $grantScopes, [$tokenScopes]);
            $use = $pdo->prepare('UPDATE MCP007 SET MCP007_Data_Ultimo_Uso=UTC_TIMESTAMP(),MCP007_IP_Ultimo_Uso=:ip WHERE MCP007_Id=:id AND MCP007_Status=\'active\'');
            $use->execute(['id' => $row['MCP007_Id'], 'ip' => substr($ip, 0, 45)]);
            // An active OAuth identity without a legacy ERP actor must be
            // authenticated but cannot reach actor-required Tools. This keeps
            // the functional erp_actor_required response instead of failing
            // later in LegacyExecutionContextAdapter.
            if (((int) ($row['MCP005_OAuth_Actor_Valid'] ?? 0)) !== 1) {
                return new ExecutionContext($connectionId, (int) $row['MCP007_Id'], (int) $row['MCP001_C004_Id'], null, $scopes, 'oauth', (int) $row['MCP005_MCP004_Id'], (int) $row['MCP005_Id'], (int) $row['MCP007_Id']);
            }
            OperationalTrace::reach('eligibility_validation');
            if (!(new McpConnectionEligibilityResolver($pdo))->canAuthorizeConnection($userId, $connectionId)) return null;
            OperationalTrace::reach('execution_context');
            return new ExecutionContext($connectionId, (int) $row['MCP007_Id'], (int) $row['MCP001_C004_Id'], $userId, $scopes, 'oauth', (int) $row['MCP005_MCP004_Id'], (int) $row['MCP005_Id'], (int) $row['MCP007_Id']);
        } catch (Throwable $exception) { throw $exception; }
    }

    private function validSecret(string $secret, string $hash, string $version): bool
    {
        $pepper = $this->peppers[$version] ?? '';
        return is_string($pepper) && $pepper !== '' && hash_equals($hash, hash_hmac('sha256', $secret, $pepper, true));
    }

    private function hashWithCurrentPepper(string $secret): void
    {
        $pepper = $this->peppers[$this->currentPepperVersion] ?? '';
        if (is_string($pepper) && $pepper !== '') hash_hmac('sha256', $secret, $pepper, true);
    }

    private function expired($value): bool
    {
        if ($value === null) return false;
        $expiration = \DateTimeImmutable::createFromFormat('!Y-m-d H:i:s', (string) $value, new \DateTimeZone('UTC'));
        return $expiration === false || $expiration <= new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }

    /** @return string[] */
    private function scopes(string $json): array
    {
        $scopes = json_decode($json, true);
        if (!is_array($scopes)) throw new RuntimeException('Invalid MCP scopes.');
        return $scopes;
    }

    /** @return string[] */
    private function scopeRows(PDO $pdo, string $table, string $foreignKey, int $id): array
    {
        $column = $table . '_Scope';
        $statement = $pdo->prepare("SELECT $column FROM $table WHERE $foreignKey=:id ORDER BY $column");
        $statement->execute(['id' => $id]);
        return array_map(static fn (array $row): string => (string) $row[$column], $statement->fetchAll());
    }
}
