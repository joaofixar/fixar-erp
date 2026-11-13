<?php
namespace hardness;

function dashboardCaixaPrevisaoHabilitada($indicador)
{
    return !empty($indicador['previsaoCaixa']['habilitada']);
}

// As opções atual/todas mantêm as regras existentes; um código seleciona uma empresa do grupo.
function dashboardCaixaEmpresaSelecionada($config, $abrangencia, &$erro)
{
    $erro = '';
    if ($abrangencia === 'atual' || $abrangencia === 'todas') return null;
    $empresas = isset($config['empresas']) && is_array($config['empresas']) ? array_map('intval', $config['empresas']) : array();
    if ((!is_string($abrangencia) && !is_int($abrangencia))
        || !preg_match('/^[1-9][0-9]{0,9}$/D', (string) $abrangencia)
        || !in_array((int) $abrangencia, $empresas, true)) {
        $erro = 'Selecione uma empresa do grupo configurado para o fluxo de caixa.';
        return false;
    }
    return (int) $abrangencia;
}

function dashboardCaixaPrevisaoArquivo($empresa, $indicador)
{
    if ((int) $empresa <= 0 || !preg_match('/^[a-z][a-zA-Z0-9_]{1,49}$/', $indicador)) return '';
    $base = dashboardPersonalizadoArquivoAcessos($empresa);
    return $base === '' ? '' : dirname($base) . '/' . (int) $empresa . 'DASHBOARD PERSONALIZAVEL-previsao-' . $indicador . '.json';
}

function dashboardCaixaPrevisaoData($valor)
{
    if (!is_string($valor)) return false;
    $data = \DateTime::createFromFormat('!Y-m-d', $valor);
    return $data && $data->format('Y-m-d') === $valor;
}

function dashboardCaixaPrevisaoLerDocumento($arquivo, &$erro)
{
    if (!is_file($arquivo)) return array('versao' => 2, 'lancamentos' => array(), 'arquivosMigrados' => array());
    $dados = json_decode(@file_get_contents($arquivo), true);
    if (!is_array($dados) || !isset($dados['lancamentos']) || !is_array($dados['lancamentos'])
        || (isset($dados['arquivosMigrados']) && !is_array($dados['arquivosMigrados']))) {
        $erro = 'Não foi possível ler as previsões de caixa.'; return false;
    }
    $ids = array();
    foreach ($dados['lancamentos'] as $item) {
        if (!is_array($item) || !isset($item['id'], $item['descricao'], $item['data'], $item['valor'])
            || !is_string($item['id']) || !preg_match('/^[a-f0-9]{24}$/D', $item['id']) || isset($ids[$item['id']])
            || !is_string($item['descricao']) || trim($item['descricao']) === '' || strlen($item['descricao']) > 240
            || !dashboardCaixaPrevisaoData($item['data']) || (!is_int($item['valor']) && !is_float($item['valor']))
            || !is_finite((float) $item['valor']) || $item['valor'] <= 0 || $item['valor'] > 1000000000000) {
            $erro = 'Não foi possível ler as previsões de caixa: há um lançamento inválido.'; return false;
        }
        $ids[$item['id']] = true;
    }
    if (!isset($dados['arquivosMigrados'])) $dados['arquivosMigrados'] = array();
    return $dados;
}

function dashboardCaixaPrevisaoBloquear($arquivo, &$erro)
{
    if ($arquivo === '') { $erro = 'Não foi possível localizar as previsões de caixa.'; return false; }
    $dir = dirname($arquivo);
    if (!is_dir($dir) && !@mkdir($dir, 0770, true) && !is_dir($dir)) { $erro = 'Não foi possível salvar a previsão.'; return false; }
    $lock = @fopen($arquivo . '.lock', 'c');
    if (!$lock || !flock($lock, LOCK_EX)) { if ($lock) fclose($lock); $erro = 'Não foi possível acessar as previsões de caixa.'; return false; }
    @chmod($arquivo . '.lock', 0660);
    return $lock;
}

function dashboardCaixaPrevisaoGravarDocumento($arquivo, $dados, &$erro)
{
    $tmp = $arquivo . '.' . uniqid('tmp-', true);
    $dados['versao'] = 2;
    $json = json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    if ($json === false || @file_put_contents($tmp, $json . "\n", LOCK_EX) === false) {
        @unlink($tmp); $erro = 'Não foi possível salvar a previsão.'; return false;
    }
    @chmod($tmp, 0660);
    if (!@rename($tmp, $arquivo)) { @unlink($tmp); $erro = 'Não foi possível salvar a previsão.'; return false; }
    return true;
}

