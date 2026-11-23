<?php
namespace hardness;

function dashboardPeriodosData($valor)
{
    if (!is_string($valor) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $valor)) return false;
    $data = \DateTimeImmutable::createFromFormat('!Y-m-d', $valor);
    return $data && $data->format('Y-m-d') === $valor ? $data : false;
}

function dashboardPeriodosDataAnterior($data, $periodo)
{
    $ano = (int) $data->format('Y');
    $mes = (int) $data->format('n');
    $dia = (int) $data->format('j');
    if ($periodo === 'mesAnterior') {
        $mes--;
        if ($mes === 0) { $mes = 12; $ano--; }
    } elseif ($periodo === 'anoAnterior') {
        $ano--;
    }
    // Não repetir o último dia de fevereiro/setembro para preencher dias inexistentes.
    return checkdate($mes, $dia, $ano) ? sprintf('%04d-%02d-%02d', $ano, $mes, $dia) : null;
}

function dashboardIndicadorComparativoParametrosCte($sql)
{
    // MySQL 8.0 pode perder parametros em CTEs aninhadas reutilizadas.
    // Vincula cada valor uma vez, fora da fonte, sem interpolar dados no SQL.
    $campos = array();
    $sql = preg_replace_callback('/:([a-z][a-zA-Z0-9_]*)/', function ($match) use (&$campos) {
        $nome = $match[1];
        $campos[$nome] = ':' . $nome . ' AS `' . $nome . '`';
        return '(SELECT `' . $nome . '` FROM dashboard_comparativo_parametros)';
    }, $sql);
    if (!$campos) return $sql;
    $cte = 'WITH dashboard_comparativo_parametros AS (SELECT ' . implode(', ', $campos) . ' LIMIT 1)';
    return preg_match('/^WITH\s+/i', $sql, $inicio)
        ? $cte . ', ' . substr($sql, strlen($inicio[0]))
        : $cte . ' ' . $sql;
}

