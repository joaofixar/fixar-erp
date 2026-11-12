<?php
namespace hardness;

require_once __DIR__ . '/iset_sku.php';

/** Valida um pedido normalizado sem criar ou alterar qualquer registro do ERP. */
class iset_validador_
{
    private function sql($valor) { return mysql_real_escape_string(trim((string) $valor)); }
    private function sqlLike($valor)
    {
        $valor = str_replace(array('\\', '%', '_'), array('\\\\', '\\%', '\\_'), trim((string) $valor));
        return mysql_real_escape_string($valor);
    }

    private function localizarD022Iset($descricao, $parcial = false)
    {
        $descricao = trim((string) $descricao);
        if ($descricao === '') {
            return array();
        }

        $descricao = $parcial ? $this->sqlLike($descricao) : $this->sql($descricao);
        $comparacao = $parcial
            ? "D022.D022_Descricao_Ecommerce LIKE '%{$descricao}%'"
            : "D022.D022_Descricao_Ecommerce = '{$descricao}'";

        $sqlD022 = <<<SQL
            SELECT
                D022.D022_Id,
                D022.D022_D036_Id
            FROM D022
                LEFT JOIN D022A ON D022A.D022A_D022_Id = D022.D022_Id
            WHERE IFNULL(D022A.D022A_Flag_Ativo, 'S') = 'S'
                AND {$comparacao}
            ORDER BY D022.D022_Id ASC
            LIMIT 1
SQL;
        $consultaD022 = mysql_query($sqlD022);
        if (!$consultaD022) {
            return array('erro' => 'Não foi possível localizar a transportadora ISET. Banco informou: ' . (mysql_error() ?: 'diagnóstico não retornado pelo banco.'));
        }

        $D022 = mysql_fetch_assoc($consultaD022);
        return $D022 ?: array();
    }

    private function localizarD022PadraoIset()
    {
        global $g;

        $D022_Id = isset($g['C031']['IsetD022IdTransportadora']) ? (int) $g['C031']['IsetD022IdTransportadora'] : 0;
        if ($D022_Id <= 0) {
            return array();
        }

        $sqlD022 = <<<SQL
            SELECT
                D022.D022_Id,
                D022.D022_D036_Id
            FROM D022
                LEFT JOIN D022A ON D022A.D022A_D022_Id = D022.D022_Id
            WHERE D022.D022_Id = '{$D022_Id}'
                AND IFNULL(D022A.D022A_Flag_Ativo, 'S') = 'S'
            ORDER BY D022.D022_Id ASC
            LIMIT 1
SQL;
        $consultaD022 = mysql_query($sqlD022);
        if (!$consultaD022) {
            return array('erro' => 'Não foi possível validar a transportadora padrão da ISET. Banco informou: ' . (mysql_error() ?: 'diagnóstico não retornado pelo banco.'));
        }

        $D022 = mysql_fetch_assoc($consultaD022);
        return $D022 ?: array();
    }