// Executada sob o bloqueio do arquivo compartilhado. Mantém os originais como
// backup e registra cada arquivo importado para não restaurar itens removidos.
function dashboardCaixaPrevisaoMigrar($arquivo, $dados, &$erro)
{
    $prefixo = substr($arquivo, 0, -5);
    $legados = glob($prefixo . '-u*.json');
    if ($legados === false) { $erro = 'Não foi possível localizar as previsões antigas de caixa.'; return false; }
    $porId = array();
    foreach ($dados['lancamentos'] as $item) $porId[$item['id']] = $item;
    foreach ($legados as $legado) {
        $nome = basename($legado);
        if (!preg_match('/^' . preg_quote(basename($prefixo), '/') . '-u[1-9][0-9]*\.json$/D', $nome)
            || in_array($nome, $dados['arquivosMigrados'], true)) continue;
        $antigos = dashboardCaixaPrevisaoLerDocumento($legado, $erro);
        if ($antigos === false) return false;
        foreach ($antigos['lancamentos'] as $item) {
            if (isset($porId[$item['id']])) {
                if ($porId[$item['id']] === $item) continue;
                do { $item['id'] = bin2hex(random_bytes(12)); } while (isset($porId[$item['id']]));
            }
            $porId[$item['id']] = $item;
        }
        $dados['arquivosMigrados'][] = $nome;
    }
    $dados['lancamentos'] = array_values($porId);
    return $dados;
}

function dashboardCaixaPrevisaoLer($arquivo, &$erro)
{
    $erro = '';
    $lock = dashboardCaixaPrevisaoBloquear($arquivo, $erro);
    if ($lock === false) return false;
    try {
        $dados = dashboardCaixaPrevisaoLerDocumento($arquivo, $erro);
        if ($dados === false) return false;
        $migrados = dashboardCaixaPrevisaoMigrar($arquivo, $dados, $erro);
        if ($migrados === false) return false;
        if ($migrados !== $dados && !dashboardCaixaPrevisaoGravarDocumento($arquivo, $migrados, $erro)) return false;
        return $migrados['lancamentos'];
    } finally { flock($lock, LOCK_UN); fclose($lock); }
}

// Compartilhada por empresa e indicador; não cria títulos financeiros no ERP.
function dashboardCaixaPrevisaoSalvar($arquivo, $entrada, &$erro)
{
    $erro = '';
    if ($arquivo === '' || !is_array($entrada)) { $erro = 'Previsão inválida.'; return false; }
    $id = isset($entrada['id']) && is_string($entrada['id']) ? $entrada['id'] : '';
    if ($id !== '' && !preg_match('/^[a-f0-9]{24}$/', $id)) { $erro = 'Lançamento inválido.'; return false; }
    $remover = isset($entrada['remover']) && $entrada['remover'] === true;
    if (!$remover) {
        $descricao = isset($entrada['descricao']) && is_string($entrada['descricao']) ? trim($entrada['descricao']) : '';
        $valor = isset($entrada['valor']) ? $entrada['valor'] : null;
        if ($descricao === '' || strlen($descricao) > 240 || !dashboardCaixaPrevisaoData(isset($entrada['data']) ? $entrada['data'] : null)
            || (!is_int($valor) && !is_float($valor)) || !is_finite((float) $valor) || $valor <= 0 || $valor > 1000000000000 || round($valor, 2) <= 0) {
            $erro = 'Informe descrição, data válida e valor positivo com até 1 trilhão.'; return false;
        }
    } elseif ($id === '') { $erro = 'Selecione a previsão para remover.'; return false; }
    $lock = dashboardCaixaPrevisaoBloquear($arquivo, $erro);
    if ($lock === false) return false;
    try {
        $dados = dashboardCaixaPrevisaoLerDocumento($arquivo, $erro);
        if ($dados === false) return false;
        $dados = dashboardCaixaPrevisaoMigrar($arquivo, $dados, $erro);
        if ($dados === false) return false;
        $porId = array();
        foreach ($dados['lancamentos'] as $item) $porId[$item['id']] = $item;
        if ($id !== '' && !isset($porId[$id])) { $erro = 'Previsão não encontrada. Atualize o indicador.'; return false; }
        if ($remover) unset($porId[$id]);
        else {
            if ($id === '') {
                if (count($porId) >= 200) { $erro = 'Limite de 200 previsões por empresa. Remova as previsões antigas.'; return false; }
                $id = bin2hex(random_bytes(12));
            }
            $porId[$id] = array('id' => $id, 'descricao' => $descricao, 'data' => $entrada['data'], 'valor' => round($valor, 2));
        }
        $dados['lancamentos'] = array_values($porId);
        if (!dashboardCaixaPrevisaoGravarDocumento($arquivo, $dados, $erro)) return false;
        return array('ok' => true, 'id' => $id);
    } finally { flock($lock, LOCK_UN); fclose($lock); }
}

