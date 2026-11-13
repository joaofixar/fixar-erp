<?php
namespace hardness;

// Somente memória da requisição ROOT. Não escreve logs, arquivos ou tabelas.
function dashboardSqlIniciar($autorizacao, $acao, $entrada)
{
    if (!empty($GLOBALS['dashboardSql']['handlerPhp'])) restore_error_handler();
    unset($GLOBALS['dashboardSql'], $GLOBALS['dashboardSqlErroBanco']);
    if (!dashboardPersonalizadoEhRoot($autorizacao)
        || !in_array($acao, array('indicator', 'commercialClientHistory', 'configuration'), true)) return;
    $GLOBALS['dashboardSql'] = array(
        'inicio' => microtime(true), 'bytes' => 0, 'finalizado' => false,
        'relatorio' => array('versao' => 2, 'motor' => 'analise-sql-20261023-v2', 'phpVersao' => PHP_VERSION,
            'data' => (new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')))->format(DATE_ATOM),
            'empresa' => (int) $autorizacao['empresa'], 'usuario' => (int) $autorizacao['usuario'],
            'acao' => $acao, 'indicador' => dashboardSqlTexto(isset($entrada['id']) ? $entrada['id'] : '', 100),
            'aba' => dashboardSqlTexto(isset($entrada['tab']) ? $entrada['tab'] : '', 100),
            'aberturaId' => isset($entrada['diagnosticoCarga']) && is_string($entrada['diagnosticoCarga']) && preg_match('/^[a-f0-9]{16}$/D', $entrada['diagnosticoCarga']) ? $entrada['diagnosticoCarga'] : '',
            'consultas' => array(), 'quantidadeConsultas' => 0, 'tempoSqlMs' => 0,
            'consultasOmitidas' => 0, 'limiteSqlMs' => 15000, 'lentoAcimaMs' => 1000,
            'observacao' => 'Medição das consultas executadas neste acesso. Não executa EXPLAIN nem consultas adicionais. Linhas são as retornadas, não as examinadas pelo MySQL.'),
    );
    set_error_handler(function ($nivel, $mensagem, $arquivo, $linha) {
        if (empty($GLOBALS['dashboardSql']) || !(error_reporting() & $nivel)
            || dirname($arquivo) !== __DIR__ || basename($arquivo) === 'connection.php') return false;
        $r =& $GLOBALS['dashboardSql']['relatorio'];
        if (!isset($r['avisosPhp'])) $r['avisosPhp'] = array();
        if (count($r['avisosPhp']) < 10) $r['avisosPhp'][] = array('nivel' => $nivel,
            'mensagem' => dashboardSqlTexto($mensagem, 2048), 'arquivo' => basename($arquivo), 'linha' => $linha);
        // A resposta ROOT continua JSON válido, com o aviso preservado no relatório.
        return true;
    }, E_WARNING | E_NOTICE | E_USER_WARNING | E_USER_NOTICE);
    $GLOBALS['dashboardSql']['handlerPhp'] = true;
    register_shutdown_function(function () {
        if (empty($GLOBALS['dashboardSql']) || $GLOBALS['dashboardSql']['finalizado']) return;
        $ultimo = error_get_last();
        if (!$ultimo || !in_array($ultimo['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR), true) || headers_sent()) return;
        $erro = 'Execução interrompida pelo PHP (tempo ou recursos).';
        if (!empty($GLOBALS['dashboardSql']['emExecucao'])) {
            $q = $GLOBALS['dashboardSql']['emExecucao'];
            dashboardSqlRegistrar($q['sql'], $q['parametros'], $q['inicio'], false, $erro, $q['origem']);
        }
        http_response_code(500);
        echo json_encode(dashboardSqlResposta(500, array('error' => $erro)), JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    });
}

function dashboardSqlEtapa($fase)
{
    if (empty($GLOBALS['dashboardSql']['emExecucao'])) return;
    $q =& $GLOBALS['dashboardSql']['emExecucao'];
    $agora = microtime(true);
    if (isset($q['fase'], $q['marco'])) $q['etapasMs'][$q['fase']] = round(($agora - $q['marco']) * 1000, 2);
    $q['fase'] = $fase;
    $q['marco'] = $agora;
}

function dashboardSqlExcecao($e)
{
    if (empty($GLOBALS['dashboardSql'])) return;
    $GLOBALS['dashboardSql']['relatorio']['erroPhp'] = array('tipo' => get_class($e),
        'mensagem' => dashboardSqlTexto($e->getMessage(), 2048),
        'arquivo' => basename($e->getFile()), 'linha' => $e->getLine());
}

function dashboardSqlTexto($valor, $limite = 8192)
{
    if (!is_scalar($valor) && $valor !== null) return '[valor inválido]';
    $valor = (string) $valor;
    if (strlen($valor) <= $limite) return $valor;
    $texto = function_exists('mb_strcut') ? mb_strcut($valor, 0, $limite, 'UTF-8') : substr($valor, 0, $limite);
    return $texto . ' [TRUNCADO]';
}

function dashboardSqlSanitizar($valor, $nivel = 0)
{
    if ($nivel > 12) return '[TRUNCADO]';
    if (!is_array($valor)) return is_string($valor) ? dashboardSqlTexto($valor) : (is_scalar($valor) || $valor === null ? $valor : '[omitido]');
    $saida = array(); $n = 0;
    foreach ($valor as $chave => $item) {
        if (++$n > 100) { $saida['_truncado'] = true; break; }
        $saida[$chave] = preg_match('/senha|password|dbPass|dbHost|dbUser|dbDatabase|secret|token|cookie|authorization|credencial/i', (string) $chave)
            ? '[omitido]' : dashboardSqlSanitizar($item, $nivel + 1);
    }
    return $saida;
}

function dashboardSqlParametros($parametros)
{
    $saida = array();
    foreach (array_slice($parametros, 0, 40, true) as $chave => $valor) {
        $saida[$chave] = is_string($valor) ? dashboardSqlTexto($valor, 500) : (is_scalar($valor) || $valor === null ? $valor : '[omitido]');
    }
    if (count($parametros) > 40) $saida['_truncado'] = true;
    return dashboardSqlSanitizar($saida);
}

function dashboardSqlContexto($indicador, $entrada)
{
    if (empty($GLOBALS['dashboardSql'])) return;
    $r =& $GLOBALS['dashboardSql']['relatorio'];
    $r['titulo'] = dashboardSqlTexto(isset($indicador['titulo']) ? $indicador['titulo'] : '', 200);
    $r['configuracaoSha256'] = hash('sha256', serialize($indicador));
    $r['configuracaoJson'] = dashboardSqlTexto(json_encode(dashboardSqlSanitizar($indicador), JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE), 32768);
    $filtros = array();
    foreach ($entrada as $chave => $valor) {
        if (strpos($chave, 'f_') === 0 || in_array($chave, array('ufMapa', 'detalheGrupo', 'detalheBloco', 'somenteDetalhamento', 'cotacoesDados', 'carregarProgressivo'), true)) $filtros[$chave] = $valor;
    }
    $r['entrada'] = dashboardSqlParametros($filtros);
    $r['continuacaoProgressiva'] = !empty($entrada['cargaToken']);
}

function dashboardSqlRegistrar($sql, $parametros, $inicio, $linhas, $erro, $origem)
{
    $ctx =& $GLOBALS['dashboardSql'];
    $r =& $ctx['relatorio'];
    $ms = round((microtime(true) - $inicio) * 1000, 2);
    $r['quantidadeConsultas']++;
    $r['tempoSqlMs'] += $ms;
    preg_match_all('/:([a-z][a-zA-Z0-9_]*)/', $sql, $nomes);
    $usados = array_intersect_key($parametros, array_fill_keys($nomes[1], true));
    $consulta = array('ordem' => $r['quantidadeConsultas'], 'tempoMs' => $ms,
        'lenta' => $ms >= 1000, 'linhasRetornadas' => is_array($linhas) ? count($linhas) : null,
        'origem' => $origem, 'sqlSha256' => hash('sha256', $sql), 'sql' => dashboardSqlTexto($sql, 12288),
        'parametros' => dashboardSqlParametros($usados), 'erro' => dashboardSqlTexto($erro, 2048),
        'mysql' => isset($GLOBALS['dashboardSqlErroBanco']) ? $GLOBALS['dashboardSqlErroBanco'] : null);
    if (!empty($ctx['emExecucao'])) {
        $fase = isset($ctx['emExecucao']['fase']) ? $ctx['emExecucao']['fase'] : '';
        dashboardSqlEtapa('concluida');
        $consulta['etapasMs'] = isset($ctx['emExecucao']['etapasMs']) ? $ctx['emExecucao']['etapasMs'] : array();
        if ($erro !== '') $consulta['faseDaFalha'] = $fase;
    }
    $bytes = strlen(json_encode($consulta, JSON_INVALID_UTF8_SUBSTITUTE));
    // Reserva espaço para o último erro mesmo quando o limite já foi atingido.
    if ($erro !== '') $r['ultimaConsultaComErro'] = $consulta;
    if (!isset($r['consultaMaisLenta']) || $ms > $r['consultaMaisLenta']['tempoMs']) $r['consultaMaisLenta'] = $consulta;
    if (count($r['consultas']) >= 40 || $ctx['bytes'] + $bytes > 98304) { $r['consultasOmitidas']++; return; }
    $ctx['bytes'] += $bytes;
    $r['consultas'][] = $consulta;
}

function dashboardSqlErroBanco($numero, $mensagem, $estado = '')
{
    if (empty($GLOBALS['dashboardSql'])) return;
    $GLOBALS['dashboardSqlErroBanco'] = array('codigo' => (int) $numero,
        'mensagem' => dashboardSqlTexto($mensagem, 2048), 'sqlState' => dashboardSqlTexto($estado, 10));
}

function dashboardSqlResposta($codigo, $dados)
{
    if (empty($GLOBALS['dashboardSql'])) return $dados;
    $ctx =& $GLOBALS['dashboardSql'];
    $ctx['finalizado'] = true;
    if (!empty($ctx['handlerPhp'])) { restore_error_handler(); $ctx['handlerPhp'] = false; }
    if ($ctx['relatorio']['acao'] === 'configuration' && $codigo < 400) return $dados;
    $r = $ctx['relatorio'];
    $r['httpStatus'] = $codigo;
    $r['erro'] = isset($dados['error']) ? dashboardSqlTexto($dados['error'], 2048) : '';
    $r['tempoServidorMs'] = round((microtime(true) - $ctx['inicio']) * 1000, 2);
    $r['tempoSqlMs'] = round($r['tempoSqlMs'], 2);
    $r['tempoForaSqlMs'] = max(0, round($r['tempoServidorMs'] - $r['tempoSqlMs'], 2));
    $r['picoMemoriaMb'] = round(memory_get_peak_usage(true) / 1048576, 2);
    $r['cacheFiltrosUsado'] = !empty($GLOBALS['dashboardFiltrosCache']['usado']);
    $r['avisos'] = array();
    foreach (isset($r['avisosPhp']) ? $r['avisosPhp'] : array() as $aviso) $r['avisos'][] = array('componente' => 'PHP ' . $aviso['arquivo'] . ':' . $aviso['linha'], 'mensagem' => $aviso['mensagem']);
    foreach (array('desempenhoComercial.resumo.metaAviso', 'painelCotacoes.metasErro') as $caminho) {
        $valor = $dados;
        foreach (explode('.', $caminho) as $parte) $valor = is_array($valor) && isset($valor[$parte]) ? $valor[$parte] : null;
        if (is_string($valor) && $valor !== '') $r['avisos'][] = array('componente' => $caminho, 'mensagem' => dashboardSqlTexto($valor, 2048));
    }
    // Somente quantidades; não copia linhas com dados do cliente para o log.
    $r['volumesRetornados'] = array();
    foreach (array('listagem', 'grafico', 'calendarios', 'secoes', 'geografia') as $chave) {
        if (isset($dados[$chave]) && is_array($dados[$chave])) $r['volumesRetornados'][$chave] = count($dados[$chave]);
    }
    if (isset($dados['card']['quantidade'])) $r['volumesRetornados']['cardQuantidade'] = $dados['card']['quantidade'];
    if (isset($dados['desempenhoComercial']['linhas'])) $r['volumesRetornados']['clientesComerciais'] = count($dados['desempenhoComercial']['linhas']);
    if (isset($dados['painelCotacoes']['fontes']) && is_array($dados['painelCotacoes']['fontes'])) {
        foreach ($dados['painelCotacoes']['fontes'] as $fonte => $linhas) if (is_array($linhas)) $r['volumesRetornados']['cotacoes_' . $fonte] = count($linhas);
    }
    if (isset($dados['carregamento'])) $r['carregamento'] = dashboardSqlSanitizar($dados['carregamento']);
    $dados['analiseSql'] = $r;
    return $dados;
}
