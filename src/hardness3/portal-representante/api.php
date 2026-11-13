<?php
namespace hardness;
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/documentos.php';
require_once __DIR__ . '/comparativo.php';
try {
    $acao = isset($_GET['acao']) && is_string($_GET['acao']) ? $_GET['acao'] : 'sessao';
    if ($portalModoGestor) {
        if (!$portalGestorContexto) throw new RepresentantePortalErro('A visualização do gestor expirou. Abra novamente pelo dashboard.', 403);
        if (!in_array($acao, array('sair', 'sessao', 'resumo', 'lista', 'cobrancas', 'regioes', 'comparativo', 'documento'), true))
            throw new RepresentantePortalErro('Esta operação não está disponível na visualização do gestor.', 403);
    }
    $post = in_array($acao, array('solicitarCodigo', 'confirmarCodigo', 'solicitarSenha', 'salvarSenha', 'entrar', 'sair', 'alternarDemonstracao', 'documento'), true);
    if ($_SERVER['REQUEST_METHOD'] !== ($post ? 'POST' : 'GET')) throw new RepresentantePortalErro('Método não permitido.', 405);
    if ($post) portalRepresentanteCsrf();
    if ($acao === 'sair') {
        if ($portalModoGestor) {
            unset($_SESSION['portalGestor'][$portalGestorToken]);
            portalRepresentanteResponder(200, array('ok' => true));
        }
        $convite = isset($_SESSION['portalConvite']) ? $_SESSION['portalConvite'] : '';
        session_regenerate_id(true);
        $_SESSION = array('portalCsrf' => bin2hex(random_bytes(32)));
        if (is_string($convite) && preg_match('/^[a-f0-9]{48}$/D', $convite)) $_SESSION['portalConvite'] = $convite;
        portalRepresentanteResponder(200, array('ok' => true));
    }
    if (!in_array($acao, array('solicitarCodigo', 'confirmarCodigo', 'solicitarSenha', 'salvarSenha', 'entrar', 'sessao', 'resumo', 'lista', 'cobrancas', 'regioes', 'comparativo', 'alternarDemonstracao', 'documento'), true)) throw new RepresentantePortalErro('Operação não encontrada.', 404);
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    $navegador = session_id() . "\0" . (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
    if ($acao === 'solicitarCodigo') {
        $convite = isset($_SESSION['portalConvite']) ? $_SESSION['portalConvite'] : '';
        $acesso = portalRepresentanteAcesso($confUsuario, $convite);
        $registro = false;
        if ($acesso) { $conexao = portalRepresentanteConectar($confUsuario); try { $registro = portalRepresentanteLerCadastro($conexao, $acesso['representante']); } finally { portalRepresentanteFecharConexao($conexao); } }
        $resultado = portalRepresentanteSolicitarCodigo($confUsuario, $convite, isset($_POST['email']) ? $_POST['email'] : '', $registro, $ip, $navegador);
        unset($_SESSION['portalCadastro'], $_SESSION['portalTokenSenha']);
        $_SESSION['portalDesafio'] = $resultado['desafio'];
        portalRepresentanteResponder(200, $resultado);
    }
    if ($acao === 'confirmarCodigo') {
        $id = isset($_SESSION['portalDesafio']) ? $_SESSION['portalDesafio'] : '';
        $convite = isset($_SESSION['portalConvite']) ? $_SESSION['portalConvite'] : '';
        $acesso = portalRepresentanteAcesso($confUsuario, $convite);
        $registro = false;
        if ($acesso) { $conexao = portalRepresentanteConectar($confUsuario); try { $registro = portalRepresentanteLerCadastro($conexao, $acesso['representante']); } finally { portalRepresentanteFecharConexao($conexao); } }
        $sessao = portalRepresentanteConfirmarCodigo($confUsuario, $id, isset($_POST['codigo']) ? $_POST['codigo'] : '', $registro, $ip, $navegador);
        session_regenerate_id(true);
        $_SESSION = array('portalCadastro' => $sessao, 'portalConvite' => $sessao['convite'], 'portalCsrf' => bin2hex(random_bytes(32)));
        portalRepresentanteResponder(200, array('ok' => true));
    }
    if (in_array($acao, array('solicitarSenha', 'salvarSenha', 'entrar'), true)) {
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $convite = isset($_SESSION['portalConvite']) ? $_SESSION['portalConvite'] : null;
        if ($acao === 'entrar') $convite = portalRepresentanteLocalizarLogin($confUsuario, $convite, $email, $ip);
        $acesso = portalRepresentanteAcesso($confUsuario, $convite);
        $registro = false;
        if ($acesso) { $conexao = portalRepresentanteConectar($confUsuario); try { $registro = portalRepresentanteLerCadastro($conexao, $acesso['representante']); } finally { portalRepresentanteFecharConexao($conexao); } }
        if ($acao === 'solicitarSenha') {
            $cadastro = isset($_SESSION['portalCadastro']) ? $_SESSION['portalCadastro'] : null;
            portalRepresentanteResponder(200, portalRepresentanteSolicitarSenha($confUsuario, $cadastro, $registro, $ip));
        }
        if ($acao === 'salvarSenha') {
            $cadastro = isset($_SESSION['portalCadastro']) ? $_SESSION['portalCadastro'] : null;
            $autorizacao = portalRepresentanteCadastroValido($acesso, $cadastro) ? $cadastro
                : (isset($_SESSION['portalTokenSenha']) ? $_SESSION['portalTokenSenha'] : '');
            $resultado = portalRepresentanteSalvarSenha($confUsuario, $convite, $autorizacao,
                isset($_POST['senha']) ? $_POST['senha'] : '', isset($_POST['confirmacao']) ? $_POST['confirmacao'] : '', $registro, $ip);
            session_regenerate_id(true);
            $_SESSION = array('portalConvite' => $convite, 'portalCsrf' => bin2hex(random_bytes(32)), 'portalSenhaSalva' => true,
                'portalEmailConfirmado' => portalRepresentanteIdentidade($registro)['email']);
            portalRepresentanteResponder(200, $resultado);
        }
        $sessao = portalRepresentanteEntrar($confUsuario, $convite, $email, isset($_POST['senha']) ? $_POST['senha'] : '', $registro);
        session_regenerate_id(true);
        $_SESSION = array('portal' => $sessao, 'portalConvite' => $sessao['convite'], 'portalCsrf' => bin2hex(random_bytes(32)));
        portalRepresentanteResponder(200, array('ok' => true));
    }
    $sessao = isset($_SESSION['portal']) ? $_SESSION['portal'] : null;
    $acesso = !$portalModoGestor && is_array($sessao) && isset($sessao['convite']) ? portalRepresentanteAcesso($confUsuario, $sessao['convite']) : false;
    if (!$portalModoGestor && !$acesso) throw new RepresentantePortalErro('Identifique-se para acessar o acompanhamento.', 401);
    $conexao = portalRepresentanteConectar($confUsuario);
    try {
        $registroPortal = portalRepresentanteLerCadastro($conexao, $portalModoGestor ? $portalGestorContexto['representante'] : $acesso['representante']);
        $conta = $portalModoGestor ? portalRepresentanteGestorConta($confUsuario, $portalGestorContexto, $registroPortal)
            : portalRepresentanteValidarSessao($confUsuario, $sessao, $registroPortal);
        if (!$conta) { unset($_SESSION['portal']); throw new RepresentantePortalErro('Seu acesso expirou. Identifique-se novamente.', 401); }
        if ($portalModoGestor) $_SESSION['portalGestor'][$portalGestorToken]['ultimo'] = time();
        else $_SESSION['portal']['ultimo'] = time();
        if ($acao === 'alternarDemonstracao') portalRepresentanteResponder(200, portalRepresentanteDemonstracaoAlternar($confUsuario, $conta, $_SESSION, $_SERVER, isset($_POST['ativo']) ? $_POST['ativo'] : ''));
        $demonstracao = !$portalModoGestor && portalRepresentanteDemonstracaoAtiva($confUsuario, $conta, $_SESSION, $_SERVER);
        if ($acao === 'sessao') portalRepresentanteResponder(200, array('nome' => $conta['nome'], 'csrf' => $portalCsrfAtual, 'demonstracao' => $demonstracao));
        $conta['painel'] = portalRepresentanteLerConfiguracao($conta['empresa']);
        session_write_close();
        if ($acao === 'documento') {
            if ($demonstracao) throw new RepresentantePortalErro('Documentos de demonstração não possuem impressão.', 403);
            $documento = isset($_GET['documento']) && is_string($_GET['documento']) ? $_GET['documento'] : '';
            $id = isset($_GET['id']) && is_string($_GET['id']) && preg_match('/^[1-9][0-9]{0,9}$/D', $_GET['id']) ? (int) $_GET['id'] : 0;
            if (!$id || !in_array($documento, array('danfe','boleto','boletosNota','orcamento','disponibilidade'), true)) throw new RepresentantePortalErro('Documento inválido.');
            if ($documento === 'danfe') {
                $registro = portalRepresentanteDocumentoNota($conexao, $conta, $id);
                $pdf = portalRepresentanteDocumentoDanfe($confUsuario, $registro);
            } elseif ($documento === 'orcamento') {
                $registro = portalRepresentanteDocumentoOrcamento($conexao, $conta, $id);
                $pdf = portalRepresentanteDocumentoOrcamentoPdf($confUsuario, $conta, $registro);
            } elseif ($documento === 'disponibilidade') {
                $registro = portalRepresentanteDocumentoDisponibilidade($conexao, $conta, $id);
                $pdf = portalRepresentanteDocumentoDisponibilidadePdf($confUsuario, $conta, $registro);
            } elseif ($documento === 'boletosNota') {
                $boletos = portalRepresentanteDocumentoBoletosNota($conexao, $conta, $id);
                $pdf = portalRepresentanteDocumentoBoletosZip($confUsuario, $conta, $boletos);
            } else {
                $registro = portalRepresentanteDocumentoBoleto($conexao, $conta, $id);
                $pdf = portalRepresentanteDocumentoBoletoPdf($confUsuario, $conta, $registro);
            }
            portalRepresentanteDocumentoResponder($documento, $id, $pdf);
        }
        $filtros = portalRepresentanteFiltros($_GET);
        $tipo = $acao === 'cobrancas' ? 'cobrancas' : (isset($_GET['tipo']) && is_string($_GET['tipo']) ? $_GET['tipo'] : 'orcamentos');
        if (!in_array($acao, array('cobrancas','regioes'), true) && !in_array($tipo, array('orcamentos', 'pedidos', 'notas', 'boletos'), true)) throw new RepresentantePortalErro('Visão inválida.');
        if ($acao !== 'lista' || $tipo !== 'orcamentos') $filtros['segmento'] = '';
        $nota = $acao === 'cobrancas' && isset($_GET['nota']) && is_scalar($_GET['nota']) ? (int) $_GET['nota'] : 0;
        if ($acao === 'cobrancas' && $nota < 1) throw new RepresentantePortalErro('Nota inválida.');
        $demonstracaoNaTela = isset($_SERVER['HTTP_X_PORTAL_DEMONSTRACAO']) && $_SERVER['HTTP_X_PORTAL_DEMONSTRACAO'] === '1';
        if ($demonstracao !== $demonstracaoNaTela) throw new RepresentantePortalErro('Recarregue a página para atualizar o modo de visualização.', 409);
        if ($acao === 'comparativo') {
            if (!in_array('comparativo',$conta['painel']['layout']['menu'],true)) throw new RepresentantePortalErro('Comparativo não habilitado neste painel.',404);
            portalRepresentanteResponder(200,portalRepresentanteComparativoCarregar($conexao,$tipo,$conta,$filtros,$demonstracao));
        }
        if ($acao === 'regioes') {
            if ($demonstracao) portalRepresentanteResponder(200, portalRepresentanteRegioesDemonstracao($filtros));
            $consulta = portalRepresentanteRegioesSql($conta, $filtros);
            portalRepresentanteResponder(200, portalRepresentanteRegioesResposta(portalRepresentanteConsultar($conexao, $consulta['sql'], $consulta['params'])));
        }
        if ($demonstracao) portalRepresentanteResponder(200, portalRepresentanteDemonstracaoResposta($acao, $tipo, $filtros, $nota));
        if ($acao === 'resumo') {
            $definicao = portalRepresentanteIndicadoresSql($tipo, $conta, $filtros);
            $painel = array('tipo' => $tipo, 'atrasos' => array(), 'atualizado' => date('c'));
            foreach ($definicao['queries'] as $parte => $sql) $painel[$parte] = portalRepresentanteConsultar($conexao, $sql, $definicao['params']);
            $painel['resumo'] = $painel['resumo'][0];
            portalRepresentanteResponder(200, $painel);
        }
        $limite = $acao === 'cobrancas' ? 100 : 50;
        $fonte = portalRepresentanteFonte($tipo, $conta, $filtros, $nota);
        $linhas = portalRepresentanteConsultar($conexao, portalRepresentanteSql($fonte, false, false, $acao === 'cobrancas' ? 1 : $filtros['pagina'], $limite), $fonte['params']);
        $mais = count($linhas) > $limite;
        if ($mais && $acao === 'cobrancas') throw new RepresentantePortalErro('Mais de 100 cobranças nesta nota. Solicite o detalhamento à empresa.');
        if ($mais) array_pop($linhas);
        if ($tipo === 'pedidos') $linhas = portalRepresentantePedidosDetalhar($conexao, $conta, $linhas, $filtros);
        $linhas = portalRepresentanteDocumentosMarcar($conexao, $conta, $tipo, $linhas);
        portalRepresentanteResponder(200, array('linhas' => $linhas, 'mais' => $mais, 'pagina' => $filtros['pagina']));
    } finally { portalRepresentanteFecharConexao($conexao); }
} catch (RepresentantePortalErro $erro) { portalRepresentanteResponder($erro->status, array('error' => $erro->getMessage())); }
catch (\Throwable $erro) { portalRepresentanteResponder(503, array('error' => 'Não foi possível concluir. Tente novamente mais tarde ou contate a empresa.')); }
