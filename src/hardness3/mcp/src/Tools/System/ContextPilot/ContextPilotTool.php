<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\System\ContextPilot;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\Presentation\ToolResultPresenter;
use HardnessMcp\Tools\ToolDefinition;
use HardnessMcp\Tools\ToolExecutionResult;
use HardnessMcp\Tools\ToolInterface;
use Throwable;

final class ContextPilotTool implements ToolInterface
{
    private ContextPilotGatewayInterface $gateway;
    private ToolResultPresenter $presenter;

    public function __construct(ContextPilotGatewayInterface $gateway, ?ToolResultPresenter $presenter = null)
    {
        $this->gateway = $gateway;
        $this->presenter = $presenter ?? new ToolResultPresenter();
    }

    public function getDefinition(): ToolDefinition
    {
        return new ToolDefinition('hardness.system.context_pilot', 'Validates the authorized ERP execution context through the MCP legacy bridge.', ['type' => 'object', 'properties' => new \stdClass(), 'additionalProperties' => false], ['company:read']);
    }

    /** @param array<string,mixed> $arguments */
    public function execute(array $arguments, ExecutionContext $executionContext): ToolExecutionResult
    {
        try {
            $legacyResult = $this->gateway->execute($executionContext);
        } catch (Throwable $exception) {
            return $this->presenter->functionalError('legacy_context_unavailable', 'O contexto legado autorizado não pôde ser preparado.', 'tools_call_context_pilot_unavailable');
        }

        $safe = [
            'pilot' => 'ready',
            'company_id' => (int) ($legacyResult['company_id'] ?? 0),
            'responsible_user_id' => (int) ($legacyResult['user_id'] ?? 0),
            'profile_id' => (int) ($legacyResult['profile_id'] ?? 0),
            'approval_level' => (int) ($legacyResult['approval_level'] ?? 0),
        ];
        if ($safe['company_id'] < 1 || $safe['responsible_user_id'] < 1 || $safe['profile_id'] < 1 || $safe['approval_level'] < 1) {
            return $this->presenter->functionalError('legacy_context_unavailable', 'O contexto legado autorizado não pôde ser preparado.', 'tools_call_context_pilot_unavailable');
        }

        return $this->presenter->record($safe, 'Contexto ERP autorizado', [
            ['path' => 'pilot', 'label' => 'Piloto'],
            ['path' => 'company_id', 'label' => 'Empresa'],
            ['path' => 'responsible_user_id', 'label' => 'Usuário responsável'],
            ['path' => 'profile_id', 'label' => 'Perfil'],
            ['path' => 'approval_level', 'label' => 'Alçada'],
        ]);
    }
}
