<?php
namespace hardness;

require_once __DIR__ . '/iset_cliente.php';

/**
 * Consulta produtos na ISET e mantém o último resultado por empresa/produto.
 * A classe não conhece HTTP diretamente e não expõe a resposta bruta ao chamador.
 */
class iset_consultas_produtos_
{
    const ENCONTRADO = 'ENCONTRADO';
    const NAO_ENCONTRADO = 'NAO_ENCONTRADO';
    const ERRO = 'ERRO';
    private $ultimaResposta = array();

    /** $produto deve ter sido relido pelo domínio de produtos para a empresa atual. */
    public function consultarProduto($empresaId, array $produto, array $campos)
    {
        $this->ultimaResposta = array();
        $empresaId = (int) $empresaId;
        $d009Id = isset($produto['D009_Id']) ? (int) $produto['D009_Id'] : 0;
        if ($empresaId <= 0 || $d009Id <= 0) {
            return $this->resultado(self::ERRO, 0, '', '', 'Produto Hardness inválido para consulta.');
        }

        $skuHardness = trim((string) (isset($produto['sku']) ? $produto['sku'] : ''));
        if ($skuHardness === '') {
            $mensagem = 'Produto Hardness sem SKU configurado para consulta na ISET.';
            $this->salvarErro($empresaId, $produto, '', '', 0, $mensagem);
            return $this->resultado(self::ERRO, 0, '', '', $mensagem);
        }
        $skuIset = (new ISET_SKU())->paraIset($skuHardness);

        try {
            $resposta = $this->cliente()->buscarProdutoPorSku($skuIset);
        } catch (\Throwable $erro) {
            $resposta = array('ok' => false, 'status' => 0, 'dados' => array(), 'mensagem' => 'Falha inesperada ao consultar a ISET.');
        }

        $this->ultimaResposta = $resposta;
        $status = isset($resposta['status']) ? (int) $resposta['status'] : 0;
        if (empty($resposta['ok'])) {
            if ($this->cliente()->ausenciaProdutoConfirmada($resposta, $skuIset)) {
                if (!$this->salvarNaoEncontrado($empresaId, $produto, '', $skuIset, $status)) return $this->resultado(self::ERRO, $status, '', $skuHardness, 'Falha ao persistir a consulta sem produto.');
                return $this->resultado(self::NAO_ENCONTRADO, $status, '', $skuHardness, 'Produto não encontrado na ISET.');
            }
            $mensagem = $this->mensagemSegura(isset($resposta['mensagem']) ? $resposta['mensagem'] : 'Não foi possível consultar a ISET.');
            $this->salvarErro($empresaId, $produto, '', $skuIset, $status, $mensagem);
            return $this->resultado(self::ERRO, $status, '', $skuHardness, $mensagem);
        }

        $produtoIset = $this->validarRespostaProduto($resposta, $skuIset);
        if (!$produtoIset['ok']) {
            $this->salvarErro($empresaId, $produto, '', $skuIset, $status, $produtoIset['mensagem']);
            return array_merge($this->resultado(self::ERRO, $status, '', $skuHardness, $produtoIset['mensagem']), array('motivo' => $produtoIset['motivo']));
        }

        $produtoRemoto = $produtoIset['produto'];
        $idRetornado = isset($produtoRemoto['id']) ? trim((string) $produtoRemoto['id']) : '';
        $skuRetornado = isset($produtoRemoto['cod_ref']) ? trim((string) $produtoRemoto['cod_ref']) : $skuIset;
        if (!$this->salvarProdutoValidado($empresaId, $produto, $produtoRemoto, $status, $campos)) {
            return $this->resultado(self::ERRO, $status, $idRetornado, $skuRetornado, 'A ISET respondeu ao produto, mas o cache da consulta não pôde ser salvo.');
        }

        return $this->resultado(self::ENCONTRADO, $status, $idRetornado, (new ISET_SKU())->paraHardness($skuRetornado), 'Produto consultado na ISET.');
    }

