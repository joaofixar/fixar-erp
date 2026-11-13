<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Partner\Infrastructure;

use HardnessMcp\Tools\Partner\Domain\PartnerRepositoryInterface;
use PDO;

final class PdoPartnerRepository implements PartnerRepositoryInterface
{
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    /** @return array<int,array<string,mixed>> */
    public function searchByDocument(int $companyId, string $document, string $role, int $limit): array
    {
        $sql = "SELECT d024.D024_Id,d024.D024_Nome_Empresa,d024.D024_Nome_Fantasia,d024.D024_Cpf,d024.D024_Cnpj,d024.D024_Flag_Cliente,d024.D024_Flag_Fornecedor,d024.D024_Flag_Ativo,d024.D024_Cidade,d024.D024_Estado\n"
            . "FROM D024 d024 WHERE d024.D024_Flag_Ativo='S' AND (d024.D024_C004_Id IS NULL OR d024.D024_C004_Id=0 OR d024.D024_C004_Id=:company_id)";
        if ($role === 'customer') { $sql .= " AND d024.D024_Flag_Cliente='S'"; }
        if ($role === 'supplier') { $sql .= " AND d024.D024_Flag_Fornecedor='S'"; }
        $sql .= " AND (REPLACE(REPLACE(REPLACE(IFNULL(d024.D024_Cpf,''),'.',''),'-',''),' ','')=:document_cpf OR REPLACE(REPLACE(REPLACE(REPLACE(IFNULL(d024.D024_Cnpj,''),'.',''),'/',''),'-',''),' ','')=:document_cnpj)";
        $sql .= ' ORDER BY d024.D024_Nome_Empresa,d024.D024_Id LIMIT :limit';
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':company_id', $companyId, PDO::PARAM_INT);
        $statement->bindValue(':document_cpf', $document, PDO::PARAM_STR);
        $statement->bindValue(':document_cnpj', $document, PDO::PARAM_STR);
        $statement->bindValue(':limit', min($limit, 21), PDO::PARAM_INT);
        $statement->execute();
        $rows = $statement->fetchAll();
        return is_array($rows) ? $rows : [];
    }

    public function searchByName(int $companyId, string $query, string $role, int $limit): array
    {
        $sql = "SELECT d024.D024_Id,d024.D024_Nome_Empresa,d024.D024_Nome_Fantasia,d024.D024_Flag_Cliente,d024.D024_Flag_Fornecedor,d024.D024_Cidade,d024.D024_Estado\n"
            . "FROM D024 d024 WHERE d024.D024_Flag_Ativo='S' AND (d024.D024_C004_Id IS NULL OR d024.D024_C004_Id=0 OR d024.D024_C004_Id=:company_id)";
        if ($role === 'customer') { $sql .= " AND d024.D024_Flag_Cliente='S'"; }
        if ($role === 'supplier') { $sql .= " AND d024.D024_Flag_Fornecedor='S'"; }
        $legal = "TRIM(REPLACE(REPLACE(d024.D024_Nome_Empresa,'  ',' '),'  ',' '))";
        $fantasy = "TRIM(REPLACE(REPLACE(d024.D024_Nome_Fantasia,'  ',' '),'  ',' '))";
        $sql .= " AND ($legal=:exact_legal OR $fantasy=:exact_fantasy OR $legal LIKE :prefix_legal OR $fantasy LIKE :prefix_fantasy)";
        $sql .= " ORDER BY ($legal=:order_legal) DESC,($fantasy=:order_fantasy) DESC,d024.D024_Nome_Empresa,d024.D024_Id LIMIT :limit";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':company_id', $companyId, PDO::PARAM_INT);
        foreach ([':exact_legal', ':exact_fantasy', ':order_legal', ':order_fantasy'] as $parameter) $statement->bindValue($parameter, $query, PDO::PARAM_STR);
        foreach ([':prefix_legal', ':prefix_fantasy'] as $parameter) $statement->bindValue($parameter, $query . '%', PDO::PARAM_STR);
        $statement->bindValue(':limit', min($limit, 6), PDO::PARAM_INT);
        $statement->execute(); $rows = $statement->fetchAll();
        return is_array($rows) ? $rows : [];
    }
    public function findVisibleById(int $companyId, int $partnerId): ?array { $sql="SELECT d.D024_Id,d.D024_Nome_Empresa,d.D024_Nome_Fantasia,d.D024_Flag_Pessoa_Fisica_Juridica,d.D024_Cpf,d.D024_Cnpj,d.D024_Flag_Ativo,d.D024_Flag_Cliente,d.D024_Flag_Fornecedor,d.D024_Endereco,d.D024_Numero,d.D024_Complemento,d.D024_Bairro,COALESCE(NULLIF(TRIM(d.D024_Cidade),''),city.D020_Nome_Cidade) AS D024_Cidade,COALESCE(NULLIF(TRIM(d.D024_Estado),''),state.D018_UF) AS D024_Estado,d.D024_Cep FROM D024 d LEFT JOIN D020 city ON city.D020_Id=d.D024_D020_Id LEFT JOIN D018 state ON state.D018_Id=d.D024_D018_Id WHERE d.D024_Id=:id AND (d.D024_C004_Id IS NULL OR d.D024_C004_Id=0 OR d.D024_C004_Id=:company) LIMIT 1"; $s=$this->pdo->prepare($sql); $s->execute(['id'=>$partnerId,'company'=>$companyId]); $row=$s->fetch(); return is_array($row)?$row:null; }
}
