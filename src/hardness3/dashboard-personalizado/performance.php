<?php
namespace hardness;

// Contexto definido pelos endpoints somente após autorização e restrições.
function dashboardFiltrosCacheContexto($tenant, $autorizacao, $acessos, $indicador)
{
    $GLOBALS['dashboardFiltrosCache'] = array(
        'escopo' => hash('sha256', serialize(array($tenant, $autorizacao, $acessos, $indicador))),
        'ignorar' => false,
        'usado' => false,
    );
}

function dashboardFiltrosCacheTtl()
{
    $valor = getenv('HARDNESS_DASHBOARD_FILTER_CACHE_TTL');
    return $valor === false ? 30 : max(0, min(60, (int) $valor));
}

function dashboardFiltrosCacheElegivel($sql)
{
    // Somente consultas simples de cadastros. Movimentos e permissões não entram.
    if (preg_match_all('/\bSELECT\b/i', $sql) !== 1
        || preg_match('/\b(NOW|CURDATE|CURRENT_DATE|CURRENT_TIMESTAMP|RAND|UUID|SYSDATE)\b/i', $sql)) return false;
    preg_match_all('/\b(?:FROM|JOIN)\s+`?([a-zA-Z][a-zA-Z0-9_]*)`?/i', $sql, $tabelas);
    if (empty($tabelas[1])) return false;
    foreach ($tabelas[1] as $tabela) {
        if (!preg_match('/^D[0-9]{3}[A-Z]?$/i', $tabela) && strcasecmp($tabela, 'SMT029') !== 0) return false;
    }
    return true;
}

function dashboardFiltrosCacheDiretorio()
{
    $dir = sys_get_temp_dir() . '/hardness-dashboard-filtros-' . substr(hash('sha256', __DIR__), 0, 16);
    if (is_link($dir)) return false;
    if (!is_dir($dir) && !@mkdir($dir, 0700, true) && !is_dir($dir)) return false;
    if (!@chmod($dir, 0700) || !is_readable($dir) || !is_writable($dir)) return false;
    return $dir;
}

function dashboardFiltrosConsultar($conexao, $sql, $parametros, &$erro)
{
    $ctx = isset($GLOBALS['dashboardFiltrosCache']) ? $GLOBALS['dashboardFiltrosCache'] : array();
    $ttl = dashboardFiltrosCacheTtl();
    $dir = false;
    if ($ttl > 0 && !empty($ctx['escopo'])
        && !dashboardCargaAtiva() && dashboardFiltrosCacheElegivel($sql)) $dir = dashboardFiltrosCacheDiretorio();
    $arquivo = false;
    if ($dir !== false) {
        $arquivo = $dir . '/' . hash('sha256', serialize(array($ctx['escopo'], $sql, $parametros))) . '.json';
        if (empty($ctx['ignorar']) && !is_link($arquivo) && is_file($arquivo) && @filesize($arquivo) <= 262144) {
            $cache = json_decode(@file_get_contents($arquivo), true);
            if (is_array($cache) && isset($cache['expira'], $cache['criado'], $cache['linhas'])
                && $cache['expira'] > time() && $cache['criado'] > time() - $ttl
                && is_array($cache['linhas']) && count($cache['linhas']) <= 2000) {
                $GLOBALS['dashboardFiltrosCache']['usado'] = true;
                $erro = '';
                return $cache['linhas'];
            }
            @unlink($arquivo);
        }
    }
    $linhas = dashboardIndicadorConsultar($conexao, $sql, $parametros, $erro);
    if ($linhas === false || $arquivo === false || count($linhas) > 2000) return $linhas;
    foreach ($linhas as $linha) {
        if (!is_array($linha) || !array_key_exists('valor', $linha) || !array_key_exists('titulo', $linha)) return $linhas;
    }
    $json = json_encode(array('criado' => time(), 'expira' => time() + $ttl, 'linhas' => $linhas), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false || strlen($json) > 262144) return $linhas;
    $temp = @tempnam($dir, 'tmp-');
    if ($temp !== false) {
        if (@chmod($temp, 0600) && @file_put_contents($temp, $json, LOCK_EX) !== false && !is_link($arquivo)) @rename($temp, $arquivo);
        if (is_file($temp)) @unlink($temp);
    }
    // Limpeza oportunista limitada; nunca percorre todas as cargas por requisição.
    if (mt_rand(1, 100) === 1) {
        try { $it = new \DirectoryIterator($dir); }
        catch (\UnexpectedValueException $e) { return $linhas; }
        $vistos = 0;
        foreach ($it as $file) {
            if (++$vistos > 100) break;
            if ($file->isFile() && !$file->isLink() && $file->getMTime() < time() - 120) @unlink($file->getPathname());
        }
    }
    return $linhas;
}

