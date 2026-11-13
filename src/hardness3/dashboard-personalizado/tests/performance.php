<?php
namespace hardness;
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/access.php';
require_once dirname(__DIR__) . '/custom-indicator-engine.php';
if (!defined('MYSQLI_ASSOC')) define('MYSQLI_ASSOC', 1);
class PerformanceStatement {
    public $sql; public $valores = array();
    function __construct($sql) { $this->sql = $sql; }
    function bind_param($tipos, &...$valores) { $this->valores = $valores; return true; }
}
function mysqli_prepare($conn, $sql) { return new PerformanceStatement($sql); }
function mysqli_stmt_execute($stmt) { $GLOBALS['perfConsultas'][] = array($stmt->sql, $stmt->valores); return empty($GLOBALS['perfFalhar']); }
function mysqli_stmt_errno($stmt) { return 0; }
function mysqli_stmt_error($stmt) { return 'erro ficticio'; }
function mysqli_stmt_get_result($stmt) {
    if (strpos($stmt->sql, 'AS dashboard_detalhamento') !== false) return array(array('grupo'=>'A', 'documento'=>101, 'valor'=>25));
    if (strpos($stmt->sql, 'D060') !== false) return $GLOBALS['perfOpcoes'];
    if (strpos($stmt->sql, 'COUNT(*) AS quantidade') !== false) return array(array('quantidade'=>1, 'valor'=>25));
    return array(array('id'=>1, 'grupo'=>'A', 'valor'=>25, 'quantidade'=>1));
}
function mysqli_fetch_all($rows, $mode) { return $rows; }
function mysqli_stmt_close($stmt) { return true; }
function checkPerf($ok, $msg) { if (!$ok) throw new \RuntimeException($msg); }
$originalEnv = array(getenv('HARDNESS_DASHBOARD_FILTER_CACHE_TTL'),getenv('HARDNESS_DASHBOARD_DETAIL_FAST_PATH'));
$base='SELECT T255_Id AS id, T255_Quantidade_Saldo AS valor, T255_Observacao AS grupo FROM T255 WHERE T255_C004_Id = :empresa AND (:vendedor = 0 OR T255_C007_Id = :vendedor)';
$i=array('id'=>'perf_teste','titulo'=>'Indicador ficticio','filtros'=>array(array('id'=>'vendedor','titulo'=>'Vendedor','tipo'=>'numero','padrao'=>7)), 'sqlBase'=>$base, 'card'=>array('campo'=>'valor'), 'grafico'=>array('habilitado'=>false), 'graficosPorGrupo'=>array('habilitado'=>false), 'composicao'=>array('habilitada'=>false), 'listagem'=>array('limite'=>50, 'agrupamento'=>array('campo'=>'grupo','campoValor'=>'valor'), 'detalhamento'=>array('modal'=>true,'campoGrupo'=>'grupo','sqlBase'=>$base, 'colunas'=>array(array('campo'=>'documento')))));
$entrada=array('detalheGrupo'=>'A');
$GLOBALS['perfConsultas']=array();
$full=dashboardIndicadorExecutar(null,$i,2,$entrada,$erro);
$fullQueries=$GLOBALS['perfConsultas'];
$GLOBALS['perfConsultas']=array();
$fast=dashboardIndicadorExecutar(null,$i,2,$entrada+array('somenteDetalhamento'=>'1'),$erro);
checkPerf($fast!==false && $full!==false && $fast['listagemDetalhes']===$full['listagemDetalhes'], 'Detalhes mudaram');
checkPerf(count($GLOBALS['perfConsultas'])===1 && count($fullQueries)===3, 'Detalhe deve reduzir de 3 consultas para 1');
checkPerf($GLOBALS['perfConsultas'][0]===$fullQueries[2], 'SQL ou parâmetros do detalhe mudaram');
checkPerf(in_array(2,$GLOBALS['perfConsultas'][0][1],true) && in_array(7.0,$GLOBALS['perfConsultas'][0][1],true), 'Empresa/responsável não preservados');
checkPerf($fast['indicador']===$full['indicador'], 'Metadados do detalhe mudaram');
$semLimite=$i;unset($semLimite['listagem']['limite']);
$GLOBALS['perfConsultas']=array();dashboardIndicadorExecutar(null,$semLimite,2,$entrada,$erro);
$defaultFull=$GLOBALS['perfConsultas'][2];
$GLOBALS['perfConsultas']=array();dashboardIndicadorExecutar(null,$semLimite,2,$entrada+array('somenteDetalhamento'=>'1'),$erro);
checkPerf($GLOBALS['perfConsultas'][0]===$defaultFull && strpos($defaultFull[0],'LIMIT 200')!==false,'Limite padrão mudou');
putenv('HARDNESS_DASHBOARD_DETAIL_FAST_PATH=0');
checkPerf(dashboardIndicadorExecutar(null,$i,2,$entrada+array('somenteDetalhamento'=>'1'),$erro)===$full, 'Desativação do caminho rápido mudou o legado');
putenv('HARDNESS_DASHBOARD_DETAIL_FAST_PATH');
$invalid=$i;$invalid['listagem']['detalhamento']['sqlBase']='SELECT T255_Id AS id FROM T255';
checkPerf(dashboardIndicadorExecutar(null,$invalid,2,$entrada+array('somenteDetalhamento'=>'1'),$erro)===false, 'Detalhe sem empresa aceito');
checkPerf(dashboardIndicadorExecutar(null,$i,2,array('detalheGrupo'=>str_repeat('x',201),'somenteDetalhamento'=>'1'),$erro)===false, 'Grupo inválido aceito');
$restrictedInput=array('f_vendedor'=>'999','detalheGrupo'=>'A','somenteDetalhamento'=>'1');
$restrictedBase=$i;
$restrictedBase['filtros'][0]=array('id'=>'vendedor','titulo'=>'Vendedor','tipo'=>'selecao','padrao'=>'','opcoes'=>array(array('valor'=>'9','titulo'=>'Ficticio')));
$restricted=dashboardPersonalizadoAplicarRestricaoUsuario($restrictedBase,array('vendedorId'=>9,'vendedorNome'=>'Ficticio'),$restrictedInput,$erro);
checkPerf($restricted!==false && $restrictedInput['f_vendedor']==='9','Responsável não foi forçado pelo servidor');
$r=dashboardIndicadorExecutar(null,$restricted,2,$restrictedInput,$erro);
checkPerf($r!==false && $r['listagemDetalhes']===$fast['listagemDetalhes'],'Detalhe restrito falhou');
// A restrição efetiva vem do endpoint; conferir também seu contrato no teste existente de acessos.
foreach (array('multiempresa','painelCotacoes') as $special) {
    $legacy=$i;$legacy[$special]=$special==='multiempresa'?true:array('carregamentoProgressivo'=>true);
    if ($special==='multiempresa') checkPerf(!dashboardIndicadorPodeDetalharDireto($legacy,$entrada+array('somenteDetalhamento'=>'1')), 'Especial deveria preservar fluxo legado');
}
$legacy=$i;$legacy['desempenhoComercial']=array('carregamentoProgressivo'=>true);
checkPerf(!dashboardIndicadorPodeDetalharDireto($legacy,$entrada+array('somenteDetalhamento'=>'1')), 'Lotes alterados');

