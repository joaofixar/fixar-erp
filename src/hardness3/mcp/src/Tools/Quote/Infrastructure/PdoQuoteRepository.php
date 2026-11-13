<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Quote\Infrastructure;

use HardnessMcp\Tools\Quote\Domain\QuoteRepositoryInterface;
use PDO;

final class PdoQuoteRepository implements QuoteRepositoryInterface
{
    private PDO $pdo;
    private bool $proposalCustomerAvailable;
    public function __construct(PDO $pdo, bool $proposalCustomerAvailable = false) { $this->pdo = $pdo; $this->proposalCustomerAvailable = $proposalCustomerAvailable; }

    /** @return array<int,array<string,mixed>> */
    public function search(int $companyId, string $issuedFrom, string $issuedTo, ?int $customerId, ?string $origin, int $limit): array
    {
        $proposalSelect = $this->proposalCustomerAvailable ? ',proposal_customer.D024_Id AS proposal_customer_id,proposal_customer.D024_Nome_Fantasia AS proposal_customer_name_fantasy,proposal_customer.D024_Nome_Empresa AS proposal_customer_name' : '';
        $proposalJoin = $this->proposalCustomerAvailable ? ' LEFT JOIN T003A proposal_extension ON proposal_extension.T003A_T003_Id=t003.T003_Id LEFT JOIN D024 proposal_customer ON proposal_customer.D024_Id=proposal_extension.T003A_D024_Id_Cliente_Proposta' : '';
        $sql = "SELECT t003.T003_Id,t003.T003_Data_Emissao,t003.T003_Flag_Status_Orcamento,T003_Status_Orcamento(t003.T003_Flag_Status_Orcamento) AS status_label,t003.T003_Valor_Total,d024.D024_Id,d024.D024_Nome_Fantasia,d024.D024_Nome_Empresa,d046.D046_Nome AS customer_group_name{$proposalSelect}\n"
            . "FROM T003 t003 LEFT JOIN D024 d024 ON d024.D024_Id=t003.T003_D024_Id LEFT JOIN D046 d046 ON d046.D046_Id=d024.D024_D046_Id{$proposalJoin}\n"
            . "WHERE t003.T003_C004_Id=:company_id AND t003.T003_Data_Emissao>=:issued_from AND t003.T003_Data_Emissao<=:issued_to";
        if ($customerId !== null) { $sql .= ' AND t003.T003_D024_Id=:customer_id'; }
        if ($origin === 'assistant_ia') { $sql .= " AND EXISTS (SELECT 1 FROM T003A t003a INNER JOIN D201 d201 ON d201.D201_Id=t003a.T003A_D201_Id WHERE t003a.T003A_T003_Id=t003.T003_Id AND d201.D201_Chave_Sistema='MCP_IA')"; }
        $sql .= ' ORDER BY t003.T003_Data_Emissao DESC,t003.T003_Id DESC LIMIT :limit';
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':company_id', $companyId, PDO::PARAM_INT);
        $statement->bindValue(':issued_from', $issuedFrom, PDO::PARAM_STR);
        $statement->bindValue(':issued_to', $issuedTo, PDO::PARAM_STR);
        if ($customerId !== null) { $statement->bindValue(':customer_id', $customerId, PDO::PARAM_INT); }
        $statement->bindValue(':limit', min($limit, 51), PDO::PARAM_INT);
        $statement->execute();
        $rows = $statement->fetchAll();
        return is_array($rows) ? $rows : [];
    }

    /** @return array{header:array<string,mixed>,items:array<int,array<string,mixed>>}|null */
    public function findById(int $companyId, int $quoteId): ?array
    {
        $header = $this->pdo->prepare("SELECT t003.T003_Id,t003.T003_Data_Emissao,t003.T003_Flag_Status_Orcamento,T003_Status_Orcamento(t003.T003_Flag_Status_Orcamento) AS status_label,t003.T003_Valor_Total,d024.D024_Id,d024.D024_Nome_Fantasia,d024.D024_Nome_Empresa FROM T003 t003 LEFT JOIN D024 d024 ON d024.D024_Id=t003.T003_D024_Id WHERE t003.T003_Id=:quote_id AND t003.T003_C004_Id=:company_id LIMIT 1");
        $header->execute(['quote_id' => $quoteId, 'company_id' => $companyId]);
        $row = $header->fetch();
        if (!is_array($row)) { return null; }

        // Native PDO prepares do not allow reusing a named placeholder.
        // Keep the company constraint in both joins, with distinct bindings.
        $items = $this->pdo->prepare("SELECT t004.T004_Id,t004.T004_Item,t004.T004_Descricao_Produto,t004.T004_Codigo_Produto,t004.T004_Codigo_Local,t004.T004_Quantidade,t004.T004_Valor_Preco_Unitario,t004.T004_Valor_Total_Preco,d001.D001_Id,d001.D001_Codigo_Produto,d037.D037_Unidade FROM T004 t004 LEFT JOIN D009 d009 ON d009.D009_Id=t004.T004_D009_Id LEFT JOIN D049 d049 ON d049.D049_Id=d009.D009_D049_Id LEFT JOIN D001 d001 ON d001.D001_Id=d049.D049_D001_Id LEFT JOIN D037 d037 ON d037.D037_Id=t004.T004_D037_Id WHERE t004.T004_T003_Id=:quote_id AND t004.T004_C004_Id=:company_id AND IFNULL(t004.T004_Flag_Cancelado,'N')<>'S' AND IFNULL(t004.T004_Flag_Excluir,2)<>1 ORDER BY t004.T004_Item,t004.T004_Id");
        $items->execute(['quote_id' => $quoteId, 'company_id' => $companyId]);
        $rows = $items->fetchAll();
        return ['header' => $row, 'items' => is_array($rows) ? $rows : []];
    }
}
