<?php
namespace hardness;
if (realpath(isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '') === __FILE__) { http_response_code(404); exit; }
$loginEtapa = 'entrar'; $loginNome = ''; $loginMensagem = ''; $loginEmail = '';
$empresaPortalLogin = 0;
if (!$conta) {
    try {
        if (isset($_SESSION['portalConvite'])) {
            $acessoLogin = portalRepresentanteAcesso($confUsuario, $_SESSION['portalConvite']);
            $registroLogin = false;
            if ($acessoLogin) {
                $conexao = portalRepresentanteConectar($confUsuario);
                try {
                    $registroLogin = portalRepresentanteLerCadastro($conexao, $acessoLogin['representante']);
                    if (portalRepresentanteCadastroIdentidade($confUsuario, $acessoLogin, $registroLogin)) {
                        $empresaPortalLogin = (int) $acessoLogin['empresa'];
                        try { $empresaPortal = portalRepresentanteEmpresa($conexao, $confUsuario, $acessoLogin['empresa']); }
                        catch (\Throwable $erroEmpresa) { /* A logo não impede o primeiro acesso. */ }
                    }
                }
                finally { portalRepresentanteFecharConexao($conexao); }
            }
            if (!portalRepresentanteCadastroIdentidade($confUsuario, $acessoLogin, $registroLogin)) {
                $loginEtapa = 'indisponivel';
                $loginMensagem = 'Este acesso foi revogado, está indisponível ou o cadastro foi alterado. Solicite um novo link à empresa.';
            } else {
                $loginNome = $registroLogin['D021_Nome_Empresa'];
                if (!empty($acessoLogin['senhaHash'])) {
                    unset($_SESSION['portalTokenSenha'], $_SESSION['portalCadastro']);
                    $emailConfirmado = isset($_SESSION['portalEmailConfirmado']) ? $_SESSION['portalEmailConfirmado'] : '';
                    if (is_string($emailConfirmado) && hash_equals(portalRepresentanteIdentidade($registroLogin)['email'], $emailConfirmado)) $loginEmail = $emailConfirmado;
                } elseif (portalRepresentanteCadastroValido($acessoLogin, isset($_SESSION['portalCadastro']) ? $_SESSION['portalCadastro'] : null)) {
                    $loginEtapa = 'senha';
                } elseif (isset($_SESSION['portalTokenSenha'])) {
                    if (portalRepresentanteTokenSenhaValido($acessoLogin, $_SESSION['portalTokenSenha'])) $loginEtapa = 'senha';
                    else {
                        unset($_SESSION['portalTokenSenha']);
                        $loginEtapa = 'email';
                        $loginMensagem = 'O link para criar a senha expirou ou foi substituído. Confirme seu e-mail novamente para cadastrar a senha.';
                    }
                } else {
                    $loginEtapa = 'email';
                    if (isset($_SESSION['portalCadastro'])) {
                        unset($_SESSION['portalCadastro']);
                        $loginMensagem = 'A confirmação expirou. Confirme seu e-mail novamente para cadastrar a senha.';
                    }
                }
            }
        }
        if (!empty($_SESSION['portalSenhaSalva'])) {
            $loginMensagem = 'Senha cadastrada com sucesso. Entre com seu e-mail e sua senha.';
            unset($_SESSION['portalSenhaSalva']);
        }
    } catch (\Throwable $erro) {
        $loginEtapa = 'indisponivel';
        $loginMensagem = 'Não foi possível verificar seu acesso agora. Tente novamente mais tarde.';
    }
}
