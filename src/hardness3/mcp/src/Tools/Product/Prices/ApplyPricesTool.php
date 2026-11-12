<?php
declare(strict_types=1);
namespace HardnessMcp\Tools\Product\Prices;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\{ToolDefinition,ToolExecutionResult,ToolInterface};
use HardnessMcp\Tools\Presentation\ToolResultPresenter;

final class ApplyPricesTool implements ToolInterface
{
    private ?ApplyPricesRepository $repository;
    private ?PriceReportExportService $reportExporter;
    public function __construct(?ApplyPricesRepository $repository=null, ?PriceReportExportService $reportExporter=null) { $this->repository=$repository; $this->reportExporter=$reportExporter; }
    public function getDefinition(): ToolDefinition {
        return new ToolDefinition('hardness.product.prices.apply',
            'Applies exactly a previously shown price preview only after explicit user approval. Pass its batch_id and preview_hash unchanged. confirmed must be true. Set allow_partial=true only if the user explicitly approves applying valid rows while leaving blocked rows; set allow_zero=true only if zero prices were explicitly approved. Never derive approval from spreadsheet upload alone. Changes are atomic per product, not across the entire batch. Report conflicts and partial outcomes. When the report service is configured, this operation automatically includes an XLSX audit download link. Retry the identical approved arguments to resume; already applied items are never applied again. Do not automatically create a new batch for conflicts or expired remaining rows: show a new preview for approval.',
            ['type'=>'object','properties'=>[
                'batch_id'=>['type'=>'string','minLength'=>32,'maxLength'=>32],
                'preview_hash'=>['type'=>'string','minLength'=>64,'maxLength'=>64],
                'confirmed'=>['type'=>'boolean','enum'=>[true]],
                'allow_partial'=>['type'=>'boolean'], 'allow_zero'=>['type'=>'boolean'],
            ],'required'=>['batch_id','preview_hash','confirmed','allow_partial','allow_zero'],'additionalProperties'=>false],
            ['product:prices:apply'],true);
    }
    public function execute(array $arguments,ExecutionContext $context): ToolExecutionResult {
        $p=new ToolResultPresenter();
        try {
            if(!$this->repository)throw new \RuntimeException('apply_unavailable');
            $r=$this->repository->apply($arguments,$context);
            if($this->reportExporter!==null){try{$r['report']=$this->reportExporter->export($arguments['batch_id'],$context);}catch(\Throwable $ignored){$r['report_error']='export_unavailable';}}
            return $p->detailWithItems($r,'Resultado da aplicação de preços',[
                ['path'=>'batch_id','label'=>'Lote'],['path'=>'status','label'=>'Situação'],
                ['path'=>'summary.applied','label'=>'Aplicados'],['path'=>'summary.conflict','label'=>'Conflitos'],
                ['path'=>'summary.failed','label'=>'Falhas'],['path'=>'summary.ready','label'=>'Pendentes'],
                ['path'=>'report.download_url','label'=>'Planilha XLSX'],['path'=>'report_error','label'=>'Planilha'],
            ],'items',[['path'=>'line','label'=>'Linha'],['path'=>'code','label'=>'Código'],['path'=>'status','label'=>'Situação'],['path'=>'error','label'=>'Pendência']],'tools_call_product_prices_apply');
        } catch(\Throwable $e) {
            $messages=[
                'batch_unavailable'=>'O lote não está disponível para este usuário e conexão.',
                'actor_required'=>'É necessário um responsável ERP válido.',
                'approval_required'=>'Confirme explicitamente a prévia antes de aplicar.',
                'preview_changed'=>'A prévia não corresponde aos dados aprovados. Prepare uma nova prévia.',
                'preview_expired'=>'A prévia expirou. Prepare uma nova prévia para os itens restantes.',
                'approval_mismatch'=>'Para retomar, mantenha as opções da aprovação original.',
                'partial_approval_required'=>'O lote possui linhas bloqueadas. É necessária aprovação explícita para aplicar somente as válidas.',
                'zero_approval_required'=>'O lote contém preços zerados. É necessária aprovação explícita.',
                'transactional_storage_required'=>'As tabelas envolvidas precisam suportar transações.',
            ];
            $code=isset($messages[$e->getMessage()])?$e->getMessage():'apply_unavailable';
            return $p->functionalError($code,$messages[$code]??'Não foi possível concluir a aplicação. Alguns itens podem ter sido concluídos. Reenvie o mesmo lote e a mesma aprovação para obter o resultado e retomar sem duplicação.','tools_call_product_prices_apply_failed',[],$code);
        }
    }
}
