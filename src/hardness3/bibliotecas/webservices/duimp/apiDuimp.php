<?php
namespace hardness;

class ApiDuimp
{
    private $tokenCacheTtl = 50;
    private $clientId = '';
    private $clientSecret = '';
    private $perfil = '';
    private $ambiente = '';
    private $baseUrlAutenticacao = '';
    private $baseUrlDuimp = '';
    private $baseUrlCatalogo = '';
    private $authorizationToken = '';
    private $csrfToken = '';
    private $tokenGeneratedAt = 0;
    private $tokenCacheFile = '';
    private $tudoOk = true;
    private $msgErro = '';

    public function __construct()
    {
        $this->carregarConfiguracoes();
        if ($this->tudoOk) {
            $this->resolverBaseUrls();
            $this->tokenCacheFile = $this->getTokenCacheFile();
            $this->carregarTokenCache();
        }
    }

    public function isTudoOk()
    {
        return $this->tudoOk;
    }

    public function getMsgErro()
    {
        return $this->msgErro;
    }

    public function getAmbiente()
    {
        return $this->ambiente;
    }

    public function getAuthorizationToken()
    {
        return $this->authorizationToken;
    }

    public function getCsrfToken()
    {
        return $this->csrfToken;
    }

    public function setTokens($authorizationToken, $csrfToken)
    {
        $this->authorizationToken = trim((string) $authorizationToken);
        $this->csrfToken = trim((string) $csrfToken);
        if ($this->authorizationToken !== '' && $this->csrfToken !== '') {
            $this->tokenGeneratedAt = time();
            $this->salvarTokenCache();
        }
    }

    public function getPerfil()
    {
        return $this->perfil;
    }

    public function consultarVersaoVigente($numeroDuimp)
    {
        return $this->get('/ext/duimp/' . rawurlencode($numeroDuimp) . '/versoes', array(), true, 'duimp');
    }

    public function consultarDuimp($numeroDuimp, $versao)
    {
        return $this->get('/ext/duimp/' . rawurlencode($numeroDuimp) . '/' . rawurlencode($versao), array(), true, 'duimp');
    }

    public function consultarItensDuimp($numeroDuimp, $versao)
    {
        return $this->get('/ext/duimp/' . rawurlencode($numeroDuimp) . '/' . rawurlencode($versao) . '/itens', array(), true, 'duimp');
    }

    public function consultarItemDuimp($numeroDuimp, $versao, $numeroItem)
    {
        return $this->get('/ext/duimp/' . rawurlencode($numeroDuimp) . '/' . rawurlencode($versao) . '/itens/' . rawurlencode($numeroItem), array(), true, 'duimp');
    }

    public function consultarProduto($cpfCnpjRaiz, $codigo, $versao)
    {
        return $this->get('/ext/produto', array(
            'cpfCnpjRaiz' => $cpfCnpjRaiz,
            'codigo' => $codigo,
            'versao' => $versao,
        ), true, 'catalogo');
    }

    private function carregarConfiguracoes()
    {
        global $g;

        $this->ambiente = strtoupper(trim((string) $this->getConfiguracao('duimpAmbiente')));
        if (empty($this->ambiente)) {
            $this->ambiente = 'HOMOLOGACAO';
        }

        $this->perfil = trim((string) $this->getConfiguracao('duimpPerfil'));
        if (empty($this->perfil)) {
            $this->deuErro('Perfil da DUIMP não configurado.');
            return;
        }

        if ($this->ambiente === 'PRODUCAO') {
            $this->clientId = trim((string) $this->getConfiguracao('duimpClientIdProducao'));
            $this->clientSecret = trim((string) $this->getConfiguracao('duimpClientSecretProducao'));
        } else {
            $this->clientId = trim((string) $this->getConfiguracao('duimpClientIdHomologacao'));
            $this->clientSecret = trim((string) $this->getConfiguracao('duimpClientSecretHomologacao'));
        }

        if (empty($this->clientId)) {
            $this->deuErro('Client ID da DUIMP não configurado para o ambiente selecionado.');
            return;
        }

        if (empty($this->clientSecret)) {
            $this->deuErro('Client Secret da DUIMP não configurado para o ambiente selecionado.');
            return;
        }
    }