// Recebe as parcelas cadastradas, preserva sua proporção e distribui os centavos
// sobre o valor ainda não faturado. Não inventa condições para pedidos incompletos.
function dashboardCaixaPrevisaoPedidos($linhas, &$pendencias, &$resumo = null, $diasUteisCreditoBoleto = 2)
{
    $pedidos = array(); $resultado = array(); $pendencias = array();
    foreach ($linhas as $linha) $pedidos[(string) $linha['pedido']][] = $linha;
    $resumo = array('pedidosConsultados' => count($pedidos), 'semSaldoPendente' => 0, 'dadosIncompletos' => 0);
    foreach ($pedidos as $pedido => $parcelas) {
        $primeira = $parcelas[0];
        $fator = max(0, min(1, (float) $primeira['fatorPendente']));
        if ($fator <= 0) { $resumo['semSaldoPendente']++; continue; }
        $total = 0; $valido = dashboardCaixaPrevisaoData($primeira['dataEntrega']) && empty($primeira['financeiroAntecipado']);
        foreach ($parcelas as $parcela) {
            if (empty($parcela['parcelaId']) || !is_numeric($parcela['prazoDias']) || abs((int) $parcela['prazoDias']) > 3650
                || !is_numeric($parcela['valorParcela']) || $parcela['valorParcela'] <= 0 || (int) $parcela['portadorId'] <= 0) $valido = false;
            $total += (float) $parcela['valorParcela'];
        }
        if (abs($total - (float) $primeira['valorPedido']) > 0.02) $valido = false;
        if (!$valido) { $resumo['dadosIncompletos']++; $pendencias[] = $pedido; continue; }
        $alvo = (int) round($total * $fator * 100); $acumulado = 0; $distribuido = 0;
        foreach ($parcelas as $parcela) {
            $acumulado += (float) $parcela['valorParcela'];
            $centavos = (int) round($acumulado / $total * $alvo) - $distribuido;
            $distribuido += $centavos;
            if ($centavos <= 0) continue;
            $data = new \DateTime($primeira['dataEntrega']);
            $data->modify(sprintf('%+d days', (int) $parcela['prazoDias']));
            $resultado[] = array('contaId' => isset($parcela['contaId']) ? (int) $parcela['contaId'] : 0, 'serie' => 'pedidos', 'documento' => 'Pedido ' . $pedido . ' / parcela ' . $parcela['numeroParcela'],
                'descricao' => $primeira['descricao'], 'situacao' => 'Previsão de pedido', 'statusTituloDescricao' => 'Simulação',
                'portador' => isset($parcela['portador']) ? $parcela['portador'] : 'Não informado',
                'pedido' => $pedido, 'empresa' => $primeira['empresa'], 'dataEmissao' => $primeira['dataEmissao'],
                'dataEntrega' => $primeira['dataEntrega'], 'prazoDias' => (int) $parcela['prazoDias'],
                'dataVencimento' => $data->format('Y-m-d'), 'data' => $data->format('Y-m-d'),
                'diasUteisAdicionais' => $parcela['boleto'] === 'S' ? $diasUteisCreditoBoleto : 0, 'valor' => $centavos / 100);
        }
    }
    return $resultado;
}

// Confere os resultados configurados antes de associá-los às parcelas de T090.
function dashboardCaixaPrevisaoIndexarSaldos($linhas, $lote, $empresas, &$erro)
{
    $saldos = array();
    foreach ($linhas as $linha) {
        $pedido = isset($linha['pedido']) ? (string) $linha['pedido'] : '';
        $empresa = isset($linha['empresa']) ? (string) $linha['empresa'] : '';
        if (!preg_match('/^[1-9][0-9]{0,9}$/', $pedido) || !in_array((int) $pedido, $lote, true)
            || !preg_match('/^[1-9][0-9]{0,9}$/', $empresa) || !in_array((int) $empresa, $empresas, true)
            || !isset($linha['fatorPendente'], $linha['financeiroAntecipado'], $linha['valorPedido'])
            || !is_numeric($linha['fatorPendente']) || !is_finite((float) $linha['fatorPendente'])
            || !is_numeric($linha['valorPedido']) || !is_finite((float) $linha['valorPedido'])
            || !in_array((string) $linha['financeiroAntecipado'], array('0', '1'), true)
            || isset($saldos[$pedido])) {
            $erro = 'A consulta de saldos retornou um pedido inválido, duplicado ou fora do lote/empresa.';
            return false;
        }
        $saldos[$pedido] = $linha;
    }
    if (count($saldos) !== count($lote)) {
        $erro = 'Não foi possível conferir o saldo de todos os pedidos do lote.';
        return false;
    }
    return $saldos;
}

