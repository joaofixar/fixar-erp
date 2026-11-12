<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\ItemReport\Infrastructure;

use HardnessMcp\Tools\ItemReport\Domain\ItemReportRepositoryInterface;
use PDO;
use RuntimeException;

final class PdoItemReportRepository implements ItemReportRepositoryInterface
{
    private PDO $pdo; private string $source;
    public function __construct(PDO $pdo, string $source) { $this->pdo = $pdo; $this->source = $source; }

    public function summary(int $companyId, string $from, string $to, ?int $customerId, ?int $sellerId, ?string $productCode): array
    {
        [$fromSql, $where, $parameters] = $this->queryParts($companyId, $from, $to, $customerId, $sellerId, $productCode);
        $statement = $this->pdo->prepare('SELECT COALESCE(SUM(i.quantity),0) AS quantity, COALESCE(SUM(i.net_total),0) AS net_total, COALESCE(SUM(i.cost_total),0) AS cost_total, COUNT(DISTINCT i.document_id) AS document_count FROM ' . $fromSql . ' ' . $where);
        $statement->execute($parameters);
        return $statement->fetch() ?: [];
    }

    public function products(int $companyId, string $from, string $to, ?int $customerId, ?int $sellerId, ?string $productCode, string $groupBy, int $limit): array
    {
        [$fromSql, $where, $parameters] = $this->queryParts($companyId, $from, $to, $customerId, $sellerId, $productCode);
        $customer = $groupBy === 'product_customer' ? ",h.customer_id AS customer_id,COALESCE(NULLIF(customer.D024_Nome_Fantasia, ''),customer.D024_Nome_Empresa) AS customer_name," : '';
        $join = $groupBy === 'product_customer' ? " LEFT JOIN D024 customer ON customer.D024_Id=h.customer_id" : '';
        $group = $groupBy === 'product_customer' ? "i.product_code,i.description,i.unit,h.customer_id,customer.D024_Nome_Fantasia,customer.D024_Nome_Empresa" : "i.product_code,i.description,i.unit";
        $sql = "SELECT " . $customer . "i.product_code,i.description,i.unit,COALESCE(SUM(i.quantity),0) AS quantity,COALESCE(SUM(i.net_total),0) AS net_total,COALESCE(SUM(i.cost_total),0) AS cost_total,COUNT(DISTINCT i.document_id) AS document_count FROM " . $fromSql . $join . " " . $where . " GROUP BY " . $group . " ORDER BY net_total DESC,quantity DESC,i.product_code ASC LIMIT :limit";
        $statement = $this->pdo->prepare($sql); foreach ($parameters as $name => $value) $statement->bindValue(':' . $name, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        $statement->bindValue(':limit', min($limit, 51), PDO::PARAM_INT); $statement->execute(); return $statement->fetchAll() ?: [];
    }

    /** @return array{0:string,1:string,2:array<string,int|string>} */
    private function queryParts(int $companyId, string $from, string $to, ?int $customerId, ?int $sellerId, ?string $productCode): array
    {
        $parts = $this->sourceParts();
        $where = 'WHERE h.company_id=:company_id AND h.issued_at>=:date_from AND h.issued_at<=:date_to';
        $parameters = ['company_id' => $companyId, 'date_from' => $from, 'date_to' => $to];
        if ($customerId !== null) { $where .= ' AND h.customer_id=:customer_id'; $parameters['customer_id'] = $customerId; }
        if ($sellerId !== null) { $where .= ' AND h.seller_id=:seller_id'; $parameters['seller_id'] = $sellerId; }
        if ($productCode !== null && $productCode !== '') { $where .= ' AND i.product_code=:product_code'; $parameters['product_code'] = $productCode; }
        return [$parts, $where, $parameters];
    }

    private function sourceParts(): string
    {
        if ($this->source === 'quote') return "(SELECT t003.T003_Id AS document_id,t003.T003_C004_Id AS company_id,t003.T003_Data_Emissao AS issued_at,t003.T003_D024_Id AS customer_id,t003.T003_C007_Id_Vendedor_Interno AS seller_id,t004.T004_Codigo_Produto AS product_code,t004.T004_Descricao_Produto AS description,d037.D037_Unidade AS unit,t004.T004_Quantidade AS quantity,t004.T004_Valor_Total_Preco AS net_total,t004.T004_Valor_Total_Custo AS cost_total FROM T004 t004 INNER JOIN T003 t003 ON t003.T003_Id=t004.T004_T003_Id LEFT JOIN D037 d037 ON d037.D037_Id=t004.T004_D037_Id WHERE IFNULL(t004.T004_Flag_Cancelado,'N')<>'S' AND IFNULL(t004.T004_Flag_Excluir,2)<>1) i INNER JOIN (SELECT T003_Id AS document_id,T003_C004_Id AS company_id,T003_Data_Emissao AS issued_at,T003_D024_Id AS customer_id,T003_C007_Id_Vendedor_Interno AS seller_id FROM T003) h ON h.document_id=i.document_id";
        if ($this->source === 'order') return "(SELECT t005.T005_Id AS document_id,t005.T005_C004_Id AS company_id,t005.T005_Data_Emissao AS issued_at,t005.T005_D024_Id AS customer_id,t005.T005_C007_Id_Vendedor_Interno AS seller_id,t006.T006_Codigo_Produto AS product_code,t006.T006_Descricao_Produto AS description,d037.D037_Unidade AS unit,t006.T006_Quantidade AS quantity,t006.T006_Valor_Total_Preco AS net_total,t006.T006_Valor_Total_Custo AS cost_total FROM T006 t006 INNER JOIN T005 t005 ON t005.T005_Id=t006.T006_T005_Id LEFT JOIN D037 d037 ON d037.D037_Id=t006.T006_D037_Id WHERE IFNULL(t006.T006_Flag_Cancelado,'N')<>'S' AND IFNULL(t006.T006_Flag_Excluir,2)<>1) i INNER JOIN (SELECT T005_Id AS document_id,T005_C004_Id AS company_id,T005_Data_Emissao AS issued_at,T005_D024_Id AS customer_id,T005_C007_Id_Vendedor_Interno AS seller_id FROM T005) h ON h.document_id=i.document_id";
        if ($this->source === 'sales') return "(SELECT t007.T007_Id AS document_id,t007.T007_C004_Id AS company_id,t007.T007_Data_Emissao AS issued_at,t007.T007_D024_Id AS customer_id,t007.T007_C007_Id_Vendedor_Interno AS seller_id,t008.T008_Codigo_Produto AS product_code,t008.T008_Descricao_Produto AS description,t008.T008_Unidade AS unit,t008.T008_Quantidade AS quantity,t008.T008_Valor_Total_Preco AS net_total,t008.T008_Valor_Total_Custo AS cost_total FROM T008 t008 INNER JOIN T007 t007 ON t007.T007_Id=t008.T008_T007_Id WHERE IFNULL(t008.T008_Flag_Excluir,'N')<>'S' AND t007.T007_Flag_Entrada_Saida='S' AND t007.T007_Flag_Cancelada='N' AND t007.T007_Numero_Protocolo_Nfe IS NOT NULL AND TRIM(t007.T007_Numero_Protocolo_Nfe)<>'') i INNER JOIN (SELECT T007_Id AS document_id,T007_C004_Id AS company_id,T007_Data_Emissao AS issued_at,T007_D024_Id AS customer_id,T007_C007_Id_Vendedor_Interno AS seller_id FROM T007) h ON h.document_id=i.document_id";
        throw new RuntimeException('Unknown item report source.');
    }
}
