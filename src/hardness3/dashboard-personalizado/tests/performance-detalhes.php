<?php
namespace hardness;
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/custom-indicator-engine.php';
if (!defined('MYSQLI_ASSOC')) define('MYSQLI_ASSOC', 1);

// Somente fixtures: não abre conexão, sessão ou arquivos de credenciais.
class DetalhesStatement {
    public $sql; public $valores = array();
    function __construct($sql) { $this->sql = $sql; }
    function bind_param($tipos, &...$valores) { $this->valores = $valores; return true; }
}
function mysqli_prepare($conn, $sql) { return new DetalhesStatement($sql); }
function mysqli_stmt_execute($stmt) { $GLOBALS['detalhesQueries'][] = array($stmt->sql, $stmt->valores); return empty($GLOBALS['detalhesFalha']); }
function mysqli_stmt_errno($stmt) { return 0; }
function mysqli_stmt_error($stmt) { return 'Falha ficticia'; }
function mysqli_stmt_close($stmt) {}
function mysqli_fetch_all($rows, $mode) { return $rows; }
function mysqli_stmt_get_result($stmt) {
    if (strpos($stmt->sql, 'AS dashboard_calendario_detalhamento') !== false) return array(array('id'=>1, 'data'=>'2026-10-10', 'valor'=>'120.25'));
    if (strpos($stmt->sql, 'SELECT DISTINCT D207_Data') === 0) return array(array('data'=>'2026-10-13'));
    if (strpos($stmt->sql, "SELECT 'receber'") === 0) return array(array('id'=>1,'data'=>'2026-10-10','serie'=>'receber','diasUteisAdicionais'=>2,'valor'=>'120.25'));
    if (strpos($stmt->sql, "SELECT 'pagar'") === 0) return array(array('id'=>2,'data'=>'2026-10-10','serie'=>'pagar','diasUteisAdicionais'=>0,'valor'=>'30.10'));
    if (strpos($stmt->sql, 'SELECT COUNT(*)') === 0) return array(array('quantidade'=>2,'valor'=>'150.35'));
    return array();
}
function checkDetalhes($ok, $message) { if (!$ok) throw new \RuntimeException($message); }
function runDetalhes($indicador, $entrada) {
    $GLOBALS['detalhesQueries'] = array();
    $result = dashboardIndicadorExecutar(null, $indicador, 7, $entrada, $error);
    return array($result, $GLOBALS['detalhesQueries'], $error);
}
$base = 'SELECT T255_Id AS id, T255_Quantidade_Saldo AS valor, T255_Observacao AS situacao, T255_Data_Emissao AS data FROM T255 WHERE T255_C004_Id = :empresa AND T255_Data_Emissao BETWEEN :dataInicio AND :dataFim';
$indicador = array('id'=>'fixture_detalhes','titulo'=>'Fixture', 'sqlBase'=>$base,
    'filtros'=>array(array('id'=>'dataInicio','tipo'=>'data','padrao'=>'2026-10-01'),array('id'=>'dataFim','tipo'=>'data','padrao'=>'2026-10-31')),
    'card'=>array('campo'=>'valor'), 'grafico'=>array('campoData'=>'data'),
    'composicao'=>array('campo'=>'situacao'), 'listagem'=>array('limite'=>10),
    'calendarios'=>array(array('id'=>'agenda','campoData'=>'data','campoValor'=>'valor','periodo'=>'proximos_30_dias','detalhamento'=>array('limite'=>25))));
$env = getenv('HARDNESS_DASHBOARD_DETAIL_FAST_PATH');
putenv('HARDNESS_DASHBOARD_DETAIL_FAST_PATH');
$entrada = array('detalheCalendarioId'=>'agenda','detalheCalendarioData'=>date('Y-m-d'));
list($full,$queriesFull) = runDetalhes($indicador,$entrada);
list($fast,$queriesFast) = runDetalhes($indicador,$entrada+array('somenteDetalhamento'=>'1'));
checkDetalhes($full!==false && $fast!==false && $full['calendarioDetalhes']===$fast['calendarioDetalhes'], 'Calendário mudou');
checkDetalhes(count($queriesFull)===6 && count($queriesFast)===1, 'Calendário deve reduzir 6 consultas para 1');
checkDetalhes($queriesFast[0]===$queriesFull[4], 'SQL/parâmetros/limite/período independente do calendário mudaram');
checkDetalhes($full['indicador']===$fast['indicador'] && in_array(7,$queriesFast[0][1],true), 'Empresa ou metadados mudaram');
foreach (array('ultimos_30_dias','filtrado') as $periodo) {
    $indicador['calendarios'][0]['periodo']=$periodo;
    list($full,$qFull)=runDetalhes($indicador,$entrada);
    list($fast,$qFast)=runDetalhes($indicador,$entrada+array('somenteDetalhamento'=>'1'));
    checkDetalhes($full['calendarioDetalhes']===$fast['calendarioDetalhes'] && $qFull[4]===$qFast[0], 'Período do calendário mudou: '.$periodo);
}
$indicador['grafico']=array('tipo'=>'barras_comparativas','considerarFeriadosNacionais'=>true,'limitarAoPeriodoFiltrado'=>true,
    'detalhamento'=>array('habilitado'=>true,'limite'=>25), 'sqlSeries'=>array(
        array('chave'=>'receber','sqlBase'=>"SELECT 'receber' AS serie, T255_Id AS id FROM T255 WHERE T255_C004_Id = :empresa AND T255_Data_Emissao BETWEEN :dataInicio AND :dataFim"),
        array('chave'=>'pagar','sqlBase'=>"SELECT 'pagar' AS serie, T255_Id AS id FROM T255 WHERE T255_C004_Id = :empresa AND T255_Data_Emissao BETWEEN :dataInicio AND :dataFim")));