function dashboardCaixaPrevisaoMesclarSaldos($parcelas, $saldos, &$erro)
{
    $encontrados = array();
    foreach ($parcelas as &$parcela) {
        $pedido = isset($parcela['pedido']) ? (string) $parcela['pedido'] : '';
        if (!isset($saldos[$pedido], $parcela['empresa'], $parcela['valorPedido'])
            || (string) $saldos[$pedido]['empresa'] !== (string) $parcela['empresa']
            || (string) $saldos[$pedido]['valorPedido'] !== (string) $parcela['valorPedido']) {
            $erro = 'As parcelas e o saldo do pedido não conferem. Atualize o indicador.';
            return false;
        }
        $parcela['fatorPendente'] = $saldos[$pedido]['fatorPendente'];
        $parcela['financeiroAntecipado'] = $saldos[$pedido]['financeiroAntecipado'];
        $encontrados[$pedido] = true;
    }
    unset($parcela);
    if (count($encontrados) !== count($saldos)) {
        $erro = 'A consulta de parcelas não retornou todos os pedidos conferidos.';
        return false;
    }
    return $parcelas;
}

// Candidatos, saldos e parcelas usam a mesma conexão e os filtros do JSON.
function dashboardCaixaPrevisaoConsultarPedidos($conexao, $indicador, $parametros, &$erro)
{
    $config = $indicador['previsaoCaixa'];
    $empresaSelecionada = dashboardCaixaEmpresaSelecionada($config, isset($parametros['abrangenciaEmpresa']) ? $parametros['abrangenciaEmpresa'] : 'atual', $erro);
    if ($empresaSelecionada === false) return false;
    $sql = dashboardPersonalizadoSqlTexto($config['sqlPedidos']);
    if (!dashboardIndicadorValidarSql($sql, $erro)) return false;
    $separarSaldos = getenv('HARDNESS_DASHBOARD_ORDER_BALANCE_FAST_PATH') !== '0'
        && (!empty($config['sqlPedidosSaldos']) || !empty($config['sqlPedidosParcelas']));
    $sqlSaldos = '';
    if ($separarSaldos) {
        if (empty($config['sqlPedidosSaldos']) || empty($config['sqlPedidosParcelas']) || empty($config['sqlPedidosCandidatos'])) {
            $erro = 'Configure as consultas de candidatos, saldos e parcelas para separar o cálculo por pedido.';
            return false;
        }
        $sqlSaldos = dashboardPersonalizadoSqlTexto($config['sqlPedidosSaldos']);
        $sql = dashboardPersonalizadoSqlTexto($config['sqlPedidosParcelas']);
        if (!dashboardIndicadorValidarSql($sqlSaldos, $erro) || !dashboardIndicadorValidarSql($sql, $erro)) return false;
        if (!preg_match('/:pedidoInicio\b/', $sqlSaldos) || !preg_match('/:pedidoFim\b/', $sqlSaldos)) {
            $erro = 'Configure os limites de pedido na consulta de saldos.'; return false;
        }
    }
    if (empty($config['sqlPedidosCandidatos'])) return dashboardIndicadorConsultar($conexao, $sql, $parametros, $erro);
    $candidatosSql = dashboardPersonalizadoSqlTexto($config['sqlPedidosCandidatos']);
    if (!dashboardIndicadorValidarSql($candidatosSql, $erro)) return false;
    if (!preg_match('/:pedidoInicio\b/', $sql) || !preg_match('/:pedidoFim\b/', $sql)) {
        $erro = 'Configure os limites de pedido na consulta de parcelas.'; return false;
    }
    $candidatos = dashboardIndicadorConsultar($conexao, $candidatosSql, $parametros, $erro);
    if ($candidatos === false) return false;
    if (count($candidatos) > 5000) { $erro = 'Mais de 5.000 pedidos candidatos. Reduza o período de emissão.'; return false; }
    $ids = array();
    foreach ($candidatos as $candidato) {
        $id = isset($candidato['pedido']) ? (string) $candidato['pedido'] : '';
        if (!preg_match('/^[1-9][0-9]{0,9}$/', $id)) { $erro = 'A consulta de candidatos retornou um pedido inválido.'; return false; }
        $ids[(int) $id] = (int) $id;
    }
    $ids = array_values($ids); sort($ids, SORT_NUMERIC);
    $empresas = array();
    if ($separarSaldos) {
        $empresas = isset($parametros['abrangenciaEmpresa']) && $parametros['abrangenciaEmpresa'] === 'todas'
            ? array_values(array_unique(array_map('intval', isset($config['empresas']) ? $config['empresas'] : array())))
            : array((int) $parametros['empresa']);
        if ($empresaSelecionada !== null) $empresas = array($empresaSelecionada);
    }
    $linhas = array(); $inicio = 0; $tamanho = 25;
    while ($inicio < count($ids)) {
        $lote = array_slice($ids, $inicio, $tamanho);
        $parametrosLote = array_merge($parametros, array('pedidoInicio' => $lote[0], 'pedidoFim' => $lote[count($lote) - 1]));
        $erro = '';
        $saldos = null; $falhaSaldo = false;
        if ($separarSaldos) {
            // Uma linha por pedido antes de expandir T090; mesma conexão/transação.
            $resultadoSaldos = dashboardIndicadorConsultar($conexao, $sqlSaldos, $parametrosLote, $erro);
            $falhaSaldo = $resultadoSaldos === false;
            if (!$falhaSaldo) {
                $saldos = dashboardCaixaPrevisaoIndexarSaldos($resultadoSaldos, $lote, $empresas, $erro);
                if ($saldos === false) return false;
            }
        }
        $parcelas = $falhaSaldo ? false : dashboardIndicadorConsultar($conexao, $sql, $parametrosLote, $erro);
        if ($parcelas === false) {
            $numero = isset($GLOBALS['dashboardConsultaErroNumero']) ? (int) $GLOBALS['dashboardConsultaErroNumero'] : 0;
            if (in_array($numero, array(3024, 1969, 1317), true) && count($lote) > 1) { $tamanho = max(1, (int) floor(count($lote) / 2)); continue; }
            return false;
        }
        if ($separarSaldos) {
            $parcelas = dashboardCaixaPrevisaoMesclarSaldos($parcelas, $saldos, $erro);
            if ($parcelas === false) return false;
        }
        // Uma falha posterior invalida toda a carga; nunca devolver totais parciais.
        $linhas = array_merge($linhas, $parcelas);
        if (count($linhas) > 5000) { $erro = 'A simulação excedeu 5.000 parcelas. Reduza o período de emissão.'; return false; }
        $inicio += count($lote);
    }
    $erro = '';
    return $linhas;
}

