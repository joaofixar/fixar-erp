<?php
declare(strict_types=1);
require_once __DIR__.'/../src/XmlSemanticComparator.php';

function isolation_assert(bool $ok,string $message):void {if(!$ok)throw new RuntimeException($message);}
$root=dirname(__DIR__);
$index=(string)file_get_contents($root.'/public/index.php');
$ui=(string)file_get_contents($root.'/public/task63-batch.php');
$js=(string)file_get_contents($root.'/public/task63-batch.js');
$generator=(string)file_get_contents($root.'/src/XmlGenerator.php');
foreach(['prepare_real_batch_pilot_queue','send_real_batch_pilot_groups','consult_real_batch_pilot_queue','resume_real_batch_pilot_queue','cancel_real_batch_cohort'] as $action)isolation_assert(strpos($index,"'$action'")!==false,'ação piloto precisa ser bloqueada antes do fluxo: '.$action);
isolation_assert(strpos($index,"require_once __DIR__ . '/../src/GnreRealBatchPilot.php';")===false,'fluxo individual não pode carregar a classe do piloto');
isolation_assert(strpos($ui,'<?php if (false): /* Pilot UI retained in source for audit; emergency-disabled. */ ?>')!==false,'painel do piloto deve permanecer suprimido da interface');
isolation_assert(strpos($ui,'Lote real — piloto em Homologação')!==false,'histórico do piloto deve ser preservado no código');
isolation_assert(strpos($js,'data-task76-pilot-start')===false&&strpos($js,'data-task76-auto-consult')===false,'Fila individual não pode executar código do piloto');
isolation_assert(strpos($generator,'identificadorGuia')===false,'gerador individual não pode serializar identificadorGuia');
if(!class_exists('DOMDocument')){echo "OK emergency pilot isolation checks (XML semantic case skipped: DOM unavailable)\n";exit;}

$authorized='<TLote_GNRE><guias><TDadosGNRE><ufFavorecida>BA</ufFavorecida><tipoGnre>0</tipoGnre><contribuinteEmitente><identificacao><CNPJ>123</CNPJ></identificacao></contribuinteEmitente><itensGNRE><item><receita>100001</receita><valor tipo="11">10.00</valor></item></itensGNRE><valorGNRE>10.00</valorGNRE><dataPagamento>2026-11-07</dataPagamento></TDadosGNRE></guias></TLote_GNRE>';
$presentationOnly='<TLote_GNRE> <guias><TDadosGNRE><dataPagamento>2026-11-07</dataPagamento><valorGNRE>10.00</valorGNRE><itensGNRE><item><valor tipo="11">10.00</valor><receita>100001</receita></item></itensGNRE><contribuinteEmitente><identificacao><CNPJ>123</CNPJ></identificacao></contribuinteEmitente><tipoGnre>0</tipoGnre><ufFavorecida>BA</ufFavorecida></TDadosGNRE></guias></TLote_GNRE>';
$rejected=str_replace('<valorGNRE>10.00</valorGNRE>','<valorGNRE>11.00</valorGNRE>',$authorized);
isolation_assert(GnreXmlSemanticComparator::firstDifference($authorized,$presentationOnly)===null,'ordem e apresentação não podem produzir diferença semântica');
$difference=GnreXmlSemanticComparator::firstDifference($authorized,$rejected);
isolation_assert(is_array($difference)&&$difference['path']==='/TLote_GNRE/guias[1]/TDadosGNRE[1]/valorGNRE[1]/text()'&&$difference['authorized']==='10.00'&&$difference['rejected']==='11.00','comparador deve apontar a primeira diferença semântica concreta');
foreach([$authorized,$rejected] as $xml)isolation_assert(strpos($xml,'identificadorGuia')===false,'XML individual sintético não pode conter identificadorGuia');
echo "OK emergency pilot isolation and XML diagnostic checks\n";
