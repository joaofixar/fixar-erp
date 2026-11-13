<?php
// Exercita o motor com mysqli simulado. Não abre conexão ou lê credenciais.
namespace hardness;
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/custom-indicator-engine.php';
if (!defined('MYSQLI_ASSOC')) define('MYSQLI_ASSOC', 1);
if (!function_exists('mb_substr')) { function mb_substr($s, $a, $b, $encoding) { return substr($s, $a, $b); } }
class UnionStatement {
    public $sql, $values = array();
    function __construct($sql) { $this->sql = $sql; }
    function bind_param($types, &...$values) { $this->values = $values; return true; }
}
function mysqli_prepare($connection, $sql) { return new UnionStatement($sql); }
function mysqli_stmt_execute($stmt) { $GLOBALS['unionQueries'][] = $stmt; return true; }
function mysqli_stmt_errno($stmt) { return 0; }
function mysqli_stmt_close($stmt) {}
function mysqli_fetch_all($rows, $mode) { return $rows; }
function mysqli_stmt_get_result($stmt) { return array(array('valor' => 15, 'quantidade' => 2)); }
function checkUnion($ok, $message) { if (!$ok) throw new \RuntimeException($message); }
$plain = 'SELECT T007_Id AS id, T007_Valor_Total AS valor FROM T007 WHERE T007_C004_Id = :empresa';
$union = $plain . ' UNION ALL ' . $plain;
$indicator = array('id' => 'teste_union', 'titulo' => 'Teste', 'sqlBase' => $union,
    'card' => array('campo' => 'valor'), 'grafico' => array('habilitado' => false),
    'composicao' => array('habilitada' => false), 'listagem' => array('habilitada' => false),
    'secoes' => array(array('titulo' => 'Bloco', 'sqlBase' => $union,
        'colunas' => array(array('campo' => 'valor', 'titulo' => 'Valor')))));
$GLOBALS['unionQueries'] = array();
checkUnion(dashboardIndicadorExecutar(null, $indicator, 7, array(), $error) === false
    && !$GLOBALS['unionQueries'], 'Raiz sem autorização deve rejeitar UNION antes da consulta.');
$indicator['permitirUnionAll'] = true;
$GLOBALS['unionQueries'] = array();
checkUnion(dashboardIndicadorExecutar(null, $indicator, 7, array(), $error) === false
    && count($GLOBALS['unionQueries']) === 1, 'Autorização na raiz não deve autorizar a seção.');
$indicator['secoes'][0]['permitirUnionAll'] = true;
$GLOBALS['unionQueries'] = array();
$result = dashboardIndicadorExecutar(null, $indicator, 7, array(), $error);
checkUnion($result !== false, $error);
checkUnion(count($GLOBALS['unionQueries']) === 2 && count($result['secoes']) === 1,
    'Raiz e seção autorizadas devem executar seus próprios SQLs.');
foreach ($GLOBALS['unionQueries'] as $stmt) {
    checkUnion($stmt->values === array(7, 7), 'Cada ramo mantém empresa parametrizada.');
}
checkUnion(!isset($result['indicador']['permitirUnionAll']) && !isset($result['secoes'][0]['permitirUnionAll']),
    'Permissões SQL permanecem no servidor.');
foreach (array(str_replace('UNION ALL', 'UNION', $union), $union . '; DELETE FROM T007',
    $union . ' AND SLEEP(1) = 0', str_replace('T007_Valor_Total', 'T007_password', $union),
    str_replace('FROM T007', 'FROM INFORMATION_SCHEMA.TABLES', $union),
    str_replace('T007_C004_Id = :empresa', '1=1', $union)) as $invalid) {
    checkUnion(!dashboardIndicadorValidarSql($invalid, $error, false, true),
        'A opção UNION ALL não deve relaxar as demais restrições: ' . $invalid);
}
$indicator['sqlBase'] = $plain;
unset($indicator['permitirUnionAll']);
$indicator['secoes'][0]['sqlBase'] = $plain;
unset($indicator['secoes'][0]['permitirUnionAll']);
checkUnion(dashboardIndicadorExecutar(null, $indicator, 7, array(), $error) !== false,
    'SELECT legado sem opção deve continuar funcionando.');
echo "OK: UNION ALL na raiz e nos blocos exige opção independente; demais restrições preservadas.\n";
