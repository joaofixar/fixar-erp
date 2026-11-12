<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Conversion;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\ItemReport\ItemReportInput;
use HardnessMcp\Tools\Presentation\ToolResultPresenter;
use HardnessMcp\Tools\ToolDefinition;
use HardnessMcp\Tools\ToolExecutionResult;
use HardnessMcp\Tools\ToolInterface;
use InvalidArgumentException;

final class ProductConversionTool implements ToolInterface
{
    private ProductConversionHandler $handler; private ToolResultPresenter $presenter;
    public function __construct(ProductConversionHandler $handler, ?ToolResultPresenter $presenter = null) { $this->handler = $handler; $this->presenter = $presenter ?? new ToolResultPresenter(); }
    public function getDefinition(): ToolDefinition { return (new ProductConversionDefinition())->toToolDefinition(); }
    public function execute(array $arguments, ExecutionContext $context): ToolExecutionResult
    {
        try { $result = $this->handler->handle(ItemReportInput::fromArguments($arguments), $context); }
        catch (InvalidArgumentException $exception) { return $this->presenter->functionalError('product_conversion_summary_invalid_period', $exception->getMessage(), 'tools_call_product_conversion_summary_invalid_period'); }
        catch (\Throwable $exception) { return $this->presenter->functionalError('product_conversion_summary_unavailable', 'Não foi possível consultar o funil de produtos agora. Tente novamente.', 'tools_call_product_conversion_summary_unavailable'); }
        $summary = [['path' => 'summary.quoted_quantity', 'label' => 'Orçado', 'format' => 'quantity'], ['path' => 'summary.ordered_quantity', 'label' => 'Pedido', 'format' => 'quantity'], ['path' => 'summary.invoiced_quantity', 'label' => 'Faturado', 'format' => 'quantity'], ['path' => 'summary.quote_to_order_percent', 'label' => 'Conversão orçamento/pedido (%)'], ['path' => 'summary.quote_to_invoice_percent', 'label' => 'Conversão orçamento/NF (%)'], ['path' => 'summary.invoiced_net_total', 'label' => 'Receita faturada', 'format' => 'money'], ['path' => 'summary.invoiced_gross_profit', 'label' => 'Lucro bruto', 'format' => 'money']];
        $rows = [['path' => 'customer_id', 'label' => 'Cliente'], ['path' => 'customer_name', 'label' => 'Nome do cliente'], ['path' => 'product_code', 'label' => 'Produto'], ['path' => 'description', 'label' => 'Descrição'], ['path' => 'unit', 'label' => 'Unidade'], ['path' => 'quoted_quantity', 'label' => 'Orçado', 'format' => 'quantity'], ['path' => 'ordered_quantity', 'label' => 'Pedido', 'format' => 'quantity'], ['path' => 'invoiced_quantity', 'label' => 'Faturado', 'format' => 'quantity'], ['path' => 'quote_to_order_percent', 'label' => 'Conv. orçamento/pedido (%)'], ['path' => 'order_to_invoice_percent', 'label' => 'Conv. pedido/NF (%)'], ['path' => 'quote_to_invoice_percent', 'label' => 'Conv. orçamento/NF (%)'], ['path' => 'pending_order_quantity', 'label' => 'Pendente pedido', 'format' => 'quantity'], ['path' => 'pending_invoice_quantity', 'label' => 'Pendente NF', 'format' => 'quantity'], ['path' => 'invoiced_net_total', 'label' => 'Receita faturada', 'format' => 'money'], ['path' => 'invoiced_cost_total', 'label' => 'Custo faturado', 'format' => 'money'], ['path' => 'invoiced_gross_profit', 'label' => 'Lucro bruto', 'format' => 'money'], ['path' => 'invoiced_gross_margin_percent', 'label' => 'Margem faturada (%)']];
        return $this->presenter->report($result, 'Funil de produtos', $summary, 'rows', $rows, 'tools_call_product_conversion_summary');
    }
}
