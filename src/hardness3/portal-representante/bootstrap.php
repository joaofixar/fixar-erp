<?php
namespace hardness;
if (realpath(isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '') === __FILE__) { http_response_code(404); exit; }
require_once __DIR__ . '/core.php';
require_once __DIR__ . '/dados.php';
require_once __DIR__ . '/indicadores.php';
require_once __DIR__ . '/demonstracao.php';
require_once __DIR__ . '/regioes.php';
require_once __DIR__ . '/gestor.php';
$portalModoGestor = array_key_exists('gestor', $_GET) || isset($_SERVER['HTTP_X_PORTAL_GESTOR']);
$portalGestorToken = isset($_SERVER['HTTP_X_PORTAL_GESTOR']) ? $_SERVER['HTTP_X_PORTAL_GESTOR'] : (isset($_GET['gestor']) ? $_GET['gestor'] : '');
ini_set('display_errors', '0');
ini_set('session.use_strict_mode', '1');
header('Cache-Control: no-store, private');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; frame-ancestors " . ($portalModoGestor ? "'self'" : "'none'") . "; base-uri 'none'; form-action 'self'");
if (!$portalModoGestor) {
    session_name('HARDNESS_REPRESENTANTE');
    $portalCookiePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/') . '/';
    $portalCookieSeguro = (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https');
    session_set_cookie_params(array('lifetime' => 0, 'path' => $portalCookiePath, 'secure' => $portalCookieSeguro, 'httponly' => true, 'samesite' => 'Lax'));
}
require_once __DIR__ . '/../bibliotecas/confUsuario.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
date_default_timezone_set('America/Sao_Paulo');
$portalGestorContexto = $portalModoGestor ? portalRepresentanteGestorContexto($confUsuario, $_SESSION, $portalGestorToken) : false;
if (!$portalModoGestor && empty($_SESSION['portalCsrf'])) $_SESSION['portalCsrf'] = bin2hex(random_bytes(32));
$portalCsrfAtual = $portalModoGestor ? ($portalGestorContexto ? $portalGestorContexto['csrf'] : '') : $_SESSION['portalCsrf'];

function portalRepresentanteResponder($codigo, $dados)
{
    http_response_code($codigo); header('Content-Type: application/json; charset=utf-8');
    echo json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); exit;
}

function portalRepresentanteCsrf()
{
    global $portalCsrfAtual;
    $token = isset($_SERVER['HTTP_X_CSRF_TOKEN']) ? $_SERVER['HTTP_X_CSRF_TOKEN'] : '';
    if (!is_string($token) || $portalCsrfAtual === '' || !hash_equals($portalCsrfAtual, $token)) throw new RepresentantePortalErro('O formulário expirou. Recarregue a página.', 403);
}