require_once dirname(__DIR__) . '/../hardness/fpdf/fpdf.php';
$exportSource=file_get_contents(dirname(__DIR__).'/export.php');
$exportSource=substr($exportSource,strpos($exportSource,'function dashboardExportarErro'));
$exportSource=substr($exportSource,0,strpos($exportSource,'$autorizacao = dashboardPersonalizadoLerAutorizacao'));
eval('namespace hardness; '.$exportSource);
$exportFast=dashboardExportarPrepararDetalhamento($fast,'A');
$exportFull=dashboardExportarPrepararDetalhamento($full,'A');
checkPerf(dashboardExportarTabela($exportFast)===dashboardExportarTabela($exportFull),'Dados de exportação do detalhe mudaram');
checkPerf($exportFast['indicador']===$exportFull['indicador'],'Filtros ou títulos da exportação mudaram');

putenv('HARDNESS_DASHBOARD_FILTER_CACHE_TTL=30');
$sql='SELECT D060_Id AS valor, D060_Descricao_Grupo AS titulo FROM D060 INNER JOIN D001 ON D001_D060_Id = D060_Id INNER JOIN D009 ON D009_D001_Id = D001_Id WHERE D009_C004_Id = :empresa';
$auth=array('usuario'=>1,'empresa'=>2,'perfil'=>3);$access=array('perfis'=>array('3'=>array('perf_teste')));
$tenant=bin2hex(random_bytes(12));
$GLOBALS['perfOpcoes']=array(array('valor'=>'1','titulo'=>'Setor ficticio'));
dashboardFiltrosCacheContexto($tenant,$auth,$access,$i);
$ctx=$GLOBALS['dashboardFiltrosCache']['escopo'];
$file=dashboardFiltrosCacheDiretorio().'/'.hash('sha256',serialize(array($ctx,$sql,array('empresa'=>2)))).'.json';
$files=array($file);
$GLOBALS['perfConsultas']=array();
$a=dashboardFiltrosConsultar(null,$sql,array('empresa'=>2),$erro);
$b=dashboardFiltrosConsultar(null,$sql,array('empresa'=>2),$erro);
checkPerf($a===$b && count($GLOBALS['perfConsultas'])===1,'Cache não reutilizou cadastro');
checkPerf((fileperms($file)&0777)===0600,'Arquivo sem proteção');
$cached=json_decode(file_get_contents($file),true);$cached['expira']=time()-1;file_put_contents($file,json_encode($cached));
dashboardFiltrosConsultar(null,$sql,array('empresa'=>2),$erro);
checkPerf(count($GLOBALS['perfConsultas'])===2,'TTL ignorado');
$previous=$ctx;
foreach (array(array('outro-tenant',$auth,$access,$i),array($tenant,array('usuario'=>2,'empresa'=>2,'perfil'=>3),$access,$i),array($tenant,array('usuario'=>1,'empresa'=>4,'perfil'=>3),$access,$i),array($tenant,$auth,array('perfis'=>array()),$i),array($tenant,$auth,$access,array_merge($i,array('sqlBase'=>$base.' AND 1=1')))) as $args) {
    dashboardFiltrosCacheContexto(...$args);
    checkPerf($GLOBALS['dashboardFiltrosCache']['escopo']!==$previous,'Escopo compartilhado indevidamente');
    $previous=$GLOBALS['dashboardFiltrosCache']['escopo'];
    $GLOBALS['perfOpcoes']=array(array('valor'=>'scope-'.count($files),'titulo'=>'Escopo isolado'));
    $own=dashboardFiltrosConsultar(null,$sql,array('empresa'=>2),$erro);
    checkPerf($own===$GLOBALS['perfOpcoes'],'Cache vazou entre escopos');
    $files[]=dashboardFiltrosCacheDiretorio().'/'.hash('sha256',serialize(array($previous,$sql,array('empresa'=>2)))).'.json';
}
foreach (array('SELECT T005_Id AS valor FROM T005','SELECT C019_Id AS valor FROM C019','SELECT D060_Id AS valor FROM D060 WHERE NOW() > 0','SELECT D060_Id AS valor FROM D060 WHERE EXISTS (SELECT 1 FROM T005)') as $notCache) checkPerf(!dashboardFiltrosCacheElegivel($notCache),'Consulta não cadastral entrou no cache');
checkPerf(dashboardFiltrosCacheElegivel($sql),'Cadastro válido ignorado');
dashboardFiltrosCacheContexto($tenant,$auth,$access,$i);
putenv('HARDNESS_DASHBOARD_FILTER_CACHE_TTL=0');
$before=count($GLOBALS['perfConsultas']);dashboardFiltrosConsultar(null,$sql,array('empresa'=>2),$erro);dashboardFiltrosConsultar(null,$sql,array('empresa'=>2),$erro);
checkPerf(count($GLOBALS['perfConsultas'])===$before+2,'Cache não desativou');
putenv('HARDNESS_DASHBOARD_FILTER_CACHE_TTL=30');
// Nova opção selecionada deve forçar leitura e atualizar o cache, sem erro falso.
$withFilter=$i;$withFilter['filtros'][]=array('id'=>'setor','tipo'=>'selecao','padrao'=>'','opcoesSql'=>$sql);
$GLOBALS['perfOpcoes']=array(array('valor'=>'2','titulo'=>'Setor novo'));
$new=dashboardIndicadorExecutar(null,$withFilter,2,array('f_setor'=>'2'),$erro);
checkPerf($new!==false,'Cadastro novo rejeitado: '.$erro);
checkPerf($new['indicador']['filtros'][1]['opcoes'][0]['valor']==='2','Lista não foi renovada');
$before=count($GLOBALS['perfConsultas']);dashboardFiltrosConsultar(null,$sql,array('empresa'=>2),$erro);
checkPerf(count($GLOBALS['perfConsultas'])===$before,'Cache renovado não reutilizado');
// Falhas de consulta e respostas inválidas não podem ser armazenadas.
$errorSql=$sql.' ';
$errorFile=dashboardFiltrosCacheDiretorio().'/'.hash('sha256',serialize(array($ctx,$errorSql,array('empresa'=>2)))).'.json';
$GLOBALS['perfFalhar']=true;
checkPerf(dashboardFiltrosConsultar(null,$errorSql,array('empresa'=>2),$erro)===false && !is_file($errorFile),'Erro foi armazenado');
$GLOBALS['perfFalhar']=false;
$validOptions=$GLOBALS['perfOpcoes'];$GLOBALS['perfOpcoes']=array(array('sem_colunas'=>'invalido'));
checkPerf(dashboardFiltrosConsultar(null,$errorSql,array('empresa'=>2),$erro)===$GLOBALS['perfOpcoes'] && !is_file($errorFile),'Resposta inválida foi armazenada');
$GLOBALS['perfOpcoes']=$validOptions;
// Corrupção do arquivo volta à leitura normal.
file_put_contents($file,'{invalido');
checkPerf(dashboardFiltrosConsultar(null,$sql,array('empresa'=>2),$erro)===$GLOBALS['perfOpcoes'],'Cache inválido não recuperou');
foreach ($files as $file) @unlink($file);
foreach (array('HARDNESS_DASHBOARD_FILTER_CACHE_TTL','HARDNESS_DASHBOARD_DETAIL_FAST_PATH') as $n=>$env) {
    if ($originalEnv[$n]===false) putenv($env);else putenv($env.'='.$originalEnv[$n]);
}
echo "OK: detalhe idêntico (3 consultas -> 1), empresa/responsável, fallback, cache/TTL, isolamento e cadastro novo.\n";