    private function resolverFreteIset(array $pedido)
    {
        $frete = isset($pedido['totais']) && array_key_exists('frete', $pedido['totais']) ? $pedido['totais']['frete'] : null;
        $freteResolvido = array(
            'custo' => null,
            'titulo' => trim(isset($pedido['totais']['frete_titulo']) ? (string) $pedido['totais']['frete_titulo'] : ''),
            'metodo' => trim(isset($pedido['totais']['frete_metodo']) ? (string) $pedido['totais']['frete_metodo'] : ''),
            'D022_Id' => null,
            'D036_Id' => null,
            'flagFrete' => null,
            'criterio' => '',
            'transportadoraPadrao' => false,
            'erro' => '',
        );
        if ($frete === null || $frete === '' || !is_numeric($frete) || (float) $frete < 0) {
            $freteResolvido['erro'] = 'O valor do frete informado pela ISET está ausente, inválido ou negativo.';
            return $freteResolvido;
        }

        $freteResolvido['custo'] = (float) $frete;

        // O código do módulo identifica melhor a entrega. O título é usado em
        // seguida para atender cadastros antigos que registram somente o nome.
        $formas = array(
            array('valor' => $freteResolvido['metodo'], 'criterio' => 'metodo_exato', 'parcial' => false),
            array('valor' => $freteResolvido['titulo'], 'criterio' => 'titulo_exato', 'parcial' => false),
            array('valor' => $freteResolvido['metodo'], 'criterio' => 'metodo_parcial', 'parcial' => true),
            array('valor' => $freteResolvido['titulo'], 'criterio' => 'titulo_parcial', 'parcial' => true),
        );
        foreach ($formas as $forma) {
            if (trim((string) $forma['valor']) === '') {
                continue;
            }

            $D022 = $this->localizarD022Iset($forma['valor'], $forma['parcial']);
            if (!empty($D022['erro'])) {
                $freteResolvido['erro'] = $D022['erro'];
                return $freteResolvido;
            }
            if (!empty($D022['D022_Id'])) {
                $freteResolvido['D022_Id'] = (int) $D022['D022_Id'];
                $freteResolvido['D036_Id'] = !empty($D022['D022_D036_Id']) ? (int) $D022['D022_D036_Id'] : null;
                $freteResolvido['flagFrete'] = '0';
                $freteResolvido['criterio'] = $forma['criterio'];
                return $freteResolvido;
            }
        }

        if ($freteResolvido['custo'] > 0) {
            $D022 = $this->localizarD022PadraoIset();
            if (!empty($D022['erro'])) {
                $freteResolvido['erro'] = $D022['erro'];
                return $freteResolvido;
            }
            if (empty($D022['D022_Id'])) {
                $freteResolvido['erro'] = 'Não foi encontrada transportadora para a ISET e a transportadora padrão não está configurada, não existe ou está inativa.';
                return $freteResolvido;
            }

            $freteResolvido['D022_Id'] = (int) $D022['D022_Id'];
            $freteResolvido['D036_Id'] = !empty($D022['D022_D036_Id']) ? (int) $D022['D022_D036_Id'] : null;
            $freteResolvido['flagFrete'] = '0';
            $freteResolvido['criterio'] = 'transportadora_padrao';
            $freteResolvido['transportadoraPadrao'] = true;
            return $freteResolvido;
        }

        $freteResolvido['flagFrete'] = '9';
        $freteResolvido['criterio'] = 'sem_transportadora_frete_zero';
        return $freteResolvido;
    }

    private function localizarProduto($codigo)
    {
        global $g;
        $codigo = $this->sql((new ISET_SKU())->paraHardness($codigo));
        if ($codigo === '') return array();
        $empresa = (int) $g['empresaAtual'];
        $q = mysql_query("SELECT D001.D001_Id,D049.D049_Id,D009.D009_Id FROM D001 INNER JOIN D049 ON D049.D049_D001_Id=D001.D001_Id INNER JOIN D009 ON D009.D009_D049_Id=D049.D049_Id WHERE D001.D001_Codigo_Produto='{$codigo}' AND D009.D009_C004_Id='{$empresa}'");
        if (!$q) return array();
        $linhas = array(); while ($r = mysql_fetch_assoc($q)) $linhas[] = $r;
        return count($linhas) === 1 ? $linhas[0] : array('quantidade' => count($linhas));
    }

    private function localizarProdutoTeste()
    {
        global $g;
        $empresa = (int) $g['empresaAtual'];
        $q = mysql_query("SELECT D009.D009_Id FROM D001 INNER JOIN D049 ON D049.D049_D001_Id=D001.D001_Id INNER JOIN D009 ON D009.D009_D049_Id=D049.D049_Id WHERE D009.D009_C004_Id='{$empresa}' AND D001.D001_Flag_Pre_Cadastro<>'S' AND LOWER(D001.D001_Descricao_Produto) LIKE '%teste%hardness%' ORDER BY D001.D001_Id ASC,D009.D009_Id ASC LIMIT 1");
        return $q ? (mysql_fetch_assoc($q) ?: array()) : array();
    }

    private function produtoTemServicos($d001)
    {
        $q = mysql_query("SELECT D135_Id FROM D135 WHERE D135_D001_Id='" . (int) $d001 . "' LIMIT 1");
        return $q && mysql_num_rows($q) > 0;
    }

