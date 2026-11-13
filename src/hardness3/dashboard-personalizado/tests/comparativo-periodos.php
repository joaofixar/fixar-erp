<?php
// Registros fictícios e mysqli simulado; não abre conexão nem lê credenciais.
namespace hardness;
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/custom-indicator-engine.php';
if (!defined('MYSQLI_ASSOC')) define('MYSQLI_ASSOC', 1);
if (!function_exists('mb_substr')) { function mb_substr($s, $a, $b, $encoding) { return substr($s, $a, $b); } }
class PeriodosStatement {
    public $sql, $values = array();
    function __construct($sql) { $this->sql = $sql; }
    function bind_param($types, &...$values) { $this->values = $values; return true; }
}
function mysqli_prepare($connection, $sql) { return new PeriodosStatement($sql); }
function mysqli_stmt_execute($s) {
    $GLOBALS['periodosQueries'][] = array($s->sql, $s->values);
    return empty($GLOBALS['periodosFailAt']) || count($GLOBALS['periodosQueries']) !== $GLOBALS['periodosFailAt'];
}
function mysqli_stmt_errno($s) { return 0; }
function mysqli_stmt_error($s) { return 'Falha simulada.'; }
function mysqli_stmt_close($s) {}
function mysqli_fetch_all($rows, $mode) { return $rows; }
function mysqli_stmt_get_result($s) {
    if (strpos($s->sql, 'AS dashboard_periodos') === false) return array();
    $inicio = $s->values[count($s->values) - 2];
    $fim = $s->values[count($s->values) - 1];
    $rows = array();
    foreach ($GLOBALS['periodosRecords'] as $data => $valor) {
        if ($data >= $inicio && $data <= $fim) $rows[] = array('data' => $data, 'valor' => $valor);
    }
    return $rows;
}
function checkPeriodos($condition, $message) { if (!$condition) throw new \RuntimeException($message); }
$indicator = require __DIR__ . '/fixtures/comparativo-periodos.php';
$graph = $indicator['grafico'];
$params = array('empresa' => 1, 'dataInicio' => '2026-10-01', 'dataFim' => '2026-10-03', 'maquina' => '0');
$GLOBALS['periodosQueries'] = array();
$GLOBALS['periodosRecords'] = array('2026-10-01' => 10, '2026-10-03' => 30, '2026-09-01' => 5, '2026-09-02' => 7, '2025-10-01' => 4, '2025-10-03' => 9);

