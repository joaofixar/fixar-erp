<?php
/** Testes isolados de gravação e contratos SQL/migração. Não executa SQL nem acessa banco. */
namespace hardness;
if (PHP_SAPI !== 'cli') { exit; }
error_reporting(E_ALL);
set_error_handler(function ($nivel, $mensagem) { throw new \RuntimeException($mensagem); });
$raiz = dirname(__DIR__, 2);
$verificacoes = 0;
function conferir($condicao, $mensagem) {
    global $verificacoes;
    $verificacoes++;
    if (!$condicao) { throw new \RuntimeException($mensagem); }
}
function definirClasse($nome) {}
require $raiz . '/bibliotecas/classes/GFormWrap.php';

// Extrai a função legada real de formatação, sem carregar bootstrap/conexões.
$legado = file_get_contents($raiz . '/old/lib/funcoes.php');
preg_match('/function formata_valor_gravar\([^\n]+\{.*?^\}/ms', $legado, $funcao);
conferir(isset($funcao[0]), 'Formatador real não encontrado');
eval('namespace hardness; ' . $funcao[0]);
$consultas = [];
$falharGravacao = false;
function mysql_query($sql) {
    global $consultas, $falharGravacao;
    $consultas[] = $sql;
    return !$falharGravacao;
}
function mysql_real_escape_string($valor) { return addslashes($valor); }
function mysql_error() { return 'falha simulada'; }

$ven = file_get_contents($raiz . '/bibliotecas/classes/VEN003.php');
$inicio = strpos($ven, '// BEGIN NFDC CAMPOS ESPECIAIS');
$fim = strpos($ven, '// END NFDC CAMPOS ESPECIAIS', $inicio);
conferir($inicio !== false && $fim !== false, 'Bloco de gravação não encontrado');
$bloco = substr($ven, $inicio, $fim - $inicio);
$campos = ['T008A_Valor_IBS_Transferido', 'T008A_Valor_CBS_Transferido', 'T008A_Competencia_Ajuste',
    'T008A_Valor_IBS_Ajuste_Competencia', 'T008A_Valor_CBS_Ajuste_Competencia',
    'T008A_Valor_IBS_Estornado', 'T008A_Valor_CBS_Estornado'];
preg_match('/\$zT008A_Valor_IBS_Transferido\s*=.*?\$zT008A_Valor_CBS_Estornado\s*=.*?;/s', $ven, $leitura);
conferir(isset($leitura[0]), 'Leitura dos sete campos não encontrada');
$gravar = function ($valores, $manual) use ($bloco, $leitura) {
    $form = new GFormWrap_($valores);
    $T008_Id = 123;
    $T008_Flag_Calculo_Manual = $manual;
    eval('namespace hardness; ' . $leitura[0]);
    return eval('namespace hardness; ' . $bloco);
};
foreach (['N', 'S'] as $manual) {
    $consultas = [];
    $gravar(array_combine($campos, ['0,10', '1.234,56', '08/2026', '2,30', '3,40', '4,50', '5,60']), $manual);
    conferir(count($consultas) === 1, "Uma gravação especial com cálculo manual=$manual");
    foreach (array_combine($campos, ['0.10', '1234.56', '08/2026', '2.30', '3.40', '4.50', '5.60']) as $campo => $valor) {
        conferir(strpos($consultas[0], "$campo='$valor'") !== false, "Campo não gravado: $campo / $manual");
    }
    conferir(strpos($consultas[0], "WHERE T008A_T008_Id='123'") !== false, 'Gravação sem filtro do item');
    conferir(strpos($consultas[0], 'T008A_Valor_CBS=') === false, 'Gravação especial alterou tributo comum');
    $consultas = [];
    $gravar([], $manual);
    conferir(!$consultas, 'Campos ausentes apagaram dados existentes');
    $gravar(['T008A_Valor_IBS_Transferido' => '0,00'], $manual);
    conferir(count($consultas) === 1 && strpos($consultas[0], "T008A_Valor_IBS_Transferido='0.00'") !== false, 'Zero explícito ignorado');
    conferir(strpos($consultas[0], 'T008A_Valor_CBS_Transferido') === false, 'Campo não enviado alterado');
    $consultas = [];
    $gravar(['T008A_Valor_CBS_Estornado' => '', 'T008A_Competencia_Ajuste' => ''], $manual);
    conferir(strpos($consultas[0], "T008A_Valor_CBS_Estornado='0'") !== false, 'Valor em branco não normalizado');
    conferir(strpos($consultas[0], "T008A_Competencia_Ajuste=''") !== false, 'Competência não foi limpa');
}
$falharGravacao = true;
conferir(strpos($gravar(['T008A_Valor_IBS_Transferido' => '0,10'], 'N'), 'Erro ao gravar') === 0, 'Erro de persistência ignorado');
$wrap = new GFormWrap_([]);
conferir($wrap->campoValorEnviado('ausente') === '', 'Contrato antigo do wrapper alterado');
conferir($wrap->campoValorEnviado('ausente', false) === false, 'Wrapper não distingue ausência');
$inicioManual = strrpos(substr($ven, 0, $inicio), "if (\$T008_Flag_Calculo_Manual == 'S')");
$manual = substr($ven, $inicioManual, $inicio - $inicioManual);
foreach ($campos as $campo) {
    conferir(strpos($manual, $campo) === false, 'Campo especial ainda depende de cálculo manual');
}
$finalSalvar = substr($ven, $fim, strpos($ven, 'return true;', $fim) - $fim);
conferir(strrpos($finalSalvar, 'call T007_Gravar_Totalizacao_4') > strrpos($finalSalvar, 'CALL T008_Gravar_Totalizacao_4'), 'Cabeçalho calculado antes do último recálculo do item');

