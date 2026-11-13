<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Quote\GetById;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\ToolDefinition;
use HardnessMcp\Tools\ToolExecutionResult;
use HardnessMcp\Tools\ToolInterface;
use HardnessMcp\Tools\Presentation\ToolResultPresenter;

final class QuoteGetByIdTool implements ToolInterface
{
    private QuoteGetByIdHandler $handler; private ToolResultPresenter $presenter; public function __construct(QuoteGetByIdHandler $handler, ?ToolResultPresenter $presenter=null) { $this->handler = $handler; $this->presenter=$presenter??new ToolResultPresenter(); }
    public function getDefinition(): ToolDefinition { return (new QuoteGetByIdDefinition())->toToolDefinition(); }
    /** @param array<string,mixed> $arguments */
    public function execute(array $arguments, ExecutionContext $context): ToolExecutionResult
    {
        try { $quote = $this->handler->handle(QuoteGetByIdInput::fromArguments($arguments), $context); }
        catch (\Throwable $exception) { return $this->presenter->functionalError('quote_lookup_unavailable', 'Não foi possível consultar este orçamento agora. Tente novamente.', 'tools_call_quote_lookup_unavailable'); }
        if ($quote === null) return $this->presenter->functionalError('quote_not_found','O orçamento solicitado não foi encontrado para a empresa vinculada.','tools_call_quote_not_found');
        return $this->presenter->detailWithItems($quote,'Orçamento',[['path'=>'id','label'=>'Número'],['path'=>'issued_at','label'=>'Emissão','format'=>'date'],['path'=>'customer.name','label'=>'Cliente'],['path'=>'status','label'=>'Status'],['path'=>'total','label'=>'Total','format'=>'money']],'items',[['path'=>'id','label'=>'Item'],['path'=>'line_number','label'=>'Linha'],['path'=>'product.code','label'=>'Produto'],['path'=>'description','label'=>'Descrição'],['path'=>'quantity','label'=>'Quantidade','format'=>'quantity'],['path'=>'unit','label'=>'Unidade'],['path'=>'total','label'=>'Total','format'=>'money']]);
    }
}
