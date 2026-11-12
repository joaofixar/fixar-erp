<?php
declare(strict_types=1);
namespace HardnessMcp\Tools\Product\Prices;
use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\{ToolDefinition,ToolExecutionResult,ToolInterface};
use HardnessMcp\Tools\Presentation\ToolResultPresenter;
final class PriceReportExportTool implements ToolInterface
{
    private ?PriceReportExportService $service;public function __construct(?PriceReportExportService $service=null){$this->service=$service;}
    public function getDefinition():ToolDefinition{return new ToolDefinition('hardness.product.prices.export','Creates an XLSX audit report for one price-update or reversal batch. The report contains every line and the selected prices before and after the operation, using the recorded batch history rather than current prices. Give its download link to the client after an applied batch, or whenever they request an export for checking.',['type'=>'object','properties'=>['batch_id'=>['type'=>'string','minLength'=>32,'maxLength'=>32]],'required'=>['batch_id'],'additionalProperties'=>false],['product:prices:export'],true);}
    public function execute(array $a,ExecutionContext $c):ToolExecutionResult{$p=new ToolResultPresenter();try{if(!$this->service)throw new \RuntimeException('export_unavailable');$r=$this->service->export($a['batch_id'],$c);return $p->detail($r,'Planilha de conferência gerada',[['path'=>'batch_id','label'=>'Lote'],['path'=>'items','label'=>'Itens'],['path'=>'download_url','label'=>'Download XLSX']],'tools_call_product_prices_export');}catch(\Throwable $e){$code=$e->getMessage()==='batch_unavailable'?'batch_unavailable':'export_unavailable';return $p->functionalError($code,$code==='batch_unavailable'?'O lote não está disponível nesta empresa.':'Não foi possível gerar a planilha de conferência.','tools_call_product_prices_export_failed',[],$code);}}
}
