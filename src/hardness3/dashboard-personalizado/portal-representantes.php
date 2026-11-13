<?php
namespace hardness;
if (realpath(isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '') === __FILE__) { http_response_code(404); exit; }
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/access.php';

// O motor pode ser atualizado antes do portal. Carrega as dependências somente
// ao abrir esta visão, sem interromper os demais indicadores da instalação.
function dashboardPortalRepresentantesDisponivel()
{
    $raiz = dirname(__DIR__);
    foreach (array('/prog/cad/cad003/portal-acesso.php', '/portal-representante/core.php',
        '/portal-representante/dados.php', '/portal-representante/email.php', '/portal-representante/senha.php') as $arquivo) {
        if (!is_file($raiz . $arquivo)) return false;
    }
    require_once $raiz . '/prog/cad/cad003/portal-acesso.php';
    return true;
}

function dashboardPortalRepresentantesHabilitado($indicador)
{
    return isset($indicador['portalRepresentantes']['habilitado']) && $indicador['portalRepresentantes']['habilitado'] === true;
}

function dashboardPortalRepresentantesContexto($autorizacao)
{
    return array('usuarioAtual' => (int) $autorizacao['usuario'], 'empresaAtual' => (int) $autorizacao['empresa'],
        'isRoot' => !empty($autorizacao['root']), 'nivelAlcada' => !empty($autorizacao['diretor']) ? 4 : 0);
}

function dashboardPortalRepresentantesPodeGerenciar($autorizacao, $sessao, $indicadorId)
{
    if (!is_array($autorizacao) || empty($autorizacao['usuario']) || empty($autorizacao['empresa'])
        || !isset($sessao['Usuario_Atual'], $sessao['Empresa_Atual'])
        || (int) $sessao['Usuario_Atual'] !== (int) $autorizacao['usuario'] || (int) $sessao['Empresa_Atual'] !== (int) $autorizacao['empresa']
        || !is_string($indicadorId) || $indicadorId === '') return false;
    $configuracao = dashboardPersonalizadoConfiguracao($autorizacao['empresa'], $erros);
    if ($configuracao === false) return false;
    $acessos = dashboardPersonalizadoLerAcessos($autorizacao['empresa'], array_fill_keys(array_column($configuracao['indicadores'], 'id'), true));
    if (dashboardPersonalizadoRestricaoUsuarioIndicador($autorizacao, $acessos, $indicadorId) !== false) return false;
    foreach (dashboardPersonalizadoIndicadoresPermitidos($autorizacao, $configuracao, $acessos) as $indicador) {
        if ($indicador['id'] !== $indicadorId) continue;
        $portal = dashboardPortalRepresentantesHabilitado($indicador);
        foreach (isset($indicador['abas']) ? $indicador['abas'] : array() as $aba) $portal = $portal || dashboardPortalRepresentantesHabilitado($aba);
        if (!$portal) return false;
        if (dashboardPersonalizadoEhRoot($autorizacao) || !empty($autorizacao['diretor'])) return true;
        foreach (dashboardPersonalizadoIndicadoresGerenciaveis($autorizacao, $configuracao, $acessos) as $gerenciavel) {
            if ($gerenciavel['id'] === $indicadorId) return true;
        }
        return false;
    }
    return false;
}

