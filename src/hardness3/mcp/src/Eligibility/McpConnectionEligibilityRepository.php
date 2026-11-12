<?php
declare(strict_types=1);
namespace HardnessMcp\Eligibility;
use PDO;

final class McpConnectionEligibilityRepository
{
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }
    public function canAuthorize(int $userId, int $connectionId): bool
    {
        // Keep user predicates out of JOIN ... ON. Older MySQL/PDO stacks
        // can incorrectly reject a qualified C007 column there. EXISTS also
        // avoids multiplying C007 by the company-membership table.
        $sql = "SELECT c.MCP001_Id FROM MCP001 c WHERE c.MCP001_Id=:connection AND c.MCP001_Status='active' AND (c.MCP001_Data_Expiracao IS NULL OR c.MCP001_Data_Expiracao>UTC_TIMESTAMP()) AND EXISTS (SELECT 1 FROM C007 active_user WHERE active_user.C007_Id=:active_user AND active_user.C007_Ativo='S') AND EXISTS (SELECT 1 FROM C019 membership INNER JOIN C004 company ON company.C004_Id=membership.C019_C004_Id AND company.C004_Flag_Ativo='S' WHERE membership.C019_C007_Id=:membership_user AND membership.C019_C004_Id=c.MCP001_C004_Id AND membership.C019_C021_Id>0) AND (EXISTS (SELECT 1 FROM MCP017 a WHERE a.MCP017_MCP001_Id=c.MCP001_Id AND a.MCP017_Status='active') OR EXISTS (SELECT 1 FROM MCP015 d WHERE d.MCP015_MCP001_Id=c.MCP001_Id AND d.MCP015_C007_Id=:direct_user AND d.MCP015_Status='active') OR EXISTS (SELECT 1 FROM MCP016 cp INNER JOIN C019 m ON m.C019_C021_Id=cp.MCP016_C021_Id WHERE m.C019_C004_Id=c.MCP001_C004_Id AND m.C019_C007_Id=:profile_user AND cp.MCP016_MCP001_Id=c.MCP001_Id AND cp.MCP016_Status='active')) LIMIT 1";
        $statement = $this->pdo->prepare($sql); $statement->execute(['active_user' => $userId, 'membership_user' => $userId, 'connection' => $connectionId, 'direct_user' => $userId, 'profile_user' => $userId]);
        return is_array($statement->fetch());
    }
    /** @return array<int,array<string,mixed>> */
    public function listEligibleConnections(int $userId, int $companyId): array
    {
        $sql = "SELECT DISTINCT c.MCP001_Id AS id,c.MCP001_Nome AS name,c.MCP001_C004_Id AS company_id,COALESCE(NULLIF(TRIM(e.C004_Nome_Abreviado),''),e.C004_Nome_Empresa) AS company_name FROM MCP001 c INNER JOIN C004 e ON e.C004_Id=c.MCP001_C004_Id AND e.C004_Flag_Ativo='S' WHERE c.MCP001_Status='active' AND (c.MCP001_Data_Expiracao IS NULL OR c.MCP001_Data_Expiracao>UTC_TIMESTAMP()) AND EXISTS (SELECT 1 FROM C007 active_user WHERE active_user.C007_Id=:active_user AND active_user.C007_Ativo='S') AND EXISTS (SELECT 1 FROM C019 membership INNER JOIN C004 company ON company.C004_Id=membership.C019_C004_Id AND company.C004_Flag_Ativo='S' WHERE membership.C019_C007_Id=:membership_user AND membership.C019_C004_Id=c.MCP001_C004_Id AND membership.C019_C021_Id>0) AND (EXISTS (SELECT 1 FROM MCP017 a WHERE a.MCP017_MCP001_Id=c.MCP001_Id AND a.MCP017_Status='active') OR EXISTS (SELECT 1 FROM MCP015 d WHERE d.MCP015_MCP001_Id=c.MCP001_Id AND d.MCP015_C007_Id=:direct_user AND d.MCP015_Status='active') OR EXISTS (SELECT 1 FROM MCP016 cp INNER JOIN C019 m ON m.C019_C021_Id=cp.MCP016_C021_Id WHERE m.C019_C004_Id=c.MCP001_C004_Id AND m.C019_C007_Id=:profile_user AND cp.MCP016_MCP001_Id=c.MCP001_Id AND cp.MCP016_Status='active')) ORDER BY c.MCP001_Nome,c.MCP001_Id";
        $statement=$this->pdo->prepare($sql);$statement->execute(['active_user'=>$userId,'membership_user'=>$userId,'direct_user'=>$userId,'profile_user'=>$userId]);return $statement->fetchAll();
    }
}
