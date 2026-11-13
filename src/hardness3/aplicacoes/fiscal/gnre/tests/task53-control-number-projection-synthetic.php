<?php
declare(strict_types=1);

require_once __DIR__.'/../src/GuideControlProjection.php';

function t53(bool $ok,string $message):void { if(!$ok) throw new RuntimeException('Task 53: '.$message); }
function t53soap(string $status,string $control='',int $count=1):string {
    $guides=[];
    for($i=0;$i<$count;$i++) $guides[]='<guia><situacaoGuia>'.$status.'</situacaoGuia>'.($control!==''?'<numeroControle>'.$control.'</numeroControle>':'').'</guia>';
    return '<soap:Envelope xmlns:soap="urn:test" xmlns="http://www.gnre.pe.gov.br"><soap:Body><TResultLote_GNRE><situacaoProcess><codigo>100</codigo><descricao>OK</descricao></situacaoProcess><resultado>'.implode('',$guides).'</resultado></TResultLote_GNRE></soap:Body></soap:Envelope>';
}

$soap=t53soap('0','202690001313411');
t53(GnreGuideControlProjection::fromOfficialSoap($soap)==='202690001313411','autorização individual válida projeta o controle oficial.');
t53(GnreGuideControlProjection::fromOfficialSoap($soap)==='202690001313411','repetição idempotente mantém o mesmo controle candidato.');
foreach(['1','2','4','3'] as $status) t53(GnreGuideControlProjection::fromOfficialSoap(t53soap($status,'202690001313411'))===null,'situacaoGuia='.$status.' não projeta controle.');
t53(GnreGuideControlProjection::fromOfficialSoap(t53soap('0'))===null,'controle ausente não projeta.');
t53(GnreGuideControlProjection::fromOfficialSoap(t53soap('0','ABC'))===null,'controle inválido não projeta.');
t53(GnreGuideControlProjection::fromOfficialSoap(t53soap('0','202690001313411',2))===null,'resultado múltiplo/inconclusivo não associa controle à guia.');
t53(GnreGuideControlProjection::fromOfficialSoap('<soap>')===null,'SOAP inválido não projeta.');
/* O Portal real usa namespace ns1 e pode devolver somente nossoNumero, que o
   XSD denomina número interno da UF; ele não pode virar numeroControle. */
$namespaced='<soapenv:Envelope xmlns:soapenv="urn:test" xmlns:ns1="http://www.gnre.pe.gov.br"><soapenv:Body><ns1:TResultLote_GNRE><ns1:situacaoProcess><ns1:codigo>100</ns1:codigo><ns1:descricao>OK</ns1:descricao></ns1:situacaoProcess><ns1:resultado><ns1:guia><ns1:situacaoGuia>0</ns1:situacaoGuia><ns1:nossoNumero>123456</ns1:nossoNumero></ns1:guia></ns1:resultado></ns1:TResultLote_GNRE></soapenv:Body></soapenv:Envelope>';
t53(GnreGuideControlProjection::fromOfficialSoap($namespaced)===null,'nossoNumero da UF não é projetado como controle oficial.');
$namespaced=str_replace('<ns1:nossoNumero>123456</ns1:nossoNumero>','<ns1:numeroControle>202690001313411</ns1:numeroControle>',$namespaced);
t53(GnreGuideControlProjection::fromOfficialSoap($namespaced)==='202690001313411','numeroControle namespaceado da estrutura oficial é extraído.');

$worker=(string)file_get_contents(__DIR__.'/../src/QueueWorker.php');
foreach(['GNRE001_Id=? AND GNRE001_C004_Id=? AND GNRE001_Status=\'P\'','GnreGuideControlProjection::fromOfficialSoap','COALESCE(NULLIF(GNRE001_Numero_Controle,\'\'),?)','GnreGuideControlProjection::available'] as $needle) t53(strpos($worker,$needle)!==false,'persistência deve proteger escopo, autorização atual, idempotência e compatibilidade: '.$needle);

function gnre_h($value):string{return htmlspecialchars((string)$value,ENT_QUOTES,'UTF-8');}
function gnre_url(string $path=''):string{return '/gnre/'.$path;}
function gnre_guide_url(int $id,string $tab='dados'):string{return '/gnre/?edit='.$id.'&tab='.$tab;}
require_once __DIR__.'/../public/task28-guide-center.php';
$filters=['status'=>'','uf'=>'','revenue'=>'','due_from'=>'','due_to'=>'','guide'=>0,'receipt'=>'','environment'=>'','include_archived'=>false];
$row=['GNRE001_Id'=>53,'GNRE001_Numero_Controle'=>'202690001313411','GNRE001_UF_Favorecida'=>'BA','GNRE001_Valor'=>'1.00','GNRE001_Data_Vencimento'=>'2026-11-04','GNRE001_Data_Hora_Criacao'=>'2026-11-04 10:00:00','GNRE001_Data_Hora_Alteracao'=>null,'_queue'=>null,'_attempts'=>[],'_pdf_id'=>0,'_state'=>['key'=>'EMITIDA','label'=>'Emitida','action'=>'Abrir','action_tab'=>'dados'],'_item_due_dates'=>[],'_item_revenues'=>[],'_projected_revenue'=>'1000'];
$html=task28_render_guide_center($filters,[$row],[$row],[],true);
t53(strpos($html,'<th>Controle</th>')===false&&strpos($html,'202690001313411')===false,'Central não exibe cabeçalho nem valor de controle, mesmo quando a projeção existe.');
$empty=task28_render_guide_center($filters,[],[],[],true);
t53(substr_count($html,'<th>')===10&&strpos($empty,'colspan="10"')!==false,'Central mantém dez colunas de dados e colspan coerente após incluir Data de criação.');
$center=(string)file_get_contents(__DIR__.'/../public/task28-guide-center.php');
t53(strpos($center,'GNRE001_Numero_Controle')===false,'template da Central não usa a projeção como confirmação nem como dado operacional.');
t53(strpos($center,'GNRE005_Ultima_Consulta_SOAP')===false&&strpos($center,'GnreResultInterpreter')===false&&strpos($center,'PortalClient')===false,'GET da Central não interpreta SOAP nem chama o Portal.');

echo "Task 53 control-number projection synthetic: OK\n";
