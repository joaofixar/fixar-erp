<?php
/** php scripts/testes/frete-nfs.php — consultas simuladas, sem conexão com bases. */
namespace hardness;
if (PHP_SAPI !== 'cli') { exit; }
error_reporting(E_ALL);
set_error_handler(function ($nivel, $mensagem) { throw new \RuntimeException($mensagem); });
require_once dirname(__DIR__, 2) . '/bibliotecas/classes/EST004FreteNFS.php';
$verificacoes = 0;
function conferir($condicao, $mensagem) {
    global $verificacoes;
    $verificacoes++;
    if (!$condicao) { throw new \RuntimeException($mensagem); }
}
class ResultadoFreteNFS {
    public $linhas;
    public function __construct($linhas) { $this->linhas = array_values($linhas); }
}
function mysql_fetch_assoc($resultado) { return array_shift($resultado->linhas); }
function mysql_fetch_array($resultado) { return mysql_fetch_assoc($resultado); }
function mysql_real_escape_string($valor) { return addslashes($valor); }
function mysql_query($sql, $gatilhos = true) {
    global $db, $snapshot, $consultas, $falha, $itemCusto, $quantidadeGrupo;
    $sql = preg_replace('/\s+/', ' ', trim($sql));
    $consultas[] = $sql;
    if ($falha && strpos($sql, $falha) !== false) { return false; }
    if ($sql === 'START TRANSACTION') { $snapshot = $db; return true; }
    if ($sql === 'ROLLBACK') { $db = $snapshot; return true; }
    if ($sql === 'COMMIT') { return true; }
    if (strpos($sql, 'SELECT *, D009_IPV_ICMS(') === 0) { return new ResultadoFreteNFS(array($itemCusto)); }
    if (strpos($sql, 'SELECT SUM(T014_Quantidade) AS T014_Quantidade') === 0) {
        return new ResultadoFreteNFS(array(array('T014_Quantidade' => $quantidadeGrupo)));
    }
    if (strpos($sql, 'SELECT T013A_T013_Id_Frete_NFS FROM T013A') === 0) {
        preg_match("/T013A_T013_Id = '(\d+)' AND T013_C004_Id = '(\d+)'/", $sql, $m);
        return new ResultadoFreteNFS(isset($db['ext'][$m[1]]) && $db['notas'][$m[1]]['T013_C004_Id'] == $m[2] ? array($db['ext'][$m[1]]) : array());
    }
    if (strpos($sql, 'SELECT T013_Id, T013_Tipo, T013_Numero_Nota_Fiscal') === 0) {
        preg_match("/T013_C004_Id = '(\d+)' AND T013_Id IN \(([\d,]+)\)/", $sql, $m);
        return new ResultadoFreteNFS(array_filter($db['notas'], function ($nota) use ($m) {
            return $nota['T013_C004_Id'] == $m[1] && in_array($nota['T013_Id'], explode(',', $m[2]));
        }));
    }
    if (strpos($sql, 'SELECT T013A_Id, T013A_T013_Id_Frete_NFS') === 0) {
        preg_match("/T013A_T013_Id = '(\d+)'/", $sql, $m);
        return new ResultadoFreteNFS(isset($db['ext'][$m[1]]) ? array($db['ext'][$m[1]]) : array());
    }
    if (strpos($sql, 'SELECT T014_Id, T014_T013_Id,') === 0) {
        preg_match("/T014_T013_Id IN \(([\d,]+)\) AND T014_C004_Id = '(\d+)'/", $sql, $m);
        return new ResultadoFreteNFS(array_filter($db['itens'], function ($item) use ($m) {
            return $item['T014_C004_Id'] == $m[2] && in_array($item['T014_T013_Id'], explode(',', $m[1]));
        }));
    }
    if (strpos($sql, 'INSERT INTO T014A') === 0) {
        preg_match("/SELECT '(\d+)', 0/", $sql, $m);
        if (!isset($db['fretes'][$m[1]])) { $db['fretes'][$m[1]] = 0; }
        return true;
    }
    if (strpos($sql, 'SELECT T014A_Valor_Frete_NFS FROM') === 0) {
        preg_match("/T014A_T014_Id = '(\d+)'/", $sql, $m);
        return new ResultadoFreteNFS(array(array('T014A_Valor_Frete_NFS' => $db['fretes'][$m[1]])));
    }
    if (strpos($sql, 'UPDATE T014A SET T014A_Valor_Frete_NFS') === 0) {
        preg_match("/T014A_Valor_Frete_NFS = '([\d.]+)' WHERE T014A_T014_Id = '(\d+)'/", $sql, $m);
        $db['fretes'][$m[2]] = (float) $m[1];
        return true;
    }
    if (strpos($sql, 'CALL T014_Gravar_Totalizacao_Custo(') === 0) {
        preg_match("/\('(\d+)'\)/", $sql, $m);
        $item = $db['itens'][$m[1]];
        $db['custos'][$m[1]] = $item['T014_Valor_Custo_Unitario'] + $db['fretes'][$m[1]] / $item['T014_Quantidade'];
        return true;
    }
    if (strpos($sql, 'CALL T013_Gravar_Totalizacao(') === 0) { return true; }
    if (strpos($sql, 'INSERT INTO T013A') === 0) {
        preg_match("/VALUES \('(\d+)'\)/", $sql, $m);
        $db['ext'][$m[1]] = array('T013A_Id' => $m[1], 'T013A_T013_Id_Frete_NFS' => 0, 'T013A_JSON' => null);
        return true;
    }
    if (strpos($sql, 'UPDATE T013A SET') === 0) {
        preg_match("/T013A_T013_Id_Frete_NFS = '(\d+)'.*CAST\('(.*)' AS JSON\).*T013A_T013_Id = '(\d+)'/", $sql, $m);
        $ext = &$db['ext'][$m[3]];
        $json = json_decode($ext['T013A_JSON'], true) ?: array();
        $json['freteNFS'] = json_decode(stripslashes($m[2]), true);
        $ext['T013A_JSON'] = json_encode($json);
        $ext['T013A_T013_Id_Frete_NFS'] = (int) $m[1];
        return true;
    }
    if (strpos($sql, 'UPDATE T013 SET T013_Flag_Frete') === 0) {
        preg_match("/T013_Flag_Rateio_Frete = '([12])' WHERE T013_Id = '(\d+)'/", $sql, $m);
        $db['notas'][$m[2]]['T013_Flag_Frete'] = '4';
        $db['notas'][$m[2]]['T013_Flag_Rateio_Frete'] = $m[1];
        return true;
    }
    if (strpos($sql, 'SELECT T013_Tipo, T013_Flag_Frete') === 0) {
        preg_match("/T013_Id = '(\d+)' AND T013_C004_Id = '(\d+)'/", $sql, $m);
        $nota = isset($db['notas'][$m[1]]) ? $db['notas'][$m[1]] : null;
        if (!$nota || $nota['T013_C004_Id'] != $m[2]) { return new ResultadoFreteNFS(array()); }
        return new ResultadoFreteNFS(array(array_merge($nota, isset($db['ext'][$m[1]]) ? $db['ext'][$m[1]] : array('T013A_T013_Id_Frete_NFS' => 0, 'T013A_JSON' => null))));
    }
    if (strpos($sql, 'SELECT T013_Id, T013_Tipo, T013_Flag_Frete') === 0) {
        preg_match("/T013A_T013_Id_Frete_NFS = '(\d+)' AND T013_C004_Id = '(\d+)'/", $sql, $m);
        $linhas = array();
        foreach ($db['ext'] as $id => $ext) {
            if ($ext['T013A_T013_Id_Frete_NFS'] == $m[1] && $db['notas'][$id]['T013_C004_Id'] == $m[2]) {
                $linhas[] = array_merge($db['notas'][$id], $ext);
            }
        }
        return new ResultadoFreteNFS($linhas);
    }
    if (strpos($sql, 'SELECT T014_Id, T014_Quantidade, T014_Valor_Custo_Unitario, IFNULL(') === 0) {
        preg_match("/T014_T013_Id = '(\d+)' AND T014_C004_Id = '(\d+)'/", $sql, $m);
        $linhas = array();
        foreach ($db['itens'] as $id => $item) {
            if ($item['T014_T013_Id'] == $m[1] && $item['T014_C004_Id'] == $m[2]) {
                $linhas[] = array_merge($item, array('T014A_Valor_Frete_NFS' => isset($db['fretes'][$id]) ? $db['fretes'][$id] : 0));
            }
        }
        return new ResultadoFreteNFS($linhas);
    }
    if (strpos($sql, 'SELECT T013A_Id FROM T013A INNER JOIN') === 0) {
        preg_match("/T013_C004_Id = '(\d+)'.*T013A_T013_Id = '(\d+)'/", $sql, $m);
        foreach ($db['ext'] as $id => $ext) {
            if ($db['notas'][$id]['T013_C004_Id'] == $m[1] &&
                (($id == $m[2] && $ext['T013A_T013_Id_Frete_NFS'] > 0) || $ext['T013A_T013_Id_Frete_NFS'] == $m[2])) {
                return new ResultadoFreteNFS(array($ext));
            }
        }
        return new ResultadoFreteNFS(array());
    }
    throw new \RuntimeException('Consulta inesperada no teste: ' . $sql);
}
function item($id, $nota, $quantidade, $preco) {
    return array('T014_Id' => $id, 'T014_T013_Id' => $nota, 'T014_C004_Id' => 1,
                 'T014_Quantidade' => $quantidade, 'T014_Valor_Custo_Unitario' => $preco);
}
$iguais = array(item(1, 2, 1, 10), item(2, 2, 1, 10), item(3, 2, 1, 10));
conferir(EST004FreteNFS::distribuir($iguais, 2000, '1') === array(1 => 667, 2 => 667, 3 => 666), 'Resíduo de centavos incorreto');
mt_srand(112793);
for ($i = 0; $i < 200; $i++) {
    $itens = array();
    for ($j = 1; $j <= 17; $j++) { $itens[] = item($j, 2, mt_rand(1, 1000) / 10, mt_rand(0, 10000) / 100); }
    $valor = mt_rand(1, 10000000);
    foreach (array('1', '2') as $forma) {
        $parcelas = EST004FreteNFS::distribuir($itens, $valor, $forma);
        conferir(array_sum($parcelas) === $valor && min($parcelas) >= 0, 'Rateio perdeu ou criou centavos');
    }
}
foreach (array(array(array(), 20, '1'), array($iguais, 0, '1'), array($iguais, 20, '3'), array(array(item(1, 2, 0, 10)), 20, '1')) as $parametros) {
    $falhou = false;
    try { EST004FreteNFS::distribuir(...$parametros); } catch (\RuntimeException $e) { $falhou = true; }
    conferir($falhou, 'Base inválida aceita');
}
$db = array('notas' => array(), 'ext' => array(), 'itens' => array(), 'fretes' => array(), 'custos' => array());
foreach (array(1 => 10, 2 => 1, 3 => 1, 4 => 10, 5 => 1) as $id => $tipo) {
    $db['notas'][$id] = array('T013_Id' => $id, 'T013_C004_Id' => 1, 'T013_Tipo' => $tipo,
        'T013_Numero_Nota_Fiscal' => $id == 1 ? '1515' : '112793', 'T013_Valor_Total' => $tipo == 10 ? 20 : 200,
        'T013_Flag_Travar_Nota' => 'N', 'T013_Flag_Financeiro_Finalizado' => 'N', 'T013_Flag_Estoque_Finalizado' => 'N',
        'T013_Flag_Cancelada' => 'N', 'T013_Flag_Frete' => '0', 'T013_Valor_Frete' => 0);
}
$db['ext'][1] = array('T013A_Id' => 1, 'T013A_T013_Id_Frete_NFS' => 0, 'T013A_JSON' => '{"preservar":"sim"}');
$db['itens'] = array(101 => item(101, 2, 2, 50), 102 => item(102, 2, 1, 100), 201 => item(201, 3, 1, 80));
$g = array('empresaAtual' => 1, 'usuarioAtual' => 7, 'c029Ids' => array(189 => true));
$consultas = array(); $falha = ''; $snapshot = array();
$servico = new EST004FreteNFS();
conferir(is_array($servico->ratear(1, 2, '1', '20')), 'Não rateou a NFS 1515');
conferir($db['fretes'] === array(101 => 10.0, 102 => 10.0), 'Rateio por preço incorreto');
conferir($db['custos'] === array(101 => 55.0, 102 => 110.0), 'Custo unitário não inclui o frete');
conferir($db['notas'][2]['T013_Valor_Total'] === 200 && $db['notas'][2]['T013_Valor_Frete'] === 0, 'Alterou os valores fiscais do XML');
conferir(json_decode($db['ext'][1]['T013A_JSON'], true)['preservar'] === 'sim', 'Apagou outro dado do JSON');
conferir($servico->validarFinalizacao(1) === true && $servico->validarFinalizacao(2) === true, 'Bloqueou rateio válido');
conferir(is_array($servico->ratear(1, 2, '1', '20')) && array_sum($db['fretes']) === 20.0, 'Repetição duplicou o frete');
$db['notas'][1]['T013_Valor_Total'] = 30;
conferir(is_string($servico->ratear(1, 2, '1', '20')), 'Não rejeitou valor desatualizado');
conferir(is_string($servico->validarFinalizacao(1)) && is_string($servico->validarFinalizacao(2)), 'Finalizou NFS alterada sem novo rateio');
conferir(is_array($servico->ratear(1, 2, '1', '30')) && $db['fretes'][101] === 15.0, 'Não substituiu rateio anterior');
$db['notas'][4]['T013_Valor_Total'] = 5;
conferir(is_array($servico->ratear(4, 2, '1', '5')) && $db['fretes'][101] === 17.5, 'Segunda NFS apagou a primeira');
conferir(is_array($servico->ratear(1, 2, '2', '30')) && $db['fretes'][101] === 22.5 && $db['fretes'][102] === 12.5, 'Rateio por quantidade incorreto');
conferir(is_array($servico->ratear(1, 3, '1', '30')) && $db['fretes'][101] === 2.5 && $db['fretes'][201] === 30.0, 'Troca da nota não removeu somente a parcela da NFS');
$antes = $db;
$falha = "CALL T014_Gravar_Totalizacao_Custo('201')";
conferir(is_string($servico->ratear(1, 2, '1', '30')) && $db === $antes && end($consultas) === 'ROLLBACK', 'Falha parcial não desfez a transação');
$falha = '';
foreach (array('T013_Flag_Travar_Nota', 'T013_Flag_Estoque_Finalizado', 'T013_Flag_Financeiro_Finalizado', 'T013_Flag_Cancelada') as $campo) {
    $db['notas'][2][$campo] = 'S';
    conferir(is_string($servico->ratear(4, 2, '1', '5')), 'Aceitou nota finalizada/cancelada');
    $db['notas'][2][$campo] = 'N';
}
$db['notas'][2]['T013_C004_Id'] = 9;
conferir(is_string($servico->ratear(4, 2, '1', '5')), 'Aceitou nota de outra empresa');
$db['notas'][2]['T013_C004_Id'] = 1;
$g['c029Ids'] = array();
conferir(is_string($servico->ratear(4, 2, '1', '5')), 'Aceitou usuário sem permissão');
$g['c029Ids'] = array(189 => true);
conferir(is_string($servico->ratear(1, 1, '1', '30')), 'Aceitou autorreferência');
conferir(is_string($servico->ratear(2, 3, '1', '200')), 'Aceitou origem que não é NFS');
$db['itens'][101]['T014_Quantidade'] = 3;
conferir(is_string($servico->validarFinalizacao(2)), 'Finalizou rateio com itens alterados');
$db['itens'][101]['T014_Quantidade'] = 2;
conferir($servico->validarFinalizacao(2) === true && $servico->validarFinalizacao(5) === true, 'Bloqueou nota válida/comum');
conferir(is_string($servico->validarRemocao(1)) && is_string($servico->validarRemocao(3)) && $servico->validarRemocao(5) === true, 'Não protegeu os vínculos ao excluir/cancelar');

