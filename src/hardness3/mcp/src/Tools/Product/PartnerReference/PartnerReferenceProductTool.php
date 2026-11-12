<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Product\PartnerReference;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\Presentation\ToolResultPresenter;
use HardnessMcp\Tools\ToolDefinition;
use HardnessMcp\Tools\ToolExecutionResult;
use HardnessMcp\Tools\ToolInterface;

final class PartnerReferenceProductTool implements ToolInterface
{
    private PartnerReferenceProductHandler $handler;
    private ToolResultPresenter $presenter;

    public function __construct(PartnerReferenceProductHandler $handler, ?ToolResultPresenter $presenter = null)
    {
        $this->handler = $handler;
        $this->presenter = $presenter ?? new ToolResultPresenter();
    }

    public function getDefinition(): ToolDefinition { return (new PartnerReferenceProductDefinition())->toToolDefinition(); }

    /** @param array<string,mixed> $arguments */
    public function execute(array $arguments, ExecutionContext $context): ToolExecutionResult
    {
        try {
            $handled = $this->handler->handle(PartnerReferenceProductInput::fromArguments($arguments), $context);
        } catch (\Throwable $exception) {
            return $this->presenter->functionalError('partner_reference_lookup_unavailable', 'Não foi possível consultar a referência do parceiro agora. Tente novamente.', 'tools_call_partner_reference_lookup_unavailable');
        }
        if ($handled['partner'] === null) {
            return $this->presenter->functionalError('partner_not_available', 'O parceiro não está disponível com o papel informado neste contexto.', 'tools_call_partner_reference_partner_not_available');
        }
        $result = $handled['result'];
        if (($result['count'] ?? 0) === 0) {
            return $this->presenter->functionalError('partner_product_reference_not_found', 'Nenhuma referência de produto do parceiro foi encontrada neste contexto.', 'tools_call_partner_reference_not_found');
        }
        return $this->presenter->collection($result, 'items', 'produto', 'produtos', [
            ['path' => 'id', 'label' => 'ID interno'],
            ['path' => 'code', 'label' => 'Código'],
            ['path' => 'description', 'label' => 'Descrição'],
            ['path' => 'unit', 'label' => 'Unidade'],
            ['path' => 'source', 'label' => 'Origem'],
        ]);
    }
}
