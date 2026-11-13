<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Quote\Search;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\ToolDefinition;
use HardnessMcp\Tools\ToolExecutionResult;
use HardnessMcp\Tools\ToolInterface;
use HardnessMcp\Tools\Presentation\ToolResultPresenter;

final class QuoteSearchTool implements ToolInterface
{
    private QuoteSearchHandler $handler; private ToolResultPresenter $presenter; public function __construct(QuoteSearchHandler $handler, ?ToolResultPresenter $presenter=null) { $this->handler = $handler; $this->presenter=$presenter??new ToolResultPresenter(); }
    public function getDefinition(): ToolDefinition { return (new QuoteSearchDefinition())->toToolDefinition(); }
    /** @param array<string,mixed> $arguments */ public function execute(array $arguments, ExecutionContext $context): ToolExecutionResult
    {
        try { $result = $this->handler->handle(QuoteSearchInput::fromArguments($arguments), $context); }
        catch (\Throwable $exception) { return $this->presenter->functionalError('quote_search_unavailable', 'Não foi possível consultar os orçamentos agora. Tente novamente.', 'tools_call_quote_search_unavailable'); }
        return $this->presenter->collection($result,'items','orçamento','orçamentos',[['path'=>'id','label'=>'Orçamento'],['path'=>'issued_at','label'=>'Emissão','format'=>'date'],['path'=>'customer.name','label'=>'Cliente'],['path'=>'status','label'=>'Status'],['path'=>'total','label'=>'Total','format'=>'money']]);
    }
}
