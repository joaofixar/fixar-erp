<?php
namespace hardness;

require_once __DIR__ . '/consultas-compartilhadas.php';

// Reutilização opt-in, somente nesta requisição. Não cria tabelas, não mantém
// cache entre usuários e não modifica os SQLs ou filtros da personalização.
function dashboardFonteCompartilhadaPlanejar($indicador, $sqlCard, $parametros, &$erro)
{
    $erro = '';
    $config = isset($indicador['fonteCompartilhada']) ? $indicador['fonteCompartilhada'] : array();
    if (empty($config['habilitada'])) return null;
    $base = dashboardPersonalizadoSqlTexto(isset($config['sqlBase']) ? $config['sqlBase'] : '');
    if (!dashboardIndicadorValidarSql($base, $erro, false, !empty($indicador['permitirUnionAll']))) return false;
    $needle = '(' . $base . ')';
    if (substr_count($sqlCard, $needle) !== 1) return null;
    $ids = isset($config['secoes']) && is_array($config['secoes']) ? $config['secoes'] : array();
    if (!$ids || count($ids) > 6) return null;
    foreach ($ids as $id) if (!is_string($id) || dashboardIndicadorIdentificador($id, '') !== $id) { $erro = 'Bloco inválido na fonte compartilhada.'; return false; }
    if (count(array_unique($ids)) !== count($ids)) return null;
    $fields = array('quantidade', 'valor');
    foreach (array_slice(isset($indicador['resumos']) ? $indicador['resumos'] : array(), 0, 8) as $n => $unused) $fields[] = 'resumo' . $n;
    $queries = array('card' => array('sql' => $sqlCard, 'campos' => $fields));
    foreach ($ids as $id) {
        if (!is_string($id) || dashboardIndicadorIdentificador($id, '') !== $id) { $erro = 'Bloco inválido na fonte compartilhada.'; return false; }
        $found = null;
        foreach (isset($indicador['secoes']) ? $indicador['secoes'] : array() as $s) if (isset($s['id']) && $s['id'] === $id) $found = $s;
        if ($found === null || !empty($found['paginacao']['habilitada'])) return null;
        $sql = dashboardPersonalizadoSqlTexto(isset($found['sqlBase']) ? $found['sqlBase'] : '');
        if (!dashboardIndicadorValidarSql($sql, $erro, false, !empty($found['permitirUnionAll']))) return false;
        if (substr_count($sql, $needle) !== 1) return null;
        $columns = array();
        foreach (isset($found['colunas']) ? $found['colunas'] : array() as $column) {
            $field = dashboardIndicadorIdentificador(isset($column['campo']) ? $column['campo'] : '', '');
            if ($field === '') { $erro = 'Coluna inválida na fonte compartilhada.'; return false; }
            $columns[] = $field;
        }
        if (!$columns) { $erro = 'Bloco compartilhado sem colunas.'; return false; }
        $queries[$id] = array('sql' => 'SELECT `' . implode('`, `', $columns) . '` FROM (' . $sql . ') AS bloco LIMIT 501', 'campos' => $columns);
    }
    // Calendario e tendencia podem reutilizar a mesma fonte fiscal. O opt-in
    // exige os mesmos filtros; janelas independentes continuam no fluxo normal.
    if (isset($config['incluirSeries']) && $config['incluirSeries'] === true) {
        $graph = isset($indicador['grafico']) ? $indicador['grafico'] : array();
        if (!empty($graph) && (!isset($graph['habilitado']) || $graph['habilitado'])) {
            if (!empty($graph['tipo']) || isset($queries['_grafico'])) return null;
            $day = isset($parametros['dataInicio'], $parametros['dataFim']) && substr($parametros['dataInicio'], 0, 7) === substr($parametros['dataFim'], 0, 7);
            $date = dashboardIndicadorIdentificador(isset($graph['campoData']) ? $graph['campoData'] : 'data', 'data');
            $value = dashboardIndicadorIdentificador(isset($graph['campoValor']) ? $graph['campoValor'] : 'valor', 'valor');
            $calc = isset($graph['calculo']) ? $graph['calculo'] : 'soma';
            $aggregate = $calc === 'contagem' ? 'COUNT(*)' : (($calc === 'media' ? 'AVG' : 'SUM') . '(`' . $value . '`)');
            $period = $day ? '%Y-%m-%d' : '%Y-%m'; $label = $day ? '%d/%m' : '%m/%Y';
            $quantidade = dashboardIndicadorSqlQuantidade($graph) . dashboardTendenciaSeriesSql($graph);
            $queries['_grafico'] = array('tipo' => 'grafico', 'campos' => dashboardTendenciaSeriesCampos(dashboardIndicadorCamposSerie(array('periodo', 'rotulo', 'valor'), $graph), $graph), 'ordem' => '`periodo` ASC',
                'sql' => "SELECT DATE_FORMAT(`{$date}`, '{$period}') AS periodo, DATE_FORMAT(`{$date}`, '{$label}') AS rotulo, COALESCE({$aggregate}, 0) AS valor{$quantidade} FROM ({$base}) AS dashboard_base GROUP BY periodo, rotulo ORDER BY periodo LIMIT 501");
        }
        foreach (isset($indicador['calendarios']) ? $indicador['calendarios'] : array() as $index => $calendar) {
            if (!is_array($calendar) || (isset($calendar['habilitado']) && !$calendar['habilitado'])) continue;
            if (!is_int($index) || $index < 0) return null;
            if (dashboardIndicadorParametrosCalendario($calendar, $parametros) !== $parametros || isset($queries['_calendario' . $index])) return null;
            $date = dashboardIndicadorIdentificador(isset($calendar['campoData']) ? $calendar['campoData'] : 'data', 'data');
            $value = dashboardIndicadorIdentificador(isset($calendar['campoValor']) ? $calendar['campoValor'] : 'valor', 'valor');
            $calc = isset($calendar['calculo']) ? $calendar['calculo'] : 'soma';
            $aggregate = $calc === 'contagem' ? 'COUNT(*)' : (($calc === 'media' ? 'AVG' : 'SUM') . '(`' . $value . '`)');
            $quantidade = dashboardIndicadorSqlQuantidade($calendar);
            $queries['_calendario' . $index] = array('tipo' => 'calendario', 'indice' => $index, 'campos' => dashboardIndicadorCamposSerie(array('data', 'valor'), $calendar), 'ordem' => '`data` ASC',
                'sql' => "SELECT DATE(`{$date}`) AS data, COALESCE({$aggregate}, 0) AS valor{$quantidade} FROM ({$base}) AS dashboard_base WHERE `{$date}` IS NOT NULL GROUP BY DATE(`{$date}`) ORDER BY data LIMIT 501");
        }
    }
    $componentes = array();
    if (getenv('HARDNESS_DASHBOARD_SHARED_COMPONENTS') !== '0'
        && (!isset($indicador['consultasCompartilhadas']) || $indicador['consultasCompartilhadas'] !== false)) {
        $sqlBase = dashboardPersonalizadoSqlTexto(isset($indicador['sqlBase']) ? $indicador['sqlBase'] : '');
        // Só reúne componentes derivados da fonte exata já autorizada. Os
        // calendários com outro período e as consultas próprias ficam de fora.
        if ($sqlBase !== '' && substr_count($sqlCard, '(' . $sqlBase . ')') === 1) {
            // As series explicitas ja sao devolvidas diretamente ao motor.
            // Exclui-las da coleta automatica evita agregacoes duplicadas.
            $indicadorComponentes = $indicador;
            if (isset($queries['_grafico'])) $indicadorComponentes['grafico'] = array('habilitado' => false);
            if (isset($config['incluirSeries']) && $config['incluirSeries'] === true) $indicadorComponentes['calendarios'] = array();
            $consultas = dashboardConsultasCompartilhadasConsultas($indicadorComponentes, $sqlBase, $sqlCard, $parametros);
            foreach (array_slice($consultas, 1) as $n => $consulta) {
                if (isset($consulta['secao']) && isset($queries[$consulta['secao']])) continue;
                if (substr_count($consulta['sql'], $needle) !== 1) continue;
                $id = '__componente_' . $n;
                if (isset($queries[$id])) continue;
                $queries[$id] = $consulta;
                $componentes[$id] = true;
            }
        }
    }
    $parts = array();
    foreach ($queries as $id => $query) {
        preg_match_all('/:([a-z][a-zA-Z0-9_]*)/', $query['sql'], $matches);
        foreach (array_unique($matches[1]) as $name) if (!array_key_exists($name, $parametros)) { $erro = 'Filtro ausente na fonte compartilhada: ' . $name; return false; }
        $pairs = array();
        foreach ($query['campos'] as $field) $pairs[] = "'" . $field . "', CAST(linha.`" . $field . '` AS CHAR)';
        $querySql = str_replace($needle, 'dashboard_bi_fonte', $query['sql']);
        // Marcador distingue bloco vazio de resposta incompleta.
        $ordem = empty($query['ordem']) ? '' : 'ORDER BY ' . $query['ordem'];
        $parts[] = "SELECT '" . $id . "' AS parte, 0 AS marcador, 0 AS ordem, NULL AS dados\nUNION ALL\n"
            . "SELECT '" . $id . "' AS parte, 1 AS marcador, ROW_NUMBER() OVER (" . $ordem . ") AS ordem, JSON_OBJECT(" . implode(', ', $pairs) . ') AS dados FROM (' . $querySql . ') AS linha';
    }
    // LIMIT impede o merge da CTE, mantendo uma única materialização da fonte.
    $sql = 'SELECT saida.parte, saida.marcador, saida.dados FROM (WITH dashboard_bi_fonte AS (SELECT origem.* FROM (' . $base
        . ") AS origem LIMIT 18446744073709551615)\n" . implode("\nUNION ALL\n", $parts) . ') AS saida ORDER BY saida.parte, saida.ordem';
    return array('sql' => $sql, 'consultas' => $queries, 'componentes' => $componentes);
}

