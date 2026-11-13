<?php
namespace hardness;

/**
 * Lê e grava somente as configurações C031 da integração ISET.
 */
class iset_configuracoes_
{
    const CAMPOS = [
        'IsetUrlBase',
        'IsetIdentificador',
        'IsetChaveAcesso',
        'IsetStatusImportacao',
        'IsetD022IdTransportadora',
        'IsetD027IdPagamento',
        'IsetModoTesteImportacao',
        'IsetStatusPedidosMapeamentoJson',
        'IsetProdutosConfiguracaoJson',
        'IsetProdutosPermitirCompraPorEstoque',
    ];

    public function obter()
    {
        global $g;

        $configuracao = [];
        foreach (self::CAMPOS as $campo) {
            $configuracao[$campo] = isset($g['C031'][$campo]) ? (string) $g['C031'][$campo] : '';
        }

        return $configuracao;
    }

    public function salvar($urlBase, $identificador, $chave, $status, $transportadora, $portadorPagamento, $modoTeste)
    {
        $erro = $this->validar($urlBase, $identificador, $chave, $status, $transportadora, $portadorPagamento);
        if ($erro !== '') {
            return [false, $erro];
        }

        $valores = [
            'IsetUrlBase'              => [rtrim(trim((string) $urlBase), '/'), 'Domínio HTTPS da loja ISET usado para acessar a API.'],
            'IsetIdentificador'         => [trim((string) $identificador), 'Identificador (ID de usuário) da API ISET.'],
            'IsetChaveAcesso'           => [trim((string) $chave), 'Chave de acesso da API ISET.'],
            'IsetStatusImportacao'      => [trim((string) $status), 'IDs dos status ISET permitidos para importação.'],
            'IsetD022IdTransportadora'  => [trim((string) $transportadora), 'D022_Id padrão usado em pedidos ISET com frete.'],
            'IsetD027IdPagamento'       => [trim((string) $portadorPagamento), 'D027_Id padrão usado para pagamentos de pedidos ISET sem mapeamento específico.'],
            'IsetModoTesteImportacao'   => [$modoTeste === 'S' ? 'S' : 'N', 'Ativa a importação de teste da ISET.'],
        ];

        foreach ($valores as $campo => $dados) {
            $resultado = $this->gravarCampo($campo, $dados[0], $dados[1]);
            if ($resultado !== true) {
                return [false, $resultado];
            }
        }

        if (function_exists('processarCacheTabela')) {
            processarCacheTabela('C031');
        }

        return [true, 'Configurações salvas.'];
    }

    public function testar(array $configuracao)
    {
        $resposta = (new ISET_CLIENTE($configuracao))->testarComunicacao();

        return [$resposta['ok'], $resposta['mensagem']];
    }

    public function obterTokenCache($campoToken, $campoExpiracao)
    {
        global $g;

        return array(
            'token'     => isset($g['C031'][$campoToken]) ? (string) $g['C031'][$campoToken] : '',
            'expiraEm'  => isset($g['C031'][$campoExpiracao]) ? (int) $g['C031'][$campoExpiracao] : 0,
        );
    }

    public function salvarTokenCache($campoToken, $campoExpiracao, $token, $expiraEm)
    {
        $resultado = $this->gravarCampo($campoToken, (string) $token, 'Cache interno do token da API ISET.');
        if ($resultado !== true) {
            return false;
        }

        return $this->gravarCampo($campoExpiracao, (string) (int) $expiraEm, 'Validade do cache interno do token da API ISET.') === true;
    }

    /** Mapeia os status locais que devem ser enviados para cada loja ISET. */
    public function obterStatusPedidosMapeamento()
    {
        $configuracao = $this->obter();
        $json = isset($configuracao['IsetStatusPedidosMapeamentoJson'])
            ? trim((string) $configuracao['IsetStatusPedidosMapeamentoJson']) : '';

        // A configuração ainda pode não existir em bases que não receberam a
        // atualização. Nesse caso, nenhum status deve ser enviado até o mapa ser salvo.
        if ($json === '') {
            $json = '[]';
        }

        return self::validarJsonStatusPedidos($json);
    }

