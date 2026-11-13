<?php
namespace hardness;

if (realpath(isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '') === __FILE__) { http_response_code(404); exit; }
require_once __DIR__ . '/email.php';
require_once __DIR__ . '/senha.php';

class RepresentantePortalErro extends \RuntimeException
{
    public $status;
    public function __construct($mensagem, $status = 422) { parent::__construct($mensagem); $this->status = $status; }
}

function portalRepresentanteConfig($conf)
{
    $opcoes = isset($conf['portalRepresentante']) && is_array($conf['portalRepresentante']) ? $conf['portalRepresentante'] : array();
    $variaveis = array('urlPublica' => 'HARDNESS_REP_PUBLIC_URL',
        'armazenamento' => 'HARDNESS_REP_STORAGE', 'segredo' => 'HARDNESS_REP_SECRET');
    foreach ($variaveis as $chave => $nome) if (!isset($opcoes[$chave]) && getenv($nome) !== false) $opcoes[$chave] = getenv($nome);
    return $opcoes;
}

function portalRepresentanteTenant($conf)
{
    return hash('sha256', $conf['dbHost'] . "\0" . $conf['dbDatabase']);
}

function portalRepresentanteChave($conf)
{
    $opcoes = portalRepresentanteConfig($conf);
    $segredo = !empty($opcoes['segredo']) ? $opcoes['segredo'] : (isset($conf['dbPass']) ? $conf['dbPass'] : '');
    if (!is_string($segredo) || $segredo === '') throw new RepresentantePortalErro('O portal precisa ser configurado nesta instalação.', 503);
    return hash_hmac('sha256', 'hardness-representante-email-v1:' . portalRepresentanteTenant($conf), $segredo, true);
}

function portalRepresentanteDiretorio($conf)
{
    $opcoes = portalRepresentanteConfig($conf);
    $base = !empty($opcoes['armazenamento']) ? $opcoes['armazenamento'] : dirname(__DIR__) . '/dados_usuarios/portal-representantes-private';
    $diretorio = rtrim($base, '/\\') . '/' . portalRepresentanteTenant($conf);
    if (!is_dir($diretorio) && !@mkdir($diretorio, 0700, true) && !is_dir($diretorio)) throw new RepresentantePortalErro('Armazenamento do portal indisponível.', 503);
    if (!is_file($diretorio . '/.htaccess') && @file_put_contents($diretorio . '/.htaccess', "Require all denied\n", LOCK_EX) === false) throw new RepresentantePortalErro('Armazenamento do portal indisponível.', 503);
    return $diretorio;
}

// Um lock separado mantém leitura e substituição atômicas, inclusive em dois pedidos simultâneos.
// A função de alteração retorna o resultado da operação e recebe o estado por referência.
function portalRepresentanteEstado($conf, $alterar = null)
{
    $arquivo = portalRepresentanteDiretorio($conf) . '/estado.php';
    $handle = @fopen($arquivo . '.lock', 'c+');
    if (!$handle) throw new RepresentantePortalErro('Acessos do portal indisponíveis.', 503);
    @chmod($arquivo . '.lock', 0600);
    try {
        if (!flock($handle, $alterar ? LOCK_EX : LOCK_SH)) throw new RepresentantePortalErro('Acessos do portal indisponíveis.', 503);
        $prefixo = "<?php http_response_code(404); exit; ?>\n";
        $conteudo = is_file($arquivo) ? @file_get_contents($arquivo) : '';
        $estado = $conteudo === '' ? array('acessos' => array(), 'desafios' => array(), 'limites' => array())
            : (is_string($conteudo) && strpos($conteudo, $prefixo) === 0 ? json_decode(substr($conteudo, strlen($prefixo)), true) : null);
        if (!is_array($estado) || !isset($estado['acessos'], $estado['desafios'], $estado['limites'])
            || !is_array($estado['acessos']) || !is_array($estado['desafios']) || !is_array($estado['limites'])) throw new RepresentantePortalErro('Acessos do portal precisam ser verificados.', 503);
        if (!$alterar) return $estado;
        foreach ($estado['desafios'] as $id => $desafio) if ($desafio['expira'] < time() - 300) unset($estado['desafios'][$id]);
        foreach ($estado['limites'] as $id => $limite) if ($limite['fim'] < time()) unset($estado['limites'][$id]);
        $resultado = $alterar($estado);
        $json = json_encode($estado, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) throw new RepresentantePortalErro('Não foi possível salvar os acessos.', 503);
        $temporario = $arquivo . '.' . bin2hex(random_bytes(8)) . '.php';
        try {
            $tmp = @fopen($temporario, 'x');
            if (!$tmp) throw new RepresentantePortalErro('Não foi possível salvar os acessos.', 503);
            @chmod($temporario, 0600);
            try {
                $dados = $prefixo . $json;
                if (fwrite($tmp, $dados) !== strlen($dados) || !fflush($tmp)) throw new RepresentantePortalErro('Não foi possível salvar os acessos.', 503);
            } finally { fclose($tmp); }
            if (!@rename($temporario, $arquivo)) throw new RepresentantePortalErro('Não foi possível salvar os acessos.', 503);
        } finally { if (is_file($temporario)) @unlink($temporario); }
        return $resultado;
    } finally { flock($handle, LOCK_UN); fclose($handle); }
}

