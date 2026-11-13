<?php
declare(strict_types=1);
namespace HardnessMcp\Admin;
use HardnessMcp\Audit\McpAuditService;
use PDO;
use RuntimeException;

/** Internal CRUD for connection eligibility only; no scopes or OAuth protocol behavior. */
final class McpConnectionEligibilityAdminService
{
    private PDO $pdo; private McpAuditService $audit; private string $host; private string $ip; private ?int $actor;
    public function __construct(PDO $pdo, string $host, string $ip, ?int $actor) { $this->pdo=$pdo; $this->audit=new McpAuditService($pdo); $this->host=$host; $this->ip=$ip; $this->actor=$actor; }
    public function addDirectUser(int $company, int $connection, int $user): void { $this->connection($company,$connection); $this->user($company,$user); $this->upsert('MCP015','MCP015_MCP001_Id','MCP015_C007_Id',$connection,$user); $this->event('mcp_eligibility_user_added',$connection,$company); }
    public function revokeDirectUser(int $company, int $connection, int $user): void { $this->connection($company,$connection); $this->revoke('MCP015','MCP015_MCP001_Id','MCP015_C007_Id',$connection,$user); $this->event('mcp_eligibility_user_revoked',$connection,$company); }
    public function addProfileToConnection(int $company,int $connection,int $profile): void { $this->connection($company,$connection); $this->profile($company,$profile); $this->upsert('MCP016','MCP016_MCP001_Id','MCP016_C021_Id',$connection,$profile); $this->event('mcp_eligibility_profile_added',$connection,$company); }
    public function revokeProfileFromConnection(int $company,int $connection,int $profile): void { $this->connection($company,$connection); $this->revoke('MCP016','MCP016_MCP001_Id','MCP016_C021_Id',$connection,$profile); $this->event('mcp_eligibility_profile_revoked',$connection,$company); }
    public function setAllUsers(int $company, int $connection, bool $enabled): void
    {
        $this->connection($company, $connection);
        $sql = "INSERT INTO MCP017 (MCP017_MCP001_Id,MCP017_Status,MCP017_Data_Alteracao,MCP017_Data_Desativacao) VALUES (:connection,:status,UTC_TIMESTAMP(),:disabledAt) ON DUPLICATE KEY UPDATE MCP017_Status=VALUES(MCP017_Status),MCP017_Data_Alteracao=UTC_TIMESTAMP(),MCP017_Data_Desativacao=VALUES(MCP017_Data_Desativacao)";
        $this->pdo->prepare($sql)->execute(['connection' => $connection, 'status' => $enabled ? 'active' : 'disabled', 'disabledAt' => $enabled ? null : gmdate('Y-m-d H:i:s')]);
        $this->event($enabled ? 'mcp_eligibility_all_users_enabled' : 'mcp_eligibility_all_users_disabled', $connection, $company);
    }
    private function connection(int $company,int $connection): void { $s=$this->pdo->prepare('SELECT MCP001_Id FROM MCP001 WHERE MCP001_Id=:connection AND MCP001_C004_Id=:company');$s->execute(['connection'=>$connection,'company'=>$company]);if(!is_array($s->fetch()))throw new RuntimeException('Invalid MCP connection.'); }
    private function user(int $company,int $user): void { $s=$this->pdo->prepare("SELECT C007_Id FROM C007 WHERE C007_Id=:user AND C007_Ativo<>'N'");$s->execute(['user'=>$user]);if(!is_array($s->fetch()))throw new RuntimeException('Invalid active user.'); }
    private function profile(int $company,int $profile): void { $s=$this->pdo->prepare('SELECT p.C021_Id FROM C021 p INNER JOIN C019 m ON m.C019_C021_Id=p.C021_Id INNER JOIN C007 u ON u.C007_Id=m.C019_C007_Id AND u.C007_Ativo<>\'N\' WHERE p.C021_Id=:profile AND m.C019_C004_Id=:company LIMIT 1');$s->execute(['profile'=>$profile,'company'=>$company]);if(!is_array($s->fetch()))throw new RuntimeException('Invalid ERP profile.'); }
    private function upsert(string $table,string $left,string $right,int $leftValue,int $rightValue): void { $sql="INSERT INTO $table ($left,$right,{$table}_Status) VALUES (:left,:right,'active') ON DUPLICATE KEY UPDATE {$table}_Status='active',{$table}_Data_Revogacao=NULL";$this->pdo->prepare($sql)->execute(['left'=>$leftValue,'right'=>$rightValue]); }
    private function revoke(string $table,string $left,string $right,int $leftValue,int $rightValue): void { $sql="UPDATE $table SET {$table}_Status='revoked',{$table}_Data_Revogacao=UTC_TIMESTAMP() WHERE $left=:left AND $right=:right AND {$table}_Status='active'";$s=$this->pdo->prepare($sql);$s->execute(['left'=>$leftValue,'right'=>$rightValue]);if($s->rowCount()!==1)throw new RuntimeException('Eligibility link not found.'); }
    private function event(string $event,int $connection,int $company): void { $this->audit->recordAdministrative($event,$connection,null,$company,$this->host,$this->ip,$this->actor); }
}
