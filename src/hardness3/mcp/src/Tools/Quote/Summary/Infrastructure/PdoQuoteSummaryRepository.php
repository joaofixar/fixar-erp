<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Quote\Summary\Infrastructure;

use HardnessMcp\Tools\Quote\Summary\Domain\QuoteSummaryRepositoryInterface;
use PDO;

final class PdoQuoteSummaryRepository implements QuoteSummaryRepositoryInterface
{
    private PDO $pdo; private bool $proposalCustomerAvailable; public function __construct(PDO $pdo,bool $proposalCustomerAvailable=false){$this->pdo=$pdo;$this->proposalCustomerAvailable=$proposalCustomerAvailable;}
    /** @return array{quoted_total:string,quote_count:int} */
    public function summary(int $companyId,string $dateFrom,string $dateTo,?int $customerId,?int $sellerInternalId,?int $supervisorId,?string $status,?string $origin): array
    {
        [$where,$parameters]=$this->where($companyId,$dateFrom,$dateTo,$customerId,$sellerInternalId,$supervisorId,$status,$origin); $statement=$this->pdo->prepare('SELECT COALESCE(SUM(t003.T003_Valor_Total),0) AS quoted_total,COUNT(*) AS quote_count FROM T003 t003 '.$where); $statement->execute($parameters); $row=$statement->fetch(); return ['quoted_total'=>(string)($row['quoted_total']??'0'),'quote_count'=>(int)($row['quote_count']??0)];
    }
    /** @return array<int,array<string,mixed>> */
    public function grouped(int $companyId,string $dateFrom,string $dateTo,string $groupBy,?int $customerId,?int $sellerInternalId,?int $supervisorId,?string $status,?string $origin,int $limit): array
    {
        [$where,$parameters]=$this->where($companyId,$dateFrom,$dateTo,$customerId,$sellerInternalId,$supervisorId,$status,$origin); $select='COUNT(*) AS quote_count,COALESCE(SUM(t003.T003_Valor_Total),0) AS quoted_total'; $join='';$group='';$order='';
        if($groupBy==='day'){$select='DATE(t003.T003_Data_Emissao) AS date,'.$select;$group=' GROUP BY DATE(t003.T003_Data_Emissao)';$order=' ORDER BY date ASC';}
        if($groupBy==='week'){$select='DATE_SUB(DATE(t003.T003_Data_Emissao), INTERVAL WEEKDAY(t003.T003_Data_Emissao) DAY) AS week_start,'.$select;$group=' GROUP BY DATE_SUB(DATE(t003.T003_Data_Emissao), INTERVAL WEEKDAY(t003.T003_Data_Emissao) DAY)';$order=' ORDER BY week_start ASC';}
        if($groupBy==='month'){$select="DATE_FORMAT(t003.T003_Data_Emissao, '%Y-%m-01') AS month,".$select;$group=" GROUP BY DATE_FORMAT(t003.T003_Data_Emissao, '%Y-%m-01')";$order=' ORDER BY month ASC';}
        if($groupBy==='customer'){$join=' LEFT JOIN D024 customer ON customer.D024_Id=t003.T003_D024_Id';$select="t003.T003_D024_Id AS customer_id,COALESCE(NULLIF(customer.D024_Nome_Fantasia,''),customer.D024_Nome_Empresa) AS customer_name,".$select;$group=' GROUP BY t003.T003_D024_Id,customer.D024_Nome_Fantasia,customer.D024_Nome_Empresa';$order=' ORDER BY quoted_total DESC,customer_id ASC';}
        if($groupBy==='document'){$proposalJoin=$this->proposalCustomerAvailable?' LEFT JOIN T003A proposal_extension ON proposal_extension.T003A_T003_Id=t003.T003_Id LEFT JOIN D024 proposal_customer ON proposal_customer.D024_Id=proposal_extension.T003A_D024_Id_Cliente_Proposta':'';$join=' LEFT JOIN D024 customer ON customer.D024_Id=t003.T003_D024_Id LEFT JOIN D046 customer_group ON customer_group.D046_Id=customer.D024_D046_Id'.$proposalJoin;$select="t003.T003_Id AS document_id,t003.T003_Data_Emissao AS issued_at,COALESCE(NULLIF(customer.D024_Nome_Fantasia,''),customer.D024_Nome_Empresa) AS customer_name,customer_group.D046_Nome AS customer_group_name,".($this->proposalCustomerAvailable?"COALESCE(NULLIF(proposal_customer.D024_Nome_Fantasia,''),proposal_customer.D024_Nome_Empresa) AS proposal_customer_name,":"NULL AS proposal_customer_name,").'t003.T003_Flag_Status_Orcamento AS status,t003.T003_Valor_Total AS quoted_total,1 AS quote_count';$order=' ORDER BY t003.T003_Data_Emissao DESC,t003.T003_Id DESC';}
        if($groupBy==='seller_internal'){$join=' LEFT JOIN C007 seller ON seller.C007_Id=t003.T003_C007_Id_Vendedor_Interno LEFT JOIN C007 supervisor ON supervisor.C007_Id=seller.C007_C007_Id';$select='t003.T003_C007_Id_Vendedor_Interno AS seller_internal_id,seller.C007_Nome AS seller_internal_name,supervisor.C007_Id AS supervisor_id,supervisor.C007_Nome AS supervisor_name,'.$select;$group=' GROUP BY t003.T003_C007_Id_Vendedor_Interno,seller.C007_Nome,supervisor.C007_Id,supervisor.C007_Nome';$order=' ORDER BY quoted_total DESC,seller_internal_id ASC';}
        if($groupBy==='supervisor'){$join=' LEFT JOIN C007 seller ON seller.C007_Id=t003.T003_C007_Id_Vendedor_Interno LEFT JOIN C007 supervisor ON supervisor.C007_Id=seller.C007_C007_Id';$select='supervisor.C007_Id AS supervisor_id,supervisor.C007_Nome AS supervisor_name,'.$select;$group=' GROUP BY supervisor.C007_Id,supervisor.C007_Nome';$order=' ORDER BY quoted_total DESC,supervisor_id ASC';}
        if($groupBy==='status'){$select='t003.T003_Flag_Status_Orcamento AS status,'.$select;$group=' GROUP BY t003.T003_Flag_Status_Orcamento';$order=' ORDER BY quoted_total DESC,status ASC';}
        $statement=$this->pdo->prepare('SELECT '.$select.' FROM T003 t003'.$join.' '.$where.$group.$order.' LIMIT :limit'); foreach($parameters as $name=>$value)$statement->bindValue(':'.$name,$value,is_int($value)?PDO::PARAM_INT:PDO::PARAM_STR); $statement->bindValue(':limit',min($limit,51),PDO::PARAM_INT);$statement->execute();$rows=$statement->fetchAll();return is_array($rows)?$rows:[];
    }
    /** @return array{0:string,1:array<string,int|string>} */
    private function where(int $companyId,string $dateFrom,string $dateTo,?int $customerId,?int $sellerInternalId,?int $supervisorId,?string $status,?string $origin): array
    {
        $where='WHERE t003.T003_C004_Id=:company_id AND t003.T003_Data_Emissao>=:date_from AND t003.T003_Data_Emissao<=:date_to';$parameters=['company_id'=>$companyId,'date_from'=>$dateFrom,'date_to'=>$dateTo];if($customerId!==null){$where.=' AND t003.T003_D024_Id=:customer_id';$parameters['customer_id']=$customerId;}if($sellerInternalId!==null){$where.=' AND t003.T003_C007_Id_Vendedor_Interno=:seller_internal_id';$parameters['seller_internal_id']=$sellerInternalId;}if($supervisorId!==null){$where.=' AND EXISTS (SELECT 1 FROM C007 seller_filter WHERE seller_filter.C007_Id=t003.T003_C007_Id_Vendedor_Interno AND seller_filter.C007_C007_Id=:supervisor_id)';$parameters['supervisor_id']=$supervisorId;}if($status!==null){$where.=' AND t003.T003_Flag_Status_Orcamento=:status';$parameters['status']=$status;}if($origin==='assistant_ia'){$where.=" AND EXISTS (SELECT 1 FROM T003A t003a INNER JOIN D201 d201 ON d201.D201_Id=t003a.T003A_D201_Id WHERE t003a.T003A_T003_Id=t003.T003_Id AND d201.D201_Chave_Sistema='MCP_IA')";}return [$where,$parameters];
    }
}
