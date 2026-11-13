<?php
namespace hardness;

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
    $parts = array();
    foreach ($queries as $id => $query) {
        preg_match_all('/:([a-z][a-zA-Z0-9_]*)/', $query['sql'], $matches);
        foreach (array_unique($matches[1]) as $name) if (!array_key_exists($name, $parametros)) { $erro = 'Filtro ausente na fonte compartilhada: ' . $name; return false; }
        $pairs = array();
        foreach ($query['campos'] as $field) $pairs[] = "'" . $field . "', CAST(linha.`" . $field . '` AS CHAR)';
        $querySql = str_replace($needle, 'dashboard_bi_fonte', $query['sql']);
        // Marcador distingue bloco vazio de resposta incompleta.
        $parts[] = "SELECT '" . $id . "' AS parte, 0 AS marcador, NULL AS dados\nUNION ALL\n"
            . "SELECT '" . $id . "' AS parte, 1 AS marcador, JSON_OBJECT(" . implode(', ', $pairs) . ') AS dados FROM (' . $querySql . ') AS linha';
    }
    // LIMIT impede o merge da CTE, mantendo uma única materialização da fonte.
    $sql = 'SELECT saida.parte, saida.marcador, saida.dados FROM (WITH dashboard_bi_fonte AS (SELECT origem.* FROM (' . $base
        . ") AS origem LIMIT 18446744073709551615)\n" . implode("\nUNION ALL\n", $parts) . ') AS saida';
    return array('sql' => $sql, 'consultas' => $queries);
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
        if (count($parts[$id]) > ($id === 'card' ? 1 : 500)) { $erro = 'O bloco compartilhado excedeu 500 linhas. Reduza os filtros.'; return false; }
    }
    if (count($markers) !== count($parts) || count($parts['card']) !== 1) { $erro = 'Resposta incompleta da fonte compartilhada.'; return false; }
    $card = $parts['card'][0]; unset($parts['card']);
    foreach ($card as $value) if ($value !== null && !is_numeric($value)) { $erro = 'Total inválido da fonte compartilhada.'; return false; }
    return array('card' => $card, 'secoes' => $parts);
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
    return dashboardFonteCompartilhadaSeparar($rows, $plan, $erro);
}
