<?php
namespace hardness;

function dashboardBarrasEmpilhadasHabilitado($indicador)
{
    return !empty($indicador['grafico']) && (!isset($indicador['grafico']['habilitado']) || $indicador['grafico']['habilitado'])
        && isset($indicador['grafico']['tipo']) && $indicador['grafico']['tipo'] === 'barras_empilhadas';
}

function dashboardBarrasEmpilhadasPreparar($indicador, $entrada, &$parametros, &$erro)
{
    if (!dashboardBarrasEmpilhadasHabilitado($indicador)) return $indicador;
    $grafico = $indicador['grafico'];
    foreach (array('campoData', 'campoGrupo', 'campoValor') as $campo) {
        if (empty($grafico[$campo]) || dashboardIndicadorIdentificador($grafico[$campo]) === '') {
            $erro = 'Configure data, grupo e valor do gráfico de barras.'; return false;
        }
    }
    $inicio = isset($parametros['dataInicio']) ? (string) $parametros['dataInicio'] : '';
    $fim = isset($parametros['dataFim']) ? (string) $parametros['dataFim'] : '';
    $dataInicio = \DateTimeImmutable::createFromFormat('!Y-m-d', $inicio);
    $dataFim = \DateTimeImmutable::createFromFormat('!Y-m-d', $fim);
    if (!$dataInicio || !$dataFim || $dataInicio->format('Y-m-d') !== $inicio || $dataFim->format('Y-m-d') !== $fim || $inicio > $fim) {
        $erro = 'Informe um período válido para o gráfico de barras.'; return false;
    }
    $meses = ((int) $dataFim->format('Y') - (int) $dataInicio->format('Y')) * 12 + (int) $dataFim->format('m') - (int) $dataInicio->format('m') + 1;
    if ($meses > 120) { $erro = 'Selecione até 120 meses para o gráfico de barras.'; return false; }
    $periodo = isset($entrada['listaGraficoPeriodo']) ? $entrada['listaGraficoPeriodo'] : '';
    $serie = isset($entrada['listaGraficoSerie']) ? $entrada['listaGraficoSerie'] : '';
    if (!is_string($periodo) || !is_string($serie) || strlen($serie) > 240
        || ($serie !== '' && $periodo === '')
        || ($periodo !== '' && (!preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])$/D', $periodo)
            || $periodo < substr($inicio, 0, 7) || $periodo > substr($fim, 0, 7)))) {
        $erro = 'Seleção do gráfico de barras inválida.'; return false;
    }
    unset($indicador['listagem']['_barras']);
    $indicador['grafico']['periodoFiltrado'] = array('dataInicio' => $inicio, 'dataFim' => $fim);
    $indicador['grafico']['selecao'] = array('periodo' => $periodo, 'serie' => $serie);
    if ($periodo !== '') {
        if (!dashboardIndicadorListagemPaginadaHabilitada($indicador)) { $erro = 'Configure a tabela paginada para filtrar pelo gráfico.'; return false; }
        $parametros['graficoPeriodo'] = $periodo;
        if ($serie !== '') $parametros['graficoSerie'] = $serie;
        $indicador['listagem']['_barras'] = array('campoData' => $grafico['campoData'], 'campoGrupo' => $grafico['campoGrupo']);
        $indicador['listagem']['observacaoRodape'] = 'Seleção do gráfico: ' . substr($periodo, 5) . '/' . substr($periodo, 0, 4)
            . ($serie !== '' ? ' · ' . $serie : ' · todas as categorias') . '. Os cartões mantêm o total do período.';
    }
    return $indicador;
}

function dashboardBarrasEmpilhadasFiltrarSql($sql, $lista, $parametros)
{
    if (empty($lista['_barras']) || !isset($parametros['graficoPeriodo'])) return $sql;
    $campoData = dashboardIndicadorIdentificador($lista['_barras']['campoData']);
    $campoGrupo = dashboardIndicadorIdentificador($lista['_barras']['campoGrupo']);
    $sql = 'SELECT dashboard_barras_lista.* FROM (' . $sql . ') AS dashboard_barras_lista'
        . " WHERE DATE_FORMAT(`{$campoData}`, '%Y-%m') = :graficoPeriodo";
    if (isset($parametros['graficoSerie'])) $sql .= " AND COALESCE(NULLIF(`{$campoGrupo}`, ''), 'Não informado') = :graficoSerie";
    return $sql;
}

function dashboardBarrasEmpilhadasConsultar($conexao, $sqlBase, $grafico, $parametros, &$erro)
{
    $data = dashboardIndicadorIdentificador($grafico['campoData']);
    $grupo = dashboardIndicadorIdentificador($grafico['campoGrupo']);
    $valor = dashboardIndicadorIdentificador($grafico['campoValor']);
    $sql = "SELECT DATE_FORMAT(`{$data}`, '%Y-%m') AS periodo, COALESCE(NULLIF(`{$grupo}`, ''), 'Não informado') AS serie,"
        . " COALESCE(SUM(`{$valor}`), 0) AS valor, COUNT(*) AS quantidade FROM ({$sqlBase}) AS dashboard_barras"
        . " WHERE `{$data}` IS NOT NULL GROUP BY periodo, serie ORDER BY periodo, serie LIMIT 5001";
    $linhas = dashboardIndicadorConsultar($conexao, $sql, $parametros, $erro);
    if ($linhas === false) return false;
    if (count($linhas) > 5000) { $erro = 'O gráfico excedeu 5.000 grupos. Reduza o período.'; return false; }
    $series = array();
    foreach ($linhas as $linha) $series[(string) $linha['serie']] = true;
    if (count($series) > 60) { $erro = 'O gráfico excedeu 60 categorias. Filtre uma conta ou origem.'; return false; }
    return $linhas;
}
