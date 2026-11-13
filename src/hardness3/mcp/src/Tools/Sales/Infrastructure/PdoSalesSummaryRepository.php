<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Sales\Infrastructure;

use HardnessMcp\Tools\Sales\Domain\SalesSummaryRepositoryInterface;
use PDO;

final class PdoSalesSummaryRepository implements SalesSummaryRepositoryInterface
{
    private PDO $pdo;
    private bool $proposalCustomerAvailable;
    public function __construct(PDO $pdo, bool $proposalCustomerAvailable = false) { $this->pdo = $pdo; $this->proposalCustomerAvailable = $proposalCustomerAvailable; }

    /** @return array{invoiced_total:string,invoice_count:int} */
    public function summary(int $companyId, string $dateFrom, string $dateTo, ?int $customerId, ?int $sellerInternalId): array
    {
        [$where, $parameters] = $this->where($companyId, $dateFrom, $dateTo, $customerId, $sellerInternalId);
        $statement = $this->pdo->prepare('SELECT COALESCE(SUM(t007.T007_Valor_Total), 0) AS invoiced_total, COUNT(*) AS invoice_count FROM T007 t007 ' . $where);
        $statement->execute($parameters);
        $row = $statement->fetch();
        return ['invoiced_total' => (string) ($row['invoiced_total'] ?? '0'), 'invoice_count' => (int) ($row['invoice_count'] ?? 0)];
    }

    /** @return array<int,array<string,mixed>> */
    public function grouped(int $companyId, string $dateFrom, string $dateTo, string $groupBy, ?int $customerId, ?int $sellerInternalId, int $limit): array
    {
        [$where, $parameters] = $this->where($companyId, $dateFrom, $dateTo, $customerId, $sellerInternalId);
        $select = 'COUNT(*) AS invoice_count, COALESCE(SUM(t007.T007_Valor_Total), 0) AS invoiced_total';
        $join = '';
        $group = '';
        $order = '';

        if ($groupBy === 'day') { $select = 'DATE(t007.T007_Data_Emissao) AS date, ' . $select; $group = ' GROUP BY DATE(t007.T007_Data_Emissao)'; $order = ' ORDER BY date ASC'; }
        if ($groupBy === 'week') { $select = 'DATE_SUB(DATE(t007.T007_Data_Emissao), INTERVAL WEEKDAY(t007.T007_Data_Emissao) DAY) AS week_start, ' . $select; $group = ' GROUP BY DATE_SUB(DATE(t007.T007_Data_Emissao), INTERVAL WEEKDAY(t007.T007_Data_Emissao) DAY)'; $order = ' ORDER BY week_start ASC'; }
        if ($groupBy === 'month') { $select = "DATE_FORMAT(t007.T007_Data_Emissao, '%Y-%m-01') AS month, " . $select; $group = " GROUP BY DATE_FORMAT(t007.T007_Data_Emissao, '%Y-%m-01')"; $order = ' ORDER BY month ASC'; }
        if ($groupBy === 'customer') { $join = ' LEFT JOIN D024 customer ON customer.D024_Id=t007.T007_D024_Id'; $select = 't007.T007_D024_Id AS customer_id, COALESCE(NULLIF(customer.D024_Nome_Fantasia, \'\'), customer.D024_Nome_Empresa) AS customer_name, ' . $select; $group = ' GROUP BY t007.T007_D024_Id, customer.D024_Nome_Fantasia, customer.D024_Nome_Empresa'; $order = ' ORDER BY invoiced_total DESC, customer_id ASC'; }
        if ($groupBy === 'document') { $proposalJoin = $this->proposalCustomerAvailable ? ' LEFT JOIN T007A proposal_extension ON proposal_extension.T007A_T007_Id=t007.T007_Id LEFT JOIN D024 proposal_customer ON proposal_customer.D024_Id=proposal_extension.T007A_D024_Id_Cliente_Proposta' : ''; $join = ' LEFT JOIN D024 customer ON customer.D024_Id=t007.T007_D024_Id LEFT JOIN D046 customer_group ON customer_group.D046_Id=customer.D024_D046_Id' . $proposalJoin; $select = "t007.T007_Id AS document_id,t007.T007_Numero_Nota_Fiscal AS invoice_number,t007.T007_Data_Emissao AS issued_at,COALESCE(NULLIF(customer.D024_Nome_Fantasia,''),customer.D024_Nome_Empresa) AS customer_name,customer_group.D046_Nome AS customer_group_name," . ($this->proposalCustomerAvailable ? "COALESCE(NULLIF(proposal_customer.D024_Nome_Fantasia,''),proposal_customer.D024_Nome_Empresa) AS proposal_customer_name," : "NULL AS proposal_customer_name,") . 't007.T007_Valor_Total AS invoiced_total,1 AS invoice_count'; $order = ' ORDER BY t007.T007_Data_Emissao DESC,t007.T007_Id DESC'; }
        if ($groupBy === 'seller_internal') { $join = ' LEFT JOIN C007 seller ON seller.C007_Id=t007.T007_C007_Id_Vendedor_Interno'; $select = 't007.T007_C007_Id_Vendedor_Interno AS seller_internal_id, seller.C007_Nome AS seller_internal_name, ' . $select; $group = ' GROUP BY t007.T007_C007_Id_Vendedor_Interno, seller.C007_Nome'; $order = ' ORDER BY invoiced_total DESC, seller_internal_id ASC'; }

        $statement = $this->pdo->prepare('SELECT ' . $select . ' FROM T007 t007' . $join . ' ' . $where . $group . $order . ' LIMIT :limit');
        foreach ($parameters as $name => $value) { $statement->bindValue(':' . $name, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR); }
        $statement->bindValue(':limit', min($limit, 51), PDO::PARAM_INT);
        $statement->execute();
        $rows = $statement->fetchAll();
        return is_array($rows) ? $rows : [];
    }

    /** @return array{0:string,1:array<string,int|string>} */
    private function where(int $companyId, string $dateFrom, string $dateTo, ?int $customerId, ?int $sellerInternalId): array
    {
        $where = "WHERE t007.T007_C004_Id=:company_id AND t007.T007_Data_Emissao>=:date_from AND t007.T007_Data_Emissao<=:date_to AND t007.T007_Flag_Entrada_Saida='S' AND t007.T007_Flag_Cancelada='N' AND t007.T007_Numero_Protocolo_Nfe IS NOT NULL AND TRIM(t007.T007_Numero_Protocolo_Nfe)<>''";
        $parameters = ['company_id' => $companyId, 'date_from' => $dateFrom, 'date_to' => $dateTo];
        if ($customerId !== null) { $where .= ' AND t007.T007_D024_Id=:customer_id'; $parameters['customer_id'] = $customerId; }
        if ($sellerInternalId !== null) { $where .= ' AND t007.T007_C007_Id_Vendedor_Interno=:seller_internal_id'; $parameters['seller_internal_id'] = $sellerInternalId; }
        return [$where, $parameters];
    }
}
