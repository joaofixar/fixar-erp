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
