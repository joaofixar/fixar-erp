<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Partner\Search;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\Presentation\ToolResultPresenter;
use HardnessMcp\Tools\ToolDefinition;
use HardnessMcp\Tools\ToolExecutionResult;
use HardnessMcp\Tools\ToolInterface;

final class PartnerSearchTool implements ToolInterface
{
    private PartnerSearchHandler $handler; private ToolResultPresenter $presenter;
    public function __construct(PartnerSearchHandler $handler, ?ToolResultPresenter $presenter = null) { $this->handler = $handler; $this->presenter = $presenter ?? new ToolResultPresenter(); }
    public function getDefinition(): ToolDefinition { return (new PartnerSearchDefinition())->toToolDefinition(); }
    /** @param array<string,mixed> $arguments */
    public function execute(array $arguments, ExecutionContext $context): ToolExecutionResult
    {
        try { $result = $this->handler->handle(PartnerSearchInput::fromArguments($arguments), $context); }
        catch (\InvalidArgumentException $exception) { return $this->presenter->functionalError('partner_search_refinement_required', 'Refine a busca com nome e sobrenome, cidade ou outra informação conhecida.', 'tools_call_partner_search_refinement_required'); }
        catch (\Throwable $exception) { return $this->presenter->functionalError('partner_search_unavailable', 'Não foi possível consultar clientes e fornecedores agora. Tente novamente.', 'tools_call_partner_search_unavailable'); }
        if (($result['refine_required'] ?? false) === true) return $this->presenter->functionalError('partner_search_refinement_required', 'A busca encontrou muitos cadastros. Refine com nome e sobrenome, cidade ou outra informação conhecida.', 'tools_call_partner_search_refinement_required');
        return $this->presenter->collection($result, 'items', 'cadastro', 'cadastros', [['path' => 'candidate_ref', 'label' => 'Referência para consultar detalhes'], ['path' => 'legal_name', 'label' => 'Razão social'], ['path' => 'trading_name', 'label' => 'Fantasia'], ['path' => 'city', 'label' => 'Cidade'], ['path' => 'state', 'label' => 'UF']]);
    }
}