function portalRepresentanteDocumento($valor)
{
    if (!is_string($valor) || strlen($valor) > 40 || preg_match('/[^0-9.\-\/\s]/', $valor)) return '';
    $numero = preg_replace('/\D/', '', $valor);
    return in_array(strlen($numero), array(11, 14), true) && !preg_match('/^(\d)\1+$/', $numero) ? $numero : '';
}

function portalRepresentanteEmailValido($valor)
{
    if (!is_string($valor)) return '';
    $email = trim($valor);
    return strlen($email) <= 254 && filter_var($email, FILTER_VALIDATE_EMAIL) ? strtolower($email) : '';
}

function portalRepresentanteIdentidade($registro)
{
    if (!is_array($registro) || empty($registro['D021_Id']) || !isset($registro['D021_Ativo']) || $registro['D021_Ativo'] !== 'S') return false;
    $cpf = portalRepresentanteDocumento(isset($registro['D021_Cpf']) ? $registro['D021_Cpf'] : '');
    $cnpj = portalRepresentanteDocumento(isset($registro['D021_Cnpj']) ? $registro['D021_Cnpj'] : '');
    if (strlen($cpf) !== 11) $cpf = '';
    if (strlen($cnpj) !== 14) $cnpj = '';
    $tipo = isset($registro['D021_Flag_Pessoa_Fisica_Juridica']) ? $registro['D021_Flag_Pessoa_Fisica_Juridica'] : '';
    $documento = $tipo === 'F' ? $cpf : ($tipo === 'J' ? $cnpj : ($cpf !== '' && $cnpj === '' ? $cpf : ($cnpj !== '' && $cpf === '' ? $cnpj : '')));
    $email = portalRepresentanteEmailValido(isset($registro['D021_E_Mail']) ? $registro['D021_E_Mail'] : '');
    if ($documento === '' || $email === '') return false;
    return array('id' => (int) $registro['D021_Id'], 'nome' => (string) $registro['D021_Nome_Empresa'], 'documento' => $documento, 'email' => $email);
}

function portalRepresentanteVinculo($conf, $identidade)
{
    return hash_hmac('sha256', $identidade['id'] . "\0" . $identidade['documento'] . "\0" . $identidade['email'], portalRepresentanteChave($conf));
}

function portalRepresentanteLimitar(&$estado, $conf, $rotulo, $maximo, $segundos)
{
    $chave = hash_hmac('sha256', $rotulo, portalRepresentanteChave($conf));
    if (!isset($estado['limites'][$chave]) || $estado['limites'][$chave]['fim'] <= time()) $estado['limites'][$chave] = array('fim' => time() + $segundos, 'quantidade' => 0);
    if ($estado['limites'][$chave]['quantidade'] >= $maximo) return false;
    if (count($estado['limites']) > 20000) return false;
    $estado['limites'][$chave]['quantidade']++;
    return true;
}

function portalRepresentanteUrl($conf, $convite)
{
    $opcoes = portalRepresentanteConfig($conf);
    $url = isset($opcoes['urlPublica']) ? $opcoes['urlPublica'] : (isset($conf['urlRaiz']) ? rtrim($conf['urlRaiz'], '/') . '/hardness3/portal-representante/' : '');
    $partes = is_string($url) ? parse_url($url) : false;
    if (!$partes || empty($partes['host']) || isset($partes['query']) || isset($partes['fragment'])
        || (isset($partes['user']) || isset($partes['pass']))
        || (!isset($partes['scheme']) || ($partes['scheme'] !== 'https' && !($partes['scheme'] === 'http' && in_array($partes['host'], array('localhost', '127.0.0.1', '[::1]'), true))))) {
        throw new RepresentantePortalErro('O endereço de acesso do portal precisa ser configurado.', 503);
    }
    return rtrim($url, '/') . '/?convite=' . rawurlencode($convite);
}

