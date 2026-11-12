<?php
declare(strict_types=1);
namespace HardnessMcp\OAuth;
use HardnessMcp\Audit\McpAuditService;
use PDO;
use RuntimeException;

/** Framework-side adapter: translates an already authenticated ERP C007 into MCP018. */
final class OAuthAuthorizationBridgeService
{
    private PDO $pdo; private array $config;
    public function __construct(PDO $pdo,array $config){$this->pdo=$pdo;$this->config=$config;}
    public function resume(string $cookie,int $userId): void
    {
        $parts=explode('.',$cookie,2);if(count($parts)!==2||preg_match('/^[A-Za-z0-9_-]{16,48}$/',$parts[0])!==1||preg_match('/^[A-Za-z0-9_-]{32,64}$/',$parts[1])!==1||$userId<1)throw new RuntimeException('Invalid OAuth authorization continuation.');
        $s=$this->pdo->prepare("SELECT r.MCP018_Id,r.MCP018_Pepper_Version,r.MCP018_Secret_Hash FROM MCP018 r INNER JOIN C007 u ON u.C007_Ativo='S' WHERE u.C007_Id=:user AND r.MCP018_Selector=:selector AND r.MCP018_Status='pending' AND r.MCP018_C007_Id IS NULL AND r.MCP018_Data_Expiracao>UTC_TIMESTAMP() LIMIT 1");$s->execute(['user'=>$userId,'selector'=>$parts[0]]);$row=$s->fetch();if(!is_array($row))throw new RuntimeException('Invalid OAuth authorization continuation.');
        $pepper=(string)(($this->config['oauth']['authorization_request_peppers']??[])[$row['MCP018_Pepper_Version']]??'');if($pepper===''||!hash_equals($this->hash($parts[1],$pepper),(string)$row['MCP018_Secret_Hash']))throw new RuntimeException('Invalid OAuth authorization continuation.');
        $u=$this->pdo->prepare("UPDATE MCP018 SET MCP018_Status='authenticated',MCP018_C007_Id=:user,MCP018_Data_Autenticacao=UTC_TIMESTAMP() WHERE MCP018_Id=:id AND MCP018_Status='pending' AND MCP018_C007_Id IS NULL");$u->execute(['user'=>$userId,'id'=>$row['MCP018_Id']]);if($u->rowCount()!==1)throw new RuntimeException('Invalid OAuth authorization continuation.');
        (new McpAuditService($this->pdo))->recordOAuthAdministrative('oauth_authorization_authenticated',null,null,null,null,null,'oauth-authorization','',$userId);
    }
    private function hash(string $secret,string $pepper): string{return hash_hmac('sha256',$secret,$pepper,true);}
}
