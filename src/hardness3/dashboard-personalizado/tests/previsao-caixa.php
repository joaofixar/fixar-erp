<?php
namespace hardness;
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/projecao-caixa.php';

function dashboardPersonalizadoArquivoAcessos($empresa) { return $GLOBALS['caixaTesteDir'] . '/' . $empresa . '-acessos.json'; }
$GLOBALS['caixaTesteDir'] = sys_get_temp_dir() . '/previsao-caixa-teste-' . bin2hex(random_bytes(8));
mkdir($GLOBALS['caixaTesteDir'], 0700);
try {
    $fluxo = require __DIR__ . '/fixtures/previsao-caixa.php';
    $fluxo['_caixaUsuario'] = 7;
    $arquivo = dashboardCaixaPrevisaoArquivo(1, 7, $fluxo['id']);
    $entrada = array('descricao' => '<Despesa fictícia>', 'data' => '2026-10-17', 'valor' => 123.45);
    $salvo = dashboardCaixaPrevisaoSalvar($arquivo, $entrada, $erro);
    conferir('', $erro, 'Salvar previsão');
    conferir(true, !empty($salvo['id']), 'ID gerado');
    conferir(array(), dashboardCaixaPrevisaoLer(dashboardCaixaPrevisaoArquivo(1, 8, $fluxo['id']), $erro), 'Isolamento por usuário');
    conferir(array(), dashboardCaixaPrevisaoLer(dashboardCaixaPrevisaoArquivo(5, 7, $fluxo['id']), $erro), 'Isolamento por empresa');
    foreach (array(array('valor' => -1), array('valor' => '100'), array('valor' => 0.001), array('valor' => INF), array('data' => '2026-02-30'), array('descricao' => ''), array('id' => '../invalido')) as $invalido) {
        conferir(false, dashboardCaixaPrevisaoSalvar($arquivo, array_merge($entrada, $invalido), $erro), 'Entrada inválida');
    }
    $erro = '';
    $conexao->feriados = array();
    $parcela = array('pedido' => 100, 'empresa' => 1, 'dataEmissao' => '2026-10-15', 'dataEntrega' => '2026-10-16',
        'descricao' => 'Cliente fictício', 'valorPedido' => 100.01, 'fatorPendente' => 0.5, 'parcelaId' => 1,
        'numeroParcela' => 1, 'prazoDias' => 0, 'valorParcela' => 50, 'portadorId' => 1, 'boleto' => 'S', 'financeiroAntecipado' => 0);
    $conexao->parcelas = array($parcela, array_merge($parcela, array('parcelaId' => 2, 'numeroParcela' => 2, 'valorParcela' => 50.01, 'prazoDias' => 30)),
        array_merge($parcela, array('pedido' => 101, 'dataEntrega' => '0000-00-00')),
        array_merge($parcela, array('pedido' => 102, 'fatorPendente' => 0)),
        array_merge($parcela, array('pedido' => 103, 'financeiroAntecipado' => 1)));
    $input = array('f_dataInicio' => '2026-10-01', 'f_dataFim' => '2026-11-30', 'f_emissaoPedidosInicio' => '2026-10-15');
    $resultado = executarFixture($conexao, $fluxo, $input);
    conferir(array(101, 103), $resultado['previsaoCaixa']['pedidosPendentes'], 'Sinaliza dados incompletos e financeiro antecipado');
    $pedidos = array_values(array_filter($resultado['grafico'], function ($b) { return $b['serie'] === 'pedidos'; }));
    conferir(50.01, round(array_sum(array_column($pedidos, 'valor')), 2), 'Valor parcial e arredondamento conservado');
    conferir(array('2026-10-20', '2026-11-18'), array_column($pedidos, 'periodo'), 'Entrega + prazo + dias úteis/boletos/feriados');
    $manual = array_values(array_filter($resultado['grafico'], function ($b) { return $b['serie'] === 'previsao_pagar'; }));
    conferir('2026-10-19', $manual[0]['periodo'], 'Previsão manual no próximo dia útil');
    conferir(123.45, $manual[0]['valor'], 'Valor manual');
    foreach ($resultado['grafico'] as $barra) {
        $detalhe = executarFixture($conexao, $fluxo, $input + array('detalheGraficoSerie' => $barra['serie'], 'detalheGraficoData' => $barra['periodo']));
        conferir(round($barra['valor'], 2), round(array_sum(array_column($detalhe['graficoDetalhes'], 'valor')), 2), 'Todas as séries fecham com o detalhamento');
    }
    // Exercita a tabela compartilhada de CSV/PDF sem carregar o endpoint/sessão.
    $exportSource = file_get_contents(dirname(__DIR__) . '/export.php');
    $inicioExport = strpos($exportSource, 'function dashboardExportarTexto(');
    $fimExport = strpos($exportSource, 'function dashboardExportarPrepararDetalhamento(');
    eval('namespace hardness; ' . substr($exportSource, $inicioExport, $fimExport - $inicioExport));
    list($cabecalho, $linhasExportadas) = dashboardExportarTabela($resultado);
    conferir(array('Data projetada', 'Origem', 'Valor'), $cabecalho, 'Colunas de CSV/PDF');
    $totaisExportados = array_values(array_filter($linhasExportadas, function ($r) { return $r[0] === 'Total'; }));
    conferir(4, count($totaisExportados), 'Quatro séries exportadas');
    conferir('R$ 50,01', $totaisExportados[2][2], 'Previsão de pedidos exportada');
    conferir('R$ 123,45', $totaisExportados[3][2], 'Previsão manual exportada');
    $outraEmpresa = dashboardCaixaPrevisaoArquivo(5, 7, $fluxo['id']);
    dashboardCaixaPrevisaoSalvar($outraEmpresa, array_merge($entrada, array('valor' => 20)), $erro);
    $todas = executarFixture($conexao, $fluxo, $input + array('f_abrangenciaEmpresa' => 'todas'));
    conferir(2, count($todas['previsaoCaixa']['lancamentos']), 'Todas as empresas inclui previsões próprias das duas empresas');
    $itensOutraEmpresa = array_values(array_filter($todas['previsaoCaixa']['lancamentos'], function ($r) { return $r['empresa'] === 5; }));
    conferir(false, $itensOutraEmpresa[0]['editavel'], 'Edição exige empresa de origem');
    $semPedidos = executarFixture($conexao, $fluxo, $input + array('f_simularPedidos' => 'N'));
    conferir(false, in_array('pedidos', array_column($semPedidos['grafico'], 'serie'), true), 'Simulação de pedidos opcional');
    $editado = dashboardCaixaPrevisaoSalvar($arquivo, array_merge($entrada, array('id' => $salvo['id'], 'valor' => 200)), $erro);
    conferir(true, $editado !== false, 'Editar previsão');
    conferir(200.0, dashboardCaixaPrevisaoLer($arquivo, $erro)[0]['valor'] * 1.0, 'Edição persistida');
    conferir(true, dashboardCaixaPrevisaoSalvar($arquivo, array('id' => $salvo['id'], 'remover' => true), $erro) !== false, 'Remover previsão');
    conferir(array(), dashboardCaixaPrevisaoLer($arquivo, $erro), 'Remoção persistida');
    conferir(false, dashboardCaixaPrevisaoSalvar($arquivo, array('id' => $salvo['id'], 'remover' => true), $erro), 'ID removido não pode ser reutilizado');
    echo "OK: persistência isolada, validação, edição/remoção, parcelas, centavos, dias úteis e integração entre previsões, barras e detalhes.\n";
} finally {
    foreach (glob($GLOBALS['caixaTesteDir'] . '/*') as $file) unlink($file);
    rmdir($GLOBALS['caixaTesteDir']);
}
