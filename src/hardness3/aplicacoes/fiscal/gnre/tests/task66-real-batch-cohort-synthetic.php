<?php
declare(strict_types=1);
require_once __DIR__.'/../src/GnreRealBatchCohort.php';

function t66(bool $ok,string $message):void { if(!$ok)throw new RuntimeException($message); }
function t66_throws(callable $callable,string $contains):void { try{$callable();}catch(RuntimeException $e){t66(strpos($e->getMessage(),$contains)!==false,'bloqueio inesperado: '.$e->getMessage());return;}throw new RuntimeException('A seleção deveria ter sido bloqueada: '.$contains); }
function t66_tdados(string $revenue='100001'):array { return ['ufFavorecida'=>'PR','tipoGnre'=>'0','contribuinteEmitente'=>['identificacao'=>['CNPJ'=>'12345678000199']],'itensGNRE'=>['item'=>[['receita'=>$revenue,'valor'=>[['tipo'=>'11','valor'=>'10.00']]]]],'valorGNRE'=>'10.00','dataPagamento'=>'2026-11-10']; }
function t66_guide(int $id=1):array { return ['GNRE001_Id'=>$id,'GNRE001_C004_Id'=>77,'GNRE001_Status'=>'R','GNRE001_Arquivada'=>'N','GNRE001_GNRE007_Id'=>88,'GNRE001_UF_Favorecida'=>'PR','GNRE007_Id'=>88,'GNRE007_C004_Id'=>77,'GNRE007_Ambiente'=>'2','GNRE007_UF'=>'PR','GNRE007_Regras_JSON'=>'{"environment":"2"}']; }

