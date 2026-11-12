<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Sales;

use InvalidArgumentException;
use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\Presentation\ToolResultPresenter;
use HardnessMcp\Tools\ToolDefinition;
use HardnessMcp\Tools\ToolExecutionResult;
use HardnessMcp\Tools\ToolInterface;

final class SalesSummaryTool implements ToolInterface
{
    private SalesSummaryHandler $handler;
    private ToolResultPresenter $presenter;
    public function __construct(SalesSummaryHandler $handler, ?ToolResultPresenter $presenter = null) { $this->handler = $handler; $this->presenter = $presenter ?? new ToolResultPresenter(); }
    public function getDefinition(): ToolDefinition { return (new SalesSummaryDefinition())->toToolDefinition(); }

    /** @param array<string,mixed> $arguments */
    public function execute(array $arguments, ExecutionContext $context): ToolExecutionResult
    {
        try {
            $result = $this->handler->handle(SalesSummaryInput::fromArguments($arguments), $context);
        } catch (InvalidArgumentException $exception) {
            return $this->presenter->functionalError('sales_summary_invalid_period', $exception->getMessage(), 'tools_call_sales_summary_invalid_period');
        } catch (\Throwable $exception) {
            return $this->presenter->functionalError('sales_summary_unavailable', 'Não foi possível consultar o resumo de vendas agora. Tente novamente.', 'tools_call_sales_summary_unavailable');
        }

        return $this->presenter->report($result, 'Resumo de vendas faturadas por NF-e', [
            ['path' => 'summary.invoiced_total', 'label' => 'Total faturado', 'format' => 'money'],
            ['path' => 'summary.invoice_count', 'label' => 'NF-e'],
        ], 'rows', $this->rowFields((string) $result['group_by']));
    }

    /** @return array<int,array<string,mixed>> */
    private function rowFields(string $groupBy): array
    {
        $fields = [
            'day' => [['path' => 'date', 'label' => 'Data', 'format' => 'date']],
            'week' => [['path' => 'week_start', 'label' => 'Semana de', 'format' => 'date']],
            'month' => [['path' => 'month', 'label' => 'Mês', 'format' => 'date']],
            'customer' => [['path' => 'customer_id', 'label' => 'Cliente'], ['path' => 'customer_name', 'label' => 'Nome']],
            'seller_internal' => [['path' => 'seller_internal_id', 'label' => 'Vendedor interno'], ['path' => 'seller_internal_name', 'label' => 'Nome']],
            'document' => [['path' => 'invoice_number', 'label' => 'NF'], ['path' => 'document_id', 'label' => 'ID'], ['path' => 'issued_at', 'label' => 'Emissão', 'format' => 'date'], ['path' => 'customer_name', 'label' => 'Cliente'], ['path' => 'customer_group_name', 'label' => 'Grupo'], ['path' => 'proposal_customer_name', 'label' => 'Cliente da proposta']],
        ];
        return array_merge($fields[$groupBy] ?? [], [
            ['path' => 'invoiced_total', 'label' => 'Faturado', 'format' => 'money'],
            ['path' => 'invoice_count', 'label' => 'NF-e'],
        ]);
    }
}
