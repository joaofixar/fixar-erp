<?php
/** Teste isolado do método real; persistência e agregações simuladas, sem MySQL/SEFAZ. */
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
$fonte = file_get_contents($raiz . '/bibliotecas/classes/VEN003.php');
$inicio = strpos($fonte, 'public function totalizarValoresEspeciaisNFDebitoCredito(');
$fim = strpos($fonte, 'public function imprimirNFeMarcada(', $inicio);
conferir($inicio !== false && $fim !== false, 'Método de totalização não localizado');
eval('namespace hardness; class TotaisNFDC { ' . substr($fonte, $inicio, $fim - $inicio) . '}');
$g = ['empresaAtual' => 1];
$itens = [];
$cabecalho = [];
$consultas = [];
$falha = false;
function mysql_error() { return 'falha simulada'; }
function mysql_query($sql) {
    global $consultas, $itens, $cabecalho, $falha;
    $consultas[] = $sql;
    if ($falha) { return false; }
    conferir(preg_match("/WHERE PROD.T008_T007_Id = '(\d+)'/", $sql, $id) === 1, 'Filtro de itens ausente');
    conferir(strpos($sql, "WHERE NF.T007_Id = '{$id[1]}'") !== false, 'Filtro do cabeçalho diverge dos itens');
    preg_match_all('/IFNULL\(SUM\(ITEM\.(T008A_\w+)\), 0\) AS (\w+)/', $sql, $somas, PREG_SET_ORDER);
    preg_match_all('/CAB\.(T007A_\w+) = TOTAIS\.(\w+)/', $sql, $destinos, PREG_SET_ORDER);
    conferir(count($somas) === 6 && count($destinos) === 6, 'Deve sincronizar somente os seis totais especiais');
    $totais = [];
    foreach ($somas as $soma) {
        // Simulação em centavos: não executa nem valida a sintaxe SQL em um servidor.
        $centavos = 0;
        foreach ($itens as $item) {
            if ($item['T008_T007_Id'] === (int) $id[1]) {
                $centavos += (int) round((float) ($item[$soma[1]] ?? 0) * 100);
            }
        }
        $totais[$soma[2]] = number_format($centavos / 100, 2, '.', '');
    }
    foreach ($destinos as $destino) { $cabecalho[$destino[1]] = $totais[$destino[2]]; }
    return true;
}
$totalizador = new TotaisNFDC();
$campos = ['IBS_Transferido', 'CBS_Transferido', 'IBS_Ajuste_Competencia',
    'CBS_Ajuste_Competencia', 'IBS_Estornado', 'CBS_Estornado'];
function item($id, $valores) {
    global $campos;
    return ['T008_T007_Id' => $id] + array_combine(array_map(function ($campo) {
        return 'T008A_Valor_' . $campo;
    }, $campos), $valores);
}
$cabecalho = ['T007A_Valor_Total_IBS' => '0.00', 'T007A_Valor_Total_CBS' => '0.01',
    'T007A_Valor_Total_Base_CBS_IBS' => '1.35', 'T007A_Valor_Total_Reforma' => '1.35'];
$comuns = $cabecalho;
$itens = [item(576, ['4.20', '1.20', 0, 0, 0, 0]), item(999, [999, 999, 999, 999, 999, 999])];
conferir($totalizador->totalizarValoresEspeciaisNFDebitoCredito(576) === true, 'Totalização falhou');
conferir($cabecalho['T007A_Valor_Total_IBS_Transferido'] === '4.20', 'IBS transferido do exemplo não totalizado');
conferir($cabecalho['T007A_Valor_Total_CBS_Transferido'] === '1.20', 'CBS transferido do exemplo não totalizado');
$itens[] = item(576, ['0.10', '0.20', '3.45', '6.78', '1.01', '2.02']);
$totalizador->totalizarValoresEspeciaisNFDebitoCredito(576);
$esperados = ['4.30', '1.40', '3.45', '6.78', '1.01', '2.02'];
foreach ($campos as $i => $campo) {
    conferir($cabecalho['T007A_Valor_Total_' . $campo] === $esperados[$i], 'Soma/mapeamento incorreto: ' . $campo);
}
$anterior = $cabecalho;
$totalizador->totalizarValoresEspeciaisNFDebitoCredito(576);
conferir($cabecalho === $anterior, 'Repetir totalização acumulou valores');
// Regravar apenas um item deve refletir a soma atual, não manter o total anterior.
$itens[0] = item(576, [0, 0, 0, 0, 0, 0]);
$totalizador->totalizarValoresEspeciaisNFDebitoCredito(576);
conferir($cabecalho['T007A_Valor_Total_IBS_Transferido'] === '0.10', 'Edição para zero não refletida');
$itens = [];
$totalizador->totalizarValoresEspeciaisNFDebitoCredito(576);
foreach ($campos as $campo) { conferir($cabecalho['T007A_Valor_Total_' . $campo] === '0.00', 'Soma vazia não zerada'); }
foreach ($comuns as $campo => $valor) { conferir($cabecalho[$campo] === $valor, 'Tributo/total comum alterado'); }
$sql = $consultas[0];
foreach (["NF.T007_C004_Id = '1'", "NF.T007_Flag_Finalidade_NFe IN ('5', '6')",
    "IFNULL(NF.T007_Numero_Protocolo_Nfe, '') = ''", "IFNULL(NF.T007_Flag_Cancelada, 'N') != 'S'",
    "IFNULL(NF.T007_Flag_Inutilizado, 'N') != 'S'", "IFNULL(NF.T007_Flag_Denegado, 'N') != 'S'"] as $filtro) {
    conferir(strpos($sql, $filtro) !== false, 'Proteção SQL ausente: ' . $filtro);
}
conferir(strpos($sql, 'Situacao_Tributaria') === false && strpos($sql, 'Classificacao_Tributaria') === false, 'Total depende do CST/classe');
conferir(strpos($sql, 'Flag_Calcular') === false, 'Total especial depende do cálculo automático');
$falha = true;
conferir(is_string($totalizador->totalizarValoresEspeciaisNFDebitoCredito(576)), 'Falha de persistência ignorada');
$antes = count($consultas);
conferir(is_string($totalizador->totalizarValoresEspeciaisNFDebitoCredito(0)), 'ID inválido aceito');
$g['empresaAtual'] = 0;
conferir(is_string($totalizador->totalizarValoresEspeciaisNFDebitoCredito(576)), 'Empresa ausente aceita');
conferir(count($consultas) === $antes, 'Executou consulta sem nota/empresa');
conferir(substr_count($fonte, '$this->totalizarValoresEspeciaisNFDebitoCredito(') === 2, 'Geração ou edição não sincroniza totais');
echo "OK: $verificacoes verificações do totalizador direto, com persistência simulada; sem banco ou SEFAZ.\n";