$one=t66_guide();$two=t66_guide(2);
GnreRealBatchCohort::assertSelection([$one,$two],77);
$frozen=GnreRealBatchCohort::describeParticipant(t66_tdados());
t66($frozen['complete'],'guia homogênea completa deve gerar participante congelável');
t66($frozen['fragment']!==''&&preg_match('/^[a-f0-9]{64}$/',$frozen['fragment_sha256'])===1&&preg_match('/^[a-f0-9]{64}$/',$frozen['fingerprint'])===1,'fragmento, SHA e fingerprint devem ser persistíveis');
t66(GnreRealBatchCohort::FINGERPRINT_VERSION==='GNRE-BATCH-CORRELATION-v1','somente a versão do contrato Task 65 pode ser usada');
t66(GnreRealBatchCohort::describeParticipant(t66_tdados())===$frozen,'repetição idempotente preserva exatamente a evidência congelada');
$otherCompany=$two;$otherCompany['GNRE001_C004_Id']=78;t66_throws(static fn()=>GnreRealBatchCohort::assertSelection([$one,$otherCompany],77),'empresas');
$otherUf=$two;$otherUf['GNRE001_UF_Favorecida']='RJ';$otherUf['GNRE007_UF']='RJ';$otherUf['GNRE001_GNRE007_Id']=89;$otherUf['GNRE007_Id']=89;
$third=t66_guide(3);$third['GNRE001_UF_Favorecida']='PE';$third['GNRE007_UF']='PE';$third['GNRE001_GNRE007_Id']=90;$third['GNRE007_Id']=90;
GnreRealBatchCohort::assertSelection([$one,$otherUf,$third],77);
$otherEnvironment=$two;$otherEnvironment['GNRE007_Ambiente']='1';$otherEnvironment['GNRE007_Regras_JSON']='{"environment":"1"}';t66_throws(static fn()=>GnreRealBatchCohort::assertSelection([$one,$otherEnvironment],77),'ambientes');
$otherSnapshot=$two;$otherSnapshot['GNRE001_GNRE007_Id']=89;$otherSnapshot['GNRE007_Id']=89;GnreRealBatchCohort::assertSelection([$one,$otherSnapshot],77);
$withoutSnapshot=$two;$withoutSnapshot['GNRE001_GNRE007_Id']=0;t66_throws(static fn()=>GnreRealBatchCohort::assertSelection([$withoutSnapshot],77),'snapshot oficial');
$notDraft=$two;$notDraft['GNRE001_Status']='P';t66_throws(static fn()=>GnreRealBatchCohort::assertSelection([$notDraft],77),'rascunho');
$incomplete=t66_tdados();unset($incomplete['valorGNRE']);t66(GnreRealBatchCohort::describeParticipant($incomplete)['fragment']!=='','ausência de total agregado não pode descartar TDadosGNRE válido por item');
foreach(['attempts','receipts','queue','pdf'] as $trace)t66_throws(static fn()=>GnreRealBatchCohort::assertNoIndividualState([$trace=>true]),'não pode participar');
t66_throws(static fn()=>GnreRealBatchCohort::assertItemsPerGuide(3,2),'máximo oficial permitido para uma guia');
GnreRealBatchCohort::assertItemsPerGuide(2,2);
$projectionGuide=['GNRE001_UF_Favorecida'=>'PR','GNRE001_Tipo_GNRE'=>'0','GNRE001_Data_Pagamento'=>'2026-11-10'];
$projectionItem=['GNRE003_Codigo_Receita'=>'100001','GNRE003_Detalhamento_Receita'=>'01','GNRE002_Identificacao'=>'NF-1','GNRE003_Tipo_Documento_Origem'=>'55','GNRE003_Codigo_Produto'=>'0001','GNRE003_Referencia_Periodo'=>'0','GNRE003_Referencia_Mes'=>'11','GNRE003_Referencia_Ano'=>'2026','GNRE003_Referencia_Parcela'=>'001','GNRE003_Data_Vencimento'=>'2026-11-11','GNRE003_Convenio'=>'CONV','GNRE003_Destinatario_Tipo_Identificacao'=>'CNPJ','GNRE003_Destinatario_Identificacao'=>'12345678000199','GNRE003_Destinatario_Razao_Social'=>'Destinatário','GNRE003_Destinatario_Codigo_Municipio'=>'04400','GNRE003_Campos_Extras_JSON'=>'[{"codigo":1,"valor":"X"}]','GNRE006'=>[['GNRE006_Tipo_Valor'=>'11','GNRE006_Valor'=>'10']]];
$projectionRules=['address_required'=>['cnpj'=>'N'],'receipts'=>['100001'=>['fields'=>['emitter'=>'S'],'value'=>'P']]];
$emitter=['cnpj'=>'12345678000199','name'=>'Emitente','errors'=>[]];
$projected=GnreTdadosProjection::project($projectionGuide,[$projectionItem],$projectionRules,$emitter);
t66(isset($projected['contribuinteEmitente']['identificacao']['CNPJ']),'snapshot que exige emitente deve projetá-lo');
t66(isset($projected['valorGNRE'])&&$projected['valorGNRE']==='10.00','valorGNRE deve usar a mesma totalização do XML');
$itemProjected=$projected['itensGNRE']['item'][0];t66(isset($itemProjected['documentoOrigem'],$itemProjected['contribuinteDestinatario'],$itemProjected['referencia'],$itemProjected['valor'],$itemProjected['camposExtras']),'campos condicionais aplicáveis devem ser projetados');
$noEmitter=$projectionRules;$noEmitter['receipts']['100001']['fields']['emitter']='N';$withoutEmitter=GnreTdadosProjection::project($projectionGuide,[$projectionItem],$noEmitter,$emitter);t66(!isset($withoutEmitter['contribuinteEmitente']),'snapshot sem emitente não pode inventá-lo');
$calculated=$projectionRules;$calculated['receipts']['100001']['value']='N';$noTotal=GnreTdadosProjection::project($projectionGuide,[array_merge($projectionItem,['GNRE006'=>[]])],$calculated,$emitter);t66(!isset($noTotal['valorGNRE'])&&GnreRealBatchCohort::describeParticipant($noTotal)['fragment']!=='','o fingerprint preserva TDadosGNRE válido mesmo sem total agregado');

