<?php
namespace hardness;
if (realpath(isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '') === __FILE__) { http_response_code(404); exit; }

function portalRepresentanteEmailIndice($conf, $email)
{
    return hash_hmac('sha256', 'email:' . portalRepresentanteEmailValido($email), portalRepresentanteChave($conf));
}

function portalRepresentanteLimparCredenciais(&$acesso)
{
    unset($acesso['senhaHash'], $acesso['senhaCriadaEm'], $acesso['emailConfirmadoEm'],
        $acesso['cadastroAutorizacao'], $acesso['cadastroExpira'], $acesso['cadastroSenha'], $acesso['desafioAtual']);
}

function portalRepresentanteCadastroValido($acesso, $cadastro)
{
    return $acesso && $acesso['ativo'] && empty($acesso['senhaHash']) && is_array($cadastro)
        && isset($cadastro['versao'], $cadastro['autorizacao'], $cadastro['expira'], $acesso['cadastroAutorizacao'], $acesso['cadastroExpira'])
        && is_string($cadastro['versao']) && is_string($cadastro['autorizacao'])
        && $cadastro['expira'] > time() && $acesso['cadastroExpira'] > time()
        && hash_equals($acesso['versao'], $cadastro['versao'])
        && hash_equals($acesso['cadastroAutorizacao'], hash('sha256', $cadastro['autorizacao']));
}

function portalRepresentanteCadastroIdentidade($conf, $acesso, $registro)
{
    $identidade = portalRepresentanteIdentidade($registro);
    return $acesso && $acesso['ativo'] && $identidade && $acesso['representante'] === $identidade['id']
        && hash_equals($acesso['vinculo'], portalRepresentanteVinculo($conf, $identidade));
}

function portalRepresentanteSolicitarSenha($conf, $cadastro, $registro, $ip)
{
    $convite = is_array($cadastro) && isset($cadastro['convite']) ? $cadastro['convite'] : '';
    $token = bin2hex(random_bytes(32));
    $hash = hash('sha256', $token);
    $reserva = portalRepresentanteEstado($conf, function (&$estado) use ($conf, $cadastro, $convite, $registro, $ip, $hash) {
        if (!portalRepresentanteLimitar($estado, $conf, 'senha-envio-ip:' . $ip, 10, 900)) return array('erro' => 'Muitas solicitações. Aguarde 15 minutos.', 'status' => 429);
        $acesso = isset($estado['acessos'][$convite]) ? $estado['acessos'][$convite] : false;
        if (!portalRepresentanteCadastroValido($acesso, $cadastro) || !portalRepresentanteCadastroIdentidade($conf, $acesso, $registro))
            return array('erro' => 'A confirmação expirou ou o acesso foi revogado. Abra o link da empresa e confirme seu e-mail novamente.', 'status' => 401);
        if (!empty($acesso['cadastroSenha']['solicitadoEm']) && $acesso['cadastroSenha']['solicitadoEm'] > time() - 60)
            return array('erro' => 'Aguarde um minuto antes de reenviar o link de cadastro.', 'status' => 429);
        if (!portalRepresentanteLimitar($estado, $conf, 'senha-envio:' . $convite, 3, 900)) return array('erro' => 'Muitas solicitações. Aguarde 15 minutos.', 'status' => 429);
        $estado['acessos'][$convite]['cadastroSenha'] = array('hash' => $hash, 'versao' => $acesso['versao'],
            'expira' => time() + 1800, 'solicitadoEm' => time(), 'enviado' => false);
        return array('gestor' => $acesso['gestor'], 'versao' => $acesso['versao']);
    });
    portalRepresentanteResultado($reserva);
    $identidade = portalRepresentanteIdentidade($registro);
    try {
        portalRepresentanteEmail(portalRepresentanteEmailConfiguracao($conf, $reserva['gestor']), $identidade['email'],
            'Cadastre sua senha — Portal do Representante', "Olá, " . $identidade['nome'] . ".\n\nSeu e-mail foi confirmado. Crie sua senha pelo link:\n"
            . portalRepresentanteUrl($conf, $convite) . '&cadastro=' . $token
            . "\n\nO link vale por 30 minutos e funciona uma única vez. Depois, entre no portal com seu e-mail e a senha criada.\nNão compartilhe este link.\n\nHardness — Portal do Representante");
    } catch (\Throwable $erro) {
        portalRepresentanteEstado($conf, function (&$estado) use ($convite, $hash) {
            if (isset($estado['acessos'][$convite]['cadastroSenha']['hash']) && hash_equals($estado['acessos'][$convite]['cadastroSenha']['hash'], $hash)) unset($estado['acessos'][$convite]['cadastroSenha']);
        });
        throw new RepresentantePortalErro('Não foi possível enviar o link para cadastrar a senha. Tente novamente mais tarde ou contate a empresa.', 503);
    }
    $enviado = portalRepresentanteEstado($conf, function (&$estado) use ($convite, $hash, $reserva) {
        $acesso = &$estado['acessos'][$convite];
        if (!$acesso['ativo'] || !hash_equals($acesso['versao'], $reserva['versao']) || !isset($acesso['cadastroSenha']['hash'])
            || !hash_equals($acesso['cadastroSenha']['hash'], $hash)) return false;
        $acesso['cadastroSenha']['enviado'] = true;
        return true;
    });
    if (!$enviado) throw new RepresentantePortalErro('O acesso mudou durante o envio. Este link não pode mais ser utilizado.', 409);
    return array('ok' => true, 'reenviarEm' => 60, 'mensagem' => 'Enviamos um novo e-mail com o link para cadastrar sua senha. Abra-o em até 30 minutos. Verifique também a pasta de spam.');
}