    private function resolverBaseUrls()
    {
        if ($this->ambiente === 'PRODUCAO') {
            $host = 'https://portalunico.siscomex.gov.br';
        } else {
            // Para intervenientes privados, a homologação utiliza o ambiente VAL do Portal Único.
            $host = 'https://val.portalunico.siscomex.gov.br';
        }

        $this->baseUrlAutenticacao = $host . '/portal/api';
        $this->baseUrlDuimp = $host . '/duimp-api/api';
        $this->baseUrlCatalogo = $host . '/catp/api';
    }

    private function getConfiguracao($chave)
    {
        global $g;

        if (isset($g['C031']) && is_array($g['C031']) && array_key_exists($chave, $g['C031'])) {
            return $g['C031'][$chave];
        }

        return '';
    }

    private function ensureAuthenticated()
    {
        if (!$this->tudoOk) {
            return false;
        }

        if (!empty($this->authorizationToken) && !empty($this->csrfToken)) {
            return true;
        }

        if ($this->carregarTokenCache()) {
            return true;
        }

        return $this->autenticar();
    }

    private function autenticar()
    {
        $retorno = $this->post('/autenticar/chave-acesso', array(), null, false, 'autenticacao');
        if ($retorno === false) {
            return false;
        }

        if (empty($this->authorizationToken)) {
            $this->deuErro('Não foi possível obter o token de autorização da API DUIMP.');
            return false;
        }

        if (empty($this->csrfToken)) {
            $this->deuErro('Não foi possível obter o token CSRF da API DUIMP.');
            return false;
        }

        $this->tokenGeneratedAt = time();
        $this->salvarTokenCache();

        return true;
    }

    private function get($path, $query = array(), $includeAuth = true, $servico = 'duimp')
    {
        return $this->request('GET', $path, $query, null, $includeAuth, $servico);
    }

    private function post($path, $query = array(), $body = null, $includeAuth = true, $servico = 'duimp')
    {
        return $this->request('POST', $path, $query, $body, $includeAuth, $servico);
    }

    private function request($method, $path, $query = array(), $body = null, $includeAuth = true, $servico = 'duimp', $permitirNovaAutenticacao = true)
    {
        $this->limparErro();

        if ($includeAuth && !$this->ensureAuthenticated()) {
            return false;
        }

        $baseUrl = $this->getBaseUrlServico($servico);
        if ($baseUrl === '') {
            $this->deuErro('Serviço da API DUIMP inválido para a requisição.');
            return false;
        }

        $url = rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        $headers = $this->montarHeaders($includeAuth, $body !== null);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 120);

        if ($body !== null) {
            $payload = json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        }

        $response = curl_exec($curl);
        if ($response === false) {
            $erroCurl = curl_error($curl);
            curl_close($curl);
            $this->deuErro('Erro ao comunicar com a API DUIMP: ' . $erroCurl);
            return false;
        }

        $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $headerSize = (int) curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        curl_close($curl);

        $rawHeaders = substr($response, 0, $headerSize);
        $rawBody = substr($response, $headerSize);
        $headersResposta = $this->parseHeaders($rawHeaders);

        if (isset($headersResposta['set-token']) && $headersResposta['set-token'] !== '') {
            $this->authorizationToken = $headersResposta['set-token'];
        }

        if (isset($headersResposta['x-csrf-token']) && $headersResposta['x-csrf-token'] !== '') {
            $this->csrfToken = $headersResposta['x-csrf-token'];
        }

        if (!empty($this->authorizationToken) && !empty($this->csrfToken)) {
            $this->tokenGeneratedAt = time();
            $this->salvarTokenCache();
        }

