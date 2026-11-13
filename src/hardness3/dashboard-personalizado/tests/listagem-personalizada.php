<?php
// Registros fictícios e mysqli simulado; não abre conexão nem lê credenciais.
namespace hardness;
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/custom-indicator-engine.php';
if (!defined('MYSQLI_ASSOC')) define('MYSQLI_ASSOC', 1);
if (!function_exists('mb_substr')) { function mb_substr($s, $a, $b, $encoding) { return substr($s, $a, $b); } }

class ListagemStatement {
    public $sql, $values = array();
    function __construct($sql) { $this->sql = $sql; }
    function bind_param($types, &...$values) { $this->values = $values; return true; }
}
function mysqli_prepare($connection, $sql) { return new ListagemStatement($sql); }
function mysqli_stmt_execute($s) {
    $GLOBALS['listaQueries'][] = $s;
    return empty($GLOBALS['listaFailAt']) || count($GLOBALS['listaQueries']) !== $GLOBALS['listaFailAt'];
}
function mysqli_stmt_errno($s) { return 0; }
function mysqli_stmt_error($s) { return 'Falha simulada.'; }
function mysqli_stmt_close($s) {}
function mysqli_fetch_all($rows, $mode) { return $rows; }
function mysqli_stmt_get_result($s) {
    $rows = $GLOBALS['listaRows'];
    if (strpos($s->sql, 'AS dashboard_lista_total') !== false) {
        return array(array('totalLinhas' => count($rows), 'total0' => array_sum(array_column($rows, 'valor')),
            'total1' => array_sum(array_column($rows, 'quantidade'))));
    }
    if (strpos($s->sql, 'AS dashboard_lista_pagina') !== false) {
        if (preg_match('/ LIMIT (\d+) OFFSET (\d+)$/', $s->sql, $m)) return array_slice($rows, (int) $m[2], (int) $m[1]);
        return $rows;
    }
    return array(array('valor' => 220, 'quantidade' => 4));
}
function checkLista($condition, $message) { if (!$condition) throw new \RuntimeException($message); }

$lista = array(
    'sqlBase' => 'SELECT T007_Id AS id, T007_Valor_Total AS valor FROM T007 WHERE T007_C004_Id = :empresa AND YEAR(T007_Data_Emissao) = :ano',
    'paginacao' => array('habilitada' => true, 'tamanhoPagina' => 50, 'campoChave' => 'id'),
    'ordenacao' => array('campo' => 'valor', 'direcao' => 'DESC'),
    'rodapeTotais' => array('campos' => array('valor', 'quantidade'))
);
$indicator = array('id' => 'teste_listagem', 'titulo' => 'Teste', 'sqlBase' => $lista['sqlBase'],
    'filtros' => array(array('id' => 'ano', 'tipo' => 'numero', 'padrao' => 'ano_atual', 'inteiro' => true, 'minimo' => 1900, 'maximo' => 2100)),
    'card' => array('campo' => 'valor', 'campoContagem' => 'id'), 'composicao' => array('habilitada' => false),
    'grafico' => array('habilitado' => false), 'listagem' => $lista);
