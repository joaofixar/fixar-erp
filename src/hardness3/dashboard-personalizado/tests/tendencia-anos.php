<?php
namespace hardness;
if (PHP_SAPI !== 'cli') exit;
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/custom-indicator-engine.php';
if (!defined('MYSQLI_ASSOC')) define('MYSQLI_ASSOC', 1);
class TendenciaAnoStatement {
    public $sql; public $valores = array();
    function __construct($sql) { $this->sql = $sql; }
    function bind_param($tipos, &...$valores) { $this->valores = $valores; return true; }
}
function mysqli_prepare($conexao, $sql) { return new TendenciaAnoStatement($sql); }
function mysqli_stmt_execute($stmt) { $GLOBALS['anoQueries'][] = array($stmt->sql, $stmt->valores); return empty($GLOBALS['anoFail']); }
function mysqli_stmt_errno($stmt) { return 0; }
function mysqli_stmt_error($stmt) { return 'falha fictícia'; }
function mysqli_stmt_get_result($stmt) { return $GLOBALS['anoRows']; }
function mysqli_fetch_all($rows, $mode) { return $rows; }
function mysqli_stmt_close($stmt) { return true; }
function checkAno($ok, $message) { if (!$ok) throw new \RuntimeException($message); }
$base = 'SELECT T007_Id AS id, T007_Data_Emissao AS data, T007_Valor_Total_Nota AS valor, 10 AS kg, 20 AS valorA, 2 AS kgA, 30 AS valorB, 3 AS kgB'
    . ' FROM T007 WHERE T007_C004_Id = :empresa AND T007_Data_Emissao >= :dataInicio AND T007_Data_Emissao < DATE_ADD(:dataFim, INTERVAL 1 DAY)'
    . " AND (:visaoNegocio = 'todas' OR :visaoNegocio = 'aco')";
$graph = array('apresentacao' => 'padrao', 'campoData' => 'data', 'campoValor' => 'valor', 'campoQuantidade' => 'kg',
    'comparativoAnos' => array('habilitado' => true, 'anos' => array(2023, 2024, 2025)),
    'seriesSelecionaveis' => array('habilitado' => true, 'series' => array(
        array('id' => 'grupo_a', 'titulo' => 'Aço', 'campoValor' => 'valorA', 'campoQuantidade' => 'kgA', 'cor' => '#123456'),
        array('id' => 'grupo_b', 'titulo' => 'Serviço', 'campoValor' => 'valorB', 'campoQuantidade' => 'kgB', 'cor' => '#654321')
    )));
$indicator = array('id' => 'tendencia_ano_teste', 'titulo' => 'Fictício', 'sqlBase' => $base, 'grafico' => $graph, 'filtros' => array(
    array('id' => 'dataInicio', 'tipo' => 'data', 'padrao' => 'inicio_mes'),
    array('id' => 'dataFim', 'tipo' => 'data', 'padrao' => 'hoje'),
    array('id' => 'visaoNegocio', 'tipo' => 'selecao', 'padrao' => 'todas', 'opcoes' => array(array('valor' => 'todas', 'titulo' => 'Todas'), array('valor' => 'aco', 'titulo' => 'Aço')))
));
$input = array('somenteComparativoTendencia' => '1', 'f_dataInicio' => '2026-11-01', 'f_dataFim' => '2026-11-19', 'f_visaoNegocio' => 'aco');
$params = array('empresa' => 5, 'dataInicio' => '2026-11-01', 'dataFim' => '2026-11-19', 'visaoNegocio' => 'aco');
$series = array('grupo_a' => array('valor' => '20.10', 'quantidade' => '2.444'), 'grupo_b' => array('valor' => 30, 'quantidade' => 3));
foreach (array(2023, 2024, 2025) as $year) {
    $GLOBALS['anoQueries'] = array();
    $GLOBALS['anoRows'] = array(array('periodo' => $year . '-11-01', 'valor' => '50.10', 'quantidade' => '5.444', 'series' => json_encode($series)));
    $result = dashboardIndicadorExecutar(null, $indicator, 5, $input + array('anoComparativoTendencia' => (string) $year), $error);
    checkAno($result !== false, $error);
    checkAno(array_keys($result) === array('comparativoTendencia'), 'Não deve recalcular cartões, calendário ou outras partes do indicador.');
    $comparison = $result['comparativoTendencia'];
    checkAno($comparison['atual'] === array('inicio' => '2026-11-01', 'fim' => '2026-11-19'), 'Período atual permanece intacto.');
    checkAno($comparison['anterior'] === array('inicio' => $year . '-11-01', 'fim' => $year . '-11-19') && $comparison['ano'] === $year && $comparison['rotulo'] === (string) $year, 'Mesmo intervalo no ano escolhido.');
    checkAno(count($GLOBALS['anoQueries']) === 1, 'Uma leitura atende valores, quantidades e todas as atividades.');
    list($sql, $values) = $GLOBALS['anoQueries'][0];
    foreach (array(5, 'aco', $year . '-11-01', $year . '-11-19') as $value) checkAno(in_array($value, $values, true), 'Empresa, visão e período devem permanecer vinculados.');
    checkAno(strpos($sql, $year . '-11-01') === false && strpos($sql, 'LIMIT 501') !== false, 'Sem interpolação de datas e sem truncamento silencioso.');
    checkAno($comparison['linhas'][0]['series']['grupo_a']['quantidade'] === 2.444, 'Quantidade mantém precisão original.');
}
$plan = dashboardTendenciaAnoPlanejar($indicator, $base, $params, 2023, $error);
checkAno($params['dataInicio'] === '2026-11-01', 'Não altera parâmetros do chamador.');
$months = dashboardTendenciaAnoPlanejar($indicator, $base, array_merge($params, array('dataInicio' => '2026-08-15')), 2023, $error);
checkAno($months['comparativo']['agrupamento'] === 'mes' && $months['parametros']['dataInicio'] === '2023-08-15' && $months['parametros']['dataFim'] === '2023-11-19', 'Preserva meses e limites parciais.');
$cross = dashboardTendenciaAnoPlanejar($indicator, $base, array_merge($params, array('dataInicio' => '2026-12-15', 'dataFim' => '2027-02-10')), 2023, $error);
checkAno($cross['parametros']['dataInicio'] === '2023-12-15' && $cross['parametros']['dataFim'] === '2024-02-10' && $cross['comparativo']['rotulo'] === '2023–2024', 'Ano escolhido é o início do intervalo na virada do ano.');
$leap = dashboardTendenciaAnoPlanejar($indicator, $base, array_merge($params, array('dataInicio' => '2024-02-29', 'dataFim' => '2024-03-02')), 2023, $error);
checkAno($leap['parametros']['dataInicio'] === '2023-02-28' && $leap['parametros']['dataFim'] === '2023-03-02', '29 de fevereiro limita ao último dia válido no ano escolhido.');
$nested = 'SELECT origem.id, origem.data, origem.valor, origem.kg, origem.valorA, origem.kgA, origem.valorB, origem.kgB'
    . ' FROM (WITH R981 AS (' . $base . ') SELECT id, data, valor, kg, valorA, kgA, valorB, kgB FROM R981) AS origem';