$schema=(string)file_get_contents(__DIR__.'/../../../../scripts/atualizacaoBase.php');
$service=(string)file_get_contents(__DIR__.'/../src/GnreRealBatchCohort.php');
foreach(['CREATE TABLE IF NOT EXISTS GNRE010','CREATE TABLE IF NOT EXISTS GNRE011','UQ_GNRE011_Coorte_Guia','GNRE011_Decisao_Correlacao varchar(32) DEFAULT NULL','GNRE011_Candidatos_Correlacao mediumtext DEFAULT NULL'] as $needle)t66(strpos($schema,$needle)!==false,'schema Task 66 ausente: '.$needle);
foreach(['mysqli_begin_transaction','mysqli_rollback','FOR UPDATE','GNRE004','GNRE005','GNRE008'] as $needle)t66(strpos($service,$needle)!==false,'serviço deve proteger transação/histórico: '.$needle);
foreach(['INSERT INTO GNRE004','INSERT INTO GNRE005','INSERT INTO GNRE008','ReceptionClient','ResultClient'] as $needle)t66(strpos($service,$needle)===false,'Task 66 não pode criar XML, tentativa, fila, PDF ou chamar Portal: '.$needle);
t66(strpos($service,'GnreXmlGenerator())->generate')!==false&&strpos($service,'TDadosGNRE')!==false,'coorte deve congelar o TDadosGNRE extraído do XML individual validado');
t66(strpos($service,'function tdados(')===false,'coorte não pode reintroduzir montagem local legada de TDadosGNRE');
t66(strpos($service,'splitPhysicalLots')!==false&&strpos($service,'MAX_GUIDES_PER_PHYSICAL_LOT=30')!==false,'coorte deve dividir o lote nacional pelo limite operacional de 30 guias');
$entries=[];for($i=1;$i<=31;$i++)$entries[]=['guide'=>['GNRE001_Id'=>$i],'description'=>['physical_bytes'=>100]];
$lots=GnreRealBatchCohort::splitPhysicalLots($entries);t66(count($lots)===2&&count($lots[0])===30&&count($lots[1])===1,'31 guias devem gerar dois lotes físicos');
$smallLimit=[];for($i=1;$i<=21;$i++)$smallLimit[]=['guide'=>['GNRE001_Id'=>$i],'description'=>['physical_bytes'=>100,'guide_limit'=>20]];
t66(count(GnreRealBatchCohort::splitPhysicalLots($smallLimit))===2,'limite oficial menor que 30 deve prevalecer');
$itemLimit=[];for($i=1;$i<=3;$i++)$itemLimit[]=['guide'=>['GNRE001_Id'=>$i],'description'=>['physical_bytes'=>100,'item_count'=>2,'item_limit'=>3]];
t66(count(GnreRealBatchCohort::splitPhysicalLots($itemLimit))===3,'limite oficial de itens do lote deve prevalecer');
$bytes=[['guide'=>['GNRE001_Id'=>1],'description'=>['physical_bytes'=>613000]],['guide'=>['GNRE001_Id'=>2],'description'=>['physical_bytes'=>2000]]];t66(count(GnreRealBatchCohort::splitPhysicalLots($bytes))===2,'600 KB deve dividir antes de transmitir');
t66(strpos($service,"GNRE010_Situacao='PREPARADO'")!==false,'idempotência deve reutilizar somente coorte PREPARADO');
t66(strpos($service,'GNRE011_Decisao_Correlacao,GNRE011_Candidatos_Correlacao')!==false&&strpos($service,'NULL,NULL,NOW()')!==false,'decisão e candidatos devem começar nulos');
echo "Task 66 real batch cohort synthetic checks OK\n";
