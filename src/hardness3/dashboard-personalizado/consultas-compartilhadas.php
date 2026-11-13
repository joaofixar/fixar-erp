<?php
namespace hardness;

// Resultados locais a uma única execução autorizada. Nunca são persistidos.
function dashboardConsultasCompartilhadasChave($sql, $parametros)
{
    return hash('sha256', serialize(array($sql, $parametros)));
}

function dashboardConsultasCompartilhadasPlanejar($i, $base, $card, $parametros)
{
    $consultas = array();
    $adicionar = function ($sql, $campos, $ordem = '') use (&$consultas) {
        $consultas[] = array('sql' => $sql, 'campos' => $campos, 'ordem' => $ordem);
    };
    $camposCard = array('quantidade', 'valor');
    foreach (array_slice(isset($i['resumos']) ? $i['resumos'] : array(), 0, 8) as $n => $r) $camposCard[] = 'resumo' . $n;
    $adicionar($card, $camposCard);
    $campoCard = dashboardIndicadorIdentificador(isset($i['card']['campo']) ? $i['card']['campo'] : 'valor', 'valor');
    $ativo = function ($config, $flag) { return $config && (!isset($config[$flag]) || $config[$flag]); };
    $geo = isset($i['geografia']) ? $i['geografia'] : array();
    if ($ativo($geo, 'habilitada') && (!isset($geo['tipo']) || $geo['tipo'] === 'brasil')) {
        $uf = dashboardIndicadorIdentificador(isset($geo['campoUf']) ? $geo['campoUf'] : 'uf', 'uf');
        $valor = dashboardIndicadorIdentificador(isset($geo['campoValor']) ? $geo['campoValor'] : $campoCard, $campoCard);
        $cliente = dashboardIndicadorIdentificador(isset($geo['campoCliente']) ? $geo['campoCliente'] : 'descricao', 'descricao');
        $sql = "SELECT COALESCE(NULLIF(`{$uf}`, ''), 'Não informado') AS uf, COALESCE(SUM(`{$valor}`), 0) AS valor, COALESCE(AVG(`{$valor}`), 0) AS ticketMedio, COUNT(DISTINCT NULLIF(`{$cliente}`, '')) AS clientes FROM ({$base}) AS dashboard_base GROUP BY uf ORDER BY valor DESC";
        $adicionar($sql, array('uf', 'valor', 'ticketMedio', 'clientes'), '`valor` DESC');
        if (!empty($geo['campoCidade'])) {
            $cidade = dashboardIndicadorIdentificador($geo['campoCidade'], 'cidade');
            $sql = "SELECT COALESCE(NULLIF(`{$uf}`, ''), 'Não informado') AS uf, COALESCE(NULLIF(`{$cidade}`, ''), 'Não informado') AS cidade, COALESCE(SUM(`{$valor}`), 0) AS valor FROM ({$base}) AS dashboard_base GROUP BY uf, cidade HAVING valor > 0 ORDER BY uf, valor DESC";
            $adicionar($sql, array('uf', 'cidade', 'valor'), '`uf` ASC, `valor` DESC');
        }
    }
    $comp = isset($i['composicao']) ? $i['composicao'] : array();
    if ($ativo($comp, 'habilitada') && empty($comp['sqlBase'])) {
        $campo = dashboardIndicadorIdentificador(isset($comp['campo']) ? $comp['campo'] : 'situacao', 'situacao');
        $valor = dashboardIndicadorIdentificador(isset($comp['campoValor']) ? $comp['campoValor'] : $campoCard, $campoCard);
        $calculo = isset($comp['calculo']) ? $comp['calculo'] : 'soma';
        $agg = $calculo === 'contagem' ? 'COUNT(*)' : ($calculo === 'media' ? 'AVG(`' . $valor . '`)' : 'SUM(`' . $valor . '`)');
        $adicionar('SELECT `' . $campo . '` AS chave, COALESCE(' . $agg . ', 0) AS valor FROM (' . $base . ') AS dashboard_base GROUP BY `' . $campo . '`', array('chave', 'valor'));
    }
    $graf = isset($i['grafico']) ? $i['grafico'] : array();
    if ($ativo($graf, 'habilitado') && empty($graf['tipo'])) {
        $data = dashboardIndicadorIdentificador(isset($graf['campoData']) ? $graf['campoData'] : 'data', 'data');
        $valor = dashboardIndicadorIdentificador(isset($graf['campoValor']) ? $graf['campoValor'] : $campoCard, $campoCard);
        $calculo = isset($graf['calculo']) ? $graf['calculo'] : 'soma';
        $agg = $calculo === 'contagem' ? 'COUNT(*)' : ($calculo === 'media' ? 'AVG(`' . $valor . '`)' : 'SUM(`' . $valor . '`)');
        $inicio = isset($parametros['dataInicio']) ? substr((string) $parametros['dataInicio'], 0, 7) : '';
        $fim = isset($parametros['dataFim']) ? substr((string) $parametros['dataFim'], 0, 7) : '';
        $dia = $inicio !== '' && $inicio === $fim;
        $periodo = $dia ? '%Y-%m-%d' : '%Y-%m'; $rotulo = $dia ? '%d/%m' : '%m/%Y';
        $adicionar("SELECT DATE_FORMAT(`{$data}`, '{$periodo}') AS periodo, DATE_FORMAT(`{$data}`, '{$rotulo}') AS rotulo, COALESCE({$agg}, 0) AS valor FROM ({$base}) AS dashboard_base GROUP BY periodo, rotulo ORDER BY periodo", array('periodo', 'rotulo', 'valor'), '`periodo` ASC');
    }
    $grupos = isset($i['graficosPorGrupo']) ? $i['graficosPorGrupo'] : array();
    if ($ativo($grupos, 'habilitado')) {
        $campo = dashboardIndicadorIdentificador(isset($grupos['campoGrupo']) ? $grupos['campoGrupo'] : 'grupo', 'grupo');
        $data = dashboardIndicadorIdentificador(isset($grupos['campoData']) ? $grupos['campoData'] : 'data', 'data');
        $valor = dashboardIndicadorIdentificador(isset($grupos['campoValor']) ? $grupos['campoValor'] : $campoCard, $campoCard);
        $calculo = isset($grupos['calculo']) ? $grupos['calculo'] : 'soma';
        $agg = $calculo === 'contagem' ? 'COUNT(*)' : ($calculo === 'media' ? 'AVG(`' . $valor . '`)' : 'SUM(`' . $valor . '`)');
        $sql = "SELECT COALESCE(NULLIF(`{$campo}`, ''), 'Não informado') AS grupo, DATE(`{$data}`) AS data, COALESCE({$agg}, 0) AS valor FROM ({$base}) AS dashboard_base WHERE `{$data}` IS NOT NULL GROUP BY grupo, DATE(`{$data}`) ORDER BY grupo, data";
        $adicionar($sql, array('grupo', 'data', 'valor'), '`grupo` ASC, `data` ASC');
    }
    $lista = isset($i['listagem']) ? $i['listagem'] : array();
    // Listas simples, Pareto, paginação e detalhes seguem seus contratos atuais.
    if ($ativo($lista, 'habilitada') && !empty($lista['agrupamento']) && !dashboardIndicadorListagemPaginadaHabilitada($i)) {
        $agr = $lista['agrupamento'];
        $campo = dashboardIndicadorIdentificador(isset($agr['campo']) ? $agr['campo'] : 'descricao', 'descricao');
        $valor = dashboardIndicadorIdentificador(isset($agr['campoValor']) ? $agr['campoValor'] : $campoCard, $campoCard);
        $contagem = dashboardIndicadorIdentificador(isset($agr['campoContagem']) ? $agr['campoContagem'] : '', '');
        $campos = array($campo, 'quantidade', 'valor'); $extras = ''; $incluidos = array();
        foreach ((isset($agr['camposSoma']) ? $agr['camposSoma'] : array()) as $extra) {
            if (!is_string($extra)) continue;
            $extra = dashboardIndicadorIdentificador($extra, '');
            if ($extra === '' || $extra === $campo || in_array($extra, array('quantidade', 'valor', 'percentual'), true) || isset($incluidos[$extra])) continue;
            $incluidos[$extra] = true; $campos[] = $extra;
            $extras .= ', COALESCE(SUM(`' . $extra . '`), 0) AS `' . $extra . '`';
        }
        foreach ((isset($agr['campos']) ? $agr['campos'] : array()) as $extra) {
            $extra = dashboardIndicadorIdentificador($extra, '');
            if ($extra === '' || $extra === $campo || isset($incluidos[$extra])) continue;
            $incluidos[$extra] = true; $campos[] = $extra;
            $extras .= ', MAX(`' . $extra . '`) AS `' . $extra . '`';
        }
        $ordem = '`valor` DESC';
        if (!empty($lista['ordenacao'])) {
            $o = $lista['ordenacao'];
            $ordem = '`' . dashboardIndicadorIdentificador(isset($o['campo']) ? $o['campo'] : 'valor', 'valor') . '` ' . (isset($o['direcao']) && strtoupper($o['direcao']) === 'ASC' ? 'ASC' : 'DESC');
        }
        $limite = isset($lista['limite']) ? max(1, min(500, (int) $lista['limite'])) : 200;
        $grupoSql = "COALESCE(NULLIF(CAST(`{$campo}` AS CHAR), ''), 'Não informado')";
        $countSql = $contagem !== '' ? "COUNT(DISTINCT NULLIF(CAST(`{$contagem}` AS CHAR), ''))" : 'COUNT(*)';
        $ordemSql = empty($lista['ordenacao']) ? 'valor DESC' : $ordem;
        $sql = "SELECT {$grupoSql} AS `{$campo}`, {$countSql} AS quantidade, COALESCE(SUM(`{$valor}`), 0) AS valor{$extras} FROM ({$base}) AS dashboard_base GROUP BY `{$campo}` ORDER BY {$ordemSql} LIMIT {$limite}";
        // Se a ordenação usa campo não projetado, deixa a consulta original.
        preg_match('/^`([^`]+)`/', $ordem, $m);
        if (isset($m[1]) && in_array($m[1], $campos, true)) $adicionar($sql, $campos, $ordem);
    }
    if (count($consultas) < 2) return null;
    $partes = array();
    foreach ($consultas as $n => $q) {
        $pares = array();
        foreach ($q['campos'] as $campo) $pares[] = "'" . $campo . "', CAST(linha.`" . $campo . '` AS CHAR)';
        $consulta = str_replace('(' . $base . ')', 'dashboard_fonte_unica', $q['sql']);
        $ordem = $q['ordem'] === '' ? '' : 'ORDER BY ' . $q['ordem'];
        $partes[] = "SELECT {$n} AS parte, 0 AS ordem, NULL AS dados\nUNION ALL\nSELECT {$n} AS parte, ROW_NUMBER() OVER ({$ordem}) AS ordem, JSON_OBJECT(" . implode(', ', $pares) . ') AS dados FROM (' . $consulta . ') AS linha';
    }
    // LIMIT máximo do MySQL impede merge da CTE e preserva todas as linhas,
    // inclusive duplicadas. Uma materialização atende todos os agrupamentos.
    $sql = 'SELECT resultado.parte, resultado.ordem, resultado.dados FROM (WITH dashboard_fonte_unica AS (SELECT fonte.* FROM (' . $base . ") AS fonte LIMIT 18446744073709551615)\n" . implode("\nUNION ALL\n", $partes) . ') AS resultado ORDER BY resultado.parte, resultado.ordem';
    return array('sql' => $sql, 'consultas' => $consultas);
}