$cte = dashboardTendenciaAnoPlanejar($indicator, $nested, $params, 2023, $error);
checkAno($cte !== false && strpos($cte['sql'], 'WITH dashboard_comparativo_parametros AS') === 0, 'Preserva correção dos parâmetros em CTEs MySQL 8: ' . $error);
dashboardIndicadorPrepararSql($cte['sql'], $cte['parametros'], $values, $types, $error);
checkAno($error === '' && in_array('2023-11-01', $values, true) && substr_count($cte['sql'], ':dataInicio') === 1, 'Parâmetros históricos são vinculados uma única vez nas CTEs.');
foreach (array(2022, '2023 OR 1=1', array(2023), null, false, 2023.5) as $bad) {
    $GLOBALS['anoQueries'] = array();
    checkAno(dashboardIndicadorExecutar(null, $indicator, 5, $input + array('anoComparativoTendencia' => $bad), $error) === false && !$GLOBALS['anoQueries'], 'Ano fora da lista ou malformado não consulta.');
}
foreach (array(array('dataInicio' => '2026-02-30'), array('dataFim' => '2026-01-01'), array('dataInicio' => '2000-01-01')) as $bad) {
    $GLOBALS['anoQueries'] = array();
    checkAno(dashboardTendenciaAnoConsultar(null, $indicator, $base, array_merge($params, $bad), 2023, $error) === false && !$GLOBALS['anoQueries'], 'Períodos inválidos ou excessivos não consultam.');
}
$disabled = $indicator; unset($disabled['grafico']['comparativoAnos']);
$GLOBALS['anoQueries'] = array();
checkAno(dashboardTendenciaAnoConsultar(null, $disabled, $base, $params, 2023, $error) === false && !$GLOBALS['anoQueries'], 'Sem opt-in, o ano não é consultável.');
$GLOBALS['anoRows'] = array();
checkAno(dashboardTendenciaAnoConsultar(null, $indicator, $base, $params, 2023, $error)['linhas'] === array(), 'Ausência real de movimentos é aceita como série vazia.');
$valid = array('periodo' => '2023-11-01', 'valor' => 50, 'quantidade' => 5, 'series' => $series);
foreach (array(array_merge($valid, array('periodo' => '2024-11-01')), array_merge($valid, array('series' => array())), array_merge($valid, array('quantidade' => 'x')), array_merge($valid, array('valor' => INF))) as $bad) {
    $GLOBALS['anoRows'] = array($bad);
    checkAno(dashboardTendenciaAnoConsultar(null, $indicator, $base, $params, 2023, $error) === false, 'Resposta inválida não vira zeros.');
}
$GLOBALS['anoRows'] = array($valid, $valid);
checkAno(dashboardTendenciaAnoConsultar(null, $indicator, $base, $params, 2023, $error) === false, 'Não aceita pontos duplicados.');
$GLOBALS['anoRows'] = array_fill(0, 501, $valid);
checkAno(dashboardTendenciaAnoConsultar(null, $indicator, $base, $params, 2023, $error) === false, 'Não exibe série truncada.');
$GLOBALS['anoFail'] = true;
checkAno(dashboardTendenciaAnoConsultar(null, $indicator, $base, $params, 2023, $error) === false, 'Falhas de leitura não são apresentadas como ausência de faturamento.');
echo "OK: 2023/2024/2025 sob demanda, uma consulta, filtros preservados, meses parciais, virada de ano, bissexto, CTEs MySQL 8, limites e falhas sem zeros falsos.\n";
