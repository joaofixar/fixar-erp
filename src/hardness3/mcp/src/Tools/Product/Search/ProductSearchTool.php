<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Product\Search;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\ToolDefinition;
use HardnessMcp\Tools\ToolExecutionResult;
use HardnessMcp\Tools\ToolInterface;
use HardnessMcp\Tools\Presentation\ToolResultPresenter;

final class ProductSearchTool implements ToolInterface
{
    private ProductSearchHandler $handler;
    private ToolResultPresenter $presenter;
    public function __construct(ProductSearchHandler $handler, ?ToolResultPresenter $presenter = null) { $this->handler = $handler; $this->presenter=$presenter??new ToolResultPresenter(); }
    public function getDefinition(): ToolDefinition { return (new ProductSearchDefinition())->toToolDefinition(); }
    /** @param array<string,mixed> $arguments */
    public function execute(array $arguments, ExecutionContext $context): ToolExecutionResult
    {
        try { $result = $this->handler->handle(ProductSearchInput::fromArguments($arguments), $context); }
        catch (\Throwable $exception) { return $this->presenter->functionalError('product_search_unavailable', 'Não foi possível consultar os produtos agora. Tente novamente.', 'tools_call_product_search_unavailable'); }
        return $this->presenter->collection($result,'items','produto','produtos',[['path'=>'id','label'=>'ID interno'],['path'=>'code','label'=>'Código'],['path'=>'description','label'=>'Descrição'],['path'=>'unit','label'=>'Unidade']]);
    }
}
