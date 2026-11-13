<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Logistics;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\Presentation\ToolResultPresenter;
use HardnessMcp\Tools\ToolDefinition;
use HardnessMcp\Tools\ToolExecutionResult;
use HardnessMcp\Tools\ToolInterface;
use InvalidArgumentException;

final class LoadReportTool implements ToolInterface
{
    private string $kind; private PdoLoadReportRepository $repository; private ToolResultPresenter $presenter;
    public function __construct(string $kind, PdoLoadReportRepository $repository) { $this->kind = $kind; $this->repository = $repository; $this->presenter = new ToolResultPresenter(); }
    public function getDefinition(): ToolDefinition { return $this->kind === 'summary' ? LoadReportDefinition::summary() : ($this->kind === 'list' ? LoadReportDefinition::listing() : LoadReportDefinition::detail()); }
    public function execute(array $arguments, ExecutionContext $context): ToolExecutionResult
    {
        try {
            if ($this->kind === 'detail') { $result = $this->repository->detail($context->getCompanyId(), (int)($arguments['load_id'] ?? 0)); if ($result === null) return $this->presenter->functionalError('logistics_load_not_found', 'Carga não encontrada para a empresa desta conexão.', 'tools_call_logistics_load_not_found'); return $this->presenter->detailWithItems($result, 'Carga', [['path' => 'load_id', 'label' => 'Carga'], ['path' => 'description', 'label' => 'Descrição'], ['path' => 'status', 'label' => 'Status'], ['path' => 'scheduled_at', 'label' => 'Previsão', 'format' => 'date'], ['path' => 'driver_name', 'label' => 'Motorista'], ['path' => 'carrier_name', 'label' => 'Transportadora'], ['path' => 'vehicle_plate', 'label' => 'Veículo'], ['path' => 'load_total', 'label' => 'Total', 'format' => 'money'], ['path' => 'load_weight', 'label' => 'Peso', 'format' => 'quantity'], ['path' => 'freight_total', 'label' => 'Frete', 'format' => 'money'], ['path' => 'freight_per_kg', 'label' => 'Frete/kg', 'format' => 'money'], ['path' => 'freight_per_km', 'label' => 'Frete/km', 'format' => 'money']], 'items', [['path' => 'delivery_sequence', 'label' => 'Sequência'], ['path' => 'order_id', 'label' => 'Pedido'], ['path' => 'customer_name', 'label' => 'Cliente'], ['path' => 'destination_city', 'label' => 'Cidade'], ['path' => 'destination_state', 'label' => 'UF'], ['path' => 'destination_region', 'label' => 'Região'], ['path' => 'invoice_numbers', 'label' => 'NF(s)'], ['path' => 'order_delivery_at', 'label' => 'Entrega', 'format' => 'date'], ['path' => 'order_total', 'label' => 'Valor', 'format' => 'money'], ['path' => 'order_weight', 'label' => 'Peso', 'format' => 'quantity'], ['path' => 'order_freight_total', 'label' => 'Frete', 'format' => 'money']]); }
            $input = LoadReportInput::period($arguments, $this->kind === 'summary'); $result = $this->kind === 'summary' ? $this->repository->summary($context->getCompanyId(), $input) : $this->repository->listing($context->getCompanyId(), $input);
            return $this->presenter->report($result, $this->kind === 'summary' ? 'Resumo de cargas' : 'Cargas', $this->kind === 'summary' ? [['path' => 'summary.load_count', 'label' => 'Cargas'], ['path' => 'summary.load_total', 'label' => 'Total', 'format' => 'money'], ['path' => 'summary.freight_total', 'label' => 'Frete', 'format' => 'money']] : [], 'rows', $this->kind === 'summary' ? [['path' => 'status', 'label' => 'Status'], ['path' => 'date', 'label' => 'Data', 'format' => 'date'], ['path' => 'load_count', 'label' => 'Cargas'], ['path' => 'load_total', 'label' => 'Total', 'format' => 'money']] : [['path' => 'load_id', 'label' => 'Carga'], ['path' => 'description', 'label' => 'Descrição'], ['path' => 'status', 'label' => 'Status'], ['path' => 'scheduled_at', 'label' => 'Previsão', 'format' => 'date'], ['path' => 'driver_name', 'label' => 'Motorista'], ['path' => 'carrier_name', 'label' => 'Transportadora'], ['path' => 'linked_order_count', 'label' => 'Pedidos'], ['path' => 'customer_names', 'label' => 'Clientes'], ['path' => 'destinations', 'label' => 'Destinos'], ['path' => 'invoice_numbers', 'label' => 'NF(s)'], ['path' => 'freight_total', 'label' => 'Frete', 'format' => 'money']]);
        } catch (InvalidArgumentException $exception) { return $this->presenter->functionalError('logistics_load_invalid_period', $exception->getMessage(), 'tools_call_logistics_load_invalid_period'); } catch (\Throwable $exception) { return $this->presenter->functionalError('logistics_load_unavailable', 'Não foi possível consultar as cargas agora. Tente novamente.', 'tools_call_logistics_load_unavailable'); }
    }
}
