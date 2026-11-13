<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Company\Infrastructure;

use HardnessMcp\Tools\Company\Domain\CompanyRepositoryInterface;
use PDO;

final class PdoCompanyRepository implements CompanyRepositoryInterface
{
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    /** @return array<string, mixed>|null */
    public function findBasicById(int $companyId): ?array
    {
        $statement = $this->pdo->prepare('SELECT C004_Id,C004_Nome_Empresa,C004_Nome_Abreviado,C004_Cnpj,C004_Cidade,C004_Uf,C004_Flag_Ativo FROM C004 WHERE C004_Id=:c004_id LIMIT 1');
        $statement->execute(['c004_id' => $companyId]);
        $row = $statement->fetch();
        return is_array($row) ? $row : null;
    }
}