function dashboardCaixaPrevisaoCarregar($conexao, $indicador, $empresa, $parametros, &$erro)
{
    $empresaSelecionada = dashboardCaixaEmpresaSelecionada($indicador['previsaoCaixa'], isset($parametros['abrangenciaEmpresa']) ? $parametros['abrangenciaEmpresa'] : 'atual', $erro);
    if ($empresaSelecionada === false) return false;
    $empresas = array((int) $empresa);
    if (isset($parametros['abrangenciaEmpresa']) && $parametros['abrangenciaEmpresa'] === 'todas') {
        $empresas = isset($indicador['previsaoCaixa']['empresas']) && is_array($indicador['previsaoCaixa']['empresas'])
            ? array_values(array_unique(array_filter(array_map('intval', $indicador['previsaoCaixa']['empresas']), function ($id) { return $id > 0; }))) : $empresas;
    }
    if ($empresaSelecionada !== null) $empresas = array($empresaSelecionada);
    $linhas = array(); $lancamentos = array();
    foreach ($empresas as $empresaPrevisao) {
        $itens = dashboardCaixaPrevisaoLer(dashboardCaixaPrevisaoArquivo($empresaPrevisao, $indicador['id']), $erro);
        if ($itens === false) return false;
        foreach ($itens as $item) {
            $item['empresa'] = $empresaPrevisao; $item['editavel'] = $empresaPrevisao === (int) $empresa;
            $lancamentos[] = $item;
            if ($item['valor'] < $parametros['valorMinimo']) continue;
            $linhas[] = array('contaId' => 0, 'serie' => 'previsao_pagar', 'documento' => 'Previsão manual', 'descricao' => $item['descricao'],
                'situacao' => 'Previsão manual', 'statusTituloDescricao' => 'Simulação', 'empresa' => $empresaPrevisao,
                'data' => $item['data'], 'dataVencimento' => $item['data'], 'diasUteisAdicionais' => 0, 'valor' => $item['valor']);
        }
    }
    $pendencias = array();
    $simulacao = array(
        'habilitada' => isset($parametros['simularPedidos']) && $parametros['simularPedidos'] === 'S',
        'empresa' => (int) $empresa,
        'abrangenciaEmpresa' => isset($parametros['abrangenciaEmpresa']) ? $parametros['abrangenciaEmpresa'] : 'atual',
        'emissaoPedidosInicio' => isset($parametros['emissaoPedidosInicio']) ? $parametros['emissaoPedidosInicio'] : '',
        'dataInicio' => isset($parametros['dataInicio']) ? $parametros['dataInicio'] : '',
        'dataFim' => isset($parametros['dataFim']) ? $parametros['dataFim'] : '',
        'parcelasConsultadas' => 0, 'pedidosConsultados' => 0, 'semSaldoPendente' => 0,
        'dadosIncompletos' => 0, 'parcelasGeradas' => 0, 'parcelasAcimaMinimo' => 0,
        'parcelasNoPeriodo' => 0, 'valorNoPeriodo' => 0, 'parcelasForaContas' => 0,
    );
    if ($simulacao['habilitada']) {
        $diasUteisCreditoBoleto = isset($indicador['previsaoCaixa']['diasUteisCreditoBoleto']) ? $indicador['previsaoCaixa']['diasUteisCreditoBoleto'] : 2;
        if (!is_int($diasUteisCreditoBoleto) || $diasUteisCreditoBoleto < 0 || $diasUteisCreditoBoleto > 15) {
            $erro = 'Configure um prazo inteiro de 0 a 15 dias úteis para o crédito dos boletos dos pedidos.';
            return false;
        }
        $parcelas = dashboardCaixaPrevisaoConsultarPedidos($conexao, $indicador, $parametros, $erro);
        if ($parcelas === false) { $erro = 'Falha ao consultar os pedidos da simulação. ' . $erro; return false; }
        if (count($parcelas) > 5000) { $erro = 'A simulação excedeu 5.000 parcelas. Reduza o período de emissão dos pedidos.'; return false; }
        $pedidos = dashboardCaixaPrevisaoPedidos($parcelas, $pendencias, $resumo, $diasUteisCreditoBoleto);
        $simulacao = array_merge($simulacao, $resumo);
        $simulacao['parcelasConsultadas'] = count($parcelas);
        $simulacao['parcelasGeradas'] = count($pedidos);
        foreach ($pedidos as $pedido) if ($pedido['valor'] >= $parametros['valorMinimo']) {
            $simulacao['parcelasAcimaMinimo']++;
            $linhas[] = $pedido;
        }
    }
    usort($lancamentos, function ($a, $b) { return strcmp($a['data'], $b['data']); });
    return array('linhas' => $linhas, 'lancamentos' => $lancamentos, 'pedidosPendentes' => $pendencias, 'simulacao' => $simulacao);
}

function dashboardCaixaPrevisaoMensagem($simulacao)
{
    if (!$simulacao['habilitada']) return 'Simulação de pedidos desativada na consulta aplicada.';
    if ($simulacao['parcelasConsultadas'] === 0) return 'Nenhum pedido encontrado para a empresa e o período de emissão aplicados.';
    if ($simulacao['parcelasGeradas'] === 0) {
        return 'Nenhuma parcela simulada: ' . $simulacao['semSaldoPendente'] . ' pedido(s) sem saldo pendente de faturamento e '
            . $simulacao['dadosIncompletos'] . ' pedido(s) com dados incompletos ou financeiro antecipado.';
    }
    if ($simulacao['parcelasAcimaMinimo'] === 0) return 'As parcelas simuladas ficaram abaixo do valor mínimo aplicado.';
    if ($simulacao['parcelasNoPeriodo'] === 0 && !empty($simulacao['parcelasForaContas'])) return 'Nenhum recebimento de pedido nas contas e no período selecionados.';
    if ($simulacao['parcelasNoPeriodo'] === 0) return 'Os recebimentos simulados ficaram fora do período após o ajuste de dias úteis.';
    return '';
}
