<?php
declare(strict_types=1);
require_once __DIR__.'/../src/NfGnreDraftCreator.php';
function t61(bool $condition,string $message):void {if(!$condition)throw new RuntimeException('Task 61: '.$message);}
function component61(array $fact,string $tax):array {foreach(GnreNfDraftCreator::components($fact) as $component)if($component['tax']===$tax)return $component;return [];}
$st=component61(['indicator_st'=>true,'T007_Valor_ICMS_Substituicao_Retencao'=>'10.25'],'ST');
t61($st['receipt']==='100099'&&$st['value_type']==='11'&&$st['value']==='10.25','ST deve usar receita 100099 e valor principal decimal.');
$stWithFcp=component61(['indicator_st'=>true,'T007_Valor_ICMS_Substituicao_Retencao'=>'10.25','T007_Valor_FCP_ST'=>'1.75','T008_Valor_FCP_ST'=>'999.99'],'ST');t61($stWithFcp['fcp_st_value']==='1.75'&&GnreNfDraftCreator::valueLinesForComponent($stWithFcp,true)===[['11','10.25'],['12','1.75']],'ST deve gravar principal 11 e FCP-ST 12 exclusivamente do total T007.');
$difal=component61(['indicator_difal'=>true,'T007_Valor_Total_Partilha_ICMS_Destino'=>'20.10'],'DIFAL');
t61($difal['receipt']==='100102'&&$difal['value_type']==='11'&&$difal['value']==='20.10','DIFAL deve usar receita 100102 e valor principal decimal.');
$fcp=component61(['indicator_fcp'=>true,'T007_Valor_Total_FCP_Partilha'=>'3.33'],'FCP');
t61($fcp['receipt']==='100129'&&$fcp['value_type']==='11'&&$fcp['value']==='3.33','FCP fora do RJ deve usar receita 100129 e valor principal próprio.');
t61(component61(['indicator_st'=>true,'T007_Valor_ICMS_Substituicao_Retencao'=>'0.00'],'ST')===[],'Total zero não cria componente.');
$source=(string)file_get_contents(__DIR__.'/../src/NfGnreDraftCreator.php');$reader=(string)file_get_contents(__DIR__.'/../src/NfGnreEligibilityReader.php');
foreach(['GnreConfigUfRules::receipt',"'receipt'=>'100099'",'GNRE003','GNRE006','gnre_task15_retotal','mysqli_rollback','America/Sao_Paulo','GNRE002_T007_Id','GNRE002_Tributo_Origem'] as $needle)t61(strpos($source,$needle)!==false,'Contrato de persistência/rollback ausente: '.$needle);
foreach(['GNRE002_Tributo_Origem=\'ST\'','GNRE002_Tributo_Origem=\'DIFAL\'','GNRE002_Tributo_Origem=\'FCP\'','GNRE001_C004_Id=n.T007_C004_Id'] as $needle)t61(strpos($reader,$needle)!==false,'Listagem deve manter somente componentes pendentes na empresa ativa: '.$needle);
foreach(['Portal','XmlGenerator','ReceptionClient','OfficialPdf','Financeiro','VEN012'] as $forbidden)t61(strpos($source,$forbidden)===false,'Criação assistida não pode acionar fluxo externo: '.$forbidden);
foreach(["'tax'=>'DIFAL+FCP'","'status'=>'ready'","\$entry['types']=['0']","A geração assistida aceita somente Guia simples", "self::persistItem(\$db,\$guideId,\$documentId,\$parts['DIFAL']", "\$fcpType='12'", "GNRE002_Tributo_Origem"] as $needle)t61(strpos($source,$needle)!==false,'Composição RJ/tipo simples ausente: '.$needle);
t61(strpos($source,"'value_type'=>'11']];")!==false,'FCP isolado não pode voltar a usar o tipo complementar.');
echo "Task 61 assisted fill synthetic: OK\n";