function dashboardIndicadorMesDetalhamento($indicador, $entrada, &$erro)
{
    if (!array_key_exists('detalheMes', $entrada)) return 0;
    $meses = isset($indicador['listagem']['detalhamento']['mesesPorColuna']) ? $indicador['listagem']['detalhamento']['mesesPorColuna'] : array();
    $valor = $entrada['detalheMes'];
    if (!is_scalar($valor) || !preg_match('/^(?:[1-9]|1[0-2])$/', (string) $valor)
        || !is_array($meses) || !in_array((int) $valor, array_map('intval', $meses), true)
        || !isset($entrada['detalheGrupo']) || !is_scalar($entrada['detalheGrupo']) || trim((string) $entrada['detalheGrupo']) === '') {
        $erro = 'O mês selecionado para o detalhamento é inválido.';
        return false;
    }
    return (int) $valor;
}

function dashboardIndicadorConsultarDetalhamento($conexao, $detalhamento, $campoAgrupamento, $limite, $parametros, &$erro)
{
    $sqlBase = dashboardPersonalizadoSqlTexto(isset($detalhamento['sqlBase']) ? $detalhamento['sqlBase'] : '');
    if (!dashboardIndicadorValidarSql($sqlBase, $erro)) return false;
    preg_match_all('/:([a-z][a-zA-Z0-9_]*)/', $sqlBase, $encontrados);
    foreach (array_unique($encontrados[1]) as $parametro) {
        if (!array_key_exists($parametro, $parametros)) {
            $erro = 'O detalhamento usa o parâmetro :' . $parametro . ', mas ele não está configurado.';
            return false;
        }
    }
    $campo = dashboardIndicadorIdentificador(isset($detalhamento['campoGrupo']) ? $detalhamento['campoGrupo'] : $campoAgrupamento, $campoAgrupamento);
    $limite = isset($detalhamento['limite']) ? max(1, min(5000, (int) $detalhamento['limite'])) : $limite;
    $sql = 'SELECT * FROM (' . $sqlBase . ') AS dashboard_detalhamento';
    if (!empty($detalhamento['modal'])) $sql .= " WHERE COALESCE(NULLIF(`{$campo}`, ''), 'Não informado') = :detalheGrupo";
    $ranking = isset($detalhamento['ranking']) && is_array($detalhamento['ranking']) ? $detalhamento['ranking'] : array();
    $campoRanking = dashboardIndicadorIdentificador(isset($ranking['campoValor']) ? $ranking['campoValor'] : '', '');
    if (!empty($ranking) && $campoRanking === '') { $erro = 'Coluna inválida no ranking.'; return false; }
    if ($campoRanking !== '' || !empty($detalhamento['ordenacao']['campo'])) {
        $campoOrdem = $campoRanking !== '' ? $campoRanking : dashboardIndicadorIdentificador($detalhamento['ordenacao']['campo'], '');
        if ($campoOrdem === '') { $erro = 'Ordenação inválida no detalhamento.'; return false; }
        $direcao = $campoRanking !== '' || (isset($detalhamento['ordenacao']['direcao']) && strtoupper($detalhamento['ordenacao']['direcao']) === 'DESC') ? 'DESC' : 'ASC';
        $sql .= ' ORDER BY `' . $campoOrdem . '` ' . $direcao;
    }
    $linhas = dashboardIndicadorConsultar($conexao, $sql . ' LIMIT ' . $limite, $parametros, $erro);
    if ($linhas === false) return false;
    $grupos = array();
    $posicoes = array();
    foreach ($linhas as $linha) {
        $grupo = isset($linha[$campo]) ? trim((string) $linha[$campo]) : '';
        $grupo = $grupo === '' ? 'Não informado' : $grupo;
        if (!isset($grupos[$grupo])) $grupos[$grupo] = array();
        if ($campoRanking !== '') {
            $valor = isset($linha[$campoRanking]) ? (float) $linha[$campoRanking] : 0;
            if (!isset($posicoes[$grupo])) $posicoes[$grupo] = array('valor' => null, 'posicao' => 0);
            if ($posicoes[$grupo]['valor'] === null || $valor !== $posicoes[$grupo]['valor']) $posicoes[$grupo]['posicao']++;
            $posicoes[$grupo]['valor'] = $valor;
            $campoPosicao = dashboardIndicadorIdentificador(isset($ranking['campoPosicao']) ? $ranking['campoPosicao'] : 'posicao', 'posicao');
            $linha[$campoPosicao] = $posicoes[$grupo]['posicao'];
        }
        $grupos[$grupo][] = $linha;
    }
    return $grupos;
}