function dashboardConsultasCompartilhadasSeparar($linhas, $plano, &$erro)
{
    $resultado = array_fill(0, count($plano['consultas']), array()); $marcadores = array(); $ordens = array();
    foreach ($linhas as $linha) {
        if (!isset($linha['parte'], $linha['ordem']) || !ctype_digit((string) $linha['parte']) || !ctype_digit((string) $linha['ordem'])) { $erro = 'Resposta inválida das consultas compartilhadas.'; return false; }
        $n = (int) $linha['parte']; $ordem = (int) $linha['ordem'];
        if (!isset($plano['consultas'][$n]) || !array_key_exists('dados', $linha)) { $erro = 'Componente desconhecido na consulta compartilhada.'; return false; }
        if ($ordem === 0) {
            if (isset($marcadores[$n]) || $linha['dados'] !== null) { $erro = 'Componente duplicado na consulta compartilhada.'; return false; }
            $marcadores[$n] = true; $ordens[$n] = 0; continue;
        }
        if (!isset($marcadores[$n]) || $ordem !== $ordens[$n] + 1) { $erro = 'Resposta incompleta das consultas compartilhadas.'; return false; }
        $dados = is_string($linha['dados']) ? json_decode($linha['dados'], true) : null;
        if (!is_array($dados) || count($dados) !== count($plano['consultas'][$n]['campos'])) { $erro = 'Dados inválidos na consulta compartilhada.'; return false; }
        foreach ($plano['consultas'][$n]['campos'] as $campo) if (!array_key_exists($campo, $dados)) { $erro = 'Campo ausente na consulta compartilhada.'; return false; }
        $resultado[$n][] = $dados; $ordens[$n] = $ordem;
    }
    if (count($marcadores) !== count($plano['consultas']) || count($resultado[0]) !== 1) { $erro = 'Totais incompletos na consulta compartilhada.'; return false; }
    return $resultado;
}

function dashboardConsultasCompartilhadasPreparar($conexao, $i, $base, $card, $parametros, &$erro)
{
    if (!isset($i['consultasCompartilhadas']) || $i['consultasCompartilhadas'] !== true || (dashboardCargaAtiva() && !dashboardCargaGeralAtiva())
        || !empty($i['baseCompartilhada']['habilitada'])) return true;
    $versao = mysqli_get_server_info($conexao);
    if (stripos($versao, 'mariadb') !== false || version_compare($versao, '8.0.0', '<')) return true;
    $plano = dashboardConsultasCompartilhadasPlanejar($i, $base, $card, $parametros);
    if ($plano === null) return true;
    $linhas = dashboardIndicadorConsultar($conexao, $plano['sql'], $parametros, $erro);
    if ($linhas === false) return false;
    $resultados = dashboardConsultasCompartilhadasSeparar($linhas, $plano, $erro);
    if ($resultados === false) return false;
    foreach ($plano['consultas'] as $n => $q) $GLOBALS['dashboardConsultasCompartilhadas'][dashboardConsultasCompartilhadasChave($q['sql'], $parametros)] = $resultados[$n];
    return true;
}
