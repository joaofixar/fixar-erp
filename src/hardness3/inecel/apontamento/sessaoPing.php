<?php
namespace hardness;

/**
 * Keep-alive da sessao do apontamento (chamado por js/sessaoKeepAlive.js).
 * Mantem o arquivo de sessao vivo no servidor, mas nao conta como atividade
 * do usuario, entao o logout por inatividade (8h) continua valendo.
 */

require_once(__DIR__ . '/sessaoApontamento.php');

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');
header("X-Robots-Tag: noindex, nofollow", true);

apontamentoIniciarSessao();

$ok = apontamentoSessaoValida(false);
if (!$ok && !empty($_SESSION['Usuario_Atual'])) {
    $_SESSION = array();
    session_destroy();
} else {
    session_write_close();
}

echo json_encode(array('ok' => $ok));
