<?php
namespace hardness;
if (PHP_SAPI !== 'cli') exit;
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/custom-indicator-engine.php';
function conferirSeries($ok, $message) { if (!$ok) throw new \RuntimeException($message); }
$graph = array('apresentacao' => 'padrao', 'calculo' => 'soma', 'campoQuantidade' => 'kg', 'seriesSelecionaveis' => array('habilitado' => true, 'series' => array(
    array('id' => 'grupo_a', 'titulo' => 'Grupo A', 'campoValor' => 'valorA', 'campoQuantidade' => 'kgA', 'cor' => '#123456'),
    array('id' => 'grupo_b', 'titulo' => 'Grupo B', 'campoValor' => 'valorB', 'campoQuantidade' => 'kgB', 'cor' => '#654321'),
)));
conferirSeries(dashboardTendenciaSeriesValidar($graph, $error), $error);
$sql = dashboardTendenciaSeriesSql($graph);
conferirSeries(strpos($sql, 'SUM(`valorA`)') !== false && strpos($sql, 'SUM(`kgB`)') !== false && strpos($sql, 'FROM') === false, 'Agrega os campos sem adicionar leituras.');
conferirSeries(dashboardTendenciaSeriesCampos(array('valor'), $graph) === array('valor', 'series'), 'Projecao da fonte compartilhada.');
$values = array('grupo_a' => array('valor' => '25.10', 'quantidade' => '1.234'), 'grupo_b' => array('valor' => 0, 'quantidade' => 0));
foreach (array($values, json_encode($values)) as $value) {
    $rows = dashboardTendenciaSeriesNormalizar(array(array('series' => $value)), $graph, $error);
    conferirSeries($rows !== false && $rows[0]['series']['grupo_a']['valor'] === 25.1 && $rows[0]['series']['grupo_b']['quantidade'] === 0.0, 'Leitura de JSON e objeto, com zeros validos.');
}
foreach (array(null, 'null', '{}', '{', array('grupo_a' => array('valor' => 1, 'quantidade' => 2)), array_merge($values, array('grupo_a' => array('valor' => 'x', 'quantidade' => 1)))) as $bad) {
    conferirSeries(dashboardTendenciaSeriesNormalizar(array(array('series' => $bad)), $graph, $error) === false, 'Nao transforma resposta incompleta em zeros.');
}
foreach (array(array('id' => 'id invalido'), array('campoValor' => 'valor);DROP TABLE T001'), array('cor' => 'red'), array('titulo' => ''), array('campoQuantidade' => '')) as $bad) {
    $invalid = $graph; $invalid['seriesSelecionaveis']['series'][0] = array_merge($invalid['seriesSelecionaveis']['series'][0], $bad);
    conferirSeries(!dashboardTendenciaSeriesValidar($invalid, $error), 'Valida nomes e campos antes de montar SQL.');
}
$duplicate = $graph; $duplicate['seriesSelecionaveis']['series'][1]['id'] = 'grupo_a';
conferirSeries(!dashboardTendenciaSeriesValidar($duplicate, $error), 'Rejeita IDs duplicados.');
conferirSeries(dashboardTendenciaSeriesSql(array()) === '' && dashboardTendenciaSeriesNormalizar(array(array('valor' => 10)), array(), $error) === array(array('valor' => 10)), 'Legado sem alteracao.');
echo "OK: series opcionais na mesma agregacao, validacao de campos, tipos, zeros e rejeicao de respostas parciais.\n";
