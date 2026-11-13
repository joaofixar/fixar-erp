<?php
namespace hardness;
if (realpath(isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '') === __FILE__) { http_response_code(404); exit; }
require_once dirname(__DIR__, 3) . '/portal-representante/core.php';
require_once dirname(__DIR__, 3) . '/portal-representante/dados.php';

function cad003PortalPodeGerenciar($contexto, $sessao)
{
    $usuario = isset($contexto['usuarioAtual']) ? (int) $contexto['usuarioAtual'] : 0;
    $empresa = isset($contexto['empresaAtual']) ? (int) $contexto['empresaAtual'] : 0;
    return $usuario > 0 && $empresa > 0 && isset($sessao['Usuario_Atual'], $sessao['Empresa_Atual'])
        && (int) $sessao['Usuario_Atual'] === $usuario && (int) $sessao['Empresa_Atual'] === $empresa
        && ($usuario === 63 || !empty($contexto['isRoot']) || (isset($contexto['nivelAlcada']) && (int) $contexto['nivelAlcada'] === 4));
}

function cad003PortalCsrf($conf, $empresa)
{
    $chave = portalRepresentanteTenant($conf) . ':' . (int) $empresa;
    if (empty($_SESSION['cad003PortalCsrf'][$chave])) {
        // O framework fecha a sessão antes de renderizar o grid AJAX.
        $sessaoFechada = session_status() !== PHP_SESSION_ACTIVE;
        if ($sessaoFechada) session_start();
        if (empty($_SESSION['cad003PortalCsrf'][$chave])) $_SESSION['cad003PortalCsrf'][$chave] = bin2hex(random_bytes(32));
        if ($sessaoFechada) session_write_close();
    }
    return $_SESSION['cad003PortalCsrf'][$chave];
}

function cad003PortalRepresentanteColuna($array)
{
    global $g, $confUsuario;
    if (!cad003PortalPodeGerenciar($g, $_SESSION)) return '';
    $id = isset($array['todosCampos']['D021_Id']) ? (int) $array['todosCampos']['D021_Id'] : 0;
    if ($id < 1) return '';
    $csrf = htmlspecialchars(cad003PortalCsrf($confUsuario, $g['empresaAtual']), ENT_QUOTES, 'UTF-8');
    return '<span class="cad003-portal-acesso"><button type="button" data-rep-acao="liberar" data-rep-id="' . $id . '" data-rep-csrf="' . $csrf . '"><span>Disponibilizar link</span></button> '
        . '<button type="button" data-rep-acao="revogar" data-rep-id="' . $id . '" data-rep-csrf="' . $csrf . '" title="Invalidar o acesso externo deste representante"><span>Revogar acesso</span></button><small class="cad003-portal-status" hidden></small></span>';
}

function cad003PortalProcessar($contexto, $conf, $acao, $entrada, $metodo)
{
    if (!cad003PortalPodeGerenciar($contexto, $_SESSION)) throw new RepresentantePortalErro('Somente ROOT ou diretor pode disponibilizar ou revogar o acesso externo.', 403);
    if ($metodo !== 'POST') throw new RepresentantePortalErro('Método não permitido.', 405);
    $token = isset($entrada['csrf']) ? $entrada['csrf'] : '';
    if (!is_string($token) || !hash_equals(cad003PortalCsrf($conf, $contexto['empresaAtual']), $token)) throw new RepresentantePortalErro('O formulário expirou. Atualize a lista de representantes.', 403);
    return cad003PortalExecutar($contexto, $conf, $acao, $entrada);
}

// Chamado somente após a validação da permissão e do CSRF no ERP ou no dashboard.
function cad003PortalExecutar($contexto, $conf, $acao, $entrada)
{
    $id = isset($entrada['representante']) ? $entrada['representante'] : '';
    if (!is_string($id) || !preg_match('/^[1-9][0-9]{0,9}$/D', $id)) throw new RepresentantePortalErro('Representante inválido.');
    if ($acao === 'portalRepresentanteRevogar') return portalRepresentanteRevogar($conf, $contexto['empresaAtual'], (int) $id);
    if ($acao !== 'portalRepresentanteLiberar') throw new RepresentantePortalErro('Operação inválida.', 404);
    portalRepresentanteUrl($conf, str_repeat('0', 48));
    $conexao = portalRepresentanteConectar($conf);
    try { $registro = portalRepresentanteLerCadastro($conexao, (int) $id); } finally { portalRepresentanteFecharConexao($conexao); }
    return portalRepresentanteLiberar($conf, $contexto['empresaAtual'], $registro, $contexto['usuarioAtual']);
}