function portalRepresentanteLiberar($conf, $empresa, $registro, $usuario)
{
    $identidade = portalRepresentanteIdentidade($registro);
    if (!$identidade) throw new RepresentantePortalErro('Cadastre CPF ou CNPJ e um e-mail válido para este representante ativo.');
    if ((int) $empresa <= 0 || (int) $usuario <= 0) throw new RepresentantePortalErro('Acesso não autorizado.', 403);
    portalRepresentanteUrl($conf, str_repeat('0', 48));
    $remetente = portalRepresentanteEmailConfiguracao($conf, $usuario);
    $vinculo = portalRepresentanteVinculo($conf, $identidade);
    $reservado = portalRepresentanteEstado($conf, function (&$estado) use ($conf, $empresa, $identidade, $vinculo, $usuario) {
        if (!portalRepresentanteLimitar($estado, $conf, 'convite:' . $empresa . ':' . $identidade['id'], 3, 900)) return array('erro' => 'Aguarde antes de reenviar o link.', 'status' => 429);
        $id = null;
        foreach ($estado['acessos'] as $chave => $acesso) if ($acesso['empresa'] === (int) $empresa && $acesso['representante'] === $identidade['id'] && $acesso['ativo']) { $id = $chave; break; }
        if ($id !== null && !empty($estado['acessos'][$id]['ultimoConvite']) && $estado['acessos'][$id]['ultimoConvite'] > time() - 60) return array('erro' => 'Aguarde um minuto antes de reenviar o link.', 'status' => 429);
        if ($id === null) {
            if (count($estado['acessos']) >= 10000) return array('erro' => 'Limite de acessos atingido. Contate o suporte.', 'status' => 503);
            $id = bin2hex(random_bytes(24));
            $estado['acessos'][$id] = array('empresa' => (int) $empresa, 'representante' => $identidade['id'], 'ativo' => false, 'versao' => bin2hex(random_bytes(16)), 'vinculo' => $vinculo);
        }
        $acesso = &$estado['acessos'][$id];
        if (!hash_equals($acesso['vinculo'], $vinculo)) { $acesso['versao'] = bin2hex(random_bytes(16)); $acesso['ativo'] = false; $acesso['vinculo'] = $vinculo; portalRepresentanteLimparCredenciais($acesso); unset($acesso['primeiroAcesso'], $acesso['ultimoAcesso']); }
        $acesso['emailIndice'] = portalRepresentanteEmailIndice($conf, $identidade['email']);
        $acesso['ultimoConvite'] = time(); $acesso['gestor'] = (int) $usuario;
        $acesso['situacaoEnvio'] = 'enviando';
        $acesso['envioAtual'] = bin2hex(random_bytes(16));
        return array('convite' => $id, 'envio' => $acesso['envioAtual']);
    });
    portalRepresentanteResultado($reservado);
    $url = portalRepresentanteUrl($conf, $reservado['convite']);
    try { portalRepresentanteEmail($remetente, $identidade['email'], 'Acesso ao Portal do Representante',
        "Olá, " . $identidade['nome'] . ".\n\nAcompanhe seus orçamentos, pedidos, notas fiscais e pagamentos pelo link:\n" . $url
        . "\n\nNo primeiro acesso, confirme seu e-mail com o código recebido e cadastre sua senha na mesma tela. Nos próximos acessos, use seu e-mail e a senha cadastrada.\n\nHardness — Portal do Representante"); }
    catch (\Throwable $erro) {
        portalRepresentanteEstado($conf, function (&$estado) use ($reservado) {
            $acesso = &$estado['acessos'][$reservado['convite']];
            if (isset($acesso['envioAtual']) && hash_equals($acesso['envioAtual'], $reservado['envio'])) {
                $acesso['situacaoEnvio'] = 'falha'; unset($acesso['envioAtual']);
            }
        });
        // O transporte já converte a falha SMTP em uma mensagem sem dados sensíveis.
        if ($erro instanceof RepresentantePortalErro) throw $erro;
        throw new RepresentantePortalErro('Não foi possível enviar o e-mail com o link. Verifique a configuração de envio do responsável e tente novamente.', 503);
    }
    $ativado = portalRepresentanteEstado($conf, function (&$estado) use ($reservado) {
        $acesso = &$estado['acessos'][$reservado['convite']];
        if (isset($acesso['envioAtual']) && hash_equals($acesso['envioAtual'], $reservado['envio'])) { $acesso['ativo'] = true; $acesso['emailSolicitado'] = time(); $acesso['situacaoEnvio'] = 'enviado'; unset($acesso['envioAtual'], $acesso['revogadoEm']); return true; }
        return false;
    });
    if (!$ativado) throw new RepresentantePortalErro('O acesso foi revogado durante o envio do e-mail. O link está inativo.', 409);
    return array('ok' => true, 'ativo' => true, 'mensagem' => 'Link enviado para ' . $identidade['email'] . '. Oriente o representante a verificar também a pasta de spam.');
}

