<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\ItemReport;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\Presentation\ToolResultPresenter;
use HardnessMcp\Tools\ToolExecutionResult;
use HardnessMcp\Tools\ToolInterface;
use InvalidArgumentException;

final class ItemReportTool implements ToolInterface
{
    private ToolResultPresenter $presenter;
    private ItemReportHandler $handler; private string $source; private string $scope;
    public function __construct(ItemReportHandler $handler, string $source, string $scope, ?ToolResultPresenter $presenter = null) { $this->handler = $handler; $this->source = $source; $this->scope = $scope; $this->presenter = $presenter ?? new ToolResultPresenter(); }
    public function getDefinition(): \HardnessMcp\Tools\ToolDefinition { return (new ItemReportDefinition($this->source, $this->scope))->toToolDefinition(); }
    public function execute(array $arguments, ExecutionContext $context): ToolExecutionResult
    {
        try { $result = $this->handler->handle(ItemReportInput::fromArguments($arguments), $context); }
        catch (InvalidArgumentException $exception) { return $this->presenter->functionalError($this->source . '_item_summary_invalid_period', $exception->getMessage(), 'tools_call_' . $this->source . '_item_summary_invalid_period'); }
        catch (\Throwable $exception) { return $this->presenter->functionalError($this->source . '_item_summary_unavailable', 'Não foi possível consultar o relatório de itens agora. Tente novamente.', 'tools_call_' . $this->source . '_item_summary_unavailable'); }
        $displayLimit = isset($arguments['limit']) ? min((int) $arguments['limit'], 20) : 10;
        $presenter = $displayLimit > 10 ? new ToolResultPresenter($displayLimit, 12000) : $this->presenter;
        return $presenter->report($result, 'Resumo de itens', [['path' => 'summary.quantity', 'label' => 'Quantidade', 'format' => 'quantity'], ['path' => 'summary.net_total', 'label' => 'Receita líquida', 'format' => 'money'], ['path' => 'summary.cost_total', 'label' => 'Custo', 'format' => 'money'], ['path' => 'summary.gross_profit', 'label' => 'Lucro bruto', 'format' => 'money'], ['path' => 'summary.gross_margin_percent', 'label' => 'Margem bruta (%)'], ['path' => 'summary.document_count', 'label' => 'Documentos']], 'rows', [['path' => 'customer_id', 'label' => 'Cliente'], ['path' => 'customer_name', 'label' => 'Nome do cliente'], ['path' => 'product_code', 'label' => 'Produto'], ['path' => 'description', 'label' => 'Descrição'], ['path' => 'unit', 'label' => 'Unidade'], ['path' => 'quantity', 'label' => 'Quantidade', 'format' => 'quantity'], ['path' => 'average_net_unit_price', 'label' => 'Preço líquido médio', 'format' => 'money'], ['path' => 'net_total', 'label' => 'Receita líquida', 'format' => 'money'], ['path' => 'cost_total', 'label' => 'Custo', 'format' => 'money'], ['path' => 'gross_profit', 'label' => 'Lucro bruto', 'format' => 'money'], ['path' => 'gross_margin_percent', 'label' => 'Margem (%)'], ['path' => 'document_count', 'label' => 'Documentos']], 'tools_call_' . $this->source . '_item_summary');
    }
}
