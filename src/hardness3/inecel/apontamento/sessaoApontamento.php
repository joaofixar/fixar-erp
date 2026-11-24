<?php
namespace hardness;

/**
 * Controle de sessao das telas de apontamento.
 *
 * - Logout por inatividade: 8 horas (usa $_SESSION['tempoDeslogarInatividade'],
 *   gravado no login, e $_SESSION['Data_Hora_Login'] como ultima atividade,
 *   mesmo padrao do verificaUsuarioAtivo() do framework).
 * - Enquanto a pagina estiver aberta, o js/sessaoKeepAlive.js chama
 *   sessaoPing.php periodicamente para o arquivo de sessao nao ser apagado
 *   pelo garbage collector do PHP (padrao do servidor ~24 min). O ping NAO
 *   conta como atividade, entao o limite de 8h sem uso continua valendo.
 */

if (!defined('APONTAMENTO_TEMPO_INATIVIDADE')) {
    define('APONTAMENTO_TEMPO_INATIVIDADE', 28800); // 8 horas
}

function apontamentoIniciarSessao()
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    if ((int) ini_get('session.gc_maxlifetime') < APONTAMENTO_TEMPO_INATIVIDADE) {
        @ini_set('session.gc_maxlifetime', (string) APONTAMENTO_TEMPO_INATIVIDADE);
    }
    session_start();
}

function apontamentoTempoInatividade(): int
{
    if (!empty($_SESSION['tempoDeslogarInatividade']) && (int) $_SESSION['tempoDeslogarInatividade'] > 0) {
        return (int) $_SESSION['tempoDeslogarInatividade'];
    }
    return APONTAMENTO_TEMPO_INATIVIDADE;
}

/**
 * Retorna true se o usuario esta logado e dentro do limite de inatividade.
 * Com $registrarAtividade = true, renova o contador de inatividade.
 */
function apontamentoSessaoValida(bool $registrarAtividade = true): bool
{
    if (empty($_SESSION['Usuario_Atual'])) {
        return false;
    }

    $agora = time();
    $ultimaAtividade = isset($_SESSION['Data_Hora_Login']) ? (int) $_SESSION['Data_Hora_Login'] : $agora;

    if (($agora - $ultimaAtividade) > apontamentoTempoInatividade()) {
        return false;
    }

    if ($registrarAtividade) {
        $_SESSION['Data_Hora_Login'] = $agora;
    }
    return true;
}

/**
 * Usado no topo das paginas: inicia a sessao, trata ?logout=1 e
 * redireciona para o login se nao estiver logado ou se passou do limite.
 */
function apontamentoExigirLogin()
{
    apontamentoIniciarSessao();

    if (isset($_GET['logout'])) {
        session_destroy();
        header('Location: ./index.php');
        exit;
    }

    if (!apontamentoSessaoValida(true)) {
        $_SESSION = array();
        session_destroy();
        header('Location: ./index.php');
        exit;
    }
}
