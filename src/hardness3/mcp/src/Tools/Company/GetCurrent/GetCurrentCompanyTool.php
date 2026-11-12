<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Company\GetCurrent;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\ToolDefinition;
use HardnessMcp\Tools\ToolExecutionResult;
use HardnessMcp\Tools\ToolInterface;
use HardnessMcp\Tools\Presentation\ToolResultPresenter;

final class GetCurrentCompanyTool implements ToolInterface
{
    private GetCurrentCompanyHandler $handler;
    private GetCurrentCompanyDefinition $definition;
    private ToolResultPresenter $presenter;

    public function __construct(GetCurrentCompanyHandler $handler, ?GetCurrentCompanyDefinition $definition = null, ?ToolResultPresenter $presenter = null)
    {
        $this->handler = $handler;
        $this->definition = $definition ?? new GetCurrentCompanyDefinition();
        $this->presenter = $presenter ?? new ToolResultPresenter();
    }

    public function getDefinition(): ToolDefinition { return $this->definition->toToolDefinition(); }

    /** @param array<string, mixed> $arguments */
    public function execute(array $arguments, ExecutionContext $executionContext): ToolExecutionResult
    {
        $company = $this->handler->handle(GetCurrentCompanyInput::fromArguments($arguments), $executionContext);
        if ($company === null) {
            return $this->presenter->functionalError('company_not_found', 'A empresa vinculada a esta conexão não foi encontrada.', 'tools_call_company_not_found');
        }

        return $this->presenter->record($company, 'Empresa vinculada', [
            ['path'=>'legal_name','label'=>'Razão social'], ['path'=>'short_name','label'=>'Nome fantasia'], ['path'=>'document','label'=>'Documento'], ['path'=>'city','label'=>'Cidade'], ['path'=>'state','label'=>'Estado'], ['path'=>'status','label'=>'Status','format'=>'status','map'=>['S'=>'ativo','N'=>'inativo']],
        ]);
    }
}