    public function validar(array $pedido)
    {
        global $g;
        $itens = array();
        $bloqueado = false;
        $itensNaoLocalizados = array();
        $empresa = (int) $g['empresaAtual'];
        $permitidos = array_filter(array_map('trim', explode(',', isset($g['C031']['IsetStatusImportacao']) ? $g['C031']['IsetStatusImportacao'] : '')));
        $ok = !empty($permitidos) && in_array((string) $pedido['pedido']['status_id'], $permitidos, true);
        $itens[] = array('grupo'=>'Status','ok'=>$ok,'texto'=>$ok ? 'Permitido para importação.' : 'Status não permitido nas configurações.');
        $bloqueado = !$ok;

        $id = $this->sql($pedido['pedido']['id_externo']);
        $q = mysql_query("SELECT T005_Id FROM T005 WHERE T005_C004_Id='{$empresa}' AND T005_Id_Pedido_Externo='{$id}'");
        $n = $q ? mysql_num_rows($q) : 0;
        $itens[] = array('grupo'=>'Pedido','ok'=>$n===0,'texto'=>$n===0 ? 'Ainda não importado.' : 'Já importado no Hardness.');
        $bloqueado = $bloqueado || $n > 0;

        $doc = preg_replace('/\D/', '', $pedido['cliente']['documento']);
        $campo = $pedido['cliente']['tipo']==='J' ? 'D024_Cnpj' : ($pedido['cliente']['tipo']==='F' ? 'D024_Cpf' : '');
        if ($campo === '' || $doc === '') {
            $itens[] = array('grupo'=>'Cliente','ok'=>false,'texto'=>'CPF/CNPJ não informado.');
            $bloqueado = true;
        } else {
            $q = mysql_query("SELECT D024_Id FROM D024 WHERE D024_C004_Id='{$empresa}' AND REPLACE(REPLACE(REPLACE(TRIM({$campo}), '/', ''), '-', ''), '.', '')='" . $this->sql($doc) . "'");
            $n = $q ? mysql_num_rows($q) : 0;
            $itens[] = array('grupo'=>'Cliente','ok'=>$n<=1,'texto'=>$n===1 ? 'Cliente localizado.' : ($n===0 ? 'Cliente será criado na importação.' : 'CPF/CNPJ duplicado no sistema.'));
            $bloqueado = $bloqueado || $n > 1;
        }

        $produtoComServico = false;
        foreach ($pedido['itens'] as $indice => $item) {
            $produto = $this->localizarProduto($item['codigo_referencia']);
            if (empty($produto['D009_Id'])) { $itensNaoLocalizados[$indice] = true; continue; }
            if ($this->produtoTemServicos($produto['D001_Id'])) $produtoComServico = true;
        }
        if ($itensNaoLocalizados) {
            $modoTeste = isset($g['C031']['IsetModoTesteImportacao']) && $g['C031']['IsetModoTesteImportacao'] === 'S';
            $produtoTeste = $modoTeste ? $this->localizarProdutoTeste() : array();
            $okTeste = $modoTeste && !empty($produtoTeste['D009_Id']);
            $itens[] = array('grupo'=>'Produto','ok'=>$okTeste,'texto'=>$okTeste ? 'Produtos ausentes serão substituídos pelo produto de teste.' : 'Há produtos que não foram encontrados no sistema.');
            $bloqueado = $bloqueado || !$okTeste;
        } elseif ($produtoComServico) {
            $itens[] = array('grupo'=>'Produto','ok'=>false,'texto'=>'Há produtos com serviços associados, não suportados nesta importação.');
            $bloqueado = true;
        } else {
            $itens[] = array('grupo'=>'Produto','ok'=>true,'texto'=>'Todos os produtos foram encontrados.');
        }
        $frete = $this->resolverFreteIset($pedido);
        $ok = $frete['erro'] === '';
        if (!$ok) {
            $texto = $frete['erro'];
        } elseif ($frete['transportadoraPadrao']) {
            $texto = 'Transportadora padrão selecionada porque não houve correspondência para a entrega ISET.';
        } elseif ($frete['flagFrete'] === '9') {
            $texto = 'Pedido sem frete e sem transportadora correspondente.';
        } else {
            $texto = 'Transportadora localizada pela entrega ISET.';
        }
        $itens[] = array('grupo'=>'Frete','ok'=>$ok,'texto'=>$texto);
        $bloqueado = $bloqueado || !$ok;

        return array('itens'=>$itens,'itens_nao_localizados'=>$itensNaoLocalizados,'bloqueado'=>$bloqueado,'frete'=>$frete);
    }
}

if (function_exists(__NAMESPACE__ . '\\definirClasse')) {
    definirClasse('ISET_VALIDADOR');
}

if (!class_exists(__NAMESPACE__ . '\\iset_validador', false)) {
    class_alias(__NAMESPACE__ . '\\iset_validador_', __NAMESPACE__ . '\\iset_validador');
}
