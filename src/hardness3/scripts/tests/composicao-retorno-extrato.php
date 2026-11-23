<?php
namespace hardness;

// Testes isolados: nenhuma configuracao ou conexao do ERP e carregada.
error_reporting(E_ALL);
set_error_handler(function ($n, $m, $f, $l) { throw new \ErrorException($m, 0, $n, $f, $l); });
require_once __DIR__ . '/../../bibliotecas/classes/ComposicaoRetornoExtrato.php';
require_once __DIR__ . '/../../bibliotecas/funcoesCallback/composicaoRetornoExtrato.php';

$verificacoes = 0;
function conferir($condicao, $mensagem) {
    global $verificacoes;
    if (!$condicao) { throw new \RuntimeException($mensagem); }
    $verificacoes++;
}
function credito($id = 101, $valor = '100.00', $data = '2026-01-15') {
    return array('movimento_id' => $id, 'valor_movimento' => $valor, 'data_movimento' => $data);
}
function evento($id, $valor, $retorno = 500, $credito = '2026-01-15', $pagamento = '2026-01-14', $banco = 341) {
    return array('retorno_id' => $retorno, 'arquivo_retorno' => 'TESTE.RET', 'codigo_banco' => $banco,
        'registro_retorno_id' => $id, 'titulo_id' => $id + 1000, 'recebido_no_retorno' => $valor,
        'credito_no_retorno' => $credito, 'pagamento_no_retorno' => $pagamento,
        'duplicata' => 'TESTE-' . $id, 'empresa_titulo' => 'EMPRESA TESTE', 'cliente' => 'CLIENTE TESTE');
}
function base() {
    global $cabecalho, $creditos, $eventos, $consultas, $gravacoes, $falha;
    $cabecalho = array('extrato_id' => 10, 'empresa_id' => 7, 'conta_bancaria_id' => 20, 'conta_bancaria' => 'CONTA TESTE');
    $creditos = array(credito()); $eventos = array(evento(1, '40.00'), evento(2, '60.00'));
    $consultas = $gravacoes = array(); $falha = '';
}
class ResultadoComposicao {
    public $linhas;
    function __construct($linhas) { $this->linhas = $linhas; }
}
function mysql_query($sql) {
    global $cabecalho, $creditos, $eventos, $consultas, $falha;
    $consultas[] = $sql;
    conferir(!preg_match('/\b(?:INSERT|UPDATE|DELETE|CALL)\b/i', $sql), 'Conferencia executa somente SELECT');
    if ($falha !== '' && strpos($sql, $falha) !== false) { return false; }
    if (strpos($sql, 'FIN003_COMPOSICAO_CABECALHO') !== false) {
        conferir(strpos($sql, 'T215_Id=10 AND e.T215_C004_Id=7') !== false, 'Autoriza extrato pela empresa atual');
        return new ResultadoComposicao($cabecalho ? array($cabecalho) : array());
    }
    if (strpos($sql, 'FIN003_COMPOSICAO_CREDITOS') !== false) { return new ResultadoComposicao($creditos); }
    if (strpos($sql, 'FIN003_COMPOSICAO_EVENTOS') !== false) {
        conferir(strpos($sql, 'p.T037_C004_Id=7 AND p.T037_D007_Id=20') !== false, 'Retorno da empresa atual e da conta exata');
        return new ResultadoComposicao($eventos);
    }
    throw new \RuntimeException('Consulta nao prevista');
}
function mysql_fetch_assoc($resultado) { return array_shift($resultado->linhas); }
function mysqli_query($sql) {
    global $gravacoes;
    conferir((bool) preg_match('/^update T216 set T216_Flag_Importar=/i', $sql), 'Somente flags nativas da grade permanecem');
    $gravacoes[] = $sql; return true;
}
function definirClasse($nome) {} // Impede carregar personalizacao/configuracao no FIN003.php nativo.
class FIN003 {}
function gBotaoAuditoria($from) {}
class ComposicaoUiTeste {
    function gridFiltroExtra($ativo, $filtro, $extra) { return $extra; }
    function gridFiltroPrint($filtro) { return ''; }
    function grid($colunas, $dados) {
        global $creditos;
        $campos = array_column($colunas, 'colunaValor');
        conferir(in_array('campoComposicaoRetorno', $campos, true), 'Coluna de composicao presente');
        conferir(in_array('T216_Flag_Importar', $campos, true) && in_array('campoDesmembrar', $campos, true), 'Operacoes nativas preservadas');
        $html = '';
        foreach (array_merge($creditos, array(credito(999, '-100.00'))) as $linha) {
            $html .= '<tr><td>' . composicaoRetornoExtrato(array('todosCampos' => array('T216_Id' => $linha['movimento_id']))) . '</td></tr>';
        }
        return array('thead' => '', 'tbody' => $html, 'tfoot' => '');
    }
}
class ComposicaoSqlTeste { function gerarSQLGrid($grid, $from, $extra) { return array('', array()); } }
class ComposicaoSmartyTeste {
    public $valores = array();
    function assign($campo, $valor) { $this->valores[$campo] = $valor; }
    function display($template) { echo $this->valores['tbody'] ?? ''; }
}
function tela($tipo, $opcoes = array()) {
    global $g;
    $g = array('empresaAtual' => 7, 'divId' => 'teste', 'divIdRoot' => 'root', 'r_' => 'fin/fin003/grid/fin003GridTransacoes/',
        'r' => array(3 => $tipo === 'grade' ? 'fin003GridTransacoes' : 'fin003ContentComposicaoRetorno'),
        'ui' => new ComposicaoUiTeste(), 'sqlAuto' => new ComposicaoSqlTeste(), 'smarty' => new ComposicaoSmartyTeste());
    $r_acaoId = 10; $r_T216_Id = $opcoes['movimento'] ?? 101; $r_T037_Id = $opcoes['retorno'] ?? 500;
    $r_assinatura = $opcoes['assinatura'] ?? hash('sha256', '1,2');
    ob_start();
    include __DIR__ . '/../../prog/fin/fin003/' . ($tipo === 'grade' ? 'grid.php' : 'content.php');
    return ob_get_clean();
}

