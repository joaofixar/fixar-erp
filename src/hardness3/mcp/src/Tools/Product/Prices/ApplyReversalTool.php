<?php
declare(strict_types=1);
namespace HardnessMcp\Tools\Product\Prices;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\{ToolDefinition,ToolExecutionResult,ToolInterface};
use HardnessMcp\Tools\Presentation\ToolResultPresenter;

final class ApplyReversalTool implements ToolInterface
{
    private ?ApplyPricesRepository $repository;
    public function __construct(?ApplyPricesRepository $repository=null){$this->repository=$repository;}
    public function getDefinition(): ToolDefinition{return new ToolDefinition('hardness.product.prices.reversal_apply','Applies exactly one previously shown reversal preview after explicit user approval. Pass its reversal batch_id and preview_hash unchanged with confirmed=true. allow_partial=true is required only when the user explicitly approves restoring the valid items while blocked items remain unchanged. Reversal never bypasses a price conflict. Retry identical arguments only to resume the same reversal.',[
        'type'=>'object','properties'=>['batch_id'=>['type'=>'string','minLength'=>32,'maxLength'=>32],'preview_hash'=>['type'=>'string','minLength'=>64,'maxLength'=>64],'confirmed'=>['type'=>'boolean','enum'=>[true]],'allow_partial'=>['type'=>'boolean']],'required'=>['batch_id','preview_hash','confirmed','allow_partial'],'additionalProperties'=>false
    ],['product:prices:reverse'],true);}
    public function execute(array $a,ExecutionContext $c): ToolExecutionResult{$p=new ToolResultPresenter();try{if(!$this->repository)throw new \RuntimeException('apply_unavailable');$a['allow_zero']=true;$r=$this->repository->apply($a,$c,'reversal');return $p->detailWithItems($r,'Resultado da reversão de preços',[['path'=>'batch_id','label'=>'Lote'],['path'=>'status','label'=>'Situação'],['path'=>'summary.applied','label'=>'Restaurados'],['path'=>'summary.conflict','label'=>'Conflitos']], 'items',[['path'=>'line','label'=>'Linha'],['path'=>'code','label'=>'Código'],['path'=>'status','label'=>'Situação'],['path'=>'error','label'=>'Pendência']],'tools_call_product_prices_reversal_apply');}catch(\Throwable $e){$m=['batch_unavailable'=>'O lote de reversão não está disponível.','approval_required'=>'Confirme explicitamente a prévia de reversão.','preview_changed'=>'A prévia de reversão não corresponde aos dados aprovados. Prepare outra.','preview_expired'=>'A prévia de reversão expirou. Prepare outra.','partial_approval_required'=>'Há itens bloqueados. É necessária aprovação explícita para reverter somente os válidos.','approval_mismatch'=>'Para retomar, mantenha as opções da aprovação original.'];$code=isset($m[$e->getMessage()])?$e->getMessage():'apply_unavailable';return $p->functionalError($code,$m[$code]??'Não foi possível concluir a reversão. Reenvie o mesmo lote para consultar o estado.','tools_call_product_prices_reversal_apply_failed',[],$code);}}
}