if (isset($argv[1]) && $argv[1] === '--fixture') {
    $GLOBALS['periodosRecords'] = array();
    for ($day = 1; $day <= 19; $day++) {
        foreach (array('2026-10-' => 1, '2026-09-' => .8, '2025-10-' => .65) as $month => $factor) {
            $GLOBALS['periodosRecords'][$month . sprintf('%02d', $day)] = round((1400 + $day * 45 + sin($day * .8) * 400) * $factor, 3);
        }
    }
    foreach ($indicator['filtros'] as &$filter) unset($filter['opcoesSql'], $filter['opcoesCadastro']);
    unset($filter);
    $result = dashboardIndicadorExecutar(null, $indicator, 1, array('f_dataInicio' => '2026-10-01', 'f_dataFim' => '2026-10-19'), $error);
    checkPeriodos($result !== false, $error);
    $result['updatedAt'] = '19/10/2026 15:44 · Dados fictícios';
    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$original = $params;
$result = dashboardIndicadorConsultarComparativoPeriodos(null, $graph, $params, $error);
checkPeriodos($result !== false, $error);
checkPeriodos(array_column($result['series'], 'total') === array(40.0, 12.0, 13.0), 'Totais das três linhas.');
checkPeriodos($result['linhas'][1]['atual'] === 0.0 && $result['linhas'][1]['anoAnterior'] === 0.0, 'Dia sem produção é zero.');
checkPeriodos($params === $original && count($GLOBALS['periodosQueries']) === 3, 'Três leituras delimitadas sem alterar os filtros originais.');
foreach ($GLOBALS['periodosQueries'] as $query) {
    checkPeriodos(strpos($query[0], 'T257_Quantidade') !== false && strpos($query[0], 'T257A_Peso') === false, 'Comparação usa a quantidade apontada.');
    checkPeriodos(in_array(1, $query[1], true) && in_array('0', $query[1], true), 'Empresa e máquina permanecem parametrizadas.');
}
checkPeriodos($result['linhas'][0]['dataMesAnterior'] === '2026-09-01' && $result['linhas'][0]['dataAnoAnterior'] === '2025-10-01', 'Mesmos dias do mês e ano anteriores.');
checkPeriodos(dashboardPeriodosDataAnterior(new \DateTimeImmutable('2026-01-01'), 'mesAnterior') === '2025-12-01', 'Virada do ano.');

// O intervalo de leitura anterior pode conter dias sem correspondência: não entram no total.
$GLOBALS['periodosRecords'] = array('2024-01-28' => 1, '2024-01-29' => 2, '2024-01-30' => 999, '2024-01-31' => 999, '2024-02-01' => 3);
$boundary = array_merge($params, array('dataInicio' => '2024-02-28', 'dataFim' => '2024-03-01'));
$result = dashboardIndicadorConsultarComparativoPeriodos(null, $graph, $boundary, $error);
checkPeriodos($result['series'][1]['total'] === 6.0, 'Não soma dias que não correspondem ao período filtrado.');
checkPeriodos($result['linhas'][1]['anoAnterior'] === null, '29 de fevereiro sem correspondência no ano anterior.');
$GLOBALS['periodosQueries'] = array();
$boundary = array_merge($params, array('dataInicio' => '2026-03-31', 'dataFim' => '2026-03-31'));
$result = dashboardIndicadorConsultarComparativoPeriodos(null, $graph, $boundary, $error);
checkPeriodos($result['linhas'][0]['mesAnterior'] === null && $result['series'][1]['dataInicio'] === null, 'Não transforma 31 de março em 28 de fevereiro.');
checkPeriodos(count($GLOBALS['periodosQueries']) === 2, 'Sem leitura de um período sem datas correspondentes.');

foreach (array(array('dataInicio' => '2026-02-30'), array('dataInicio' => '2026-11-01'), array('dataInicio' => array()), array('dataInicio' => '2000-01-01')) as $bad) {
    $GLOBALS['periodosQueries'] = array();
    checkPeriodos(dashboardIndicadorConsultarComparativoPeriodos(null, $graph, array_merge($params, $bad), $error) === false, 'Rejeita período inválido ou excessivo.');
    checkPeriodos(!$GLOBALS['periodosQueries'], 'Validação acontece antes das leituras.');
}
foreach (array('SELECT T257_Quantidade FROM T257', 'DELETE FROM T257', 'SELECT T257_Quantidade FROM T257 WHERE T257_C004_Id = :empresa AND T257_Id = :desconhecido') as $sql) {
    $GLOBALS['periodosQueries'] = array();
    $bad = $graph; $bad['sqlBase'] = $sql;
    checkPeriodos(dashboardIndicadorConsultarComparativoPeriodos(null, $bad, $params, $error) === false && !$GLOBALS['periodosQueries'], 'Bloqueia fonte sem empresa, escrita e parâmetros desconhecidos.');
}
$GLOBALS['periodosQueries'] = array();
$GLOBALS['periodosFailAt'] = 2;
checkPeriodos(dashboardIndicadorConsultarComparativoPeriodos(null, $graph, $params, $error) === false && count($GLOBALS['periodosQueries']) === 2, 'Falha não apresenta comparativo parcial.');
unset($GLOBALS['periodosFailAt']);
$public = dashboardIndicadorDefinicaoPublica($indicator, array());
checkPeriodos(!isset($public['grafico']['sqlBase']), 'Fonte SQL não é exposta ao navegador.');
echo "OK: três séries, dias sem produção, calendário, ano bissexto, filtros, leituras delimitadas, validação e falhas sem resultados parciais.\n";