function portalRepresentanteResultado($resultado)
{
    if (is_array($resultado) && isset($resultado['erro'])) throw new RepresentantePortalErro($resultado['erro'], isset($resultado['status']) ? $resultado['status'] : 422);
    return $resultado;
}

function portalRepresentanteRevogar($conf, $empresa, $representante)
{
    portalRepresentanteEstado($conf, function (&$estado) use ($empresa, $representante) {
        foreach ($estado['acessos'] as &$acesso) if ($acesso['empresa'] === (int) $empresa && $acesso['representante'] === (int) $representante) {
            $acesso['ativo'] = false; $acesso['revogadoEm'] = time(); $acesso['versao'] = bin2hex(random_bytes(16)); portalRepresentanteLimparCredenciais($acesso); unset($acesso['envioAtual']);
        }
        unset($acesso);
    });
    return array('ok' => true, 'ativo' => false, 'mensagem' => 'Acesso revogado. Links, códigos, senha e sessões anteriores foram invalidados. Uma nova liberação exigirá confirmar o e-mail e cadastrar uma nova senha.');
}

function portalRepresentanteAcesso($conf, $convite)
{
    if (!is_string($convite) || !preg_match('/^[a-f0-9]{48}$/D', $convite)) return false;
    $estado = portalRepresentanteEstado($conf);
    return isset($estado['acessos'][$convite]) && $estado['acessos'][$convite]['ativo'] ? $estado['acessos'][$convite] : false;
}

function portalRepresentanteSolicitarCodigo($conf, $convite, $email, $registro, $ip, $navegador)
{
    $identidade = portalRepresentanteIdentidade($registro);
    $email = portalRepresentanteEmailValido($email);
    $codigo = (string) random_int(100000, 999999);
    $id = bin2hex(random_bytes(24));
    $resposta = array('ok' => true, 'desafio' => $id, 'expiraEm' => 300, 'reenviarEm' => 60,
        'mensagem' => 'Se os dados corresponderem ao cadastro, você receberá um código no e-mail cadastrado. Verifique também a pasta de spam.');
    $preparado = portalRepresentanteEstado($conf, function (&$estado) use ($conf, $convite, $email, $identidade, $ip, $navegador, $id, $codigo) {
        if (!portalRepresentanteLimitar($estado, $conf, 'solicitar-ip:' . $ip, 10, 900)) return array('erro' => 'Muitas solicitações. Aguarde 15 minutos para tentar novamente.', 'status' => 429);
        if (!$identidade || $email === '' || !is_string($convite) || !isset($estado['acessos'][$convite])) return false;
        $acesso = &$estado['acessos'][$convite];
        if (!$acesso['ativo'] || !empty($acesso['senhaHash']) || $acesso['representante'] !== $identidade['id'] || !hash_equals($identidade['email'], $email)
            || !hash_equals($acesso['vinculo'], portalRepresentanteVinculo($conf, $identidade))) return false;
        if (isset($acesso['ultimoCodigo']) && $acesso['ultimoCodigo'] > time() - 60) return array('erro' => 'Aguarde um minuto antes de solicitar outro código.', 'status' => 429);
        if (!portalRepresentanteLimitar($estado, $conf, 'codigo-acesso:' . $convite, 3, 900)) return array('erro' => 'Muitas solicitações. Aguarde 15 minutos para tentar novamente.', 'status' => 429);
        $acesso['ultimoCodigo'] = time(); $acesso['desafioAtual'] = $id;
        $estado['desafios'][$id] = array('convite' => $convite, 'versao' => $acesso['versao'], 'vinculo' => $acesso['vinculo'], 'expira' => time() + 300,
            'tentativas' => 0, 'enviado' => false, 'navegador' => hash('sha256', $navegador), 'hash' => hash_hmac('sha256', $id . "\0" . $codigo, portalRepresentanteChave($conf)));
        return array('gestor' => isset($acesso['gestor']) ? (int) $acesso['gestor'] : 0);
    });
    portalRepresentanteResultado($preparado);
    if (!is_array($preparado)) return $resposta; // Não revela CPF/CNPJ, e-mail nem existência de acesso.
    try { portalRepresentanteEmail(portalRepresentanteEmailConfiguracao($conf, $preparado['gestor']), $identidade['email'], 'Confirme seu e-mail — Portal do Representante',
        "Seu código de confirmação é: " . $codigo . ".\n\nVálido por 5 minutos e para um único uso. Não compartilhe este código.\nSe você não solicitou o acesso, ignore este e-mail.\n\nHardness — Portal do Representante"); }
    catch (\Throwable $erro) {
        portalRepresentanteEstado($conf, function (&$estado) use ($id) { unset($estado['desafios'][$id]); });
        throw new RepresentantePortalErro('Não foi possível enviar o código por e-mail. Tente novamente mais tarde ou contate a empresa.', 503);
    }
    portalRepresentanteEstado($conf, function (&$estado) use ($id) { if (isset($estado['desafios'][$id])) $estado['desafios'][$id]['enviado'] = true; });
    return $resposta;
}