// Contrato estático: cada SUM deve alimentar a variável e a coluna correspondentes.
// Não simula nem declara execução de stored procedures em MySQL.
$sql = file_get_contents($raiz . '/scripts/sql/T007_Gravar_Totalizacao_4_Reforma.sql');
preg_match('/SELECT (.*?) INTO (.*?) FROM T008/s', preg_replace('/\s+/', ' ', $sql), $select);
conferir(count($select) === 3, 'SELECT de totalização não encontrado');
preg_match_all('/SUM\((T008A_\w+)\)/', $select[1], $origens);
$destinos = array_map('trim', explode(',', $select[2]));
conferir(count($origens[1]) === count($destinos), 'Quantidade de somas/variáveis diverge');
foreach (array_filter($campos, function ($campo) { return $campo !== 'T008A_Competencia_Ajuste'; }) as $campo) {
    $total = str_replace('T008A_Valor_', 'T007A_Valor_Total_', $campo);
    $indice = array_search($campo, $origens[1], true);
    conferir($indice !== false && $destinos[$indice] === 'x' . $total, 'Mapeamento de soma incorreto: ' . $total);
    conferir(strpos($sql, "IFNULL(SUM($campo), 0)") !== false, 'Nota sem itens não zera: ' . $total);
    conferir(preg_match('/DECLARE x' . $total . ' decimal\(15, 2\);/', $sql) === 1, 'Variável monetária não declarada');
    conferir(preg_match('/\b' . $total . '\s*=\s*x' . $total . '\b/', $sql) === 1, 'Soma não persistida');
}
conferir(strpos($sql, 'WHERE T008_T007_Id = xT007_Id') !== false, 'Soma mistura notas');
conferir(strpos($sql, "ifnull(T007_Numero_Protocolo_Nfe,'')=''") !== false, 'Total modifica nota autorizada');

$sqlItem = file_get_contents($raiz . '/scripts/sql/T008_Gravar_Totalizacao_4_Reforma.sql');
preg_match('/\/\* NT 2025\.002 v1\.51, UB13:.*?;\s*/s', $sqlItem, $normalizacao);
conferir(isset($normalizacao[0]), 'Normalização centralizada ausente');
foreach (["IN ('800', '811')", "T007_Flag_Finalidade_NFe IN ('5', '6')", "IFNULL(T007_Numero_Protocolo_Nfe, '') = ''",
    "IFNULL(T007_Flag_Cancelada, 'N') != 'S'", "IFNULL(T007_Flag_Inutilizado, 'N') != 'S'", 'T008_Id = xT008_Id'] as $filtro) {
    conferir(strpos($normalizacao[0], $filtro) !== false, 'Proteção ausente: ' . $filtro);
}
foreach (array_merge($campos, ['T008A_Valor_Total_Reforma']) as $campo) {
    conferir(strpos($normalizacao[0], $campo) === false, 'Normalização apaga valor especial ou vItem');
}
conferir(strpos($sqlItem, $normalizacao[0]) < strpos($sqlItem, 'T008A_Valor_Total_Reforma               ='), 'Normalização posterior ao cálculo de vItem');
conferir(strpos($manual, 'T008A_Valor_Total_Reforma = 0') === false, 'Edição ainda força vItem zerado');

$migracao = file_get_contents($raiz . '/scripts/atualizacaoBase.php');
conferir(preg_match('/Criar_Campo\("T008A_Percentual_Diferimento_IBS_UF", "decimal\(7,\s*4\)/', $migracao) === 1, 'Criação não suporta quatro casas');
conferir(preg_match('/Alterar_Tipo_Campo\("T008A_Percentual_Diferimento_IBS_UF", "decimal\(7,\s*4\)/', $migracao) === 1, 'Base existente não recebe correção');
$formulario = file_get_contents($raiz . '/prog/ven/ven003/form.php');
conferir(preg_match("/'name' => 'T008A_Percentual_Diferimento_IBS_UF'[^\n]+ 'casasDecimais' => 4/", $formulario) === 1, 'Formulário perde casas decimais');
$xsd = file_get_contents($raiz . '/hardness/class/PL/PL_Nfe/DFeTiposBasicos_v1.00.xsd');
conferir(strpos($xsd, 'name="pDif" type="TDec_0302_04RTC"') !== false, 'Referência XSD do percentual mudou');
echo "OK: $verificacoes verificações de gravação e contratos estáticos SQL/migração; sem banco ou SEFAZ.\n";