// D021 é o cadastro compartilhado da instalação. Convites e logins pertencem
// exclusivamente à empresa autenticada; nunca expomos chaves ou URLs de acesso.
function dashboardPortalRepresentantesLinhas($registros, $estado, $conf, $empresa)
{
    $porRepresentante = array();
    foreach ($estado['acessos'] as $acesso) {
        if ((int) $acesso['empresa'] !== (int) $empresa) continue;
        $id = (int) $acesso['representante'];
        if (!isset($porRepresentante[$id])) $porRepresentante[$id] = array();
        $porRepresentante[$id][] = $acesso;
    }
    $linhas = array();
    foreach ($registros as $registro) {
        if ($registro['D021_Ativo'] !== 'S') continue;
        $id = (int) $registro['D021_Id'];
        $identidade = portalRepresentanteIdentidade($registro);
        $vinculo = $identidade ? portalRepresentanteVinculo($conf, $identidade) : '';
        $atual = null; $ultimoEnvio = 0; $ultimoAcesso = 0; $revogavel = false;
        foreach (isset($porRepresentante[$id]) ? $porRepresentante[$id] : array() as $acesso) {
            $ultimoEnvio = max($ultimoEnvio, isset($acesso['emailSolicitado']) ? (int) $acesso['emailSolicitado'] : 0);
            $ultimoAcesso = max($ultimoAcesso, isset($acesso['ultimoAcesso']) ? (int) $acesso['ultimoAcesso'] : 0);
            $revogavel = $revogavel || !empty($acesso['ativo']) || !empty($acesso['envioAtual']);
            if ($atual === null || (!empty($acesso['ativo']) && empty($atual['ativo']))
                || (!empty($acesso['ativo']) === !empty($atual['ativo']) && (int) $acesso['ultimoConvite'] > (int) $atual['ultimoConvite'])) $atual = $acesso;
        }
        $ativo = $identidade && $atual && !empty($atual['ativo']) && hash_equals($atual['vinculo'], $vinculo);
        $pendencia = !$identidade ? 'Informe CPF/CNPJ e um e-mail válido no cadastro do representante.'
            : ($atual && !empty($atual['ativo']) && !$ativo ? 'Cadastro alterado. Reenvie o link para atualizar o acesso.' : '');
        $status = 'sem_envio';
        if (!$identidade || $pendencia !== '') $status = 'cadastro';
        elseif ($ativo) {
            $status = 'enviado';
            if (!empty($atual['senhaHash'])) $status = !empty($atual['ultimoAcesso']) && !empty($atual['senhaCriadaEm']) && $atual['ultimoAcesso'] >= $atual['senhaCriadaEm'] ? 'acessou' : 'senha_cadastrada';
            elseif (!empty($atual['cadastroSenha']['enviado'])) $status = $atual['cadastroSenha']['expira'] > time() ? 'senha_enviada' : 'senha_expirada';
            elseif (!empty($atual['emailConfirmadoEm'])) $status = 'email_confirmado';
        }
        elseif ($atual && !empty($atual['revogadoEm'])) $status = 'revogado';
        elseif ($atual && isset($atual['situacaoEnvio']) && $atual['situacaoEnvio'] === 'falha') $status = 'falha';
        elseif ($atual && !empty($atual['envioAtual'])) $status = 'nao_confirmado';
        elseif ($atual) $status = 'inativo';
        $linhas[] = array('id' => $id, 'nome' => (string) $registro['D021_Nome_Empresa'],
            'email' => (string) $registro['D021_E_Mail'], 'cidade' => (string) $registro['cidade'], 'uf' => (string) $registro['uf'],
            'status' => $status, 'ativo' => (bool) $ativo, 'podeEnviar' => (bool) $identidade, 'podeRevogar' => $revogavel,
            'emailConfirmadoEm' => $atual && !empty($atual['emailConfirmadoEm']) ? $atual['emailConfirmadoEm'] : null,
            'senhaCriadaEm' => $atual && !empty($atual['senhaCriadaEm']) ? $atual['senhaCriadaEm'] : null,
            'ultimoEnvio' => $ultimoEnvio ?: null, 'ultimoAcesso' => $ultimoAcesso ?: null, 'pendencia' => $pendencia,
            'falhaReenvio' => $ativo && isset($atual['situacaoEnvio']) && $atual['situacaoEnvio'] === 'falha');
    }
    return $linhas;
}

