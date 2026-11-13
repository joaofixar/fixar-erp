<?php
namespace hardness;

// Ativação explícita por indicador. Não mantém estado entre requisições.
function dashboardBaseCompartilhadaObjetoSql($campos, $alias)
{
    $pares = array();
    foreach ($campos as $campo) $pares[] = "'" . $campo . "', CAST(" . $alias . '.`' . $campo . '` AS CHAR)';
    return 'JSON_OBJECT(' . implode(', ', $pares) . ')';
}

function dashboardBaseCompartilhadaPlanejar($indicador, $sqlBase, $sqlCard, &$erro)
{
    $erro = '';
    $config = isset($indicador['baseCompartilhada']) ? $indicador['baseCompartilhada'] : array();
    if (!is_array($config) || empty($config['habilitada'])) return null;
    // Formas complexas conservam o fluxo anterior; só a tabela simples e os
    // agrupamentos sem ordenação própria participam do reaproveitamento.
    foreach (array('grafico' => 'habilitado', 'composicao' => 'habilitada', 'geografia' => 'habilitada', 'graficosPorGrupo' => 'habilitado') as $nome => $flag) {
        if (!empty($indicador[$nome]) && (!isset($indicador[$nome][$flag]) || $indicador[$nome][$flag])) return null;
    }
    if (!empty($indicador['calendarios']) || !empty($indicador['comparativoRepresentantes']) || dashboardIndicadorListagemPaginadaHabilitada($indicador)) return null;
    $lista = isset($indicador['listagem']) ? $indicador['listagem'] : array();
    if (empty($lista) || (isset($lista['habilitada']) && !$lista['habilitada']) || !empty($lista['agrupamento']) || !empty($lista['detalhamento']) || !empty($indicador['card']['campoContagem'])) return null;
    $chave = dashboardIndicadorIdentificador(isset($config['campoChave']) ? $config['campoChave'] : '', '');
    $ordem = dashboardIndicadorIdentificador(isset($lista['ordenacao']['campo']) ? $lista['ordenacao']['campo'] : '', '');
    if ($chave === '' || $ordem === '' || !isset($lista['ordenacao']['direcao']) || strtoupper($lista['ordenacao']['direcao']) !== 'DESC') return null;
    $numerico = false; $camposLista = array($chave);
    foreach ((isset($lista['colunas']) ? $lista['colunas'] : array()) as $coluna) {
        $campo = dashboardIndicadorIdentificador(isset($coluna['campo']) ? $coluna['campo'] : '', '');
        if ($campo === '') { $erro = 'Coluna inválida na base compartilhada.'; return false; }
        $camposLista[] = $campo;
        if ($campo === $ordem && in_array(isset($coluna['formato']) ? $coluna['formato'] : '', array('numero', 'moeda', 'percentual'), true)) $numerico = true;
    }
    if (!$numerico) return null;
    $camposLista = array_values(array_unique($camposLista));
    if (!in_array($ordem, $camposLista, true)) return null;
    if (!dashboardIndicadorValidarSql($sqlBase, $erro, !empty($indicador['multiempresa']), !empty($indicador['permitirUnionAll']))) return false;
    $fonte = '(' . $sqlBase . ')';
    if (substr_count($sqlCard, $fonte) !== 1) return null;
    $camposCard = array('quantidade', 'valor');
    foreach (array_slice(isset($indicador['resumos']) ? $indicador['resumos'] : array(), 0, 8) as $indice => $resumo) $camposCard[] = 'resumo' . $indice;
    $limite = isset($lista['limite']) ? max(1, min(500, (int) $lista['limite'])) : 200;
    $partes = array("SELECT 'card' AS parte, 0 AS bloco, 0 AS ordemValor, '' AS ordemTexto, 0 AS ordemId, " . dashboardBaseCompartilhadaObjetoSql($camposCard, 'card') . ' AS dados FROM (' . str_replace($fonte, 'dashboard_base_unica', $sqlCard) . ') AS card');
    $colunasLista = '`' . implode('`, `', $camposLista) . '`';
    $partes[] = "SELECT 'lista' AS parte, 1 AS bloco, lista.`" . $ordem . "` AS ordemValor, '' AS ordemTexto, lista.`" . $chave . '` AS ordemId, ' . dashboardBaseCompartilhadaObjetoSql($camposLista, 'lista') . ' AS dados FROM (SELECT ' . $colunasLista . ' FROM dashboard_base_unica ORDER BY `' . $ordem . '` DESC, `' . $chave . '` ASC LIMIT ' . $limite . ') AS lista';
    $ids = isset($config['secoes']) && is_array($config['secoes']) ? array_values($config['secoes']) : array();
    if (!$ids || count($ids) > 6) return null;
    foreach ($ids as $id) if (!is_string($id)) { $erro = 'Bloco inválido na base compartilhada.'; return false; }
    if (count(array_unique($ids)) !== count($ids)) return null;
    $secoes = array();
    foreach ($ids as $indice => $id) {
        if (!is_string($id) || dashboardIndicadorIdentificador($id, '') !== $id) { $erro = 'Bloco inválido na base compartilhada.'; return false; }
        $secao = null;
        foreach ((isset($indicador['secoes']) ? $indicador['secoes'] : array()) as $candidata) if (isset($candidata['id']) && $candidata['id'] === $id) $secao = $candidata;
        if ($secao === null) { $erro = 'Bloco não encontrado na base compartilhada: ' . $id; return false; }
        $sql = dashboardPersonalizadoSqlTexto(isset($secao['sqlBase']) ? $secao['sqlBase'] : '');
        if (!dashboardIndicadorValidarSql($sql, $erro, false, !empty($secao['permitirUnionAll']))) return false;
        if (substr_count($sql, $fonte) !== 1) return null;
        $sql = str_replace($fonte, 'dashboard_base_unica', $sql);
        if (preg_match('/\b(?:ORDER\s+BY|LIMIT)\b/i', $sql)) return null;
        $campos = array();
        foreach ((isset($secao['colunas']) ? $secao['colunas'] : array()) as $coluna) {
            $campo = dashboardIndicadorIdentificador(isset($coluna['campo']) ? $coluna['campo'] : '', '');
            if ($campo === '') { $erro = 'Coluna inválida no bloco compartilhado.'; return false; }
            $campos[] = $campo;
        }
        if (!$campos) { $erro = 'Informe as colunas do bloco compartilhado.'; return false; }
        $parte = 'secao_' . $indice;
        $secoes[$parte] = array('id' => $id, 'campos' => $campos);
        $partes[] = "SELECT '" . $parte . "' AS parte, " . ($indice + 2) . ' AS bloco, 0 AS ordemValor, secao.`' . $campos[0] . "` AS ordemTexto, 0 AS ordemId, " . dashboardBaseCompartilhadaObjetoSql($campos, 'secao') . ' AS dados FROM (SELECT `' . implode('`, `', $campos) . '` FROM (' . $sql . ') AS dashboard_bloco ORDER BY `' . $campos[0] . '` LIMIT 501) AS secao';
    }
    return array('sql' => "SELECT saida.parte, saida.dados FROM (WITH dashboard_base_unica AS (" . $sqlBase . ")\n" . implode("\nUNION ALL\n", $partes) . ') AS saida ORDER BY saida.bloco, saida.ordemValor DESC, saida.ordemTexto ASC, saida.ordemId ASC',
        'camposCard' => $camposCard, 'camposLista' => $camposLista, 'chave' => $chave, 'limite' => $limite, 'secoes' => $secoes);
}

