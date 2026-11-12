<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Conversion\Infrastructure;

use HardnessMcp\Tools\Conversion\Domain\ProductConversionRepositoryInterface;
use PDO;

final class PdoProductConversionRepository implements ProductConversionRepositoryInterface
{
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }
    public function summary(int $companyId, string $from, string $to, ?int $customerId, ?int $sellerId, ?string $productCode): array
    {
        [$fromSql, $where, $parameters] = $this->queryParts($companyId, $from, $to, $customerId, $sellerId, $productCode);
        $statement = $this->pdo->prepare('SELECT COALESCE(SUM(x.quoted_quantity),0) quoted_quantity,COALESCE(SUM(x.ordered_quantity),0) ordered_quantity,COALESCE(SUM(x.invoiced_quantity),0) invoiced_quantity,COALESCE(SUM(x.invoiced_net_total),0) invoiced_net_total,COALESCE(SUM(x.invoiced_cost_total),0) invoiced_cost_total FROM ' . $fromSql . ' ' . $where);
        $statement->execute($parameters); return $statement->fetch() ?: [];
    }
    public function products(int $companyId, string $from, string $to, ?int $customerId, ?int $sellerId, ?string $productCode, string $groupBy, int $limit): array
    {
        [$fromSql, $where, $parameters] = $this->queryParts($companyId, $from, $to, $customerId, $sellerId, $productCode);
        $customer = $groupBy === 'product_customer' ? ",x.customer_id AS customer_id,COALESCE(NULLIF(customer.D024_Nome_Fantasia, ''),customer.D024_Nome_Empresa) AS customer_name," : '';
        $join = $groupBy === 'product_customer' ? " LEFT JOIN D024 customer ON customer.D024_Id=x.customer_id" : '';
        $group = $groupBy === 'product_customer' ? "x.product_code,x.description,x.unit,x.customer_id,customer.D024_Nome_Fantasia,customer.D024_Nome_Empresa" : "x.product_code,x.description,x.unit";
        $sql = "SELECT " . $customer . "x.product_code,x.description,x.unit,COALESCE(SUM(x.quoted_quantity),0) quoted_quantity,COALESCE(SUM(x.ordered_quantity),0) ordered_quantity,COALESCE(SUM(x.invoiced_quantity),0) invoiced_quantity,COALESCE(SUM(x.invoiced_net_total),0) invoiced_net_total,COALESCE(SUM(x.invoiced_cost_total),0) invoiced_cost_total FROM " . $fromSql . $join . " " . $where . " GROUP BY " . $group . " ORDER BY invoiced_net_total DESC,quoted_quantity DESC,x.product_code ASC LIMIT :limit";
        $statement = $this->pdo->prepare($sql); foreach ($parameters as $name => $value) $statement->bindValue(':' . $name, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR); $statement->bindValue(':limit', min($limit, 51), PDO::PARAM_INT); $statement->execute(); return $statement->fetchAll() ?: [];
    }
    /** @return array{0:string,1:string,2:array<string,int|string>} */
    private function queryParts(int $companyId, string $from, string $to, ?int $customerId, ?int $sellerId, ?string $productCode): array
    {
        $where = 'WHERE x.company_id=:company_id AND x.issued_at>=:date_from AND x.issued_at<=:date_to'; $parameters = ['company_id' => $companyId, 'date_from' => $from, 'date_to' => $to];
        if ($customerId !== null) { $where .= ' AND x.customer_id=:customer_id'; $parameters['customer_id'] = $customerId; }
        if ($sellerId !== null) { $where .= ' AND x.seller_id=:seller_id'; $parameters['seller_id'] = $sellerId; }
        if ($productCode !== null && $productCode !== '') { $where .= ' AND x.product_code=:product_code'; $parameters['product_code'] = $productCode; }
        $fromSql = "(SELECT t003.T003_C004_Id company_id,t003.T003_Data_Emissao issued_at,t003.T003_D024_Id customer_id,t003.T003_C007_Id_Vendedor_Interno seller_id,t004.T004_Codigo_Produto product_code,t004.T004_Descricao_Produto description,d037.D037_Unidade unit,t004.T004_Quantidade quoted_quantity,COALESCE(o.ordered_quantity,0) ordered_quantity,COALESCE(n.invoiced_quantity,0) invoiced_quantity,COALESCE(n.invoiced_net_total,0) invoiced_net_total,COALESCE(n.invoiced_cost_total,0) invoiced_cost_total FROM T004 t004 INNER JOIN T003 t003 ON t003.T003_Id=t004.T004_T003_Id LEFT JOIN D037 d037 ON d037.D037_Id=t004.T004_D037_Id LEFT JOIN (SELECT T006_T004_Id quote_item_id,SUM(T006_Quantidade) ordered_quantity FROM T006 WHERE IFNULL(T006_Flag_Cancelado,'N')<>'S' AND IFNULL(T006_Flag_Excluir,2)<>1 GROUP BY T006_T004_Id) o ON o.quote_item_id=t004.T004_Id LEFT JOIN (SELECT t006.T006_T004_Id quote_item_id,SUM(t008.T008_Quantidade) invoiced_quantity,SUM(t008.T008_Valor_Total_Preco) invoiced_net_total,SUM(t008.T008_Valor_Total_Custo) invoiced_cost_total FROM T008 t008 INNER JOIN T006 t006 ON t006.T006_Id=t008.T008_T006_Id INNER JOIN T007 t007 ON t007.T007_Id=t008.T008_T007_Id WHERE IFNULL(t006.T006_Flag_Cancelado,'N')<>'S' AND IFNULL(t006.T006_Flag_Excluir,2)<>1 AND IFNULL(t008.T008_Flag_Excluir,'N')<>'S' AND t007.T007_Flag_Entrada_Saida='S' AND t007.T007_Flag_Cancelada='N' AND t007.T007_Numero_Protocolo_Nfe IS NOT NULL AND TRIM(t007.T007_Numero_Protocolo_Nfe)<>'' GROUP BY t006.T006_T004_Id) n ON n.quote_item_id=t004.T004_Id WHERE IFNULL(t004.T004_Flag_Cancelado,'N')<>'S' AND IFNULL(t004.T004_Flag_Excluir,2)<>1) x";
        return [$fromSql, $where, $parameters];
    }
}