function portalRepresentanteTokenSenhaValido($acesso, $token)
{
    return $acesso && $acesso['ativo'] && empty($acesso['senhaHash']) && is_string($token) && preg_match('/^[a-f0-9]{64}$/D', $token)
        && !empty($acesso['cadastroSenha']['enviado']) && $acesso['cadastroSenha']['expira'] > time()
        && hash_equals($acesso['versao'], $acesso['cadastroSenha']['versao'])
        && hash_equals($acesso['cadastroSenha']['hash'], hash('sha256', $token));
}

function portalRepresentanteAutorizacaoSenhaValida($acesso, $convite, $autorizacao)
{
    if (is_array($autorizacao)) {
        return isset($autorizacao['convite']) && $autorizacao['convite'] === $convite
            && portalRepresentanteCadastroValido($acesso, $autorizacao);
    }
    // Mantém válidos os links de cadastro enviados antes do fluxo direto por código.
    return portalRepresentanteTokenSenhaValido($acesso, $autorizacao);
}

function portalRepresentanteSalvarSenha($conf, $convite, $autorizacao, $senha, $confirmacao, $registro, $ip)
{
    $acesso = portalRepresentanteAcesso($conf, $convite);
    if (!portalRepresentanteAutorizacaoSenhaValida($acesso, $convite, $autorizacao) || !portalRepresentanteCadastroIdentidade($conf, $acesso, $registro))
        throw new RepresentantePortalErro('A confirmação expirou, já foi utilizada ou o acesso foi revogado. Abra o link da empresa e confirme seu e-mail novamente.', 401);
    // O limite de 72 bytes evita truncamento silencioso com bcrypt.
    if (!is_string($senha) || preg_match_all('/./us', $senha) < 10 || strlen($senha) > 72 || strpos($senha, "\0") !== false)
        throw new RepresentantePortalErro('Use uma senha com pelo menos 10 caracteres e no máximo 72 bytes.');
    if (!is_string($confirmacao) || !hash_equals($senha, $confirmacao)) throw new RepresentantePortalErro('A confirmação da senha não confere.');
    $permitido = portalRepresentanteEstado($conf, function (&$estado) use ($conf, $ip) {
        return portalRepresentanteLimitar($estado, $conf, 'senha-salvar:' . $ip, 10, 900);
    });
    if (!$permitido) throw new RepresentantePortalErro('Muitas tentativas. Aguarde 15 minutos.', 429);
    $hash = password_hash($senha, PASSWORD_DEFAULT);
    if (!is_string($hash)) throw new RepresentantePortalErro('Não foi possível cadastrar a senha. Tente novamente.', 503);
    $resultado = portalRepresentanteEstado($conf, function (&$estado) use ($conf, $convite, $autorizacao, $hash, $registro) {
        $acesso = &$estado['acessos'][$convite];
        if (!portalRepresentanteAutorizacaoSenhaValida($acesso, $convite, $autorizacao) || !portalRepresentanteCadastroIdentidade($conf, $acesso, $registro))
            return array('erro' => 'A confirmação já foi utilizada ou o acesso foi revogado.', 'status' => 401);
        $acesso['senhaHash'] = $hash; $acesso['senhaCriadaEm'] = time();
        $acesso['versao'] = bin2hex(random_bytes(16));
        unset($acesso['cadastroSenha'], $acesso['cadastroAutorizacao'], $acesso['cadastroExpira'], $acesso['desafioAtual'], $acesso['primeiroAcesso'], $acesso['ultimoAcesso']);
        return array('ok' => true, 'mensagem' => 'Senha cadastrada. Entre com seu e-mail e sua senha.');
    });
    return portalRepresentanteResultado($resultado);
}

