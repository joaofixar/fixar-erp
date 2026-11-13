<?php
namespace hardness;
if(PHP_SAPI!=='cli') { http_response_code(404);exit; }
// Executa a montagem real dos lotes; o acesso ao banco é substituído por captura.
require dirname(__DIR__).'/carregamento-progressivo.php';
require dirname(__DIR__).'/painel-cotacoes.php';
function dashboardIndicadorConsultarDireto($connection,$sql,$params,&$error) {
    $GLOBALS['sqlCapturado']=array('sql'=>$sql,'parametros'=>$params);
    $GLOBALS['dashboardConsultaErroNumero']=0;$error='Captura local, sem conexao';return false;
}
$document=json_decode(file_get_contents(__DIR__.'/fixtures/painel-cotacoes.json'),true);
foreach($argv as $argument) if(strpos($argument,'--config=')===0) $document=json_decode(file_get_contents(substr($argument,9)),true);
$indicators=array_values(array_filter($document['indicadores'],function($i) { return !empty($i['painelCotacoes']['habilitado']); }));
if(count($indicators)!==1) throw new \RuntimeException('A fixture deve conter um Painel de Cotações.');
$config=$indicators[0]['painelCotacoes'];
$config['atribuirClienteProposta']=true;
$config['_empresas']=$config['empresasConfiguradas'];
foreach($config['_empresas'] as $index=>&$company) {
    // Empresas fictícias, sem resolver CNPJ nem consultar um servidor.
    if(empty($company['id'])) $company['id']=5;
    foreach(array('clientesTransferencia','servicosIndustrializacao') as $key) if(!isset($company[$key])) $company[$key]=array();
}
unset($company);
$queries=array();
foreach(array('orcamentos','pedidos','notas') as $type) {
    $queries[$type.'_periodo']=dashboardCotacoesSql($type,$config);
    $queries[$type.'_historico']=dashboardCotacoesSql($type,$config,true);
    $queries[$type.'_toneladas']=dashboardCotacoesSqlToneladas($type,$config);
    $queries[$type.'_toneladas_historico']=dashboardCotacoesSqlToneladas($type,$config,true);
}
$results=array();
foreach(array(array(0,0),array(7,0),array(70,1)) as $scope) foreach($queries as $name=>$sql) {
    $sql=preg_replace('/=\s*:empresa\b/',' IN (1,5)',$sql);
    $params=array('empresa'=>1,'dataInicio'=>'2026-04-01','dataFim'=>'2026-04-15','servicosInicio'=>'2025-07-20','restrito'=>$scope[0],'equipeRestrita'=>$scope[1]);
    $GLOBALS['dashboardCarga']=array('estado'=>array('consultas'=>array()),'leitura'=>false);
    $error='';dashboardCargaConsultar(null,$sql,$params,$error);
    $first=$GLOBALS['sqlCapturado'];
    $keys=array_keys($GLOBALS['dashboardCarga']['estado']['consultas']);
    $GLOBALS['dashboardCarga']['estado']['consultas'][$keys[0]]['offset']=2000;
    dashboardCargaConsultar(null,$sql,$params,$error);
    $results[$scope[0].'_'.$scope[1].'_'.$name]=array('base'=>$sql,'pagina1'=>$first,'pagina2'=>$GLOBALS['sqlCapturado']);
    unset($GLOBALS['dashboardCarga']);
}
echo json_encode($results,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT),"\n";