// Executa a validação de custo real, inclusive para item dividido entre ordens de compra.
$fonte = file_get_contents(dirname(__DIR__, 2) . '/bibliotecas/classes/EST004.php');
$inicio = strpos($fonte, 'public function validacaoCusto(');
$fim = strpos($fonte, 'public function ', $inicio + 16);
eval('namespace hardness; class ValidadorCustoFreteNFS { ' . substr($fonte, $inicio, $fim - $inicio) . ' }');
$itemCusto = array('T014_Id' => 101, 'T014_Id_Duplicado' => 0, 'D006_Flag_Atualiza_Custo' => 'S',
    'D006_Flag_Substituicao_Tributaria' => 'N', 'D006_Flag_Credita_IPI' => 'S', 'D006_Flag_Deb_Crd_ICMS' => 'N',
    'T014_Valor_ICMS_Substituicao' => 0, 'T014_Valor_IPI' => 0, 'T014_Valor_ICMS' => 0,
    'T014A_Valor_Acrescimo_Custo' => 0, 'T014A_Valor_Frete_NFS' => 10, 'T014_Quantidade' => 2,
    'T014_Valor_Custo_Unitario' => 50, 'T014_Valor_Custo_Final_Unitario' => 55, 'T014_Valor_Frete' => 0,
    'T014_Valor_Seguro' => 0, 'T014_Valor_Despesas_Acessorias' => 0, 'T014_Valor_Total_Desconto' => 0,
    'D006_Flag_Somar_DIFAL_Custo' => 'N', 'D001_Codigo_Produto' => 'TESTE');
foreach (array(2, 5) as $quantidadeGrupo) {
    conferir((new ValidadorCustoFreteNFS())->validacaoCusto(2) === '', 'Validação não reconhece o frete NFS por quantidade própria do item');
}
echo "OK: {$verificacoes} verificações de rateio, custo, repetição, troca de nota, transação e bloqueios; sem acesso a banco.\n";