    /** Normaliza os campos declarados no JSON para uso posterior na comparação. */
    public function normalizarCampos(array $produtoIset, array $campos)
    {
        $valores = array();
        foreach ($campos as $campo) {
            if (!is_array($campo) || empty($campo['id']) || empty($campo['payload']) || empty($campo['tipo'])) continue;
            $bruto = array_key_exists($campo['payload'], $produtoIset) ? $produtoIset[$campo['payload']] : null;
            $valores[$campo['id']] = $this->normalizarValor($bruto, $campo['tipo']);
        }
        return $valores;
    }

    /** Extrai o produto correspondente e remove campos que não podem compor snapshots locais. */
    public function produtoRemotoSeguro(array $dados, $sku)
    {
        $produtoIset = $this->extrairProdutoCorrespondente($dados, 'cod_ref', $sku);
        if (!$produtoIset['ok']) return $produtoIset;
        $produtoIset['produto'] = $this->removerDadosSensiveis($produtoIset['produto']);
        return $produtoIset;
    }

    public function validarRespostaProduto(array $resposta, $sku, $id = 0)
    {
        if (empty($resposta['ok']) || !isset($resposta['status']) || (int) $resposta['status'] !== 200) {
            return array('ok' => false, 'motivo' => 'FALHA_CONSULTA', 'mensagem' => 'A consulta não confirmou o produto na ISET.');
        }
        $dados = isset($resposta['dados']) && is_array($resposta['dados']) ? $resposta['dados'] : array();
        $produto = $this->produtoRemotoSeguro($dados, $sku);
        if (!$produto['ok']) return $produto;
        if ($id > 0 && (int) $produto['produto']['id'] !== (int) $id) {
            return array('ok' => false, 'motivo' => 'CONFLITO_VINCULO', 'mensagem' => 'A ISET retornou um ID diferente do vínculo consultado.');
        }
        return $produto;
    }