function dashboardBaseCompartilhadaSeparar($linhas, $plano, &$erro)
{
    $erro = ''; $resultado = array('card' => null, 'listagem' => array(), 'secoes' => array()); $ids = array();
    foreach ($plano['secoes'] as $secao) $resultado['secoes'][$secao['id']] = array();
    if (count($linhas) > 1 + $plano['limite'] + 501 * count($plano['secoes'])) { $erro = 'Resposta da base compartilhada excedeu o limite.'; return false; }
    foreach ($linhas as $linha) {
        $parte = isset($linha['parte']) ? $linha['parte'] : '';
        $dados = isset($linha['dados']) && is_string($linha['dados']) ? json_decode($linha['dados'], true) : null;
        if (!is_array($dados)) { $erro = 'Resposta inválida da base compartilhada.'; return false; }
        if ($parte === 'card') {
            if ($resultado['card'] !== null) { $erro = 'Totais duplicados na base compartilhada.'; return false; }
            foreach ($plano['camposCard'] as $campo) if (!isset($dados[$campo]) || !is_numeric($dados[$campo])) { $erro = 'Totais incompletos na base compartilhada.'; return false; }
            if (!ctype_digit((string) $dados['quantidade'])) { $erro = 'Contagem inválida na base compartilhada.'; return false; }
            $resultado['card'] = $dados;
        } elseif ($parte === 'lista') {
            foreach ($plano['camposLista'] as $campo) if (!array_key_exists($campo, $dados)) { $erro = 'Lista incompleta na base compartilhada.'; return false; }
            $chave = $dados[$plano['chave']];
            if (!is_scalar($chave) || (string) $chave === '' || isset($ids[(string) $chave])) { $erro = 'Chave inválida ou duplicada na base compartilhada.'; return false; }
            $ids[(string) $chave] = true;
            $resultado['listagem'][] = $dados;
            if (count($resultado['listagem']) > $plano['limite']) { $erro = 'Lista da base compartilhada excedeu o limite.'; return false; }
        } elseif (isset($plano['secoes'][$parte])) {
            $secao = $plano['secoes'][$parte];
            foreach ($secao['campos'] as $campo) if (!array_key_exists($campo, $dados)) { $erro = 'Bloco incompleto na base compartilhada.'; return false; }
            $resultado['secoes'][$secao['id']][] = $dados;
            if (count($resultado['secoes'][$secao['id']]) > 500) { $erro = 'O bloco excedeu 500 linhas. Reduza o período.'; return false; }
        } else { $erro = 'Resultado desconhecido na base compartilhada.'; return false; }
    }
    if ($resultado['card'] === null || count($resultado['listagem']) !== min($plano['limite'], (int) $resultado['card']['quantidade'])) { $erro = 'Resposta incompleta da base compartilhada.'; return false; }
    return $resultado;
}

function dashboardBaseCompartilhadaConsultar($conexao, $indicador, $sqlBase, $sqlCard, $parametros, &$erro)
{
    $erro = '';
    if (!isset($indicador['baseCompartilhada']) || !is_array($indicador['baseCompartilhada']) || empty($indicador['baseCompartilhada']['habilitada'])) return null;
    $versao = mysqli_get_server_info($conexao);
    if (stripos($versao, 'mariadb') !== false || version_compare($versao, '8.0.0', '<')) return null;
    $plano = dashboardBaseCompartilhadaPlanejar($indicador, $sqlBase, $sqlCard, $erro);
    if ($plano === null || $plano === false) return $plano;
    $linhas = dashboardIndicadorConsultar($conexao, $plano['sql'], $parametros, $erro);
    if ($linhas === false) return false;
    return dashboardBaseCompartilhadaSeparar($linhas, $plano, $erro);
}
