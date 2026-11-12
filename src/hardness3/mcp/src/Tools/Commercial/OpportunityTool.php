<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Commercial;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\Presentation\ToolResultPresenter;
use HardnessMcp\Tools\ToolDefinition;
use HardnessMcp\Tools\ToolExecutionResult;
use HardnessMcp\Tools\ToolInterface;
use InvalidArgumentException;

final class OpportunityTool implements ToolInterface
{
    private OpportunityHandler $handler; private ToolResultPresenter $presenter;
    public function __construct(OpportunityHandler $handler, ?ToolResultPresenter $presenter = null) { $this->handler = $handler; $this->presenter = $presenter ?? new ToolResultPresenter(); }
    public function getDefinition(): ToolDefinition { return (new OpportunityDefinition())->toToolDefinition(); }
    public function execute(array $arguments, ExecutionContext $context): ToolExecutionResult
    {
        try { $result = $this->handler->handle(OpportunityInput::fromArguments($arguments), $context); }
        catch (InvalidArgumentException $exception) { return $this->presenter->functionalError('commercial_opportunities_invalid_period', $exception->getMessage(), 'tools_call_commercial_opportunities_invalid_period'); }
        catch (\Throwable $exception) { return $this->presenter->functionalError('commercial_opportunities_unavailable', 'Não foi possível consultar oportunidades comerciais agora. Tente novamente.', 'tools_call_commercial_opportunities_unavailable'); }
        $displayLimit = isset($arguments['limit']) ? min((int) $arguments['limit'], 50) : 20;
        $presenter = $displayLimit > 10 ? new ToolResultPresenter($displayLimit, 24000) : $this->presenter;
        return $presenter->report($result, 'Oportunidades comerciais', [['path' => 'summary.opportunity_count', 'label' => 'Oportunidades'], ['path' => 'summary.pending_total', 'label' => 'Valor pendente estimado', 'format' => 'money']], 'rows', [['path' => 'priority', 'label' => 'Prioridade'], ['path' => 'opportunity_type', 'label' => 'Tipo'], ['path' => 'document_id', 'label' => 'Documento'], ['path' => 'issued_at', 'label' => 'Emissão', 'format' => 'date'], ['path' => 'age_days', 'label' => 'Dias em aberto'], ['path' => 'customer_id', 'label' => 'Cliente'], ['path' => 'customer_name', 'label' => 'Nome do cliente'], ['path' => 'seller_name', 'label' => 'Vendedor'], ['path' => 'original_total', 'label' => 'Valor original', 'format' => 'money'], ['path' => 'progressed_total', 'label' => 'Já convertido/faturado', 'format' => 'money'], ['path' => 'pending_total', 'label' => 'Pendente estimado', 'format' => 'money']], 'tools_call_commercial_opportunities');
    }
}