// Os cartoes genericos reutilizam as datas e a variacao do Desempenho Comercial.
// Uma unica agregacao adicional atende todas as metricas, sem consultar por card.
function dashboardIndicadorComparativoCartoesPlanejar($indicador, $sqlBase, $sqlCard, $parametros, &$erro)
{
    $erro = '';
    if (empty($indicador['comparativoMesAnterior'])) return null;
    if (!dashboardTendenciaSeriesValidar($indicador['grafico'] ?? array(), $erro)) return false;
    $inicio = dashboardPeriodosData(isset($parametros['dataInicio']) ? $parametros['dataInicio'] : '');
    $fim = dashboardPeriodosData(isset($parametros['dataFim']) ? $parametros['dataFim'] : '');
    if (!$inicio || !$fim || $inicio > $fim) { $erro = 'Informe um período válido para comparar os cartões.'; return false; }
    $periodos = dashboardComercialPeriodosComparacao(array('inicio' => $inicio->format('Y-m-d'), 'fim' => $fim->format('Y-m-d')));
    $card = isset($indicador['card']) ? $indicador['card'] : array();
    $metricas = array('card' => array('alias' => 'valor', 'campo' => isset($card['campo']) ? $card['campo'] : 'valor',
        'calculo' => isset($card['calculo']) ? $card['calculo'] : 'soma', 'formato' => isset($card['formato']) ? $card['formato'] : 'numero'));
    foreach (array_slice(isset($indicador['resumos']) ? $indicador['resumos'] : array(), 0, 8) as $index => $resumo) {
        $metricas['resumo' . $index] = array('alias' => 'resumo' . $index, 'campo' => isset($resumo['campo']) ? $resumo['campo'] : '',
            'calculo' => 'soma', 'formato' => isset($resumo['formato']) ? $resumo['formato'] : 'numero', 'preservarNulo' => !empty($resumo['preservarNulo']));
    }
    foreach ($metricas as &$metrica) {
        $metrica['campo'] = dashboardIndicadorIdentificador($metrica['campo']);
        if ($metrica['campo'] === '') { $erro = 'Campo inválido no comparativo dos cartões.'; return false; }
        $metrica['formato'] = dashboardIndicadorFormatoPermitido($metrica['formato']);
    }
    unset($metrica);
    $multiplosMeses = $inicio->format('Y-m') !== $fim->format('Y-m');
    $parametros['dataInicio'] = $periodos['anterior']['inicio'];
    $parametros['dataFim'] = $periodos['anterior']['fim'];
    $sql = $sqlCard;
    if ($multiplosMeses) {
        $campoData = dashboardIndicadorIdentificador(isset($indicador['grafico']['campoData']) ? $indicador['grafico']['campoData'] : 'data', 'data');
        $colunas = array();
        foreach ($periodos as $periodo => $datas) {
            $pInicio = 'cartoes' . ucfirst($periodo) . 'Inicio'; $pFim = 'cartoes' . ucfirst($periodo) . 'Fim';
            if (array_key_exists($pInicio, $parametros) || array_key_exists($pFim, $parametros)) { $erro = 'Parâmetro reservado no comparativo dos cartões.'; return false; }
            $parametros[$pInicio] = $datas['inicio']; $parametros[$pFim] = $datas['fim'];
            $condicao = '`' . $campoData . '` >= :' . $pInicio . ' AND `' . $campoData . '` < DATE_ADD(:' . $pFim . ', INTERVAL 1 DAY)';
            foreach ($metricas as $metrica) {
                $expressao = 'CASE WHEN ' . $condicao . ' THEN ' . ($metrica['calculo'] === 'contagem' ? '1' : '`' . $metrica['campo'] . '`') . ' ELSE NULL END';
                $agregado = ($metrica['calculo'] === 'contagem' ? 'COUNT' : ($metrica['calculo'] === 'media' ? 'AVG' : 'SUM')) . '(' . $expressao . ')';
                $colunas[] = (!empty($metrica['preservarNulo']) ? $agregado : 'COALESCE(' . $agregado . ', 0)') . ' AS ' . $periodo . '_' . $metrica['alias'];
            }
        }
        // Consulta somente os dois meses de referencia, mesmo em filtros de anos.
        $parametros['dataInicio'] = min($periodos['atual']['inicio'], $periodos['anterior']['inicio']);
        $parametros['dataFim'] = max($periodos['atual']['fim'], $periodos['anterior']['fim']);
        $sql = 'SELECT ' . implode(', ', $colunas) . ' FROM (' . $sqlBase . ') AS dashboard_base';
    }
    $grafico = null;
    if (!empty($indicador['grafico']['comparativoMesAnterior']) && isset($indicador['grafico']['apresentacao']) && $indicador['grafico']['apresentacao'] === 'padrao') {
        $inicioAnterior = $inicio->modify('first day of this month')->modify('-1 month');
        $fimAnterior = $fim->modify('first day of this month')->modify('-1 month');
        $deslocar = function ($data, $mes) { return $mes->format('Y-m-') . str_pad(min((int) $data->format('j'), (int) $mes->format('t')), 2, '0', STR_PAD_LEFT); };
        $grafico = array('atual' => array('inicio' => $inicio->format('Y-m-d'), 'fim' => $fim->format('Y-m-d')),
            'anterior' => array('inicio' => $deslocar($inicio, $inicioAnterior), 'fim' => $deslocar($fim, $fimAnterior)), 'agrupamento' => $multiplosMeses ? 'mes' : 'dia');
        foreach (array('graficoAnteriorInicio' => $grafico['anterior']['inicio'], 'graficoAnteriorFim' => $grafico['anterior']['fim']) as $nome => $valor) {
            if (array_key_exists($nome, $parametros)) { $erro = 'Parâmetro reservado no comparativo da tendência.'; return false; }
            $parametros[$nome] = $valor;
        }
        // Um unico conjunto do periodo anterior atende seis cartoes e a linha.
        $parametros['dataInicio'] = min($parametros['dataInicio'], $grafico['anterior']['inicio']);
        $parametros['dataFim'] = max($parametros['dataFim'], $grafico['anterior']['fim']);
        $campoData = dashboardIndicadorIdentificador(isset($indicador['grafico']['campoData']) ? $indicador['grafico']['campoData'] : 'data', 'data');
        $campoValor = dashboardIndicadorIdentificador(isset($indicador['grafico']['campoValor']) ? $indicador['grafico']['campoValor'] : 'valor', 'valor');
        $calculo = isset($indicador['grafico']['calculo']) ? $indicador['grafico']['calculo'] : 'soma';
        $agregado = $calculo === 'contagem' ? 'COUNT(*)' : (($calculo === 'media' ? 'AVG' : 'SUM') . '(`' . $campoValor . '`)');
        $formatoData = $multiplosMeses ? '%Y-%m' : '%Y-%m-%d';
        $quantidade = dashboardIndicadorSqlQuantidade($indicador['grafico']) . dashboardTendenciaSeriesSql($indicador['grafico']);
        $jsonQuantidade = empty($indicador['grafico']['campoQuantidade']) ? '' : ", 'quantidade', serie.quantidade";
        if (dashboardTendenciaSeries($indicador['grafico'])) $jsonQuantidade .= ", 'series', JSON_EXTRACT(serie.series, '$')";
        $sqlSerie = "SELECT DATE_FORMAT(`{$campoData}`, '{$formatoData}') AS periodo, COALESCE({$agregado}, 0) AS valor{$quantidade} FROM dashboard_comparativo_fonte"
            . " WHERE `{$campoData}` >= :graficoAnteriorInicio AND `{$campoData}` < DATE_ADD(:graficoAnteriorFim, INTERVAL 1 DAY)"
            . ' GROUP BY periodo ORDER BY periodo LIMIT 501';
        $sql = str_replace('(' . $sqlBase . ')', 'dashboard_comparativo_fonte', $sql);
        $sql = 'SELECT dados_cartoes.*, (SELECT JSON_ARRAYAGG(JSON_OBJECT(\'periodo\', serie.periodo, \'valor\', serie.valor' . $jsonQuantidade . ')) FROM (' . $sqlSerie . ') AS serie) AS graficoAnterior FROM (' . $sql . ') AS dados_cartoes';
        $sql = 'WITH dashboard_comparativo_fonte AS (SELECT origem.* FROM (' . $sqlBase . ') AS origem LIMIT 18446744073709551615) ' . $sql;
    }
    if (preg_match('/\bWITH\b/i', $sqlBase)) $sql = dashboardIndicadorComparativoParametrosCte($sql);
    return array('sql' => 'SELECT dashboard_comparativo_cartoes.* FROM (' . $sql . ') AS dashboard_comparativo_cartoes',
        'parametros' => $parametros, 'periodos' => $periodos, 'metricas' => $metricas, 'multiplosMeses' => $multiplosMeses, 'grafico' => $grafico);
}

