<?php
namespace hardness;

// Resultados locais a uma única execução autorizada. Nunca são persistidos.
function dashboardConsultasCompartilhadasChave($sql, $parametros)
{
    return hash('sha256', serialize(array($sql, $parametros)));
}

function dashboardConsultasCompartilhadasHabilitadas($indicador)
{
    return getenv('HARDNESS_DASHBOARD_SHARED_QUERIES') !== '0'
        && (!isset($indicador['consultasCompartilhadas']) || $indicador['consultasCompartilhadas'] === true);
}

// Descobre apenas projeções explícitas e inequívocas. Não interpreta nem
// reescreve filtros/subconsultas. Expressões sem alias conservam o fluxo normal.
function dashboardConsultasCompartilhadasCampos($sql)
{
    if (!preg_match('/^SELECT\s+(?:(?:DISTINCT|ALL)\s+)?/i', $sql, $inicio)) return null;
    $campos = array(); $partes = array(); $parte = ''; $nivel = 0; $aspas = '';
    for ($n = strlen($inicio[0]), $fim = strlen($sql); $n < $fim; $n++) {
        $c = $sql[$n];
        if ($aspas !== '') {
            $parte .= $c;
            if ($c === '\\' && $n + 1 < $fim) $parte .= $sql[++$n];
            elseif ($c === $aspas) {
                if ($n + 1 < $fim && $sql[$n + 1] === $aspas) $parte .= $sql[++$n];
                else $aspas = '';
            }
            continue;
        }
        if ($c === "'" || $c === '"' || $c === '`') $aspas = $c;
        elseif ($c === '(') $nivel++;
        elseif ($c === ')') { if (--$nivel < 0) return null; }
        elseif ($nivel === 0 && preg_match('/\G(?:FROM|UNION|ORDER\s+BY|LIMIT)\b/i', $sql, $m, 0, $n)
            && ($n === 0 || !preg_match('/[a-zA-Z0-9_]/', $sql[$n - 1]))) break;
        elseif ($c === ',' && $nivel === 0) { $partes[] = trim($parte); $parte = ''; continue; }
        $parte .= $c;
    }
    if ($nivel !== 0 || $aspas !== '') return null;
    $partes[] = trim($parte);
    foreach ($partes as $item) {
        if (preg_match('/\s+AS\s+(`?)([a-zA-Z_][a-zA-Z0-9_]{0,63})\1$/i', $item, $m)) $campo = $m[2];
        elseif (preg_match('/^(?:`?[a-zA-Z_][a-zA-Z0-9_]*`?\.)?`?([a-zA-Z_][a-zA-Z0-9_]{0,63})`?$/', $item, $m)) $campo = $m[1];
        else return null;
        if (isset($campos[strtolower($campo)])) return null;
        $campos[strtolower($campo)] = $campo;
    }
    return $campos ? array_values($campos) : null;
}

