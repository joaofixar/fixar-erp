<?php
namespace hardness;
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/custom-indicator-engine.php';
if (!defined('MYSQLI_ASSOC')) define('MYSQLI_ASSOC', 1);
function endivCheck($condition, $message) { if (!$condition) throw new \RuntimeException($message); }
class EndivStatement {
    public $sql, $values = array();
    public function __construct($sql) { $this->sql = $sql; }
    public function bind_param($types, &...$values) { $this->values = $values; return true; }
}
function mysqli_prepare($connection, $sql) { return new EndivStatement($sql); }
function mysqli_stmt_execute($s) { $GLOBALS['endivQueries'][] = $s; return true; }
function mysqli_stmt_errno($s) { return 0; }
function mysqli_stmt_close($s) {}
function mysqli_fetch_all($rows, $mode) { return $rows; }
function mysqli_stmt_get_result($s) {
    $rows = $GLOBALS['endivRows'];
    if (strpos($s->sql, 'AS dashboard_barras_lista') !== false) {
        $month = ''; foreach ($s->values as $value) if (is_string($value) && preg_match('/^2026-[0-9]{2}$/D', $value)) $month = $value;
        $series = strpos($s->sql, "COALESCE(NULLIF(`conta`, ''), 'Não informado') = ?") !== false ? end($s->values) : '';
        $rows = array_values(array_filter($rows, function ($r) use ($month, $series) {
            return substr($r['dataVencimento'], 0, 7) === $month && ($series === '' || $r['conta'] === $series);
        }));
    }
    if (strpos($s->sql, 'AS dashboard_lista_total') !== false) {
        return array(array('totalLinhas' => count($rows), 'total0' => array_sum(array_column($rows, 'valorPago')),
            'total1' => array_sum(array_column($rows, 'valorAberto')), 'total2' => array_sum(array_column($rows, 'valorComprometido'))));
    }
    if (strpos($s->sql, 'AS dashboard_lista_pagina') !== false) {
        usort($rows, function ($a, $b) { return strcmp($a['dataVencimento'], $b['dataVencimento']) ?: $a['id'] - $b['id']; });
        if (preg_match('/ LIMIT ([0-9]+) OFFSET ([0-9]+)$/', $s->sql, $m)) return array_slice($rows, (int) $m[2], (int) $m[1]);
        return $rows;
    }
    if (strpos($s->sql, 'AS dashboard_barras') !== false) {
        $groups = array();
        foreach ($rows as $r) {
            $month = substr($r['dataVencimento'], 0, 7); $key = $month . '|' . $r['conta'];
            if (!isset($groups[$key])) $groups[$key] = array('periodo' => $month, 'serie' => $r['conta'], 'valor' => 0, 'quantidade' => 0);
            $groups[$key]['valor'] += $r['valorComprometido']; $groups[$key]['quantidade']++;
        }
        return array_values($groups);
    }
    if (strpos($s->sql, 'AS dashboard_base') !== false) return array(array('quantidade' => count($rows),
        'valor' => array_sum(array_column($rows, 'valorPago')), 'resumo0' => array_sum(array_column($rows, 'valorAberto')),
        'resumo1' => array_sum(array_column($rows, 'valorVencido')), 'resumo2' => array_sum(array_column($rows, 'valorAVencer'))));
    throw new \RuntimeException('Consulta inesperada: ' . $s->sql);
}
$GLOBALS['endivRows'] = array(); $GLOBALS['endivQueries'] = array();
function endivRow($id, $date, $account, $paid, $open) {
    return array('id' => $id, 'descricao' => 'Credor fictício', 'documento' => 'CONTRATO-' . $id, 'duplicata' => 'TIT-' . $id,
        'parcela' => '1/1', 'empresaNome' => 'Empresa fictícia', 'dataVencimento' => $date, 'conta' => $account,
        'valorPago' => $paid, 'valorAberto' => $open, 'valorVencido' => $open, 'valorAVencer' => 0, 'valorComprometido' => $paid + $open);
}
$GLOBALS['endivRows'][] = endivRow(1, '2026-01-10', 'Capital Giro', 100, 0);
for ($id = 2; $id <= 53; $id++) $GLOBALS['endivRows'][] = endivRow($id, '2026-11-15', 'Capital Giro', $id === 2 ? 2.25 : 0, $id === 2 ? 7.75 : 10);
$GLOBALS['endivRows'][] = endivRow(54, '2026-11-20', 'Consórcio', 25, 75);
$GLOBALS['endivRows'][] = endivRow(55, '2026-12-10', 'Capital Giro', 0, 200);
$indicator = json_decode(file_get_contents(__DIR__ . '/fixtures/endividamento.json'), true);
$input = array('f_dataInicio' => '2026-01-01', 'f_dataFim' => '2026-12-31');
$result = dashboardIndicadorExecutar(null, $indicator, 1, $input, $error);
endivCheck($result !== false, $error);
endivCheck(count($result['resumos']) === 3 && $result['card']['valor'] === 127.25, 'Pago inclui pagamentos parciais.');
endivCheck(abs(array_sum(array_column($result['grafico'], 'valor')) - (127.25 + $result['resumos'][0]['valor'])) < .001, 'Barras reconciliam pago e aberto, sem duplicar parcelas.');
endivCheck(count($result['listagem']) === 50 && $result['listagemPaginacao']['total'] === 55, 'Primeira página não trunca o total.');
endivCheck(dashboardIndicadorValorPadrao(array('padrao' => 'fim_ano')) === date('Y-12-31'), 'Data final acompanha o ano atual.');
if (in_array('--payload', $argv, true)) { echo json_encode($result, JSON_UNESCAPED_UNICODE); return; }
$selectedInput = $input + array('listaGraficoPeriodo' => '2026-11', 'listaGraficoSerie' => 'Capital Giro');
$GLOBALS['endivQueries'] = array();
$selected = dashboardIndicadorExecutar(null, $indicator, 1, $selectedInput + array('somenteListagem' => '1'), $error);
endivCheck($selected !== false, $error);
endivCheck(count($GLOBALS['endivQueries']) === 2, 'Clique consulta somente total e página da listagem.');
endivCheck($selected['listagemPaginacao']['total'] === 52 && $selected['listagemPaginacao']['totais']['valorComprometido'] === 520.0, 'Total inclui todas as parcelas da barra.');
endivCheck(!isset($selected['indicador']['listagem']['_barras']), 'Configuração interna não é publicada.');
endivCheck(strpos($selected['indicador']['listagem']['observacaoRodape'], '11/2026') !== false, 'Exportação identifica a seleção.');
endivCheck(in_array('Capital Giro', end($GLOBALS['endivQueries'])->values, true), 'Categoria é vinculada, não interpolada.');
if (in_array('--queries', $argv, true)) {
    echo json_encode(array('queries' => $GLOBALS['endivQueries'], 'rows' => $GLOBALS['endivRows'])); return;
}
$page = dashboardIndicadorExecutar(null, $indicator, 1, $selectedInput + array('somenteListagem' => '1', 'listaOffset' => '50'), $error);
endivCheck(count($page['listagem']) === 2 && $page['listagemPaginacao']['temMais'] === false, 'Próxima página conserva mês e categoria.');
$month = dashboardIndicadorExecutar(null, $indicator, 1, $input + array('listaGraficoPeriodo' => '2026-11', 'somenteListagem' => '1'), $error);
endivCheck($month['listagemPaginacao']['total'] === 53, 'Mês inteiro reúne as categorias.');
$export = dashboardIndicadorExecutar(null, $indicator, 1, $selectedInput, $error);
endivCheck($export['card'] === $result['card'] && $export['grafico'] === $result['grafico'], 'Seleção da lista mantém cartões e gráfico do período.');
$before = count($GLOBALS['endivQueries']);
foreach (array(array('listaGraficoPeriodo' => '2026-13'), array('listaGraficoPeriodo' => '2027-01'), array('listaGraficoPeriodo' => array('2026-11')),
    array('listaGraficoSerie' => 'Capital Giro'), array('listaGraficoPeriodo' => '2026-11', 'listaGraficoSerie' => array()), array('f_dataFim' => '2036-12-31')) as $invalid) {
    endivCheck(dashboardIndicadorExecutar(null, $indicator, 1, array_merge($input, $invalid), $error) === false, 'Rejeita seleção inválida.');
}
endivCheck($before === count($GLOBALS['endivQueries']), 'Seleções inválidas não consultam dados.');
$unsafe = $indicator; $unsafe['grafico']['campoGrupo'] = 'conta); DELETE';
endivCheck(dashboardIndicadorExecutar(null, $unsafe, 1, $input, $error) === false, 'Campo do gráfico validado.');
echo "OK: quatro cartões, parcelas parciais, gráfico mensal, clique filtra lista/paginação/totais, parâmetros, exportação e limites; sem base de cliente.\n";
