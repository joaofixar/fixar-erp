<?php
namespace hardness;


if (!defined('APONTAMENTO_DEBUG_IMPRESSORA')) {
    define('APONTAMENTO_DEBUG_IMPRESSORA', false);
}
if (!defined('APONTAMENTO_DEBUG_SQL_TEMPO')) {
    define('APONTAMENTO_DEBUG_SQL_TEMPO', false);
}
header("X-Robots-Tag: noindex, nofollow", true);

if (function_exists('session_status')) {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
} else if (session_id() === '') {
    session_start();
}



if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ./index.php');
    exit;
}

if (empty($_SESSION['Usuario_Atual'])) {
    header('Location: ./index.php');
    exit;
}

if (isset($_GET['debug_sql_tempo'])) {
    if ($_GET['debug_sql_tempo'] === '1') {
        $_SESSION['apontamento_debug_sql_tempo'] = '1';
        $_SESSION['apontamento_flash'] = array(
            'tipo' => 'success',
            'mensagem' => 'Debug de tempo SQL ativado. As operacoes de T255/T256/T257/T257A serao gravadas em tmp/apontamento_sql_tempo.log.'
        );
    } else if ($_GET['debug_sql_tempo'] === '0') {
        unset($_SESSION['apontamento_debug_sql_tempo']);
        $_SESSION['apontamento_flash'] = array(
            'tipo' => 'success',
            'mensagem' => 'Debug de tempo SQL desativado.'
        );
    }
}

$g = array(
    'usuarioAtual' => $_SESSION['Usuario_Atual'],
    'empresaAtual' => isset($_SESSION['Empresa_Atual']) ? $_SESSION['Empresa_Atual'] : null,
);

require_once('../../bibliotecas/confUsuario.php');
include_once('../../configuracoes/sysConfig.php');
require_once('../../bibliotecas/conexaoBanco.php');
require_once('../../bibliotecas/funcoesGerais.php');


function apontamentoEscapar($valor)
{
    global $g;
    if (!empty($g['conexaoBanco']) && function_exists('\\mysqli_real_escape_string')) {
        return \mysqli_real_escape_string($g['conexaoBanco'], trim((string) $valor));
    }

    return mysql_real_escape_string(trim((string) $valor));
}

function apontamentoNumero($valor)
{
    $valor = trim((string) $valor);
    if ($valor === '') {
        return 0.0;
    }
    $valor = str_replace('.', '', $valor);
    $valor = str_replace(',', '.', $valor);
    return (float) $valor;
}

function apontamentoFormatar($valor, $casas = 3)
{
    return number_format((float) $valor, $casas, ',', '.');
}

function apontamentoFlash($tipo = null, $mensagem = null)
{
    if ($tipo !== null && $mensagem !== null) {
        $_SESSION['apontamento_flash'] = array('tipo' => $tipo, 'mensagem' => $mensagem);
        return null;
    }
    if (empty($_SESSION['apontamento_flash'])) {
        return null;
    }
    $flash = $_SESSION['apontamento_flash'];
    unset($_SESSION['apontamento_flash']);
    return $flash;
}

function apontamentoDebugSqlTempoAtivo()
{
    if (APONTAMENTO_DEBUG_SQL_TEMPO) {
        return true;
    }

    return !empty($_SESSION['apontamento_debug_sql_tempo']);
}

function apontamentoDebugSqlTempoArquivo()
{
    static $arquivo = null;
    if ($arquivo !== null) {
        return $arquivo;
    }

    $arquivo = dirname(__FILE__) . '/../../tmp/apontamento_sql_tempo.log';
    return $arquivo;
}

function apontamentoDebugSqlTempoRequestId()
{
    static $requestId = null;
    if ($requestId !== null) {
        return $requestId;
    }

    try {
        $requestId = date('Ymd_His') . '_' . bin2hex(random_bytes(4));
    } catch (\Exception $e) {
        $requestId = date('Ymd_His') . '_' . uniqid();
    }

    return $requestId;
}

function apontamentoDebugSqlTempoResumoInicializar()
{
    static $inicializado = false;

    if ($inicializado || !apontamentoDebugSqlTempoAtivo()) {
        return;
    }

    $GLOBALS['apontamentoDebugSqlTempoResumo'] = array(
        'request_start' => microtime(true),
        'query_count' => 0,
        'query_time_ms' => 0.0,
        'operacoes' => array(),
    );

    register_shutdown_function('hardness\\apontamentoDebugSqlTempoFinalizar');
    $inicializado = true;
}

function apontamentoDebugSqlTempoExtrairOperacao($sql)
{
    $sqlNormalizado = preg_replace('/\s+/', ' ', trim((string) $sql));
    if ($sqlNormalizado === '') {
        return null;
    }

    if (preg_match('/^(INSERT\s+INTO|UPDATE)\s+(T257A|T257|T256|T255)\b/i', $sqlNormalizado, $matches)) {
        return array(
            'tipo' => strtoupper(str_replace('  ', ' ', $matches[1])),
            'tabela' => strtoupper($matches[2]),
            'sql' => $sqlNormalizado,
        );
    }

    if (preg_match('/^(COMMIT|ROLLBACK|START TRANSACTION|SET AUTOCOMMIT)/i', $sqlNormalizado, $matches)) {
        return array(
            'tipo' => strtoupper($matches[1]),
            'tabela' => '',
            'sql' => $sqlNormalizado,
        );
    }

    return null;
}

function apontamentoDebugSqlTempoRegistrar($evento, array $dados = array())
{
    if (!apontamentoDebugSqlTempoAtivo()) {
        return;
    }

    apontamentoDebugSqlTempoResumoInicializar();

    $payload = array_merge(
        array(
            'timestamp' => date('Y-m-d H:i:s'),
            'request_id' => apontamentoDebugSqlTempoRequestId(),
            'evento' => $evento,
            'uri' => isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '',
            'metodo' => isset($_SERVER['REQUEST_METHOD']) ? (string) $_SERVER['REQUEST_METHOD'] : '',
            'usuario' => isset($_SESSION['Usuario_Atual']) ? (int) $_SESSION['Usuario_Atual'] : 0,
            'empresa' => isset($_SESSION['Empresa_Atual']) ? (int) $_SESSION['Empresa_Atual'] : 0,
            'op_id' => isset($_REQUEST['op_id']) ? (int) $_REQUEST['op_id'] : 0,
            'item_id' => isset($_REQUEST['item_id']) ? (int) $_REQUEST['item_id'] : 0,
            't257_id' => isset($_REQUEST['t257_id']) ? (int) $_REQUEST['t257_id'] : 0,
            'acao' => isset($_REQUEST['acao']) ? (string) $_REQUEST['acao'] : '',
        ),
        $dados
    );

    error_log(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL, 3, apontamentoDebugSqlTempoArquivo());
}

function apontamentoDebugSqlTempoRegistrarQuery($sql, $duracaoMs, $ok, $erro = '', $linhasAfetadas = null)
{
    $operacao = apontamentoDebugSqlTempoExtrairOperacao($sql);
    if ($operacao === null) {
        return;
    }

    if (!empty($GLOBALS['apontamentoDebugSqlTempoResumo'])) {
        $GLOBALS['apontamentoDebugSqlTempoResumo']['query_count']++;
        $GLOBALS['apontamentoDebugSqlTempoResumo']['query_time_ms'] += (float) $duracaoMs;

        $chaveOperacao = trim($operacao['tipo'] . ' ' . $operacao['tabela']);
        if (!isset($GLOBALS['apontamentoDebugSqlTempoResumo']['operacoes'][$chaveOperacao])) {
            $GLOBALS['apontamentoDebugSqlTempoResumo']['operacoes'][$chaveOperacao] = array(
                'quantidade' => 0,
                'tempo_ms' => 0.0,
            );
        }

        $GLOBALS['apontamentoDebugSqlTempoResumo']['operacoes'][$chaveOperacao]['quantidade']++;
        $GLOBALS['apontamentoDebugSqlTempoResumo']['operacoes'][$chaveOperacao]['tempo_ms'] += (float) $duracaoMs;
    }

    apontamentoDebugSqlTempoRegistrar('sql', array(
        'operacao' => $operacao['tipo'],
        'tabela' => $operacao['tabela'],
        'duracao_ms' => round((float) $duracaoMs, 3),
        'ok' => $ok ? 1 : 0,
        'linhas_afetadas' => $linhasAfetadas,
        'erro' => $erro,
        'sql' => $operacao['sql'],
    ));
}

function apontamentoDebugSqlTempoMedirBloco($nome, callable $callback, array $contexto = array())
{
    $inicio = microtime(true);

    try {
        $resultado = $callback();
        apontamentoDebugSqlTempoRegistrar('bloco', array_merge($contexto, array(
            'bloco' => $nome,
            'duracao_ms' => round((microtime(true) - $inicio) * 1000, 3),
            'ok' => 1,
        )));
        return $resultado;
    } catch (\Exception $e) {
        apontamentoDebugSqlTempoRegistrar('bloco', array_merge($contexto, array(
            'bloco' => $nome,
            'duracao_ms' => round((microtime(true) - $inicio) * 1000, 3),
            'ok' => 0,
            'erro' => $e->getMessage(),
        )));
        throw $e;
    }
}

function apontamentoDebugSqlTempoFinalizar()
{
    if (!apontamentoDebugSqlTempoAtivo() || empty($GLOBALS['apontamentoDebugSqlTempoResumo'])) {
        return;
    }

    $resumo = $GLOBALS['apontamentoDebugSqlTempoResumo'];
    apontamentoDebugSqlTempoRegistrar('resumo_request', array(
        'duracao_request_ms' => round((microtime(true) - $resumo['request_start']) * 1000, 3),
        'query_count' => (int) $resumo['query_count'],
        'query_time_ms' => round((float) $resumo['query_time_ms'], 3),
        'operacoes' => $resumo['operacoes'],
    ));
}
function apontamentoDebugSqlTempoResumoAtual()
{
    if (empty($GLOBALS['apontamentoDebugSqlTempoResumo'])) {
        return array(
            'query_count' => 0,
            'query_time_ms' => 0.0,
            'operacoes' => array(),
        );
    }

    $resumo = $GLOBALS['apontamentoDebugSqlTempoResumo'];
    return array(
        'query_count' => isset($resumo['query_count']) ? (int) $resumo['query_count'] : 0,
        'query_time_ms' => isset($resumo['query_time_ms']) ? (float) $resumo['query_time_ms'] : 0.0,
        'operacoes' => isset($resumo['operacoes']) && is_array($resumo['operacoes']) ? $resumo['operacoes'] : array(),
    );
}

function apontamentoDebugSqlTempoTabelasAlvo()
{
    return array('T255', 'T256', 'T257', 'T257A');
}