$entrada = array('detalheGraficoSerie'=>'receber','detalheGraficoDataInicio'=>'2026-10-16','detalheGraficoDataFim'=>'2026-10-16');
list($full,$queriesFull) = runDetalhes($indicador,$entrada);
list($fast,$queriesFast) = runDetalhes($indicador,$entrada+array('somenteDetalhamento'=>'1'));
checkDetalhes($full!==false && $fast!==false && $full['graficoDetalhes']===$fast['graficoDetalhes'], 'Gráfico mudou');
checkDetalhes(count($fast['graficoDetalhes'])===1 && $fast['graficoDetalhes'][0]['dataProjetada']==='2026-10-16', 'Dias úteis/nacional/cadastrado não preservados');
checkDetalhes(count($queriesFull)===7 && count($queriesFast)===3, 'Gráfico deve executar somente fontes e feriados');
checkDetalhes($queriesFast===array_slice($queriesFull,2,3), 'Fontes, parâmetros ou feriados mudaram');
// Alterar período do clique não pode estreitar o período original das fontes.
$fora=$entrada; $fora['detalheGraficoDataInicio']='2026-11-01'; $fora['detalheGraficoDataFim']='2026-11-02';
list($semDados,$qFora)=runDetalhes($indicador,$fora+array('somenteDetalhamento'=>'1'));
checkDetalhes($semDados['graficoDetalhes']===array() && $qFora===$queriesFast, 'Clique alterou período da fonte');
putenv('HARDNESS_DASHBOARD_DETAIL_FAST_PATH=0');
list($legacy,$qLegacy)=runDetalhes($indicador,$entrada+array('somenteDetalhamento'=>'1'));
checkDetalhes($legacy===$full && $qLegacy===$queriesFull, 'Desativação deve restaurar fluxo completo');
putenv('HARDNESS_DASHBOARD_DETAIL_FAST_PATH');
foreach (array(array('ufMapa'=>'SP'),array('detalheGrupo'=>'outro'),array('paretoClasse'=>'A')) as $conflito) {
    checkDetalhes(dashboardIndicadorDetalheVisualDireto($indicador,$entrada+$conflito+array('somenteDetalhamento'=>'1'))==='', 'Contextos misturados aceitos');
}
foreach (array(array('detalheGraficoSerie'=>'receber','detalheGraficoDataInicio'=>'2026-02-30','detalheGraficoDataFim'=>'2026-03-01'),array('detalheCalendarioId'=>'agenda','detalheCalendarioData'=>'2026-02-30')) as $invalida) {
    list($result,$q)=runDetalhes($indicador,$invalida+array('somenteDetalhamento'=>'1'));
    checkDetalhes($result===false && !$q,'Data inválida executou consulta');
}
$invalid=$indicador; $invalid['grafico']['sqlSeries'][0]['sqlBase']='SELECT T255_Id AS id FROM T255';
list($result)=runDetalhes($invalid,$entrada+array('somenteDetalhamento'=>'1'));
checkDetalhes($result===false,'Fonte sem empresa aceita');
$geral=$indicador; $geral['carregamentoProgressivo']=true;
checkDetalhes(dashboardIndicadorDetalheVisualDireto($geral,$entrada+array('somenteDetalhamento'=>'1'))==='grafico','Modo geral impediu detalhes fora da carga');
$GLOBALS['dashboardCarga']=array('geral'=>true);
checkDetalhes(dashboardIndicadorDetalheVisualDireto($geral,$entrada+array('somenteDetalhamento'=>'1'))==='','Carga ativa não pode mudar de caminho');
unset($GLOBALS['dashboardCarga']);
$GLOBALS['detalhesFalha']=true;
list($result)=runDetalhes($indicador,$entrada+array('somenteDetalhamento'=>'1'));
checkDetalhes($result===false,'Falha apresentou detalhes parciais');
if ($env===false) putenv('HARDNESS_DASHBOARD_DETAIL_FAST_PATH'); else putenv('HARDNESS_DASHBOARD_DETAIL_FAST_PATH='.$env);
echo "OK: calendário 6 -> 1; gráfico 7 -> 3; paridade SQL/dados, períodos, dias úteis, empresa, limites, fallback e falhas.\n";
