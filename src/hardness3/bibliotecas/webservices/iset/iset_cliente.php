<?php
namespace hardness;

require_once __DIR__ . '/iset_configuracoes.php';
require_once __DIR__ . '/iset_sku.php';
require_once __DIR__ . '/iset_logs.php';

/**
 * Cliente HTTP da API ISET.
 *
 * Não contém regras de importação nem grava dados de pedidos no ERP. Ele apenas
 * centraliza autenticação, retenção do token e chamadas autenticadas à API.
 */
class iset_cliente_
{
    const TOKEN_CAMPO = 'IsetApiAccessToken';
    const TOKEN_EXPIRACAO_CAMPO = 'IsetApiTokenExpiraEm';
    const TOKEN_MARGEM_RENOVACAO = 30;
    const TOKEN_VALIDADE_PADRAO = 900;

    private $configuracao;
    private $token = '';
    private $tokenExpiraEm = 0;

    public function __construct(array $configuracao = array()) {
        $this->configuracao = $configuracao ?: $this->lerConfiguracao();
    }

    /** Retorna a configuração necessária para autenticar, sem expor a chave em mensagens. */
    private function lerConfiguracao() {
        global $g;
        return array(
            'IsetUrlBase' => isset($g['C031']['IsetUrlBase']) ? (string) $g['C031']['IsetUrlBase'] : '',
            'IsetIdentificador' => isset($g['C031']['IsetIdentificador']) ? (string) $g['C031']['IsetIdentificador'] : '',
            'IsetChaveAcesso' => isset($g['C031']['IsetChaveAcesso']) ? (string) $g['C031']['IsetChaveAcesso'] : '',
        );
    }

    /**
     * Executa uma chamada autenticada. O caminho é relativo a /ws/v1.
     * Retorno padronizado: ok, status, dados e mensagem.
     */
    public function requisitar($metodo, $caminho, $corpo = null) {
        $metodo = strtoupper(trim((string) $metodo));
        if (!in_array($metodo, array('GET', 'POST', 'PUT', 'DELETE'), true)) {
            return $this->erro(0, 'Método HTTP não suportado pela biblioteca ISET.');
        }
        if (!is_string($caminho) || strpos($caminho, '/') !== 0 || strpos($caminho, '..') !== false || $caminho === '/oauth') {
            return $this->erro(0, 'Caminho da API ISET inválido.');
        }

        $token = $this->obterToken();
        $chamadas = isset($token['diagnostico']) ? array($token['diagnostico']) : array();
        if (!$token['ok']) {
            return array_merge($token, array('requisicaoExecutada' => false, 'chamadas' => $chamadas));
        }

        $resposta = $this->executarHttp($metodo, $caminho, $corpo, $token['dados']['token']);
        if (isset($resposta['diagnostico'])) $chamadas[] = $resposta['diagnostico'];
        $executada = !empty($resposta['diagnostico']['executada']);
        // Repetir insert após uma resposta incerta pode criar outro cadastro.
        if ($resposta['status'] === 401 && $caminho !== '/product/insert') {
            // Um token recusado não é reutilizável. Limpa-o e tenta autenticar uma vez.
            $this->limparToken();
            $token = $this->obterToken();
            if (isset($token['diagnostico'])) $chamadas[] = $token['diagnostico'];
            if (!$token['ok']) {
                return array_merge($token, array('requisicaoExecutada' => $executada, 'chamadas' => $chamadas));
            }
            $resposta = $this->executarHttp($metodo, $caminho, $corpo, $token['dados']['token']);
            if (isset($resposta['diagnostico'])) $chamadas[] = $resposta['diagnostico'];
            $executada = $executada || !empty($resposta['diagnostico']['executada']);
        }

        if ($resposta['ok']) {
            // A ISET renova a validade do token a cada chamada autenticada.
            $this->salvarToken($token['dados']['token'], time() + self::TOKEN_VALIDADE_PADRAO);
        }

        return array_merge($resposta, array('requisicaoExecutada' => $executada, 'chamadas' => $chamadas));
    }