function portalRepresentanteConfirmarCodigo($conf, $id, $codigo, $registro, $ip, $navegador)
{
    $identidade = portalRepresentanteIdentidade($registro);
    $resultado = portalRepresentanteEstado($conf, function (&$estado) use ($conf, $id, $codigo, $identidade, $ip, $navegador) {
        $invalido = array('erro' => 'Código inválido ou expirado. Solicite um novo código.', 'status' => 401);
        if (!portalRepresentanteLimitar($estado, $conf, 'verificar-ip:' . $ip, 20, 900)) return array('erro' => 'Muitas tentativas. Aguarde 15 minutos.', 'status' => 429);
        if (!is_string($id) || !isset($estado['desafios'][$id])) return $invalido;
        $desafio = &$estado['desafios'][$id];
        $acesso = isset($estado['acessos'][$desafio['convite']]) ? $estado['acessos'][$desafio['convite']] : false;
        if (!$acesso || !$acesso['ativo'] || !empty($acesso['senhaHash']) || empty($desafio['enviado']) || $desafio['expira'] <= time() || $desafio['tentativas'] >= 5
            || !isset($acesso['desafioAtual']) || $acesso['desafioAtual'] !== $id || !hash_equals($acesso['versao'], $desafio['versao'])
            || !hash_equals($desafio['navegador'], hash('sha256', $navegador)) || !$identidade || $identidade['id'] !== $acesso['representante']
            || !hash_equals($desafio['vinculo'], portalRepresentanteVinculo($conf, $identidade))) return $invalido;
        $desafio['tentativas']++;
        if (!is_string($codigo) || !preg_match('/^[0-9]{6}$/D', $codigo)
            || !hash_equals($desafio['hash'], hash_hmac('sha256', $id . "\0" . $codigo, portalRepresentanteChave($conf)))) return $invalido;
        $autorizacao = bin2hex(random_bytes(32));
        $cadastro = array('convite' => $desafio['convite'], 'versao' => $acesso['versao'],
            'autorizacao' => $autorizacao, 'expira' => time() + 900);
        $atual = &$estado['acessos'][$desafio['convite']];
        $atual['emailConfirmadoEm'] = time();
        $atual['emailIndice'] = portalRepresentanteEmailIndice($conf, $identidade['email']);
        $atual['cadastroAutorizacao'] = hash('sha256', $autorizacao);
        $atual['cadastroExpira'] = $cadastro['expira'];
        unset($atual['cadastroSenha'], $atual['desafioAtual'], $estado['desafios'][$id]);
        return $cadastro; // Confirma somente o e-mail; ainda não autentica o representante.
    });
    return portalRepresentanteResultado($resultado);
}

function portalRepresentanteValidarSessao($conf, $sessao, $registro, $agora = null)
{
    $agora = $agora === null ? time() : $agora;
    if (!is_array($sessao) || !isset($sessao['metodo']) || $sessao['metodo'] !== 'senha' || !isset($sessao['convite'], $sessao['versao'], $sessao['tenant'], $sessao['inicio'], $sessao['ultimo'], $sessao['vinculo'])
        || !hash_equals(portalRepresentanteTenant($conf), $sessao['tenant']) || $sessao['inicio'] + 28800 <= $agora || $sessao['ultimo'] + 1800 <= $agora) return false;
    $acesso = portalRepresentanteAcesso($conf, $sessao['convite']);
    $identidade = portalRepresentanteIdentidade($registro);
    if (!$acesso || empty($acesso['senhaHash']) || !$identidade || $identidade['id'] !== $acesso['representante'] || !hash_equals($acesso['versao'], $sessao['versao'])
        || !hash_equals($sessao['vinculo'], $acesso['vinculo']) || !hash_equals($acesso['vinculo'], portalRepresentanteVinculo($conf, $identidade))) return false;
    return array('empresa' => $acesso['empresa'], 'representante' => $acesso['representante'], 'nome' => $identidade['nome']);
}
