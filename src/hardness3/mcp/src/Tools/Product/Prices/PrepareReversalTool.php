<?php
declare(strict_types=1);
namespace HardnessMcp\Tools\Product\Prices;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\{ToolDefinition,ToolExecutionResult,ToolInterface};
use HardnessMcp\Tools\Presentation\ToolResultPresenter;
use HardnessMcp\Tools\Validation\ToolArgumentsValidator;

final class PrepareReversalTool implements ToolInterface
{
    private ?PriceHistoryRepository $repository;
    public function __construct(?PriceHistoryRepository $repository=null){$this->repository=$repository;}
    public function getDefinition(): ToolDefinition { return new ToolDefinition('hardness.product.prices.reversal_prepare','Prepares a new reversal preview from previously applied items of one source price-update batch. It never changes prices. Omit item_lines to request every applied item, or provide explicit source line numbers to request only those items. The tool blocks any item whose selected current price changed after the source update, whose product changed, or whose automatic pricing is active. Present the resulting preview to the user and require explicit approval before calling reversal_apply.',[
        'type'=>'object','properties'=>[
            'source_batch_id'=>['type'=>'string','minLength'=>32,'maxLength'=>32],
            'request_key'=>['type'=>'string','minLength'=>1,'maxLength'=>128],
            'item_lines'=>['type'=>'array','minItems'=>1,'maxItems'=>PriceBatchLimits::maxItems(),'items'=>['type'=>'integer','minimum'=>1,'maximum'=>4294967295]],
        ],'required'=>['source_batch_id','request_key'],'additionalProperties'=>false
    ],['product:prices:reverse'],true,new ToolArgumentsValidator(PriceBatchLimits::maxItems(),PriceBatchLimits::argumentBytes())); }
    public function execute(array $a,ExecutionContext $c): ToolExecutionResult {
        $p=new ToolResultPresenter();try{
            if(!preg_match('/\A[A-Za-z0-9_-]{1,128}\z/',$a['request_key']))throw new \RuntimeException('invalid_request_key');
            if(isset($a['item_lines'])&&count($a['item_lines'])!==count(array_unique($a['item_lines'])))throw new \RuntimeException('duplicate_line');
            if(!$this->repository)throw new \RuntimeException('reversal_unavailable');$r=$this->repository->prepareReversal($a,$c);
            return $p->detailWithItems($r,'Prévia de reversão preparada; nenhum preço foi alterado',[['path'=>'batch_id','label'=>'Lote de reversão'],['path'=>'source_batch_id','label'=>'Lote original'],['path'=>'expires_at','label'=>'Validade UTC']], 'items',[['path'=>'line','label'=>'Linha'],['path'=>'code','label'=>'Código'],['path'=>'status','label'=>'Situação'],['path'=>'error','label'=>'Pendência']],'tools_call_product_prices_reversal_prepare');
        }catch(\Throwable $e){$m=['actor_required'=>'É necessário um responsável ERP válido.','invalid_request_key'=>'Use uma chave de solicitação válida.','duplicate_line'=>'As linhas selecionadas devem ser distintas.','source_batch_unavailable'=>'O lote original não está disponível nesta empresa.','no_applied_items'=>'O lote original não possui itens aplicados para reverter.','idempotency_conflict'=>'A chave já foi usada para outra reversão. Use uma nova chave.'];$code=isset($m[$e->getMessage()])?$e->getMessage():'reversal_unavailable';return $p->functionalError($code,$m[$code]??'Não foi possível preparar a reversão. Nenhum preço foi alterado.','tools_call_product_prices_reversal_prepare_failed',[],$code);}
    }
}