    /** Autentica somente quando não há token válido disponível para a empresa atual. */
    public function obterToken() {
        $configuracaoInvalida = $this->validarConfiguracao();
        if ($configuracaoInvalida !== '') {
            return $this->erro(0, $configuracaoInvalida);
        }

        $this->carregarToken();
        if ($this->token !== '' && $this->tokenExpiraEm > time() + self::TOKEN_MARGEM_RENOVACAO) {
            return $this->sucesso(200, array('token' => $this->token), 'Token ISET reutilizado.');
        }

        $resposta = $this->executarHttp('POST', '/oauth', null, null, true);
        if (!$resposta['ok']) {
            if ($resposta['status'] === 401) {
                return array_merge($resposta, array('mensagem' => 'A ISET recusou o identificador ou a chave de acesso configurados.'));
            }
            return $resposta;
        }
        if (!is_array($resposta['dados']) || empty($resposta['dados']['token'])) {
            return array_merge($resposta, array('ok' => false, 'mensagem' => 'A ISET respondeu à autenticação, mas não retornou um token válido.'));
        }

        $expiraEm = $this->interpretarExpiracao(isset($resposta['dados']['expires_in']) ? $resposta['dados']['expires_in'] : null);
        $this->salvarToken($resposta['dados']['token'], $expiraEm);
        return array_merge($this->sucesso(200, array('token' => $resposta['dados']['token']), 'Autenticação ISET concluída.'), array('diagnostico' => isset($resposta['diagnostico']) ? $resposta['diagnostico'] : array('executada' => false)));
    }

    /** Valida autenticação e uma chamada protegida, sem criar ou alterar dados na ISET. */
    public function testarComunicacao() {
        $resposta = $this->requisitar('GET', '/payment/list');
        if (!$resposta['ok']) {
            return $resposta;
        }
        return $this->sucesso(200, array(), 'Conexão autenticada e comunicação com a API ISET confirmadas.');
    }

