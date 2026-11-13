<?php
namespace hardness;

// Automático para leituras compatíveis. false e a variável de ambiente permitem
// desativar; os motores especializados e as telas editáveis mantêm seu contrato.
function dashboardCargaGeralHabilitada($indicador)
{
    return getenv('HARDNESS_DASHBOARD_PROGRESSIVE_GENERAL') !== '0'
        && (!isset($indicador['carregamentoProgressivo']) || $indicador['carregamentoProgressivo'] === true)
        && empty($indicador['listagem']['paginacao']['habilitada'])
        && empty($indicador['desempenhoComercial']['habilitado'])
        && empty($indicador['painelCotacoes']['habilitado'])
        && empty($indicador['clientesInativos']['habilitado'])
        && empty($indicador['abcClientes']['habilitado'])
        && empty($indicador['previsaoCaixa']['habilitada'])
        && empty($indicador['saldoCaixa']['habilitado'])
        && empty($indicador['listagem']['detalhamento']['edicaoTitulo']['habilitado'])
        && empty($indicador['listagem']['detalhamento']['agenda']['habilitarInclusao'])
        && empty($indicador['desempenhoComercial']['carregamentoProgressivo'])
        && empty($indicador['painelCotacoes']['carregamentoProgressivo']);
}

function dashboardCargaGeralAtiva()
{
    return !empty($GLOBALS['dashboardCarga']['geral']);
}

function dashboardCargaGeralLimitesObrigatorios()
{
    return empty($GLOBALS['dashboardCarga']['limitesOpcionais']);
}

function dashboardCargaGeralAssinaturaEntrada($entrada)
{
    // A execução completa e sua exportação usam a mesma assinatura. Contextos
    // que mudam os dados (aba, UF, Pareto, detalhes) continuam na assinatura.
    unset($entrada['action'], $entrada['cargaToken'], $entrada['carregarProgressivo'],
        $entrada['formato'], $entrada['escopo'], $entrada['somenteDetalhamento'], $entrada['diagnosticoCarga']);
    ksort($entrada);
    return $entrada;
}

function dashboardCargaGeralConsultar($conexao, $sql, $parametros, &$erro)
{
    $ctx = &$GLOBALS['dashboardCarga'];
    $chave = hash('sha256', serialize(array($sql, $parametros)));
    $arquivo = $ctx['dir'] . '/' . $chave . '.json';
    if (isset($ctx['fontesEmMemoria'][$chave])) {
        $erro = '';
        return $ctx['fontesEmMemoria'][$chave];
    }
    if (isset($ctx['estado']['consultas'][$chave])) {
        $memoriaAntes = memory_get_usage();
        $consulta = $ctx['estado']['consultas'][$chave];
        if (is_link($arquivo) || !is_file($arquivo) || filesize($arquivo) > 8388608) {
            throw new \RuntimeException('Fonte indisponível. Atualize o indicador.');
        }
        $json = @file_get_contents($arquivo);
        if ($json === false || !isset($consulta['hash']) || !hash_equals($consulta['hash'], hash('sha256', $json))) {
            throw new \RuntimeException('Fonte inválida. Atualize o indicador.');
        }
        $linhas = json_decode($json, true);
        if (!is_array($linhas) || count($linhas) !== $consulta['registros']) {
            throw new \RuntimeException('Fonte incompleta. Atualize o indicador.');
        }
        $erro = '';
        return dashboardCargaMemorizarFonte($chave, $linhas, strlen($json), memory_get_usage() - $memoriaAntes);
    }
    if ($ctx['leitura'] || $ctx['estado']['concluida']) {
        throw new \RuntimeException('Os dados desta visualização mudaram. Atualize o indicador antes de exportar.');
    }
    $semSnapshot = !empty($ctx['estado']['semSnapshot']) || count($ctx['estado']['consultas']) >= 100;
    if ($semSnapshot && dashboardCargaGeralLimitesObrigatorios()) {
        throw new \RuntimeException('O carregamento excedeu 100 fontes. Reduza a configuração do indicador.');
    }
    if (!$semSnapshot) dashboardCargaVerificarOrcamento();
    // Não modifica SQL, datas, agrupamentos, limites ou parâmetros. Cada fonte
    // é concluída integralmente; não soma agregados de períodos separados.
    $memoriaAntes = memory_get_usage();
    $linhas = dashboardIndicadorConsultarDireto($conexao, $sql, $parametros, $erro);
    if ($linhas === false) return false;
    $memoriaLinhas = memory_get_usage() - $memoriaAntes;
    if (!empty($ctx['lotesAgrupados'])) $ctx['lotesAgrupados']['executados']++;
    if (count($linhas) > 20000) {
        if (dashboardCargaGeralLimitesObrigatorios()) {
            throw new \RuntimeException('Uma fonte excedeu 20.000 linhas. Reduza o período ou os filtros.');
        }
        $semSnapshot = true;
    }
    // Ativação automática não cria um limite novo para indicadores legados.
    // Fontes grandes terminam nesta requisição, sem truncar nem reler o SQL.
    if ($semSnapshot) {
        $ctx['estado']['semSnapshot'] = true;
        return $linhas;
    }
    $json = json_encode($linhas, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
    if ($json === false) {
        if (dashboardCargaGeralLimitesObrigatorios()) throw new \RuntimeException('Não foi possível serializar a fonte. Atualize o indicador.');
        $ctx['estado']['semSnapshot'] = true;
        return $linhas;
    }
    $bytes = strlen($json);
    $totalBytes = isset($ctx['estado']['bytes']) ? $ctx['estado']['bytes'] : 0;
    if ($bytes > 8388608 || $totalBytes + $bytes > 67108864) {
        if (dashboardCargaGeralLimitesObrigatorios()) {
            throw new \RuntimeException('Os dados excederam o limite do carregamento. Reduza o período ou os filtros.');
        }
        $ctx['estado']['semSnapshot'] = true;
        return $linhas;
    }
    // O lock da carga cobre consulta, gravação e atualização do estado.
    $temp = @tempnam($ctx['dir'], 'fonte-');
    if ($temp === false) throw new \RuntimeException('Não foi possível preparar a fonte. Tente novamente.');
    try {
        if (!@chmod($temp, 0600) || @file_put_contents($temp, $json, LOCK_EX) !== $bytes
            || is_link($arquivo) || !@rename($temp, $arquivo)) {
            throw new \RuntimeException('Não foi possível salvar a fonte. Tente novamente.');
        }
    } finally {
        if (is_file($temp)) @unlink($temp);
    }
    $ctx['estado']['consultas'][$chave] = array('registros' => count($linhas), 'hash' => hash('sha256', $json));
    $ctx['estado']['bytes'] = $totalBytes + $bytes;
    $ctx['progresso'] = array('token' => $ctx['token'], 'fontesConcluidas' => count($ctx['estado']['consultas']));
    if (!empty($ctx['lotesAgrupados'])) return dashboardCargaMemorizarFonte($chave, $linhas, $bytes, $memoriaLinhas);
    throw new DashboardCargaPendente();
}
