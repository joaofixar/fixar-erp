<?php
namespace hardness;

// Só executa definições da personalização autorizada no servidor.
function dashboardConferenciaExecutar($conexao, $indicador, $parametros, $entrada, $filtros, &$erro)
{
    $id = $entrada['detalheBloco'];
    $grupo = isset($entrada['detalheBlocoGrupo']) ? $entrada['detalheBlocoGrupo'] : '';
    if (!is_string($id) || !preg_match('/^[a-zA-Z0-9_-]{1,80}$/', $id)
        || !is_scalar($grupo) || strlen((string) $grupo) > 200
        || empty($indicador['conferencia'][$id])) {
        $erro = 'Bloco de conferência inválido.';
        return false;
    }
    $def = $indicador['conferencia'][$id];
    $dia = isset($entrada['detalheBlocoDia']) ? $entrada['detalheBlocoDia'] : '';
    $campoDataDia = '';
    if ($dia !== '') {
        $campoDataDia = dashboardIndicadorIdentificador(isset($def['campoDataDia']) ? $def['campoDataDia'] : '');
        if (!is_string($dia) || !isset($def['camposDia'][$dia]) || $campoDataDia === '' || (string) $grupo === ''
            || !is_int($def['camposDia'][$dia]) || $def['camposDia'][$dia] < 0 || $def['camposDia'][$dia] > 6) {
            $erro = 'Dia inválido na conferência.';
            return false;
        }
    }
    $sql = dashboardPersonalizadoSqlTexto(isset($def['sqlBase']) ? $def['sqlBase'] : '');
    if (!dashboardIndicadorValidarSql($sql, $erro)) return false;
    preg_match_all('/:([a-z][a-zA-Z0-9_]*)/', $sql, $usados);
    foreach (array_unique($usados[1]) as $nome) {
        if (!array_key_exists($nome, $parametros)) {
            $erro = 'Filtro não configurado na conferência: ' . $nome;
            return false;
        }
    }
    $colunas = isset($def['colunas']) ? $def['colunas'] : array();
    $campos = array();
    foreach ($colunas as $coluna) {
        $campo = dashboardIndicadorIdentificador(isset($coluna['campo']) ? $coluna['campo'] : '');
        if ($campo === '') { $erro = 'Coluna inválida na conferência.'; return false; }
        $campos[] = '`' . $campo . '`';
    }
    if (!$campos) { $erro = 'Informe as colunas da conferência.'; return false; }
    $consulta = 'SELECT ' . implode(', ', $campos) . ' FROM (' . $sql . ') AS conferencia_base';
    if ((string) $grupo !== '') {
        $campoGrupo = dashboardIndicadorIdentificador(isset($def['campoGrupo']) ? $def['campoGrupo'] : '');
        if ($campoGrupo === '') { $erro = 'Este bloco não permite selecionar um grupo.'; return false; }
        $consulta .= ' WHERE `' . $campoGrupo . '` = :conferenciaGrupo';
        $parametros['conferenciaGrupo'] = (string) $grupo;
    }
    if ($dia !== '') {
        $consulta .= ' AND WEEKDAY(`' . $campoDataDia . '`) = :conferenciaDia';
        $parametros['conferenciaDia'] = $def['camposDia'][$dia];
    }
    // Uma linha extra detecta excesso e impede a apresentação de somas parciais.
    $linhas = dashboardIndicadorConsultar($conexao, $consulta . ' LIMIT 5001', $parametros, $erro);
    if ($linhas === false) return false;
    if (count($linhas) > 5000) {
        $erro = 'A conferência excedeu 5.000 registros. Reduza o período ou filtre a OP/máquina para conferir todos os valores.';
        return false;
    }
    $totais = array();
    foreach (isset($def['totais']) ? $def['totais'] : array() as $total) {
        $campo = isset($total['campo']) ? $total['campo'] : '';
        $valor = 0;
        $distintos = array();
        foreach ($linhas as $linha) {
            $item = isset($linha[$campo]) ? $linha[$campo] : null;
            if (isset($total['calculo']) && $total['calculo'] === 'distintos') {
                if ($item !== null) $distintos[(string) $item] = true;
            } else $valor += (float) $item;
        }
        if (isset($total['calculo']) && $total['calculo'] === 'distintos') $valor = count($distintos);
        $totais[] = array('titulo' => $total['titulo'], 'valor' => $valor, 'formato' => 'numero');
    }
    return array('conferencia' => array(
        'titulo' => isset($def['titulo']) ? $def['titulo'] : 'Registros considerados',
        'criterio' => isset($def['criterio']) ? $def['criterio'] : '',
        'grupo' => (string) $grupo,
        'colunas' => $colunas,
        'linhas' => $linhas,
        'totais' => $totais,
        'filtros' => $filtros,
    ));
}