        $rawBody = trim($rawBody);
        $bodyDecodificado = array();
        if ($rawBody !== '') {
            $bodyDecodificado = json_decode($rawBody, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $bodyDecodificado = $rawBody;
            }
        }

        if ($status < 200 || $status >= 300) {
            $mensagem = $this->extrairMensagemErro($bodyDecodificado);
            if ($includeAuth && $permitirNovaAutenticacao && $this->deveRenovarToken($status, $mensagem)) {
                $this->limparTokenAtual();
                if ($this->autenticar()) {
                    return $this->request($method, $path, $query, $body, $includeAuth, $servico, false);
                }
            }
            if (empty($mensagem)) {
                $mensagem = 'Resposta HTTP ' . $status . ' ao consumir a API DUIMP.';
            } else {
                $mensagem = 'Resposta HTTP ' . $status . ' ao consumir a API DUIMP. ' . $mensagem;
            }
            $this->deuErro($mensagem);
            return false;
        }

        return $bodyDecodificado;
    }

    private function getBaseUrlServico($servico)
    {
        if ($servico === 'autenticacao') {
            return $this->baseUrlAutenticacao;
        }

        if ($servico === 'catalogo') {
            return $this->baseUrlCatalogo;
        }

        return $this->baseUrlDuimp;
    }

    private function montarHeaders($includeAuth, $enviarJson)
    {
        $headers = array(
            'Accept: application/json',
            'Role-Type: ' . $this->perfil,
            'Client-Id: ' . $this->clientId,
            'Client-Secret: ' . $this->clientSecret,
        );

        if ($includeAuth) {
            $headers[] = 'Authorization: ' . $this->authorizationToken;
            $headers[] = 'X-CSRF-Token: ' . $this->csrfToken;
        }

        if ($enviarJson) {
            $headers[] = 'Content-Type: application/json';
        }

        return $headers;
    }

    private function parseHeaders($rawHeaders)
    {
        $headers = array();
        $linhas = preg_split("/
|
|
/", trim($rawHeaders));
        foreach ($linhas as $linha) {
            if (strpos($linha, ':') === false) {
                continue;
            }

            list($nome, $valor) = explode(':', $linha, 2);
            $headers[strtolower(trim($nome))] = trim($valor);
        }

        return $headers;
    }

    private function extrairMensagemErro($payload)
    {
        if (is_string($payload)) {
            return $payload;
        }

        if (!is_array($payload)) {
            return '';
        }

        $campos = array('message', 'mensagem', 'detail', 'error', 'descricao');
        foreach ($campos as $campo) {
            if (!empty($payload[$campo]) && !is_array($payload[$campo])) {
                return (string) $payload[$campo];
            }
        }

        return '';
    }

    private function deveRenovarToken($status, $mensagem)
    {
        $status = (int) $status;
        if ($status === 401 || $status === 403) {
            return true;
        }

        if ($status !== 422) {
            return false;
        }

        $mensagem = strtolower((string) $mensagem);
        if ($mensagem === '') {
            return false;
        }

        return (strpos($mensagem, 'token') !== false
            || strpos($mensagem, 'autentica') !== false
            || strpos($mensagem, 'csrf') !== false
            || strpos($mensagem, 'opera') !== false && strpos($mensagem, 'autorizad') !== false);
    }

    private function carregarTokenCache()
    {
        if ($this->tokenCacheFile === '' || !is_file($this->tokenCacheFile)) {
            return false;
        }

        $conteudo = @file_get_contents($this->tokenCacheFile);
        if ($conteudo === false || trim($conteudo) === '') {
            return false;
        }

        $dados = json_decode($conteudo, true);
        if (!is_array($dados)) {
            return false;
        }

        $authorizationToken = isset($dados['authorizationToken']) ? trim((string) $dados['authorizationToken']) : '';
        $csrfToken = isset($dados['csrfToken']) ? trim((string) $dados['csrfToken']) : '';
        $generatedAt = isset($dados['generatedAt']) ? (int) $dados['generatedAt'] : 0;

        if ($authorizationToken === '' || $csrfToken === '' || !$this->tokenAindaValido($generatedAt)) {
            $this->limparTokenCache();
            return false;
        }

        $this->authorizationToken = $authorizationToken;
        $this->csrfToken = $csrfToken;
        $this->tokenGeneratedAt = $generatedAt;
        return true;
    }

    private function salvarTokenCache()
    {
        if ($this->tokenCacheFile === '' || $this->authorizationToken === '' || $this->csrfToken === '') {
            return false;
        }

        $diretorio = dirname($this->tokenCacheFile);
        if (!is_dir($diretorio)) {
            @mkdir($diretorio, 0777, true);
        }

        if (!is_dir($diretorio)) {
            return false;
        }

        if ($this->tokenGeneratedAt <= 0) {
            $this->tokenGeneratedAt = time();
        }

        $dados = array(
            'authorizationToken' => $this->authorizationToken,
            'csrfToken' => $this->csrfToken,
            'generatedAt' => $this->tokenGeneratedAt,
        );

        return @file_put_contents($this->tokenCacheFile, json_encode($dados)) !== false;
    }

    private function limparTokenAtual()
    {
        $this->authorizationToken = '';
        $this->csrfToken = '';
        $this->tokenGeneratedAt = 0;
        $this->limparTokenCache();
    }

    private function limparTokenCache()
    {
        if ($this->tokenCacheFile !== '' && is_file($this->tokenCacheFile)) {
            @unlink($this->tokenCacheFile);
        }
    }

    private function tokenAindaValido($generatedAt)
    {
        $generatedAt = (int) $generatedAt;
        if ($generatedAt <= 0) {
            return false;
        }

        return ((time() - $generatedAt) < $this->tokenCacheTtl);
    }

    private function getTokenCacheFile()
    {
        $hash = md5(implode('|', array(
            $this->getTenantIdentifier(),
            $this->getEmpresaIdentifier(),
            $this->ambiente,
            $this->perfil,
            $this->clientId,
        )));

        return rtrim($this->getTokenCacheDir(), '/') . '/duimp-token-cache-' . $hash . '.json';
    }

    private function getTokenCacheDir()
    {
        global $g, $confUsuario;

        if (isset($g['pathDados']) && trim((string) $g['pathDados']) !== '') {
            return rtrim((string) $g['pathDados'], '/') . '/tmp/duimp';
        }

        $tenant = $this->getTenantIdentifier();
        if (isset($confUsuario['pathRaiz']) && trim((string) $confUsuario['pathRaiz']) !== '') {
            return rtrim((string) $confUsuario['pathRaiz'], '/') . '/dados_usuarios/' . $tenant . '/tmp/duimp';
        }

        return 'dados_usuarios/' . $tenant . '/tmp/duimp';
    }

    private function getTenantIdentifier()
    {
        global $confUsuario, $g;

        if (isset($confUsuario['dbDatabase']) && trim((string) $confUsuario['dbDatabase']) !== '') {
            return trim((string) $confUsuario['dbDatabase']);
        }

        if (isset($g['pathDados']) && preg_match('#dados_usuarios/([^/]+)/#', (string) $g['pathDados'], $matches)) {
            return $matches[1];
        }

        if (isset($_REQUEST['db']) && trim((string) $_REQUEST['db']) !== '') {
            return trim((string) $_REQUEST['db']);
        }

        return 'default';
    }

    private function getEmpresaIdentifier()
    {
        global $g;

        if (isset($g['empresaAtual']) && (string) $g['empresaAtual'] !== '') {
            return (string) $g['empresaAtual'];
        }

        if (isset($_REQUEST['p']) && (string) $_REQUEST['p'] !== '') {
            return (string) $_REQUEST['p'];
        }

        return '0';
    }

    private function limparErro()
    {
        $this->tudoOk = true;
        $this->msgErro = '';
    }

    private function deuErro($mensagem)
    {
        $this->tudoOk = false;
        $this->msgErro = $mensagem;
    }
}
