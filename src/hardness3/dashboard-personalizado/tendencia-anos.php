<?php
namespace hardness;

// Consulta somente o ano escolhido. Cartões, calendário e fonte atual não são recarregados.
function dashboardTendenciaAnoPlanejar($indicador, $sqlBase, $parametros, $ano, &$erro)
{
    $erro = '';
    $grafico = isset($indicador['grafico']) ? $indicador['grafico'] : array();
    $config = isset($grafico['comparativoAnos']) ? $grafico['comparativoAnos'] : array();
    $anos = isset($config['anos']) && is_array($config['anos']) ? $config['anos'] : array();
    if (($config['habilitado'] ?? false) !== true || !dashboardTendenciaSeries($grafico)
        || ($grafico['habilitado'] ?? true) === false || !dashboardTendenciaSeriesValidar($grafico, $erro)
        || !$anos || count($anos) > 10) {
        if ($erro === '') $erro = 'Este gráfico não possui comparação por ano configurada.';
        return false;
    }
    foreach ($anos as $opcao) {
        if ((!is_int($opcao) && !is_string($opcao)) || !preg_match('/^(19|20|21)\d{2}$/', (string) $opcao)) {
            $erro = 'Os anos de comparação configurados são inválidos.'; return false;
        }
    }
    if ((!is_int($ano) && !is_string($ano)) || !preg_match('/^(19|20|21)\d{2}$/', (string) $ano)
        || !in_array((int) $ano, array_map('intval', $anos), true)) {
        $erro = 'Selecione um dos anos de comparação disponíveis.'; return false;
    }
    $inicio = dashboardPeriodosData($parametros['dataInicio'] ?? '');
    $fim = dashboardPeriodosData($parametros['dataFim'] ?? '');
    if (!$inicio || !$fim || $inicio > $fim || (int) $inicio->diff($fim)->format('%a') >= 3660) {
        $erro = 'Informe um período válido de até 3.660 dias para comparar a tendência.'; return false;
    }
    if (!dashboardIndicadorValidarSql($sqlBase, $erro, !empty($indicador['multiempresa']), !empty($indicador['permitirUnionAll']))) return false;
    $campoData = dashboardIndicadorIdentificador($grafico['campoData'] ?? 'data');
    $campoValor = dashboardIndicadorIdentificador($grafico['campoValor'] ?? 'valor');
    if ($campoData === '' || $campoValor === '' || (!empty($grafico['campoQuantidade']) && dashboardIndicadorIdentificador($grafico['campoQuantidade']) === '')) {
        $erro = 'Os campos da tendência são inválidos.'; return false;
    }
    // Em intervalos que atravessam o ano, o ano escolhido corresponde ao início.
    $deslocamento = (int) $ano - (int) $inicio->format('Y');
    $deslocar = function ($data) use ($deslocamento) {
        $anoDestino = (int) $data->format('Y') + $deslocamento;
        $primeiro = new \DateTimeImmutable(sprintf('%04d-%02d-01', $anoDestino, (int) $data->format('n')));
        return $primeiro->format('Y-m-') . sprintf('%02d', min((int) $data->format('j'), (int) $primeiro->format('t')));
    };
    $atual = array('inicio' => $inicio->format('Y-m-d'), 'fim' => $fim->format('Y-m-d'));
    $anterior = array('inicio' => $deslocar($inicio), 'fim' => $deslocar($fim));
    $parametros['dataInicio'] = $anterior['inicio']; $parametros['dataFim'] = $anterior['fim'];
    $mensal = $inicio->format('Y-m') !== $fim->format('Y-m');
    $formato = $mensal ? '%Y-%m' : '%Y-%m-%d';
    $sql = "SELECT DATE_FORMAT(`{$campoData}`, '{$formato}') AS periodo, COALESCE(SUM(`{$campoValor}`), 0) AS valor"
        . dashboardIndicadorSqlQuantidade($grafico) . dashboardTendenciaSeriesSql($grafico)
        . ' FROM (' . $sqlBase . ') AS dashboard_tendencia_ano'
        . " WHERE `{$campoData}` >= :dataInicio AND `{$campoData}` < DATE_ADD(:dataFim, INTERVAL 1 DAY)"
        . ' GROUP BY periodo ORDER BY periodo LIMIT 501';
    if (preg_match('/\bWITH\b/i', $sqlBase)) $sql = dashboardIndicadorComparativoParametrosCte($sql);
    $rotulo = substr($anterior['inicio'], 0, 4);
    if ($rotulo !== substr($anterior['fim'], 0, 4)) $rotulo .= '–' . substr($anterior['fim'], 0, 4);
    return array('sql' => $sql, 'parametros' => $parametros, 'grafico' => $grafico,
        'comparativo' => array('atual' => $atual, 'anterior' => $anterior, 'ano' => (int) $ano,
            'rotulo' => $rotulo, 'tipo' => 'ano', 'agrupamento' => $mensal ? 'mes' : 'dia'));
}

function dashboardTendenciaAnoConsultar($conexao, $indicador, $sqlBase, $parametros, $ano, &$erro)
{
    $plano = dashboardTendenciaAnoPlanejar($indicador, $sqlBase, $parametros, $ano, $erro);
    if ($plano === false) return false;
    $linhas = dashboardIndicadorConsultar($conexao, $plano['sql'], $plano['parametros'], $erro);
    if ($linhas === false) return false;
    if (count($linhas) > 500) { $erro = 'A tendência ultrapassou o limite de pontos. Reduza o período.'; return false; }
    $linhas = dashboardTendenciaSeriesNormalizar($linhas, $plano['grafico'], $erro);
    if ($linhas === false) return false;
    $datas = array();
    foreach ($linhas as $linha) {
        $data = $linha['periodo'] ?? '';
        $diario = $plano['comparativo']['agrupamento'] === 'dia';
        $valida = is_string($data) && ($diario ? dashboardPeriodosData($data) !== false : preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $data));
        $inicio = substr($plano['comparativo']['anterior']['inicio'], 0, $diario ? 10 : 7);
        $fim = substr($plano['comparativo']['anterior']['fim'], 0, $diario ? 10 : 7);
        if (!$valida || $data < $inicio || $data > $fim || isset($datas[$data])
            || !isset($linha['valor']) || !is_numeric($linha['valor']) || !is_finite((float) $linha['valor'])
            || (!empty($plano['grafico']['campoQuantidade']) && (!isset($linha['quantidade']) || !is_numeric($linha['quantidade']) || !is_finite((float) $linha['quantidade'])))) {
            $erro = 'Resposta inválida da tendência do ano escolhido.'; return false;
        }
        $datas[$data] = true;
    }
    return array_merge($plano['comparativo'], array('linhas' => $linhas));
}
