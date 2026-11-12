<?php

declare(strict_types=1);

namespace HardnessMcp\Tools;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Core\McpException;
use HardnessMcp\Tenant\ResolvedTenantContext;
use HardnessMcp\Tools\Validation\ToolArgumentsValidator;
use HardnessMcp\Tools\Presentation\ToolResultPresenter;
use Throwable;

final class ToolExecutor
{
    private ToolRegistry $registry;
    private ToolArgumentsValidator $argumentsValidator;
    private ?ResolvedTenantContext $resolvedTenantContext;
    public function __construct(ToolRegistry $registry, ?ToolArgumentsValidator $argumentsValidator = null, ?ResolvedTenantContext $resolvedTenantContext = null) { $this->registry = $registry; $this->argumentsValidator = $argumentsValidator ?? new ToolArgumentsValidator(); $this->resolvedTenantContext = $resolvedTenantContext; }

    /** @return array<int, array<string, mixed>> */
    public function list(ExecutionContext $executionContext): array
    {
        $tools = [];
        foreach ($this->registry->all() as $tool) {
            $definition = $tool->getDefinition();
            if (!$this->isAvailable($tool, $executionContext, $definition)) { continue; }
            $tools[] = ['name' => PublicToolNameMap::publicName($definition->getName()), 'description' => $definition->getDescription(), 'inputSchema' => $definition->getInputSchema()];
        }
        return $tools;
    }

    /** @param mixed $params */
    public function call($params, ExecutionContext $executionContext, bool $argumentsAreObject, $rawArguments = null): ToolExecutionResult
    {
        if (!is_array($params) || $this->isList($params) || !isset($params['name']) || !is_string($params['name'])) {
            throw new McpException('Parâmetros inválidos para esta Tool. Confira os campos obrigatórios, tipos e limites definidos no schema.', -32602);
        }
        $tool = $this->registry->find(PublicToolNameMap::internalName($params['name']));
        if ($tool === null) { throw new McpException('Method not found.', -32601); }
        if ($tool instanceof ConditionallyAvailableToolInterface && !$tool->isAvailable($executionContext)) { throw new McpException('Method not found.', -32601); }
        if (!array_key_exists('arguments', $params) || !is_array($params['arguments']) || !$argumentsAreObject) {
            throw new McpException('Parâmetros inválidos para esta Tool. Confira os campos obrigatórios, tipos e limites definidos no schema.', -32602);
        }

        $definition = $tool->getDefinition();
        $validator = $definition->getArgumentsValidator() ?? $this->argumentsValidator;
        if (!$validator->isValid($params['arguments'], $rawArguments ?? (object) $params['arguments'], $definition->getInputSchema())) {
            throw new McpException('Parâmetros inválidos para esta Tool. Confira os campos obrigatórios, tipos e limites definidos no schema.', -32602);
        }
        if (!$this->hasScopes($executionContext, $definition)) {
            return (new ToolResultPresenter())->functionalError('scope_denied', 'Esta conexão não está autorizada a usar esta Tool.', 'tools_call_scope_denied');
        }
        if ($definition->requiresErpActor() && !$executionContext->hasErpActor()) {
            return (new ToolResultPresenter())->functionalError('erp_actor_required', 'Esta conexão precisa de um usuário responsável válido para executar esta Tool.', 'tools_call_erp_actor_required');
        }

        try {
            return $tool->execute($params['arguments'], $executionContext);
        } catch (Throwable $exception) {
            return (new ToolResultPresenter())->functionalError('tool_unavailable', 'A Tool não pôde ser concluída.', 'tools_call_internal_error');
        }
    }

    private function hasScopes(ExecutionContext $executionContext, ToolDefinition $definition): bool
    {
        foreach ($definition->getRequiredScopes() as $scope) {
            if (!$executionContext->hasScope($scope)) { return false; }
        }
        return true;
    }

    private function isAvailable(ToolInterface $tool, ExecutionContext $executionContext, ToolDefinition $definition): bool
    {
        return (!$tool instanceof ConditionallyAvailableToolInterface || $tool->isAvailable($executionContext))
            && $this->hasScopes($executionContext, $definition)
            && (!$definition->requiresErpActor() || $executionContext->hasErpActor());
    }

    /** @param array<mixed> $value */
    private function isList(array $value): bool { return array_keys($value) === range(0, count($value) - 1); }
}