function dashboardIndicadorConsultarItensDetalhamento($conexao, $indicador, $entrada, $parametros, &$erro)
{
    $detalhe = isset($indicador['listagem']['detalhamento']) ? $indicador['listagem']['detalhamento'] : array();
    $itens = isset($detalhe['itens']) ? $detalhe['itens'] : array();
    $nota = isset($entrada['detalheNota']) && is_string($entrada['detalheNota']) ? $entrada['detalheNota'] : '';
    if ((isset($indicador['listagem']['habilitada']) && !$indicador['listagem']['habilitada'])
        || empty($detalhe['modal']) || (isset($detalhe['habilitado']) && !$detalhe['habilitado'])
        || empty($itens) || (isset($itens['habilitado']) && !$itens['habilitado'])
        || empty($parametros['detalheGrupo']) || !preg_match('/^[1-9][0-9]{0,17}$/', $nota)) {
        $erro = 'Informe uma nota e um grupo válidos para consultar os itens.';
        return false;
    }
    $sqlBase = dashboardPersonalizadoSqlTexto(isset($itens['sqlBase']) ? $itens['sqlBase'] : '');
    if (!dashboardIndicadorValidarSql($sqlBase, $erro)) return false;
    $parametros['detalheNota'] = $nota;
    preg_match_all('/:([a-z][a-zA-Z0-9_]*)/', $sqlBase, $encontrados);
    foreach (array_unique($encontrados[1]) as $parametro) {
        if (!array_key_exists($parametro, $parametros)) {
            $erro = 'Os itens usam um parâmetro não configurado: :' . $parametro . '.';
            return false;
        }
    }
    $campoGrupo = dashboardIndicadorIdentificador(isset($detalhe['campoGrupo']) ? $detalhe['campoGrupo'] : 'descricao', 'descricao');
    $campoNota = dashboardIndicadorIdentificador(isset($itens['campoNota']) ? $itens['campoNota'] : 'notaId', 'notaId');
    $limite = isset($itens['limite']) ? max(1, min(5000, (int) $itens['limite'])) : 500;
    // Mesmo nas chamadas manuais, nota e grupo ficam restritos à base filtrada.
    $sql = 'SELECT * FROM (' . $sqlBase . ') AS dashboard_itens_nota'
        . " WHERE COALESCE(NULLIF(`{$campoGrupo}`, ''), 'Não informado') = :detalheGrupo"
        . " AND `{$campoNota}` = :detalheNota LIMIT " . ($limite + 1);
    $linhas = dashboardIndicadorConsultar($conexao, $sql, $parametros, $erro);
    if ($linhas === false) return false;
    return array('notaId' => $nota, 'linhas' => array_slice($linhas, 0, $limite), 'temMais' => count($linhas) > $limite);
}

function dashboardIndicadorPodeDetalharDireto($indicador, $entrada)
{
    if (getenv('HARDNESS_DASHBOARD_DETAIL_FAST_PATH') === '0'
        || !isset($entrada['somenteDetalhamento']) || $entrada['somenteDetalhamento'] !== '1'
        || !isset($entrada['detalheGrupo']) || !is_string($entrada['detalheGrupo']) || trim($entrada['detalheGrupo']) === ''
        || !empty($indicador['multiempresa']) || dashboardCargaAtiva()
        || (dashboardCargaHabilitada($indicador) && !dashboardCargaGeralHabilitada($indicador))
        || dashboardClientesInativosHabilitado($indicador)) return false;
    foreach (array('ufMapa', 'detalheCalendarioId', 'detalheGraficoSerie', 'paretoInicio', 'paretoFim', 'paretoClasse') as $chave) {
        if (!empty($entrada[$chave])) return false;
    }
    $lista = isset($indicador['listagem']) ? $indicador['listagem'] : array();
    $detalhe = isset($lista['detalhamento']) ? $lista['detalhamento'] : array();
    $campoPadrao = isset($lista['agrupamento']['campo']) ? $lista['agrupamento']['campo'] : 'descricao';
    return (!isset($lista['habilitada']) || $lista['habilitada']) && !empty($detalhe['modal'])
        && (!isset($detalhe['habilitado']) || $detalhe['habilitado'])
        && dashboardIndicadorIdentificador(isset($detalhe['campoGrupo']) ? $detalhe['campoGrupo'] : $campoPadrao) !== ''
        && dashboardPersonalizadoSqlTexto(isset($detalhe['sqlBase']) ? $detalhe['sqlBase'] : '') !== '';
}

