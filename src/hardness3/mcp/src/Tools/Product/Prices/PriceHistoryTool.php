<?php
declare(strict_types=1);
namespace HardnessMcp\Tools\Product\Prices;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\{ToolDefinition,ToolExecutionResult,ToolInterface};
use HardnessMcp\Tools\Presentation\ToolResultPresenter;

final class PriceHistoryTool implements ToolInterface
{
    private ?PriceHistoryRepository $repository;
    public function __construct(?PriceHistoryRepository $repository=null){$this->repository=$repository;}
    public function getDefinition(): ToolDefinition { return new ToolDefinition('hardness.product.prices.history','Lists price-update and reversal batches for the company. It exposes operational history and selected price before/after values only to connections with the dedicated history permission. Use batch_id to inspect one batch; do not infer that a batch is reversible without preparing a reversal preview.',[
        'type'=>'object','properties'=>['batch_id'=>['type'=>'string','minLength'=>32,'maxLength'=>32],'limit'=>['type'=>'integer','minimum'=>1,'maximum'=>100]],'additionalProperties'=>false
    ],['product:prices:history']); }
    public function execute(array $a,ExecutionContext $c): ToolExecutionResult {
        $p=new ToolResultPresenter();try{if(!$this->repository)throw new \RuntimeException();$a['limit']=$a['limit']??50;$r=$this->repository->history($a,$c);if(isset($a['batch_id']))return $p->detailWithItems(['batch_id'=>$a['batch_id'],'items'=>$r['items']??[]],'Histórico do lote',[['path'=>'batch_id','label'=>'Lote']],'items',[['path'=>'line','label'=>'Linha'],['path'=>'code','label'=>'Código'],['path'=>'status','label'=>'Situação'],['path'=>'error','label'=>'Pendência']],'tools_call_product_prices_history');return $p->collection($r,'batches','lote de preços','lotes de preços',[['path'=>'batch_id','label'=>'Lote'],['path'=>'type','label'=>'Tipo'],['path'=>'status','label'=>'Situação'],['path'=>'created_at','label'=>'Criado UTC']],'tools_call_product_prices_history');}catch(\Throwable $e){return $p->functionalError('history_unavailable','Não foi possível consultar o histórico de preços.','tools_call_product_prices_history_failed');}
    }
}
