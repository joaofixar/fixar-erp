<?php
declare(strict_types=1);
namespace HardnessMcp\Tools\LegacyReports;
use HardnessMcp\Context\ExecutionContext;use HardnessMcp\Tools\{ToolDefinition,ToolExecutionResult,ToolInterface};use HardnessMcp\Tools\Presentation\ToolResultPresenter;
final class LegacyReportsListTool implements ToolInterface { private LegacyReportCatalog $catalog; public function __construct(LegacyReportCatalog $catalog){$this->catalog=$catalog;} public function getDefinition():ToolDefinition{return new ToolDefinition('hardness.legacy_reports.list','Lists every legacy API report available for this ERP installation, including configured custom AppApi reports. Use its id with hardness_legacy_reports_get.',['type'=>'object','properties'=>new \stdClass(),'additionalProperties'=>false],['legacy_reports:read']);}public function execute(array $a,ExecutionContext $c):ToolExecutionResult{$reports=[];foreach($this->catalog->all() as $r)$reports[]=['id'=>$r['id'],'name'=>$r['name'],'description'=>$r['description'],'parameters'=>$r['parameters'],'requires_dates'=>$r['requires_dates']];return (new ToolResultPresenter())->collection(['reports'=>$reports],'reports','relatório legado','relatórios legados',[['path'=>'id','label'=>'ID'],['path'=>'name','label'=>'Nome'],['path'=>'description','label'=>'Descrição']]);}}
final class LegacyReportsGetTool implements ToolInterface {
    private LegacyReportCatalog $catalog; private LegacyReportGateway $gateway; private ?LegacyReportDownloadLinkService $downloads; private string $exportDirectory; private string $tenantDatabase;
    public function __construct(LegacyReportCatalog $catalog, LegacyReportGateway $gateway, ?LegacyReportDownloadLinkService $downloads=null, string $exportDirectory='', string $tenantDatabase='') { $this->catalog=$catalog; $this->gateway=$gateway; $this->downloads=$downloads; $this->exportDirectory=$exportDirectory; $this->tenantDatabase=$tenantDatabase; }
    public function getDefinition(): ToolDefinition {
        return new ToolDefinition('hardness.legacy_reports.get','Runs an approved legacy API report with the same query parameters and returns its JSON data without changing its records or field names.',[
            'type'=>'object','properties'=>[
                'report'=>['type'=>'string','minLength'=>1,'maxLength'=>80],
                'DATAINICIAL'=>['type'=>'string','minLength'=>10,'maxLength'=>10],
                'DATAFINAL'=>['type'=>'string','minLength'=>10,'maxLength'=>10],
                'parameters'=>['type'=>'object','properties'=>new \stdClass(),'additionalProperties'=>true],
                'delivery'=>['type'=>'string','enum'=>['export','page'],'default'=>'export'],
                'cursor'=>['type'=>'string','minLength'=>1,'maxLength'=>20],
                'page_size'=>['type'=>'integer','minimum'=>1,'maximum'=>500],
            ],'required'=>['report'],'additionalProperties'=>false,
        ],['legacy_reports:read'],true);
    }
    public function execute(array $a, ExecutionContext $c): ToolExecutionResult {
        $p=new ToolResultPresenter(); $r=$this->catalog->find((string)$a['report']);
        if($r===null)return $p->functionalError('legacy_report_not_found','O relatório solicitado não está disponível nesta instalação.','tools_call_legacy_report_not_found');
        if($r['requires_dates']&&(!isset($a['DATAINICIAL'],$a['DATAFINAL']))) return $p->functionalError('legacy_report_dates_required','Este relatório exige DATAINICIAL e DATAFINAL.','tools_call_legacy_report_dates_required');
        $delivery=(string)($a['delivery']??'export');
        if($delivery==='export'){
            if($this->downloads===null||$this->exportDirectory===''||$this->tenantDatabase===''||$c->getOAuthGrantId()===null)return $p->functionalError('legacy_report_export_oauth_required','A exportação exige uma conexão OAuth ativa com o escopo de relatórios legados.','tools_call_legacy_report_export_oauth_required');
            try{$export=$this->gateway->export($r,$a,$c,$this->exportDirectory);$url=$this->downloads->issue($export['file'],$this->tenantDatabase,$c);if($url===null)throw new \RuntimeException('legacy_report_export_unavailable');}catch(\Throwable $e){$code=in_array($e->getMessage(),['legacy_report_actor_required','legacy_report_actor_unavailable'],true)?$e->getMessage():'legacy_report_export_unavailable';return $p->functionalError($code,'Não foi possível preparar a exportação do relatório legado agora.','tools_call_legacy_report_export_failed',[],$code);}
            return new ToolExecutionResult(['content'=>[['type'=>'text','text'=>'A exportação completa foi preparada. Use download_url para obter o JSON integral.']],'structuredContent'=>['report'=>['id'=>$r['id'],'name'=>$r['name']],'delivery'=>'export','download_url'=>$url,'bytes'=>$export['bytes'],'expires_in_seconds'=>LegacyReportDownloadLinkService::DOWNLOAD_TTL_SECONDS]],'tools_call_legacy_report_export');
        }
        if(isset($r['app_code']))return $p->functionalError('legacy_report_pagination_not_supported','Este relatório personalizado não suporta paginação SQL MCP com segurança. Use delivery=export para obter o JSON completo.','tools_call_legacy_report_pagination_not_supported');
        $size=max(1,min(500,(int)($a['page_size']??100)));$cursor=$a['cursor']??null;$offset=0;
        if(is_string($cursor)){if(!ctype_digit($cursor))return $p->functionalError('legacy_report_cursor_invalid','O cursor informado é inválido.','tools_call_legacy_report_cursor_invalid');$offset=(int)$cursor;}
        $request=$a;$request['MCP_PAGE_SIZE']=$size;$request['MCP_PAGE_OFFSET']=$offset;
        try {$data=$this->gateway->fetch($r,$request,$c);} catch(\Throwable $e){$code=in_array($e->getMessage(),['legacy_report_actor_required','legacy_report_actor_unavailable','legacy_report_invalid_json'],true)?$e->getMessage():'legacy_report_unavailable';return $p->functionalError($code,'Não foi possível executar o relatório legado agora.','tools_call_legacy_report_failed',[],$code);}
        $result=['report'=>['id'=>$r['id'],'name'=>$r['name']],'data'=>$data,'offset'=>$offset];if(is_array($data)&&array_keys($data)===range(0,count($data)-1)&&count($data)>$size){if(count($data)>$size+1)return $p->functionalError('legacy_report_pagination_not_supported','Este relatório personalizado não aplicou a paginação MCP e não pode ser consultado com segurança em grande volume.','tools_call_legacy_report_pagination_not_supported');$result['data']=array_slice($data,0,$size);$next=$offset+$size;$result['has_more']=true;$result['next_cursor']=(string)$next;}else{$result['has_more']=false;}
        return new ToolExecutionResult(['content'=>[['type'=>'text','text'=>'Relatório legado retornado. Os dados desta página estão em structuredContent.data; quando has_more for verdadeiro, use next_cursor para buscar a próxima página.']],'structuredContent'=>$result],'tools_call_legacy_report_get');
    }
}
