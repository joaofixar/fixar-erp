<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Order\Infrastructure;

use HardnessMcp\Tools\Order\Domain\OrderBacklogRepositoryInterface;
use PDO;

final class PdoOrderBacklogRepository implements OrderBacklogRepositoryInterface
{
    private PDO $pdo;
    private bool $proposalCustomerAvailable;

    public function __construct(PDO $pdo, bool $proposalCustomerAvailable = false)
    {
        $this->pdo = $pdo;
        $this->proposalCustomerAvailable = $proposalCustomerAvailable;
    }

    public function summary(int $companyId, string $from, string $to, ?int $customerId, ?int $sellerId, ?int $status): array
    {
        [$where, $parameters] = $this->where($companyId, $from, $to, $customerId, $sellerId, $status);
        $statement = $this->pdo->prepare('SELECT COALESCE(SUM(t.T005_Valor_Total),0) backlog_total,COUNT(*) order_count FROM T005 t ' . $where);
        $statement->execute($parameters);
        return $statement->fetch() ?: [];
    }

    public function grouped(int $companyId, string $from, string $to, string $groupBy, ?int $customerId, ?int $sellerId, ?int $status, int $limit): array
    {
        [$where, $parameters] = $this->where($companyId, $from, $to, $customerId, $sellerId, $status);
        $totals = 'COALESCE(SUM(t.T005_Valor_Total),0) backlog_total,COUNT(*) order_count';
        $join = '';
        $group = '';
        $order = '';

        if ($groupBy === 'day') { $select = 'DATE(t.T005_Data_Emissao) date,' . $totals; $group = ' GROUP BY DATE(t.T005_Data_Emissao)'; $order = ' ORDER BY date ASC'; }
        elseif ($groupBy === 'week') { $select = 'DATE_SUB(DATE(t.T005_Data_Emissao), INTERVAL WEEKDAY(t.T005_Data_Emissao) DAY) week_start,' . $totals; $group = ' GROUP BY DATE_SUB(DATE(t.T005_Data_Emissao), INTERVAL WEEKDAY(t.T005_Data_Emissao) DAY)'; $order = ' ORDER BY week_start ASC'; }
        elseif ($groupBy === 'month') { $select = "DATE_FORMAT(t.T005_Data_Emissao, '%Y-%m-01') month," . $totals; $group = " GROUP BY DATE_FORMAT(t.T005_Data_Emissao, '%Y-%m-01')"; $order = ' ORDER BY month ASC'; }
        elseif ($groupBy === 'customer') { $join = ' LEFT JOIN D024 customer ON customer.D024_Id=t.T005_D024_Id'; $select = "t.T005_D024_Id customer_id,COALESCE(NULLIF(customer.D024_Nome_Fantasia,''),customer.D024_Nome_Empresa) customer_name," . $totals; $group = ' GROUP BY t.T005_D024_Id,customer.D024_Nome_Fantasia,customer.D024_Nome_Empresa'; $order = ' ORDER BY backlog_total DESC,customer_id ASC'; }
        elseif ($groupBy === 'seller_internal') { $join = ' LEFT JOIN C007 seller ON seller.C007_Id=t.T005_C007_Id_Vendedor_Interno'; $select = 't.T005_C007_Id_Vendedor_Interno seller_internal_id,seller.C007_Nome seller_internal_name,' . $totals; $group = ' GROUP BY t.T005_C007_Id_Vendedor_Interno,seller.C007_Nome'; $order = ' ORDER BY backlog_total DESC,seller_internal_id ASC'; }
        elseif ($groupBy === 'document') {
            $proposalJoin = $this->proposalCustomerAvailable ? ' LEFT JOIN T005A proposal_extension ON proposal_extension.T005A_T005_Id=t.T005_Id LEFT JOIN D024 proposal_customer ON proposal_customer.D024_Id=proposal_extension.T005A_D024_Id_Cliente_Proposta' : '';
            $join = ' LEFT JOIN D024 customer ON customer.D024_Id=t.T005_D024_Id LEFT JOIN D046 customer_group ON customer_group.D046_Id=customer.D024_D046_Id' . $proposalJoin;
            $select = "t.T005_Id document_id,t.T005_Data_Emissao issued_at,COALESCE(NULLIF(customer.D024_Nome_Fantasia,''),customer.D024_Nome_Empresa) customer_name,customer_group.D046_Nome customer_group_name," . ($this->proposalCustomerAvailable ? "COALESCE(NULLIF(proposal_customer.D024_Nome_Fantasia,''),proposal_customer.D024_Nome_Empresa) proposal_customer_name," : 'NULL proposal_customer_name,') . 't.T005_Flag_Status status,t.T005_Nome_Status status_name,t.T005_Valor_Total backlog_total,1 order_count';
            $order = ' ORDER BY t.T005_Data_Emissao DESC,t.T005_Id DESC';
        }
        else { $select = 't.T005_Flag_Status status,MAX(t.T005_Nome_Status) status_name,' . $totals; $group = ' GROUP BY t.T005_Flag_Status'; $order = ' ORDER BY backlog_total DESC,status ASC'; }

        $statement = $this->pdo->prepare('SELECT ' . $select . ' FROM T005 t' . $join . ' ' . $where . $group . $order . ' LIMIT :limit');
        foreach ($parameters as $name => $value) { $statement->bindValue(':' . $name, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR); }
        $statement->bindValue(':limit', min($limit, 51), PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll() ?: [];
    }

    /** @return array{0:string,1:array<string,int|string>} */
    private function where(int $companyId, string $from, string $to, ?int $customerId, ?int $sellerId, ?int $status): array
    {
        $where = 'WHERE t.T005_C004_Id=:company_id AND t.T005_Data_Emissao>=:date_from AND t.T005_Data_Emissao<=:date_to';
        $parameters = ['company_id' => $companyId, 'date_from' => $from, 'date_to' => $to];
        if ($customerId !== null) { $where .= ' AND t.T005_D024_Id=:customer_id'; $parameters['customer_id'] = $customerId; }
        if ($sellerId !== null) { $where .= ' AND t.T005_C007_Id_Vendedor_Interno=:seller_id'; $parameters['seller_id'] = $sellerId; }
        if ($status !== null) { $where .= ' AND t.T005_Flag_Status=:status'; $parameters['status'] = $status; }
        return [$where, $parameters];
    }
}