function apontamentoDebugSqlTempoEventoEhAlvo(array $evento)
{
    $tabelasAlvo = apontamentoDebugSqlTempoTabelasAlvo();

    if (isset($evento['evento']) && $evento['evento'] === 'sql') {
        $tabela = isset($evento['tabela']) ? strtoupper((string) $evento['tabela']) : '';
        return in_array($tabela, $tabelasAlvo, true);
    }

    if (isset($evento['evento']) && $evento['evento'] === 'bloco') {
        $bloco = isset($evento['bloco']) ? (string) $evento['bloco'] : '';
        return in_array($bloco, array(
            'apontamentoExecutarOutroInclusao',
            'apontamentoExecutarOutroAtualizacao',
            'apontamentoAtualizaTotaisItemEOp'
        ), true);
    }

    if (isset($evento['evento']) && $evento['evento'] === 'resumo_request') {
        if (empty($evento['operacoes']) || !is_array($evento['operacoes'])) {
            return false;
        }

        foreach (array_keys($evento['operacoes']) as $operacaoNome) {
            foreach ($tabelasAlvo as $tabelaAlvo) {
                if (stripos((string) $operacaoNome, $tabelaAlvo) !== false) {
                    return true;
                }
            }
        }
    }

    return false;
}
function apontamentoDebugSqlTempoLerEventosRecentes($limite = 60)
{
    $arquivo = apontamentoDebugSqlTempoArquivo();
    if (!is_file($arquivo)) {
        return array();
    }

    $linhas = @file($arquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!$linhas) {
        return array();
    }

    $linhas = array_slice($linhas, -1 * max(1, (int) $limite));
    $eventos = array();

    foreach ($linhas as $linha) {
        $evento = json_decode(trim((string) $linha), true);
        if (is_array($evento)) {
            $eventos[] = $evento;
        }
    }

    return array_reverse($eventos);
}

function apontamentoDebugSqlTempoMontarPainel($limiteEventos = 60, $limiteRequests = 10)
{
    $eventos = apontamentoDebugSqlTempoLerEventosRecentes($limiteEventos);
    $requests = array();
    $sqls = array();
    $blocos = array();

    foreach ($eventos as $evento) {
        $requestId = isset($evento['request_id']) ? (string) $evento['request_id'] : 'sem_request_id';
        if (!isset($requests[$requestId])) {
            $requests[$requestId] = array(
                'request_id' => $requestId,
                'uri' => isset($evento['uri']) ? (string) $evento['uri'] : '',
                'acao' => isset($evento['acao']) ? (string) $evento['acao'] : '',
                'op_id' => isset($evento['op_id']) ? (int) $evento['op_id'] : 0,
                'item_id' => isset($evento['item_id']) ? (int) $evento['item_id'] : 0,
                'query_count' => 0,
                'query_time_ms' => 0.0,
                'duracao_request_ms' => null,
            );
        }

        if (isset($evento['evento']) && $evento['evento'] === 'sql') {
            $requests[$requestId]['query_count']++;
            $requests[$requestId]['query_time_ms'] += isset($evento['duracao_ms']) ? (float) $evento['duracao_ms'] : 0.0;
            $sqls[] = $evento;
        }

        if (isset($evento['evento']) && $evento['evento'] === 'bloco') {
            $blocos[] = $evento;
        }

        if (isset($evento['evento']) && $evento['evento'] === 'resumo_request') {
            $requests[$requestId]['duracao_request_ms'] = isset($evento['duracao_request_ms']) ? (float) $evento['duracao_request_ms'] : null;
        }
    }

    usort($sqls, function ($a, $b) {
        $aTempo = isset($a['duracao_ms']) ? (float) $a['duracao_ms'] : 0.0;
        $bTempo = isset($b['duracao_ms']) ? (float) $b['duracao_ms'] : 0.0;
        if ($aTempo == $bTempo) {
            return 0;
        }
        return ($aTempo < $bTempo) ? 1 : -1;
    });

    usort($blocos, function ($a, $b) {
        $aTempo = isset($a['duracao_ms']) ? (float) $a['duracao_ms'] : 0.0;
        $bTempo = isset($b['duracao_ms']) ? (float) $b['duracao_ms'] : 0.0;
        if ($aTempo == $bTempo) {
            return 0;
        }
        return ($aTempo < $bTempo) ? 1 : -1;
    });

    $requests = array_values($requests);
    usort($requests, function ($a, $b) {
        $aTempo = $a['duracao_request_ms'] !== null ? (float) $a['duracao_request_ms'] : (float) $a['query_time_ms'];
        $bTempo = $b['duracao_request_ms'] !== null ? (float) $b['duracao_request_ms'] : (float) $b['query_time_ms'];
        if ($aTempo == $bTempo) {
            return 0;
        }
        return ($aTempo < $bTempo) ? 1 : -1;
    });

    return array(
        'eventos' => $eventos,
        'sqls' => array_slice($sqls, 0, 20),
        'blocos' => array_slice($blocos, 0, 20),
        'requests' => array_slice($requests, 0, max(1, (int) $limiteRequests)),
        'resumo_atual' => apontamentoDebugSqlTempoResumoAtual(),
    );
}
function apontamentoInterpretarRespostaLegada($body, $mensagemPadrao)
{
    $texto = trim((string) $body);
    $texto = preg_replace('/^\xEF\xBB\xBF/', '', $texto);

    $candidatos = array($texto);
    if (preg_match('/[A-Za-z0-9_]+\((\{.*\})\);?/s', $texto, $matches)) {
        $candidatos[] = $matches[1];
    }
    if (preg_match('/(\{(?:[^{}]|(?R))*\})/s', $texto, $matches)) {
        $candidatos[] = $matches[1];
    }
    if (preg_match('/(\[[\s\S]*\])/', $texto, $matches)) {
        $candidatos[] = $matches[1];
    }

    foreach ($candidatos as $json) {
        $data = json_decode(trim((string) $json), true);
        if (is_array($data)) {
            if (!empty($data['code'])) {
                return array('ok' => true, 'data' => $data);
            }

            $erro = isset($data['data']['erro']) ? trim((string) $data['data']['erro']) : '';
            if ($erro === '' && isset($data['erro'])) {
                $erro = trim((string) $data['erro']);
            }
            if ($erro === '') {
                $erro = $mensagemPadrao;
            }

            return array('ok' => false, 'erro' => $erro, 'raw' => $texto);
        }
    }

    $amostra = preg_replace('/\s+/', ' ', $texto);
    $amostra = mb_substr($amostra, 0, 220, 'UTF-8');
    return array('ok' => false, 'erro' => $mensagemPadrao . ' Resposta recebida: ' . $amostra, 'raw' => $texto);
}

function apontamentoMontarUrlLegado($caminho, array $params)
{
    $caminho = '/' . ltrim((string) $caminho, '/');
    $scriptName = isset($_SERVER['SCRIPT_NAME']) ? str_replace('\\', '/', (string) $_SERVER['SCRIPT_NAME']) : '';
    $basePath = '';

    $marker = '/inecel/apontamento/';
    $markerPos = $scriptName !== '' ? stripos($scriptName, $marker) : false;
    if ($markerPos !== false) {
        $basePath = substr($scriptName, 0, $markerPos);
    }

    return rtrim($basePath, '/') . $caminho . '?' . http_build_query($params);
}


function apontamentoOrigemAtual()
{
    $httpsAtivo = (
        (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443)
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
    );
    $scheme = $httpsAtivo ? 'https' : 'http';
    $host = isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] !== ''
        ? (string) $_SERVER['HTTP_HOST']
        : (string) (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost');

    return $scheme . '://' . $host;
}

function apontamentoMontarCookieHeader()
{
    if (empty($_COOKIE) || !is_array($_COOKIE)) {
        return '';
    }

    $cookies = array();
    foreach ($_COOKIE as $nome => $valor) {
        if ($nome === '' || is_array($valor)) {
            continue;
        }
        $cookies[] = $nome . '=' . rawurlencode((string) $valor);
    }

    return implode('; ', $cookies);
}

function apontamentoHttpGetAutenticado($url)
{
    $cookieHeader = apontamentoMontarCookieHeader();
    $headers = array(
        'Accept: application/json,text/plain,*/*',
        'X-Requested-With: XMLHttpRequest'
    );

    if ($cookieHeader !== '') {
        $headers[] = 'Cookie: ' . $cookieHeader;
    }

    $referer = apontamentoOrigemAtual() . (string) (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 45);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_REFERER, $referer);
        curl_setopt($ch, CURLOPT_USERAGENT, (string) (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'HardnessApontamento/1.0'));
        if (stripos($url, 'https://') === 0) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }

        $body = curl_exec($ch);
        $error = curl_error($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $effectiveUrl = (string) curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);

        return array(
            'ok' => $body !== false && $error === '',
            'body' => (string) $body,
            'status' => $status,
            'effective_url' => $effectiveUrl,
            'error' => $error,
        );
    }

    $context = stream_context_create(array(
        'http' => array(
            'method' => 'GET',
            'timeout' => 45,
            'ignore_errors' => true,
            'header' => implode("\r\n", array_merge($headers, array('Referer: ' . $referer))),
        ),
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
        ),
    ));

    $body = @file_get_contents($url, false, $context);
    $status = 0;
    if (!empty($http_response_header) && preg_match('/\s(\d{3})\s/', (string) $http_response_header[0], $matches)) {
        $status = (int) $matches[1];
    }

    return array(
        'ok' => $body !== false,
        'body' => (string) $body,
        'status' => $status,
        'effective_url' => $url,
        'error' => $body === false ? 'Falha ao consultar o gerador da etiqueta.' : '',
    );
}
function apontamentoSqlQuery($sql)
{
    global $g;

    $inicio = microtime(true);
    $resultado = \mysqli_query($g['conexaoBanco'], $sql);
    $duracaoMs = (microtime(true) - $inicio) * 1000;
    $erro = $resultado === false ? \mysqli_error($g['conexaoBanco']) : '';
    $linhasAfetadas = function_exists('\mysqli_affected_rows') ? @\mysqli_affected_rows($g['conexaoBanco']) : null;

    apontamentoDebugSqlTempoRegistrarQuery($sql, $duracaoMs, $resultado !== false, $erro, $linhasAfetadas);

    return $resultado;
}

function apontamentoSqlError()
{
    global $g;

    return \mysqli_error($g['conexaoBanco']);
}

function apontamentoSqlExec($sql)
{
    $resultado = apontamentoSqlQuery($sql);
    if ($resultado === false) {
        throw new \Exception(apontamentoSqlError());
    }

    return $resultado;
}
function apontamentoAtualizaTotaisItemEOp($T256_Id, $T255_Id)
{
    return apontamentoDebugSqlTempoMedirBloco('apontamentoAtualizaTotaisItemEOp', function () use ($T256_Id, $T255_Id) {
        $possuiPlanoCorte = apontamentoBuscarUmaLinha("SELECT SMT134_Id FROM SMT134 WHERE SMT134_T255_Id = '{$T255_Id}' LIMIT 1");
        if (!$possuiPlanoCorte) {
            return;
        }

        $sqlUpdateT256 = "UPDATE T256
                          SET
                              T256_Quantidade = (
                                  SELECT COALESCE(SUM(T257_Quantidade), 0)
                                  FROM T257
                                  WHERE T257_T256_Id = '{$T256_Id}'
                              ),
                              T256_Quantidade_Produzida = (
                                  SELECT COALESCE(SUM(T257_Quantidade), 0)
                                  FROM T257
                                  WHERE T257_T256_Id = '{$T256_Id}'
                              )
                          WHERE T256_Id = '{$T256_Id}'";
        apontamentoSqlExec($sqlUpdateT256);

        $sqlUpdateT255 = "UPDATE T255
                          SET
                              T255_Quantidade = (
                                  SELECT COALESCE(SUM(T256_Quantidade), 0)
                                  FROM T256
                                  WHERE T256_T255_Id = '{$T255_Id}'
                              ),
                              T255_Quantidade_Produzida = (
                                  SELECT COALESCE(SUM(T256_Quantidade), 0)
                                  FROM T256
                                  WHERE T256_T255_Id = '{$T255_Id}'
                              )
                          WHERE T255_Id = '{$T255_Id}'";
        apontamentoSqlExec($sqlUpdateT255);
    }, array(
        't256_id' => (int) $T256_Id,
        't255_id' => (int) $T255_Id,
    ));
}