function dashboardConsultasCompartilhadasConsultas($i, $base, $card, $parametros)
{
    $consultas = array(); $incluidas = array();
    $adicionar = function ($sql, $campos, $ordem = '') use (&$consultas, &$incluidas) {
        if (isset($incluidas[$sql])) return $incluidas[$sql];
        $incluidas[$sql] = count($consultas);
        $consultas[] = array('sql' => $sql, 'campos' => $campos, 'ordem' => $ordem);
        return count($consultas) - 1;
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
    $composicoes = array($comp);
    if ($ativo($comp, 'habilitada')) foreach (isset($comp['blocos']) && is_array($comp['blocos']) ? $comp['blocos'] : array() as $bloco) {
        if (!empty($bloco['campo'])) $composicoes[] = array_merge($comp, array('campo' => $bloco['campo'], 'sqlBase' => ''));
    }
    foreach ($composicoes as $comp) if ($ativo($comp, 'habilitada') && empty($comp['sqlBase'])) {
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
    if (getenv('HARDNESS_DASHBOARD_SHARED_COMPONENTS') !== '0') {
        if (dashboardBarrasEmpilhadasHabilitado($i)) {
            $adicionar(dashboardBarrasEmpilhadasSql($base, $graf), array('periodo', 'serie', 'valor', 'quantidade'), '`periodo` ASC, `serie` ASC');
        }
        foreach (isset($i['calendarios']) ? $i['calendarios'] : array() as $calendario) {
            if (!is_array($calendario) || (isset($calendario['habilitado']) && !$calendario['habilitado'])) continue;
            // Calendários fixos continuam independentes quando mudam as datas.
            // Não amplia nem recorta a fonte materializada para atendê-los.
            if (dashboardIndicadorParametrosCalendario($calendario, $parametros) !== $parametros) continue;
            $adicionar(dashboardIndicadorSqlCalendario($base, $calendario, $campoCard), array('data', 'valor'), '`data` ASC');
        }
        $listaTotal = isset($i['listagem']) ? $i['listagem'] : array();
        if (dashboardIndicadorListagemPaginadaHabilitada($i) && empty($listaTotal['_barras'])
            && dashboardPersonalizadoSqlTexto($listaTotal['sqlBase']) === $base) {
            $aliases = array(); $erroTotais = '';
            $sqlTotais = dashboardIndicadorListagemSqlTotais($listaTotal, $base, $aliases, $erroTotais);
            if ($sqlTotais !== false) $adicionar($sqlTotais, array_merge(array('totalLinhas'), array_keys($aliases)));
            $camposPagina = dashboardConsultasCompartilhadasCampos($base);
            if ($camposPagina !== null && !(defined('HARDNESS_DASHBOARD_EXPORTAR_LISTAGEM_COMPLETA') && HARDNESS_DASHBOARD_EXPORTAR_LISTAGEM_COMPLETA)) {
                $ordemPagina = '';
                $pagina = dashboardIndicadorListagemSqlPagina($listaTotal, $base, 0, false, $ordemPagina, $erroTotais);
                preg_match_all('/`([^`]+)`/', $ordemPagina, $camposOrdem);
                if ($pagina !== false && !array_diff(array_map('strtolower', $camposOrdem[1]), array_map('strtolower', $camposPagina))) {
                    $adicionar($pagina, $camposPagina, $ordemPagina);
                }
            }
        }
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
    // Listas simples só participam quando todos os campos retornados são
    // conhecidos. A projeção completa preserva os campos usados pelos detalhes.
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
    } elseif ($ativo($lista, 'habilitada') && !dashboardIndicadorListagemPaginadaHabilitada($i)) {
        $campos = dashboardConsultasCompartilhadasCampos($base);
        if ($campos !== null) {
            $ordem = '';
            if (!empty($lista['ordenacao'])) {
                $campo = dashboardIndicadorIdentificador(isset($lista['ordenacao']['campo']) ? $lista['ordenacao']['campo'] : 'valor', 'valor');
                if (!in_array($campo, $campos, true)) $campos = null;
                $ordem = '`' . $campo . '` ' . (isset($lista['ordenacao']['direcao']) && strtoupper($lista['ordenacao']['direcao']) === 'ASC' ? 'ASC' : 'DESC');
            }
            if ($campos !== null) {
                $limite = isset($lista['limite']) ? max(1, min(500, (int) $lista['limite'])) : 200;
                $adicionar('SELECT * FROM (' . $base . ') AS dashboard_base' . ($ordem === '' ? '' : ' ORDER BY ' . $ordem) . ' LIMIT ' . $limite, $campos, $ordem);
            }
        }
    }
    foreach (array_slice(isset($i['secoes']) ? $i['secoes'] : array(), 0, 6) as $secao) {
        if (!empty($secao['paginacao']['habilitada'])) continue;
        $sql = dashboardPersonalizadoSqlTexto(isset($secao['sqlBase']) ? $secao['sqlBase'] : '');
        if (substr_count('(' . $sql . ')', '(' . $base . ')') !== 1
            || !dashboardIndicadorValidarSql($sql, $erroSecao, false, !empty($secao['permitirUnionAll']))) continue;
        $campos = array();
        foreach (isset($secao['colunas']) ? $secao['colunas'] : array() as $coluna) {
            $campo = dashboardIndicadorIdentificador(isset($coluna['campo']) ? $coluna['campo'] : '', '');
            if ($campo === '' || in_array($campo, $campos, true)) { $campos = array(); break; }
            $campos[] = $campo;
        }
        if (!$campos) continue;
        $indice = $adicionar('SELECT `' . implode('`, `', $campos) . '` FROM (' . $sql . ') AS dashboard_bloco LIMIT 501', $campos);
        $consultas[$indice]['secao'] = isset($secao['id']) ? $secao['id'] : '';
    }
    // O executor também usa estes SQLs para localizar os resultados da fonte
    // compartilhada. Mantém a mesma projeção, ordenação, limites e parâmetros.
    return $consultas;
}

function dashboardConsultasCompartilhadasPlanejar($i, $base, $card, $parametros)
{
    $consultas = dashboardConsultasCompartilhadasConsultas($i, $base, $card, $parametros);
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
    if (!dashboardConsultasCompartilhadasHabilitadas($i) || (dashboardCargaAtiva() && !dashboardCargaGeralAtiva())
        || !empty($i['baseCompartilhada']['habilitada'])) return true;
    if (!isset($i['consultasCompartilhadas']) && preg_match('/\b(RAND|UUID|UUID_SHORT|SYSDATE)\b|@/i', $base)) return true;
    $plano = dashboardConsultasCompartilhadasPlanejar($i, $base, $card, $parametros);
    if ($plano === null) return true;
    if (!function_exists('mysqli_get_server_info') && !function_exists(__NAMESPACE__ . '\\mysqli_get_server_info')) return true;
    $versao = mysqli_get_server_info($conexao);
    if (stripos($versao, 'mariadb') !== false || version_compare($versao, '8.0.0', '<')) return true;
    $linhas = dashboardIndicadorConsultar($conexao, $plano['sql'], $parametros, $erro);
    if ($linhas === false) return false;
    $resultados = dashboardConsultasCompartilhadasSeparar($linhas, $plano, $erro);
    if ($resultados === false) return false;
    foreach ($plano['consultas'] as $n => $q) $GLOBALS['dashboardConsultasCompartilhadas'][dashboardConsultasCompartilhadasChave($q['sql'], $parametros)] = $resultados[$n];
    return true;
}
