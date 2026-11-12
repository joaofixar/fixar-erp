<?php

declare(strict_types=1);

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Schema\ProposalCustomerCapability;
use HardnessMcp\Tools\Order\Domain\OrderBacklogRepositoryInterface;
use HardnessMcp\Tools\Order\Infrastructure\PdoOrderBacklogRepository;
use HardnessMcp\Tools\Order\OrderBacklogDefinition;
use HardnessMcp\Tools\Order\OrderBacklogHandler;
use HardnessMcp\Tools\Order\OrderBacklogInput;
use HardnessMcp\Tools\Quote\Summary\Infrastructure\PdoQuoteSummaryRepository;
use HardnessMcp\Tools\Quote\Summary\QuoteSummaryDefinition;
use HardnessMcp\Tools\Quote\Summary\QuoteSummaryOutput;
use HardnessMcp\Tools\Quote\Infrastructure\PdoQuoteRepository;
use HardnessMcp\Tools\Quote\QuoteOutput;
use HardnessMcp\Tools\Sales\Infrastructure\PdoSalesSummaryRepository;
use HardnessMcp\Tools\Sales\SalesSummaryDefinition;
use HardnessMcp\Tools\Sales\SalesSummaryOutput;

final class DocumentSummaryStatement extends PDOStatement
{
    private array $rows;
    public array $bound = [];

    public function __construct(array $rows) { $this->rows = $rows; }
    public function bindValue($parameter, $value, $type = PDO::PARAM_STR) { $this->bound[(string) $parameter] = $value; return true; }
    public function execute($parameters = null) { return true; }
    public function fetchAll(int $mode = \PDO::FETCH_DEFAULT, mixed ...$args): array { return $this->rows; }
    public function fetch($mode = null, $orientation = null, $offset = null) { return $this->rows[0] ?? false; }
}

final class DocumentSummaryPdo extends PDO
{
    public array $preparedSql = [];
    private array $capabilityRows;

    public function __construct(bool $withIde)
    {
        $this->capabilityRows = $withIde ? [
            ['TABLE_NAME' => 't003a', 'COLUMN_NAME' => 'T003A_D024_Id_Cliente_Proposta'],
            ['TABLE_NAME' => 't005a', 'COLUMN_NAME' => 'T005A_D024_Id_Cliente_Proposta'],
            ['TABLE_NAME' => 't007a', 'COLUMN_NAME' => 'T007A_D024_Id_Cliente_Proposta'],
        ] : [
            ['TABLE_NAME' => 'T003A', 'COLUMN_NAME' => 'T003A_D024_Id_Cliente_Proposta'],
        ];
    }

    public function query($statement, $mode = PDO::ATTR_DEFAULT_FETCH_MODE, ...$fetchModeArgs)
    {
        return new DocumentSummaryStatement($this->capabilityRows);
    }

    public function prepare($statement, $options = [])
    {
        $this->preparedSql[] = (string) $statement;
        return new DocumentSummaryStatement([]);
    }
}

foreach ([false, true] as $withIde) {
    $pdo = new DocumentSummaryPdo($withIde);
    $capability = ProposalCustomerCapability::isAvailable($pdo);
    mcpAssert($capability === $withIde, 'proposal-customer capability must require all three IDE fields');

    (new PdoQuoteRepository($pdo, $capability))->search(1, '2026-08-01', '2026-08-31', null, null, 21);
    $quoteSearchSql = $pdo->preparedSql[count($pdo->preparedSql) - 1];
    mcpAssert(strpos($quoteSearchSql, 'D046') !== false && (strpos($quoteSearchSql, 'proposal_extension') !== false) === $withIde, 'quote search must always include customer group and must include proposal customer only on IDE bases');

    (new PdoQuoteSummaryRepository($pdo, $capability))->grouped(1, '2026-08-01', '2026-08-31', 'document', null, null, null, null, null, 21);
    (new PdoOrderBacklogRepository($pdo, $capability))->grouped(1, '2026-08-01', '2026-08-31', 'document', null, null, null, 21);
    (new PdoSalesSummaryRepository($pdo, $capability))->grouped(1, '2026-08-01', '2026-08-31', 'document', null, null, 21);

    $documentSql = array_slice($pdo->preparedSql, -3);
    foreach ($documentSql as $sql) {
        mcpAssert(strpos($sql, 'D046') !== false && strpos($sql, 'customer_group_name') !== false, 'every document report must join the general customer group');
        mcpAssert((strpos($sql, 'proposal_extension') !== false) === $withIde, 'document reports must reference IDE extension tables only when the complete capability exists');
        mcpAssert($withIde || strpos($sql, 'NULL AS proposal_customer_name') !== false || strpos($sql, 'NULL proposal_customer_name') !== false, 'non-IDE document reports must return a null proposal-customer column');
    }
}