    /**
     * Persiste somente um produto cuja identidade já foi validada contra a
     * consulta executada. O snapshot sempre representa a resposta da ISET.
     */
    public function salvarProdutoValidado($empresaId, array $produto, array $produtoIset, $statusHttp, array $campos)
    {
        $empresaId = (int) $empresaId;
        if ($empresaId <= 0 || empty($produto['D009_Id']) || (int) $statusHttp !== 200) return false;
        $idIset = isset($produtoIset['id']) ? $produtoIset['id'] : '';
        if ((!is_int($idIset) && !is_string($idIset)) || !filter_var($idIset, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 4294967295)))) return false;
        $sku = isset($produtoIset['cod_ref']) ? trim((string) $produtoIset['cod_ref']) : '';
        if ($sku === '') return false;
        $produtoIset = $this->removerDadosSensiveis($produtoIset);
        return $this->salvarSucesso($empresaId, $produto, $idIset, $sku, $statusHttp, $produtoIset, $this->normalizarCampos($produtoIset, $campos));
    }

    /** A mesma representação será usada posteriormente pela comparação Hardness × ISET. */
    public function normalizarValor($valor, $tipo)
    {
        switch ($tipo) {
            case 'texto':
                if ($valor === null) return null;
                $valor = trim((string) $valor);
                return $valor === '' ? null : $valor;
            case 'numero':
                $numero = $this->numero($valor);
                return $numero === null ? null : number_format($numero, 2, '.', '');
            case 'inteiro':
                $numero = $this->numero($valor);
                return $numero === null ? null : (int) $numero;
            case 'booleano':
                if (is_bool($valor)) return $valor;
                if (is_numeric($valor)) return (float) $valor != 0.0;
                return in_array(strtoupper(trim((string) $valor)), array('1', 'S', 'SIM', 'TRUE', 'Y', 'YES'), true);
            case 'imagens':
                if (!is_array($valor)) return array();
                $imagens = array(); $vistos = array();
                foreach ($valor as $imagem) {
                    if (is_array($imagem)) $imagem = isset($imagem['original']) ? $imagem['original'] : '';
                    $imagem = trim((string) $imagem);
                    if ($imagem === '' || isset($vistos[$imagem])) continue;
                    $vistos[$imagem] = true;
                    $imagens[] = $imagem;
                }
                return $imagens;
        }
        return null;
    }

    /** A ISET importa as URIs enviadas e passa a devolver endereços próprios para as fotos. */
    public function imagensEquivalentes($hardness, $iset)
    {
        $hardness = $this->normalizarValor($hardness, 'imagens');
        $iset = $this->normalizarValor($iset, 'imagens');
        return count($hardness) === count($iset);
    }

    protected function cliente()
    {
        return new ISET_CLIENTE();
    }

    /** Sucesso substitui integralmente o snapshot anterior. */
    protected function salvarSucesso($empresaId, array $produto, $idIset, $sku, $statusHttp, array $resposta, array $valores)
    {
        $respostaJson = json_encode($resposta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $valoresJson = json_encode($valores, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($respostaJson === false || $valoresJson === false) return false;
        $dados = $this->dadosCache($empresaId, $produto, $idIset, $sku, $statusHttp);
        $sql = "INSERT INTO INT004 (INT004_C004_Id,INT004_D009_Id,INT004_D001_Id,INT004_D049_Id,INT004_Id_Produto_ISET,INT004_SKU,INT004_Status_Http,INT004_Encontrado,INT004_Consultado_Em,INT004_Resposta_Json,INT004_Valores_Normalizados_Json,INT004_Mensagem_Erro,INT004_Ultimo_Sucesso_Em) VALUES ('{$dados['empresaId']}','{$dados['d009Id']}','{$dados['d001Id']}','{$dados['d049Id']}','{$dados['idIset']}','{$dados['sku']}','{$dados['statusHttp']}','S',NOW(),'" . mysql_real_escape_string($respostaJson) . "','" . mysql_real_escape_string($valoresJson) . "',NULL,NOW()) ON DUPLICATE KEY UPDATE INT004_D001_Id=VALUES(INT004_D001_Id),INT004_D049_Id=VALUES(INT004_D049_Id),INT004_Id_Produto_ISET=VALUES(INT004_Id_Produto_ISET),INT004_SKU=VALUES(INT004_SKU),INT004_Status_Http=VALUES(INT004_Status_Http),INT004_Encontrado='S',INT004_Consultado_Em=NOW(),INT004_Resposta_Json=VALUES(INT004_Resposta_Json),INT004_Valores_Normalizados_Json=VALUES(INT004_Valores_Normalizados_Json),INT004_Mensagem_Erro=NULL,INT004_Ultimo_Sucesso_Em=NOW()";
        return mysql_query($sql) !== false;
    }

    /** 404 é um resultado válido e invalida os valores anteriormente consultados. */
    protected function salvarNaoEncontrado($empresaId, array $produto, $idIset, $sku, $statusHttp)
    {
        $dados = $this->dadosCache($empresaId, $produto, $idIset, $sku, $statusHttp);
        $sql = "INSERT INTO INT004 (INT004_C004_Id,INT004_D009_Id,INT004_D001_Id,INT004_D049_Id,INT004_Id_Produto_ISET,INT004_SKU,INT004_Status_Http,INT004_Encontrado,INT004_Consultado_Em,INT004_Resposta_Json,INT004_Valores_Normalizados_Json,INT004_Mensagem_Erro,INT004_Ultimo_Sucesso_Em) VALUES ('{$dados['empresaId']}','{$dados['d009Id']}','{$dados['d001Id']}','{$dados['d049Id']}','{$dados['idIset']}','{$dados['sku']}','{$dados['statusHttp']}','N',NOW(),NULL,NULL,NULL,NULL) ON DUPLICATE KEY UPDATE INT004_D001_Id=VALUES(INT004_D001_Id),INT004_D049_Id=VALUES(INT004_D049_Id),INT004_Id_Produto_ISET=VALUES(INT004_Id_Produto_ISET),INT004_SKU=VALUES(INT004_SKU),INT004_Status_Http=VALUES(INT004_Status_Http),INT004_Encontrado='N',INT004_Consultado_Em=NOW(),INT004_Resposta_Json=NULL,INT004_Valores_Normalizados_Json=NULL,INT004_Mensagem_Erro=NULL";
        return mysql_query($sql) !== false;
    }

    /** Erros técnicos preservam a última resposta e os últimos valores normalizados. */
    protected function salvarErro($empresaId, array $produto, $idIset, $sku, $statusHttp, $mensagem)
    {
        $dados = $this->dadosCache($empresaId, $produto, $idIset, $sku, $statusHttp);
        $mensagem = mysql_real_escape_string(substr($this->mensagemSegura($mensagem), 0, 1000));
        $sql = "INSERT INTO INT004 (INT004_C004_Id,INT004_D009_Id,INT004_D001_Id,INT004_D049_Id,INT004_Id_Produto_ISET,INT004_SKU,INT004_Status_Http,INT004_Encontrado,INT004_Consultado_Em,INT004_Resposta_Json,INT004_Valores_Normalizados_Json,INT004_Mensagem_Erro,INT004_Ultimo_Sucesso_Em) VALUES ('{$dados['empresaId']}','{$dados['d009Id']}','{$dados['d001Id']}','{$dados['d049Id']}','{$dados['idIset']}','{$dados['sku']}','{$dados['statusHttp']}','E',NOW(),NULL,NULL,'{$mensagem}',NULL) ON DUPLICATE KEY UPDATE INT004_D001_Id=VALUES(INT004_D001_Id),INT004_D049_Id=VALUES(INT004_D049_Id),INT004_Id_Produto_ISET=VALUES(INT004_Id_Produto_ISET),INT004_SKU=VALUES(INT004_SKU),INT004_Status_Http=VALUES(INT004_Status_Http),INT004_Encontrado='E',INT004_Consultado_Em=NOW(),INT004_Mensagem_Erro=VALUES(INT004_Mensagem_Erro)";
        return mysql_query($sql) !== false;
    }

    private function extrairProdutoCorrespondente(array $dados, $campo, $esperado)
    {
        if (array_key_exists('status', $dados) && (string) $dados['status'] !== '200') {
            return array('ok' => false, 'motivo' => 'FALHA_CONSULTA', 'mensagem' => 'O status do JSON não confirma a consulta do produto.');
        }
        if (!isset($dados['result']) || !is_array($dados['result'])) {
            return array('ok' => false, 'motivo' => 'FALHA_CONSULTA', 'mensagem' => 'A ISET respondeu ao produto sem a estrutura result esperada.');
        }
        if (count($dados['result']) > 1 || (array_key_exists('totalFound', $dados) && is_numeric($dados['totalFound']) && $dados['totalFound'] > 1)) {
            return array('ok' => false, 'motivo' => 'DUPLICIDADE', 'mensagem' => 'A ISET retornou múltiplos produtos. Nenhum cadastro será alterado.');
        }
        if (array_key_exists('totalFound', $dados) && (string) $dados['totalFound'] !== '1') {
            return array('ok' => false, 'motivo' => 'FALHA_CONSULTA', 'mensagem' => 'A quantidade de produtos informada pela ISET é incoerente.');
        }
        $encontrados = array();
        foreach ($dados['result'] as $produto) {
            if (!is_array($produto) || !array_key_exists($campo, $produto)) continue;
            if ($this->identidadeIgual($produto[$campo], $esperado)) $encontrados[] = $produto;
        }
        if (count($encontrados) !== 1) {
            return array('ok' => false, 'motivo' => 'CONFLITO_VINCULO', 'mensagem' => 'A ISET retornou produto que não corresponde ao SKU consultado.');
        }
        if (!isset($encontrados[0]['id']) || (!is_int($encontrados[0]['id']) && !is_string($encontrados[0]['id'])) || !filter_var($encontrados[0]['id'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 4294967295)))) {
            return array('ok' => false, 'motivo' => 'FALHA_CONSULTA', 'mensagem' => 'A ISET não retornou um ID de produto válido.');
        }
        return array('ok' => true, 'produto' => $encontrados[0], 'mensagem' => '');
    }

    private function identidadeIgual($atual, $esperado)
    {
        return trim((string) $atual) === trim((string) $esperado);
    }

    private function dadosCache($empresaId, array $produto, $idIset, $sku, $statusHttp)
    {
        return array(
            'empresaId' => (int) $empresaId,
            'd009Id' => isset($produto['D009_Id']) ? (int) $produto['D009_Id'] : 0,
            'd001Id' => isset($produto['D001_Id']) ? (int) $produto['D001_Id'] : 0,
            'd049Id' => isset($produto['D049_Id']) ? (int) $produto['D049_Id'] : 0,
            'idIset' => mysql_real_escape_string(substr(trim((string) $idIset), 0, 100)),
            'sku' => mysql_real_escape_string(substr(trim((string) $sku), 0, 100)),
            'statusHttp' => max(0, (int) $statusHttp),
        );
    }

    private function numero($valor)
    {
        if ($valor === null || is_array($valor) || is_object($valor)) return null;
        $valor = trim((string) $valor);
        if ($valor === '') return null;
        $virgula = strrpos($valor, ','); $ponto = strrpos($valor, '.');
        if ($virgula !== false && $ponto !== false) {
            if ($virgula > $ponto) $valor = str_replace('.', '', $valor); else $valor = str_replace(',', '', $valor);
        }
        $valor = str_replace(',', '.', $valor);
        return is_numeric($valor) ? (float) $valor : null;
    }

    private function removerDadosSensiveis(array $dados)
    {
        $limpos = array();
        foreach ($dados as $chave => $valor) {
            if (preg_match('/(?:access[_-]?token|authorization|password|secret|credential|api[_-]?key|chave)/i', (string) $chave)) continue;
            if (is_array($valor)) $valor = $this->removerDadosSensiveis($valor);
            $limpos[$chave] = $valor;
        }
        return $limpos;
    }

    private function mensagemSegura($mensagem)
    {
        return preg_replace('/(?:access[_-]?token|authorization|password|secret|credential|api[_-]?key|chave)\s*[:=]\s*[^\s,;]+/i', '[dado sensível removido]', trim((string) $mensagem));
    }

    private function resultado($estado, $statusHttp, $idIset, $sku, $mensagem)
    {
        $candidatos = array();
        if (!empty($this->ultimaResposta['dados']['result']) && is_array($this->ultimaResposta['dados']['result'])) {
            foreach ($this->ultimaResposta['dados']['result'] as $item) if (is_array($item)) $candidatos[] = array('id' => isset($item['id']) ? $item['id'] : null, 'sku' => isset($item['cod_ref']) ? $item['cod_ref'] : null);
        }
        return array('motivo' => $estado === self::ENCONTRADO ? 'IDENTIDADE_CONFIRMADA' : 'FALHA_CONSULTA', 'candidatos' => $candidatos, 'chamadas' => isset($this->ultimaResposta['chamadas']) ? $this->ultimaResposta['chamadas'] : array(), 'requisicaoExecutada' => !empty($this->ultimaResposta['requisicaoExecutada']), 'estado' => $estado, 'statusHttp' => (int) $statusHttp, 'idIset' => (string) $idIset, 'sku' => (string) $sku, 'mensagem' => $this->mensagemSegura($mensagem), 'diagnostico' => isset($this->ultimaResposta['diagnostico']) ? $this->ultimaResposta['diagnostico'] : array('executada' => false));
    }
}

if (function_exists(__NAMESPACE__ . '\\definirClasse')) definirClasse('ISET_CONSULTAS_PRODUTOS');
if (!class_exists(__NAMESPACE__ . '\\iset_consultas_produtos', false)) class_alias(__NAMESPACE__ . '\\iset_consultas_produtos_', __NAMESPACE__ . '\\iset_consultas_produtos');