base();
if (($argv[1] ?? '') === '--sql') {
    ComposicaoRetornoExtrato::consultar(10, 7); echo json_encode($consultas); exit;
}
if (($argv[1] ?? '') === '--compor-json') {
    $dados = json_decode(stream_get_contents(STDIN), true);
    echo json_encode(ComposicaoRetornoExtrato::compor($dados['movimentos'], $dados['eventos'])); exit;
}

foreach (array('0' => 0, '1.05' => 105, '-2.01' => -201, '12.2300' => 1223, '1.001' => null,
    '1,00' => null, '1e2' => null, '1000000000000.00' => null) as $valor => $esperado) {
    conferir(ComposicaoRetornoExtrato::centavos((string) $valor) === $esperado, 'Decimal exato: ' . $valor);
}
$r = ComposicaoRetornoExtrato::compor($creditos, $eventos);
conferir($r[101]['situacao'] === 'encontrado' && $r[101]['total'] === 10000 && count($r[101]['membros']) === 2, 'Grupo completo pelo recebido');
conferir(ComposicaoRetornoExtrato::compor($creditos, array_reverse($eventos)) === $r, 'Resultado independente da ordem de leitura');
conferir(!ComposicaoRetornoExtrato::compor(array(credito(101, '100.01')), $eventos), 'Diferenca de um centavo');
$e = $eventos; $e[0]['titulo_id'] = 0;
conferir(!ComposicaoRetornoExtrato::compor($creditos, $e), 'Registro sem titulo impede grupo parcial');
$e = $eventos; $e[] = $e[0];
conferir(!ComposicaoRetornoExtrato::compor($creditos, $e), 'Registro repetido');
$e = $eventos; $e[1]['titulo_id'] = $e[0]['titulo_id'];
conferir(!ComposicaoRetornoExtrato::compor($creditos, $e), 'Titulo repetido');
$r = ComposicaoRetornoExtrato::compor(array(credito(), credito(102)), $eventos);
conferir($r[101]['situacao'] === 'ambiguo' && $r[102]['situacao'] === 'ambiguo', 'Credito duplicado nao reutiliza titulos');
$e = array_merge($eventos, array(evento(3, '100.00', 501)));
$r = ComposicaoRetornoExtrato::compor($creditos, $e);
conferir($r[101]['situacao'] === 'ambiguo', 'Dois retornos possiveis');
$e = array_merge($eventos, array(evento(3, '40.00', 501))); $e[2]['titulo_id'] = $e[0]['titulo_id'];
$r = ComposicaoRetornoExtrato::compor(array(credito(), credito(102, '40.00')), $e);
conferir($r[101]['situacao'] === 'ambiguo' && $r[102]['situacao'] === 'ambiguo', 'Titulo compartilhado entre alternativas');
$e = $eventos; $e[1]['credito_no_retorno'] = '2026-01-16';
$r = ComposicaoRetornoExtrato::compor(array(credito(101, '40.00'), credito(102, '60.00', '2026-01-16')), $e);
conferir(count($r[101]['membros']) === 1 && count($r[102]['membros']) === 1, 'Arquivo com varias datas de credito');
$e = $eventos; $e[1]['pagamento_no_retorno'] = '2026-01-13';
$r = ComposicaoRetornoExtrato::compor(array(credito(101, '40.00'), credito(102, '60.00')), $e);
conferir(count($r[101]['membros']) === 1 && count($r[102]['membros']) === 1, 'Grupos completos por pagamento');
$e[0]['pagamento_no_retorno'] = '0000-00-00';
conferir(!ComposicaoRetornoExtrato::compor(array(credito(101, '40.00')), $e), 'Pagamento ausente nao cria subgrupo artificial');
foreach (array('0.00', '-1.00', '40.001') as $valor) {
    $e = $eventos; $e[0]['recebido_no_retorno'] = $valor;
    conferir(!ComposicaoRetornoExtrato::compor($creditos, $e), 'Valor invalido no grupo: ' . $valor);
}
// Particao Santander: ancora unica e restante completo em outra linha.
$s = array(evento(1, '10.00', 500, '2026-01-15', '2026-01-14', 33),
    evento(2, '20.00', 500, '2026-01-15', '2026-01-14', 33), evento(3, '30.00', 500, '2026-01-15', '2026-01-14', 33));