$quoteSearchRows = QuoteOutput::search([
    ['T003_Id' => '1', 'T003_Data_Emissao' => '2026-08-02', 'status_label' => 'Pendente', 'T003_Valor_Total' => '50.00', 'D024_Id' => '7', 'D024_Nome_Fantasia' => 'Cliente A', 'D024_Nome_Empresa' => 'Cliente A Ltda', 'customer_group_name' => 'Grupo A', 'proposal_customer_name_fantasy' => 'Proposta A'],
    ['T003_Id' => '2', 'T003_Data_Emissao' => '2026-08-01', 'status_label' => 'Finalizado', 'T003_Valor_Total' => '40.00', 'D024_Id' => '8', 'D024_Nome_Fantasia' => 'Cliente B', 'D024_Nome_Empresa' => 'Cliente B Ltda', 'customer_group_name' => null],
]);
mcpAssert(($quoteSearchRows['items'][0]['customer_group_name'] ?? null) === 'Grupo A' && ($quoteSearchRows['items'][0]['proposal_customer_name'] ?? null) === 'Proposta A' && !array_key_exists('customer_group_name', $quoteSearchRows['items'][1]), 'quote search must expose filled group and proposal customer values without inventing empty values');

$quoteGroups = (new QuoteSummaryDefinition())->toToolDefinition()->getInputSchema()['properties']['group_by']['enum'];
$orderGroups = (new OrderBacklogDefinition())->toToolDefinition()->getInputSchema()['properties']['group_by']['enum'];
$salesGroups = (new SalesSummaryDefinition())->toToolDefinition()->getInputSchema()['properties']['group_by']['enum'];
foreach ([$quoteGroups, $orderGroups, $salesGroups] as $groups) {
    mcpAssert(in_array('document', $groups, true) && !in_array('proposal_customer', $groups, true), 'summary contracts must expose document and must not expose proposal_customer as a grouping');
}

$quoteRows = QuoteSummaryOutput::rows([
    ['document_id' => '10', 'issued_at' => '2026-08-02', 'customer_name' => 'Cliente A', 'customer_group_name' => 'Grupo A', 'proposal_customer_name' => 'Proposta A', 'status' => 'P', 'quoted_total' => '50.00'],
    ['document_id' => '11', 'issued_at' => '2026-08-01', 'customer_name' => 'Cliente B', 'customer_group_name' => null, 'proposal_customer_name' => null, 'status' => 'F', 'quoted_total' => '40.00'],
], 'document');
mcpAssert(($quoteRows[0]['customer_group_name'] ?? null) === 'Grupo A' && array_key_exists('customer_group_name', $quoteRows[1]) && $quoteRows[1]['customer_group_name'] === null && ($quoteRows[0]['proposal_customer_name'] ?? null) === 'Proposta A', 'quote documents must preserve filled and empty group values and the optional proposal customer');

$salesRows = SalesSummaryOutput::rows([
    ['document_id' => '20', 'invoice_number' => '1234', 'issued_at' => '2026-08-02', 'customer_name' => 'Cliente A', 'customer_group_name' => 'Grupo A', 'proposal_customer_name' => 'Proposta A', 'invoiced_total' => '70.00'],
    ['document_id' => '21', 'invoice_number' => '1235', 'issued_at' => '2026-08-01', 'customer_name' => 'Cliente B', 'customer_group_name' => null, 'proposal_customer_name' => null, 'invoiced_total' => '60.00'],
], 'document');
mcpAssert(($salesRows[0]['invoice_number'] ?? null) === '1234' && ($salesRows[0]['customer_group_name'] ?? null) === 'Grupo A' && array_key_exists('customer_group_name', $salesRows[1]) && $salesRows[1]['customer_group_name'] === null, 'sales documents must expose NF number and preserve filled and empty group values');

$orderRepository = new class implements OrderBacklogRepositoryInterface {
    public function summary(int $companyId, string $from, string $to, ?int $customerId, ?int $sellerId, ?int $status): array { return ['backlog_total' => '90.00', 'order_count' => 2]; }
    public function grouped(int $companyId, string $from, string $to, string $groupBy, ?int $customerId, ?int $sellerId, ?int $status, int $limit): array { return [
        ['document_id' => '30', 'issued_at' => '2026-08-02', 'customer_name' => 'Cliente A', 'customer_group_name' => 'Grupo A', 'proposal_customer_name' => 'Proposta A', 'status' => '2', 'status_name' => 'Liberado', 'backlog_total' => '50.00'],
        ['document_id' => '31', 'issued_at' => '2026-08-01', 'customer_name' => 'Cliente B', 'customer_group_name' => null, 'proposal_customer_name' => null, 'status' => '1', 'status_name' => 'Pendente', 'backlog_total' => '40.00'],
    ]; }
};
$orderResult = (new OrderBacklogHandler($orderRepository))->handle(OrderBacklogInput::fromArguments(['date_from' => '2026-08-01', 'date_to' => '2026-08-31', 'group_by' => 'document']), new ExecutionContext(1, 1, 1, 1, ['order:report:read']));
mcpAssert(($orderResult['rows'][0]['status_name'] ?? null) === 'Liberado' && ($orderResult['rows'][0]['customer_group_name'] ?? null) === 'Grupo A' && array_key_exists('customer_group_name', $orderResult['rows'][1]) && $orderResult['rows'][1]['customer_group_name'] === null, 'order documents must expose status and preserve filled and empty group values');