function dashboardPortalRepresentantesCarregar($conexao, $indicador, $autorizacao, $conf, $sessao, $indicadorId = null)
{
    $registros = dashboardIndicadorConsultar($conexao, "SELECT D021_Id, D021_Nome_Empresa, D021_Ativo,
        D021_Flag_Pessoa_Fisica_Juridica, D021_Cpf, D021_Cnpj, D021_E_Mail,
        COALESCE(D020_Nome_Cidade, '') AS cidade, COALESCE(D018_UF, '') AS uf
        FROM D021 LEFT JOIN D020 ON D020_Id = D021_D020_Id LEFT JOIN D018 ON D018_Id = D021_D018_Id
        WHERE D021_Ativo = 'S'"
        . dashboardIndicadorRepresentantesCondicaoComercial($indicador['portalRepresentantes'])
        . " ORDER BY D021_Nome_Empresa, D021_Id LIMIT 5001", array(), $erro);
    if ($registros === false) throw new RepresentantePortalErro('Não foi possível consultar os representantes. Verifique a atualização do cadastro de e-mail.', 503);
    if (count($registros) > 5000) throw new RepresentantePortalErro('O cadastro excede o limite desta visão. Contate o suporte para ampliar a consulta.', 422);
    $podeGerenciar = dashboardPortalRepresentantesPodeGerenciar($autorizacao, $sessao, $indicadorId === null ? $indicador['id'] : $indicadorId);
    return array('linhas' => dashboardPortalRepresentantesLinhas($registros, portalRepresentanteEstado($conf), $conf, $autorizacao['empresa']),
        'podeGerenciar' => $podeGerenciar, 'podeVisualizar' => $podeGerenciar && is_file(dirname(__DIR__) . '/portal-representante/gestor.php'),
        'csrf' => $podeGerenciar ? cad003PortalCsrf($conf, $autorizacao['empresa']) : '');
}

function dashboardPortalRepresentantesAcao($autorizacao, $conf, $entrada, $metodo, $indicadorId = '')
{
    if (!is_array($entrada) || !isset($entrada['acao']) || !in_array($entrada['acao'], array('enviar', 'revogar', 'visualizar', 'fecharVisualizacao'), true)) throw new RepresentantePortalErro('Operação inválida.');
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (!dashboardPortalRepresentantesPodeGerenciar($autorizacao, $_SESSION, $indicadorId))
        throw new RepresentantePortalErro('Seu usuário não tem permissão para gerenciar o portal neste indicador. Confira a liberação e a visão de dados no dashboard.', 403);
    if ($metodo !== 'POST') throw new RepresentantePortalErro('Método não permitido.', 405);
    $csrf = isset($entrada['csrf']) ? $entrada['csrf'] : '';
    if (!is_string($csrf) || !hash_equals(cad003PortalCsrf($conf, $autorizacao['empresa']), $csrf))
        throw new RepresentantePortalErro('O formulário expirou. Atualize a lista de representantes.', 403);
    if (in_array($entrada['acao'], array('visualizar', 'fecharVisualizacao'), true)) {
        if ($entrada['acao'] === 'fecharVisualizacao') {
            $token = isset($entrada['contexto']) ? $entrada['contexto'] : '';
            if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/D', $token)) throw new RepresentantePortalErro('Visualização inválida.');
            unset($_SESSION['portalGestor'][$token]);
            return array('ok' => true);
        }
        $arquivo = dirname(__DIR__) . '/portal-representante/gestor.php';
        if (!is_file($arquivo)) throw new RepresentantePortalErro('Atualize o Portal do Representante para habilitar a visualização do gestor.', 503);
        require_once $arquivo;
        $id = isset($entrada['representante']) ? $entrada['representante'] : '';
        if (!is_string($id) || !preg_match('/^[1-9][0-9]{0,9}$/D', $id)) throw new RepresentantePortalErro('Representante inválido.');
        $conexao = portalRepresentanteConectar($conf);
        try { $registro = portalRepresentanteLerCadastro($conexao, (int) $id); } finally { portalRepresentanteFecharConexao($conexao); }
        return portalRepresentanteGestorAbrir($conf, $_SESSION, $autorizacao, $registro, $_COOKIE[dashboardPersonalizadoNomeCookieSessao()], $indicadorId);
    }
    return cad003PortalExecutar(dashboardPortalRepresentantesContexto($autorizacao), $conf,
        $entrada['acao'] === 'enviar' ? 'portalRepresentanteLiberar' : 'portalRepresentanteRevogar', $entrada);
}
