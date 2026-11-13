<?php
namespace hardness;

function dashboardPersonalizadoNomeCookieSessao()
{
    return 'HARDNESS_DASHBOARD_SESSION';
}

function dashboardPersonalizadoCookieSeguro()
{
    return (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off')
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https');
}

function dashboardPersonalizadoBase64UrlCodificar($valor)
{
    return rtrim(strtr(base64_encode($valor), '+/', '-_'), '=');
}

function dashboardPersonalizadoBase64UrlDecodificar($valor)
{
    if (!is_string($valor) || !preg_match('/^[a-zA-Z0-9_-]+$/', $valor)) {
        return false;
    }

    $resto = strlen($valor) % 4;
    if ($resto > 0) {
        $valor .= str_repeat('=', 4 - $resto);
    }

    return base64_decode(strtr($valor, '-_', '+/'), true);
}

function dashboardPersonalizadoChaveAutorizacao($confUsuario)
{
    if (!is_array($confUsuario)
        || empty($confUsuario['dbHost'])
        || empty($confUsuario['dbDatabase'])
        || empty($confUsuario['dbUser'])
        || empty($confUsuario['dbPass'])) {
        return false;
    }

    $material = implode("\0", array(
        $confUsuario['dbHost'],
        $confUsuario['dbDatabase'],
        $confUsuario['dbUser'],
        $confUsuario['dbPass'],
    ));

    return hash('sha256', "hardness-dashboard-personalizado-v1\0" . $material, true);
}

function dashboardPersonalizadoCriarAutorizacao($contexto, $confUsuario)
{
    $chave = dashboardPersonalizadoChaveAutorizacao($confUsuario);
    $usuario = isset($contexto['usuario']) ? (int) $contexto['usuario'] : 0;
    $empresa = isset($contexto['empresa']) ? (int) $contexto['empresa'] : 0;
    $perfil = isset($contexto['perfil']) ? (int) $contexto['perfil'] : 0;
    $root = !empty($contexto['root']);
    $diretor = !empty($contexto['diretor']);
    if ($chave === false || $usuario <= 0 || $empresa <= 0) {
        return false;
    }

    $dados = json_encode(array(
        'usuario' => $usuario,
        'empresa' => $empresa,
        'perfil' => $perfil,
        'root' => $root,
        'diretor' => $diretor,
        'expira' => time() + 43200,
        'navegador' => hash('sha256', isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : ''),
    ));
    if ($dados === false) {
        return false;
    }

    $conteudo = dashboardPersonalizadoBase64UrlCodificar($dados);
    $assinatura = hash_hmac('sha256', $conteudo, $chave);

    return $conteudo . '.' . $assinatura;
}

function dashboardPersonalizadoValidarAutorizacao($autorizacao, $confUsuario)
{
    $chave = dashboardPersonalizadoChaveAutorizacao($confUsuario);
    if ($chave === false
        || !is_string($autorizacao)
        || !preg_match('/^([a-zA-Z0-9_-]+)\.([a-f0-9]{64})$/', $autorizacao, $partes)) {
        return false;
    }

    $assinaturaEsperada = hash_hmac('sha256', $partes[1], $chave);
    if (!hash_equals($assinaturaEsperada, $partes[2])) {
        return false;
    }

    $json = dashboardPersonalizadoBase64UrlDecodificar($partes[1]);
    $dados = $json === false ? false : json_decode($json, true);
    $navegador = hash('sha256', isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
    if (!is_array($dados)
        || empty($dados['usuario'])
        || (int) $dados['usuario'] <= 0
        || empty($dados['empresa'])
        || (int) $dados['empresa'] <= 0
        || empty($dados['expira'])
        || (int) $dados['expira'] < time()
        || empty($dados['navegador'])
        || !hash_equals($dados['navegador'], $navegador)) {
        return false;
    }

    return array(
        'usuario' => (int) $dados['usuario'],
        'empresa' => (int) $dados['empresa'],
        'perfil' => isset($dados['perfil']) ? (int) $dados['perfil'] : 0,
        'root' => !empty($dados['root']),
        'diretor' => !empty($dados['diretor']),
    );
}

function dashboardPersonalizadoLerAutorizacao($confUsuario)
{
    $nomeCookie = dashboardPersonalizadoNomeCookieSessao();

    return dashboardPersonalizadoValidarAutorizacao(
        isset($_COOKIE[$nomeCookie]) ? $_COOKIE[$nomeCookie] : '',
        $confUsuario
    );
}

function dashboardPersonalizadoGravarAutorizacao($contexto, $confUsuario)
{
    if (headers_sent()) {
        return false;
    }

    $autorizacao = dashboardPersonalizadoCriarAutorizacao($contexto, $confUsuario);
    if ($autorizacao === false) {
        return false;
    }

    return setcookie(
        dashboardPersonalizadoNomeCookieSessao(),
        $autorizacao,
        0,
        '/hardness3/dashboard-personalizado/',
        '',
        dashboardPersonalizadoCookieSeguro(),
        true
    );
}

function dashboardPersonalizadoDiretorioTokens()
{
    return rtrim(sys_get_temp_dir(), '/\\') . '/hardness-dashboard-sessions';
}

function dashboardPersonalizadoCriarTokenSessao()
{
    global $g;

    $usuario = isset($_SESSION['Usuario_Atual'])
        ? (int) $_SESSION['Usuario_Atual']
        : (isset($g['usuarioAtual']) ? (int) $g['usuarioAtual'] : 0);
    $empresa = isset($_SESSION['Empresa_Atual'])
        ? (int) $_SESSION['Empresa_Atual']
        : (isset($g['empresaAtual']) ? (int) $g['empresaAtual'] : 0);
    $perfil = isset($_SESSION['Perfil_Usuario'])
        ? (int) $_SESSION['Perfil_Usuario']
        : (isset($g['perfilUsuario']) ? (int) $g['perfilUsuario'] : 0);
    $root = !empty($g['isRoot']) || $usuario === 63;
    $flagVendedor = isset($g['C007']['C007_Flag_Vendedor'])
        ? (string) $g['C007']['C007_Flag_Vendedor']
        : (isset($g['c007']['C007_Flag_Vendedor']) ? (string) $g['c007']['C007_Flag_Vendedor'] : '');
    $diretor = (isset($g['nivelAlcada']) && (int) $g['nivelAlcada'] === 4) || $flagVendedor === 'D';

    if ($usuario <= 0 || $empresa <= 0) {
        return false;
    }

    $diretorio = dashboardPersonalizadoDiretorioTokens();
    if (!is_dir($diretorio) && !@mkdir($diretorio, 0700, true)) {
        return false;
    }

    foreach ((array) glob($diretorio . '/*.json') as $arquivoExpirado) {
        if (@filemtime($arquivoExpirado) < time() - 43200) {
            @unlink($arquivoExpirado);
        }
    }

    try {
        $token = bin2hex(random_bytes(32));
    } catch (\Exception $excecao) {
        return false;
    }

    $arquivo = $diretorio . '/' . hash('sha256', $token) . '.json';
    $dados = json_encode(array(
        'usuario' => $usuario,
        'empresa' => $empresa,
        'perfil' => $perfil,
        'root' => $root,
        'diretor' => $diretor,
        'expira' => time() + 43200,
        'navegador' => hash('sha256', isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : ''),
    ));
    if (@file_put_contents($arquivo, $dados, LOCK_EX) === false) {
        return false;
    }
    @chmod($arquivo, 0600);

    return $token;
}

function dashboardPersonalizadoConsumirTokenSessao($token)
{
    if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/', $token)) {
        return false;
    }

    $arquivo = dashboardPersonalizadoDiretorioTokens() . '/' . hash('sha256', $token) . '.json';
    if (!is_file($arquivo) || !is_readable($arquivo)) {
        return false;
    }

    $dados = json_decode(file_get_contents($arquivo), true);
    @unlink($arquivo);
    $navegador = hash('sha256', isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
    if (!is_array($dados)
        || empty($dados['usuario'])
        || (int) $dados['usuario'] <= 0
        || empty($dados['empresa'])
        || (int) $dados['empresa'] <= 0
        || empty($dados['expira'])
        || (int) $dados['expira'] < time()
        || !isset($dados['navegador'])
        || !hash_equals($dados['navegador'], $navegador)) {
        return false;
    }

    return array(
        'usuario' => (int) $dados['usuario'],
        'empresa' => (int) $dados['empresa'],
        'perfil' => isset($dados['perfil']) ? (int) $dados['perfil'] : 0,
        'root' => !empty($dados['root']),
        'diretor' => !empty($dados['diretor']),
    );
}