$m = array(credito(101, '10.00'), credito(102, '50.00'));
$r = ComposicaoRetornoExtrato::compor($m, $s);
conferir(count($r[101]['membros']) === 1 && count($r[102]['membros']) === 2, 'Santander separa os membros das duas linhas');
conferir(!ComposicaoRetornoExtrato::compor(array($m[0]), $s), 'Exige ambas as pontas da particao');
$s[0]['codigo_banco'] = $s[1]['codigo_banco'] = $s[2]['codigo_banco'] = 341;
conferir(!ComposicaoRetornoExtrato::compor($m, $s), 'Particao restrita ao banco correspondente');
conferir(!ComposicaoRetornoExtrato::compor(array_fill(0, 501, credito()), $eventos, $aviso) && $aviso !== null, 'Limite de creditos');
conferir(!ComposicaoRetornoExtrato::compor($creditos, array_fill(0, 5001, $eventos[0]), $aviso) && $aviso !== null, 'Limite de eventos');
$e = array(); $m = array(); $total = 2500 * 2501 / 2;
for ($i = 1; $i <= 2500; $i++) { $e[] = evento($i, $i . '.00', 500, '2026-01-15', '2026-01-14', 33); }
for ($i = 1; $i <= 5; $i++) { $m[] = credito($i, $i . '.00'); $m[] = credito($i + 10, ($total - $i) . '.00'); }
conferir(!ComposicaoRetornoExtrato::compor($m, $e, $aviso) && $aviso !== null, 'Limite de alternativas nao esconde ambiguidade');