function dashboardIndicadorDetalheVisualDireto($indicador, $entrada)
{
    if (getenv('HARDNESS_DASHBOARD_DETAIL_FAST_PATH') === '0'
        || !isset($entrada['somenteDetalhamento']) || $entrada['somenteDetalhamento'] !== '1'
        || !empty($indicador['multiempresa']) || dashboardCargaAtiva()
        || (dashboardCargaHabilitada($indicador) && !dashboardCargaGeralHabilitada($indicador))
        || dashboardClientesInativosHabilitado($indicador)) return '';
    foreach (array('ufMapa', 'detalheGrupo', 'detalheBloco', 'paretoInicio', 'paretoFim', 'paretoClasse') as $chave) {
        if (!empty($entrada[$chave])) return '';
    }
    if (!empty($entrada['detalheCalendarioId']) && empty($entrada['detalheGraficoSerie'])) {
        foreach (isset($indicador['calendarios']) ? $indicador['calendarios'] : array() as $calendario) {
            if (isset($calendario['id']) && $calendario['id'] === $entrada['detalheCalendarioId']
                && (!isset($calendario['habilitado']) || $calendario['habilitado'])
                && !empty($calendario['detalhamento'])
                && (!isset($calendario['detalhamento']['habilitado']) || $calendario['detalhamento']['habilitado'])) return 'calendario';
        }
    }
    $grafico = isset($indicador['grafico']) ? $indicador['grafico'] : array();
    if (empty($entrada['detalheCalendarioId']) && !empty($entrada['detalheGraficoSerie'])
        && isset($grafico['tipo']) && $grafico['tipo'] === 'barras_comparativas'
        && (!isset($grafico['habilitado']) || $grafico['habilitado'])
        && !empty($grafico['detalhamento'])
        && (!isset($grafico['detalhamento']['habilitado']) || $grafico['detalhamento']['habilitado'])) return 'grafico';
    return '';
}

function dashboardIndicadorResultadoDetalhe($indicador, $filtros)
{
    return array(
        'indicador' => dashboardIndicadorDefinicaoPublica($indicador, $filtros),
        'card' => array('valor' => 0, 'quantidade' => 0, 'formato' => dashboardIndicadorFormatoPermitido(isset($indicador['card']['formato']) ? $indicador['card']['formato'] : 'numero')),
        'listagemDetalhes' => array(), 'listagem' => array(), 'resumos' => array(), 'secoes' => array(),
        'geografia' => array(), 'composicao' => array(), 'grafico' => array(), 'graficoDetalhes' => array(),
        'graficosPorGrupo' => array(), 'calendarios' => array(), 'calendarioDetalhes' => array(),
        'paretoClasse' => null, 'comparativoRepresentantes' => array(),
    );
}

function dashboardIndicadorParametrosCalendario($calendario, $parametros)
{
    if (isset($calendario['periodo']) && $calendario['periodo'] === 'ultimos_30_dias') {
        $parametros['dataInicio'] = date('Y-m-d', strtotime('-29 days'));
        $parametros['dataFim'] = date('Y-m-d');
    } elseif (isset($calendario['periodo']) && $calendario['periodo'] === 'proximos_30_dias') {
        $parametros['dataInicio'] = date('Y-m-d');
        $parametros['dataFim'] = date('Y-m-d', strtotime('+29 days'));
    }
    return $parametros;
}

function dashboardIndicadorConsultarDetalheCalendario($conexao, $sqlBase, $calendario, $campoCard, $parametros, $data, &$erro)
{
    $campoData = dashboardIndicadorIdentificador(isset($calendario['campoData']) ? $calendario['campoData'] : 'data', 'data');
    $campoValor = dashboardIndicadorIdentificador(isset($calendario['campoValor']) ? $calendario['campoValor'] : $campoCard, $campoCard);
    $detalhe = $calendario['detalhamento'];
    $limite = isset($detalhe['limite']) ? max(1, min(5000, (int) $detalhe['limite'])) : 1000;
    $parametros['detalheCalendarioData'] = $data;
    $sql = "SELECT * FROM ({$sqlBase}) AS dashboard_calendario_detalhamento WHERE `{$campoData}` IS NOT NULL AND DATE(`{$campoData}`) = :detalheCalendarioData AND COALESCE(`{$campoValor}`, 0) > 0 ORDER BY `{$campoData}` LIMIT {$limite}";
    return dashboardIndicadorConsultar($conexao, $sql, $parametros, $erro);
}
