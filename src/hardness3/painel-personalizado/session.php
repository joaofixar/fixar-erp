<?php
namespace hardness;

require_once __DIR__ . '/../dashboard-personalizado/session.php';

function painelPersonalizadoContexto()
{
    global $g;
    $flagVendedor = isset($g['C007']['C007_Flag_Vendedor'])
        ? (string) $g['C007']['C007_Flag_Vendedor']
        : (isset($g['c007']['C007_Flag_Vendedor']) ? (string) $g['c007']['C007_Flag_Vendedor'] : '');
    return array(
        'usuario' => isset($g['usuarioAtual']) ? (int) $g['usuarioAtual'] : 0,
        'empresa' => isset($g['empresaAtual']) ? (int) $g['empresaAtual'] : 0,
        'perfil' => isset($g['perfilUsuario']) ? (int) $g['perfilUsuario'] : 0,
        'root' => !empty($g['isRoot']) || (isset($g['usuarioAtual']) && (int) $g['usuarioAtual'] === 63),
        'diretor' => (isset($g['nivelAlcada']) && (int) $g['nivelAlcada'] === 4) || $flagVendedor === 'D',
    );
}

function painelPersonalizadoCredencial($config)
{
    // Reutiliza o formato assinado do dashboard, isolando a autorização do PAINEL.
    if (!empty($config['dbPass'])) $config['dbPass'] = hash_hmac('sha256', 'hardness-painel-v1', $config['dbPass']);
    return $config;
}

function painelPersonalizadoLerAutorizacao($config)
{
    return dashboardPersonalizadoValidarAutorizacao(isset($_COOKIE['HARDNESS_PAINEL_SESSION']) ? $_COOKIE['HARDNESS_PAINEL_SESSION'] : '', painelPersonalizadoCredencial($config));
}

function painelPersonalizadoDiretorioTokens()
{
    return rtrim(sys_get_temp_dir(), '/\\') . '/hardness-painel-sessions';
}

function painelPersonalizadoCriarTokenSessao()
{
    global $confUsuario;
    $autorizacao = dashboardPersonalizadoCriarAutorizacao(painelPersonalizadoContexto(), painelPersonalizadoCredencial($confUsuario));
    if ($autorizacao === false) return false;
    $diretorio = painelPersonalizadoDiretorioTokens();
    if (!is_dir($diretorio) && !@mkdir($diretorio, 0700, true)) return false;
    foreach ((array) glob($diretorio . '/*') as $arquivo) {
        if (@filemtime($arquivo) < time() - 43200) @unlink($arquivo);
    }
    try {
        $token = bin2hex(random_bytes(32));
    } catch (\Exception $e) {
        return false;
    }
    $arquivo = $diretorio . '/' . hash('sha256', $token);
    if (@file_put_contents($arquivo, $autorizacao, LOCK_EX) === false) return false;
    @chmod($arquivo, 0600);
    return $token;
}

function painelPersonalizadoAutorizarToken($token, $config)
{
    if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/', $token)) return false;
    $arquivo = painelPersonalizadoDiretorioTokens() . '/' . hash('sha256', $token);
    if (!is_readable($arquivo)) return false;
    $autorizacao = file_get_contents($arquivo);
    if (dashboardPersonalizadoValidarAutorizacao($autorizacao, painelPersonalizadoCredencial($config)) === false) return false;
    // Mantém a validade original (12h), inclusive ao reabrir o mesmo atalho.
    return setcookie('HARDNESS_PAINEL_SESSION', $autorizacao, array(
        'expires' => 0,
        'path' => '/hardness3/painel.php',
        'secure' => dashboardPersonalizadoCookieSeguro(),
        'httponly' => true,
        'samesite' => 'Strict',
    ));
}