    public function salvarStatusPedidosMapeamento($json)
    {
        $validacao = self::validarJsonStatusPedidos($json);
        if (!$validacao['ok']) {
            return array(false, implode(' ', $validacao['erros']));
        }

        $resultado = $this->gravarCampo(
            'IsetStatusPedidosMapeamentoJson',
            $validacao['json'],
            'Mapeamento entre status de pedidos do Hardness e da ISET.'
        );
        if ($resultado !== true) {
            return array(false, $resultado);
        }
        if (function_exists('processarCacheTabela')) {
            processarCacheTabela('C031');
        }

        return array(true, 'Mapeamento de status dos pedidos salvo.');
    }

    public static function jsonStatusPedidosPadrao()
    {
        return json_encode(self::statusPedidosPadrao(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public static function statusPedidosPadrao()
    {
        return array(
            array('statusHardness' => 0, 'statusISET' => 2),
            array('statusHardness' => 8, 'statusISET' => 3),
            array('statusHardness' => 2, 'statusISET' => 6),
            array('statusHardness' => 3, 'statusISET' => 7),
            array('statusHardness' => 4, 'statusISET' => 9),
            array('statusHardness' => 5, 'statusISET' => 16),
        );
    }

    /**
     * O schema é mantido fechado para evitar que uma configuração incorreta
     * seja aceita silenciosamente e deixe de atualizar pedidos na ISET.
     */
    public static function validarJsonStatusPedidos($json)
    {
        $json = trim((string) $json);
        if ($json === '') {
            $json = '[]';
        }

        $dados = json_decode($json, true);
        if (!is_array($dados) || json_last_error() !== JSON_ERROR_NONE) {
            $erro = function_exists('json_last_error_msg') ? json_last_error_msg() : 'JSON inválido';
            return array('ok' => false, 'erros' => array('JSON inválido: ' . $erro . '.'), 'mapa' => array(), 'json' => '');
        }
        if (substr($json, 0, 1) !== '[' || ($dados && array_keys($dados) !== range(0, count($dados) - 1))) {
            return array('ok' => false, 'erros' => array('O mapeamento deve ser uma lista JSON.'), 'mapa' => array(), 'json' => '');
        }

        $erros = array();
        $mapa = array();
        $normalizado = array();
        foreach ($dados as $indice => $item) {
            $rotulo = 'Mapeamento[' . $indice . ']';
            if (!is_array($item)) {
                $erros[] = $rotulo . ' deve ser um objeto.';
                continue;
            }
            if (!array_key_exists('statusHardness', $item) || !array_key_exists('statusISET', $item)) {
                $erros[] = $rotulo . ' deve informar statusHardness e statusISET.';
                continue;
            }
            foreach ($item as $campo => $valor) {
                if ($campo !== 'statusHardness' && $campo !== 'statusISET') {
                    $erros[] = $rotulo . ' possui a propriedade não permitida "' . $campo . '".';
                }
            }
            if (!is_int($item['statusHardness']) || $item['statusHardness'] < 0) {
                $erros[] = $rotulo . ' deve informar statusHardness como inteiro maior ou igual a zero.';
                continue;
            }
            if (!is_int($item['statusISET']) || $item['statusISET'] <= 0) {
                $erros[] = $rotulo . ' deve informar statusISET como inteiro maior que zero.';
                continue;
            }

            $statusHardness = (string) $item['statusHardness'];
            if (isset($mapa[$statusHardness])) {
                $erros[] = 'O status Hardness ' . $statusHardness . ' foi informado mais de uma vez.';
                continue;
            }

            $mapa[$statusHardness] = (int) $item['statusISET'];
            $normalizado[] = array(
                'statusHardness' => (int) $item['statusHardness'],
                'statusISET' => (int) $item['statusISET'],
            );
        }

        return array(
            'ok' => !$erros,
            'erros' => array_values(array_unique($erros)),
            'mapa' => !$erros ? $mapa : array(),
            'json' => !$erros ? json_encode($normalizado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '',
        );
    }

    /** Configuração declarativa do catálogo de produtos, isolada por empresa em C031. */
    public function obterProdutosConfiguracao()
    {
        $json = isset($this->obter()['IsetProdutosConfiguracaoJson'])
            ? trim((string) $this->obter()['IsetProdutosConfiguracaoJson']) : '';
        if ($json === '') {
            $json = self::jsonProdutosPadrao();
        }

        return self::validarJsonProdutos($json);
    }

    public function salvarProdutosConfiguracao($json)
    {
        $validacao = self::validarJsonProdutos($json);
        if (!$validacao['ok']) {
            return array(false, implode(' ', $validacao['erros']));
        }

        $resultado = $this->gravarCampo(
            'IsetProdutosConfiguracaoJson',
            $validacao['json'],
            'Configuração declarativa dos campos enviados para produtos ISET.'
        );
        if ($resultado !== true) {
            return array(false, $resultado);
        }
        if (function_exists('processarCacheTabela')) {
            processarCacheTabela('C031');
        }
        return array(true, 'Configuração de produtos salva.');
    }

    /** Por padrão, a disponibilidade de compra é derivada do campo quantity. */
    public function permitirCompraPorEstoqueAtivo()
    {
        $configuracao = $this->obter();
        return !isset($configuracao['IsetProdutosPermitirCompraPorEstoque']) || strtoupper(trim((string) $configuracao['IsetProdutosPermitirCompraPorEstoque'])) !== 'N';
    }

    public function salvarPermitirCompraPorEstoque($ativo)
    {
        $resultado = $this->gravarCampo(
            'IsetProdutosPermitirCompraPorEstoque',
            $ativo === 'S' ? 'S' : 'N',
            'Controla allow_buy da ISET a partir do campo configurado como quantity.'
        );
        if ($resultado !== true) return array(false, $resultado);
        if (function_exists('processarCacheTabela')) processarCacheTabela('C031');
        return array(true, 'Controle de permitir compra pelo estoque salvo.');
    }

    public static function jsonProdutosPadrao()
    {
        return json_encode(self::produtosPadrao(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public static function produtosPadrao()
    {
        return array(
            'sku' => array(array('titulo' => 'SKU', 'origem' => 'D001_Codigo_Produto', 'payload' => 'cod_ref', 'tipo' => 'texto', 'visivel' => true, 'enviar' => true, 'obrigatorio' => true)),
            'nome' => array(array('titulo' => 'Produto', 'origem' => 'D001_Descricao_Produto', 'payload' => 'name', 'tipo' => 'texto', 'visivel' => true, 'enviar' => true, 'obrigatorio' => true)),
            'preco' => array(array('titulo' => 'Preço 1', 'origem' => 'D009_Preco_1', 'payload' => 'price', 'tipo' => 'numero', 'visivel' => true, 'enviar' => true, 'obrigatorio' => true)),
            'estoque' => array(array('titulo' => 'Estoque líquido', 'origem' => 'D009_Quantidade_Estoque_Liquido', 'payload' => 'quantity', 'tipo' => 'inteiro', 'visivel' => true, 'enviar' => true, 'obrigatorio' => true)),
            'status' => array(array('titulo' => 'Status ISET', 'origem' => 'ISET_Status_Produto', 'payload' => 'status', 'tipo' => 'inteiro', 'visivel' => false, 'enviar' => true, 'obrigatorio' => true)),
            'compra' => array(array('titulo' => 'Permitir compra', 'origem' => 'ISET_Allow_Buy', 'payload' => 'allow_buy', 'tipo' => 'booleano', 'visivel' => true, 'enviar' => true, 'obrigatorio' => false)),
            'imagens' => array(array('titulo' => 'Fotos', 'origem' => 'T172_Imagens', 'payload' => 'images', 'tipo' => 'imagens', 'visivel' => true, 'enviar' => true, 'obrigatorio' => false)),
        );
    }

    /**
     * Aceita somente o schema conhecido. A lista permitida é intencionalmente
     * pequena: origem jamais é interpolada como SQL fornecido pelo usuário.
     */
    public static function validarJsonProdutos($json)
    {
        $json = trim((string) $json);
        $dados = json_decode($json, true);
        if (!is_array($dados) || json_last_error() !== JSON_ERROR_NONE) {
            $erro = function_exists('json_last_error_msg') ? json_last_error_msg() : 'JSON inválido';
            return array('ok' => false, 'erros' => array('JSON inválido: ' . $erro . '.'), 'campos' => array(), 'json' => '');
        }

        $erros = array(); $campos = array(); $ids = array(); $origens = array(); $payloads = array();
        $permitidas = array_flip(self::origensProdutosPermitidas());
        foreach ($dados as $grupo => $lista) {
            if (!is_string($grupo) || trim($grupo) === '' || !is_array($lista) || !$lista) {
                $erros[] = 'Cada grupo deve ter um nome e uma lista não vazia de campos.';
                continue;
            }
            foreach ($lista as $indice => $campo) {
                $rotulo = 'Campo ' . $grupo . '[' . $indice . ']';
                if (!is_array($campo)) { $erros[] = $rotulo . ' deve ser um objeto.'; continue; }
                $faltando = false;
                foreach (array('titulo', 'origem', 'payload', 'tipo', 'visivel', 'enviar', 'obrigatorio') as $propriedade) {
                    if (!array_key_exists($propriedade, $campo)) { $erros[] = $rotulo . ' não informa "' . $propriedade . '".'; $faltando = true; }
                }
                if ($faltando) continue;
                $id = isset($campo['id']) ? trim((string) $campo['id']) : $grupo . '_' . $indice;
                $titulo = isset($campo['titulo']) ? trim((string) $campo['titulo']) : '';
                $origem = isset($campo['origem']) ? trim((string) $campo['origem']) : '';
                $payload = isset($campo['payload']) ? trim((string) $campo['payload']) : '';
                $tipo = isset($campo['tipo']) ? trim((string) $campo['tipo']) : '';
                if ($id === '' || !preg_match('/^[a-zA-Z][a-zA-Z0-9_-]{0,63}$/', $id)) $erros[] = $rotulo . ' possui id inválido.';
                if ($titulo === '') $erros[] = $rotulo . ' possui título vazio.';
                if (!isset($permitidas[$origem])) $erros[] = $rotulo . ' usa origem não permitida: ' . ($origem ?: '(vazia)') . '.';
                if (!preg_match('/^[a-z][a-z0-9_]{0,63}$/', $payload)) $erros[] = $rotulo . ' possui payload inválido.';
                if (!in_array($tipo, array('texto', 'numero', 'inteiro', 'booleano', 'imagens'), true)) $erros[] = $rotulo . ' possui tipo inválido.';
                if ($tipo === 'imagens' && $origem !== 'T172_Imagens') $erros[] = $rotulo . ' do tipo imagens deve usar exclusivamente a origem T172_Imagens.';
                if ($tipo === 'imagens' && !empty($campo['enviar']) && $payload !== 'images') $erros[] = $rotulo . ' do tipo imagens deve enviar exclusivamente o payload images.';
                foreach (array('visivel', 'enviar', 'obrigatorio') as $propriedade) {
                    if (isset($campo[$propriedade]) && !is_bool($campo[$propriedade])) $erros[] = $rotulo . ' deve informar "' . $propriedade . '" como booleano.';
                }
                if (isset($ids[$id])) $erros[] = 'ID de campo repetido: ' . $id . '.'; else $ids[$id] = true;
                if (isset($origens[$origem])) $erros[] = 'Origem repetida: ' . $origem . '.'; else $origens[$origem] = true;
                if (isset($payloads[$payload])) $erros[] = 'Payload repetido: ' . $payload . '.'; else $payloads[$payload] = true;
                $campos[] = array('id' => $id, 'grupo' => $grupo, 'titulo' => $titulo, 'origem' => $origem, 'payload' => $payload, 'tipo' => $tipo, 'visivel' => !empty($campo['visivel']), 'enviar' => !empty($campo['enviar']), 'obrigatorio' => !empty($campo['obrigatorio']));
            }
        }
        if (!$campos) $erros[] = 'Informe ao menos um campo de produto.';
        foreach (array('name', 'price', 'quantity', 'status') as $obrigatorioIset) {
            $encontrado = false;
            foreach ($campos as $campo) if ($campo['payload'] === $obrigatorioIset && $campo['enviar'] && $campo['obrigatorio']) $encontrado = true;
            if (!$encontrado) $erros[] = 'O payload obrigatório da ISET "' . $obrigatorioIset . '" deve ser enviado e marcado como obrigatório.';
        }
        $regrasFixas = array(
            'price' => array('origem' => 'D009_Preco_1', 'tipo' => 'numero'),
            'quantity' => array('origem' => 'D009_Quantidade_Estoque_Liquido', 'tipo' => 'inteiro'),
            'status' => array('origem' => 'ISET_Status_Produto', 'tipo' => 'inteiro'),
        );
        foreach ($regrasFixas as $payload => $regra) {
            foreach ($campos as $campo) {
                if ($campo['payload'] !== $payload || !$campo['enviar']) continue;
                if ($campo['origem'] !== $regra['origem'] || $campo['tipo'] !== $regra['tipo']) $erros[] = 'O payload "' . $payload . '" deve usar ' . $regra['origem'] . ' como ' . $regra['tipo'] . '.';
            }
        }
        return array('ok' => !$erros, 'erros' => array_values(array_unique($erros)), 'campos' => $campos, 'json' => !$erros ? json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '');
    }

    public static function origensProdutosPermitidas()
    {
        return array('D001_Codigo_Produto', 'D001_Descricao_Produto', 'D009_Preco_1', 'D009_Quantidade_Estoque_Liquido', 'ISET_Status_Produto', 'ISET_Allow_Buy', 'T172_Imagens');
    }

    private function validar($urlBase, $identificador, $chave, $status, $transportadora, $portadorPagamento)
    {
        $url = parse_url(rtrim(trim((string) $urlBase), '/'));
        if (!$url || !isset($url['scheme'], $url['host']) || strtolower($url['scheme']) !== 'https' || isset($url['user']) || isset($url['pass']) || (isset($url['path']) && $url['path'] !== '')) {
            return 'Informe apenas o domínio HTTPS da loja ISET, por exemplo: https://www.sualoja.com.br';
        }
        if (trim((string) $identificador) === '') {
            return 'Informe o identificador da API ISET.';
        }
        if (trim((string) $chave) === '') {
            return 'Informe a chave de acesso da API ISET.';
        }
        if (trim((string) $status) !== '' && !preg_match('/^\d+(\s*,\s*\d+)*$/', $status)) {
            return 'Informe os IDs de status separados apenas por vírgula.';
        }
        if (trim((string) $transportadora) !== '' && !ctype_digit(trim((string) $transportadora))) {
            return 'Informe apenas o ID numérico da transportadora padrão da ISET.';
        }
        if (trim((string) $portadorPagamento) !== '' && !ctype_digit(trim((string) $portadorPagamento))) {
            return 'Informe apenas o ID numérico do portador padrão de pagamento da ISET.';
        }

        return '';
    }

    private function gravarCampo($campo, $valor, $observacao)
    {
        global $g;

        $empresaId = (int) $g['empresaAtual'];
        $campoSql = mysql_real_escape_string($campo);
        $valorSql = mysql_real_escape_string($valor);
        $observacaoSql = mysql_real_escape_string($observacao);
        $sql = <<<SQL
            SELECT C031_Id
              FROM C031
             WHERE C031_C004_Id = '{$empresaId}'
               AND C031_Campo = '{$campoSql}'
             LIMIT 1
SQL;
        $consulta = mysql_query($sql);
        if (!$consulta) {
            return 'Não foi possível verificar a configuração “' . $campo . '” antes de salvar. Banco informou: ' . (mysql_error() ?: 'diagnóstico não retornado pelo banco.');
        }

        $registro = mysql_fetch_assoc($consulta);
        if ($registro) {
            $sql = "UPDATE C031 SET C031_Valor = '{$valorSql}' WHERE C031_Id = '" . (int) $registro['C031_Id'] . "'";
        } else {
            $sql = <<<SQL
                INSERT INTO C031 (
                    C031_C004_Id,
                    C031_Campo,
                    C031_Valor_Padrao,
                    C031_Valor,
                    C031_Observacao
                ) VALUES (
                    '{$empresaId}',
                    '{$campoSql}',
                    '',
                    '{$valorSql}',
                    '{$observacaoSql}'
                )
SQL;
        }

        if (!mysql_query($sql)) {
            $acao = $registro ? 'atualizar a configuração existente' : 'criar a configuração';
            return 'Não foi possível ' . $acao . ' para “' . $campo . '”. Banco informou: ' . (mysql_error() ?: 'diagnóstico não retornado pelo banco.');
        }

        $g['C031'][$campo] = $valor;
        $g['c031'][$campo] = $valor;
        return true;
    }
}

if (function_exists(__NAMESPACE__ . '\\definirClasse')) {
    definirClasse('ISET_CONFIGURACOES');
}

if (!class_exists(__NAMESPACE__ . '\\iset_configuracoes', false)) {
    class_alias(__NAMESPACE__ . '\\iset_configuracoes_', __NAMESPACE__ . '\\iset_configuracoes');
}