function dashboardFonteCompartilhadaSeparar($linhas, $plano, &$erro)
{
    $erro = ''; $parts = array(); $markers = array();
    foreach ($plano['consultas'] as $id => $unused) $parts[$id] = array();
    foreach ($linhas as $line) {
        $id = isset($line['parte']) ? $line['parte'] : '';
        if (!array_key_exists($id, $parts) || !isset($line['marcador']) || !array_key_exists('dados', $line)) { $erro = 'Resposta desconhecida da fonte compartilhada.'; return false; }
        if ((string) $line['marcador'] === '0') {
            if (isset($markers[$id]) || $line['dados'] !== null) { $erro = 'Marcador duplicado na fonte compartilhada.'; return false; }
            $markers[$id] = true; continue;
        }
        if ((string) $line['marcador'] !== '1' || !is_string($line['dados'])) { $erro = 'Resposta inválida da fonte compartilhada.'; return false; }
        $data = json_decode($line['dados'], true);
        if (!is_array($data)) { $erro = 'Dados inválidos na fonte compartilhada.'; return false; }
        foreach ($plano['consultas'][$id]['campos'] as $field) if (!array_key_exists($field, $data)) { $erro = 'Campo ausente na fonte compartilhada.'; return false; }
        $parts[$id][] = $data;
        if (empty($plano['componentes'][$id]) && count($parts[$id]) > ($id === 'card' ? 1 : 500)) { $erro = 'O bloco compartilhado excedeu 500 linhas. Reduza os filtros.'; return false; }
    }
    if (count($markers) !== count($parts) || count($parts['card']) !== 1) { $erro = 'Resposta incompleta da fonte compartilhada.'; return false; }
    $card = $parts['card'][0]; unset($parts['card']);
    $componentes = array();
    foreach (isset($plano['componentes']) ? $plano['componentes'] : array() as $id => $unused) {
        $componentes[$id] = $parts[$id];
        unset($parts[$id]);
    }
    foreach ($card as $value) if ($value !== null && !is_numeric($value)) { $erro = 'Total inválido da fonte compartilhada.'; return false; }
    $result = array('card' => $card, 'secoes' => array(), 'calendarios' => array(), 'componentes' => $componentes);
    foreach ($parts as $id => $rows) {
        $query = $plano['consultas'][$id];
        if (isset($query['tipo']) && $query['tipo'] === 'grafico') $result['grafico'] = $rows;
        elseif (isset($query['tipo']) && $query['tipo'] === 'calendario') $result['calendarios'][$query['indice']] = $rows;
        else $result['secoes'][$id] = $rows;
    }
    return $result;
}

function dashboardFonteCompartilhadaConsultar($conexao, $indicador, $sqlCard, $parametros, &$erro)
{
    if (empty($indicador['fonteCompartilhada']['habilitada'])) return null;
    $version = mysqli_get_server_info($conexao);
    // Servidores legados continuam nas consultas normais com os mesmos filtros.
    if (stripos($version, 'mariadb') !== false || version_compare($version, '8.0.0', '<')) return null;
    $plan = dashboardFonteCompartilhadaPlanejar($indicador, $sqlCard, $parametros, $erro);
    if ($plan === null || $plan === false) return $plan;
    $rows = dashboardIndicadorConsultar($conexao, $plan['sql'], $parametros, $erro);
    if ($rows === false) return false;
    $resultado = dashboardFonteCompartilhadaSeparar($rows, $plan, $erro);
    if ($resultado === false) return false;
    foreach ($resultado['componentes'] as $id => $linhas) {
        $chave = dashboardConsultasCompartilhadasChave($plan['consultas'][$id]['sql'], $parametros);
        $GLOBALS['dashboardConsultasCompartilhadas'][$chave] = $linhas;
    }
    unset($resultado['componentes']);
    return $resultado;
}