    /** Consulta um pedido da ISET sem criar ou alterar qualquer dado. */
    public function buscarPedido($pedidoId) {
        $pedidoId = filter_var($pedidoId, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
        if (!$pedidoId) {
            return $this->erro(0, 'Informe um número de pedido ISET válido.');
        }
        return $this->requisitar('GET', '/order/' . $pedidoId);
    }

    /** Consulta um produto pelo SKU. O SKU é sempre um segmento de URL. */
    public function buscarProdutoPorSku($sku) {
        $sku = (new ISET_SKU())->paraIset($sku);
        if ($sku === '') {
            return $this->erro(0, 'Informe um SKU para consultar o produto na ISET.');
        }
        return $this->requisitar('GET', '/product/sku/' . rawurlencode($sku));
    }

    /** Consulta um produto pelo identificador informado pela própria ISET. */
    public function buscarProdutoPorId($produtoId) {
        $produtoId = trim((string) $produtoId);
        if ($produtoId === '' || strlen($produtoId) > 100 || preg_match('/[[:cntrl:]]/', $produtoId)) {
            return $this->erro(0, 'Informe um identificador de produto ISET válido.');
        }
        return $this->requisitar('GET', '/product/' . rawurlencode($produtoId));
    }

    /** Cria um produto simples usando o contrato /product/insert da ISET. */
    public function inserirProduto(array $produto) {
        if (isset($produto['cod_ref'])) $produto['cod_ref'] = (new ISET_SKU())->paraIset($produto['cod_ref']);
        return $this->requisitar('POST', '/product/insert', $produto);
    }

    /** Atualiza (inclusive desativa) um produto simples da ISET. */
    public function atualizarProduto($produtoId, array $produto) {
        $produtoId = filter_var($produtoId, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
        if (!$produtoId) {
            return $this->erro(0, 'ID de produto ISET inválido.');
        }
        if (isset($produto['cod_ref'])) $produto['cod_ref'] = (new ISET_SKU())->paraIset($produto['cod_ref']);
        return $this->requisitar('POST', '/product/update/' . $produtoId, $produto);
    }

    /** Atualiza somente o status de um pedido já existente na ISET. */
    public function atualizarStatusPedido($pedidoId, $statusId) {
        $pedidoId = filter_var($pedidoId, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
        $statusId = filter_var($statusId, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
        if (!$pedidoId) {
            return $this->erro(0, 'ID de pedido ISET inválido.');
        }
        if (!$statusId) {
            return $this->erro(0, 'ID de status ISET inválido.');
        }

        return $this->requisitar('PUT', '/order/' . $pedidoId . '/status', array(
            'status_id' => (string) $statusId,
        ));
    }

    /** Cadastra um webhook usando o contrato oficial da ISET. */
    public function criarWebhook($tipo, $acao, $url) {
        return $this->requisitar('POST', '/webhook', array(
            'type' => trim((string) $tipo),
            'action' => trim((string) $acao),
            'url' => trim((string) $url),
        ));
    }

    /** Remove um webhook usando o mesmo tipo, ação e URL do cadastro. */
    public function removerWebhook($tipo, $acao, $url) {
        return $this->requisitar('DELETE', '/webhook', array(
            'type' => trim((string) $tipo),
            'action' => trim((string) $acao),
            'url' => trim((string) $url),
        ));
    }

    protected function executarHttp($metodo, $caminho, $corpo, $token = null, $autenticacaoBasica = false) {
        if (!function_exists('curl_init')) {
            return $this->erro(0, 'A extensão cURL não está disponível neste ambiente.');
        }

        $url = rtrim($this->configuracao['IsetUrlBase'], '/') . '/ws/v1' . $caminho;
        $headers = array('Accept: application/json', 'User-Agent: Hardness-ISET/1.0');
        if ($token !== null) {
            $headers[] = 'access-token: ' . $token;
        }

        $curl = curl_init($url);
        $inicio = microtime(true);
        $cabecalhos = array();
        curl_setopt($curl, CURLOPT_HEADERFUNCTION, function ($curl, $linha) use (&$cabecalhos) {
            if (preg_match('/^HTTP\//', $linha)) $cabecalhos = array();
            $partes = explode(':', $linha, 2);
            $nome = strtolower(trim($partes[0]));
            if (count($partes) === 2 && in_array($nome, array('content-type', 'date', 'age', 'cache-control', 'expires', 'via', 'x-cache', 'x-request-id', 'request-id', 'x-requestid', 'x-correlation-id', 'x-ms-request-id', 'x-amzn-requestid', 'x-amz-cf-id', 'cf-ray'), true)) {
                $cabecalhos[$nome] = substr(integracoesMascararTextoSensivel(trim($partes[1])), 0, 1000);
            }
            return strlen($linha);
        });
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $metodo);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        if ($autenticacaoBasica) {
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($curl, CURLOPT_USERPWD, $this->configuracao['IsetIdentificador'] . ':' . $this->configuracao['IsetChaveAcesso']);
        } elseif ($corpo !== null) {
            $json = json_encode($corpo);
            if ($json === false) {
                curl_close($curl);
                return $this->erro(0, 'Não foi possível transformar os dados da requisição ISET em JSON.');
            }
            $headers[] = 'Content-Type: application/json';
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
        }

        $conteudo = curl_exec($curl);
        $erroCurl = curl_error($curl);
        $numeroErro = curl_errno($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $diagnostico = $this->diagnosticoHttp($metodo, $caminho, $conteudo, $status, $cabecalhos, $inicio, $numeroErro, $erroCurl);
        if ($conteudo === false) {
            return array_merge($this->erro(0, 'Não foi possível comunicar com a ISET: ' . ($erroCurl ?: 'erro de rede sem diagnóstico.')), array('diagnostico' => $diagnostico));
        }

        $dados = json_decode($conteudo, true);
        if ($dados === null && trim($conteudo) !== '' && json_last_error() !== JSON_ERROR_NONE) {
            return array_merge($this->erro($status, 'A ISET retornou uma resposta inválida (não JSON).'), array('diagnostico' => $diagnostico));
        }
        if ($status < 200 || $status >= 300) {
            $mensagemApi = is_array($dados) && !empty($dados['message']) ? ': ' . $dados['message'] : '';
            return array_merge($this->erro($status, 'A ISET retornou HTTP ' . ($status ?: 'sem resposta') . $mensagemApi, $dados), array('diagnostico' => $diagnostico));
        }

        return array_merge($this->sucesso($status, is_array($dados) ? $dados : array(), 'Chamada à API ISET concluída.'), array('diagnostico' => $diagnostico));
    }

    /** O corpo é sanitizado antes do corte; cortar primeiro poderia expor parte de uma credencial. */
    protected function diagnosticoHttp($metodo, $caminho, $conteudo, $status, array $cabecalhos, $inicio, $numeroErro, $erro)
    {
        $dados = json_decode((string) $conteudo, true);
        $valido = $conteudo !== false && json_last_error() === JSON_ERROR_NONE;
        $corpo = $valido ? json_encode(integracoesLimparDadosLog($dados), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE) : integracoesMascararTextoSensivel((string) $conteudo);
        // Também remove credenciais conhecidas caso o servidor as ecoe sem nome de campo.
        foreach (array(isset($this->configuracao['IsetChaveAcesso']) ? $this->configuracao['IsetChaveAcesso'] : '',
            isset($this->configuracao['IsetIdentificador']) ? $this->configuracao['IsetIdentificador'] : '', $this->token) as $segredo) {
            if (!is_string($segredo) || $segredo === '') continue;
            $corpo = str_replace($segredo, '********', $corpo);
            $erro = str_replace($segredo, '********', $erro);
            foreach ($cabecalhos as $nome => $valor) $cabecalhos[$nome] = str_replace($segredo, '********', $valor);
        }
        $corpo = json_decode(json_encode((string) $corpo, JSON_INVALID_UTF8_SUBSTITUTE), true);
        $truncado = strlen($corpo) > 65536;
        $corpo = substr($corpo, 0, 65536);
        while ($corpo !== '' && !preg_match('//u', $corpo)) $corpo = substr($corpo, 0, -1);
        return array('executada' => true, 'metodo' => $metodo, 'endpoint' => $caminho,
            'horario' => date('c', (int) $inicio), 'duracaoMs' => (int) round((microtime(true) - $inicio) * 1000),
            'statusHttp' => (int) $status, 'statusJson' => is_array($dados) && isset($dados['status']) ? $dados['status'] : null,
            'erroTransporte' => (int) $numeroErro, 'mensagemTransporte' => integracoesMascararTextoSensivel($erro),
            'jsonValido' => $valido, 'tamanhoOriginalBytes' => strlen((string) $conteudo),
            'corpoTruncado' => $truncado, 'corpo' => $corpo, 'cabecalhos' => $cabecalhos);
    }

    /** A ausência precisa ser resposta do GET de produto, não de oauth ou de um proxy. */
    public function ausenciaProdutoConfirmada(array $resposta, $sku)
    {
        $sku = (new ISET_SKU())->paraIset($sku);
        $dados = isset($resposta['dados']) ? $resposta['dados'] : array();
        $http = isset($resposta['diagnostico']) ? $resposta['diagnostico'] : array();
        return empty($resposta['ok']) && isset($resposta['status']) && (int) $resposta['status'] === 404
            && !empty($http['executada']) && !empty($http['jsonValido']) && empty($http['erroTransporte'])
            && isset($http['metodo'], $http['endpoint'], $http['statusHttp']) && $http['metodo'] === 'GET'
            && $http['endpoint'] === '/product/sku/' . rawurlencode($sku) && (int) $http['statusHttp'] === 404
            && is_array($dados) && !array_diff(array_keys($dados), array('status', 'message', 'result', 'totalFound', 'totalCount'))
            && isset($dados['status'], $dados['message']) && (string) $dados['status'] === '404'
            && is_string($dados['message']) && trim($dados['message']) === 'Product not found with SKU code: ' . $sku
            && (!isset($dados['result']) || $dados['result'] === array())
            && (!isset($dados['totalFound']) || (string) $dados['totalFound'] === '0')
            && (!isset($dados['totalCount']) || (string) $dados['totalCount'] === '0');
    }

    private function validarConfiguracao() {
        foreach (array('IsetUrlBase', 'IsetIdentificador', 'IsetChaveAcesso') as $campo) {
            if (empty($this->configuracao[$campo])) {
                return 'Configure o domínio, o identificador e a chave de acesso da ISET antes de comunicar com a API.';
            }
        }
        return '';
    }

    private function interpretarExpiracao($expiresIn) {
        $valor = is_numeric($expiresIn) ? (int) $expiresIn : 0;
        if ($valor > time()) {
            return $valor;
        }
        if ($valor > 0) {
            return time() + $valor;
        }
        return time() + self::TOKEN_VALIDADE_PADRAO;
    }

    /** Mantém somente texto útil para diagnóstico de resposta não JSON; não armazena o corpo bruto. */
    private function resumoTextoResposta($conteudo) {
        $texto = trim(preg_replace('/\s+/', ' ', strip_tags((string) $conteudo)));
        return substr($texto, 0, 600);
    }

    private function carregarToken() {
        $cache = (new ISET_CONFIGURACOES())->obterTokenCache(self::TOKEN_CAMPO, self::TOKEN_EXPIRACAO_CAMPO);
        $this->token = $cache['token'];
        $this->tokenExpiraEm = $cache['expiraEm'];
    }

    private function salvarToken($token, $expiraEm) {
        if (!(new ISET_CONFIGURACOES())->salvarTokenCache(self::TOKEN_CAMPO, self::TOKEN_EXPIRACAO_CAMPO, $token, $expiraEm)) {
            return;
        }
        $this->token = (string) $token;
        $this->tokenExpiraEm = (int) $expiraEm;
    }

    private function limparToken() {
        $this->salvarToken('', 0);
    }

    private function sucesso($status, array $dados, $mensagem) {
        return array('ok' => true, 'status' => (int) $status, 'dados' => $dados, 'mensagem' => $mensagem);
    }

    private function erro($status, $mensagem, $dados = array()) {
        return array('ok' => false, 'status' => (int) $status, 'dados' => is_array($dados) ? $dados : array(), 'mensagem' => $mensagem);
    }
}

if (function_exists(__NAMESPACE__ . '\\definirClasse')) {
    definirClasse('ISET_CLIENTE');
}

if (!class_exists(__NAMESPACE__ . '\\iset_cliente', false)) {
    class_alias(__NAMESPACE__ . '\\iset_cliente_', __NAMESPACE__ . '\\iset_cliente');
}
