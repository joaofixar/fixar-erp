<?php

declare(strict_types=1);

namespace HardnessWebhooks\Admin\Context;

/** Resolves a navigation company into an immutable, authorized admin context. */
final class WebhookAdminCompanyResolver
{
    /** @return array<int,array{id:int,name:string,active_webhooks:int}> */
    public function activeCompanies(\mysqli $connection): array
    {
        $statement = $connection->prepare("SELECT c.C004_Id,c.C004_Nome_Empresa,c.C004_Nome_Abreviado,SUM(CASE WHEN e.WHK001_Status='A' THEN 1 ELSE 0 END) active_webhooks FROM C004 c LEFT JOIN WHK001 e ON e.WHK001_C004_Id=c.C004_Id WHERE c.C004_Flag_Ativo='S' GROUP BY c.C004_Id,c.C004_Nome_Empresa,c.C004_Nome_Abreviado,c.C004_Ordem_Empresa ORDER BY c.C004_Ordem_Empresa,c.C004_Id");
        if ($statement === false) throw new \RuntimeException('Could not prepare company list.');
        try {
            $statement->execute();
            $result = array();
            foreach ($statement->get_result()->fetch_all(MYSQLI_ASSOC) as $row) {
                $name = trim((string) ($row['C004_Nome_Abreviado'] ?? ''));
                if ($name === '') $name = trim((string) ($row['C004_Nome_Empresa'] ?? ''));
                $result[] = array('id' => (int) $row['C004_Id'], 'name' => $name === '' ? 'Empresa' : $name, 'active_webhooks' => (int) ($row['active_webhooks'] ?? 0));
            }
            return $result;
        } finally { $statement->close(); }
    }

    /** @param mixed $companyId */
    public function resolve(WebhookAdminContext $base, \mysqli $connection, $companyId): WebhookAdminContext
    {
        $id = (is_int($companyId) || is_string($companyId)) ? filter_var($companyId, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1))) : false;
        if ($id === false) throw new \OutOfBoundsException('Company not found.');
        $statement = $connection->prepare("SELECT C004_Id FROM C004 WHERE C004_Id=? AND C004_Flag_Ativo='S' LIMIT 1");
        if ($statement === false) throw new \RuntimeException('Could not prepare company validation.');
        try {
            $company = (int) $id;
            $statement->bind_param('i', $company);
            $statement->execute();
            if ($statement->get_result()->num_rows !== 1) throw new \OutOfBoundsException('Company not found.');
        } finally { $statement->close(); }
        return new WebhookAdminContext($base->getUserId(), $base->getProfileId(), (int) $id);
    }
}