function apontamentoObterUltimoIdInserido()
{
    global $g;

    if (!empty($g['mysqlLastId'])) {
        return (int) $g['mysqlLastId'];
    }

    $resultado = apontamentoSqlQuery("SELECT LAST_INSERT_ID() AS ultimo_id");
    if ($resultado) {
        $linha = mysql_fetch_assoc($resultado);
        if (!empty($linha['ultimo_id'])) {
            return (int) $linha['ultimo_id'];
        }
    }

    if (function_exists('mysql_insert_id')) {
        return (int) mysql_insert_id();
    }

    return 0;
}
function apontamentoExecutarOutroInclusao($itemId, $qtdeApontar, $pesoPallet, $pedidoId, $qtdeChapa, $observacao, $quantidadeLancamentos = 1)
{
    global $g;

    return apontamentoDebugSqlTempoMedirBloco('apontamentoExecutarOutroInclusao', function () use ($itemId, $qtdeApontar, $pesoPallet, $pedidoId, $qtdeChapa, $observacao, $quantidadeLancamentos, $g) {
        $T256_Id = (int) $itemId;
        $qtdeApontar = (float) apontamentoNumero($qtdeApontar);
        $pesoPallet = (float) apontamentoNumero($pesoPallet);
        $T005_Id = (int) $pedidoId;
        $QtdeChapa = (float) apontamentoNumero($qtdeChapa);
        $T257A_Observacao = apontamentoEscapar($observacao);
        $usuarioAtual = (int) $g['usuarioAtual'];
        $quantidadeLancamentos = max(1, (int) $quantidadeLancamentos);

        apontamentoSqlExec("SET AUTOCOMMIT=0");
        apontamentoSqlExec("START TRANSACTION");

        try {
            $dadosOP = apontamentoSqlQuery("SELECT T256_T255_Id,
                                           D001A_Tipo_Controle
                                    FROM T256
                                    LEFT JOIN D009 ON T256_D009_Id = D009_Id
                                    LEFT JOIN D001 ON D009_D001_Id = D001_Id
                                    LEFT JOIN D001A ON D001A_D001_Id = D001_Id
                                    WHERE T256_Id = '{$T256_Id}'");
            if (!$dadosOP) {
                throw new \Exception(apontamentoSqlError());
            }

            $rowOP = mysql_fetch_assoc($dadosOP);
            if (!$rowOP) {
                throw new \Exception('Item da OP nao encontrado.');
            }

            $T255_Id = (int) $rowOP['T256_T255_Id'];
            $tipoControle = (string) $rowOP['D001A_Tipo_Controle'];

            $possuiPlanoCorte = apontamentoBuscarUmaLinha("SELECT SMT134_Id FROM SMT134 WHERE SMT134_T255_Id = '{$T255_Id}' LIMIT 1");
            if (!$possuiPlanoCorte) {
                $totaisItem = apontamentoBuscarUmaLinha("SELECT IFNULL(T256_Quantidade, 0) AS T256_Quantidade,
                                                                 IFNULL((SELECT SUM(T257_Quantidade) FROM T257 WHERE T257_T256_Id = '{$T256_Id}'), 0) AS Quantidade_Apontada
                                                          FROM T256
                                                          WHERE T256_Id = '{$T256_Id}'");
                if (!$totaisItem) {
                    throw new \Exception('Nao foi possivel validar a quantidade do item.');
                }

                $quantidadePlanejada = (float) $totaisItem['T256_Quantidade'];
                $quantidadeJaApontada = (float) $totaisItem['Quantidade_Apontada'];
                $quantidadeTotalAposLancamento = $quantidadeJaApontada + ($qtdeApontar * $quantidadeLancamentos);

                if ($quantidadeTotalAposLancamento > $quantidadePlanejada) {
                    throw new \Exception('Sem plano de corte cadastrado para esta OP, nao e possivel apontar mais do que a quantidade da OP (' . number_format($quantidadePlanejada, 3, ',', '.') . ').');
                }
            }

            $dadosT257A = apontamentoSqlQuery("SELECT COALESCE(MAX(T257A.T257A_Sequencia), 0) AS UltimaSequencia
                                       FROM T257A
                                       LEFT JOIN T257 ON T257A_T257_Id = T257_Id
                                       LEFT JOIN T256 ON T257_T256_Id = T256_Id
                                       WHERE T256_T255_Id = '{$T255_Id}'");
            if (!$dadosT257A) {
                throw new \Exception(apontamentoSqlError());
            }

            $sequencia = mysql_fetch_assoc($dadosT257A);
            $nextSequencia = (int) $sequencia['UltimaSequencia'] + 1;

            for ($indiceLancamento = 0; $indiceLancamento < $quantidadeLancamentos; $indiceLancamento++) {
                $sequenciaAtual = $nextSequencia + $indiceLancamento;
                $lote = apontamentoEscapar($tipoControle === 'D' ? $T255_Id . ' - ' . $sequenciaAtual : ' ');

                $insertT257 = "INSERT INTO T257 (
                                    T257_T256_Id,
                                    T257_C007_Id,
                                    T257_T066_Id,
                                    T257_Data_Producao,
                                    T257_Quantidade
                                ) VALUES (
                                    '{$T256_Id}',
                                    '{$usuarioAtual}',
                                    '0',
                                    current_date(),
                                    '{$qtdeApontar}'
                                )";
                apontamentoSqlExec($insertT257);

                $T257_Id = apontamentoObterUltimoIdInserido();
                if ($T257_Id <= 0) {
                    throw new \Exception('Nao foi possivel identificar o apontamento gravado.');
                }

                $insertT257A = "INSERT INTO T257A (
                                    T257A_T257_Id,
                                    T257A_Flag_Inserir_Producao_Individual,
                                    T257A_Lote,
                                    T257A_Sequencia,
                                    T257A_Peso,
                                    T257A_T005_Id,
                                    T257A_Qtde_Chapa,
                                    T257A_Observacao
                                ) VALUES (
                                    '{$T257_Id}',
                                    'N',
                                    '{$lote}',
                                    '{$sequenciaAtual}',
                                    '{$pesoPallet}',
                                    '{$T005_Id}',
                                    '{$QtdeChapa}',
                                    '{$T257A_Observacao}'
                                )";
                apontamentoSqlExec($insertT257A);
            }

            apontamentoAtualizaTotaisItemEOp($T256_Id, $T255_Id);
            apontamentoSqlExec("COMMIT");

            return array('ok' => true, 'data' => array('code' => true, 'quantidade_lancamentos' => $quantidadeLancamentos));
        } catch (\Exception $e) {
            apontamentoSqlExec("ROLLBACK");
            return array('ok' => false, 'erro' => $e->getMessage());
        }
    }, array(
        'item_id_alvo' => (int) $itemId,
        'pedido_id' => (int) $pedidoId,
        'quantidade_lancamentos' => max(1, (int) $quantidadeLancamentos),
    ));
}

function apontamentoExecutarOutroAtualizacao($apontamentoId, $qtdeApontar, $pesoPallet, $pedidoId, $qtdeChapa, $observacao)
{
    return apontamentoDebugSqlTempoMedirBloco('apontamentoExecutarOutroAtualizacao', function () use ($apontamentoId, $qtdeApontar, $pesoPallet, $pedidoId, $qtdeChapa, $observacao) {
        $T257_Id = (int) $apontamentoId;
        $T257_Quantidade = (float) apontamentoNumero($qtdeApontar);
        $T257A_Peso = (float) apontamentoNumero($pesoPallet);
        $T257A_T005_Id = (int) $pedidoId;
        $T257A_Qtde_Chapa = (float) apontamentoNumero($qtdeChapa);
        $T257A_Observacao = apontamentoEscapar($observacao);

        apontamentoSqlExec("SET AUTOCOMMIT=0");
        apontamentoSqlExec("START TRANSACTION");

        try {
            $dadosT257 = apontamentoBuscarUmaLinha("SELECT T257_FORM.T257_T256_Id, T256_FORM.T256_T255_Id, T256_FORM.T256_Quantidade
                                      FROM T257 AS T257_FORM
                                      LEFT JOIN T256 AS T256_FORM ON T256_FORM.T256_Id = T257_FORM.T257_T256_Id
                                      WHERE T257_FORM.T257_Id = '{$T257_Id}'
                                      LIMIT 1");
            if (!$dadosT257) {
                throw new \Exception('Apontamento nao encontrado para atualizacao.');
            }

            $T256_Id = (int) $dadosT257['T257_T256_Id'];
            $T255_Id = (int) $dadosT257['T256_T255_Id'];

            $possuiPlanoCorte = apontamentoBuscarUmaLinha("SELECT SMT134_Id FROM SMT134 WHERE SMT134_T255_Id = '{$T255_Id}' LIMIT 1");
            if (!$possuiPlanoCorte) {
                $quantidadePlanejada = (float) $dadosT257['T256_Quantidade'];
                $outrosApontamentos = apontamentoBuscarUmaLinha("SELECT IFNULL(SUM(T257_Quantidade), 0) AS Total
                                                                  FROM T257
                                                                  WHERE T257_T256_Id = '{$T256_Id}'
                                                                    AND T257_Id != '{$T257_Id}'");
                $quantidadeOutros = $outrosApontamentos ? (float) $outrosApontamentos['Total'] : 0;
                $quantidadeTotalAposAtualizacao = $quantidadeOutros + $T257_Quantidade;

                if ($quantidadeTotalAposAtualizacao > $quantidadePlanejada) {
                    throw new \Exception('Sem plano de corte cadastrado para esta OP, nao e possivel apontar mais do que a quantidade da OP (' . number_format($quantidadePlanejada, 3, ',', '.') . ').');
                }
            }

            if (!apontamentoSqlQuery("UPDATE T257
                              SET T257_Quantidade = '{$T257_Quantidade}'
                              WHERE T257_Id = '{$T257_Id}'")) {
                throw new \Exception(apontamentoSqlError());
            }

            if (!apontamentoSqlQuery("UPDATE T257A
                              SET T257A_Qtde_Chapa = '{$T257A_Qtde_Chapa}',
                                  T257A_T005_Id = '{$T257A_T005_Id}',
                                  T257A_Peso = '{$T257A_Peso}',
                                  T257A_Observacao = '{$T257A_Observacao}'
                              WHERE T257A_T257_Id = '{$T257_Id}'")) {
                throw new \Exception(apontamentoSqlError());
            }

            apontamentoAtualizaTotaisItemEOp($T256_Id, $T255_Id);
            apontamentoSqlExec("COMMIT");

            return array('ok' => true, 'data' => array('code' => true));
        } catch (\Exception $e) {
            apontamentoSqlExec("ROLLBACK");
            return array('ok' => false, 'erro' => $e->getMessage());
        }
    }, array(
        't257_id_alvo' => (int) $apontamentoId,
        'pedido_id' => (int) $pedidoId,
    ));
}
function apontamentoBuscarLinhas($sql)
{
    $resultado = apontamentoSqlQuery($sql);
    $linhas = array();
    if ($resultado) {
        while ($linha = mysql_fetch_assoc($resultado)) {
            $linhas[] = $linha;
        }
    }
    return $linhas;
}

function apontamentoBuscarUmaLinha($sql)
{
    $resultado = apontamentoSqlQuery($sql);
    if (!$resultado || mysql_num_rows($resultado) <= 0) {
        return null;
    }
    return mysql_fetch_assoc($resultado);
}

function apontamentoPodeEditarApontamento($t257Id)
{
    $t257Id = (int) $t257Id;
    if ($t257Id <= 0) {
        return false;
    }

    $row = apontamentoBuscarUmaLinha("SELECT IFNULL(T001.T001_Id, 0) AS T001_Id FROM T257 LEFT JOIN T001 ON T001.T001_T257_Id = T257.T257_Id WHERE T257.T257_Id = '{$t257Id}' LIMIT 1");
    if (!$row) {
        return false;
    }

    return (int) $row['T001_Id'] <= 0;
}

function apontamentoBuscarImpressoraEtiquetaDebug()
{
    global $g;

    $empresaAtual = isset($_SESSION['Empresa_Atual']) ? (int) $_SESSION['Empresa_Atual'] : 0;
    $usuarioImpressora = '';
    $origemParametro = 'C031_empresa';

    if ($empresaAtual > 0) {
        $sqlParametro = "
            SELECT C031_Valor
            FROM C031
            WHERE C031_C004_Id = '{$empresaAtual}'
              AND C031_Campo = 'SMT_ativaUsuarioImpressora'
            LIMIT 1
        ";
        $fetchParametro = apontamentoSqlQuery($sqlParametro);
        if ($fetchParametro && mysql_num_rows($fetchParametro) > 0) {
            $dadosParametro = mysql_fetch_assoc($fetchParametro);
            $usuarioImpressora = isset($dadosParametro['C031_Valor']) ? (string) $dadosParametro['C031_Valor'] : '';
        }
    }

    if ($usuarioImpressora === '') {
        $usuarioImpressora = isset($g['C031']['SMT_ativaUsuarioImpressora']) ? (string) $g['C031']['SMT_ativaUsuarioImpressora'] : 'N';
        $origemParametro = 'g_C031_fallback';
    }

    $modoBusca = $usuarioImpressora === 'S' ? 'usuario' : 'geral';
    $sqlEtiqueta  = " SELECT D175.D175_Id, D175.D175_Nome ";
    $sqlEtiqueta .= "   FROM D175 ";
    if ($usuarioImpressora === 'S') {
        $sqlEtiqueta .= "  LEFT JOIN D175A ON D175A_D175_Id = D175_Id ";
    }
    $sqlEtiqueta .= "  WHERE D175.D175_Tipo_Impressora = 6 ";
    if ($usuarioImpressora === 'S') {
        $sqlEtiqueta .= "  AND   D175A.D175A_C007_Id = '{$g['usuarioAtual']}' ";
    }
    $sqlEtiqueta .= "  ORDER BY D175.D175_Id ASC ";
    $sqlEtiqueta .= "  LIMIT 1 ";

    $retorno = array(
        'usuario_impressora' => $usuarioImpressora,
        'modo_busca' => $modoBusca,
        'origem_parametro' => $origemParametro,
        'sql' => $sqlEtiqueta,
        'nome' => '',
        'd175_id' => '',
        'usuario_atual' => isset($g['usuarioAtual']) ? (string) $g['usuarioAtual'] : '',
        'empresa_atual' => isset($_SESSION['Empresa_Atual']) ? (string) $_SESSION['Empresa_Atual'] : '',
        'empresa_nome' => isset($_SESSION['Nome_Empresa_Atual']) ? (string) $_SESSION['Nome_Empresa_Atual'] : '',
    );

    $fetchEtiqueta = apontamentoSqlQuery($sqlEtiqueta);
    if ($fetchEtiqueta && mysql_num_rows($fetchEtiqueta) > 0) {
        $dadosEtiqueta = mysql_fetch_array($fetchEtiqueta);
        $retorno['nome'] = !empty($dadosEtiqueta['D175_Nome']) ? (string) $dadosEtiqueta['D175_Nome'] : '';
        $retorno['d175_id'] = !empty($dadosEtiqueta['D175_Id']) ? (string) $dadosEtiqueta['D175_Id'] : '';
    }

    return $retorno;
}

function apontamentoBuscarImpressoraEtiqueta()
{
    $debug = apontamentoBuscarImpressoraEtiquetaDebug();
    return isset($debug['nome']) ? (string) $debug['nome'] : '';
}



function apontamentoCompanyRows()
{
    global $g;
    $usuarioId = isset($g['usuarioAtual']) ? (int) $g['usuarioAtual'] : 0;
    if ($usuarioId <= 0) {
        return array();
    }

    $sql = "
        SELECT C019.C019_C004_Id, C019.C019_C021_Id, C004.C004_Nome_Empresa, C004.C004_Nome_Abreviado, C004.C004_Caminho_Sistema, C004.C004_Banco_Configuracao
        FROM C019
        INNER JOIN C004 ON C004.C004_Id = C019.C019_C004_Id
        WHERE C019.C019_C007_Id = '{$usuarioId}'
          AND C004.C004_Flag_Ativo = 'S'
        ORDER BY C004.C004_Ordem_Empresa ASC, C004.C004_Id ASC
    ";
    $resultado = apontamentoSqlQuery($sql);
    $linhas = array();
    if ($resultado) {
        while ($linha = mysql_fetch_assoc($resultado)) {
            $linhas[] = $linha;
        }
    }
    return $linhas;
}

function apontamentoApplyCompanySession($company)
{
    $_SESSION['Perfil_Usuario'] = $company['C019_C021_Id'];
    $_SESSION['Empresa_Atual'] = $company['C019_C004_Id'];
    $_SESSION['Empresa_Logada'] = $company['C019_C004_Id'];
    $_SESSION['Nome_Empresa_Atual'] = $company['C004_Nome_Empresa'];
    $_SESSION['Pasta_Empresa'] = $company['C004_Caminho_Sistema'];
    $_SESSION['cBd_Configuracao'] = $company['C004_Banco_Configuracao'];
}

function apontamentoSwitchCompany($empresaId, $companies)
{
    foreach ($companies as $company) {
        if ((int) $company['C019_C004_Id'] === $empresaId) {
            apontamentoApplyCompanySession($company);
            return true;
        }
    }
    return false;
}

function apontamentoCompanyTheme($empresaId)
{
    $themes = array(
        array('bg' => '#eef6ff', 'panel' => '#ffffff', 'line' => '#c9dffc', 'primary' => '#155eef', 'accent' => '#dceaff'),
        array('bg' => '#f3fbf5', 'panel' => '#ffffff', 'line' => '#cfe8d5', 'primary' => '#168a4a', 'accent' => '#dff5e6'),
        array('bg' => '#fff8ef', 'panel' => '#ffffff', 'line' => '#f0dac0', 'primary' => '#b35a00', 'accent' => '#ffe8cf'),
        array('bg' => '#fff1f3', 'panel' => '#ffffff', 'line' => '#f1ccd4', 'primary' => '#c23d5a', 'accent' => '#ffdfe5'),
        array('bg' => '#f5f3ff', 'panel' => '#ffffff', 'line' => '#ddd7fb', 'primary' => '#6941c6', 'accent' => '#ebe5ff'),
        array('bg' => '#eefaf9', 'panel' => '#ffffff', 'line' => '#c8e9e4', 'primary' => '#0f8b8d', 'accent' => '#d7f4ef'),
    );
    return $themes[$empresaId % count($themes)];
}

function apontamentoSelectedCompanyName($companies)
{
    $empresaAtual = isset($_SESSION['Empresa_Atual']) ? (int) $_SESSION['Empresa_Atual'] : 0;
    foreach ($companies as $company) {
        if ((int) $company['C019_C004_Id'] === $empresaAtual) {
            return (string) ($company['C004_Nome_Abreviado'] ?: $company['C004_Nome_Empresa']);
        }
    }
    return isset($_SESSION['Nome_Empresa_Atual']) ? (string) $_SESSION['Nome_Empresa_Atual'] : 'Empresa';
}

$companies = apontamentoCompanyRows();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['empresa_id'])) {
    $empresaId = (int) $_POST['empresa_id'];
    if (apontamentoSwitchCompany($empresaId, $companies)) {
        header('Location: ./apontar.php?op_id=' . (isset($_GET['op_id']) ? (int) $_GET['op_id'] : 0) . '&item_id=' . (isset($_GET['item_id']) ? (int) $_GET['item_id'] : 0));
        exit;
    }
}
$usuario = isset($_SESSION['Primeiro_Nome_Usuario']) ? $_SESSION['Primeiro_Nome_Usuario'] : (isset($_SESSION['Nome_Usuario']) ? $_SESSION['Nome_Usuario'] : 'Usuario');
$empresa = isset($_SESSION['Nome_Empresa_Atual']) ? $_SESSION['Nome_Empresa_Atual'] : 'Empresa';
$empresaAtualId = isset($_SESSION['Empresa_Atual']) ? (int) $_SESSION['Empresa_Atual'] : 0;
$empresaCurta = apontamentoSelectedCompanyName($companies);
$companyTheme = apontamentoCompanyTheme($empresaAtualId);
$impressoraEtiquetaDebug = apontamentoBuscarImpressoraEtiquetaDebug();
$impressoraEtiqueta = isset($impressoraEtiquetaDebug['nome']) ? $impressoraEtiquetaDebug['nome'] : '';
$apontamentoDebugSqlTempoPainel = apontamentoDebugSqlTempoAtivo() ? apontamentoDebugSqlTempoMontarPainel(120, 12) : array();



$opId = isset($_GET['op_id']) ? (int) $_GET['op_id'] : 0;
$busca = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
$itemSelecionadoId = isset($_GET['item_id']) ? (int) $_GET['item_id'] : 0;
if (isset($_GET['acao']) && $_GET['acao'] === 'gerar_etiqueta_pdf') {
    header('Content-Type: application/json; charset=UTF-8');

    try {
        $t257Id = isset($_GET['t257_id']) ? (int) $_GET['t257_id'] : 0;
        if ($t257Id <= 0) {
            echo json_encode(array('code' => false, 'erro' => 'Apontamento invalido para impressao.'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        $telaLinkRel = apontamentoMontarUrlLegado('/sistema/personalizacao/tela/d99135ae02d6770a33859939030676b3/', array(
            'ajax' => 'true',
            'T257_Id' => $t257Id,
        ));
        $telaLinkAbs = apontamentoOrigemAtual() . $telaLinkRel;
        $pdfUrl = apontamentoOrigemAtual() . apontamentoMontarUrlLegado('/sistema/personalizacaoLowCode/grid_func-ajax/telaGerarPdf/', array(
            'ajax' => 'true',
            'acaoId' => 'd99135ae02d6770a33859939030676b3',
            'link' => $telaLinkAbs,
        ));

        $respostaPdf = apontamentoHttpGetAutenticado($pdfUrl);
        if (empty($respostaPdf['ok'])) {
            echo json_encode(array(
                'code' => false,
                'erro' => 'Falha ao consultar o gerador da etiqueta.',
                'debug' => array(
                    'status' => isset($respostaPdf['status']) ? (int) $respostaPdf['status'] : 0,
                    'effective_url' => isset($respostaPdf['effective_url']) ? (string) $respostaPdf['effective_url'] : '',
                    'error' => isset($respostaPdf['error']) ? (string) $respostaPdf['error'] : '',
                ),
            ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        $interpreted = apontamentoInterpretarRespostaLegada($respostaPdf['body'], 'Nao foi possivel obter o PDF da etiqueta para impressao direta.');
        if (!empty($interpreted['ok']) && !empty($interpreted['data'])) {
            if (!empty($interpreted['data']['data']) && strpos((string) $interpreted['data']['data'], 'http') !== 0) {
                $interpreted['data']['data'] = apontamentoOrigemAtual() . '/' . ltrim((string) $interpreted['data']['data'], '/');
            }
            echo json_encode($interpreted['data'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        $amostra = isset($respostaPdf['body']) ? preg_replace('/\s+/', ' ', trim((string) $respostaPdf['body'])) : '';
        $amostra = function_exists('mb_substr') ? mb_substr($amostra, 0, 220, 'UTF-8') : substr($amostra, 0, 220);
        echo json_encode(array(
            'code' => false,
            'erro' => !empty($interpreted['erro']) ? $interpreted['erro'] : 'Nao foi possivel obter o PDF da etiqueta para impressao direta.',
            'debug' => array(
                'status' => isset($respostaPdf['status']) ? (int) $respostaPdf['status'] : 0,
                'effective_url' => isset($respostaPdf['effective_url']) ? (string) $respostaPdf['effective_url'] : '',
                'amostra' => $amostra,
            ),
            'fallback' => $telaLinkAbs,
        ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    } catch (\Throwable $e) {
        echo json_encode(array(
            'code' => false,
            'erro' => 'Falha interna ao gerar a etiqueta.',
            'debug' => array(
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ),
        ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'atualizar_apontamento') {
    $t257Id = isset($_POST['t257_id']) ? (int) $_POST['t257_id'] : 0;
    $itemPostId = isset($_POST['item_id']) ? (int) $_POST['item_id'] : 0;
    $opPostId = isset($_POST['op_id']) ? (int) $_POST['op_id'] : 0;
    $qtdeApontar = trim((string) (isset($_POST['qtde_apontar']) ? $_POST['qtde_apontar'] : ''));
    $pesoPallet = trim((string) (isset($_POST['peso_pallet']) ? $_POST['peso_pallet'] : ''));
    $qtdeChapa = trim((string) (isset($_POST['qtde_chapa']) ? $_POST['qtde_chapa'] : ''));
    $pedidoId = isset($_POST['pedido_id']) && $_POST['pedido_id'] !== '' ? (int) $_POST['pedido_id'] : 0;
    $observacao = trim((string) (isset($_POST['observacao']) ? $_POST['observacao'] : ''));

    if ($t257Id > 0 && $itemPostId > 0 && $qtdeApontar !== '') {
        if (!apontamentoPodeEditarApontamento($t257Id)) {
            apontamentoFlash('erro', 'Este apontamento ja movimentou estoque e nao pode mais ser editado.');
        } else {
            $resultadoAtualizacao = apontamentoExecutarOutroAtualizacao($t257Id, $qtdeApontar, $pesoPallet, $pedidoId, $qtdeChapa, $observacao);
            if (!empty($resultadoAtualizacao['ok'])) {
                apontamentoFlash('sucesso', 'Apontamento atualizado com sucesso.');
            } else {
                apontamentoFlash('erro', !empty($resultadoAtualizacao['erro']) ? $resultadoAtualizacao['erro'] : 'Nao foi possivel atualizar o apontamento.');
            }
        }
    }
    header('Location: ./apontar.php?op_id=' . $opPostId . '&item_id=' . $itemPostId);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'lancar_apontamento') {
    $itemPostId = isset($_POST['item_id']) ? (int) $_POST['item_id'] : 0;
    $opPostId = isset($_POST['op_id']) ? (int) $_POST['op_id'] : 0;
    $qtdeApontar = trim((string) (isset($_POST['qtde_apontar']) ? $_POST['qtde_apontar'] : ''));
    $pesoPallet = trim((string) (isset($_POST['peso_pallet']) ? $_POST['peso_pallet'] : ''));
    $qtdeChapa = trim((string) (isset($_POST['qtde_chapa']) ? $_POST['qtde_chapa'] : ''));
    $pedidoId = isset($_POST['pedido_id']) && $_POST['pedido_id'] !== '' ? (int) $_POST['pedido_id'] : 0;
    $observacao = trim((string) (isset($_POST['observacao']) ? $_POST['observacao'] : ''));
    $quantidadeLancamentos = isset($_POST['quantidade_lancamentos']) ? max(1, (int) $_POST['quantidade_lancamentos']) : 1;

    if ($itemPostId > 0 && $qtdeApontar !== '') {
        $resultadoInclusao = apontamentoExecutarOutroInclusao($itemPostId, $qtdeApontar, $pesoPallet, $pedidoId, $qtdeChapa, $observacao, $quantidadeLancamentos);
        if (!empty($resultadoInclusao['ok'])) {
            apontamentoFlash('sucesso', 'Apontamento incluido com sucesso.');
        } else {
            apontamentoFlash('erro', !empty($resultadoInclusao['erro']) ? $resultadoInclusao['erro'] : 'Nao foi possivel gravar o apontamento.');
        }
    }
    header('Location: ./apontar.php?op_id=' . $opPostId . '&item_id=' . $itemPostId);
    exit;
}
$itens = apontamentoBuscarLinhas("SELECT T256_Id, T256_T255_Id, D001_Codigo_Produto, D001_Descricao_Produto, D037_Unidade, IFNULL(T256_Quantidade, 0) AS T256_Quantidade, IFNULL(T256_Quantidade_Produzida, 0) AS T256_Quantidade_Produzida, IFNULL(T256_Quantidade_Entrada_Estoque, 0) AS T256_Quantidade_Entrada_Estoque, COALESCE(T256_Quantidade_Saldo, IFNULL(T256_Quantidade, 0) - IFNULL(T256_Quantidade_Produzida, 0)) AS T256_Quantidade_Saldo, T256_Observacao_Producao, T005_Id, T005_Data_Emissao, T006_Observacao_Entrega, D024_Nome_Empresa FROM T256 LEFT JOIN T256A ON T256_Id = T256A_T256_Id LEFT JOIN D009 ON D009_Id = T256_D009_Id LEFT JOIN D049 ON D049_Id = D009_D049_Id LEFT JOIN D001 ON D001_Id = D049_D001_Id LEFT JOIN D037 ON D037_Id = D001_D037_Id LEFT JOIN T006 ON T006_Id = T256_T006_Id LEFT JOIN T005 ON T005_Id = T006_T005_Id LEFT JOIN D024 ON D024_Id = T005_D024_Id WHERE T256_T255_Id = '" . (int) $opId . "' GROUP BY T256_Id ORDER BY T256_Id ASC");
if ($itemSelecionadoId <= 0 && !empty($itens)) {
    $itemSelecionadoId = (int) $itens[0]['T256_Id'];
}
$itemSelecionado = null;
foreach ($itens as $item) {
    if ((int) $item['T256_Id'] === $itemSelecionadoId) {
        $itemSelecionado = $item;
        break;
    }
}
if ($itemSelecionado && empty($opId)) {
    $opId = (int) $itemSelecionado['T256_T255_Id'];
}
$detalhesItem = null;
$historicoApontamentos = array();
if (!empty($itemSelecionado)) {
    $historicoApontamentos = apontamentoBuscarLinhas("SELECT T257.T257_Id, T257.T257_Quantidade, T257.T257_Data_Producao, T257A.T257A_Peso, T257A.T257A_Qtde_Chapa, T257A.T257A_Lote, T257A.T257A_T005_Id, T257A.T257A_Observacao, IFNULL(T001.T001_Id, 0) AS T001_Id, C007.C007_Primeiro_Nome, D024.D024_Nome_Empresa, CASE WHEN IFNULL(T001.T001_Id, 0) > 0 THEN 'SIM' ELSE 'NAO' END AS Estoque FROM T257 LEFT JOIN T001 ON T257.T257_Id = T001.T001_T257_Id LEFT JOIN C007 ON C007_Id = T257.T257_C007_Id LEFT JOIN T257A ON T257A.T257A_T257_Id = T257.T257_Id LEFT JOIN T005 ON T257A.T257A_T005_Id = T005.T005_Id LEFT JOIN D024 ON T005.T005_D024_Id = D024.D024_Id WHERE T257.T257_T256_Id = '" . (int) $itemSelecionadoId . "' GROUP BY T257.T257_Id ORDER BY T257.T257_Id ASC");
}
$registroEdicao = null;
$editId = isset($_GET['edit_id']) ? (int) $_GET['edit_id'] : 0;
if ($editId > 0 && !empty($itemSelecionado)) {
    if (!apontamentoPodeEditarApontamento($editId)) {
        apontamentoFlash('erro', 'Este apontamento ja movimentou estoque e nao pode mais ser editado.');
        header('Location: ./apontar.php?op_id=' . (int) $opId . '&item_id=' . (int) $itemSelecionadoId);
        exit;
    }
    $registroEdicao = apontamentoBuscarUmaLinha("SELECT T257.T257_Id, T257.T257_Quantidade, T257A.T257A_Peso, T257A.T257A_Qtde_Chapa, T257A.T257A_T005_Id, T257A.T257A_Observacao FROM T257 LEFT JOIN T257A ON T257A.T257A_T257_Id = T257.T257_Id WHERE T257.T257_Id = '{$editId}' AND T257.T257_T256_Id = '" . (int) $itemSelecionadoId . "' LIMIT 1");
}
$flash = apontamentoFlash();
$produtoLinha = '';
if (!empty($itemSelecionado)) {
    $produtoLinha = $itemSelecionado['D001_Codigo_Produto'] . ' - ' . $itemSelecionado['D001_Descricao_Produto'];
    if (!empty($itemSelecionado['D037_Unidade'])) {
        $produtoLinha .= ' / ' . $itemSelecionado['D037_Unidade'];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Apontamento Detalhado OP</title>
    <style>
        :root{--bg:<?php echo htmlspecialchars($companyTheme['bg'], ENT_QUOTES, 'UTF-8'); ?>;--card:<?php echo htmlspecialchars($companyTheme['panel'], ENT_QUOTES, 'UTF-8'); ?>;--line:<?php echo htmlspecialchars($companyTheme['line'], ENT_QUOTES, 'UTF-8'); ?>;--text:#20324d;--muted:#697c96;--primary:<?php echo htmlspecialchars($companyTheme['primary'], ENT_QUOTES, 'UTF-8'); ?>;--shadow:0 18px 40px rgba(17,47,84,.12);--success-bg:#ebfaf1;--success-text:#216f4b;--danger-bg:#fff0f0;--danger-text:#a53a3a}*{box-sizing:border-box}body{margin:0;font-family:Arial,sans-serif;background:linear-gradient(180deg,#f4f7fb 0%,var(--bg) 100%);color:var(--text)}.page{max-width:1420px;margin:0 auto;padding:16px}.window{background:var(--card);border:1px solid #bcc9d6;border-radius:12px;overflow:hidden;box-shadow:var(--shadow)}.window-bar{background:var(--primary);color:#fff;font-weight:700;font-size:15px;padding:9px 12px}.window-body{padding:16px;background:#f5f7fa}.top-meta{display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:10px;flex-wrap:wrap}.top-actions{display:flex;gap:8px;flex-wrap:wrap}.button{border:1px solid #6c7d92;border-radius:6px;padding:8px 14px;background:#fff;color:var(--text);font-size:13px;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;justify-content:center}.button.print{background:var(--primary);border-color:var(--primary);color:#fff;box-shadow:0 8px 18px rgba(45,95,167,.24)}.userbox{font-size:13px;color:var(--muted)}.flash{margin-bottom:12px;padding:12px 14px;border-radius:8px;font-size:14px}.flash.sucesso{background:var(--success-bg);color:var(--success-text);border:1px solid #b9e7cc}.flash.erro{background:var(--danger-bg);color:var(--danger-text);border:1px solid #f0c4c4}.debug-panel{margin-bottom:14px;border:1px solid #f3d19c;border-radius:10px;background:#fffaf3;overflow:hidden}.debug-panel-header{display:flex;justify-content:space-between;gap:12px;align-items:center;padding:10px 14px;background:#fff1da;color:#7a4b00;font-weight:700;font-size:13px;flex-wrap:wrap}.debug-panel-body{padding:12px 14px}.debug-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin-bottom:12px}.debug-card{background:#fff;border:1px solid #ecd5aa;border-radius:8px;padding:10px}.debug-card strong{display:block;font-size:18px;color:#7a4b00}.debug-card span{display:block;font-size:12px;color:#946200;margin-top:4px}.debug-section{margin-top:12px}.debug-section h3{margin:0 0 8px;font-size:13px;color:#7a4b00}.debug-table{width:100%;border-collapse:collapse;background:#fff}.debug-table th,.debug-table td{border:1px solid #ecdcb8;padding:7px 8px;font-size:12px;vertical-align:top}.debug-table th{background:#fff4df;color:#7a4b00;text-align:left}.debug-sql{max-width:620px;white-space:normal;word-break:break-word;color:#5b4a21}.debug-empty{padding:10px;border:1px dashed #e3c98f;border-radius:8px;background:#fff;color:#8a6b2f;font-size:12px}.debug-request{font-family:Consolas,monospace;font-size:12px;color:#5b4a21}.debug-badge{display:inline-block;padding:2px 6px;border-radius:999px;background:#ffe7b5;color:#7a4b00;font-size:11px}@media (max-width:1180px){.form-line{grid-template-columns:1fr 1fr}.debug-grid{grid-template-columns:1fr 1fr}}@media (max-width:760px){.form-line,.field-inline{grid-template-columns:1fr}.field-inline label{margin-bottom:4px}.loading-brand{flex-direction:column;gap:10px}.loading-logo{font-size:42px;letter-spacing:4px}.debug-grid{grid-template-columns:1fr}}.item-strip{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:12px}.item-chip{padding:8px 10px;border-radius:8px;border:1px solid #c8d3df;background:#fff;color:var(--text);text-decoration:none;font-size:12px}.item-chip.active{background:#dfe9f7;border-color:#90abd1;font-weight:700}.produto-bar{width:100%;height:24px;border:1px solid #bfc9d6;border-radius:4px;background:#fff;padding:2px 8px;font-size:13px;color:#3d4e68;margin-bottom:6px}.form-line{display:grid;grid-template-columns:200px 150px 170px 120px 120px minmax(280px,1fr) auto;gap:10px;align-items:end;margin-bottom:14px}.field-inline{display:grid;grid-template-columns:auto 1fr;gap:4px;align-items:center;font-size:13px;min-width:0}.field-inline label{white-space:nowrap}.field-inline input{width:100%;height:25px;border:1px solid #bfc9d6;border-radius:4px;background:#fff;padding:2px 8px;font-size:13px;min-width:0}.field-inline-sm input{width:100%;padding:2px 8px;text-align:left}.field-inline-qtde input{min-width:64px}.field-inline-lg{grid-template-columns:auto minmax(280px,1fr);min-width:0}.form-actions{display:flex;gap:8px;align-items:end;white-space:nowrap}.toolbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}.grid-wrap{background:#fff;border:1px solid #c9d3df;border-radius:8px;overflow:hidden}.table-scroll{overflow-x:auto}table{width:100%;min-width:1220px;border-collapse:collapse}thead th{background:#eef2f7;border:1px solid #c9d3df;padding:12px 8px;font-size:13px;color:#4b5563;text-align:center}tbody td{border:1px solid #d9e1ea;padding:10px 8px;font-size:13px;background:#fff}tbody tr:nth-child(even) td{background:#fafbfd}.center{text-align:center}.right{text-align:right}.empty{padding:32px 20px;text-align:center;color:var(--muted);background:#fff}.edit-pill{display:inline-block;padding:4px 8px;border-radius:999px;background:#edf1f7;color:#5b6778;font-size:12px}.loading-overlay{position:fixed;inset:0;background:rgba(245,247,250,.92);backdrop-filter:blur(3px);display:flex;align-items:center;justify-content:center;z-index:9999;opacity:0;pointer-events:none;transition:opacity .2s ease}.loading-overlay.is-visible{opacity:1;pointer-events:auto}.loading-card{width:min(560px,calc(100vw - 32px));background:#fff;border:1px solid #f3c8a7;border-radius:22px;box-shadow:0 24px 60px rgba(191,95,16,.18);padding:28px 24px;text-align:center}.loading-brand{display:flex;align-items:center;justify-content:center;gap:18px;margin-bottom:18px}.loading-mark{position:relative;width:96px;height:64px;flex:0 0 auto}.loading-mark span{position:absolute;display:block;background:#f97316;border-radius:4px;transform:skewY(-20deg)}.loading-mark span:first-child{left:0;top:8px;width:42px;height:42px}.loading-mark span:last-child{left:34px;top:0;width:42px;height:42px}.loading-mark b{position:absolute;left:26px;top:15px;width:18px;height:18px;background:#fff;display:block;transform:rotate(45deg)}.loading-logo{font-size:58px;line-height:1;font-weight:800;letter-spacing:6px;color:#f97316}.loading-subtitle{margin:0 0 16px;font-size:18px;color:#ea580c}.loading-message{margin:0 0 14px;font-size:16px;color:#374151}.loading-bar{width:100%;height:10px;background:#ffedd5;border-radius:999px;overflow:hidden}.loading-bar::after{content:"";display:block;width:38%;height:100%;background:linear-gradient(90deg,#f97316,#fb923c);border-radius:999px;animation:apontamento-loading 1s ease-in-out infinite}.loading-overlay button{pointer-events:none}@keyframes apontamento-loading{0%{transform:translateX(-120%)}100%{transform:translateX(320%)}}@media (max-width:1180px){.form-line{grid-template-columns:1fr 1fr}}@media (max-width:760px){.form-line,.field-inline{grid-template-columns:1fr}.field-inline label{margin-bottom:4px}.loading-brand{flex-direction:column;gap:10px}.loading-logo{font-size:42px;letter-spacing:4px}}
    </style>
</head>
<body>
<div id="apontamento-loading" class="loading-overlay" aria-hidden="true">
    <div class="loading-card">
        <div class="loading-brand">
            <div class="loading-mark"><span></span><span></span><b></b></div>
            <div class="loading-logo">INECEL</div>
        </div>
        <p class="loading-subtitle">Estruturas para Cabeamento</p>
        <p id="apontamento-loading-text" class="loading-message">Processando apontamento...</p>
        <div class="loading-bar"></div>
    </div>
</div>
<?php if (!empty($itemSelecionado)): ?>
<div class="page">
    <div class="window">
        <div class="window-bar">APONTAMENTO DETALHADO OP</div>
        <div class="window-body">
            <div class="top-meta">
                <div class="top-actions">
                    <a class="button js-loading-link" href="./itensOp.php?op_id=<?php echo (int) $opId; ?>">Voltar para Itens da OP</a>
                    <a class="button js-loading-link" href="./apontar.php?logout=1">Sair</a>
                </div>
                <div class="userbox">
                    <strong><?php echo htmlspecialchars($usuario, ENT_QUOTES, 'UTF-8'); ?></strong>
                    <div><?php echo htmlspecialchars($empresa, ENT_QUOTES, 'UTF-8'); ?> | OP: <?php echo (int) $opId; ?></div>
                </div>
            </div>
            <?php if (!empty($flash)): ?>
                <div class="flash <?php echo htmlspecialchars($flash['tipo'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($flash['mensagem'], ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <?php if (apontamentoDebugSqlTempoAtivo()): ?>
                <div class="debug-panel">
                    <div class="debug-panel-header">
                        <div>Debug SQL Tempo - T255/T256/T257/T257A</div>
                        <div class="debug-request">Arquivo: <?php echo htmlspecialchars(apontamentoDebugSqlTempoArquivo(), ENT_QUOTES, 'UTF-8'); ?></div>
                    </div>
                    <div class="debug-panel-body">
                        <div class="debug-grid">
                            <div class="debug-card">
                                <strong><?php echo (int) (isset($apontamentoDebugSqlTempoPainel['resumo_atual']['query_count']) ? $apontamentoDebugSqlTempoPainel['resumo_atual']['query_count'] : 0); ?></strong>
                                <span>Queries medidas nesta request</span>
                            </div>
                            <div class="debug-card">
                                <strong><?php echo number_format((float) (isset($apontamentoDebugSqlTempoPainel['resumo_atual']['query_time_ms']) ? $apontamentoDebugSqlTempoPainel['resumo_atual']['query_time_ms'] : 0), 3, ',', '.'); ?> ms</strong>
                                <span>Tempo SQL acumulado nesta request</span>
                            </div>
                            <div class="debug-card">
                                <strong><?php echo count(isset($apontamentoDebugSqlTempoPainel['requests']) ? $apontamentoDebugSqlTempoPainel['requests'] : array()); ?></strong>
                                <span>Requests recentes no painel</span>
                            </div>
                            <div class="debug-card">
                                <strong><?php echo count(isset($apontamentoDebugSqlTempoPainel['sqls']) ? $apontamentoDebugSqlTempoPainel['sqls'] : array()); ?></strong>
                                <span>Queries alvo carregadas na tela</span>
                            </div>
                        </div>

                        <div class="debug-section">
                            <h3>Resumo atual por operacao das tabelas alvo</h3>
                            <?php if (empty($apontamentoDebugSqlTempoPainel['resumo_atual']['operacoes'])): ?>
                                <div class="debug-empty">Ainda nao houve escrita monitorada nesta request. Depois de enviar ou editar um apontamento, recarregue a tela para ver os tempos desta mesma sessao e o historico recente.</div>
                            <?php else: ?>
                                <table class="debug-table">
                                    <thead><tr><th>Operacao</th><th>Quantidade</th><th>Tempo acumulado</th></tr></thead>
                                    <tbody>
                                    <?php foreach ($apontamentoDebugSqlTempoPainel['resumo_atual']['operacoes'] as $operacaoNome => $operacaoDados): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars((string) $operacaoNome, ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo (int) (isset($operacaoDados['quantidade']) ? $operacaoDados['quantidade'] : 0); ?></td>
                                            <td><?php echo number_format((float) (isset($operacaoDados['tempo_ms']) ? $operacaoDados['tempo_ms'] : 0), 3, ',', '.'); ?> ms</td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>

                        <div class="debug-section">
                            <h3>Top queries mais lentas das tabelas alvo</h3>
                            <?php if (empty($apontamentoDebugSqlTempoPainel['sqls'])): ?>
                                <div class="debug-empty">Nenhuma query registrada ainda no log.</div>
                            <?php else: ?>
                                <table class="debug-table">
                                    <thead><tr><th>Tempo</th><th>Operacao</th><th>Request</th><th>OP</th><th>Item</th><th>SQL</th></tr></thead>
                                    <tbody>
                                    <?php foreach ($apontamentoDebugSqlTempoPainel['sqls'] as $sqlEvento): ?>
                                        <tr>
                                            <td><?php echo number_format((float) (isset($sqlEvento['duracao_ms']) ? $sqlEvento['duracao_ms'] : 0), 3, ',', '.'); ?> ms</td>
                                            <td><span class="debug-badge"><?php echo htmlspecialchars(trim((string) ((isset($sqlEvento['operacao']) ? $sqlEvento['operacao'] : '') . ' ' . (isset($sqlEvento['tabela']) ? $sqlEvento['tabela'] : ''))), ENT_QUOTES, 'UTF-8'); ?></span></td>
                                            <td class="debug-request"><?php echo htmlspecialchars((string) (isset($sqlEvento['request_id']) ? $sqlEvento['request_id'] : ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo (int) (isset($sqlEvento['op_id']) ? $sqlEvento['op_id'] : 0); ?></td>
                                            <td><?php echo (int) (isset($sqlEvento['item_id']) ? $sqlEvento['item_id'] : 0); ?></td>
                                            <td class="debug-sql"><?php echo htmlspecialchars((string) (isset($sqlEvento['sql']) ? $sqlEvento['sql'] : ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>

                        <div class="debug-section">
                            <h3>Top blocos mais lentos do fluxo monitorado</h3>
                            <?php if (empty($apontamentoDebugSqlTempoPainel['blocos'])): ?>
                                <div class="debug-empty">Nenhum bloco monitorado encontrado ainda no log.</div>
                            <?php else: ?>
                                <table class="debug-table">
                                    <thead><tr><th>Tempo</th><th>Bloco</th><th>Request</th><th>OP</th><th>Item</th><th>Status</th></tr></thead>
                                    <tbody>
                                    <?php foreach ($apontamentoDebugSqlTempoPainel['blocos'] as $blocoEvento): ?>
                                        <tr>
                                            <td><?php echo number_format((float) (isset($blocoEvento['duracao_ms']) ? $blocoEvento['duracao_ms'] : 0), 3, ',', '.'); ?> ms</td>
                                            <td><?php echo htmlspecialchars((string) (isset($blocoEvento['bloco']) ? $blocoEvento['bloco'] : ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td class="debug-request"><?php echo htmlspecialchars((string) (isset($blocoEvento['request_id']) ? $blocoEvento['request_id'] : ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo (int) (isset($blocoEvento['op_id']) ? $blocoEvento['op_id'] : 0); ?></td>
                                            <td><?php echo (int) (isset($blocoEvento['item_id']) ? $blocoEvento['item_id'] : 0); ?></td>
                                            <td><?php echo !empty($blocoEvento['ok']) ? 'OK' : 'ERRO'; ?><?php echo !empty($blocoEvento['erro']) ? ': ' . htmlspecialchars((string) $blocoEvento['erro'], ENT_QUOTES, 'UTF-8') : ''; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>

                        <div class="debug-section">
                            <h3>Requests recentes mais pesadas nas tabelas alvo</h3>
                            <?php if (empty($apontamentoDebugSqlTempoPainel['requests'])): ?>
                                <div class="debug-empty">Nenhuma request encontrada no log ainda.</div>
                            <?php else: ?>
                                <table class="debug-table">
                                    <thead><tr><th>Request</th><th>Duracao request</th><th>Tempo SQL</th><th>Qtd SQL</th><th>OP</th><th>Item</th><th>Acao</th></tr></thead>
                                    <tbody>
                                    <?php foreach ($apontamentoDebugSqlTempoPainel['requests'] as $requestEvento): ?>
                                        <tr>
                                            <td class="debug-request"><?php echo htmlspecialchars((string) (isset($requestEvento['request_id']) ? $requestEvento['request_id'] : ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo isset($requestEvento['duracao_request_ms']) && $requestEvento['duracao_request_ms'] !== null ? number_format((float) $requestEvento['duracao_request_ms'], 3, ',', '.') . ' ms' : '-'; ?></td>
                                            <td><?php echo number_format((float) (isset($requestEvento['query_time_ms']) ? $requestEvento['query_time_ms'] : 0), 3, ',', '.'); ?> ms</td>
                                            <td><?php echo (int) (isset($requestEvento['query_count']) ? $requestEvento['query_count'] : 0); ?></td>
                                            <td><?php echo (int) (isset($requestEvento['op_id']) ? $requestEvento['op_id'] : 0); ?></td>
                                            <td><?php echo (int) (isset($requestEvento['item_id']) ? $requestEvento['item_id'] : 0); ?></td>
                                            <td><?php echo htmlspecialchars((string) (isset($requestEvento['acao']) ? $requestEvento['acao'] : ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (APONTAMENTO_DEBUG_IMPRESSORA): ?>
                <div class="flash" style="background:#fff7e8;border:1px solid #f3d19c;color:#7a4b00;">
                    Debug impressora: usuario=<?php echo htmlspecialchars((string) (isset($impressoraEtiquetaDebug['usuario_atual']) ? $impressoraEtiquetaDebug['usuario_atual'] : ''), ENT_QUOTES, 'UTF-8'); ?> |
                    empresa=<?php echo htmlspecialchars((string) (isset($impressoraEtiquetaDebug['empresa_atual']) ? $impressoraEtiquetaDebug['empresa_atual'] : ''), ENT_QUOTES, 'UTF-8'); ?> |
                    nome empresa=<?php echo htmlspecialchars((string) (isset($impressoraEtiquetaDebug['empresa_nome']) ? $impressoraEtiquetaDebug['empresa_nome'] : ''), ENT_QUOTES, 'UTF-8'); ?> |
                    origem parametro=<?php echo htmlspecialchars((string) (isset($impressoraEtiquetaDebug['origem_parametro']) ? $impressoraEtiquetaDebug['origem_parametro'] : ''), ENT_QUOTES, 'UTF-8'); ?> |
                    SMT_ativaUsuarioImpressora=<?php echo htmlspecialchars((string) (isset($impressoraEtiquetaDebug['usuario_impressora']) ? $impressoraEtiquetaDebug['usuario_impressora'] : ''), ENT_QUOTES, 'UTF-8'); ?> |
                    modo=<?php echo htmlspecialchars((string) (isset($impressoraEtiquetaDebug['modo_busca']) ? $impressoraEtiquetaDebug['modo_busca'] : ''), ENT_QUOTES, 'UTF-8'); ?> |
                    D175_Id=<?php echo htmlspecialchars((string) (isset($impressoraEtiquetaDebug['d175_id']) ? $impressoraEtiquetaDebug['d175_id'] : ''), ENT_QUOTES, 'UTF-8'); ?> |
                    impressora=<?php echo htmlspecialchars((string) (isset($impressoraEtiquetaDebug['nome']) ? $impressoraEtiquetaDebug['nome'] : ''), ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>
            <input class="produto-bar" type="text" value="<?php echo htmlspecialchars($produtoLinha, ENT_QUOTES, 'UTF-8'); ?>" readonly>

            <form id="apontamento-form" method="post" action="./apontar.php?op_id=<?php echo (int) $opId; ?>&item_id=<?php echo (int) $itemSelecionadoId; ?>">
                <input type="hidden" name="acao" value="<?php echo !empty($registroEdicao) ? 'atualizar_apontamento' : 'lancar_apontamento'; ?>">
                <?php if (!empty($registroEdicao)): ?><input type="hidden" name="t257_id" value="<?php echo (int) $registroEdicao['T257_Id']; ?>"><?php endif; ?>
                <input type="hidden" name="item_id" value="<?php echo (int) $itemSelecionadoId; ?>">
                <input type="hidden" name="op_id" value="<?php echo (int) $opId; ?>">
                <div class="form-line">
                    <div class="field-inline field-inline-qtde"><label for="qtde_apontar">Qtde Apontar:</label><input id="qtde_apontar" name="qtde_apontar" type="text" value="<?php echo !empty($registroEdicao) ? htmlspecialchars(apontamentoFormatar($registroEdicao['T257_Quantidade']), ENT_QUOTES, 'UTF-8') : ''; ?>" required></div>
                    <div class="field-inline"><label for="peso_pallet">Peso Pallet:</label><input id="peso_pallet" name="peso_pallet" type="text" value="<?php echo !empty($registroEdicao) ? htmlspecialchars(apontamentoFormatar($registroEdicao['T257A_Peso']), ENT_QUOTES, 'UTF-8') : ''; ?>"></div>
                    <div class="field-inline"><label for="pedido_id">Pedido Cliente:</label><input id="pedido_id" name="pedido_id" type="text" value="<?php echo !empty($registroEdicao) ? (int) $registroEdicao['T257A_T005_Id'] : (!empty($itemSelecionado['T005_Id']) ? (int) $itemSelecionado['T005_Id'] : ''); ?>"></div>
                    <div class="field-inline"><label for="qtde_chapa">Qtde Chapa:</label><input id="qtde_chapa" name="qtde_chapa" type="text" value="<?php echo !empty($registroEdicao) ? htmlspecialchars(apontamentoFormatar($registroEdicao['T257A_Qtde_Chapa']), ENT_QUOTES, 'UTF-8') : ''; ?>"></div>
                    <div class="field-inline field-inline-sm"><label for="quantidade_lancamentos">Qtde Lanc.:</label><input id="quantidade_lancamentos" name="quantidade_lancamentos" type="number" min="1" max="9999" step="1" value="1" <?php echo !empty($registroEdicao) ? 'readonly' : ''; ?>></div>
                    <div class="field-inline field-inline-lg"><label for="observacao">Observa&ccedil;&atilde;o:</label><input id="observacao" name="observacao" type="text" value="<?php echo !empty($registroEdicao) ? htmlspecialchars($registroEdicao['T257A_Observacao'], ENT_QUOTES, 'UTF-8') : ''; ?>"></div>
                    <div class="form-actions">
                        <button class="button" type="submit"><?php echo !empty($registroEdicao) ? 'Salvar' : 'Enviar'; ?></button>
                        <?php if (!empty($registroEdicao)): ?><a class="button js-loading-link" href="./apontar.php?op_id=<?php echo (int) $opId; ?>&item_id=<?php echo (int) $itemSelecionadoId; ?>">Cancelar</a><?php endif; ?>
                    </div>
                </div>
            </form>

            <div class="toolbar"><button class="button print" type="button" onclick="apontamentoImprimirSelecionados()">Imprimir</button></div>

            <div class="grid-wrap">
                <div class="table-scroll">
                    <table>
                        <thead><tr><th>#</th><th><input type="checkbox" onclick="apontamentoSelecionarTodos(this)"></th><th>Editar</th><th>Quantidade</th><th>Peso Pallet</th><th>Qtde Chapa</th><th>Emissao</th><th>Usuario</th><th>Lote</th><th>Pedido</th><th>Cliente</th><th>Estoque</th><th>Observacao</th></tr></thead>
                        <tbody>
                            <?php if (empty($historicoApontamentos)): ?><tr><td class="empty" colspan="13">Ainda n&atilde;o existem apontamentos para este item.</td></tr><?php else: ?>
                                <?php foreach ($historicoApontamentos as $indice => $historico): ?>
                                    <tr>
                                        <td class="center"><?php echo $indice + 1; ?></td>
                                        <td class="center"><input class="js-print-item" type="checkbox" value="<?php echo (int) $historico['T257_Id']; ?>"></td>
                                        <td class="center"><?php if ((int) $historico['T001_Id'] > 0): ?><span class="edit-pill">-</span><?php else: ?><a class="edit-pill js-loading-link" href="./apontar.php?op_id=<?php echo (int) $opId; ?>&item_id=<?php echo (int) $itemSelecionadoId; ?>&edit_id=<?php echo (int) $historico['T257_Id']; ?>">Editar</a><?php endif; ?></td>
                                        <td class="right"><?php echo apontamentoFormatar($historico['T257_Quantidade']); ?></td>
                                        <td class="right"><?php echo apontamentoFormatar($historico['T257A_Peso']); ?></td>
                                        <td class="right"><?php echo apontamentoFormatar($historico['T257A_Qtde_Chapa']); ?></td>
                                        <td class="center"><?php echo htmlspecialchars(gCorrigeData($historico['T257_Data_Producao']), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($historico['C007_Primeiro_Nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($historico['T257A_Lote'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="center"><?php echo !empty($historico['T257A_T005_Id']) ? (int) $historico['T257A_T005_Id'] : 0; ?></td>
                                        <td><?php echo !empty($historico['D024_Nome_Empresa']) ? htmlspecialchars($historico['D024_Nome_Empresa'], ENT_QUOTES, 'UTF-8') : '-'; ?></td>
                                        <td class="center"><?php echo (int) $historico['T001_Id'] > 0 ? 'SIM' : 'N&Atilde;O'; ?></td>
                                        <td><?php echo htmlspecialchars($historico['T257A_Observacao'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="page"><div class="window"><div class="window-body"><div class="empty">Nenhum item encontrado para esta OP.</div></div></div></div>
<?php endif; ?>
<script src="../../static/js/smt-print.js"></script>
<script src="../../static/js/print.js"></script>
<script>
function apontamentoMostrarLoading(texto) {
    var overlay = document.getElementById('apontamento-loading');
    var label = document.getElementById('apontamento-loading-text');
    if (!overlay) {
        return;
    }
    if (label && texto) {
        label.textContent = texto;
    }
    overlay.classList.add('is-visible');
    overlay.setAttribute('aria-hidden', 'false');
}

function apontamentoEsconderLoading() {
    var overlay = document.getElementById('apontamento-loading');
    if (!overlay) {
        return;
    }
    overlay.classList.remove('is-visible');
    overlay.setAttribute('aria-hidden', 'true');
}

function apontamentoSelecionarTodos(origem) {
    var itens = document.querySelectorAll('.js-print-item');
    for (var i = 0; i < itens.length; i += 1) {
        itens[i].checked = origem.checked;
    }
}

var apontamentoImpressoraEtiqueta = <?php echo json_encode($impressoraEtiqueta, JSON_UNESCAPED_UNICODE); ?>;
var apontamentoEtiquetaTelaBaseUrl = <?php echo json_encode(apontamentoMontarUrlLegado('/sistema/personalizacao/tela/d99135ae02d6770a33859939030676b3/', array('ajax' => 'true')), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
var apontamentoEtiquetaPdfBaseUrl = <?php echo json_encode(apontamentoMontarUrlLegado('/sistema/personalizacaoLowCode/grid_func-ajax/telaGerarPdf/', array('ajax' => 'true', 'acaoId' => 'd99135ae02d6770a33859939030676b3')), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

async function apontamentoPrepararImpressaoDireta() {
    if (typeof qz === 'undefined') {
        throw new Error('Biblioteca de impressao direta nao carregada.');
    }
    if (typeof authenticate !== 'function' || typeof connect !== 'function' || typeof launchQZ !== 'function') {
        throw new Error('Funcoes de conexao com a impressora nao foram carregadas.');
    }

    await authenticate();
    await connect({ retries: 2, delay: 1 });
    await launchQZ();

    if (!qz.websocket || typeof qz.websocket.isActive !== 'function' || !qz.websocket.isActive()) {
        throw new Error('QZ Tray/SMT Print nao conectado.');
    }

    if (apontamentoImpressoraEtiqueta) {
        var impressoraEncontrada = await qz.printers.find(apontamentoImpressoraEtiqueta);
        if (!impressoraEncontrada) {
            throw new Error('Impressora configurada nao encontrada: ' + apontamentoImpressoraEtiqueta);
        }
        return impressoraEncontrada;
    }

    var impressoraPadrao = await qz.printers.getDefault();
    if (!impressoraPadrao) {
        throw new Error('Nenhuma impressora padrao encontrada para impressao direta.');
    }

    return impressoraPadrao;
}

async function apontamentoImprimirDireto(pdfUrl) {
    var impressora = await apontamentoPrepararImpressaoDireta();
    var config = qz.configs.create(impressora);
    var data = [{
        type: 'pixel',
        format: 'pdf',
        flavor: 'file',
        data: pdfUrl
    }];

    await qz.print(config, data);
    return true;
}

function apontamentoObterPrintSMT() {
    var contextos = [window];
    try { if (window.parent && contextos.indexOf(window.parent) < 0) { contextos.push(window.parent); } } catch (e) {}
    try { if (window.top && contextos.indexOf(window.top) < 0) { contextos.push(window.top); } } catch (e) {}
    try { if (window.opener && contextos.indexOf(window.opener) < 0) { contextos.push(window.opener); } } catch (e) {}

    for (var i = 0; i < contextos.length; i += 1) {
        try {
            if (contextos[i] && typeof contextos[i].printSMT === 'function') {
                return contextos[i].printSMT.bind(contextos[i]);
            }
        } catch (e) {}
    }

    return null;
}

function apontamentoAbrirEtiquetaPorId(id) {
    var telaLink = apontamentoEtiquetaTelaBaseUrl + '&T257_Id=' + encodeURIComponent(id);
    var pdfLink = apontamentoEtiquetaPdfBaseUrl + '&link=' + encodeURIComponent(telaLink);

    return fetch(pdfLink, { credentials: 'same-origin' })
        .then(function(resposta) { return resposta.text(); })
        .then(function(texto) {
            var payload = null;
            var textoLimpo = (texto || '').trim();

            try {
                payload = JSON.parse(textoLimpo);
            } catch (erroJson) {
                var matchNomeado = textoLimpo.match(/^[^(]+\((.*)\);?$/s);
                if (matchNomeado) {
                    try {
                        payload = JSON.parse(matchNomeado[1]);
                    } catch (erroJsonp) {
                        payload = null;
                    }
                }

                if (!payload) {
                    var matchDireto = textoLimpo.match(/^\((\{[\s\S]*\})\);?$/s);
                    if (matchDireto) {
                        try {
                            payload = JSON.parse(matchDireto[1]);
                        } catch (erroDireto) {
                            payload = null;
                        }
                    }
                }
            }

            if (payload && payload.data) {
                var funcaoPrint = apontamentoObterPrintSMT();
                if (funcaoPrint) {
                    return Promise.resolve(funcaoPrint(payload.data, apontamentoImpressoraEtiqueta || ''))
                        .catch(function(erroImpressao) {
                            var mensagem = erroImpressao && erroImpressao.message
                                ? erroImpressao.message
                                : 'Nao foi possivel iniciar a impressao direta.';
                            alert(mensagem + ' A etiqueta sera aberta em PDF.');
                            window.open(payload.data, '_blank');
                        });
                }

                alert('Funcao printSMT nao esta disponivel nesta tela. A etiqueta sera aberta em PDF.');
                window.open(payload.data, '_blank');
                return Promise.resolve();
            }

            var amostra = textoLimpo.replace(/\s+/g, ' ').trim().substring(0, 220);
            alert('Nao foi possivel obter o PDF da etiqueta para impressao direta. Resposta recebida: ' + amostra);
            window.open(telaLink, '_blank');
            return Promise.resolve();
        })
        .catch(function(erroFetch) {
            var mensagem = erroFetch && erroFetch.message
                ? erroFetch.message
                : 'Falha ao consultar o gerador da etiqueta.';
            alert(mensagem + ' A etiqueta sera aberta na tela.');
            window.open(telaLink, '_blank');
            return Promise.resolve();
        });
}

function apontamentoImprimirSelecionados() {
    apontamentoMostrarLoading('Preparando etiquetas...');
    var itens = document.querySelectorAll('.js-print-item:checked');
    var ids = [];
    for (var i = 0; i < itens.length; i += 1) {
        ids.push(itens[i].value);
    }

    if (!ids.length) {
        apontamentoEsconderLoading();
        alert('Selecione ao menos um apontamento para imprimir.');
        return;
    }

    var sequencia = Promise.resolve();
    ids.forEach(function(id, index) {
        sequencia = sequencia.then(function() {
            if (index > 0) {
                apontamentoMostrarLoading('Preparando etiqueta ' + (index + 1) + ' de ' + ids.length + '...');
            }
            return apontamentoAbrirEtiquetaPorId(id).then(function() {
                return new Promise(function(resolve) {
                    window.setTimeout(resolve, 250);
                });
            });
        });
    });

    sequencia.finally(function() {
        window.setTimeout(apontamentoEsconderLoading, 800);
    });
}document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('apontamento-form');
    if (form) {
        form.addEventListener('submit', function () {
            var acao = form.querySelector('input[name="acao"]');
            var texto = acao && acao.value === 'atualizar_apontamento'
                ? 'Salvando apontamento...'
                : 'Enviando apontamento...';
            apontamentoMostrarLoading(texto);
        });
    }

    var links = document.querySelectorAll('.js-loading-link');
    for (var i = 0; i < links.length; i += 1) {
        links[i].addEventListener('click', function () {
            var texto = this.classList.contains('edit-pill') ? 'Abrindo apontamento para edicao...' : 'Carregando tela...';
            apontamentoMostrarLoading(texto);
        });
    }
});
</script>
</body>
</html>