function dashboardIndicadorConsultarComparativoCartoes($conexao, $indicador, $sqlBase, $sqlCard, $resultadoCard, $parametros, &$erro)
{
    $plano = dashboardIndicadorComparativoCartoesPlanejar($indicador, $sqlBase, $sqlCard, $parametros, $erro);
    if ($plano === null || $plano === false) return $plano;
    $linhas = dashboardIndicadorConsultar($conexao, $plano['sql'], $plano['parametros'], $erro);
    if ($linhas === false) return false;
    if (count($linhas) !== 1) { $erro = 'Resposta inválida do comparativo dos cartões.'; return false; }
    $comparativo = array('periodos' => $plano['periodos'], 'multiplosMeses' => $plano['multiplosMeses'], 'metricas' => array());
    foreach ($plano['metricas'] as $chave => $metrica) {
        $atual = $plano['multiplosMeses'] ? $linhas[0] : $resultadoCard;
        $campoAtual = ($plano['multiplosMeses'] ? 'atual_' : '') . $metrica['alias'];
        $campoAnterior = ($plano['multiplosMeses'] ? 'anterior_' : '') . $metrica['alias'];
        if (!array_key_exists($campoAtual, $atual) || !array_key_exists($campoAnterior, $linhas[0])) { $erro = 'Métrica ausente no comparativo dos cartões.'; return false; }
        foreach (array($atual[$campoAtual], $linhas[0][$campoAnterior]) as $valor) if ($valor !== null && !is_numeric($valor)) { $erro = 'Valor inválido no comparativo dos cartões.'; return false; }
        $comparativo['metricas'][$chave] = dashboardComercialVariacao($atual[$campoAtual], $linhas[0][$campoAnterior], $metrica['formato'] === 'toneladas' ? 3 : 2);
        $comparativo['metricas'][$chave]['formato'] = $metrica['formato'];
    }
    if ($plano['grafico'] !== null) {
        if (!array_key_exists('graficoAnterior', $linhas[0])) { $erro = 'Série anterior ausente no comparativo da tendência.'; return false; }
        $serie = $linhas[0]['graficoAnterior'] === null ? array() : json_decode($linhas[0]['graficoAnterior'], true);
        if (!is_array($serie) || count($serie) > 500) { $erro = 'A série comparativa excedeu o limite ou está inválida. Reduza o período.'; return false; }
        $serie = dashboardTendenciaSeriesNormalizar($serie, $indicador['grafico'], $erro);
        if ($serie === false) return false;
        foreach ($serie as $ponto) {
            if (dashboardIndicadorSqlQuantidade($indicador['grafico']) !== '' && (!isset($ponto['quantidade']) || !is_numeric($ponto['quantidade']))) { $erro = 'Quantidade inválida na série anterior da tendência.'; return false; }
            if (!is_array($ponto) || !isset($ponto['periodo'], $ponto['valor']) || !preg_match('/^\d{4}-\d{2}(?:-\d{2})?$/', $ponto['periodo']) || !is_numeric($ponto['valor'])) { $erro = 'Valor inválido na série anterior da tendência.'; return false; }
        }
        $comparativo['grafico'] = array_merge($plano['grafico'], array('linhas' => $serie));
    }
    return $comparativo;
}

