<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Product\Infrastructure;

use HardnessMcp\Tools\Product\Domain\ProductRepositoryInterface;
use HardnessMcp\Tools\Product\PartnerReference\Domain\PartnerReferenceProductRepositoryInterface;
use PDO;

final class PdoProductRepository implements ProductRepositoryInterface, PartnerReferenceProductRepositoryInterface
{
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    /** @return array<int,array<string,mixed>> */
    public function search(int $companyId, string $query, int $limit): array
    {
        $term = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $query) . '%';
        $sql = "SELECT d001.D001_Id,d001.D001_Codigo_Produto,d001.D001_Descricao_Produto,d037.D037_Unidade\n"
            . "FROM D001 d001\n"
            . "LEFT JOIN D037 d037 ON d037.D037_Id=d001.D001_D037_Id\n"
            . "WHERE d001.D001_Flag_Ativo='S'\n"
            . "  AND NULLIF(TRIM(d001.D001_Codigo_Produto),'') IS NOT NULL\n"
            . "  AND NULLIF(TRIM(d001.D001_Descricao_Produto),'') IS NOT NULL\n"
            . "  AND (d001.D001_Codigo_Produto LIKE :code_term ESCAPE '\\\\' OR d001.D001_Descricao_Produto LIKE :description_term ESCAPE '\\\\')\n"
            . "  AND EXISTS (SELECT 1 FROM D049 d049 INNER JOIN D009 d009 ON d009.D009_D049_Id=d049.D049_Id WHERE d049.D049_D001_Id=d001.D001_Id AND d009.D009_C004_Id=:company_id)\n"
            . "ORDER BY d001.D001_Codigo_Produto,d001.D001_Id LIMIT :limit";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':code_term', $term, PDO::PARAM_STR);
        $statement->bindValue(':description_term', $term, PDO::PARAM_STR);
        $statement->bindValue(':company_id', $companyId, PDO::PARAM_INT);
        $statement->bindValue(':limit', min($limit, 51), PDO::PARAM_INT);
        $statement->execute();
        $rows = $statement->fetchAll();
        return is_array($rows) ? $rows : [];
    }

    public function findVisiblePartnerByCode(int $companyId, int $partnerCode, string $role): ?array
    {
        $roleColumn = $role === 'customer' ? 'D024_Flag_Cliente' : 'D024_Flag_Fornecedor';
        $sql = "SELECT D024_Id FROM D024 WHERE D024_Id=:partner_code AND D024_Flag_Ativo='S' AND {$roleColumn}='S' AND (D024_C004_Id IS NULL OR D024_C004_Id=0 OR D024_C004_Id=:company_id) LIMIT 1";
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['partner_code' => $partnerCode, 'company_id' => $companyId]);
        $row = $statement->fetch();
        return is_array($row) ? $row : null;
    }

    /** @return array<int,array<string,mixed>> */
    public function findProductsByPartnerReference(int $companyId, int $partnerCode, string $role, string $referenceCode): array
    {
        $referenceRoleColumn = $role === 'customer' ? 'D083_Flag_Cliente' : 'D083_Flag_Fornecedor';
        $sql = "SELECT DISTINCT d001.D001_Id,d001.D001_Codigo_Produto,d001.D001_Descricao_Produto,d037.D037_Unidade\n"
            . "FROM D083 d083\n"
            . "INNER JOIN D001 d001 ON d001.D001_Id=d083.D083_D001_Id\n"
            . "LEFT JOIN D037 d037 ON d037.D037_Id=d001.D001_D037_Id\n"
            . "WHERE d083.D083_D024_Id=:partner_code\n"
            . "  AND d083.D083_Codigo_Produto_Fornecedor=:reference_code\n"
            . "  AND d083.{$referenceRoleColumn}='S'\n"
            . "  AND d001.D001_Flag_Ativo='S'\n"
            . "  AND NULLIF(TRIM(d001.D001_Codigo_Produto),'') IS NOT NULL\n"
            . "  AND NULLIF(TRIM(d001.D001_Descricao_Produto),'') IS NOT NULL\n"
            . "  AND EXISTS (SELECT 1 FROM D049 d049 INNER JOIN D009 d009 ON d009.D009_D049_Id=d049.D049_Id WHERE d049.D049_D001_Id=d001.D001_Id AND d009.D009_C004_Id=:company_id)\n"
            . "ORDER BY d001.D001_Codigo_Produto,d001.D001_Id";
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['partner_code' => $partnerCode, 'reference_code' => $referenceCode, 'company_id' => $companyId]);
        $rows = $statement->fetchAll();
        return is_array($rows) ? $rows : [];
    }
}