base(); $consulta = ComposicaoRetornoExtrato::consultar(10, 7);
conferir(count($consultas) === 3 && count($consulta['grupos'][101]['membros']) === 2, 'Tres leituras em lote');
$antes = count($consultas);
$g = array('composicoesRetornoExtrato' => $consulta['grupos'], 'composicaoRetornoExtratoId' => 10);
$link = composicaoRetornoExtrato(array('todosCampos' => array('T216_Id' => 101)));
conferir(count($consultas) === $antes && strpos($link, 'fin003ContentComposicaoRetorno') !== false, 'Callback sem consulta adicional');
conferir(strpos($link, '2 títulos') !== false, 'Resumo da quantidade');
$assinatura = $consulta['grupos'][101]['assinatura'];
conferir(ComposicaoRetornoExtrato::localizar($consulta, 101, 500, $assinatura) !== false, 'Localiza grupo revalidado');
foreach (array(array(102, 500, $assinatura), array(101, 501, $assinatura), array(101, 500, str_repeat('0', 64)), array(101, 500, array())) as $parametros) {
    conferir(ComposicaoRetornoExtrato::localizar($consulta, $parametros[0], $parametros[1], $parametros[2]) === false, 'Recusa parametros alterados');
}
base(); $cabecalho = false; $c = ComposicaoRetornoExtrato::consultar(10, 7);
conferir(!$c['extrato'] && count($consultas) === 1, 'Extrato de outra empresa nao consulta retornos');
base(); $cabecalho['empresa_id'] = 8; $c = ComposicaoRetornoExtrato::consultar(10, 7);
conferir(!$c['extrato'] && count($consultas) === 1, 'Confere empresa do resultado autorizado');
base(); $c = ComposicaoRetornoExtrato::consultar(array(10), 7);
conferir(!$c['extrato'] && !$consultas, 'Parametro invalido nao consulta');
base(); $creditos = array(); $c = ComposicaoRetornoExtrato::consultar(10, 7);
conferir(!$c['grupos'] && count($consultas) === 2, 'Sem creditos nao consulta retornos');
foreach (array('CABECALHO', 'CREDITOS', 'EVENTOS') as $etapa) {
    base(); $falha = 'FIN003_COMPOSICAO_' . $etapa; $c = ComposicaoRetornoExtrato::consultar(10, 7);
    conferir(!$c['grupos'] && $c['aviso'] !== '', 'Falha SQL com aviso e sem resultado parcial');
}
base(); for ($i = 0; $i <= 500; $i++) { $creditos[$i] = credito($i + 1); }
$c = ComposicaoRetornoExtrato::consultar(10, 7);
conferir(!$c['grupos'] && $c['aviso'] !== '' && count($consultas) === 2, 'Leitura de creditos limitada');
base(); $eventos = array_fill(0, 5001, $eventos[0]); $c = ComposicaoRetornoExtrato::consultar(10, 7);
conferir(!$c['grupos'] && $c['aviso'] !== '', 'Leitura de retornos limitada');

base(); $html = tela('grade');
conferir(count($consultas) === 3 && count($gravacoes) === 2, 'Grade preserva somente as duas atualizacoes nativas');
conferir(substr_count($html, '<tr>') === 2 && substr_count($html, '<a ') === 1, 'Mantem uma linha por OFX e somente credito com link');
base(); $html = tela('janela');
conferir(count($consultas) === 3 && !$gravacoes, 'Janela revalida somente com SELECT');
conferir(strpos($html, 'TESTE-1') !== false && strpos($html, 'TESTE-2') !== false && strpos($html, '100,00') !== false, 'Lista os membros e total exatos');
conferir(strpos($html, 'Processar Retorno') === false && strpos($html, 'Situação da baixa') === false, 'Escopo limitado aos valores do retorno');
base(); $eventos[0]['cliente'] = '<script>alert(1)</script>'; $html = tela('janela');
conferir(strpos($html, '<script>alert(1)') === false && strpos($html, '&lt;script&gt;') !== false, 'Escapa dados do retorno');
foreach (array(array('retorno' => 501), array('movimento' => 102), array('assinatura' => str_repeat('0', 64))) as $opcoes) {
    base(); $html = tela('janela', $opcoes);
    conferir(strpos($html, 'ui-state-error') !== false && strpos($html, '<table>') === false && !$gravacoes, 'Janela alterada nao lista titulos');
}
base(); $eventos[0]['recebido_no_retorno'] = '40.01'; $html = tela('janela');
conferir(strpos($html, 'ui-state-error') !== false && strpos($html, '<table>') === false, 'Revalida composicao desatualizada');
base(); $cabecalho = false; $html = tela('grade');
conferir(strpos($html, 'ui-state-error') !== false && !$gravacoes && count($consultas) === 1, 'Grade nao executa flags em extrato nao autorizado');

echo 'OK: ' . $verificacoes . " verificações de composição, autorização e telas, sem banco real.\n";