function dashboardIndicadorConsultarComparativoPeriodos($conexao, $grafico, $parametros, &$erro)
{
    $erro = '';
    $inicio = dashboardPeriodosData(isset($parametros['dataInicio']) ? $parametros['dataInicio'] : '');
    $fim = dashboardPeriodosData(isset($parametros['dataFim']) ? $parametros['dataFim'] : '');
    if (!$inicio || !$fim || $inicio > $fim) {
        $erro = 'Informe um período inicial e final válido para comparar a produção.';
        return false;
    }
    if ((int) $inicio->diff($fim)->format('%a') >= 3660) {
        $erro = 'Selecione até 3.660 dias para o gráfico comparativo.';
        return false;
    }
    $sqlBase = dashboardPersonalizadoSqlTexto(isset($grafico['sqlBase']) ? $grafico['sqlBase'] : '');
    if (!dashboardIndicadorValidarSql($sqlBase, $erro)) return false;
    $campoData = dashboardIndicadorIdentificador(isset($grafico['campoData']) ? $grafico['campoData'] : 'data');
    $campoValor = dashboardIndicadorIdentificador(isset($grafico['campoValor']) ? $grafico['campoValor'] : 'valor');
    if ($campoData === '' || $campoValor === '') {
        $erro = 'Os campos do gráfico comparativo são inválidos.';
        return false;
    }
    // Validar todos os parâmetros antes de iniciar as três leituras.
    dashboardIndicadorPrepararSql($sqlBase, $parametros, $valores, $tipos, $erro);
    if ($erro !== '') return false;

    $series = array(
        array('chave' => 'atual', 'rotulo' => 'Período filtrado'),
        array('chave' => 'mesAnterior', 'rotulo' => 'Mês anterior'),
        array('chave' => 'anoAnterior', 'rotulo' => 'Ano anterior'),
    );
    $linhas = array();
    $datas = array('atual' => array(), 'mesAnterior' => array(), 'anoAnterior' => array());
    for ($dia = $inicio; $dia <= $fim; $dia = $dia->modify('+1 day')) {
        $linha = array('periodo' => $dia->format('Y-m-d'), 'rotulo' => $dia->format('d/m/Y'));
        foreach ($series as $serie) {
            $chave = $serie['chave'];
            $data = $chave === 'atual' ? $dia->format('Y-m-d') : dashboardPeriodosDataAnterior($dia, $chave);
            $linha['data' . ucfirst($chave)] = $data;
            $linha[$chave] = $data === null ? null : 0.0;
            if ($data !== null) $datas[$chave][] = $data;
        }
        $linhas[] = $linha;
    }

    $sql = 'SELECT DATE(`' . $campoData . '`) AS data, COALESCE(SUM(`' . $campoValor . '`), 0) AS valor'
        . ' FROM (' . $sqlBase . ') AS dashboard_periodos'
        . ' WHERE `' . $campoData . '` >= :dataInicio AND `' . $campoData . '` <= :dataFim'
        . ' GROUP BY DATE(`' . $campoData . '`) ORDER BY data';
    foreach ($series as &$serie) {
        $chave = $serie['chave'];
        $serie['total'] = 0.0;
        $serie['dataInicio'] = $datas[$chave] ? min($datas[$chave]) : null;
        $serie['dataFim'] = $datas[$chave] ? max($datas[$chave]) : null;
        $porData = array();
        if ($datas[$chave]) {
            $parametrosSerie = $parametros;
            $parametrosSerie['dataInicio'] = $serie['dataInicio'];
            $parametrosSerie['dataFim'] = $serie['dataFim'];
            $registros = dashboardIndicadorConsultar($conexao, $sql, $parametrosSerie, $erro);
            if ($registros === false) return false;
            foreach ($registros as $registro) {
                $porData[(string) $registro['data']] = (float) $registro['valor'];
            }
        }
        foreach ($linhas as &$linha) {
            $data = $linha['data' . ucfirst($chave)];
            if ($data === null) continue;
            $linha[$chave] = isset($porData[$data]) ? $porData[$data] : 0.0;
            $serie['total'] += $linha[$chave];
        }
        unset($linha);
    }
    unset($serie);
    return array('linhas' => $linhas, 'series' => $series);
}