$GLOBALS['listaRows'] = array();
for ($id = 1; $id <= 125; $id++) $GLOBALS['listaRows'][] = array('id' => $id, 'valor' => $id === 1 ? 220 : 0, 'quantidade' => $id === 1 ? 4 : 0);
$GLOBALS['listaQueries'] = array();
$result = dashboardIndicadorExecutar(null, $indicator, 7, array('f_ano' => '2026'), $error);
checkLista($result !== false, $error);
checkLista(count($result['listagem']) === 50 && $result['listagemPaginacao']['total'] === 125, 'Primeira página deve carregar apenas 50 de 125.');
checkLista($result['listagemPaginacao']['totais'] === array('valor' => 220.0, 'quantidade' => 4.0), 'Totais devem abranger toda a tabela, inclusive campo quantidade.');
checkLista(strpos($GLOBALS['listaQueries'][0]->sql, 'COUNT(DISTINCT `id`)') !== false, 'Contagem configurada ignora marcadores de calendário sem id.');
checkLista(!isset($result['indicador']['listagem']['sqlBase']) && !isset($result['indicador']['listagem']['permitirUnionAll']), 'SQL fica somente no servidor.');
$ids = array_column($result['listagem'], 'id');
foreach (array(50 => 50, 100 => 25) as $offset => $quantity) {
    $GLOBALS['listaQueries'] = array();
    $page = dashboardIndicadorExecutar(null, $indicator, 7, array('f_ano' => '2026', 'somenteListagem' => '1', 'listaOffset' => (string) $offset), $error);
    checkLista($page !== false, $error);
    checkLista(count($page['listagem']) === $quantity && count($GLOBALS['listaQueries']) === 2 && !isset($page['card']), 'Mostrar mais consulta somente resumo e próxima página da tabela.');
    $query = $GLOBALS['listaQueries'][1];
    checkLista(strpos($query->sql, 'ORDER BY `valor` DESC, `id` ASC LIMIT 50 OFFSET ' . $offset) !== false, 'Desempate por chave mantém ordenação estável entre páginas.');
    checkLista($query->values === array(7, 2026.0), 'Empresa da sessão e ano seguem parametrizados.');
    $ids = array_merge($ids, array_column($page['listagem'], 'id'));
}
checkLista(count(array_unique($ids)) === 125 && !$page['listagemPaginacao']['temMais'], 'Páginas completas sem repetições.');
checkLista(dashboardIndicadorValorPadrao(array('padrao' => 'ano_atual')) === date('Y'), 'Ano padrão acompanha o calendário.');
foreach (array('2026.5', '1899', '2101', 'texto') as $year) {
    $GLOBALS['listaQueries'] = array();
    checkLista(dashboardIndicadorExecutar(null, $indicator, 7, array('f_ano' => $year), $error) === false && !$GLOBALS['listaQueries'], 'Ano inválido não consulta.');
}
foreach (array('-1', '50 OR 1=1', array('50'), '9999999999') as $offset) {
    $GLOBALS['listaQueries'] = array();
    checkLista(dashboardIndicadorConsultarListagemPaginada(null, $lista, array('empresa' => 7, 'ano' => 2026), array('listaOffset' => $offset), $error) === false && !$GLOBALS['listaQueries'], 'Offset inválido não consulta.');
}
foreach (array('DELETE FROM T007', 'SELECT T007_Id FROM T007', $lista['sqlBase'] . ' AND T007_Id = :inexistente') as $sql) {
    $bad = $lista; $bad['sqlBase'] = $sql; $GLOBALS['listaQueries'] = array();
    checkLista(dashboardIndicadorConsultarListagemPaginada(null, $bad, array('empresa' => 7, 'ano' => 2026), array(), $error) === false && !$GLOBALS['listaQueries'], 'Fonte inválida não consulta.');
}
$bad = $lista; $bad['paginacao']['campoChave'] = 'id;DROP'; $GLOBALS['listaQueries'] = array();
checkLista(dashboardIndicadorConsultarListagemPaginada(null, $bad, array('empresa' => 7, 'ano' => 2026), array(), $error) === false && !$GLOBALS['listaQueries'], 'Chave inválida é recusada antes da leitura.');
for ($id = 126; $id <= 626; $id++) $GLOBALS['listaRows'][] = array('id' => $id, 'valor' => 0, 'quantidade' => 0);
$page = dashboardIndicadorConsultarListagemPaginada(null, $lista, array('empresa' => 7, 'ano' => 2026), array('listaOffset' => '600'), $error);
checkLista(count($page['linhas']) === 26 && $page['paginacao']['total'] === 626, 'Paginação ultrapassa o limite legado de 500.');
define('HARDNESS_DASHBOARD_EXPORTAR_LISTAGEM_COMPLETA', true);
$export = dashboardIndicadorExecutar(null, $indicator, 7, array('f_ano' => '2026'), $error);
checkLista(count($export['listagem']) === 626 && !$export['listagemPaginacao']['temMais'], 'Exportação recebe todas as páginas.');
$GLOBALS['listaQueries'] = array(); $GLOBALS['listaFailAt'] = 2;
checkLista(dashboardIndicadorConsultarListagemPaginada(null, $lista, array('empresa' => 7, 'ano' => 2026), array(), $error) === false, 'Falha não apresenta página parcial.');
unset($GLOBALS['listaFailAt']);
$GLOBALS['listaRows'] = array(); $GLOBALS['listaQueries'] = array();
$empty = dashboardIndicadorConsultarListagemPaginada(null, $lista, array('empresa' => 7, 'ano' => 2026), array(), $error);
checkLista($empty['linhas'] === array() && $empty['paginacao']['total'] === 0 && count($GLOBALS['listaQueries']) === 1, 'Tabela vazia evita leitura adicional.');
$disabled = $indicator; unset($disabled['listagem']['paginacao']);
checkLista(dashboardIndicadorExecutar(null, $disabled, 7, array('somenteListagem' => '1'), $error) === false, 'Paginação exige configuração explícita.');
echo "OK: páginas 50/100/125, totais completos, exportação de 626 registros, filtros, desempate, validação e falhas sem resultados parciais.\n";