// Localiza somente convites desta instalação. E-mails presentes em mais de uma
// empresa devem usar o link da empresa para não escolher uma carteira arbitrária.
function portalRepresentanteLocalizarLogin($conf, $convite, $email, $ip)
{
    $indice = portalRepresentanteEmailIndice($conf, $email);
    $resultado = portalRepresentanteEstado($conf, function (&$estado) use ($conf, $convite, $indice, $ip) {
        if (!portalRepresentanteLimitar($estado, $conf, 'login-ip:' . $ip, 30, 900)
            || !portalRepresentanteLimitar($estado, $conf, 'login-email:' . $indice, 10, 900))
            return array('erro' => 'Muitas tentativas. Aguarde 15 minutos para entrar novamente.', 'status' => 429);
        $encontrados = array();
        foreach ($estado['acessos'] as $id => $acesso) {
            if ($convite !== null && $id !== $convite) continue;
            if ($acesso['ativo'] && !empty($acesso['senhaHash']) && isset($acesso['emailIndice']) && hash_equals($acesso['emailIndice'], $indice)) $encontrados[] = $id;
        }
        return count($encontrados) === 1 ? $encontrados[0] : false;
    });
    return portalRepresentanteResultado($resultado);
}

function portalRepresentanteEntrar($conf, $convite, $email, $senha, $registro)
{
    $acesso = portalRepresentanteAcesso($conf, $convite);
    $identidade = portalRepresentanteIdentidade($registro);
    $invalido = 'E-mail ou senha inválidos, ou acesso revogado. Utilize o link disponibilizado pela empresa.';
    // A verificação também ocorre para conta inexistente, sem revelar seu cadastro.
    $hash = $acesso && !empty($acesso['senhaHash']) ? $acesso['senhaHash'] : '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';
    $valida = is_string($senha) && strlen($senha) <= 72 && strpos($senha, "\0") === false && password_verify($senha, $hash);
    if (!$valida || !portalRepresentanteCadastroIdentidade($conf, $acesso, $registro)
        || !hash_equals($identidade['email'], portalRepresentanteEmailValido($email))) throw new RepresentantePortalErro($invalido, 401);
    $resultado = portalRepresentanteEstado($conf, function (&$estado) use ($conf, $convite, $acesso, $hash, $invalido) {
        $atual = &$estado['acessos'][$convite];
        if (!$atual['ativo'] || !isset($atual['senhaHash']) || !hash_equals($atual['senhaHash'], $hash)
            || !hash_equals($atual['versao'], $acesso['versao'])) return array('erro' => $invalido, 'status' => 401);
        if (empty($atual['primeiroAcesso'])) $atual['primeiroAcesso'] = time();
        $atual['ultimoAcesso'] = time();
        return array('metodo' => 'senha', 'convite' => $convite, 'versao' => $atual['versao'], 'vinculo' => $atual['vinculo'],
            'tenant' => portalRepresentanteTenant($conf), 'inicio' => time(), 'ultimo' => time());
    });
    return portalRepresentanteResultado($resultado);
}
