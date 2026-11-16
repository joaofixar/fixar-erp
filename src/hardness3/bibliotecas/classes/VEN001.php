<?php
namespace hardness;

// VEN001 does not instantiate MYTCPDF.  Keeping TCPDF out of this class load
// avoids unrelated legacy parser warnings for non-printing operations.
require_once "old/lib/funcoes.php";

definirClasse('VEN001');
class VEN001_
{   

    public function processoOrcamento($T004_Id){
        
    }

    public function processoPedido($T005_Id){
        
    }

    public function incluirEnsaioOrcamento($T003_Id){
        
    }

    /**
     * Inclui o cabeçalho inicial de um orçamento sem depender do formulário web.
     *
     * A empresa e o usuário responsável são sempre obtidos do contexto legado.
     * Os vendedores e o representante seguem os padrões cadastrados no cliente,
     * como no formulário ven001FormOrcamento.
     *
     * @param array $dados customer_id
     * @return array{success:bool,quote_id?:int,error_code?:string}
     */
    public function incluirCabecalhoOrcamento(array $dados)
    {
        global $g;

        $empresaId = (int) (isset($g['empresaAtual']) ? $g['empresaAtual'] : 0);
        $usuarioId = (int) (isset($g['usuarioAtual']) ? $g['usuarioAtual'] : 0);
        if ($empresaId <= 0 || $usuarioId <= 0 || empty($g['conexaoBanco'])) {
            return array('success' => false, 'error_code' => 'legacy_context_unavailable');
        }

        $clienteId = $this->ven001CabecalhoInteiro($dados, 'customer_id');
        if ($clienteId <= 0) {
            return array('success' => false, 'error_code' => 'invalid_quote_header');
        }

        $cliente = mysql_query("SELECT D024_Id, D024_Nome_Empresa, D024_D018_Id, D024_Flag_Lista_Preco, D024_Flag_Contribuinte, D024_C004_Id,
                                       D024_D006_Id, D024_Flag_Destino_Produto, D024_C007_Vendedor_Interno,
                                       D024_C007_Vendedor_Externo, D024_D021_Id
                                  FROM D024
                                 WHERE D024_Id = '{$clienteId}'
                                   AND D024_Flag_Cliente = 'S'
                                   AND D024_Flag_Ativo = 'S'");
        if (!$cliente || !($cliente = mysql_fetch_assoc($cliente))) {
            return array('success' => false, 'error_code' => 'customer_not_authorized');
        }

        $listaPreco = trim((string) $cliente['D024_Flag_Lista_Preco']);
        if ($listaPreco === '' || (int) $cliente['D024_Flag_Contribuinte'] < -1) {
            return array('success' => false, 'error_code' => 'customer_not_ready_for_quote');
        }
        if (!empty($g['C031']['separarClientePorEmpresa']) && $g['C031']['separarClientePorEmpresa'] === 'S'
            && !empty($cliente['D024_C004_Id']) && (int) $cliente['D024_C004_Id'] !== $empresaId) {
            return array('success' => false, 'error_code' => 'customer_not_authorized');
        }

        // Same defaults selected by ven001/form_func-ajax when a customer is
        // chosen.  No MCP-specific default is introduced here.
        $contatoId = 0;
        if (($g['C031']['orcamentoCarregarPrimeiroContato'] ?? '') !== 'N') {
            $contato = mysql_query("SELECT D013_Id FROM D013 WHERE D013_D024_Id = '{$clienteId}' AND D013_Flag_Ativo = 'S' ORDER BY D013_Id LIMIT 1");
            $linhaContato = $contato ? mysql_fetch_assoc($contato) : false;
            $contatoId = (int) ($linhaContato['D013_Id'] ?? 0);
        }
        $destinoProduto = $this->ven001DestinoPadraoOrcamento($cliente);
        $tipoDocumento = 6; // NFe is the normal selected option in ven001FormOrcamento.

        $vendedor = mysql_query("SELECT C007_Id, C007_Flag_Expedicao FROM C007 WHERE C007_Id = '{$usuarioId}' AND C007_Ativo != 'N' AND IFNULL(C007_Flag_Vendedor, '') != ''");
        if (!$vendedor || mysql_num_rows($vendedor) !== 1) {
            return array('success' => false, 'error_code' => 'responsible_user_not_seller');
        }
        $vendedor = mysql_fetch_assoc($vendedor);

        $modeloExpedicao = isset($g['C031']['modeloExpedicao']) ? (string) $g['C031']['modeloExpedicao'] : '';
        $flagExpedicao = $modeloExpedicao === '1' ? ($tipoDocumento === 4 ? '2' : '1') : '';
        if ($modeloExpedicao === '2') {
            $flagExpedicao = ($vendedor['C007_Flag_Expedicao'] ?? '') === '1' || ($vendedor['C007_Flag_Expedicao'] ?? '') === '2'
                ? $vendedor['C007_Flag_Expedicao'] : '';
        }

        // ven001/form_func-ajax uses the customer's internal seller when the
        // record exists and is not marked absent.  The user in T003_C007_Id
        // remains the actor that created the quote.
        $vendedorInternoId = $this->ven001VendedorInternoClienteOuAtor(
            $cliente['D024_C007_Vendedor_Interno'] ?? 0,
            $usuarioId
        );
        // The web form copies these two customer defaults directly.  It does
        // not create a fallback external seller or representative.
        $vendedorExternoId = $this->ven001CabecalhoInteiro($cliente, 'D024_C007_Vendedor_Externo');
        $representanteId = $this->ven001CabecalhoInteiro($cliente, 'D024_D021_Id');

        $colunas = array(
                'T003_D024_Id', 'T003_D013_Id', 'T003_Flag_Acp', 'T003_Flag_Destino_Produto',
                'T003_Data_Emissao', 'T003_C007_Id_Vendedor_Interno', 'T003_C007_Id', 'T003_Flag_Lista_Preco',
                'T003_C004_Id', 'T003_Nome_Cliente', 'T003_D018_Id', 'T003_Forma_Pagamento', 'T003_Frete',
                'T003_Prazo_Entrega', 'T003_Validade_Proposta', 'T003_Flag_Expedicao'
            );
        $valores = array(
                "'{$clienteId}'", "'{$contatoId}'", "'{$tipoDocumento}'", "'" . mysql_real_escape_string($destinoProduto) . "'",
                'CURDATE()', "'{$vendedorInternoId}'", "'{$usuarioId}'", "'" . mysql_real_escape_string($listaPreco) . "'",
                "'{$empresaId}'", "'" . mysql_real_escape_string($cliente['D024_Nome_Empresa']) . "'", "'" . (int) $cliente['D024_D018_Id'] . "'",
                "''", "''", "''", "''", "'" . mysql_real_escape_string($flagExpedicao) . "'"
            );
        if ($vendedorExternoId > 0) {
            $colunas[] = 'T003_C007_Id_Vendedor_Externo';
            $valores[] = "'{$vendedorExternoId}'";
        }
        if ($representanteId > 0) {
            $colunas[] = 'T003_D021_Id';
            $valores[] = "'{$representanteId}'";
        }
        if (!empty($g['C031']['venderEstoqueMatriz']) && $g['C031']['venderEstoqueMatriz'] === 'S') {
            $colunas[] = 'T003_C004_Id_Estoque';
            $valores[] = "'{$empresaId}'";
        }

        $sql = 'INSERT INTO T003 (' . implode(', ', $colunas) . ') VALUES (' . implode(', ', $valores) . ')';
        if (!mysql_query($sql)) {
            throw new \RuntimeException('quote_header_insert_failed');
        }
        $orcamentoId = (int) $g['mysqlLastId'];
        if ($orcamentoId <= 0) {
            throw new \RuntimeException('quote_header_insert_failed');
        }

        $ratearFrete = !empty($g['C031']['ratearFreteVendaNoCusto']) ? $g['C031']['ratearFreteVendaNoCusto'] : 'N';
        $conversaoUnidade = !empty($g['C031']['utilizaUnidadeConversaoVenda']) ? $g['C031']['utilizaUnidadeConversaoVenda'] : 'N';
        $sql = "INSERT INTO T003A (T003A_T003_Id, T003A_Hora_Inclusao, T003A_Flag_Ratear_Frete_Custo, T003A_Flag_Conversao_Unidade) VALUES ('{$orcamentoId}', CURTIME(), '" . mysql_real_escape_string($ratearFrete) . "', '" . mysql_real_escape_string($conversaoUnidade) . "')";
        if (!mysql_query($sql)) {
            throw new \RuntimeException('quote_header_extension_insert_failed');
        }

        return array('success' => true, 'quote_id' => $orcamentoId);
    }

    private function ven001CabecalhoInteiro(array $dados, $campo)
    {
        return isset($dados[$campo]) && is_scalar($dados[$campo]) && ctype_digit((string) $dados[$campo]) ? (int) $dados[$campo] : 0;
    }

    private function ven001VendedorInternoClienteOuAtor($vendedorClienteId, $usuarioId)
    {
        $vendedorClienteId = is_scalar($vendedorClienteId) && ctype_digit((string) $vendedorClienteId)
            ? (int) $vendedorClienteId : 0;
        $usuarioId = (int) $usuarioId;

        if ($vendedorClienteId <= 0 || $vendedorClienteId === $usuarioId) {
            return $usuarioId;
        }

        $vendedor = mysql_query("SELECT C007_Id, C007_Flag_Ausente FROM C007 WHERE C007_Id = '{$vendedorClienteId}'");
        $vendedor = $vendedor ? mysql_fetch_assoc($vendedor) : false;
        if (!$vendedor || ($vendedor['C007_Flag_Ausente'] ?? '') === 'S') {
            return $usuarioId;
        }

        return (int) $vendedor['C007_Id'];
    }

    private function ven001DestinoPadraoOrcamento(array $cliente)
    {
        // The non-interactive MCP flow must not execute dynamic configuration
        // loaders just to infer a header default.  Preserve an explicit client
        // value; otherwise let the nullable T003 field remain empty.
        return !empty($cliente['D024_Flag_Destino_Produto'])
            ? (string) $cliente['D024_Flag_Destino_Produto']
            : '';
    }

    /** Builds the same minimal GFormWrap used by the PDF quote importer, optionally carrying the item's ERP Ambiente. */
    public function montarFormIncluirProdutoOrcamento($T003_Id, $D001_Id, $quantidade, $codigoLocal = '')
    {
        $T003_Id = (int) $T003_Id;
        $D001_Id = (int) $D001_Id;
        if ($T003_Id <= 0 || $D001_Id <= 0 || !is_numeric($quantidade) || (float) $quantidade <= 0) {
            return false;
        }
        $produto = mysql_query("SELECT d009.D009_Id, d001.D001_Codigo_Produto, d001.D001_Descricao_Produto, d001.D001_D037_Id, d001.D001_D005_Id
                                  FROM T003 t003
                                  INNER JOIN D049 d049 ON d049.D049_D001_Id = '{$D001_Id}'
                                  INNER JOIN D009 d009 ON d009.D009_D049_Id = d049.D049_Id AND d009.D009_C004_Id = t003.T003_C004_Id
                                  INNER JOIN D001 d001 ON d001.D001_Id = d049.D049_D001_Id
                                 WHERE t003.T003_Id = '{$T003_Id}'
                                 ORDER BY d009.D009_Id ASC LIMIT 1");
        $produto = $produto ? mysql_fetch_assoc($produto) : false;
        if (!$produto || (int) $produto['D009_Id'] <= 0) {
            return false;
        }
        $cabecalho = mysql_query("SELECT T003_Flag_Destino_Produto FROM T003 WHERE T003_Id = '{$T003_Id}'");
        $cabecalho = $cabecalho ? mysql_fetch_assoc($cabecalho) : array();
        require_once 'bibliotecas/classes/GFormWrap.php';
        return new GFormWrap(array(
            'T004_D009_Id' => (int) $produto['D009_Id'],
            'T004_Quantidade' => (string) $quantidade,
            'T004_Valor_Preco_Sem_Desconto_Unitario' => '0,00',
            'T004_D037_Id' => (int) $produto['D001_D037_Id'],
            'T004_Descricao_Produto' => (string) $produto['D001_Descricao_Produto'],
            'T004_Codigo_Produto' => (string) $produto['D001_Codigo_Produto'],
            'T004_T003_Id' => $T003_Id,
            'T004_D005_Id' => (int) $produto['D001_D005_Id'],
            'T004_D006_Id' => '', 'T004_Aliquota_IPI' => '0',
            'T004_Flag_Destino_Produto' => (string) ($cabecalho['T003_Flag_Destino_Produto'] ?? ''),
            'T004_Codigo_Local' => trim((string) $codigoLocal),
            'T004A_Quantidade_Conversao' => (string) $quantidade,
        ));
    }

    /**
     * excluir
     *
     * @param String $T003_Id
     * @return String/bool
     */
    public function excluir($T003_Id)
    {
        if (empty($T003_Id)) {
            return "Não foi passado o Id do Pedido, contate o suporte técnico";
        }
        
        $cClausula = " SELECT ";
        $cClausula .= " * ";
        $cClausula .= " FROM T003 ";
        $cClausula .= " WHERE T003_Id='{$T003_Id}' ";
        $cSql_T003 = mysql_query($cClausula);
        $mT003     = mysql_fetch_array($cSql_T003);
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        $cClausula = "delete FROM T089 WHERE T089_T003_Id='{$T003_Id}'";
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: $cErro <br /><br /> $cClausula";
        }
        
        $T004 = mysql_query("SELECT T004_Id FROM T004 WHERE T004_T003_Id='{$T003_Id}'");
        while ($mT004 = mysql_fetch_array($T004)) {
            $cClausula = "delete FROM T202 WHERE T202_T004_Id='{$mT004['T004_Id']}'";
            mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: $cErro <br /><br /> $cClausula";
            }
            $cClausula = "delete FROM T004 WHERE T004_Id='{$mT004['T004_Id']}'";
            mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: $cErro <br /> <br /> $cClausula";
            }
        }

        $D085 = mysql_query("SELECT D085_Id, D085_T259_Id FROM D085 WHERE D085_Id_Entidade = '{$T003_Id}'AND D085_Entidade = 'T003'");        
        while ($mD085 = mysql_fetch_array($D085)) {
            $cClausula = "delete FROM D085 WHERE D085_Id='{$mD085['D085_Id']}'";
            mysql_query($cClausula);

            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: $cErro <br /><br /> $cClausula";
            }

            $cClausula = "delete FROM T259 WHERE T259_Id='{$mD085['D085_T259_Id']}'";
            mysql_query($cClausula);

            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: $cErro <br /><br /> $cClausula";
            }
        }

        $cClausula = "delete FROM T003 WHERE T003_Id='{$T003_Id}'";
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: $cErro<br /><br />$cClausula";
        }
        
        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");
        return true;
    }
    
    /**
     * calculaTotalServicos
     *
     * @param String $T003_Id
     * @return String
     */
    public function calculaTotalServicos($T003_Id)
    {
        $T003_Id = mysql_real_escape_string($T003_Id);
        $sql     = "SELECT * FROM T003 WHERE T003_Id='{$T003_Id}'";
        $T003    = mysql_query($sql);
        $T003    = mysql_fetch_array($T003);
        
        $sql      = "SELECT T145_Valor_Total FROM T145 WHERE T145_T003_Id='{$T003_Id}'";
        $T145_sql = mysql_query($sql);
        
        $total = 0;
        while ($T145 = mysql_fetch_array($T145_sql)) {
            $total += $T145['T145_Valor_Total'];
        }
        
        return $total;
    }
    
    /**
     * calculaTotalGeral
     *
     * @param String $T003_Id
     * @return String
     */
    public function calculaTotalGeral($T003_Id)
    {
        
        $totalServicos = $this->calculaTotalServicos($T003_Id);
        $sql           = "SELECT * FROM T003 WHERE T003_Id='{$T003_Id}'";
        $T003          = mysql_query($sql);
        $T003          = mysql_fetch_array($T003);
        
        return $T003['T003_Valor_Total_Servicos'] + $T003['T003_Valor_Total'];
    }
    
    /**
     * calculaTotalServicosConfirmado
     *
     * @param String $T003_Id
     * @return String
     */
    public function calculaTotalServicosConfirmado($T003_Id)
    {
        $T003_Id = mysql_real_escape_string($T003_Id);
        $sql     = "SELECT * FROM T003 WHERE T003_Id='{$T003_Id}'";
        $T003    = mysql_query($sql);
        $T003    = mysql_fetch_array($T003);
        
        $sql      = "SELECT * FROM T145 WHERE T145_T003_Id='{$T003_Id}'";
        $T145_sql = mysql_query($sql);
        
        $total = 0;
        while ($T145 = mysql_fetch_array($T145_sql)) {
            $total += $T145['T145_Quantidade_Confirmada'] * $T145['T145_Valor_Unitario'];
        }
        
        return $total;
    }
    
    /**
     * calculaTotalGeralConfirmado
     *
     * @param String $T003_Id
     * @return String
     */
    public function calculaTotalGeralConfirmado($T003_Id)
    {
        
        $totalServicosConfirmado = $this->calculaTotalServicosConfirmado($T003_Id);
        $sql                     = "SELECT * FROM T003 WHERE T003_Id='{$T003_Id}'";
        $T003                    = mysql_query($sql);
        $T003                    = mysql_fetch_array($T003);
        
        return $T003['T003_Valor_Total_Servicos'] + $T003['T003_Valor_Total_Confirmado'];
    }
    
    /**
     * montarEmail
     *
     * @param String $T003_Id
     * @return Array
     */
    public function montarEmail($T003_Id)
    {
        global $g, $confUsuario;
        $T003_Id = mysql_real_escape_string($T003_Id);
        $sql     = "SELECT * ,
                          T003_Prazos(T003_Id) formaPagamento, 
                          T003_Prazos_Portador(T003_Id) formaPagamentoPortador
                     FROM T003 
                LEFT JOIN D013 ON D013_Id=T003_D013_Id 
                LEFT JOIN D024 ON D024_Id=T003_D024_Id 
                LEFT JOIN D020 ON D020_Id=D024_D020_Id
                LEFT JOIN D018 ON D018_Id=D024_D018_Id 
                LEFT JOIN T003A ON T003_Id = T003A_T003_Id
                    WHERE T003_Id='{$T003_Id}'";
        $T003    = mysql_query($sql);
        $T003    = mysql_fetch_array($T003);
        
        $sql = <<<EOT
            SELECT * FROM T004 
            LEFT JOIN D009 ON D009_Id = T004_D009_Id 
            LEFT JOIN D049 ON D049_Id = D009_D049_Id
            LEFT JOIN D001 ON D001_Id = D049_D001_Id
            LEFT JOIN T144 ON T144_D001_Id = D001_Id
            LEFT JOIN D037 ON D037_Id=T004_D037_Id
            LEFT JOIN D005 ON D005_Id=T004_D005_Id
            LEFT JOIN T004A ON T004_Id=T004A_T004_Id
            WHERE T004_T003_Id = '{$T003_Id}' 
            GROUP BY T004_Id
            ORDER BY T004_Item_Pedido_Compra,T004_Id
EOT;
        
        $T004_sql = mysql_query($sql);
        
        $sql  = "SELECT D024_E_Mail FROM D024 WHERE D024_Id='{$T003['T003_D024_Id']}'";
        $D024 = mysql_query($sql);
        $D024 = mysql_fetch_array($D024);
        
        $sql  = "SELECT D013_Nome_Contato, D013_Telefone_1, D013_DDD_Telefone_1 FROM D013 WHERE D013_Id='{$T003['T003_D013_Id']}'";
        $D013 = mysql_query($sql);
        $D013 = mysql_fetch_array($D013);
        
        $array = array();
        
        $array['Para']    = (!empty($T003['D013_E_Mail'])) ? $T003['D013_E_Mail'] : $D024['D024_E_Mail'];
        $array['Assunto'] = "Orçamento Nº[{$T003_Id}]";
        
        $tabela = 'font-size: 12px;';
        $tabela .= 'margin: 10px 25px 10px 0;';
        $tabela .= 'width: 800px;';
        $tabela .= 'text-align: left;';
        $tabela .= 'border:0px;';
        
        $th = 'font-size: 13px;';
        $th .= 'font-weight: bold;';
        $th .= 'color: black;';
        $th .= 'background: #E1ECF1;';
        $th .= 'padding: 4px 4px;';
        $th .= 'border-bottom: 0px solid #6678b1;';
        
        $td = 'border-bottom: 0px solid #ccc;';
        $td .= 'color: #000000;';
        $td .= 'background: #fff;';
        $td .= 'padding: 4px 2px 4px 2px;';
        
        if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
        } else {
            $caminhoImagem = "";
        }
        if (!empty($T003['D024_Cnpj'])) {
            $cpfCnpj = $T003['D024_Cnpj'];
        } else {
            $cpfCnpj = $T003['D024_Cpf'];
        }
        $tel = "";
        if (!empty($D013['D013_Telefone_1'])) {
            $tel = '(' . $D013['D013_DDD_Telefone_1'] . ') ' . $D013['D013_Telefone_1'];
        } else if (!empty($T003['D024_Telefone_1'])) {
            $tel = '(' . $T003['D024_DDD_Telefone_1'] . ') ' . $T003['D024_Telefone_1'];
        }
        $T003['D024_Nome_Empresa'] = substr($T003['D024_Nome_Empresa'], 0, 60);
        $T003_Data_Emissao         = gcorrigeData($T003['T003_Data_Emissao']);
        
        // TABELA DO CABEÇALHO DO LAYOUT
        $corpo = <<<EOT
            <div style="margin-left:25px;margin-top:10px;font-family:Sans-Serif;font-size:14px;color:black">
                <table width=800px border=0>
                    <tr>
                        <td width=10px><img src="{$caminhoImagem}" style="margin-left:10px"></td>
                        <td style='padding: 0px 0px 0px 30px'>
                            <font style='font-size:11px;font-weight:normal'>
                                <b>{$T003['D024_Id']} - {$T003['D024_Nome_Empresa']}</b><br />
                                CNPJ: $cpfCnpj<br />
                                <b>Att: {$D013['D013_Nome_Contato']} - {$tel}</b><br />
                                {$T003['D024_Endereco']} - {$T003['D024_Numero']} <br />
                                {$T003['D024_Bairro']} - {$T003['D024_Cep']} - {$T003['D020_Nome_Cidade']} - {$T003['D018_UF']}
                            </font>
                        </td>
                        <td width=130px align=center>
                            ORÇAMENTO<br>Nº {$T003['T003_Id']} <br> {$T003_Data_Emissao}
                        </td>
                    </tr>
                </table>
                <table style="{$tabela}" bgcolor=gray cellspacing=1px>
                    <tbody>
                        <tr>
                            <td style="{$th}" rowspan=2>Item</td>
                            <td style="{$th}" rowspan=2>Código</td>
                            <td style="{$th}" rowspan=2>Descrição</td>
                            <td style="{$th}" align="center" width=30 rowspan=2>Und</td>
                            <td style="{$th}" rowspan=2 align="center">NCM</td>
                            <td style="{$th}" align="right" width=30 rowspan=2>Qte</td>
                            <td style="{$th}" align="right" width=80 rowspan=2>Unitário R\$</td>
                            <td style="{$th}" align="right" width=60 rowspan=2>Total R\$</td>
                            <td style="{$th}" align="center" width=30 rowspan=2>IPI</td>
                            <td style="{$th}" align="center" width=30 rowspan=2>ICM</td>
                            <td style="{$th}" align="right" width=30 rowspan=2>ST</td>
                            <td style="{$th}" rowspan=2>Link</td>
                        </tr>
                        <tr>
                        </tr>
EOT;
        // LOOPING PARA MONTAR A TABELA DOS PRODUTOS
        $count = 1;
        while ($T004 = mysql_fetch_array($T004_sql)) {
            if (!empty($T004['T004_Codigo_Substituto']))
                $codigo = $T004['T004_Codigo_Substituto'];
            else {
                $codigo = $T004['T004_Codigo_Produto'];
            }
            $T004_Quantidade               = gCorrigeNumero($T004['T004_Quantidade']);
            //$somaT004_Valor_Preco_Unitario = gCorrigeNumero($T004['T004_Valor_Preco_Unitario'] + ($T004['T004_Valor_IPI'] / $T004['T004_Quantidade']));
            $somaT004_Valor_Preco_Unitario = gCorrigeNumero($T004['T004_Valor_Preco_Unitario']);
            //$somaT004_Valor_Total_Preco    = gCorrigeNumero($T004['T004_Valor_Total_Preco'] + $T004['T004_Valor_IPI']);
            $somaT004_Valor_Total_Preco    = gCorrigeNumero($T004['T004_Valor_Total_Preco']);
            $T004_Aliquota_IPIFormatado    = number_format($T004['T004_Aliquota_IPI'], 0);
            $T004_Aliquota_ICMSFormatado   = number_format($T004['T004_Aliquota_ICMS'], 0);
            $T004_Valor_ST                 = gCorrigeNumero($T004['T004_Valor_ST'] + $T004['T004A_Valor_FCP_ST']);

            $corpo .= <<<EOT
                        <tr>
                            <td style="{$td}" align="center">{$count}</td>
                            <td style="{$td}" align="center">{$codigo}</td>
                            <td style="{$td};font-weight:bold" width=350px>
                                {$T004['T004_Descricao_Produto']}<br />
                                <span style="font-weight:normal; font-size:11px; font-style:italic;">
                                    {$T004['T004_Observacao']}
                                </span>
                            </td>
                            <td style="{$td}" align="center";">
                                {$T004['D037_Unidade']}
                            </td>
                            <td style="{$td}"; font-size:11px;" align="right">
                                {$T004['D005_Classificacao_Fiscal']}
                             </td>
                            <td style="{$td}" align="right";">
                                {$T004_Quantidade}
                            </td>
                            <td style="{$td};font-weight:bold" align="right";">
                                {$somaT004_Valor_Preco_Unitario}
                            </td>
                            <td style="{$td};font-weight:bold" align="right";">
                                {$somaT004_Valor_Total_Preco}
                            </td>
                            <td style="{$td}" align="center";font-size:12px;font-weight:normal;">
                                {$T004_Aliquota_IPIFormatado}%
                            </td>
                            <td style="{$td}" align="center";font-size:12px">
                                {$T004_Aliquota_ICMSFormatado}%
                            </td>
                            <td style="{$td}" align="right";font-size:12px">
                                {$T004_Valor_ST}
                            </td>
                            <td style="{$td}";padding:0px 2px 0px 2px" width=80px>
EOT;
            if (!empty($T004["T144_Url"])) {
                $T144Url = $T004["T144_Url"];
                $corpo .= <<<EOT
                                <a href={$T144Url} target=_blank style="text-decoration:none;font-weight:bold;color:#000"><u>Ver Produto</u></a>
EOT;
            }
            $corpo .= <<<EOT

                            </td>
                        </tr>
EOT;
            $count = $count + 1;
        }
        $corpo .= <<<EOT
                    <tbody>
                </table>
            </div>
EOT;
        $sql                 = <<<EOT
        SELECT * FROM T145 
        WHERE T145_T003_Id='{$T003_Id}'
EOT;
        $result              = mysql_query($sql);
        $valorTotalOrcamento = $T003['T003_Valor_Total'];
        // CASO TENHA SERVIÇOS, MONTA A TABELA DOS SERVIÇOS DO ORÇAMENTO
        if (mysql_num_rows($result) > 0) {
            $valorTotal = 0;
            $corpo .= <<<EOT
            <div style="border:2px solid #E0EEEE; padding-top:10px;padding-bottom:10px; margin-left:22px;">
                <div style="margin-left:25px;font-family:Sans-Serif;font-weight:bold"><br /><br />Seguem abaixo os serviços do pedido</div>
                <table style="{$tabela}">
                    <tbody>
                        <tr>
                            <th style="{$th}">Código</th>
                            <td style="{$th}">Descrição</td>
                            <td style="{$th}" align="right">Qte</td>
                            <td style="{$th}" align="right">Preço</td>
                        </tr>
EOT;
            while ($T145 = mysql_fetch_array($result)) {
                $T145_Valor_Unitario = gCorrigeNumero($T145['T145_Valor_Unitario']);
                $corpo .= <<<EOT
                        <tr>
                            <td style="{$td}">
                                {$T145['T145_Id']}
                            </td>
                            <td style="{$td}">
                                {$T145['T145_Titulo']} - {$T145['T145_Descricao']}
                            </td>
                            <td style="{$td} text-align:right;">
                                {$T145['T145_Quantidade']}
                            </td>
                            <td style="{$td}text-align:right;">
                                {$T145_Valor_Unitario}
                            </td>
                        </tr>
EOT;
                $valorTotal += ($T145['T145_Quantidade'] * $T145['T145_Valor_Unitario']);
            }
            $valorTotal = gCorrigeNumero($valorTotal);
            $corpo .= <<<EOT
                    </tbody>
                </table>
                <div style="margin-left:25px;font-family:Sans-Serif;"><b>Valor total dos Serviços: </b>&nbsp;'$valorTotal'</div>
EOT;
            $valorTotalOrcamento += $valorTotal;
        }
        $T003_Valor_Total_Produtos = gCorrigeNumero($T003['T003_Valor_Total_Produtos']);
        $T003_Valor_Total_IPI      = gCorrigeNumero($T003['T003_Valor_Total_IPI']);
        $T003_Valor_ST             = gCorrigeNumero($T003['T003_Valor_ST'] + $T003['T003A_Valor_FCP_ST']);
        $T003_Valor_Frete          = gCorrigeNumero($T003['T003_Valor_Frete']);
        $valorTotalOrcamento       = gCorrigeNumero($valorTotalOrcamento);

        $tiposFrete = array('0' => 'Por conta do emitente',
                            '1' => 'Por conta do destinatário',
                            '2' => 'Terceiros',
                            '9' => 'Sem frete');
        $T003_Frete = $tiposFrete["{$T003['T003_Flag_Frete']}"];

        if (empty($T003_Frete)) {
            $T003_Frete = "Não informado";
        }

        
        //MONTA A TABELA COM OS TOTALIZADORES DO ORÇAMENTO
        $corpo .= <<<EOT
                <div style="margin-left:25px">
                    <table border=0 style="font-family:Sans-Serif;color:black;font-size:12px">
                        <tr>
                            <td>
                                Prazo de Entrega: 
                            </td>
                            <td style="font-weight:bold" colspan=2 width=290px>
                                {$T003['T003_Prazo_Entrega']}
                            </td>
                            <td>
                                Valor total Produtos: 
                            </td>
                            <td style="font-weight:bold" align="right" width=90px>
                                R\$ {$T003_Valor_Total_Produtos}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Frete: 
                            </td>
                            <td style="font-weight:bold" colspan=2>
                                {$T003_Frete}
                            </td>
                            <td>
                                Valor total IPI: 
                            </td>
                            <td style="font-weight:bold" align="right">
                                R\$ {$T003_Valor_Total_IPI}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Forma pagamento: 
                            </td>
                            <td style="font-weight:bold" colspan=2>
                                {$T003['formaPagamentoPortador']}
                            </td>
                            <td>
                                Valor total ST + FCP ST: 
                            </td>
                            <td style="font-weight:bold;" align="right">
                                R\$ {$T003_Valor_ST}
                            </td>

                        </tr>
                        <tr>
                            <td>
                                Prazo pagamento: 
                            </td>
                            <td style="font-weight:bold" colspan=2>
                                {$T003['formaPagamento']}
                            </td>
                            <td>
                                Valor Total Frete: 
                            </td>
                            <td style="font-weight:bold;" align="right">
                                R\$ {$T003_Valor_Frete}
                            </td>
                        <tr>
                        <tr>
                            <td>
                                Validade proposta: 
                            </td>
                            <td style="font-weight:bold" colspan=2>
                                {$T003['T003_Validade_Proposta']}
                            </td>
                            <td>
                                Valor total Orçamento: 
                            </td>
                            <td style="font-weight:bold;font-size:14px;" align="right">
                                R\$ {$valorTotalOrcamento}
                            </td>
                        <tr>
                            <td colspan=3>&nbsp;</td>
                        </tr>
EOT;
        if (!empty($T003["T003_Observacao"])) {
            $T003_Observacao = str_replace(chr(10), '<br>', $T003['T003_Observacao']);
            $corpo .= <<<EOT
                        <tr>
                            <td valign=top>
                                Observação: 
                            </td>
                            <td style="font-weight:bold" colspan=5>
                                {$T003_Observacao}
                            </td>
                        </tr>
EOT;
        }
        $corpo .= <<<EOT
                    </table>
                </div>
EOT;
        
        $array['Corpo']                 = $corpo;
        $array['D024_Id']               = $T003['T003_D024_Id'];
        $array['T139_Valor_Total']      = $T003['T003_Valor_Total'];
        $sql                            = "SELECT COUNT(*) FROM T004 WHERE T004_T003_Id='{$T003_Id}'";
        $registros                      = mysql_query($sql);
        $registros                      = mysql_fetch_array($registros);
        $array['T139_Numero_Registros'] = $registros[0];
        
        return $array;
    }
    /**
     * montarEmailPdf
     *
     * @param String $T003_Id
     * @param bool $imprimirHtml
     * @return String/array
     */
    public function montarEmailPdf($T003_Id, $imprimirHtml = false, $moeda = '1')
    {
        global $g, $confUsuario, $sys;

        // Ajuste do tamanho da fonte para casos que a geração do PDF está ficando com fonte muito pequena
        $fonteMinima     = 13 + $g['C031']['vendasZoomFonteImprimirPDF'];
        $fonteMedia      = 18 + $g['C031']['vendasZoomFonteImprimirPDF'];
        $fonteGrande     = 20 + $g['C031']['vendasZoomFonteImprimirPDF'];
        $magensImpressao = $g['C031']['margemImprimirPDF'];

        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();        
        
        if (!$imprimirHtml) {
            // Arquivo
            $dir = $g['pathDados'] . 'tmp/';
            is_dir($dir) OR mkdir($dir, 0777, true);
            $filename             = "orcamento-{$T003_Id}.pdf";
            $file                 = $dir . $filename;
            $fileFull             = $g['pathRaiz'] . '/' . $file;
            $retorno['Anexos']    = array(
                $file
            );
            $retorno['AnexosWeb'] = array(
                '/hardness3/' . $file
            );
            
            $T003 = <<<EOT
                SELECT * FROM T003 
                LEFT JOIN D013 ON D013_Id=T003_D013_Id
                LEFT JOIN D024 ON D024_Id=T003_D024_Id
                WHERE T003_Id='{$T003_Id}';
EOT;
            
            $T003 = mysql_query($T003);
            $T003 = mysql_fetch_array($T003);
            
            // Array de retorno para criação do e-mail
            $retorno['Para']    = (!empty($T003['D013_E_Mail'])) ? $T003['D013_E_Mail'] : $T003['D024_E_Mail'];
            $retorno['Assunto'] = "Orçamento Nº[{$T003_Id}]";
            
            $retorno['Corpo'] = "<img src='{$caminhoImagem}' style='margin-left:10px'><font size='3'><br /><br /><br /><br /></font><font style='font-size:16px'>Segue orçamento de venda número <b>$T003_Id</b> em anexo.</font><font size='3'><br /><br /></font>";
            
            $magensImpressao = ($magensImpressao <= 0 || $magensImpressao == '') ? 1 : $magensImpressao;

            $r_link = '/ven/ven001/form_func-ajax/ven001ImprimirOrcamento/?ajax=true&gerarHtml=1&T003_Id=' . $T003_Id . '&moeda=' . $moeda; // .'&alcadaUpdate='.$alcadaUpdate;
            $r_link = str_replace(' ', '+', $r_link);
            shell_exec("xvfb-run --server-num " . mt_rand(0, 98) . " -s \"-screen 0 640x480x16\" wkhtmltopdf --minimum-font-size {$fonteMinima} --quiet --margin-left {$magensImpressao} --margin-right {$magensImpressao} --margin-top {$magensImpressao} --margin-bottom {$magensImpressao} \"{$g['urlRaiz']}{$r_link}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}\" \"{$fileFull}\"");        
            //log("xvfb-run --server-num " . mt_rand(0, 98) . " -s \"-screen 0 640x480x16\" wkhtmltopdf --minimum-font-size 13 --quiet --margin-left 1 --margin-right 1 --margin-top 1 --margin-bottom 1 \"{$g['urlRaiz']}{$r_link}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}\" \"{$fileFull}\"");
            
            return $retorno;
        }
        
        /**
         * SELECT's
         */
        $C004  = mysql_query("SELECT * FROM C004 LEFT JOIN D018 ON D018_Id=C004_D018_Id LEFT JOIN D020 ON D020_Id=C004_D020_Id WHERE C004_Id = '{$g['empresaAtual']}'");
        $mC004 = mysql_fetch_array($C004);

        $T003_Id = mysql_real_escape_string($T003_Id);
        $T003    = <<<EOT
                SELECT *, 
                T003_Prazos(T003_Id) formaPagamento, 
                T003_Prazos_Portador(T003_Id) formaPagamentoPortador
                FROM T003 
                LEFT JOIN D013 ON D013_Id=T003_D013_Id 
                LEFT JOIN D116 ON D116_Id=T003_D116_Id 
                LEFT JOIN D133 ON D133_Id=D116_D133_Id 
                LEFT JOIN D134 ON D134_Id=D116_D134_Id 
                LEFT JOIN D024 ON D024_Id=T003_D024_Id 
                LEFT JOIN D020 ON D020_Id=D024_D020_Id
                LEFT JOIN D018 ON D018_Id=D024_D018_Id 
                LEFT JOIN D022 ON D022_Id=T003_D022_Id
                LEFT JOIN C007 ON C007_Id=T003_C007_Id_Vendedor_Interno
                LEFT JOIN D044 ON D044_Id='{$moeda}'
                LEFT JOIN D021 ON D021_Id=T003_D021_Id
                LEFT JOIN T003A ON T003_Id = T003A_T003_Id
                WHERE T003_Id='{$T003_Id}';
EOT;
        $T003  = mysql_query($T003);
        $mT003 = mysql_fetch_array($T003);

        $representante = $this->montarEmailPdfRepresentante($mT003['T003_D021_Id']);
        

        $simboloMoeda = $mT003['D044_Simbolo'];
        $cotacaoMoeda = ($moeda != 1) ? $mT003['T003_Cotacao_Moeda'] : 1;

        
        $T004 = <<<EOT
                SELECT *,
                       sum(T004_Quantidade) as totalItens,
                       unPadrao.D037_Unidade as unPadrao_D037_Unidade,
                       unConversao.D037_Unidade as unConversao_D037_Unidade
                 FROM T004 
                LEFT JOIN D009 ON D009_Id=T004_D009_Id 
                LEFT JOIN D049 ON D049_Id=D009_D049_Id
                LEFT JOIN D082 ON D082_Id=D049_D082_Id
                LEFT JOIN D001 ON D001_Id=D049_D001_Id
                LEFT JOIN D003 ON D003_Id=D001_D003_Id
                LEFT JOIN T144 ON T144_D001_Id=D001_Id
                LEFT JOIN D037 unPadrao ON unPadrao.D037_Id=T004_D037_Id
                LEFT JOIN D005 ON D005_Id=T004_D005_Id
                LEFT JOIN D006 ON D006_Id=T004_D006_Id
                LEFT JOIN T004A ON T004_Id=T004A_T004_Id
                LEFT JOIN D184 ON D184_Id=T004A_D184_Id
                LEFT JOIN D037 unConversao ON unConversao.D037_Id=D184_D037_Id
                WHERE T004_T003_Id = '{$T003_Id}' 
                GROUP BY T004_Id
                ORDER BY T004_Codigo_Local,T004_Item
EOT;
        $T004 = mysql_query($T004);
        
        
        $html .= <<<EOT
        <style>
            .moldura {
                font-size: 11px;
                cellspacing:0px;
                width:100%;
                padding: 0px;
            }           
            .moldura td {
                background:white;
                padding:1px 1px 1px 1px;
            }           
            .tabela {
                cellspacing:0px;
                font-size:11px;
            }           
            .tdBorda {
                border-right:1px dotted #4F5F6E;
                border-bottom:1px dotted #4F5F6E;
                font-size:11px;
            }           
            .tdBordaR {
                font-size:11px;
                border-bottom:1px dotted #4F5F6E;
            }           
            .tdBordaL {
                border-right:1px dotted #4F5F6E;
                border-bottom:1px dotted #4F5F6E;
                font-size:11px;
            }           
            .tdTitulo {
                border-bottom:1px dotted white;
                border-right:1px dotted white;
                font-size:11px;
                background:#4F5F6E;
                color:white;           
            }           
            .tdTituloR {
                border-bottom:1px dotted white;
                font-size:11px;
                background:#4F5F6E;
                color:white;
            }           
            .tdTituloL {
                border-bottom:1px dotted white;
                border-right:1px dotted white;
                font-size:11px;
                background:#4F5F6E;
                color:white;
            }     
            tr, td, th, tbody, thead, tfoot {
                page-break-inside: avoid !important;
            }    
            table {
                font-family: Arial;
            }
        </style>
EOT;
        
        // Esta function pode ser personalizada para retornar um array com personalizações para utilização neste código
        $arrayPersonalizacaoImpressao = $this->varsImprimirOrcamento($T003_Id);

        /**
         * PDF - HEADER
         */
        if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
            $caminhoImagem = "<img src=\"{$caminhoImagem}\">";
        } else {
            $caminhoImagem = "";
        }
        
        $mT003['T003_Data_Emissao'] = gCorrigeData($mT003['T003_Data_Emissao']);
        $T003_Numero_Ordem_Compra   = str_pad($T003_Id, 6, "0", STR_PAD_LEFT);


        if (!empty($mT003['D013_Telefone_1'])) {
            $tel = $mT003['D013_DDD_Telefone_1'] . ' ' . $mT003['D013_Telefone_1'];
        } else if (!empty($mT003['D024_Telefone_1'])) {
            $tel = $mT003['D024_DDD_Telefone_1'] . ' ' . $mT003['D024_Telefone_1'];
        }
        $mT003['D024_Telefone_2'] = trim($mT003['D024_Telefone_2']);
        $tel .= !empty($mT003['D024_Telefone_2']) ? " / {$mT003['D024_DDD_Telefone_2']} {$mT003['D024_Telefone_2']}" : "";
        
        $html .= <<<EOT
        <table class="moldura" style="border: 2px solid #4F5F6E;"  cellpadding="0" cellspacing="0">
          <tr>
            <td colspan=15 cellpadding=0px cellspacing=0px style="padding: 0px">
              <table width=100% cellspacing=0px border=0 cellpadding=0px>
                <tr>
                    <td rowspan=2 style="text-align: center; vertical-align: middle; border-right:2px #4F5F6E solid" width='28%'>
                        {$caminhoImagem}
                    </td>          
                    <td rowspan=2 style='border-right:2px #4F5F6E solid' align=center> 
                      <table class="tabela" align=center>
                        <tr>
                            <td><b>{$mC004['C004_Nome_Empresa']}</b></td>
                        </tr>
                        <tr>
                            <td>{$mC004['C004_Logradouro']}, {$mC004['C004_Numero']} - {$mC004['C004_Bairro']}</td>
                        </tr>
                        <tr>
                            <td><b>Telefone:</b> {$mC004['C004_DDD_Telefone']}-{$mC004['C004_Telefone']}  <b>Fax:</b> {$mC004['C004_DDD_Fax']}-{$mC004['C004_Fax']}</td>
                        </tr>
                        <tr>
                            <td><b>CEP:</b> {$mC004['C004_Cep']} {$mC004['D020_Nome_Cidade']} - {$mC004['D018_UF']}</td>
                        </tr>
                        <tr>
                            <td><b>CNPJ:</b> {$mC004['C004_Cnpj']}</td>
                        </tr>
                        <tr>
                            <td><b>Inscr. Estadual:</b> {$mC004['C004_Inscricao_Estadual']}</td>
                        </tr>
                        <tr>
                            <td><b>E-Mail:</b> {$mC004['C004_E_Mail']}</td>
                        </tr>
                        <tr>
                            <td><b>Site:</b>{$mC004['C004_Site']}</td>
                        </tr>
                      </table>
                    </td>
                    <td style="font-size:{$fonteGrande}px; border-bottom:2px #4F5F6E solid" align="center" width='28%'>
                       <b>ORÇAMENTO<br>Nº {$T003_Numero_Ordem_Compra}<br>{$mT003['T003_Data_Emissao']}<b>
                    </td>
                </tr>
                <tr>
                  <td>
                    <table class="tabela" align=center>
                        <tr>
                            <td><b><u>VENDEDOR:</u></b></td>
                        </tr>
                        <tr>
                            <td>{$mT003['C007_Primeiro_Nome']}, {$mT003['C007_DDD_Telefone']}-{$mT003['C007_Telefone']}</td>
                        </tr>
                        <tr>
                            <td>{$mT003['C007_Email']}</td>
                        </tr>
                        {$representante}
                    </table>                    
                  </td>
                </tr>
              </table>
            </td>
          </tr>  
             <tr>
                <td colspan="16" align="center" style="background:#4F5F6E;color:white;font-size:12px;font-weight:bold; border-bottom:2px #4F5F6E solid; border-top:2px #4F5F6E solid">
                    CLIENTE
                </td>
            </tr>
            <tr>
                <td colspan="16">
                    <table class="tabela" style="width:100%">
                        <tr>
                            <td width="50%">
                                <table class="tabela" width="100%">
                                    <tr>
                                        <td><b>{$mT003['D024_Id']} - {$mT003['D024_Nome_Empresa']}</b><br><b>{$mT003['T003_Cliente_Area']}</b></td>
                                    </tr>
                                    <tr>
                                        <td>{$mT003['D024_Endereco']}, {$mT003['D024_Numero']} - {$mT003['D024_Bairro']}</td>
                                    </tr>
                                    <tr>
                                        <td><b>CEP:</b> {$mT003['D024_Cep']} {$mT003['D020_Nome_Cidade']} - {$mT003['D018_UF']}</td>
                                    </tr>
                                    <tr>
                                        <td><b>CNPJ/CPF:</b> {$mT003['D024_Cnpj']}{$mT003['D024_Cpf']}</td>
                                    </tr>
                                </table>
                            </td>
                            <td width="50%">
                                <table class="tabela" width="100%">
                                    <tr>
                                        <td><b>Incr. Estadual:</b> {$mT003['D024_Inscricao_Estadual']}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Contato: </b>{$mT003['D013_Nome_Contato']}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Telefone: </b>{$tel}</td>
                                    </tr>
                                    <tr>
                                        <td><b>E-mail: </b>{$mT003['D013_E_Mail']}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
EOT;
        
        /**
         * PDF - TABELA PRODUTOS
         */
        /*
        <tr>
                <td colspan="3" style="padding:0px 0px 0px 0px">
                    <table cellspacing="0" cellpadding="4px" width="100%" height="100%" style="border:0px">
        */
        $html .= <<<EOT
                        <tr style='font-weight:bold'>
                            <td align="center" style="background:#4F5F6E;color:white;width:20px" class="tdTituloL">
                                It
                            </td>
EOT;
        if($g['C031']['exibirFotoImpressaoVenda'] == 'S'){
                    $html .= <<<EOT
                                <td align="center" style="background:#4F5F6E;color:white;width:60px" class="tdTitulo">
                                Foto
                            </td>
EOT;
        }
        $html .= <<<EOT
                            <td align="center" style="background:#4F5F6E;color:white;width:85px" class="tdTitulo">
                                Código
                            </td>
                            <td class="tdTitulo" style="background:#4F5F6E;color:white">
                                Descrição
                            </td>
EOT;
        if($g['C031']['exibirMarcaImpressaoVenda'] == 'S'){
                   $html .= <<<EOT
                            <td align="center" class="tdTitulo" style="background:#4F5F6E;color:white">
                                Marca
                            </td>
EOT;
        }
        $html .= <<<EOT
                            <td align="center" style="background:#4F5F6E;color:white;width:1%" class="tdTitulo"> 
                                UN
                            </td>
                            <td align="center" style="background:#4F5F6E;color:white;width:1%" class="tdTitulo">
                                NCM
                            </td>
                            <td align="center" style="background:#4F5F6E;color:white;width:1%" class="tdTitulo">
                                CFOP
                            </td>
                            <td align="center" style="background:#4F5F6E;color:white;width:55px" class="tdTitulo">
                                Qte
                            </td>
                            <td align="center" style="background:#4F5F6E;color:white;width:55px" class="tdTitulo">
                                Unit{$simboloMoeda}
                            </td>
                            <td align="center" style="background:#4F5F6E;color:white;width:65px" class="tdTitulo">
                                Total{$simboloMoeda}
                            </td>
                            <td align="center" style="background:#4F5F6E;color:white;width:40px" class="tdTitulo">
                                %IPI
                            </td>
                            <td align="center" style="background:#4F5F6E;color:white;width:40px" class="tdTitulo">
                                %ICM
                            </td>
                            <td align="center" style="background:#4F5F6E;color:white;width:40px" class="tdTitulo">
                                MVA
                            </td>
                            <td align="right" style="background:#4F5F6E;color:white;width:40px" class="tdTituloR" >
                                ST{$simboloMoeda}
                            </td>
                        </tr>
EOT;
        $count          = 1;
        $valorTotalProd = 0;
        $totalItens     = 0;
        $totalAmbiente  = 0;
        $ambiente       = '';

        while ($mT004 = mysql_fetch_array($T004)) {

            $unidadeMedida = $mT004['unPadrao_D037_Unidade'];
            // Se tiver unidade de conversao, imprime os valores convertidos
            if($mT003['T003A_Flag_Conversao_Unidade'] == 'S' && $mT004['T004A_D184_Id'] > 0){
                $unidadeMedida                      = $mT004['unConversao_D037_Unidade'];
                $mT004['T004_Quantidade']           = $mT004['T004A_Quantidade_Comercial'];
                $mT004['T004_Valor_Preco_Unitario'] = $mT004['T004A_Preco_Unitario_Comercial'];
            }
             
            if ($ambiente!=strtoupper(trim(trim($mT004['T004_Codigo_Local'])))){
                if (!empty($ambiente)){
                    $totalAmbiente = gCorrigeNumero($totalAmbiente);
                    $html .= "<tr><td align=center colspan=20 style='font-size:{$fonteMedia}px;font-weight:normal;background:#F4F4F4'>" . ucwords(strtolower("TOTAL ".$ambiente)) . " <b>R$ {$totalAmbiente}</b></td></tr>";
                }
                $ambiente = strtoupper(trim($mT004['T004_Codigo_Local']));
                $totalAmbiente  = 0;
                $html .= "<tr><td align=center colspan=20 style='font-size:{$fonteMedia}px;font-weight:bold;background:#343A4B;color:white'>{$ambiente}</td></tr>";
            }

            if (!empty($ambiente)){
                $totalAmbiente += $mT004['T004_Valor_Total_Preco'];
            }

            $totalItens += $mT004['totalItens'];
            if($g['C031']['precopadraoVendaProduto'] == 'bruto'){
                $mT004['T004_Valor_Total_Preco']                 = gCorrigeNumero(($mT004['T004_Valor_Preco_Bruto'] * $mT004['T004_Quantidade'])/$cotacaoMoeda);    
                $mT004['T004_Valor_Preco_Sem_Desconto_Unitario'] = gCorrigeNumero($mT004['T004_Valor_Preco_Bruto']/$cotacaoMoeda, $mT003['T003_Casas_Decimais_Preco']);
                $mT004['T004_Valor_Preco_Unitario']              = gCorrigeNumero($mT004['T004_Valor_Preco_Bruto']/$cotacaoMoeda, $mT003['T003_Casas_Decimais_Preco']);
            } else {
                $mT004['T004_Valor_Total_Preco']                 = gCorrigeNumero($mT004['T004_Valor_Total_Preco']/$cotacaoMoeda);    
                $mT004['T004_Valor_Preco_Sem_Desconto_Unitario'] = gCorrigeNumero($mT004['T004_Valor_Preco_Sem_Desconto_Unitario']/$cotacaoMoeda, $mT003['T003_Casas_Decimais_Preco']);
                $mT004['T004_Valor_Preco_Unitario']              = gCorrigeNumero($mT004['T004_Valor_Preco_Unitario']/$cotacaoMoeda, $mT003['T003_Casas_Decimais_Preco']);
            }
            $mT004['T004_Quantidade']                        = gCorrigeNumero($mT004['T004_Quantidade']);
            $mT004['T004_Aliquota_IPI']                      = gCorrigeNumero($mT004['T004_Aliquota_IPI']);
            $mT004['T004_Valor_IPI']                         = gCorrigeNumero($mT004['T004_Valor_IPI']/$cotacaoMoeda);
            $mT004['T004_Valor_ST']                          = gCorrigeNumero($mT004['T004_Valor_ST']/$cotacaoMoeda,2);
            $mT004['T004_Aliquota_ICMS']                     = gCorrigeNumero($mT004['T004_Aliquota_ICMS'], 2);
            $mT004['T004_Valor_ICMS']                        = gCorrigeNumero($mT004['T004_Valor_ICMS']/$cotacaoMoeda);
            $mT004['T004_Valor_ICMS_Substituicao_Retencao']  = gCorrigeNumero($mT004['T004_Valor_ICMS_Substituicao_Retencao']/$cotacaoMoeda);
            $mT004['T004_ST_VA_Ajustada']                    = gCorrigeNumero($mT004['T004_ST_VA_Ajustada']);

            $observacaoItem                                  = '<font></font>';
            if (!empty($mT004['T004_Observacao'])) {
                $observacaoItem = '<font style="background:#FCF9AE"><b>Observação:</b> ' . $mT004['T004_Observacao'] . '</font><br>';
            }
            if (!empty($mT004['T004_Codigo_Substituto'])) {
                //$mT004['T004_Codigo_Produto']=$mT004['T004_Codigo_Substituto'];
            }

            $foto = $CAD002->cad002FotoProduto($mT004['D001_Id'],60);

            $html .= <<<EOT
            <tr style="height:60px;max_height:60px">
EOT;
            
            if (!empty($mT004['T004_Codigo_Substituto'])) {
                $mT004['T004_Codigo_Substituto'] = $mT004['T004_Codigo_Substituto'] . '<br />';
            }
            $html .= <<<EOT
                <td align="center" class="tdBordaL">
                   <b>{$mT004['T004_Item']}</b>
                </td>
EOT;
            if($g['C031']['exibirFotoImpressaoVenda'] == 'S'){
                $html .= <<<EOT
                    <td align="center" class="tdBorda" height="50px">
                        $foto
                    </td>
EOT;
            }
        $html .= <<<EOT
                <td align="center" class="tdBorda">
                    <b>{$mT004['T004_Codigo_Substituto']}</b>
                    {$mT004['T004_Codigo_Produto']} 
                </td>
                <td class="tdBorda">
                    <b>{$mT004['T004_Descricao_Produto']} {$mT004['D049_Modelo']}</b><br>
                    {$observacaoItem}
                    <font style='color:#0F079E'><i>Prazo entrega em dias{$arrayPersonalizacaoImpressao['prazoDias']}:<b> {$mT004['T004_Observacao_Entrega']}</b></i></font>
                </td>
EOT;
                     
        if($g['C031']['exibirMarcaImpressaoVenda'] == 'S'){
            $html .= <<<EOT
                <td align="center" class="tdBorda">
                    {$mT004['D082_Marca']}
                </td>
EOT;
        }
            $html .= <<<EOT
                <td align="center" class="tdBorda">
                    {$unidadeMedida}
                </td>
                <td align="center" class="tdBorda">
                    {$mT004['D005_Classificacao_Fiscal']}
                </td>
                <td align="center" class="tdBorda">
                    {$mT004['D006_Codigo_CFOP']}
                </td>
                <td align="center" class="tdBorda">
                    <b>{$mT004['T004_Quantidade']}</b>
                </td>
                <td align="right" class="tdBorda">
                    <b>{$mT004['T004_Valor_Preco_Unitario']}</b>
                </td>
                <td align="right" class="tdBorda">
                    {$mT004['T004_Valor_Total_Preco']}
                </td>
                <td align="center" class="tdBorda">
                    {$mT004['T004_Aliquota_IPI']}
                </td>
                <td align="center" class="tdBorda">
                    {$mT004['T004_Aliquota_ICMS']}
                </td>
                <td align="right" class="tdBorda">
                    {$mT004['T004_ST_VA_Ajustada']}
                </td>
                <td align="right" class="tdBordaR">
                    {$mT004['T004_Valor_ST']}
                </td>
            </tr>
EOT;
            $count = $count + 1;
        }
       /* </table>
                
                </td>
            </tr>*/

        if (!empty($ambiente)){
            $totalAmbiente = gCorrigeNumero($totalAmbiente);
            $html .= "<tr><td align=center colspan=20 style='font-size:{$fonteMedia}px;font-weight:normal;background:#F4F4F4'>" . ucwords(strtolower("TOTAL ".$ambiente)) . " <b>R$ {$totalAmbiente}</b></td></tr>";
        }

        $html .= <<<EOT
            <tr>
                <td colspan="25" style="padding:0px 0px 0px 0px; border-top: 2px solid #4F5F6E;">

EOT;
        //Vai verificar se deve imprimir o valor do frete, mesma validação que é realiza para formar o preço venda e calcular o total do pedido
        $ratearFreteCusto          = ($mT003['T003A_Flag_Ratear_Frete_Custo'] != '') ? $mT003['T003A_Flag_Ratear_Frete_Custo'] : $g['C031']['ratearFreteVendaNoCusto'] == 'S';
        $mT003['T003_Valor_Frete'] = ($ratearFreteCusto == 'S' && $mT003['T003_Flag_Frete'] == '0') ? 0 :  $mT003['T003_Valor_Frete'];

        /**
         * PDF - TOTAIS
         */
        $mT003['T003_Valor_Total_Produtos']              = gCorrigeNumero($mT003['T003_Valor_Total_Produtos']/$cotacaoMoeda);
        $mT003['T003_Valor_Frete']                       = gCorrigeNumero($mT003['T003_Valor_Frete']/$cotacaoMoeda);
        $mT003['T003_Valor_Total_IPI']                   = gCorrigeNumero($mT003['T003_Valor_Total_IPI']/$cotacaoMoeda);
        $mT003['T003_Valor_Total_ICMS']                  = gCorrigeNumero($mT003['T003_Valor_Total_ICMS']/$cotacaoMoeda);
        $mT003['T003_Valor_Total']                       = gCorrigeNumero($mT003['T003_Valor_Total']/$cotacaoMoeda);
        $mT003['T003_Data_Entrega']                      = gCorrigeData($mT003['T003_Data_Entrega']);
        $mT003['T003_Valor_ICMS_Substituicao_Retencao']  = gCorrigeNumero($mT003['T003_Valor_ICMS_Substituicao_Retencao']/$cotacaoMoeda);
        $mT003['T003_Valor_Desconto']                    = gCorrigeNumero($mT003['T003_Valor_Desconto']/$cotacaoMoeda);
        $mT003['T003_Valor_Desconto_Manual']             = gCorrigeNumero($mT003['T003_Valor_Desconto_Manual']/$cotacaoMoeda);
        $mT003['T003_Valor_Despesas_Acessorias']         = gCorrigeNumero($mT003['T003_Valor_Despesas_Acessorias']/$cotacaoMoeda);
        $mT003['T003_Valor_ST']                          = gCorrigeNumero(($mT003['T003_Valor_ST'] + $mT003['T003A_Valor_FCP_ST'])/$cotacaoMoeda);
        $mT003['T003_Peso_Total']                        = gCorrigeNumero($mT003['T003_Peso_Total']);
        $mT003['T003_Peso_Bruto_Total']                  = gCorrigeNumero($mT003['T003_Peso_Bruto_Total']);
        $mT003['T003_Total_Cubagem']                     = gCorrigeNumero($mT003['T003_Total_Cubagem']);
        $mT003['T003_Valor_Total_Partilha_ICMS_Destino'] = gCorrigeNumero($mT003['T003_Valor_Total_Partilha_ICMS_Destino']);
        $totalItens                                      = gCorrigeNumero($totalItens);
        
        $desconto = <<<EOT
                <tr>
                    <td align="left">Produtos: </td>
                    <td align="left"><b>{$simboloMoeda} {$mT003['T003_Valor_Total_Produtos']}</b></td>
                </tr>
EOT;
        //impressão do desconto no orçamento
        if(($g['C031']['exibirDescontoImpressaoOrcPed'] == 'S')){
            if(($mT003['T003_Valor_Desconto'] < 0 && $g['C031']['mostrarDescontoProdutosAcrescimo'] == "S") || ($mT003['T003_Valor_Desconto'] > 0)){
                $desconto = <<<EOT
                    <tr>
                        <td align="left">Valor produtos: </td>
                        <td  align="left"><b>{$simboloMoeda} {$mT003['T003_Valor_Total_Produtos_Sem_Desconto']}</b></td>
                    </tr>
                    <tr>
                        <td align="left">Desconto produto: </td><td  align="left"><b>{$simboloMoeda} {$mT003['T003_Valor_Desconto']}</b></td>
                    </tr>
                    <tr>
                        <td align="left">Produtos líquido: </td>
                        <td  align="left"><b>{$simboloMoeda} {$mT003['T003_Valor_Total_Produtos']}</b></td>
                    </tr>
EOT;
            }        
            
        }else{
            $desconto = <<<EOT
                <tr>
                    <td align="left">Valor produtos: </td>
                    <td align="left"><b>{$simboloMoeda} {$mT003['T003_Valor_Total_Produtos']}</b></td>
                </tr>
EOT;
        }
        $tiposFrete = array('0' => 'Por conta do emitente',
                            '1' => 'Por conta do destinatário',
                            '2' => 'Terceiros',
                            '3' => 'Próprio por conta do remetente',
                            '4' => 'Próprio por conta do destinatário',
                            '9' => 'Sem frete');
        $T003_Frete = $tiposFrete["{$mT003['T003_Flag_Frete']}"];

        if (empty($T003_Frete)) {
            $T003_Frete = "Não informado";
        }

            $html .= <<<EOT
                    <table class="tabela" cellpadding="0" cellspacing="0" width="100%" valign="top">
                        <td valign="top" width="50%" valign="top">
                            <table class="tabela"width="100%" valign="center">
                                <tr>
                                    <td align="left" width="130px">Prazo pagamento: </td>
                                    <td align="left"><b>{$mT003['formaPagamento']}</b></td>
                                </tr>
                                <tr>
                                    <td align="left" width="130px">Forma pagamento: </td>
                                    <td align="left"><b>{$mT003['formaPagamentoPortador']}</b></td>
                                </tr>
                                <tr>
                                    <td align="left">Frete: </td>
                                    <td align="left"><b>{$T003_Frete}</b></td>
                                </tr>
                                <tr>
                                    <td align="left">Transportadora: </td>
                                    <td align="left"><b>{$mT003['D022_Nome_Empresa']}</b></td>
                                </tr>
                                <tr>
                                    <td align="left">Ordem compra: </td>
                                    <td align="left"><b>{$mT003['T003_Ordem_Compra']}</b></td>
                                </tr>
                                <tr>
                                    <td align="left">Peso líquido: </td>
                                    <td align="left"><b>{$mT003['T003_Peso_Total']} Kg</b></td>
                                </tr>
                                <tr>
                                    <td align="left">Peso bruto: </td>
                                    <td align="left"><b>{$mT003['T003_Peso_Bruto_Total']} Kg</b></td>
                                </tr>
                                <tr>
                                    <td align="left">Cubagem: </td>
                                    <td align="left"><b>{$mT003['T003_Total_Cubagem']}m³</b></td>
                                </tr>
                                <tr>
                                    <td align="left">Total itens: </td>
                                    <td align="left"><b>{$totalItens}</b></td>
                                </tr>
                            </table>
                        </td>
                        <td style="border-left: 1px solid #4F5F6E" width="50%" valign="top">
                            <table class="tabela" border=0 width="100%" valign="top">
                                {$desconto}
                                <tr>
                                    <td align="left">Valor IPI: </td>
                                    <td  align="left"><b>{$simboloMoeda} {$mT003['T003_Valor_Total_IPI']}</b></td>
                                </tr>
                                <tr>
EOT;
                    if($g['C031']['exibirDescontoImpressaoOrcPed'] == 'S'){
                        $html .= <<<EOT
                                    <td align="left">Valor desconto: </td>
                                    <td  align="left"><b>{$simboloMoeda} {$mT003['T003_Valor_Desconto_Manual']}</b></td>
                                </tr>
EOT;
                    }

                    $html .= <<<EOT
                                <tr>
                                    <td align="left">Valor frete: </td>
                                    <td  align="left"><b>{$simboloMoeda} {$mT003['T003_Valor_Frete']}</b></td>
                                </tr>
                                <tr>
                                    <td align="left" width="150">Despesas acessórias: </td>
                                    <td align="left"><b>{$simboloMoeda} {$mT003['T003_Valor_Despesas_Acessorias']}</b></td>
                                </tr>
                                <tr>
                                    <td align="left">Valor ST: </td>
                                    <td align="left"><b>{$simboloMoeda} {$mT003['T003_Valor_ST']}</b></td>
                                </tr>
                                <tr>
                                    <td align="left">Valor total: </td>
                                    <td align="left"  style="font-size:18px"><b>{$simboloMoeda} {$mT003['T003_Valor_Total']}</b></td>
                                </tr>
                                <tr>
                                    <td align="left">Valor ICMS: </td>
                                    <td align="left"><b>{$simboloMoeda} {$mT003['T003_Valor_Total_ICMS']}</b></td>
                                </tr>
EOT;    

            // Função personalizável para exibir ou não o Difal na impressão
            $exibirDifalImpressao = $this->opcaoExibirDifalImpressaoOrcamento($mT003);

            if ($exibirDifalImpressao) {
                $html .= <<<EOT
                            <tr>
                                <td align="left">Valor DIFAL: </td>
                                <td align="left"><b>{$simboloMoeda} {$mT003['T003_Valor_Total_Partilha_ICMS_Destino']}</b></td>
                            </tr>
EOT;
            }

            $html .= <<<EOT
                            </table>
                        </td>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="16" style="background:#4F5F6E;color:white;font-weight:bold; border-top:2px solid #4F5F6E; border-bottom:2px solid #4F5F6E;" align="center">
                    OBSERVAÇÃO  
                </td>
            </tr>
            <tr>
                <td colspan="16">
EOT;
        
        $html .= '<br>' . str_replace(chr(10), '<br>', '<b>' . $mT003['T003_Observacao']) . '</b><br><br>';
        
        $nomeSiteEmpresa = (!empty($sys['nomeEmpresa']) && !empty($sys['siteEmpresa'])) ? strToUpper($sys['nomeEmpresa']) .' - '. strToUpper($sys['siteEmpresa']) : 'HARDNESS SISTEMAS - WWW.HARDNESS.COM.BR';

        $html .= <<<EOT
                
                </td>
            </tr>
            <tr>
                <td colspan="16">
                </td>
            </tr>
        </table>
        <font style="font-size:10px;">{$nomeSiteEmpresa}</font>
        <br>
        <!-- <br class="clear" /> -->
        <center>
        <span style="width: 100%">{$g['C004']['C004_Observacao_Rodape_Orcamento']}</span><br>

EOT;
        return $html;
    }

    public function montarEmailPdfRepresentante($D021_Id){                             
        if($D021_Id > 0 ){
            $D021 = mysql_query("SELECT D021_Nome_Empresa,
                                    D021_DDD_Telefone_1,
                                    D021_Telefone_1
                               FROM D021 
                              WHERE D021_Id = '{$D021_Id}'");
            $mD021 = mysql_fetch_array($D021); 
            
            $representante = <<<EOT
                <tr>
                    <td><b><u>REPRESENTANTE:</u></b></td>
                </tr>
                <tr>
                    <td>{$mD021['D021_Nome_Empresa']}, {$mD021['D021_DDD_Telefone_1']}-{$mD021['D021_Telefone_1']}</td>
                </tr>
EOT;
        }else{
            $representante = '';
        }
        
        return $representante;
    }
    
    /**
     * VEN001montarPdfGrid
     *
     * @param String $sqlT003
     * @return array
     */
    public function VEN001montarPdfGrid($sqlT003)
    {
        global $g, $confUsuario;
        
        $resultT003 = mysql_query($sqlT003);
        
        
        $sqlC004    = "SELECT C004_Nome_Empresa FROM C004 WHERE C004_Id='{$g['empresaAtual']}'";
        $resultC004 = mysql_query($sqlC004);
        $C004       = mysql_fetch_array($resultC004);
        
        $sqlC007    = "SELECT * FROM C007 WHERE C007_Id='{$g['usuarioAtual']}'";
        $resultC007 = mysql_query($sqlC007);
        $C007       = mysql_fetch_array($resultC007);
        
        //$layout = $g['conf']->ini('layoutOrcamentoEmailPdf');
        
        // Array de retorno para criação do e-mail
        $array            = array();
        $array['Para']    = "";
        $array['Assunto'] = "Orçamentos";
        
        /**
         * PDF
         */
        require_once('bibliotecas/tcpdf/config/lang/eng.php');
        require_once('bibliotecas/tcpdf/tcpdf.php');
        
        // create new PDF document
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // SET document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Hardness Sistemas');
        // remove default header/footer
        $pdf->setPrintHeader(false);
        //$pdf->setPrintFooter(true);
        $pdf->SetMargins(8, 8, 8, 8);
        // SET font
        $pdf->SetFont('helvetica', '', 7);
        //SET auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        //SET image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        // Orientação da página
        $pdf->setPageOrientation('P');
        // add a page
        $pdf->AddPage();
        
        $html = '';
        /**
         * PDF - STYLE
         */
        $html .= <<<EOT
                <style>
                    .header .text {
                        font-size: 25px;
                    }
                    .header .text2 {
                        font-size: 35px;
                        font-weight: bold;
                    }
                    .titulo {
                        background-color: #E1ECF1;
                    }
                    .titulo_superior {
                        font-size: 30px;
                    }
                    .itens td {
                        border: 1px solid black;
                    }
                    .textIncluso {
                        font-size: 18px;
                        color: red;
                        background-color: #E1ECF1;
                    }
                    
                </style>
EOT;
        
        /**
         * PDF - HEADER
         */
        if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
            $caminhoImagem = "<img src=\"{$caminhoImagem}\">";
        } else {
            $caminhoImagem = "";
        }
        $html .= <<<EOT
        <table style='height:10px;border:1px solid black;'>
            <tr>
                <td align="left">{$g['c004']['C004_Nome_Empresa']}</td>
                <td align="right" style="font-size: 230%;"><b>Orçamentos</b></td>
            </tr>
        </table>
EOT;
        
        /**
         * PDF - TABELA PRODUTOS
         */
        
        if ($C007['C007_Flag_Vendedor'] == 'D' OR $C007['C007_Flag_Vendedor'] == 'S') {
            $html .= <<<EOT
            <div style="height:20px; width:100%;"></div>
                    <table class="itens" cellspacing="0" cellpadding="2">
                        <tr class="titulo">
                            <td style="width: 7%"; align="center";>Data</td>
                            <td style="width: 5%"; align=center>Nº</td>
                            <td style="width: 5%"; align="center">Cód</td>
                            <td style="width: 30%"; align="left">Cliente</td>
                            <td style="width: 10%;"; align="right">Produtos R$ </td>
                            <td style="width: 10%"; align="right">Serviços R$ </td>
                            <td style="width: 10%; "; align="right">Trocas R$ </td>
                            <td style="width: 10%"; align="right">Valor R$ </td>
                            <td style="width: 13%"; align="center">Vendedor</td>
                        </tr>
                    </table>
EOT;
        } else {
            $html .= <<<EOT
            <div style="height:20px; width:100%;"></div>
                    <table class="itens" cellspacing="0" cellpadding="2">
                        <tr class="titulo">
                            <td style="width: 7%"; align="center">Data</td>
                            <td style="width: 5%"; align=center>Nº</td>
                            <td style="width: 8%"; align="center">Cód</td>
                            <td style="width: 33%"; align="left">Cliente</td>
                            <td style="width: 12%"; align="right">Produtos R$ </td>
                            <td style="width: 12%"; align="right">Serviços R$ </td>
                            <td style="width: 10%"; align="right">Trocas R$ </td>
                            <td style="width: 12%"; align="right">Valor R$ </td>
                        </tr>
                    </table>
EOT;
        }
        
        
        $html .= <<<EOT
        <table class="itens" cellspacing="0" cellpadding="2">
EOT;
        while ($rowT003 = mysql_fetch_array($resultT003)) {
            ;
            $rowT003['T003_Valor_Total_Produtos'] = gCorrigeNumero($rowT003['T003_Valor_Total_Produtos']);
            $rowT003['T003_Valor_Total_Servicos'] = gCorrigeNumero($rowT003['T003_Valor_Total_Servicos']);
            $rowT003['T003_Valor_Troca']          = gCorrigeNumero($rowT003['T003_Valor_Troca']);
            $rowT003['T003_Valor_Total']          = gCorrigeNumero($rowT003['T003_Valor_Total']);
            $rowT003['T003_Data_Emissao']         = gCorrigeData($rowT003['T003_Data_Emissao']);
            $rowT003['D024_Nome_Empresa']         = gTruncar($rowT003['D024_Nome_Empresa'], 27, " ");
            $nomeCliente                          = $rowT003['D024_Nome_Empresa'];
            if ($rowT003['T003_Flag_Status_Orcamento'] == "A" OR $rowT003['T003_Flag_Status_Orcamento'] == 'F') {
                $corLinha = "color:#A1A1A1;";
                $sqlT005  = "SELECT T005_Id FROM T005 WHERE T005_T003_Id = '{$rowT003['T003_Id']}';";
                $resT005  = mysql_query($sqlT005);
                $rowT005  = mysql_fetch_assoc($resT005);
                if ($rowT005['T005_Id'] > 0) {
                    $nomeCliente = "{$rowT003['D024_Nome_Empresa']} ({$rowT005['T005_Id']})";
                }
                
            }
            
            if ($C007['C007_Flag_Vendedor'] == 'D' OR $C007['C007_Flag_Vendedor'] == 'S') {
                $html .= <<<EOT
            
                <tr>
                    <td style="width: 7%"; align="left">{$rowT003['T003_Data_Emissao']}</td>
                    <td style="width: 5%"; align="center">{$rowT003['T003_Id']}</td>
                    <td style="width: 5%"; align="center">{$rowT003['D024_Id']}</td>
                    <td style="width: 30%"; align="left">{$nomeCliente}</td>
                    <td style="width: 10%"; align="right">{$rowT003['T003_Valor_Total_Produtos']}</td>
                    <td style="width: 10%"; align="right">{$rowT003['T003_Valor_Total_Servicos']}</td>
                    <td style="width: 10%"; align="right">{$rowT003['T003_Valor_Troca']}</td>
                    <td style="width: 10%"; align="right">{$rowT003['T003_Valor_Total']}</td>
                    <td style="width: 13%"; align="center">{$rowT003['C007_Primeiro_Nome']}</td>
                </tr>
                
EOT;
            } else {
                $html .= <<<EOT
                <tr>
                    <td style="width: 7%"; align="left">{$rowT003['T003_Data_Emissao']}</td>
                    <td style="width: 5%"; align="center">{$rowT003['T003_Id']}</td>
                    <td style="width: 8%"; align="center">{$rowT003['D024_Id']}</td>
                    <td style="width: 33%"; align="left">{$nomeCliente}</td>
                    <td style="width: 12%"; align="right">{$rowT003['T003_Valor_Total_Produtos']}</td>
                    <td style="width: 12%"; align="right">{$rowT003['T003_Valor_Total_Servicos']}</td>
                    <td style="width: 10%"; align="right">{$rowT003['T003_Valor_Troca']}</td>
                    <td style="width: 12%"; align="right">{$rowT003['T003_Valor_Total']}</td>
                </tr>           
EOT;
                
            }
        }
        $html .= '</table>';
        
        
        /**
         * PDF - GERA!
         */
        $pdf->writeHTML($html, true, false, true, false, 'C');
        // reset pointer to the last page
        $pdf->lastPage();
        // get data
        $data = $pdf->getPDFData();
        
        // Gravar arquivo no disco
        $anexoDir = $g['pathDados'] . 'tmp/';
        is_dir($anexoDir) OR mkdir($anexoDir, 0777, true);
        $anexoNome = "{$anexoDir}orcamentoLista.pdf";
        @unlink($anexoNome);
        $h = fopen($anexoNome, 'w');
        fwrite($h, $data);
        fclose($h);
        // Adiciona como anexo
        $array['Anexos']    = array(
            $anexoNome
        );
        $array['AnexosWeb'] = array(
            '/hardness3/' . $anexoNome
        );
        // Logo         
        if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
        } else {
            $caminhoImagem = "";
        }
        // Variaveis enviadas para o modulo de e-mails (que serão gravadas na tabela T139)
        $array['Corpo'] = "<img src='{$caminhoImagem}' style='margin-left:10px'><br /><br /><br /><br /><font style='font-size:16px'>Lista de Orçamentos em anexo.</font><br /><br /><br />";
        
        return $array;
    }

    
    /**
     * varsImprimirOrcamento
     *
     * Function criada para ser personalizada, para returnar um array com dados usados na impressao do orçamemto
     * 
     * @param Int $T003_Id
     * @return array
     */    
    function varsImprimirOrcamento($T003_Id) 
    {
        $return = array();

        $return['prazoDias'] = ''; // Texto complemento do texto do prazo em dias 

        return $return;
    }

    /**
     * pagamento
     *
     * @param Form $form
     * @param String $T003_Id
     * @return String/bool
     */    
    public function pagamento($form, $T003_Id)
    {
        $Total_Parcelas = $form->campoValorEnviado('Total_Parcelas');
        $IntervalorDias = $form->campoValorEnviado('IntervalorDias');
        $D027_Id        = $form->campoValorEnviado('Portador_D027_Id');
        
        
        $cSql_T003 = mysql_query("SELECT * FROM T003 WHERE T003_Id='{$T003_Id}'");
        $mT003     = mysql_fetch_array($cSql_T003);
        
        $cSql_T089 = mysql_query("SELECT * FROM T089 LEFT JOIN D027 ON D027_Id=T089_D027_Id WHERE T089_T003_Id='{$T003_Id}'");
        while ($mT089 = mysql_fetch_array($cSql_T089)) {
            $mPrazoAntigo[$mT089['T089_Numero_Parcela']]    = $mT089['T089_Prazos_Dias'];
            $mPortadorAntigo[$mT089['T089_Numero_Parcela']] = $mT089['T089_D027_Id'];
        }
        
        mysql_query("delete T089 FROM T089 LEFT JOIN D027 ON D027_Id=T089_D027_Id WHERE T089_T003_Id='{$T003_Id}'");
        
        
        $Valor_Total_IPI       = $mT003['T003_Valor_Total_IPI'];
        $Valor_Total_Orcamento = $mT003['T003_Valor_Total'];
        
        if ($Total_Parcelas > 0) {
            $Valor_Titulo_Parcela       = ($Valor_Total_Orcamento - $Valor_Total_IPI) / $Total_Parcelas;
            $Valor_Total_Titulo         = ((round($Valor_Titulo_Parcela, 2) * $Total_Parcelas) + $Valor_Total_IPI);
            $Valor_Total_Titulo         = round($Valor_Total_Titulo, 2);
            $Diferenca_Primeira_Parcela = 0; // criado pra evitar erro de undefined no for
            if ($Valor_Total_Titulo != $Valor_Total_Orcamento) {
                $Diferenca_Primeira_Parcela = $Valor_Total_Orcamento - $Valor_Total_Titulo;
            }
        }
        
        
        for ($x = 1; $x <= $Total_Parcelas; $x++) {
            if ($x == 1) {
                $Valor_Titulo = ($Valor_Titulo_Parcela + $Valor_Total_IPI) + $Diferenca_Primeira_Parcela;
            } else {
                $Valor_Titulo = $Valor_Titulo_Parcela;
            }
            if ($D027_Id > 0) {
                $mPortadorAntigo[$x] = $D027_Id;
            }
            //if (empty($mPrazoAntigo[$x])) {
            $mPrazoAntigo[$x] = ($IntervalorDias * $x);
            //}
            $cClausula        = "insert into T089 (";
            $cClausula .= "T089_T003_Id,";
            $cClausula .= "T089_D027_Id,";
            $cClausula .= "T089_Numero_Parcela,";
            $cClausula .= "T089_Prazos_Dias,";
            $cClausula .= "T089_Data_Vencimento,";
            $cClausula .= "T089_Valor_Parcela";
            $cClausula .= ") VALUES (";
            $cClausula .= "'$T003_Id',";
            $cClausula .= "'$mPortadorAntigo[$x]',";
            $cClausula .= "'$x',";
            $cClausula .= "'$mPrazoAntigo[$x]',";
            $cClausula .= "DATE_ADD(current_date(),INTERVAL $mPrazoAntigo[$x] DAY),";
            $cClausula .= "'$Valor_Titulo'";
            $cClausula .= ")";
            mysql_query($cClausula);
            
            if (mysql_error()) {
                return 'Erro: ' . mysql_error();
            }
        }
        return true;
    }
    
    /**
     * insereServico
     *
     * @param Form $form
     * @param String $T145_Id
     * @return String/bool
     */    
    public function insereServico($form, $T145_Id)
    {       
        $D112_Id = $form->campoValorEnviado('T145_D112_Id');
        // baixa de tÃ­tulos
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
               
        $sql   = "SELECT * FROM D113 WHERE D113_D112_Id='{$D112_Id}'";
        $array = mysql_query($sql);
        
        while ($row = mysql_fetch_assoc($array)) {
            // D113_D112_Id, D113_D110_Id, D113_Titulo, D113_Descricao
            $sql = "INSERT INTO T146 (T146_T145_Id, T146_Titulo, T146_Descricao) VALUES ('" . mysql_real_escape_string($T145_Id) . "', '"
            //.mysql_real_escape_string($row['D113_D110_Id'])."', '"
                . mysql_real_escape_string($row['D113_Titulo']) . "', '" . mysql_real_escape_string($row['D113_Descricao']) . "');";
            mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return 'Erro: ' . mysql_error();
            }
        }
        
        $sql   = "SELECT * FROM D114 WHERE D114_D112_Id='{$D112_Id}'";
        $array = mysql_query($sql);
        
        while ($row = mysql_fetch_assoc($array)) {
            $sql = "INSERT INTO T152 (T152_D037_Id, T152_D009_Id, T152_T145_Id, T152_Quantidade) VALUES ('" . mysql_real_escape_string($row['D114_D037_Id']) . "', '" . mysql_real_escape_string($row['D114_D009_Id']) . "', '" . mysql_real_escape_string($T145_Id) . "', '" . mysql_real_escape_string($row['D114_Quantidade']) . "');";
            mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return 'Erro: ' . mysql_error();
            }
        }
        
        mysql_query("UPDATE T145 SET T145_Valor_Total = (T145_Valor_Unitario*T145_Quantidade) WHERE T145_Id=$T145_Id");
        
        mysql_query("COMMIT");
        return true;
    }
    
    /**
     * gerarPedido
     *
     * @param String $T003_Id
     * @return String/bool
     */
    public function gerarPedido($T003_Id)
    {
        global $g, $T005_Id;
        $T003_Id = (isset($T003_Id)) ? $T003_Id : false;

        //$T003_D024_Id;
        //$T003_Data_Emissao;
        // inserir um novo registro na T005 levando: T005_T003_Id, e os dois acima
        if ($T003_Id) {
            mysql_query("SET AUTOCOMMIT=0");
            mysql_query("START TRANSACTION");

            // Verificando se os T006 vinculados ao T004 possuem um T005
            $T004=mysql_query("SELECT T006_Id,
                                      T006_T005_Id,
                                      T006_T004_Id 
                                 FROM T004 
                            LEFT JOIN T006 ON T006_T004_Id=T004_Id 
                                WHERE T004_T003_Id ='{$T003_Id}'");

            while ($mT004 = mysql_fetch_array($T004)) {
                if($mT004['T006_Id'] > 0){
                    $T005=mysql_query("SELECT T005_Id 
                                         FROM T005 
                                        WHERE T005_Id='{$mT004['T006_T005_Id']}'");

                    $obs="T006_T004_Id: ".$mT004['T006_T004_Id'];
                    if(mysql_num_rows($T005)==0){
                        mysql_query("UPDATE T006 set T006_Observacao='{$obs}', T006_T004_Id='' WHERE T006_Id='{$mT004['T006_Id']}'");
                    }
                }
            }
            
            $sql2   = "SELECT SUM(T004_Quantidade_Confirmada) FROM T004 LEFT JOIN T006 ON T006_T004_Id=T004_Id WHERE T004_T003_Id ='$T003_Id' AND T006_T004_Id IS NULL";
            $array2 = mysql_query($sql2);
            $linha  = mysql_fetch_array($array2);

            $sql3   = "SELECT SUM(T145_Quantidade_Confirmada) FROM T145 LEFT JOIN T147 ON T147_T145_Id=T145_Id WHERE T145_T003_Id ='$T003_Id' AND T147_T145_Id IS NULL";
            $array3 = mysql_query($sql3);
            $linha2 = mysql_fetch_array($array3);

            $sqlT138    = "SELECT T138_Id FROM T138 WHERE T138_T003_Id='{$T003_Id}' AND T138_T005_Id IS NULL";
            $resultT138 = mysql_query($sqlT138);
            $linhaT138  = mysql_num_rows($resultT138);

            if ($linha[0] <= 0 && $linha2[0] <= 0 && $linhaT138 <= 0) {
                return "É necessário confirmar a quantidade de pelo menos um item, serviço ou troca.";
            }

            $T089  = mysql_query("SELECT SUM(T089_Valor_Parcela) as totalParcelas, 
                                         T003_Valor_Total, 
                                         T003_Flag_Acp 
                                    FROM T003 
                               LEFT JOIN T089 ON T089_T003_Id = T003_Id 
                                   WHERE T003_Id='{$T003_Id}'");

            $mT089 = mysql_fetch_array($T089);

            $obrigatorioGerarParcelaOrcamento = $g['C031']['obrigatorioGerarParcelaOrcamento'];
            if ($mT089['T003_Flag_Acp'] != 4 && number_format($mT089['totalParcelas'], 2) != number_format($mT089['T003_Valor_Total'], 2) && $obrigatorioGerarParcelaOrcamento == 'S') {
                return "O valor total das parcelas não confere com o total do orçamento, verifique a forma de pagamento.";
            }

            // Verifica se o banco vinculado aos portadores da forma de pagamento está ativo
            $T089 = mysql_query("SELECT IFNULL(D007_Flag_Ativo,0) as D007_Flag_Ativo, 
                                        IFNULL(D027_D007_Id,0) as D027_D007_Id,D027_Portador 
                                   FROM T089
                              LEFT JOIN D027 ON D027_Id=T089_D027_Id
                              LEFT JOIN D007 ON D007_Id=D027_D007_Id
                                  WHERE T089_T003_Id='{$T003_Id}'");

            while($mT089=mysql_fetch_array($T089)){
                if($mT089['D027_D007_Id'] > 0 && $mT089['D007_Flag_Ativo'] != 'S'){
                    return "O portador \"".$mT089['D027_Portador']."\" está com a conta bancária inativa. Por favor verifique com o setor financeiro";
                }
            }

            //Verifica se há itens do orçamento sem CFOP, NCM ou com origem em branco
            $retornoRestricao = $this->verificaDadosOrcamento($T003_Id);
            if($retornoRestricao !== true){
                return $retornoRestricao;
            }

            $permitirVenda = $g['C031']['permitirVendaSemEstoque'];
            $permitirVenda = (!empty($permitirVenda) ? $permitirVenda : 'N');

            $sqlEstoque  = mysql_query("SELECT T004_Id 
                                          FROM T004 
                                     LEFT JOIN T055 ON T055_D009_Id=T004_D009_Id AND T055_T075_Id=T004_T075_Id
                                     LEFT JOIN D009 ON D009_Id=T004_D009_Id 
                                     LEFT JOIN T006 ON T006_T004_Id=T004_Id 
                                     left join D006 on D006_Id=T004_D006_Id
                                         WHERE T004_T003_Id='{$T003_Id}' 
                                           AND (T004_Quantidade_Confirmada>IF(IFNULL(T004_T075_Id,0)>0,IFNULL(T055_Quantidade,0),IF(IFNULL(T004_T206_Id,0)>0,D009_Quantidade_DF(T004_D009_Id,T004_T206_Id, 0),IFNULL(D009_Quantidade_Estoque_Liquido,0))))
                                           AND T006_Id IS NULL 
                                           AND '{$permitirVenda}'='N'
                                           AND D006_Flag_Estoque ='D'");

            $rowsEstoque = mysql_num_rows($sqlEstoque);
            if ($rowsEstoque > 0) {
                $msg = ($rowsEstoque > 1 ? "Há itens sem estoque suficiente, favor verificar" : "Existe item sem estoque suficiente, favor verificar");
                return $msg;
            }

            $sqlEstoque  = mysql_query("SELECT T004_Id 
                                          FROM T004 
                                     LEFT JOIN D009 ON D009_Id=T004_D009_Id 
                                     LEFT JOIN D049 ON D049_Id=D009_D049_Id
                                     LEFT JOIN D001 ON D001_Id=D049_D001_Id
                                     LEFT JOIN T006 ON T006_T004_Id=T004_Id 
                                         WHERE T004_T003_Id='{$T003_Id}' 
                                           AND D001_Flag_Pre_Cadastro='S'
                                           AND (T004_T219_Id IS NULL OR T004_T219_Id <= 0)
                                           AND T004_Valor_Custo_Unitario <= 0
                                           AND T006_Id IS NULL
                                           AND T004_Quantidade_Confirmada>0");

            $rowsEstoque = mysql_num_rows($sqlEstoque);
            if ($rowsEstoque > 0 && $g['C031']['permitePedidoProdutoPreCadastro'] == 'N') {
                $msg = ($rowsEstoque > 1 ? "Há itens PRÉ-CADASTRO que não possuem retorno de cotação de compra, favor verificar" : "Existe item PRÉ-CADASTRO que não possui retorno de cotação de compra, favor verificar");
                return $msg;
            }

            // 22/01/2025 - Felipe Carrano
            // Validação por segurança porque em alguns casos estava zerando o T004_Percentual_Desconto e ajustando o T004_Valor_Preco_Unitario
            // Ainda não foi encontrado o problema
            $validaDesconto = $this->validaDescontoAplicado($T003_Id);
            if($validaDesconto !== true){
                return  $validaDesconto;
            }

            // Utilizado no APP  SOLICITA ATUALIZACAO PREÇO COTAÇÃO NO ORÇAMENTO
            $validaPrazoPreco = $this->validaPrazoPrecoCotacao($T003_Id);
            if($validaPrazoPreco !== true){
                return  $validaPrazoPreco;
            }

            $sql = "SELECT T003_Flag_Frete,
                           T003_D013_Id,
                           T003_D024_Id,
                           T003_D021_Id,
                           T003_C004_Id,
                           T003_D006_Id,
                           T003_C007_Id,
                           T003_D022_Id,
                           D020_D022_Id,
                           T003_D116_Id,
                           T003_D036_Id,
                           T003_T134_Id,
                           T003_D027_Id,
                           T003_C004_Id_Estoque,
                           T003_D148_Id_Entrega,
                           T003_D148_Id_Cobranca,
                           T003A_D148_Id_Redespacho,
                           T003_C007_Id_Vendedor_Interno,
                           T003_C007_Id_Vendedor_Externo,
                           D024_Flag_Transporta_Diferentes_UF,
                           T003_Flag_Status_Liberacao,
                           T003_Ordem_Compra,
                           T003_Observacao,
                           T003_Observacao_Comercial,
                           T003_Flag_Acp,
                           D024_Flag_Revenda_Consumidor,
                           T003_Observacao_Pagamento,
                           T003_Pgto_Parcelas,
                           T003_Pgto_Primeiro_Vcto_Tipo,
                           T003_Pgto_Primeiro_Vcto_Valor,
                           T003_Pgto_Dias_Vcto_Tipo,
                           T003_Pgto_Dias_Vcto_Valor,
                           T003_Pgto_Portador,
                           T003_Pgto_Data,
                           T003_Pgto_C007_Id,
                           T003_Liberou_Alcada_C007_Id,
                           T003_Liberou_Alcada_Data,
                           T003_Liberou_Alcada_IPV,
                           T003_Liberou_Alcada_Valor_Total,
                           T003_Liberou_Alcada_Prazo_Medio,
                           T003_Liberou_Alcada_Desconto,
                           T003_ST_ICMS_Interno,
                           T003_ICMS_Inter_Estadual,
                           T003_Flag_Lista_Preco,
                           T003_Valor_Frete,
                           T003_Valor_Frete_Calculado,
                           T003_Flag_Expedicao,
                           T003_Percentual_Comissao,
                           T003_Valor_Total_Comissao,
                           T003_Percentual_Comissao_Representante,
                           T003_Valor_Comissao_Representante,
                           T003_Percentual_Comissao_Externo,
                           T003_Valor_Total_Comissao_Externo,
                           T003_Percentual_Comissao_Usuario,
                           T003_Valor_Total_Comissao_Usuario,
                           T003_Percentual_Comissao_Manual,
                           T003_Valor_Desconto,
                           T003_Valor_Desconto_Manual,
                           T003_Total_Cubagem,
                           T003_Flag_Destino_Produto,
                           T003_Cliente_Area,
                           T003_Casas_Decimais_Preco,
                           T003_Valor_Despesas_Acessorias,
                           T003_Flag_Frete_Primeira_Parcela,
                           T003_Flag_ST_Primeira_Parcela,
                           T003_Flag_IPI_Primeira_Parcela,
                           T003_Pgto_Primeiro_Vcto_Valor_Data,
                           T003_Valor_Total,
                           T003A_Flag_Ratear_Frete_Custo,
                           T003A_Liberou_Alcada_Frete,
                           T003A_Flag_Conversao_Unidade,
                           D024_Flag_Comercial, 
                           D024_Flag_Pre_Cadastro,
                           T003A_Data_Entrega,
                           IFNULL(T003A_Valor_Total_Base_CBS_IBS, 0) AS T003A_Valor_Total_Base_CBS_IBS,
                           IFNULL(T003A_Valor_Total_CBS, 0) AS T003A_Valor_Total_CBS,
                           IFNULL(T003A_Valor_Total_IBS_Municipal, 0) AS T003A_Valor_Total_IBS_Municipal,
                           IFNULL(T003A_Valor_Total_IBS_UF, 0) AS T003A_Valor_Total_IBS_UF,
                           IFNULL(T003A_Valor_Total_IBS, 0) AS T003A_Valor_Total_IBS,
                           IFNULL(T003A_Valor_Total_Diferimento_CBS, 0) AS T003A_Valor_Total_Diferimento_CBS,
                           IFNULL(T003A_Valor_Total_Diferimento_IBS_UF, 0) AS T003A_Valor_Total_Diferimento_IBS_UF,
                           IFNULL(T003A_Valor_Total_Diferimento_IBS_Municipal, 0) AS T003A_Valor_Total_Diferimento_IBS_Municipal,
                           IFNULL(T003A_Valor_Total_Reforma, 0) AS T003A_Valor_Total_Reforma
                      FROM T003 
                 LEFT JOIN D006 ON D006_Id=T003_D006_Id 
                 LEFT JOIN D024 ON D024_Id=T003_D024_Id 
                 LEFT JOIN D020 ON D020_Id=D024_D020_Id 
                 LEFT JOIN D022 ON D022_Id=T003_D022_Id
                 LEFT JOIN T003A ON T003_Id=T003A_T003_Id
                     WHERE T003_Id='{$T003_Id}'";

            $array = mysql_query($sql);
            $error = mysql_error();
            if (!empty($error)) {
                mysql_query("ROLLBACK");
                return '1 Erro: ' . $error;
            }

            $orcamento = mysql_fetch_assoc($array);

            if ($orcamento['D024_Flag_Pre_Cadastro'] == 'S' && $g['C031']['permitePedidoClientePreCadastro'] == 'N') {
                $msg = "O Cliente esta em PRÉ-CADASTRO verificar.";
                return $msg;
            }
            
            if($orcamento['T003_Flag_Frete'] != 9 AND $g['C031']['permiteTransportarDiferentesUF'] == 'N' AND $orcamento['D024_Flag_Transporta_Diferentes_UF'] == 'N'){
                $permiteTransporte = mysql_query("SELECT UFFaturamento.D018_UF as faturamento,  
                                                         UFEntrega.D018_UF as entrega,
                                                         D022_Id
                                                    FROM T003
                                                    LEFT JOIN D022 ON D022_Id=T003_D022_Id
                                                    LEFT JOIN D018 AS UFFaturamento ON UFFaturamento.D018_Id=D022_D018_Id
                                                    LEFT JOIN D148 ON D148_Id=T003_D148_Id_Entrega
                                                    LEFT JOIN D018 AS UFEntrega ON UFEntrega.D018_Id=D148_D018_Id
                                                    WHERE T003_Id='$T003_Id'");
                $mPermiteTransporte = mysql_fetch_array($permiteTransporte);
                if($mPermiteTransporte['faturamento'] != $mPermiteTransporte['entrega']){
                    mysql_query("ROLLBACK");
                    return "UF ".$mPermiteTransporte['faturamento']." da transportadora é diferente da UF ".$mPermiteTransporte['entrega']." do cliente, favor verificar.";
                }
            }

            $T005_Data_Entrega = $g['conf']->camposValorPadrao('T005_Data_Entrega');
            $T005_Data_Entrega = (!empty($T005_Data_Entrega)) ? $T005_Data_Entrega : $orcamento['T003A_Data_Entrega'];

            $totalizarPeso = ($g['C031']['totalizarPesoVenda'] == 'S') ? 'S' : 'N';

            //if ($orcamento['D024_Flag_Pessoa_Fisica_Juridica'] == "J") {
            //    if (empty($orcamento['D024_Cnpj'])) {
            //        return "CNPJ da empresa em branco";
            //    }
            //} else if ($orcamento['D024_Flag_Pessoa_Fisica_Juridica'] == "F" && $orcamento['T003_Flag_Acp'] != 4) {
            //    if (empty($orcamento['D024_Cpf'])) {
            //        return "Cpf do cliente em branco";
            //    }
            //}

            //Ajuste flag status liberação
            //Caso o orçamento não gerou parcela, mudamos a flag para 1 (não passou por liberação), pois no pedido se for gerado parcela, é preciso passar por análise
            $sqlT089 = mysql_query("SELECT T089_Id FROM T089 WHERE T089_T003_Id = '{$T003_Id}'");
            if(mysql_num_rows($sqlT089) <= 0){
                //mysql_query("ROLLBACK");
                $orcamento['T003_Flag_Status_Liberacao'] = '1';
            }

            // funcao personalizada por cliente
            $excecoesValidaT003 = $this->excecoesValidaT003($orcamento);
            if($excecoesValidaT003 !== true){
                mysql_query("ROLLBACK");
                return $excecoesValidaT003;
            }

            $sql = "INSERT INTO T005 (T005_T003_Id,
                                      T005_D013_Id,
                                      T005_D024_Id,
                                      T005_D021_Id,
                                      T005_C004_Id,
                                      T005_D006_Id,
                                      T005_C007_Id,
                                      T005_C007_Id_Vendedor_Interno,
                                      T005_C007_Id_Vendedor_Externo,
                                      T005_Ordem_Compra,
                                      T005_Observacao,
                                      T005_Observacao_Comercial,
                                      T005_Observacao_Nota_Fiscal,
                                      T005_Flag_ACP,
                                      T005_Flag_Status,
                                      T005_Data_Emissao,
                                      T005_Hora_Emissao,
                                      T005_Flag_Situacao,
                                      T005_Flag_Revenda_Consumidor,
                                      T005_D022_Id,
                                      T005_D022_Id_Default,
                                      T005_Observacao_Pagamento,
                                      T005_Pgto_Parcelas,
                                      T005_Pgto_Primeiro_Vcto_Tipo,
                                      T005_Pgto_Primeiro_Vcto_Valor,
                                      T005_Pgto_Dias_Vcto_Tipo,
                                      T005_Pgto_Dias_Vcto_Valor,
                                      T005_Pgto_Portador,
                                      T005_Pgto_Data,
                                      T005_Pgto_C007_Id,
                                      T005_D116_Id,
                                      T005_Liberou_Alcada_C007_Id,
                                      T005_Liberou_Alcada_Data,
                                      T005_Liberou_Alcada_IPV,
                                      T005_Liberou_Alcada_Valor_Total,
                                      T005_Liberou_Alcada_Prazo_Medio,
                                      T005_Liberou_Alcada_Desconto,
                                      T005_ST_ICMS_Interno,
                                      T005_ICMS_Inter_Estadual,
                                      T005_Flag_Frete,
                                      T005_Flag_Lista_Preco,
                                      T005_Valor_Frete,
                                      T005_Valor_Frete_Calculado,
                                      T005_Flag_Expedicao,
                                      T005_Percentual_Comissao,
                                      T005_Valor_Total_Comissao,
                                      T005_Percentual_Comissao_Representante,
                                      T005_Valor_Comissao_Representante,
                                      T005_Percentual_Comissao_Externo,
                                      T005_Valor_Total_Comissao_Externo,
                                      T005_Percentual_Comissao_Usuario,
                                      T005_Valor_Total_Comissao_Usuario,
                                      T005_Percentual_Comissao_Manual,
                                      T005_Valor_Desconto,
                                      T005_Valor_Desconto_Manual,
                                      T005_Flag_Almoxarifado,
                                      T005_Total_Cubagem,
                                      T005_Flag_Destino_Produto,
                                      T005_D036_Id,
                                      T005_D148_Id_Entrega,
                                      T005_D148_Id_Cobranca,
                                      T005_Flag_Status_Liberacao,
                                      T005_Cliente_Area,
                                      T005_Casas_Decimais_Preco,
                                      T005_Data_Entrega,
                                      T005_Flag_Totalizar_Peso,
                                      T005_C004_Id_Estoque,
                                      T005_Valor_Despesas_Acessorias,
                                      T005_D027_Id,
                                      T005_Flag_Frete_Primeira_Parcela,
                                      T005_Flag_ST_Primeira_Parcela,
                                      T005_T134_Id,
                                      T005_Flag_IPI_Primeira_Parcela,
                                      T005_Pgto_Primeiro_Vcto_Valor_Data
                                      ) VALUES (
                                       '" . mysql_real_escape_string($T003_Id) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_D013_Id']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_D024_Id']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_D021_Id']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_C004_Id']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_D006_Id']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_C007_Id']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_C007_Id_Vendedor_Interno']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_C007_Id_Vendedor_Externo']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Ordem_Compra']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Observacao']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Observacao_Comercial']) . "',
                                       '',
                                       '" . mysql_real_escape_string($orcamento['T003_Flag_Acp']) . "',
                                       '0',
                                       CURDATE(),
                                       CURTIME(),
                                       '1',
                                       '" . mysql_real_escape_string($orcamento['D024_Flag_Revenda_Consumidor']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_D022_Id']) . "',
                                       '" . mysql_real_escape_string($orcamento['D020_D022_Id']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Observacao_Pagamento']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Pgto_Parcelas']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Pgto_Primeiro_Vcto_Tipo']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Pgto_Primeiro_Vcto_Valor']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Pgto_Dias_Vcto_Tipo']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Pgto_Dias_Vcto_Valor']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Pgto_Portador']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Pgto_Data']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Pgto_C007_Id']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_D116_Id']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Liberou_Alcada_C007_Id']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Liberou_Alcada_Data']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Liberou_Alcada_IPV']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Liberou_Alcada_Valor_Total']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Liberou_Alcada_Prazo_Medio']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Liberou_Alcada_Desconto']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_ST_ICMS_Interno']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_ICMS_Inter_Estadual']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Flag_Frete']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Flag_Lista_Preco']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Valor_Frete']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Valor_Frete_Calculado']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Flag_Expedicao']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Percentual_Comissao']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Valor_Total_Comissao']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Percentual_Comissao_Representante']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Valor_Comissao_Representante']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Percentual_Comissao_Externo']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Valor_Total_Comissao_Externo']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Percentual_Comissao_Usuario']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Valor_Total_Comissao_Usuario']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Percentual_Comissao_Manual']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Valor_Desconto']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Valor_Desconto_Manual']) . "',
                                       'N',
                                       '" . mysql_real_escape_string($orcamento['T003_Total_Cubagem']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Flag_Destino_Produto']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_D036_Id']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_D148_Id_Entrega']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_D148_Id_Cobranca']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Flag_Status_Liberacao']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Cliente_Area']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Casas_Decimais_Preco']) . "',
                                       '{$T005_Data_Entrega}',
                                       '{$totalizarPeso}',
                                       '" . mysql_real_escape_string($orcamento['T003_C004_Id_Estoque']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Valor_Despesas_Acessorias']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_D027_Id']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Flag_Frete_Primeira_Parcela']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Flag_ST_Primeira_Parcela']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_T134_Id']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Flag_IPI_Primeira_Parcela']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Pgto_Primeiro_Vcto_Valor_Data']) . "'
                                     );";
            log($sql);
            $retorno = mysql_query($sql);
            $erro    = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return '2 Erro: ' . $erro;
            }
            $T005_Id = $g['mysqlLastId'];

            //INSERE REGISTRO NA TABELA ESTENDIDA
            mysql_query("INSERT INTO T005A(T005A_T005_Id,
                                           T005A_Flag_Ratear_Frete_Custo,
                                           T005A_Liberou_Alcada_Frete,
                                           T005A_Flag_Conversao_Unidade,
                                           T005A_D148_Id_Redespacho,
                                           T005A_Valor_Total_Base_CBS_IBS,
                                           T005A_Valor_Total_CBS,
                                           T005A_Valor_Total_IBS_Municipal,
                                           T005A_Valor_Total_IBS_UF,
                                           T005A_Valor_Total_IBS,
                                           T005A_Valor_Total_Diferimento_CBS,
                                           T005A_Valor_Total_Diferimento_IBS_UF,
                                           T005A_Valor_Total_Diferimento_IBS_Municipal,
                                           T005A_Valor_Total_Reforma)
                                  VALUES ('{$T005_Id}',
                                          '{$orcamento['T003A_Flag_Ratear_Frete_Custo']}',
                                          '{$orcamento['T003A_Liberou_Alcada_Frete']}',
                                          '{$orcamento['T003A_Flag_Conversao_Unidade']}',
                                          '{$orcamento['T003A_D148_Id_Redespacho']}',
                                          '{$orcamento['T003A_Valor_Total_Base_CBS_IBS']}',
                                          '{$orcamento['T003A_Valor_Total_CBS']}',
                                          '{$orcamento['T003A_Valor_Total_IBS_Municipal']}',
                                          '{$orcamento['T003A_Valor_Total_IBS_UF']}',
                                          '{$orcamento['T003A_Valor_Total_IBS']}',
                                          '{$orcamento['T003A_Valor_Total_Diferimento_CBS']}',
                                          '{$orcamento['T003A_Valor_Total_Diferimento_IBS_UF']}',
                                          '{$orcamento['T003A_Valor_Total_Diferimento_IBS_Municipal']}',
                                          '{$orcamento['T003A_Valor_Total_Reforma']}')");
            $erro = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return '3 Erro ao inserir os dados complementares do pedido: ' . $erro;
            }

            //Insere registro do pedido na tarefa
            //require_once('bibliotecas/classes/ETC017.php');
            //$ETC017 = new ETC017();
            //$ETC017->inserirRegistroTarefa($T005_Id, 'T005');

            //Insere registros no histórico do pedido (T178)
            mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Gerou Pedido','')");
            //Define o Orçamento como Fechado
            mysql_query("UPDATE T003 SET T003_Flag_Perdido='F' WHERE T003_Id={$T003_Id}");

            /* Personalização: Não inserir as parcelas, apenas gravar a forma de pagamento na T005
            //selecionar da T089 e inserir na T090 (pagamento)
            $query7 = "SELECT * FROM T089 WHERE T089_T003_Id='$T003_Id'";
            $result9 = mysql_query($query7);
            while($row = mysql_fetch_array($result9)){
            $sql90 = <<<EOT
            INSERT INTO T090 (T090_T005_Id, T090_D027_Id, T090_Numero_Parcela, T090_Prazos_Dias, T090_Data_Vencimento, T090_Valor_Parcela) 
            VALUES
            ('{$T005_Id}', '{$row['T089_D027_Id']}', '{$row['T089_Numero_Parcela']}', '{$row['T089_Prazos_Dias']}', '{$row['T089_Data_Vencimento']}', '{$row['T089_Valor_Parcela']}')
            EOT;
            $retorno .= $sql90."<br ></EOT>";
            $result = mysql_query($sql90);
            if (mysql_error()) {
            mysql_query("ROLLBACK");
            return '22: Erro Parcelas: ' . mysql_error();
            }
            }*/

            //Insere os documentos do orçamento no pedido.
            $sqlDocumentos    = "SELECT * FROM T188 WHERE T188_T003_Id='{$T003_Id}'";
            $resultDocumentos = mysql_query($sqlDocumentos);
            while ($linhaDocumentos = mysql_fetch_array($resultDocumentos)) {
                // Os campos do documento podem conter aspas simples (por exemplo, na
                // descrição). Escape-os antes de montar o INSERT para não invalidar
                // a consulta nem alterar os valores gravados.
                $T167_T005_Id           = mysql_real_escape_string($T005_Id);
                $T167_C007_Id           = mysql_real_escape_string($linhaDocumentos['T188_C007_Id']);
                $T167_Nome_Arquivo      = mysql_real_escape_string($linhaDocumentos['T188_Nome_Arquivo']);
                $T167_Descricao         = mysql_real_escape_string($linhaDocumentos['T188_Descricao']);
                $T167_Data_Hora_Upload  = mysql_real_escape_string($linhaDocumentos['T188_Data_Hora_Upload']);
                $sql            = <<<EOT
                INSERT INTO T167 
                (T167_T005_Id, T167_C007_Id, T167_Nome_Arquivo, T167_Descricao, T167_Data_Hora_Upload)
                VALUES
                ('{$T167_T005_Id}','{$T167_C007_Id}','{$T167_Nome_Arquivo}','{$T167_Descricao}','{$T167_Data_Hora_Upload}')
EOT;
                $resultCopiaDoc = mysql_query($sql);
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return '19 Erro: ' . mysql_error();
                }

                //----Copia o arquivo da pasta de orçamentos para pasta de pedidos
                //Pega a extensão do arquivo
                $pathinfo              = pathinfo($linhaDocumentos['T188_Nome_Arquivo']);
                $pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];

                //Pega o caminho do arquivo no orçamento
                $dirOrigem = "{$g['pathDados']}orcamento";
                is_dir($dirOrigem) OR mkdir($dirOrigem, 0777, true);
                $fileOrigem = $dirOrigem . '/' . $linhaDocumentos['T188_Id'] . '.' . $pathinfo['extension'];

                $dirDestino = "{$g['pathDados']}pedido";
                is_dir($dirDestino) OR mkdir($dirDestino, 0777, true);
                $fileDestino = $dirDestino . '/' . $g['mysqlLastId'] . '.' . $pathinfo['extension'];
                copy($fileOrigem, $fileDestino);
            }

            // neste momento insere apenas os serviços (T145) que nao estão associados a produtos (avulsos)
            $sql    = "SELECT * FROM T145 WHERE T145_T003_Id='{$T003_Id}' AND T145_T004_Id IS NULL";
            $result = mysql_query($sql);
            $error = mysql_error();
            if (!empty($error)) {
                mysql_query("ROLLBACK");
                return '3 Erro: ' . $error;
            }

            while ($orcamentoSer = mysql_fetch_array($result)) {

                $sql     = "INSERT INTO T147 (T147_T005_Id, 
                                              T147_T145_Id, 
                                              T147_D110_Id,
                                              T147_D112_Id,
                                              T147_Titulo, 
                                              T147_Descricao, 
                                              T147_Tempo_Estimado, 
                                              T147_Quantidade,
                                              T147_Valor_Unitario,
                                              T147_Valor_Total,
                                              T147_Hora_Inicio,
                                              T147_Hora_Fim,
                                              T147_Status,
                                              T147_D116_Id) 
                                              VALUES 
                                              ('" . mysql_real_escape_string($T005_Id) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T145_Id']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T145_D110_Id']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T145_D112_Id']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T145_Titulo']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T145_Descricao']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T145_Tempo_Estimado']) . "',
                                                                     '1',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T145_Valor_Unitario']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T145_Valor_Unitario']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T145_Hora_Inicio']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T145_Hora_Fim']) . "',
                                                                     'P',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T145_D116_Id']) . "'
                                                                     );";
                $retorno = mysql_query($sql); 
                $error = mysql_error();             
                if (!empty($error)) {
                    mysql_query("ROLLBACK");
                    return '4 Erro: ' . $error;
                }
                $T147_Id = $g['mysqlLastId'];
                
                //selecionar todos da T146 onde T146_T145_Id = T145_Id
                //mais um looping para cada T146 inserir um registro na T148
                
                $sql     = "SELECT * FROM T146 WHERE T146_T145_Id=" . $orcamentoSer['T145_Id'];
                $result2 = mysql_query($sql);
                $error = mysql_error();
                if (!empty($error)) {
                    mysql_query("ROLLBACK");
                    return '5 Erro: ' . $error;
                }
                
                while ($processo = mysql_fetch_assoc($result2)) {
                    $sql     = "INSERT INTO T148 (T148_Id,
                                                  T148_T147_Id,
                                                  T148_Titulo,
                                                  T148_Descricao)
                                                  VALUES
                                                  ('',
                                                   '" . mysql_real_escape_string($T147_Id) . "',
                                                   '" . mysql_real_escape_string($processo['T146_Titulo']) . "',
                                                   '" . mysql_real_escape_string($processo['T146_Descricao']) . "'
                                                   );";
                    $retorno = mysql_query($sql);
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return '11.2 Erro: ' . mysql_error();
                    }
                }
                $sql     = "SELECT * FROM T152 WHERE T152_T145_Id=" . $orcamentoSer['T145_Id'];
                $result3 = mysql_query($sql);
                $error = mysql_error();
                if (!empty($error)) {
                    mysql_query("ROLLBACK");
                    return '5 Erro: ' . $error;
                }

                while ($material = mysql_fetch_assoc($result3)) {
                    $sql = "INSERT INTO T149(T149_Id,
                                                T149_D009_Id,
                                                T149_D037_Id,
                                                T149_T147_Id,
                                                T149_Quantidade)
                                                VALUES
                                                ('',
                                                 '" . mysql_real_escape_string($material['T152_D009_Id']) . "',
                                                 '" . mysql_real_escape_string($material['T152_D037_Id']) . "',
                                                 '" . mysql_real_escape_string($T147_Id) . "',
                                                 '" . mysql_real_escape_string($material['T152_Quantidade']) . "'
                                                 );";
                    $retorno = mysql_query($sql);
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return '7 Erro: ' . mysql_error();
                    }
                }
            }

            //selecionar todos da T004 onde t004_T003_Id = $T003
            //inserir na t006 todos da T004 selecionados
            $sql = <<<EOT
                SELECT T004_D009_Id,
                       T004_Id,
                       D009_Id,
                       D009_Quantidade_Estoque_Liquido,
                       T004_Valor_Preco_Original,
                       T004_Valor_Preco_Sem_Desconto_Unitario,
                       T004_Codigo_Substituto,
                       T004_Item,
                       T004_D037_Id,
                       T004_D005_Id,
                       T004_D006_Id,
                       D005_Classificacao_Fiscal,
                       T004_Codigo_Produto,
                       T004_Marca,
                       T004_Descricao_Produto,
                       T004_Quantidade_Confirmada,
                       T004_Valor_Preco_Bruto,
                       T004_Valor_Custo_Unitario,
                       T004_Valor_Preco_Unitario,
                       T004_Valor_Preco_Original_Bruto,
                       T004_Percentual_Desconto_Bruto,
                       T004_IPV,
                       T004_C004_Id,
                       T004_Numero_Pedido_Compra,
                       T004_Item_Pedido_Compra,
                       T004_Aliquota_IPI,
                       T004_Aliquota_ICMS,
                       T004_Percentual_Reducao_ICMS,
                       T004_Percentual_Reducao_ICMS_ST,
                       T004_ST_VA,
                       T004_Flag_ST,
                       T004_Flag_Situacao_Tributaria,
                       T004_ICMS_Inter_Estadual,
                       T004_ST_ICMS_Interno,
                       T004_Flag_Isento_PIS,
                       T004_Aliquota_PIS,
                       T004_Situacao_Tributaria_PIS,
                       T004_Flag_Isento_COFINS,
                       T004_Aliquota_COFINS,
                       T004_Flag_Preco_Especial,
                       T004_Situacao_Tributaria_COFINS,
                       T004_Situacao_Tributaria_IPI,
                       T004_D059_Id,
                       T004_Valor_Preco_Original,
                       T004_Percentual_Desconto,
                       T004_Liberou_Alcada_Saldo_Futuro,
                       D009_Quantidade_Estoque_Liquido,
                       T004_T075_Id,
                       T004_T206_Id,
                       T004_Cubagem_Unitaria,
                       T004_Total_Cubagem,
                       T004_Flag_Pre_Cadastro,
                       T004_Demonstrativo_Preco,
                       T004_Demonstrativo_Preco_Tabela,
                       T004_Demonstrativo_Preco_Transferencia,
                       T004_Demonstrativo_Preco_Cotacao,
                       T004_Valor_Preco_Compra,
                       T004_Valor_Preco_Tabela,
                       T004_Valor_Preco_Transferencia,
                       T004_Valor_Custo_Transferencia,
                       T004_Flag_Origem_Produto_Transferencia,
                       T004_Valor_Preco_Cotacao,
                       T004_T001_Id,
                       T004_Flag_Destino_Produto,
                       T004_Flag_Origem_Produto,
                       T004_Observacao_Entrega,
                       T004_Observacao,
                       T004_Observacao_Compra,
                       T004_D006_Id_Info,
                       T004_D005_Id_Info,
                       T004_T219_Id,
                       T004_Valor_ICMS_Desonerado,
                       T004_Motivo_Desoneracao_ICMS,
                       T004_Demonstrativo_Custo_Tabela,
                       T004_Demonstrativo_Preco_Venda,
                       T004_Aliquota_IRPJ,
                       T004_Aliquota_CSLL,
                       T004_Flag_Isento_IRPJ,
                       T004_Flag_Isento_CSLL,
                       T004_Flag_Transferencia,
                       T004_C004_Id_Transferencia,
                       T004_UF_ICMS_Substituicao_Devido,
                       T004_Percentual_Base_Operacao_Propria,
                       T004_Percentual_Trib_Federal,
                       T004_Percentual_Trib_Estadual,
                       T004_Percentual_Trib_Municipal,
                       T004_Aliquota_ICMS_Credito,
                       T004_Valor_ICMS_Credito,
                       T004_Preco_Pauta_ST,
                       T004_Modalidade_ICMS_Substituicao,
                       T004_Modalidade_ICMS,
                       T004_Codigo_Local,
                       T004A_T066_Id,
                       D009_Quantidade_OC_3(D009_Id,0) as D009_Quantidade_OC,
                       T004A_Percentual_Reducao_PIS,
                       T004A_Percentual_Reducao_COFINS,
                       T004A_Quantidade_Conversao,
                       T004A_Quantidade_Comercial,
                       T004A_Preco_Unitario_Comercial,
                       T004A_D184_Id,
                       T004A_Quantidade_Comercial_Confirmada,
                       T004A_Percentual_FCP,             
                       T004A_Valor_Base_FCP,                 
                       T004A_Valor_FCP,                          
                       T004A_Valor_Base_FCP_ST,
                       T004A_Valor_FCP_ST,                       
                       T004A_Valor_Base_FCP_Dest,
                       T004A_Valor_FCP_Partilha,
                       T004A_Percentual_Desconto_CFOP,
                       IFNULL(T004A_Situacao_Tributaria_CBS_IBS, '') AS T004A_Situacao_Tributaria_CBS_IBS,
                       IFNULL(T004A_Classificacao_Tributaria_CBS_IBS, '') AS T004A_Classificacao_Tributaria_CBS_IBS,
                       IFNULL(T004A_Situacao_Tributaria_CBS_IBS_Regular, '') AS T004A_Situacao_Tributaria_CBS_IBS_Regular,
                       IFNULL(T004A_Classificacao_Tributaria_CBS_IBS_Regular, '') AS T004A_Classificacao_Tributaria_CBS_IBS_Regular,
                       IFNULL(T004A_Aliquota_CBS, 0) AS T004A_Aliquota_CBS,
                       IFNULL(T004A_Percentual_Reducao_CBS, 0) AS T004A_Percentual_Reducao_CBS,
                       IFNULL(T004A_Aliquota_CBS_Efetiva, 0) AS T004A_Aliquota_CBS_Efetiva,
                       IFNULL(T004A_Valor_CBS, 0) AS T004A_Valor_CBS,
                       IFNULL(T004A_Percentual_Diferimento_CBS, 0) AS T004A_Percentual_Diferimento_CBS,
                       IFNULL(T004A_Valor_Diferimento_CBS, 0) AS T004A_Valor_Diferimento_CBS,
                       IFNULL(T004A_Aliquota_CBS_Regular, 0) AS T004A_Aliquota_CBS_Regular,
                       IFNULL(T004A_Percentual_Reducao_CBS_Regular, 0) AS T004A_Percentual_Reducao_CBS_Regular,
                       IFNULL(T004A_Aliquota_CBS_Efetiva_Regular, 0) AS T004A_Aliquota_CBS_Efetiva_Regular,
                       IFNULL(T004A_Valor_CBS_Regular, 0) AS T004A_Valor_CBS_Regular,
                       IFNULL(T004A_Aliquota_IBS_Municipal, 0) AS T004A_Aliquota_IBS_Municipal,
                       IFNULL(T004A_Percentual_Reducao_IBS_Municipal, 0) AS T004A_Percentual_Reducao_IBS_Municipal,
                       IFNULL(T004A_Aliquota_IBS_Municipal_Efetiva, 0) AS T004A_Aliquota_IBS_Municipal_Efetiva,
                       IFNULL(T004A_Valor_IBS_Municipal, 0) AS T004A_Valor_IBS_Municipal,
                       IFNULL(T004A_Percentual_Diferimento_IBS_Municipal, 0) AS T004A_Percentual_Diferimento_IBS_Municipal,
                       IFNULL(T004A_Valor_Diferimento_IBS_Municipal, 0) AS T004A_Valor_Diferimento_IBS_Municipal,
                       IFNULL(T004A_Aliquota_IBS_Municipal_Regular, 0) AS T004A_Aliquota_IBS_Municipal_Regular,
                       IFNULL(T004A_Percentual_Reducao_IBS_Municipal_Regular, 0) AS T004A_Percentual_Reducao_IBS_Municipal_Regular,
                       IFNULL(T004A_Aliquota_IBS_Municipal_Efetiva_Regular, 0) AS T004A_Aliquota_IBS_Municipal_Efetiva_Regular,
                       IFNULL(T004A_Valor_IBS_Municipal_Regular, 0) AS T004A_Valor_IBS_Municipal_Regular,
                       IFNULL(T004A_Aliquota_IBS_UF, 0) AS T004A_Aliquota_IBS_UF,
                       IFNULL(T004A_Percentual_Reducao_IBS_UF, 0) AS T004A_Percentual_Reducao_IBS_UF,
                       IFNULL(T004A_Aliquota_IBS_UF_Efetiva, 0) AS T004A_Aliquota_IBS_UF_Efetiva,
                       IFNULL(T004A_Valor_IBS_UF, 0) AS T004A_Valor_IBS_UF,
                       IFNULL(T004A_Percentual_Diferimento_IBS_UF, 0) AS T004A_Percentual_Diferimento_IBS_UF,
                       IFNULL(T004A_Valor_Diferimento_IBS_UF, 0) AS T004A_Valor_Diferimento_IBS_UF,
                       IFNULL(T004A_Aliquota_IBS_UF_Regular, 0) AS T004A_Aliquota_IBS_UF_Regular,
                       IFNULL(T004A_Percentual_Reducao_IBS_UF_Regular, 0) AS T004A_Percentual_Reducao_IBS_UF_Regular,
                       IFNULL(T004A_Aliquota_IBS_UF_Efetiva_Regular, 0) AS T004A_Aliquota_IBS_UF_Efetiva_Regular,
                       IFNULL(T004A_Valor_IBS_UF_Regular, 0) AS T004A_Valor_IBS_UF_Regular,
                       IFNULL(T004A_Valor_Base_CBS_IBS, 0) AS T004A_Valor_Base_CBS_IBS,
                       IFNULL(T004A_Valor_Total_Reforma, 0) AS T004A_Valor_Total_Reforma,
                       IFNULL(T004A_Valor_IBS, 0) AS T004A_Valor_IBS
                  FROM T004 
             LEFT JOIN T006 ON T006_T004_Id=T004_Id 
             LEFT JOIN D009 ON D009_Id=T004_D009_Id
             LEFT JOIN D005 ON D005_Id=T004_D005_Id
             LEFT JOIN T004A ON T004_Id=T004A_T004_Id
                 WHERE T004_T003_Id='{$T003_Id}' 
                   AND T006_Id is null 
                   AND T004_Quantidade_Confirmada>0;
EOT;
            $retornoConsulta = mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return '8 Erro: ' . mysql_error();
            }

            if (mysql_num_rows($retornoConsulta) <= 0) {
                //mysql_query("ROLLBACK");
                // return 'Não existem ítens para gerar pedido.';
                // O produto pode ter apenas serviços. Regra retirada
            }
            // variavel que soma quantos itens do orÃ§amento foram inseridos no pedido
            $countConfirmados = 0;
            $restricaoProduto = '';
            while ($linha = mysql_fetch_array($retornoConsulta)) {
                /*              // seleciona todos os pedidos gerados a partir do orÃ§amento atual
                $sql = "SELECT * FROM T005 WHERE T005_T003_Id='$T003_Id';";
                $ret = mysql_query($sql);
                // variavel que verifica se o item do orÃ§amento atual jÃ¡ estÃ¡ em algum pedido ja gerado deste orÃ§amento
                $incluir=true;
                // para cada pedido jÃ¡ gerado a partir do orÃ§amento atual
                while($reg = mysql_fetch_assoc($ret)){
                // seleciona os itens deste pedido
                // e verifica se algum destes itens é igual ao item atual
                $sql1 = "SELECT * FROM T006 WHERE T006_T005_Id='{$reg['T005_Id']}';";
                $ret1 = mysql_query($sql1);
                $item=mysql_fetch_assoc($ret1);
                if (!empty($item['T006_D009_Id'])){
                if ($item['T006_T004_Id']==$linha['T004_Id']){
                $incluir=false; 
                }
                }
                }
                */
                // Atualiza a quantidade de estoque no pedido

                
                //Conferido
                                                    //Conferido
                $verificacaoItemOrcamento = $this->PersonalizacaoTriggerVerificarItemOrcamento($linha['T004_Id'], $linha['T004_Quantidade_Confirmada']);
                if($verificacaoItemOrcamento !== true)
                {
                    mysql_query("ROLLBACK");
                    return $verificacaoItemOrcamento;
                }

                $T006_D009 = mysql_query("SELECT D009_Quantidade_Estoque_Liquido 
                                            FROM D009 
                                           WHERE D009_Id='{$linha['T004_D009_Id']}'");

                $mT006_D009 = mysql_fetch_assoc($T006_D009);
                $linha['D009_Quantidade_Estoque_Liquido'] = $mT006_D009['D009_Quantidade_Estoque_Liquido'];

                $linha1['D009_Quantidade_Estoque_Liquido'] = $mT006_D009['D009_Quantidade_Estoque_Liquido'];

                if ($g['C031']['permitePedidoProdutoPreCadastro'] == 'N') {
                    //Verifica se existe algum produto pré cadastro
                    $sqlRestricao     = "SELECT T004_Codigo_Produto,
                                                D001_Flag_Pre_Cadastro 
                                           FROM T004 
                                      LEFT JOIN D009 ON D009_Id=T004_D009_Id 
                                      LEFT JOIN D049 ON D049_Id=D009_D049_Id
                                      LEFT JOIN D001 ON D001_Id=D049_D001_Id
                                          WHERE T004_T003_Id='$T003_Id' 
                                            AND T004_Quantidade_Confirmada > 0 
                                            AND T004_Id = {$linha['T004_Id']}";

                    $retornoRestricao = mysql_query($sqlRestricao);
                    $rowRestricao     = mysql_fetch_array($retornoRestricao);
                    if ($rowRestricao['D001_Flag_Pre_Cadastro'] == 'S') {
                        $restricaoProduto .= "O produto " . $rowRestricao['T004_Codigo_Produto'] . " é pré-cadastro.<br />";
                        continue;
                    }
                }

                $linha['T004_Valor_Preco_Original'] = ($linha['T004_Valor_Preco_Original'] > 0 ? $linha['T004_Valor_Preco_Original'] : $linha['T004_Valor_Preco_Sem_Desconto_Unitario']);
                $countConfirmados++;
                $sql  = "INSERT INTO T006 (T006_Codigo_Substituto,
                                          T006_Flag_Status,
                                          T006_Item,
                                          T006_T004_Id,
                                          T006_D037_Id,
                                          T006_D005_Id,
                                          T006_D006_Id,
                                          T006_Classificacao_Fiscal,
                                          T006_D009_Id,
                                          T006_Codigo_Produto,
                                          T006_Marca,
                                          T006_Descricao_Produto,
                                          T006_Quantidade,
                                          T006_Valor_Preco_Bruto,
                                          T006_Valor_Custo_Unitario,
                                          T006_Valor_Preco_Unitario,
                                          T006_Valor_Preco_Sem_Desconto_Unitario,
                                          T006_Valor_Preco_Original_Bruto,
                                          T006_Percentual_Desconto_Bruto,
                                          T006_IPV,
                                          T006_T005_Id,
                                          T006_C004_Id,
                                          T006_Numero_Pedido_Compra,
                                          T006_Item_Pedido_Compra,
                                          T006_Aliquota_IPI,
                                          T006_Aliquota_ICMS,
                                          T006_Percentual_Reducao_ICMS,
                                          T006_Percentual_Reducao_ICMS_ST,
                                          T006_ST_VA,
                                          T006_Flag_ST,
                                          T006_Flag_Situacao_Tributaria,
                                          T006_ICMS_Inter_Estadual,
                                          T006_ST_ICMS_Interno,
                                          T006_Flag_Isento_PIS,
                                          T006_Aliquota_PIS,
                                          T006_Situacao_Tributaria_PIS,
                                          T006_Flag_Isento_COFINS,
                                          T006_Aliquota_COFINS,
                                          T006_Flag_Preco_Especial,
                                          T006_Situacao_Tributaria_COFINS,
                                          T006_Situacao_Tributaria_IPI,
                                          T006_D059_Id,
                                          T006_Valor_Preco_Original,
                                          T006_Percentual_Desconto,
                                          T006_Liberou_Alcada_Saldo_Futuro,
                                          T006_Quantidade_Estoque,
                                          T006_T075_Id,
                                          T006_T206_Id,
                                          T006_Cubagem_Unitaria,
                                          T006_Total_Cubagem,
                                          T006_Flag_Pre_Cadastro,
                                          T006_Demonstrativo_Preco,
                                          T006_Demonstrativo_Preco_Tabela,
                                          T006_Demonstrativo_Preco_Transferencia,
                                          T006_Demonstrativo_Preco_Cotacao,
                                          T006_Valor_Preco_Compra,
                                          T006_Valor_Preco_Tabela,
                                          T006_Valor_Preco_Transferencia,
                                          T006_Valor_Custo_Transferencia,
                                          T006_Flag_Origem_Produto_Transferencia,
                                          T006_Valor_Preco_Cotacao,
                                          T006_T001_Id,
                                          T006_Flag_Destino_Produto,
                                          T006_Flag_Origem_Produto,
                                          T006_Observacao_Entrega,
                                          T006_Observacao,
                                          T006_Observacao_Compra,
                                          T006_D006_Id_Info,
                                          T006_D005_Id_Info,
                                          T006_T219_Id,
                                          T006_Valor_ICMS_Desonerado,
                                          T006_Motivo_Desoneracao_ICMS,
                                          T006_Demonstrativo_Custo_Tabela,
                                          T006_Quantidade_OC,
                                          T006_Demonstrativo_Preco_Venda,
                                          T006_Aliquota_IRPJ,
                                          T006_Aliquota_CSLL,
                                          T006_Flag_Isento_IRPJ,
                                          T006_Flag_Isento_CSLL,
                                          T006_Flag_Transferencia,
                                          T006_C004_Id_Transferencia,
                                          T006_UF_ICMS_Substituicao_Devido,
                                          T006_Percentual_Base_Operacao_Propria,
                                          T006_Percentual_Trib_Federal,
                                          T006_Percentual_Trib_Estadual,
                                          T006_Percentual_Trib_Municipal,
                                          T006_Aliquota_ICMS_Credito,
                                          T006_Valor_ICMS_Credito,
                                          T006_Preco_Pauta_ST,
                                          T006_Modalidade_ICMS_Substituicao,
                                          T006_Modalidade_ICMS,
                                          T006_Codigo_Local,
                                          T006_T066_Id
                                          )
                                      VALUES
                                         ('" . mysql_real_escape_string($linha['T004_Codigo_Substituto']) . "',
                                          '',
                                          '" . mysql_real_escape_string($linha['T004_Item']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Id']) . "',
                                          '" . mysql_real_escape_string($linha['T004_D037_Id']) . "',
                                          '" . mysql_real_escape_string($linha['T004_D005_Id']) . "',
                                          '" . mysql_real_escape_string($linha['T004_D006_Id']) . "',
                                          '" . mysql_real_escape_string($linha['D005_Classificacao_Fiscal']) . "',
                                          '" . mysql_real_escape_string($linha['T004_D009_Id']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Codigo_Produto']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Marca']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Descricao_Produto']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Quantidade_Confirmada']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Valor_Preco_Bruto']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Valor_Custo_Unitario']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Valor_Preco_Unitario']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Valor_Preco_Unitario']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Valor_Preco_Original_Bruto']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Percentual_Desconto_Bruto']) . "',
                                          '" . mysql_real_escape_string($linha['T004_IPV']) . "',
                                          '" . mysql_real_escape_string($T005_Id) . "',
                                          '" . mysql_real_escape_string($linha['T004_C004_Id']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Numero_Pedido_Compra']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Item_Pedido_Compra']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Aliquota_IPI']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Aliquota_ICMS']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Percentual_Reducao_ICMS']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Percentual_Reducao_ICMS_ST']) . "',
                                          '" . mysql_real_escape_string($linha['T004_ST_VA']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Flag_ST']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Flag_Situacao_Tributaria']) . "',
                                          '" . mysql_real_escape_string($linha['T004_ICMS_Inter_Estadual']) . "',
                                          '" . mysql_real_escape_string($linha['T004_ST_ICMS_Interno']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Flag_Isento_PIS']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Aliquota_PIS']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Situacao_Tributaria_PIS']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Flag_Isento_COFINS']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Aliquota_COFINS']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Flag_Preco_Especial']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Situacao_Tributaria_COFINS']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Situacao_Tributaria_IPI']) . "',
                                          '" . mysql_real_escape_string($linha['T004_D059_Id']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Valor_Preco_Original']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Percentual_Desconto']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Liberou_Alcada_Saldo_Futuro']) . "',
                                          '" . mysql_real_escape_string($linha['D009_Quantidade_Estoque_Liquido']) . "',
                                          '" . mysql_real_escape_string($linha['T004_T075_Id']) . "',
                                          '" . mysql_real_escape_string($linha['T004_T206_Id']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Cubagem_Unitaria']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Total_Cubagem']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Flag_Pre_Cadastro']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Demonstrativo_Preco']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Demonstrativo_Preco_Tabela']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Demonstrativo_Preco_Transferencia']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Demonstrativo_Preco_Cotacao']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Valor_Preco_Compra']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Valor_Preco_Tabela']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Valor_Preco_Transferencia']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Valor_Custo_Transferencia']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Flag_Origem_Produto_Transferencia']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Valor_Preco_Cotacao']) . "',
                                          '" . mysql_real_escape_string($linha['T004_T001_Id']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Flag_Destino_Produto']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Flag_Origem_Produto']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Observacao_Entrega']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Observacao']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Observacao_Compra']) . "',
                                          '" . mysql_real_escape_string($linha['T004_D006_Id_Info']) . "',
                                          '" . mysql_real_escape_string($linha['T004_D005_Id_Info']) . "',
                                          '" . mysql_real_escape_string($linha['T004_T219_Id']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Valor_ICMS_Desonerado']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Motivo_Desoneracao_ICMS']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Demonstrativo_Custo_Tabela']) . "',
                                          '" . mysql_real_escape_string($linha['D009_Quantidade_OC']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Demonstrativo_Preco_Venda']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Aliquota_IRPJ']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Aliquota_CSLL']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Flag_Isento_IRPJ']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Flag_Isento_CSLL']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Flag_Transferencia']) . "',
                                          '" . mysql_real_escape_string($linha['T004_C004_Id_Transferencia']) . "',
                                          '" . mysql_real_escape_string($linha['T004_UF_ICMS_Substituicao_Devido']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Percentual_Base_Operacao_Propria']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Percentual_Trib_Federal']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Percentual_Trib_Estadual']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Percentual_Trib_Municipal']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Aliquota_ICMS_Credito']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Valor_ICMS_Credito']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Preco_Pauta_ST']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Modalidade_ICMS_Substituicao']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Modalidade_ICMS']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Codigo_Local']) . "',
                                          '" . mysql_real_escape_string($linha['T004A_T066_Id']) . "'

                                          );";                                          
                                          log('passando orçamento p/ pedido Modalidade ICMS: ' . $linha['T004_Modalidade_ICMS']);
                $ret  = mysql_query($sql, false);
                $erro = mysql_error();
                if (!empty($erro)) {
                    mysql_query("ROLLBACK");
                    return '9 Erro: ' . $erro;
                }

                $T006_Id = $g['mysqlLastId'];

                // Processa estoque por locação
                require_once('bibliotecas/classes/CAD002.php');
                $CAD002 = new CAD002();

                $CAD002->processaEstoqueLiquidoPorLocacao($linha['T004_D009_Id']);
                $CAD002->reprocessaOCPedidos($linha['T004_D009_Id']);

                //INSERE REGISTRO NA TABELA ESTENDIDA
                 mysql_query("INSERT INTO T006A(T006A_T006_Id,
                                                T006A_Percentual_Reducao_PIS,
                                                T006A_Percentual_Reducao_COFINS,
                                                T006A_Quantidade_Conversao,
                                                T006A_Quantidade_Comercial,
                                                T006A_Preco_Unitario_Comercial,
                                                T006A_D184_Id,
                                                T006A_Data_Hora_Insercao,
                                                T006A_Percentual_FCP,             
                                                T006A_Valor_Base_FCP,                  
                                                T006A_Valor_FCP,                          
                                                T006A_Valor_Base_FCP_ST,
                                                T006A_Valor_FCP_ST,                       
                                                T006A_Valor_Base_FCP_Dest,       
                                                T006A_Valor_FCP_Partilha,
                                                T006A_Percentual_Desconto_CFOP,
                                                T006A_Situacao_Tributaria_CBS_IBS,
                                                T006A_Classificacao_Tributaria_CBS_IBS,
                                                T006A_Situacao_Tributaria_CBS_IBS_Regular,
                                                T006A_Classificacao_Tributaria_CBS_IBS_Regular,
                                                T006A_Aliquota_CBS,
                                                T006A_Percentual_Reducao_CBS,
                                                T006A_Aliquota_CBS_Efetiva,
                                                T006A_Valor_CBS,
                                                T006A_Percentual_Diferimento_CBS,
                                                T006A_Valor_Diferimento_CBS,
                                                T006A_Aliquota_CBS_Regular,
                                                T006A_Percentual_Reducao_CBS_Regular,
                                                T006A_Aliquota_CBS_Efetiva_Regular,
                                                T006A_Valor_CBS_Regular,
                                                T006A_Aliquota_IBS_Municipal,
                                                T006A_Percentual_Reducao_IBS_Municipal,
                                                T006A_Aliquota_IBS_Municipal_Efetiva,
                                                T006A_Valor_IBS_Municipal,
                                                T006A_Percentual_Diferimento_IBS_Municipal,
                                                T006A_Valor_Diferimento_IBS_Municipal,
                                                T006A_Aliquota_IBS_Municipal_Regular,
                                                T006A_Percentual_Reducao_IBS_Municipal_Regular,
                                                T006A_Aliquota_IBS_Municipal_Efetiva_Regular,
                                                T006A_Valor_IBS_Municipal_Regular,
                                                T006A_Aliquota_IBS_UF,
                                                T006A_Percentual_Reducao_IBS_UF,
                                                T006A_Aliquota_IBS_UF_Efetiva,
                                                T006A_Valor_IBS_UF,
                                                T006A_Percentual_Diferimento_IBS_UF,
                                                T006A_Valor_Diferimento_IBS_UF,
                                                T006A_Aliquota_IBS_UF_Regular,
                                                T006A_Percentual_Reducao_IBS_UF_Regular,
                                                T006A_Aliquota_IBS_UF_Efetiva_Regular,
                                                T006A_Valor_IBS_UF_Regular,
                                                T006A_Valor_Base_CBS_IBS,
                                                T006A_Valor_Total_Reforma,
                                                T006A_Valor_IBS)
                                        VALUES ('{$T006_Id}',
                                                '{$linha['T004A_Percentual_Reducao_PIS']}',
                                                '{$linha['T004A_Percentual_Reducao_COFINS']}',
                                                '{$linha['T004A_Quantidade_Conversao']}',
                                                '{$linha['T004A_Quantidade_Comercial_Confirmada']}',
                                                '{$linha['T004A_Preco_Unitario_Comercial']}',
                                                '{$linha['T004A_D184_Id']}',
                                                NOW(),
                                                '{$linha['T004A_Percentual_FCP']}',             
                                                '{$linha['T004A_Valor_Base_FCP']}',                  
                                                '{$linha['T004A_Valor_FCP']}',                          
                                                '{$linha['T004A_Valor_Base_FCP_ST']}',
                                                '{$linha['T004A_Valor_FCP_ST']}',                       
                                                '{$linha['T004A_Valor_Base_FCP_Dest']}',       
                                                '{$linha['T004A_Valor_FCP_Partilha']}',
                                                '{$linha['T004A_Percentual_Desconto_CFOP']}',
                                                '{$linha['T004A_Situacao_Tributaria_CBS_IBS']}',
                                                '{$linha['T004A_Classificacao_Tributaria_CBS_IBS']}',
                                                '{$linha['T004A_Situacao_Tributaria_CBS_IBS_Regular']}',
                                                '{$linha['T004A_Classificacao_Tributaria_CBS_IBS_Regular']}',
                                                '{$linha['T004A_Aliquota_CBS']}',
                                                '{$linha['T004A_Percentual_Reducao_CBS']}',
                                                '{$linha['T004A_Aliquota_CBS_Efetiva']}',
                                                '{$linha['T004A_Valor_CBS']}',
                                                '{$linha['T004A_Percentual_Diferimento_CBS']}',
                                                '{$linha['T004A_Valor_Diferimento_CBS']}',
                                                '{$linha['T004A_Aliquota_CBS_Regular']}',
                                                '{$linha['T004A_Percentual_Reducao_CBS_Regular']}',
                                                '{$linha['T004A_Aliquota_CBS_Efetiva_Regular']}',
                                                '{$linha['T004A_Valor_CBS_Regular']}',
                                                '{$linha['T004A_Aliquota_IBS_Municipal']}',
                                                '{$linha['T004A_Percentual_Reducao_IBS_Municipal']}',
                                                '{$linha['T004A_Aliquota_IBS_Municipal_Efetiva']}',
                                                '{$linha['T004A_Valor_IBS_Municipal']}',
                                                '{$linha['T004A_Percentual_Diferimento_IBS_Municipal']}',
                                                '{$linha['T004A_Valor_Diferimento_IBS_Municipal']}',
                                                '{$linha['T004A_Aliquota_IBS_Municipal_Regular']}',
                                                '{$linha['T004A_Percentual_Reducao_IBS_Municipal_Regular']}',
                                                '{$linha['T004A_Aliquota_IBS_Municipal_Efetiva_Regular']}',
                                                '{$linha['T004A_Valor_IBS_Municipal_Regular']}',
                                                '{$linha['T004A_Aliquota_IBS_UF']}',
                                                '{$linha['T004A_Percentual_Reducao_IBS_UF']}',
                                                '{$linha['T004A_Aliquota_IBS_UF_Efetiva']}',
                                                '{$linha['T004A_Valor_IBS_UF']}',
                                                '{$linha['T004A_Percentual_Diferimento_IBS_UF']}',
                                                '{$linha['T004A_Valor_Diferimento_IBS_UF']}',
                                                '{$linha['T004A_Aliquota_IBS_UF_Regular']}',
                                                '{$linha['T004A_Percentual_Reducao_IBS_UF_Regular']}',
                                                '{$linha['T004A_Aliquota_IBS_UF_Efetiva_Regular']}',
                                                '{$linha['T004A_Valor_IBS_UF_Regular']}',
                                                '{$linha['T004A_Valor_Base_CBS_IBS']}',
                                                '{$linha['T004A_Valor_Total_Reforma']}',
                                                '{$linha['T004A_Valor_IBS']}')");
                $erro = mysql_error();
                if (!empty($erro)) {
                    mysql_query("ROLLBACK");
                    return '10 Erro ao inserir os dados complementares do item: ' . $erro;
                }

                //ATUALIZA UMA REQUISIÇÃO JA CRIADA COM T006_Id
                mysql_query("UPDATE T189 SET T189_T006_Id = '{$T006_Id}', T189_Flag_Status = 'N' WHERE T189_T004_Id = '{$linha['T004_Id']}'");
                // mysql_query("UPDATE T189 SET T189_Flag_Status = 'N' WHERE T189_T006_Id = '{$g['mysqlLastId']}'");

                /*
                JA GRAVA TOTALIZACAO QUANDO É CHAMADO O VEN002->atualizarAliquotasPedidos, UPDATE NO T006 E NA TRIGGER É CHAMADO O T006 E T005 GRAVAR TOTALIZACAO
                mysql_query("call T006_Gravar_Totalizacao_4($T006_Id)");
                $error = mysql_error();
                if (!empty($error)) {
                    mysql_query("ROLLBACK");
                    return '10 Erro: ' . $error;
                }*/

                //Atualiza a Cotação já existente para o orçamento, com o número do pedido
                mysql_query("UPDATE T219 SET T219_T006_Id = '{$T006_Id}' WHERE T219_T004_Id = '{$linha['T004_Id']}'");

                //Limpa o campo T004_Flag_Comprar para não gerar lentidão
                mysql_query("UPDATE T004 SET T004_Flag_Comprar='N' WHERE T004_Id = '{$linha['T004_Id']}'");

                //SELECT T006_Id, T066_Id FROM T006 LEFT JOIN T066 ON T066_D009_Id=T006_D009_Id WHERE T006_Id = '294670'

                //Busca D049_Id para executar function copiarEngenhariaProduto
                // Vai ser feito no final da função por conta da transaction que é ativada no copiarEngenhariaProduto
                // E pode acabar dando conflito com a transaction ativada no gerarPedido
/*                 $D049 = mysql_query("SELECT D049_Flag_Tipo 
                                       FROM D049 
                                  LEFT JOIN D009 ON D009_D049_Id=D049_Id 
                                      WHERE D009_Id='{$linha['D009_Id']}'");

                $mD049 = mysql_fetch_array($D049);

                if($mD049['D049_Flag_Tipo'] == 'PD'){
                    require_once('bibliotecas/classes/VEN002.php');
                    $VEN002 = new VEN002();
                    $VEN002->copiarEngenhariaProduto($T006_Id);
                } */

                // verificar se tem algum T145 com T145_T004_Id igual ao T004_Id que acabou de ser inserido na T006
                // ou seja, insere na T147 os serviços que estão associados ao ítem que acabou de ser inserido 

                $sql    = "SELECT * FROM T145 WHERE T145_T003_Id='$T003_Id' AND T145_T004_Id='{$linha['T004_Id']}'";
                $result = mysql_query($sql);
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return 'Erro: ' . mysql_error();
                }

                while ($orcamentoSer = mysql_fetch_array($result)) {

                    $sql     = "INSERT INTO T147 (T147_T005_Id, 
                                              T147_T145_Id, 
                                              T147_D110_Id,
                                              T147_D112_Id,
                                              T147_Titulo, 
                                              T147_Descricao, 
                                              T147_Tempo_Estimado, 
                                              T147_Quantidade,
                                              T147_Valor_Unitario,
                                              T147_Valor_Total,
                                              T147_Hora_Inicio,
                                              T147_Hora_Fim,
                                              T147_Status,
                                              T147_T006_Id, 
                                              T147_D116_Id)
                                              VALUES 
                                              ('" . mysql_real_escape_string($T005_Id) . "',
                                               '" . mysql_real_escape_string($orcamentoSer['T145_Id']) . "',
                                               '" . mysql_real_escape_string($orcamentoSer['T145_D110_Id']) . "',
                                               '" . mysql_real_escape_string($orcamentoSer['T145_D112_Id']) . "',
                                               '" . mysql_real_escape_string($orcamentoSer['T145_Titulo']) . "',
                                               '" . mysql_real_escape_string($orcamentoSer['T145_Descricao']) . "',
                                               '" . mysql_real_escape_string($orcamentoSer['T145_Tempo_Estimado']) . "',
                                                                     '1',
                                               '" . mysql_real_escape_string($orcamentoSer['T145_Valor_Unitario']) . "',
                                               '" . mysql_real_escape_string($orcamentoSer['T145_Valor_Unitario']) . "',
                                               '" . mysql_real_escape_string($orcamentoSer['T145_Hora_Inicio']) . "',
                                               '" . mysql_real_escape_string($orcamentoSer['T145_Hora_Fim']) . "',
                                                                     'P',
                                               '" . mysql_real_escape_string($T006_Id) . "',
                                               '" . mysql_real_escape_string($orcamentoSer['T145_D116_Id']) . "'
                                               );";
                    $retorno = mysql_query($sql);
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return '4 Erro: ' . mysql_error();
                    }
                    $T147_Id = $g['mysqlLastId'];

                    //selecionar todos da T146 onde T146_T145_Id = T145_Id
                    //mais um looping para cada T146 inserir um registro na T148

                    $sql     = "SELECT * FROM T146 WHERE T146_T145_Id=" . $orcamentoSer['T145_Id'];
                    $result2 = mysql_query($sql);
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return '5 Erro: ' . mysql_error();
                    }

                    while ($processo = mysql_fetch_assoc($result2)) {
                        $sql     = "INSERT INTO T148 (T148_Id,
                                                  T148_T147_Id,
                                                  T148_Titulo,
                                                  T148_Descricao)
                                                  VALUES
                                                  ('',
                                                   '" . mysql_real_escape_string($T147_Id) . "',
                                                   '" . mysql_real_escape_string($processo['T146_Titulo']) . "',
                                                   '" . mysql_real_escape_string($processo['T146_Descricao']) . "'
                                                   );";
                        $retorno = mysql_query($sql);
                        if (mysql_error()) {
                            mysql_query("ROLLBACK");
                            return '11.3 Erro: ' . mysql_error();
                        }
                    }
                    $sql     = "SELECT * FROM T152 WHERE T152_T145_Id=" . $orcamentoSer['T145_Id'];
                    $result3 = mysql_query($sql);
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return '5 Erro: ' . mysql_error();
                    }
                    
                    while ($material = mysql_fetch_assoc($result3)) {
                        $sql = "INSERT INTO T149(T149_Id,
                                                T149_D009_Id,
                                                T149_D037_Id,
                                                T149_T147_Id,
                                                T149_Quantidade)
                                                VALUES
                                                ('',
                                                 '" . mysql_real_escape_string($material['T152_D009_Id']) . "',
                                                 '" . mysql_real_escape_string($material['T152_D037_Id']) . "',
                                                 '" . mysql_real_escape_string($T147_Id) . "',
                                                 '" . mysql_real_escape_string($material['T152_Quantidade']) . "'
                                                 );";
                        
                        $retorno = mysql_query($sql);
                        if (mysql_error()) {
                            mysql_query("ROLLBACK");
                            return '7 Erro: ' . mysql_error();
                        }
                    }    
                }
                
                //Copia agregados do produto
                $sql = "SELECT * FROM T202 WHERE T202_T004_Id='{$linha['T004_Id']}'";
                
                $result = mysql_query($sql);
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return 'Erro: ' . mysql_error();
                }
                
                while ($orcamentoSer = mysql_fetch_array($result)) {
                    $sql     = "INSERT INTO T204 (T204_T006_Id, 
                                                      T204_Descricao, 
                                                      T204_Observacao,
                                                      T204_Valor_Unitario)
                                                      VALUES 
                                                      ('" . mysql_real_escape_string($T006_Id) . "',
                                                       '" . mysql_real_escape_string($orcamentoSer['T202_Descricao']) . "',
                                                       '" . mysql_real_escape_string($orcamentoSer['T202_Observacao']) . "',
                                                       '" . mysql_real_escape_string($orcamentoSer['T202_Valor_Unitario']) . "'
                                                       );";
                    $retorno = mysql_query($sql);
                    //return $sql;                 
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return '4 Erro: ' . mysql_error();
                    }
                }
            }
            
            
            $sql     = "SELECT * FROM T147 WHERE T147_T005_Id='{$T005_Id}'";
            $result9 = mysql_query($sql);
            while ($linhaVeiculo = mysql_fetch_array($result9)) {
                //Verificando se já existe o veículo de T147 cadastrado na T171
                $sql    = "SELECT * FROM T171 WHERE T171_D116_Id='{$linhaVeiculo['T147_D116_Id']}' AND T171_T005_Id='{$T005_Id}'";
                $result = mysql_query($sql);
                if (mysql_num_rows($result) == 0) {
                    $sql           = <<<EOT
                    INSERT INTO T171 
                    (T171_D116_Id, T171_T005_Id, T171_T147_Id)
                    VALUES
                    ('{$linhaVeiculo['T147_D116_Id']}','{$T005_Id}','{$linhaVeiculo['T147_Id']}')
EOT;
                    $resultEntrega = mysql_query($sql);
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return '19 Erro: ' . mysql_error();
                    }
                }
            }
            
            
            mysql_query("UPDATE T138 SET T138_T005_Id='$T005_Id' WHERE T138_T003_Id='$T003_Id'");
            $error = mysql_error();
            if (!empty($error)) {
                mysql_query("ROLLBACK");
                return 'Erro: ' . $error;
            }
            
            /*
            JA GRAVA TOTALIZACAO QUANDO É CHAMADO O VEN002->atualizarAliquotasPedidos, UPDATE NO T006 E NA TRIGGER É CHAMADO O T006 E T005 GRAVAR TOTALIZACAO
            mysql_query("call T005_Gravar_Totalizacao_4($T005_Id)");
            $error = mysql_error();
            if (!empty($error)) {
                mysql_query("ROLLBACK");
                return '11 Erro: ' . $error;
            }
            */

            /*
            QUANDO GERA O PEDIDO O ORÇAMENTO JA FOI TOTALIZADO
            mysql_query("call T003_Gravar_Totalizacao_4($T003_Id)");
            $error = mysql_error();
            if (!empty($error)) {
                mysql_query("ROLLBACK");
                return '11 Erro: ' . $error;
            }*/

            if (!empty($restricaoProduto)) {
                mysql_query("ROLLBACK");
                return $restricaoProduto . "<br ></EOT>Por favor conclua o cadastro dos itens acima.";
            }
            
            $sqlT089 = mysql_query("SELECT T089_Numero_Parcela,
                                           T089_D027_Id,
                                           T089_Valor_Parcela,
                                           AVG(T089_Prazos_Dias) as PrazoMedio 
                                      FROM T089 
                                     WHERE T089_T003_Id = '{$T003_Id}'");

            $T089    = mysql_fetch_array($sqlT089);
            mysql_query("UPDATE T005 SET T005_Prazo_Medio_Orcamento = '{$T089['PrazoMedio']}' WHERE T005_Id = '{$T005_Id}'");
            
            require_once("bibliotecas/classes/VEN002.php");
            $VEN002 = new VEN002();
                       
            // T185
            require_once('prog/outros/distribuicaoItemPedidoEstoque.php');
            
            // Gera as parcelas
            require_once('bibliotecas/classes/GFormWrap.php');
            if ($mT005['T005_Pgto_Dias_Vcto_Tipo'] == "Intervalo") {
                $mT005['T005_Pgto_Dias_Vcto_Tipo'] = 'I';
            } else {
                $mT005['T005_Pgto_Dias_Vcto_Tipo'] = 'F';
            }
            $dadosTabela = array('tabela' => 'T005',
                                             'tabelaId' =>  $T005_Id,
                                             'campos' => 'T005_Pgto_Primeiro_Vcto_Tipo as primeiroVencimentoTipo,
                                                 T005_Pgto_Primeiro_Vcto_Valor as primeiroVencimentoNum,
                                                 T005_Pgto_Primeiro_Vcto_Valor_Data as primeiroVencimentoData,
                                                 T005_Pgto_Dias_Vcto_Tipo as diasVctoTipo,
                                                 T005_Pgto_Dias_Vcto_Valor as valorIntervaloDiaFixo,
                                                 T005_D027_Id as D027_Id,
                                                 T005_Flag_Frete_Primeira_Parcela as campoUsarFreteParcela,
                                                 T005_Flag_ST_Primeira_Parcela as campoUsarSTParcela,
                                                 T005_Flag_IPI_Primeira_Parcela as campoUsarIPIParcela,
                                                 T005_Pgto_Parcelas as Total_Parcelas,
                                                 T005_Valor_Total_IPI as valorTotalIPI,
                                                 T005_Valor_Total as valorTotal,
                                                 T005_Valor_Frete as valorTotalFrete,
                                                 T005_Valor_ICMS_Substituicao_Retencao as valorTotalST',
                                             'where' => "T005_Id='{$T005_Id}'",
                                             'tabelaPgto' => 'T090',
                                             'camposPgto' => 'T090_Prazos_Dias as prazoDias,T090_D027_Id as D027Pagamento,T090_Numero_Parcela as numeroParcela',
                                             'wherePgto' => "T090_T005_Id='{$T005_Id}'");
            // Gerar Parcelas
            $retorno         = $this->atualizarParcelas($dadosTabela);
            //Atualiza aliquotas
            $retorno         = $VEN002->atualizarAliquotasPedidos($T005_Id);  
            //Verifica liberação de crédito          
            $creditoLiberado = $VEN002->verificaCreditoLiberado($T005_Id);

            $T005 = mysql_query("SELECT T005_Valor_Total,
                                        T005_Motivo_Pedido_Liberacao,
                                        T005_C004_Id,
                                        T005_D024_Id,
                                        D024_Flag_Pessoa_Fisica_Juridica,
                                        D024_Nome_Empresa,
                                        D024_Cpf,
                                        D024_Cnpj
                                   FROM T005
                              LEFT JOIN D024 ON D024_Id = T005_D024_Id
                                  WHERE T005_Id = '{$T005_Id}'");                       
            $mT005 = mysql_fetch_array($T005);
                       
            //ALTERAR TAMBEM OS DADOS DO PAGAMENTO QUANDO GERA O PEDIDO, CASO A PESSOA FAÇA ALGUMA ALTERAÇÃO NO PORTADOR OU NO VALOR DA PARCELA ---LUCAS 31/07/2015
            $sql = mysql_query("SELECT T089_Valor_Parcela, 
                                       D027_Portador, 
                                       T089_D027_Id,
                                       T089_Numero_Parcela,
                                       T089_Data_Vencimento,
                                       T089_Prazos_Dias 
                                  FROM T089 
                             LEFT JOIN D027 ON D027_Id=T089_D027_Id
                                 WHERE T089_T003_Id = '{$T003_Id}'");

            while ($row = mysql_fetch_array($sql)){

                // Pega a parcela do pedido igual ao numero da parcela do orçamento
                $T090 = mysql_query("SELECT T090_D027_Id,
                                            T090_Valor_Parcela,
                                            T090_Data_Vencimento,
                                            T090_Prazos_Dias 
                                       FROM T090 
                                      WHERE T090_T005_Id = '{$T005_Id}' 
                                        AND T090_Numero_Parcela='{$row['T089_Numero_Parcela']}'");

                $mT090 = mysql_fetch_array($T090);

                if($orcamento['T003_Valor_Total'] == $mT005['T005_Valor_Total']){

                    //  E feito o mesmo UPDATE separado abaixo
                    /*if($mT090['T090_D027_Id'] != $row['T089_D027_Id'] || $row['T089_Valor_Parcela'] != $mT090['T090_Valor_Parcela']){
                        mysql_query("UPDATE T090 SET T090_D027_Id = '{$row['T089_D027_Id']}', T090_Valor_Parcela = '{$row['T089_Valor_Parcela']}' WHERE T090_T005_Id = '{$T005_Id}' and T090_Numero_Parcela = '{$row['T089_Numero_Parcela']}'");   
                    } */
                    
                    if($mT090['T090_D027_Id'] != $row['T089_D027_Id']){
                        mysql_query("UPDATE T090 SET T090_D027_Id = '{$row['T089_D027_Id']}' WHERE T090_T005_Id = '{$T005_Id}' and T090_Numero_Parcela = '{$row['T089_Numero_Parcela']}'");
                    }   

                    if(($row['T089_Data_Vencimento'] != $mT090['T090_Data_Vencimento']) && ($g['C031']['dataVencimentoParcelasAoGerarPedido'] == 1)){
                        // dataVencimentoParcelasAoGerarPedido = 1 mantem o vencimento igual do orçamento
                        mysql_query("UPDATE T090 SET T090_Data_Vencimento = '{$row['T089_Data_Vencimento']}' WHERE T090_T005_Id = '{$T005_Id}' and T090_Numero_Parcela = '{$row['T089_Numero_Parcela']}'");
                    }                     
                    
                    if($row['T089_Valor_Parcela'] != $mT090['T090_Valor_Parcela']){
                        mysql_query("UPDATE T090 SET T090_Valor_Parcela = '{$row['T089_Valor_Parcela']}' WHERE T090_T005_Id = '{$T005_Id}' and T090_Numero_Parcela = '{$row['T089_Numero_Parcela']}'");
                    }
                       
                    if($row['T089_Prazos_Dias'] != $mT090['T090_Prazos_Dias']){
                        mysql_query("UPDATE T090 SET T090_Prazos_Dias = '{$row['T089_Prazos_Dias']}' WHERE T090_T005_Id = '{$T005_Id}' and T090_Numero_Parcela = '{$row['T089_Numero_Parcela']}'");
                    }                    
                }
            }
            $error = mysql_error();
            if (!empty($error)) {
                mysql_query("ROLLBACK");
                return 'Erro: ' . $error;
            }
            //FIM

            if ($orcamento['T003_Flag_Acp'] != 4 && $creditoLiberado == false && $g['C031']['verificaCreditoLiberadoGerarPedido'] == 'S') {
                
                mysql_query("UPDATE T005 SET T005_Flag_Status=6, T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1), T005_Flag_Separacao_Liberar='S', T005_Flag_Separacao='S',T005_Flag_Primeira_Liberacao_Credito = '0'  WHERE T005_Id='{$T005_Id}'");
                
                //Insere registros no histórico do pedido (T178)
                $mT005['T005_Motivo_Pedido_Liberacao'] = str_replace("Enviou para análise de crédito", "Enviou para análise de crédito (automaticamente ao gerar pedido)", $mT005['T005_Motivo_Pedido_Liberacao']);
                
                $mT005['T005_Motivo_Pedido_Liberacao'] = mysql_real_escape_string($mT005['T005_Motivo_Pedido_Liberacao']);
                mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'{$mT005['T005_Motivo_Pedido_Liberacao']}','')");

                try {
                    require_once('bibliotecas/classes/Webhook.php');
                    Webhook::publishForCompany((int) $mT005['T005_C004_Id'], 'financial_analysis.requested', (int) $T005_Id);
                } catch (\Throwable $exception) {
                    // Webhook is auxiliary and must not cancel order generation.
                }

            } else {
                mysql_query("UPDATE T005 SET T005_Flag_Primeira_Liberacao_Credito = '1'  WHERE T005_Id='{$T005_Id}'");
            }

            // Retorna true, essa função é personalizada de acordo com a aplicação
            // 1º utilizacação APP resposta rapida cotação compra - Felipe 21/10/2022
            $this->acoesFinaisGerarPedido($T003_Id);

            // VERIFICANDO SE GEROU CORRETAMENTE O T005 E OS T006
            $T005_T006=mysql_query("SELECT T005_Id,T006_Id from T005 LEFT JOIN T006 ON T006_T005_Id=T005_Id WHERE T005_Id='{$T005_Id}'");
            $mT005_T006=mysql_fetch_array($T005_T006);
            if($mT005_T006['T005_Id'] <=0 OR $mT005_T006['T006_Id'] <=0 ){
                mysql_query("ROLLBACK");
                return "Houve um problema na gravação dos dados. Por favor, refaça a operação";
            }

            mysql_query("COMMIT");


            //Busca D049_Id para executar function copiarEngenhariaProduto
            // Vai copiar a engenharia no fim porque start uma transaction na funcion copiarEngenhariaProduto
            // Para não dar conflito com a transação que já está ativa no gerarPedido, não vai ser mais feito no while quando o item é inserido no T006
            $D049 = mysql_query("SELECT T006_Id
                                   FROM T006 
                              LEFT JOIN D009 ON T006_D009_Id=D009_Id 
                              LEFT JOIN D049 ON D009_D049_Id=D049_Id 
                                  WHERE T006_T005_Id = '{$T005_Id}'
                                    AND D049_Flag_Tipo IN ('PD', 'IN')");
            if(mysql_num_rows($D049) > 0){
                require_once('bibliotecas/classes/VEN002.php');
                $VEN002 = new VEN002();
                while($mD049 = mysql_fetch_array($D049)){
                    $retorno = $VEN002->copiarEngenhariaProduto($mD049['T006_Id']);
                    if($retorno !== true){
                        return 'O pedido foi gerado com sucesso, mas houve um problema em copiar a engenharia dos produtos, favor entre no pedido e atualize a engenharia pelo botão Atualizar->Atualizar Engenharia';
                    }
                }
            }
            
            return true;
        } else {
            return "ERRO: T003_Id não existe";
        }
    }
    
    /**
     * aplicarIpvMinimo
     *
     * @param String $T003_Id
     * @param String $IPV_Minimo
     * @return String/bool
     */    
    public function aplicarIpvMinimo($T003_Id, $IPV_Minimo)
    {
        global $g;
        
        if ($T003_Id == 0) {
            return "Nao encontrado o id da entidade principal";
        }
        
        if ($IPV_Minimo == 0) {
            return "Digite o IPV Mínimo";
        }
        
        $cmysql_T003 = mysql_query("SELECT * FROM T003 WHERE T003_Id='$T003_Id'");
        $mT003       = mysql_fetch_array($cmysql_T003);
        
        $cmysql_T004 = mysql_query("SELECT T004_Id,
                                     T004_Valor_Custo_Unitario
                                FROM T004
                           LEFT JOIN T006 ON T006_T004_Id=T004_Id 
                               WHERE T004_T003_Id='$T003_Id' 
                                 AND (T004_Flag_Preco_Especial is Null OR T004_Flag_Preco_Especial='')
                                 AND T006_Id is Null");
        if (mysql_num_rows($cmysql_T004) == 0) {
            
            return "Não existem itens para aplicar o IPV";
        }
        while ($mT004 = mysql_fetch_array($cmysql_T004)) {
            
            $Valor_Preco_Unitario = $mT004['T004_Valor_Custo_Unitario'] * $IPV_Minimo;
            
            mysql_query("UPDATE T004
                            SET T004_Valor_Preco_Unitario='$Valor_Preco_Unitario',
                                T004_Percentual_Desconto=if(T004_Valor_Preco_Original>0,(1-(T004_Valor_Preco_Unitario/T004_Valor_Preco_Original))*100,0),
                                T004_Valor_Desconto_Unitario=if(T004_Valor_Preco_Original>0,(T004_Valor_Preco_Original*(T004_Valor_Preco_Unitario/100)),0)
                          WHERE T004_Id='$mT004[T004_Id]'");
            
            mysql_query("call T004_Gravar_Totalizacao_4({$mT004['T004_Id']});");
        }
        return true;
    }
    
    /**
     * importarOrcamento
     *
     * @param String $T003_Id
     * @param String $T003_Id_Origem
     * @return String/bool
     */    
    public function importarOrcamento($T003_Id, $T003_Id_Origem)
    {
        global $g;
        
        if (empty($T003_Id)) {
            return "Faltou o parametro T003_Id";
        }
        if (empty($T003_Id_Origem)) {
            return "Digite o numero do orçamento";
        }
        
        $cmysql_T003 = mysql_query("SELECT * FROM T003 WHERE T003_Id='$T003_Id'");
        $mT003       = mysql_fetch_array($cmysql_T003);
        
        /////////////////////////////////////////////////////////////////////////////////
        //-- VERIFICA SE O ITEM EXISTEM NA EMPRESA DE DESTINO
        /////////////////////////////////////////////////////////////////////////////////
        $cmysql_T004 = mysql_query("SELECT t2.D009_Id,
                                 D001_Codigo_Produto,
                                 D002_Descricao_Produto
                            FROM T004
                       LEFT JOIN D009 as t1 ON T004_D009_Id=t1.D009_Id
                       LEFT JOIN D049 ON D049_Id=t1.D009_D049_Id
                       LEFT JOIN D001 ON D001_Id=D049_D001_Id
                       LEFT JOIN D002 ON D002_Id=D001_D002_Id
                       LEFT JOIN D009 as t2 ON D049_Id=t2.D009_D049_Id AND t2.D009_C004_Id='{$g['empresaAtual']}'
                           WHERE T004_T003_Id='$T003_Id'
                             AND t2.D009_Id=0");
        while ($mT004 = mysql_fetch_array($cmysql_T004)) {
            $mSemCadastro[] = "{$mT004['D001_Codigo_Produto']} - {$mT004['D002_Descricao_Produto']}";
        }
        if (mysql_num_rows($cmysql_T004) > 0) {
            $SemCadastro = implode("/p", $mSemCadastro);
            return "Existem itens sem cadastro na empresa {$g['empresaAtual']}/pque estao sendo importados do orï¿½amento $T003_Id_Origem<br />$SemCadastro";
        }
        
        $cmysql_T004 = mysql_query("SELECT  T004_D005_Id,
                                  T004_D037_Id,
                                  T004_D006_Id,
                                  T004_D024_Id,
                                  T004_D082_Id,
                                  t2.D009_Id T004_D009_Id,
                                  t2.D009_D004_Id,
                                  T004_Quantidade,
                                  T004_Descricao_Produto,
                                  D009_Preco_Unitario(T004_D009_Id,'{$mT003['T003_D024_Id']}',D009_IPV_ICMS('{$mT003['T003_C004_Id']}','{$mT003['T003_D024_Id']}',T004_D009_Id,'{$mT003['T003_D006_Id']}',
                                                                   '{$mT003['T003_D018_Id']}','{$mT003['T003_Flag_Revenda_Consumidor']}','{$mT003['T003_Percentual_Comissao']}','{$mT003['T003_Flag_Acp']}',1),if(
                                                                   '{$mT003['T003_Flag_Revenda_Consumidor']}'='C',1,0),'{$mT003['T003_D006_Id']}') as T004_Valor_Preco_Original_Unitario,
                                  T004_Valor_Custo_Unitario,
                                  T004_Quantidade_Confirmada,
                                  T004_Numero_Orcamento_Venda,
                                  T004_Codigo_Produto,
                                  T004_Valor_Preco_Sem_Desconto_Unitario,
                                  T004_Valor_Preco_Unitario,
                                  T004_Percentual_Desconto,
                                  T004_Valor_Preco_Original,
                                  T004_Codigo_Substituto,
                                  T004_Marca,
                                  T004_Peso_Unitario,
                                  D009_Origem_Preco(T004_D009_Id,'{$mT003['T003_D024_Id']}',if('{$mT003['T003_Flag_Revenda_Consumidor']}'='C',1,0)) as T004_Flag_Preco_Especial,
                                  T004_Classificacao_Fiscal 
                             FROM T004 
                        LEFT JOIN D009 as t1 ON T004_D009_Id=t1.D009_Id
                        LEFT JOIN D049 ON D049_Id=t1.D009_D049_Id
                        LEFT JOIN D009 as t2 ON D049_Id=t2.D009_D049_Id AND t2.D009_C004_Id='{$g['empresaAtual']}'
                            WHERE T004_T003_Id='{$T003_Id_Origem}'");
        
        while ($mT004 = mysql_fetch_array($cmysql_T004)) {
            
            if ($mT004['T004_Flag_Preco_Especial'] == 'O') {
                $mT004['T004_Valor_Preco_Sem_Desconto_Unitario'] = $mT004['T004_Valor_Preco_Original_Unitario'];
            }
            
            unset($mGravar);
            $mGravar['T004_D005_Id']                           = $mT004['T004_D005_Id'];
            $mGravar['T004_D037_Id']                           = $mT004['T004_D037_Id'];
            $mGravar['T004_D006_Id']                           = $mT004['T004_D006_Id'];
            $mGravar['T004_T003_Id']                           = $T003_Id;
            $mGravar['T004_D024_Id']                           = $mT004['T004_D024_Id'];
            $mGravar['T004_D009_Id']                           = $mT004['T004_D009_Id'];
            $mGravar['T004_D004_Id']                           = $mT004['D009_D004_Id'];
            $mGravar['T004_D082_Id']                           = $mT004['T004_D082_Id'];
            $mGravar['T004_C004_Id']                           = $g['empresaAtual'];
            $mGravar['T004_Item']                              = 0;
            $mGravar['T004_Quantidade']                        = $mT004['T004_Quantidade'];
            $mGravar['T004_Descricao_Produto']                 = $mT004['T004_Descricao_Produto'];
            $mGravar['T004_Observacao_Entrega']                = '';
            $mGravar['T004_Valor_Preco_Sem_Desconto_Unitario'] = $mT004['T004_Valor_Preco_Sem_Desconto_Unitario'];
            $mGravar['T004_Valor_Preco_Unitario']              = $mT004['T004_Valor_Preco_Unitario'];
            $mGravar['T004_Valor_Preco_Original']              = $mT004['T004_Valor_Preco_Original'];
            $mGravar['T004_Percentual_Desconto']               = $mT004['T004_Percentual_Desconto'];
            $mGravar['T004_Valor_Custo_Unitario']              = $mT004['T004_Valor_Custo_Unitario'];
            $mGravar['T004_Quantidade_Confirmada']             = $mT004['T004_Quantidade_Confirmada'];
            $mGravar['T004_Numero_Orcamento_Venda']            = $mT004['T004_Numero_Orcamento_Venda'];
            $mGravar['T004_Codigo_Produto']                    = $mT004['T004_Codigo_Produto'];
            $mGravar['T004_Codigo_Substituto']                 = $mT004['T004_Codigo_Substituto'];
            $mGravar['T004_Marca']                             = $mT004['T004_Marca'];
            $mGravar['T004_Peso_Unitario']                     = $mT004['T004_Peso_Unitario'];
            $mGravar['T004_Flag_Preco_Especial']               = $mT004['T004_Flag_Preco_Especial'];
            $mGravar['T004_Classificacao_Fiscal']              = $mT004['T004_Classificacao_Fiscal'];
            
            gravar_entidade($mGravar, 'T004', 0, '', 'T003', $T003_Id, 'MANUAL', '001');
            
            
        }
        
        mysql_query("call T003_Gravar_Totalizacao_4($T003_Id)");
        return true;
    } 
    
    /**
     * atualizarAliquotas
     *
     * @param String $T003_Id
     * @param String $C004_Id
     * @return String/bool
     */
    public function atualizarAliquotas($T003_Id= null, $C004_Id = false, $T004_Id = null, $arrayParametros = null)
    {
        if(!isset($arrayParametros)){
            //Verifica se foi passado um T004 específico
            if($T004_Id){
                $extra = "AND T004_Id = '{$T004_Id}'";
            }

            $cClausula = "SELECT
            T004_Id,
            D009_Id,
            D001_Codigo_Produto,
            D001_Peso_Unitario_Kg,
            T003_Flag_Lista_Preco,
            T003_D024_Id,
            T003_Flag_Acp,
            T004_D006_Id,
            D001_Cubagem_Unitaria,
            D009_Preco_Pauta_ST,
            ifnull(T004_T219_Id,0) as T004_T219_Id,
            T004_Flag_Origem_Produto
            FROM T003
            LEFT JOIN T004 ON T003_Id=T004_T003_Id
            LEFT JOIN T006 ON T004_Id=T006_T004_Id
            LEFT JOIN D009 ON D009_Id=T004_D009_Id
            LEFT JOIN D049 ON D049_Id=D009_D049_Id
            LEFT JOIN D001 ON D001_Id=D049_D001_Id
                WHERE T003_Id='{$T003_Id}'
                AND T006_Id IS NULL {$extra}";

            $cmysql_T004 = mysql_query("$cClausula");
            $erro = mysql_error();
            if (!empty($erro)) {
                return "Erro consultando itens do orcamento: $erro $cClausula";
            }
            while($mT004 = mysql_fetch_array($cmysql_T004)){
                $D009_Id = $mT004['D009_Id'];
                $T003_Flag_Lista_Preco = $mT004['T003_Flag_Lista_Preco'];
                $T004_D006_Id = $mT004['T004_D006_Id'];
                $T004_T219_Id = $mT004['T004_T219_Id']; 
                $D001_Peso_Unitario_Kg = $mT004['D001_Peso_Unitario_Kg'];
                $D001_Cubagem_Unitaria = $mT004['D001_Cubagem_Unitaria'];
                $T004_Preco_Pauta_ST   = $mT004['D009_Preco_Pauta_ST'];
                $T004_Flag_Origem_Produto = $mT004['T004_Flag_Origem_Produto'];
            }
        }else{
            $D009_Id = $arrayParametros['D009_Id'];
            $T003_Flag_Lista_Preco = $arrayParametros['T003_Flag_Lista_Preco'];
            $T004_D006_Id          = $arrayParametros['T004_D006_Id'];
            $T004_T219_Id          = $arrayParametros['T004_T219_Id'];
            $D001_Peso_Unitario_Kg = $arrayParametros['D001_Peso_Unitario_Kg'];
            $D001_Cubagem_Unitaria = $arrayParametros['D001_Cubagem_Unitaria'];
            $T004_Preco_Pauta_ST   = $arrayParametros['D009_Preco_Pauta_ST'];
            $T004_Flag_Origem_Produto = $arrayParametros['T004_Flag_Origem_Produto'];
        }

        global $g;
        require_once('bibliotecas/classes/FIS003.php');
        $FIS003 = new FIS003();
        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();
        
        // Felipe Carrano 19/10/2023
        //Método retornaPrecoVenda utilizado apenas para pegar a origem do produto, não precisa das aliquotas
        //Chamando o aliquotas depois porque pode ter mudado a origem do produto e com isso pode mudar a aliquota do ICMS e IPI
      /*$Aliquotas = $this->retornaAliquotas($D009_Id, $T003_Id, false, false, $T004_Id);
        $demonstrativoNCM = $FIS003->demonstrativoNCM($Aliquotas['D005_Id']);
        $demonstrativoNCM = mysql_real_escape_string($demonstrativoNCM); */
        //$dadosPrecoVenda = $CAD002->retornaPrecoVenda($D009_Id, $T003_Flag_Lista_Preco, $T003_D024_Id, false, $T004_D006_Id, $Aliquotas['Aliquota_ICMS'], $T004_T219_Id, false, 4, $Aliquotas['Aliquota_PIS'], $Aliquotas['Aliquota_COFINS'], $Aliquotas['Aliquota_IPI'], $Aliquotas['ST_VA'], $Aliquotas['Percentual_Reducao_ICMS_ST'], $Aliquotas['ST_ICMS_Interno'], $Aliquotas['Percentual_Reducao_ICMS'], $Aliquotas['ICMS_Inter_Estadual'], $Aliquotas['Aliquota_IRPJ'], $Aliquotas['Aliquota_CSLL']);
        $dadosPrecoVenda = $CAD002->retornaPrecoVenda($D009_Id, $T003_Flag_Lista_Preco, $T003_D024_Id, false, $T004_D006_Id, 0, $T004_T219_Id, false, 4, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        $origemProduto   = $dadosPrecoVenda[5];

        if($T004_Flag_Origem_Produto != $origemProduto){
            // se alterou a origem pode mudar a aliquota do ICMS, atualiza aliquotas novamente
            mysqli_query("UPDATE T004 SET T004_Flag_Origem_Produto = '{$origemProduto}' WHERE T004_Id = '{$T004_Id}'");
        }

        $Aliquotas        = $this->retornaAliquotas($D009_Id, $T003_Id, false, false, $T004_Id);
        $demonstrativoNCM = $FIS003->demonstrativoNCM($Aliquotas['D005_Id']);
        $demonstrativoNCM = mysql_real_escape_string($demonstrativoNCM);

        if ($g['C031']['atualizarAliquotaUnidadeProduto']=='S'){
            $updateD037 =  "T004_D037_Id = D001_D037_Id, ";
        }

        mysql_query("UPDATE T004 
                  LEFT JOIN D009 ON D009_ID = T004_D009_Id 
                  LEFT JOIN D049 ON D049_Id = D009_D049_Id 
                  LEFT JOIN D001 on D001_Id = D049_D001_Id 
                        SET T004_D005_Id                            = '{$Aliquotas['D005_Id']}',
                            T004_Classificacao_Fiscal               = '{$Aliquotas['D005_Classificacao_Fiscal']}',
                            T004_Aliquota_IPI                       = '{$Aliquotas['Aliquota_IPI']}',
                            T004_Percentual_Reducao_IPI             = '{$Aliquotas['Percentual_Reducao_IPI']}',
                            T004_Aliquota_ICMS                      = '{$Aliquotas['Aliquota_ICMS']}',
                            T004_Aliquota_ICMS_Oculto               = '{$Aliquotas['Aliquota_ICMS_Oculto']}',
                            T004_Percentual_Reducao_ICMS            = '{$Aliquotas['Percentual_Reducao_ICMS']}',
                            T004_Percentual_Reducao_ICMS_ST         = '{$Aliquotas['Percentual_Reducao_ICMS_ST']}',
                            T004_Flag_ST                            = '{$Aliquotas['Flag_ST']}',
                            T004_ST_VA                              = '{$Aliquotas['ST_VA']}',
                            T004_ICMS_Inter_Estadual                = '{$Aliquotas['ICMS_Inter_Estadual']}',
                            T004_ST_ICMS_Interno                    = '{$Aliquotas['ST_ICMS_Interno']}',
                            T004_D059_Id                            = '{$Aliquotas['D059_Id']}',
                            T004_Flag_Situacao_Tributaria           = '{$Aliquotas['Flag_Situacao_Tributaria']}',
                            T004_ICMS_Inter_Estadual_MVA            = '{$Aliquotas['ST_ICMS_Inter_Estadual_MVA']}',
                            T004_ST_ICMS_Interno_MVA                = '{$Aliquotas['ST_ICMS_Interno_MVA']}',
                            T004_Flag_Isento_PIS                    = '{$Aliquotas['Flag_Isento_PIS']}',
                            T004_Aliquota_PIS                       = '{$Aliquotas['Aliquota_PIS']}',
                            T004_Situacao_Tributaria_PIS            = '{$Aliquotas['Situacao_Tributaria_PIS']}',
                            T004_Aliquota_COFINS                    = '{$Aliquotas['Aliquota_COFINS']}',
                            T004_Situacao_Tributaria_COFINS         = '{$Aliquotas['Situacao_Tributaria_COFINS']}',
                            T004_Flag_Isento_COFINS                 = '{$Aliquotas['Flag_Isento_COFINS']}',
                            T004_Situacao_Tributaria_IPI            = '{$Aliquotas['Situacao_Tributaria_IPI']}',
                            T004_Peso_Unitario                      = '{$D001_Peso_Unitario_Kg}',
                            T004_Cubagem_Unitaria                   = '{$D001_Cubagem_Unitaria}',
                            T004_Peso_Bruto                         = '{$Aliquotas['D001_Peso_Unitario_Bruto']}',
                            T004_Flag_Origem_Produto                = '{$origemProduto}',
                            T004_Percentual_Desconto                = ((T004_Valor_Preco_Original - T004_Valor_Preco_Unitario) / T004_Valor_Preco_Original) * 100,
                            T004_D005_Id_Info                       = '{$demonstrativoNCM}',
                            $updateD037
                            T004_Motivo_Desoneracao_ICMS            = '{$Aliquotas['Motivo_Desoneracao_ICMS']}',
                            T004_IPV_Sem_Lucro                      = '{$Aliquotas['IPV_Sem_Lucro']}',
                            T004_Aliquota_IRPJ                      = '{$Aliquotas['Aliquota_IRPJ']}',
                            T004_Flag_Isento_IRPJ                   = '{$Aliquotas['Flag_Isento_IRPJ']}',
                            T004_Aliquota_CSLL                      = '{$Aliquotas['Aliquota_CSLL']}',
                            T004_Flag_Isento_CSLL                   = '{$Aliquotas['Flag_Isento_CSLL']}',
                            T004_UF_ICMS_Substituicao_Devido        = '{$Aliquotas['UF_ICMS_Substituicao_Devido']}',
                            T004_Percentual_Base_Operacao_Propria   = '{$Aliquotas['Percentual_Base_Operacao_Propria']}',
                            T004_Percentual_Trib_Federal            = '{$Aliquotas['Percentual_Trib_Federal']}',
                            T004_Percentual_Trib_Estadual           = '{$Aliquotas['Percentual_Trib_Estadual']}',
                            T004_Percentual_Trib_Municipal          = '{$Aliquotas['Percentual_Trib_Municipal']}',
                            T004_Aliquota_ICMS_Credito              = '{$Aliquotas['Aliquota_ICMS_Credito']}',
                            T004_Modalidade_ICMS_Substituicao       = '{$Aliquotas['Modalidade_ICMS_Substituicao']}',
                            T004_Modalidade_ICMS                    = '{$Aliquotas['Modalidade_ICMS']}',
                            T004_Preco_Pauta_ST                     = '{$T004_Preco_Pauta_ST}',
                            T004_Percentual_Partilha_ICMS_Destino   = '{$Aliquotas['Percentual_Partilha_ICMS_Destino']}',
                            T004_Percentual_Partilha_ICMS_Origem    = '{$Aliquotas['Percentual_Partilha_ICMS_Origem']}'
                      WHERE T004_Id='{$T004_Id}'");
        $erro = mysql_error();
        
        if (!empty($erro)) {
            return "Erro atualizando item do orcamento ID {$T004_Id}: $erro";
        }

        mysql_query("UPDATE T004A SET T004A_Percentual_Reducao_PIS            = '{$Aliquotas['Percentual_Reducao_PIS']}',
                                      T004A_Percentual_Reducao_COFINS         = '{$Aliquotas['Percentual_Reducao_COFINS']}',
                                      T004A_Percentual_Reducao_Base_DIFAL     = '{$Aliquotas['Percentual_Reducao_Base_DIFAL']}',
                                      T004A_Trib_Interna_UF_Dest_Reducao_Base = '{$Aliquotas['EIP001_Trib_Interna_UF_Dest_Reducao_Base']}',
                                      T004A_Percentual_FCP                    = '{$Aliquotas['Percentual_FCP']}',
                                      T004A_Situacao_Tributaria_CBS_IBS              = '{$Aliquotas['Situacao_Tributaria_CBS_IBS']}',
                                      T004A_Classificacao_Tributaria_CBS_IBS         = '{$Aliquotas['Classificacao_Tributaria_CBS_IBS']}',
                                      T004A_Aliquota_CBS                             = '{$Aliquotas['Aliquota_CBS']}',
                                      T004A_Aliquota_IBS_UF                          = '{$Aliquotas['Aliquota_IBS_UF']}',
                                      T004A_Aliquota_IBS_Municipal                   = '{$Aliquotas['Aliquota_IBS_Municipal']}',
                                      T004A_Percentual_Reducao_CBS                   = '{$Aliquotas['Percentual_Reducao_CBS']}',
                                      T004A_Percentual_Reducao_IBS_UF                = '{$Aliquotas['Percentual_Reducao_IBS_UF']}',
                                      T004A_Percentual_Reducao_IBS_Municipal         = '{$Aliquotas['Percentual_Reducao_IBS_Municipal']}',
                                      T004A_Percentual_Diferimento_CBS               = '{$Aliquotas['Percentual_Diferimento_CBS']}',
                                      T004A_Percentual_Diferimento_IBS_UF            = '{$Aliquotas['Percentual_Diferimento_IBS_UF']}',
                                      T004A_Percentual_Diferimento_IBS_Municipal     = '{$Aliquotas['Percentual_Diferimento_IBS_Municipal']}',
                                      T004A_Situacao_Tributaria_CBS_IBS_Regular      = '{$Aliquotas['Situacao_Tributaria_CBS_IBS_Regular']}',
                                      T004A_Classificacao_Tributaria_CBS_IBS_Regular = '{$Aliquotas['Classificacao_Tributaria_CBS_IBS_Regular']}',
                                      T004A_Aliquota_CBS_Regular                     = '{$Aliquotas['Aliquota_CBS_Regular']}',
                                      T004A_Aliquota_IBS_UF_Regular                  = '{$Aliquotas['Aliquota_IBS_UF_Regular']}',
                                      T004A_Aliquota_IBS_Municipal_Regular           = '{$Aliquotas['Aliquota_IBS_Municipal_Regular']}',
                                      T004A_Percentual_Reducao_CBS_Regular           = '{$Aliquotas['Percentual_Reducao_CBS_Regular']}',
                                      T004A_Percentual_Reducao_IBS_UF_Regular        = '{$Aliquotas['Percentual_Reducao_IBS_UF_Regular']}',
                                      T004A_Percentual_Reducao_IBS_Municipal_Regular = '{$Aliquotas['Percentual_Reducao_IBS_Municipal_Regular']}'
                                WHERE T004A_T004_Id = '{$T004_Id}'");

        
        mysql_query("call T004_Gravar_Totalizacao_4('{$T004_Id}');");

        mysql_free_result($cmysql_T003);

        return true;
    }
    
    /**
     * incluirProdutoTroca
     *
     * @param Form $form
     * @return String/bool
     */     
    public function incluirProdutoTroca($form)
    {
        $T138_Quantidade           = $form->campoValorEnviado('T138_Quantidade');
        $T138_Observacao           = $form->campoValorEnviado('T138_Observacao');
        $T138_Valor_Preco_Unitario = $form->campoValorEnviado('T138_Valor_Preco_Unitario');
        $T138_Valor_Preco_Unitario = gCorrigeNumeroInverte($T138_Valor_Preco_Unitario);
        $T138_Operacao             = $form->campoValorEnviado('T138_Operacao');
        $T138_T003_Id              = $form->campoValorEnviado('T138_T003_Id');
        $T138_C004_Id              = $form->campoValorEnviado('T138_C004_Id');
        $T138_D009_Id              = $form->campoValorEnviado('T138_D009_Id');
        
        if ($T138_D009_Id <= 0) {
            return "nenhum produto foi selecionado";
        }
        
        $cClausula = " insert into T138 (";
        $cClausula .= " T138_T003_Id, ";
        $cClausula .= " T138_C004_Id, ";
        $cClausula .= " T138_D009_Id, ";
        $cClausula .= " T138_Operacao, ";
        $cClausula .= " T138_Valor_Preco_Unitario, ";
        $cClausula .= " T138_Quantidade, ";
        $cClausula .= " T138_Observacao ";
        $cClausula .= " ) VALUES ( ";
        $cClausula .= " '$T138_T003_Id', ";
        $cClausula .= " '$T138_C004_Id', ";
        $cClausula .= " '$T138_D009_Id', ";
        $cClausula .= " '$T138_Operacao', ";
        $cClausula .= " '$T138_Valor_Preco_Unitario', ";
        $cClausula .= " '$T138_Quantidade', ";
        $cClausula .= " '$T138_Observacao' ";
        $cClausula .= " ) ";
        
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            return "Erro: $cErro<br />$cClausula";
        } else {
            return true;
        } 
    }
    
    /**
     * Normaliza uma medida informada em formato brasileiro ou decimal.
     */
    private function normalizarMedidaQuantidade($valor)
    {
        $valor = trim((string) $valor);
        if ($valor === '') {
            return 0;
        }

        if (strpos($valor, ',') !== false) {
            $valor = str_replace('.', '', $valor);
            $valor = str_replace(',', '.', $valor);
        }

        if (!is_numeric($valor)) {
            return false;
        }

        return (float) $valor;
    }

    /**
     * Valida e calcula as medidas utilizadas pela calculadora de quantidade.
     * Uma combinação inteiramente vazia/zero representa a ausência de cálculo;
     * para calcular, são necessárias ao menos duas medidas positivas.
     */
    public function prepararMedidasQuantidade($altura, $largura, $comprimento)
    {
        $altura = $this->normalizarMedidaQuantidade($altura);
        $largura = $this->normalizarMedidaQuantidade($largura);
        $comprimento = $this->normalizarMedidaQuantidade($comprimento);

        if ($altura === false || $largura === false || $comprimento === false) {
            return false;
        }

        if ($altura == 0 && $largura == 0 && $comprimento == 0) {
            return array('altura' => '0.00000', 'largura' => '0.00000', 'comprimento' => '0.00000', 'resultado' => '0.00000');
        }

        $medidas = array($altura, $largura, $comprimento);
        $quantidadeMedidas = 0;
        $resultado = 1;
        foreach ($medidas as $medida) {
            if ($medida < 0) {
                return false;
            }
            if ($medida > 0) {
                $quantidadeMedidas++;
                $resultado *= $medida;
            }
        }

        if ($quantidadeMedidas < 2) {
            return false;
        }

        return array(
            'altura' => number_format($altura, 5, '.', ''),
            'largura' => number_format($largura, 5, '.', ''),
            'comprimento' => number_format($comprimento, 5, '.', ''),
            'resultado' => number_format($resultado, 5, '.', ''),
        );
    }

    /**
     * Persiste a calculadora para um item existente e atualiza suas totalizações.
     */
    public function salvarMedidasQuantidade($T004_Id, $T003_Id, $altura, $largura, $comprimento)
    {
        $T004_Id = (int) $T004_Id;
        $T003_Id = (int) $T003_Id;
        $medidas = $this->prepararMedidasQuantidade($altura, $largura, $comprimento);

        if ($T004_Id <= 0 || $T003_Id <= 0 || $medidas === false || $medidas['resultado'] <= 0) {
            return 'Informe pelo menos duas medidas numéricas maiores que zero.';
        }

        $item = mysql_query("SELECT T004_Id, T004_T003_Id FROM T004 WHERE T004_Id = '{$T004_Id}' AND T004_T003_Id = '{$T003_Id}'");
        if (mysql_num_rows($item) !== 1) {
            return 'Item do orçamento não encontrado.';
        }

        $sqlT004 = "UPDATE T004 SET T004_Quantidade = '{$medidas['resultado']}' WHERE T004_Id = '{$T004_Id}'";
        if (!mysql_query($sqlT004)) {
            return 'Erro ao atualizar a quantidade: ' . mysql_error();
        }

        $sqlT004A = "INSERT INTO T004A (T004A_T004_Id, T004A_Altura, T004A_Largura, T004A_Comprimento, T004A_Resultado)
                      VALUES ('{$T004_Id}', '{$medidas['altura']}', '{$medidas['largura']}', '{$medidas['comprimento']}', '{$medidas['resultado']}')
                      ON DUPLICATE KEY UPDATE
                        T004A_Altura = VALUES(T004A_Altura),
                        T004A_Largura = VALUES(T004A_Largura),
                        T004A_Comprimento = VALUES(T004A_Comprimento),
                        T004A_Resultado = VALUES(T004A_Resultado)";
        if (!mysql_query($sqlT004A)) {
            return 'Erro ao gravar as medidas: ' . mysql_error();
        }

        if (!mysqli_query("CALL T004_Gravar_Totalizacao_4('{$T004_Id}')")) {
            return 'Erro ao totalizar o item: ' . mysql_error();
        }
        if (!mysqli_query("CALL T003_Gravar_Totalizacao_4('{$T003_Id}')")) {
            return 'Erro ao totalizar o orçamento: ' . mysql_error();
        }

        return $medidas;
    }

    /**
     * incluirProduto
     *
     * @param Form $form
     * @param String $D009_Id
     * @param String $T003_Id
     * @return String/bool
     */
    public function incluirProduto($form, $D009_Id = false, $T003_Id = false)
    {
        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();
        require_once('bibliotecas/classes/FIS003.php');
        $FIS003 = new FIS003();
        global $g, $T004_Id;

        $T004A_Altura = 0;
        $T004A_Largura = 0;
        $T004A_Comprimento = 0;
        $T004A_Resultado = 0;
        
        if ($D009_Id > 0) {
            /*
            T004_Ultimo_Preco(D001_Id,'$mT003[T003_D024_Id]','$T003_Id',1) as Preco_Consultado, 
            T004_Ultimo_Preco(D001_Id,'$mT003[T003_D024_Id]','$T003_Id',2) as Preco_Confirmado 
            */
            
            $sql       = "SELECT T003_D024_Id FROM T003 WHERE T003_Id = '{$T003_Id}'";
            $resultado = mysql_query($sql);
            $rowT003   = mysql_fetch_assoc($resultado);
            
            
            $sql = <<<EOT
            SELECT  *
                    FROM D001
                LEFT JOIN D049 ON D049_D001_Id=D001_Id
                LEFT JOIN D083 ON D083_D001_Id=D001_Id AND D083_D024_Id = '{$rowT003['T003_D024_Id']}'
                LEFT JOIN D009 ON D009_D049_Id=D049_Id
                LEFT JOIN T001 ON T001_Id=D009_T001_Id
                LEFT JOIN D082 ON D082_Id=D049_D082_Id
                LEFT JOIN D041 ON D041_D049_Id=D049_Id
                LEFT JOIN D005 ON D005_Id=D001_D005_Id
                    WHERE D009_Id = '{$D009_Id}'
EOT;
            
            $resultado = mysql_query($sql);
            $row       = mysql_fetch_assoc($resultado);
            
            // Aliquota IPI
            //$row['D005_Aliquota_IPI'] = number_format($row['D005_Aliquota_IPI'],2,',','.');
            $cClausula = " SELECT *, ";
            $cClausula .= " D053_ST_ICMS_Interno, 
                          D053_ST_ICMS_Inter_Estadual, 
                          D053_Flag_ST, 
                          D018_Id, 
                          C004_D018_Id, 
                          C004_CFOP_ST_Venda_Dentro_Estado, 
                          C004_CFOP_ST_Venda_Fora_Estado,
                          C004_CFOP_CONSUMO_Venda_Dentro_Estado,
                          C004_CFOP_CONSUMO_Venda_Fora_Estado,
                          T003_Flag_Lista_Preco,
                          D024_Flag_Contribuinte, 
                          T003A_Flag_Conversao_Unidade";
            $cClausula .= " FROM T003 ";
            $cClausula .= " LEFT JOIN D024 ON D024_Id=T003_D024_Id ";
            $cClausula .= " LEFT JOIN D018 ON D018_Id=D024_D018_Id ";
            $cClausula .= " LEFT JOIN D053 ON D053_D018_Id=D024_D018_Id AND D053_C004_Id=T003_C004_Id ";
            $cClausula .= " LEFT JOIN C004 ON C004_Id=T003_C004_Id ";
            $cClausula .= " LEFT JOIN T003A ON T003_Id=T003A_T003_Id ";
            $cClausula .= " WHERE T003_Id='{$T003_Id}'";
            $T003  = mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                return "$cErro<br />$cClausula";
                exit;
            }
            $mT003 = mysql_fetch_array($T003);
            
            /*
            $sql = <<<EOT
            SELECT 
            D009_Id, 
            D009_IPI('{$mT003['T003_C004_Id']}',D009_Id,'{$mT003['T003_D024_Id']}','{$mT003['T003_D006_Id']}','1','{$mT003['T003_Flag_Acp']}') as Aliquota_IPI 
            FROM 
            D009 
            WHERE D009_Id='{$D009_Id}'
            EOT;
            $result = mysql_query($sql);
            $row2 = mysql_fetch_assoc($result);
            */
            
            /**
             * Os campos que estão atribuidos o valor zero que é não são selecionados nos selects acima.
             */
            $T004_D009_Id              = $D009_Id;
            $T004_Quantidade           = 0;
            $T004_Valor_Preco_Unitario = $Preco_Tabelado;
            $T004_D037_Id              = $row['D001_D037_Id'];
            $T004_Descricao_Produto    = mysql_real_escape_string($row['D001_Descricao_Produto']);
            $T004_Codigo_Produto       = $row['D001_Codigo_Produto'];
            $T004_IPV                  = "";
            $T003_Id;
            $T004_Codigo_Substituto       = $row['D083_Codigo_Produto_Fornecedor'];
            $T004_Numero_Pedido_Compra    = '';
            $T004_Item_Pedido_Compra      = 'NULL';
            $T004_Observacao              = '';
            $T004_Aliquota_IPI            = $row['Aliquota_IPI'];
            $idVeiculoOrcamento           = 0;
            $T004_Id                      = '';
            $T004_Percentual_Desconto     = 0;
            $T004_Valor_Desconto_Unitario = 0;
            $T004_Quantidade              = gCorrigeNumeroInverte($T004_Quantidade);
            $T004_Valor_Preco_Unitario    = gCorrigeNumeroInverte($T004_Valor_Preco_Unitario);
            $T004A_Quantidade_Conversao   = gCorrigeNumeroInverte($T004_Quantidade);
            if($form !== false){
                $T004_Observacao_Entrega      = $form->campoValorEnviado('T004_Observacao_Entrega');
                $T004A_T066_Id                = $form->campoValorEnviado('T004A_T004_Id');
                $T004A_Observacao_Interna     = $form->campoValorEnviado('T004A_Observacao_Interna');
                $T004A_Codigo_Obra            = $form->campoValorEnviado('T004A_Codigo_Obra');
            }

        } else {
            
            /**
             * ATENÇÃO!!!
             * 
             * Sempre que for utlizar a variável $form, é necessário adicioná-la dentro desse else
             * esse método de incluir produto no orçamento é usado também quando o orçamento é gerado
             * via catálogo no CRM, e nesse caso, nunca teremos a variável form e não irá funcionar a inclusão
             * dos itens via tab CATÁLOGO
             */
			
            $T004_D009_Id                 = $form->campoValorEnviado('T004_D009_Id');
            $T004_D144_Id                 = $form->campoValorEnviado('T004_D144_Id');
            $T004_Quantidade              = $form->campoValorEnviado('T004_Quantidade');
            $T004_Valor_Preco_Unitario    = $form->campoValorEnviado('T004_Valor_Preco_Sem_Desconto_Unitario');
            $T004_D037_Id                 = $form->campoValorEnviado('T004_D037_Id');
            $T004_Descricao_Produto       = mysql_real_escape_string($form->campoValorEnviado('T004_Descricao_Produto'));
            $T004_Codigo_Produto          = $form->campoValorEnviado('T004_Codigo_Produto');
            $T004_IPV                     = $form->campoValorEnviado('T004_IPV');
            $T004_D006_Id                 = $form->campoValorEnviado('T004_D006_Id');
            $T003_Id                      = $form->campoValorEnviado('T004_T003_Id');
            $T004_Codigo_Substituto       = $form->campoValorEnviado('T004_Codigo_Substituto');
            $T004_Numero_Pedido_Compra    = $form->campoValorEnviado('T004_Numero_Pedido_Compra');
            $T004_Item_Pedido_Compra      = $form->campoValorEnviado('T004_Item_Pedido_Compra');
            $T004_Item_Pedido_Compra      = (empty($T004_Item_Pedido_Compra) ? 'NULL' : $T004_Item_Pedido_Compra);
            $T004_Observacao              = $form->campoValorEnviado('T004_Observacao');
            $T004_Observacao_Entrega      = $form->campoValorEnviado('T004_Observacao_Entrega');
            $T004_Aliquota_IPI            = $form->campoValorEnviado('T004_Aliquota_IPI');
            $idVeiculoOrcamento           = $form->campoValorEnviado('idVeiculoOrcamento');
            $T004_D005_Id                 = $form->campoValorEnviado('T004_D005_Id');
            $T004_Percentual_Desconto     = gCorrigeNumeroInverte($form->campoValorEnviado('T004_Percentual_Desconto'));
            $T004_Id                      = '';
            $T004_Flag_Destino_Produto    = $form->campoValorEnviado('T004_Flag_Destino_Produto');
            $T004_Codigo_Local            = mysql_real_escape_string(strtoupper(trim($form->campoValorEnviado('T004_Codigo_Local'))));
            //$NcmProduto=$form->campoValorEnviado('NcmProduto');
            //$T004_Percentual_Desconto=0;
            $T004_Valor_Desconto_Unitario = 0;
            
            $T004_Quantidade            = gCorrigeNumeroInverte($T004_Quantidade);
            $T004_Valor_Preco_Unitario  = gCorrigeNumeroInverte($T004_Valor_Preco_Unitario);
            $T004A_T066_Id              = $form->campoValorEnviado('T004A_T066_Id');
            $T004A_Data_Entrega         = gCorrigeDataInverte($form->campoValorEnviado('T004A_Data_Entrega'));
            $T004A_Quantidade_Conversao = gCorrigeNumeroInverte($form->campoValorEnviado('T004A_Quantidade_Conversao'));
            $T004A_Observacao_Interna     = $form->campoValorEnviado('T004A_Observacao_Interna');
            $T004A_Codigo_Obra            = $form->campoValorEnviado('T004A_Codigo_Obra');

            $medidasQuantidade = $this->prepararMedidasQuantidade(
                $form->campoValorEnviado('T004A_Altura'),
                $form->campoValorEnviado('T004A_Largura'),
                $form->campoValorEnviado('T004A_Comprimento')
            );
            if ($medidasQuantidade === false) {
                return 'Informe pelo menos duas medidas numéricas maiores que zero para a calculadora de quantidade.';
            }
            $T004A_Altura = $medidasQuantidade['altura'];
            $T004A_Largura = $medidasQuantidade['largura'];
            $T004A_Comprimento = $medidasQuantidade['comprimento'];
            $T004A_Resultado = $medidasQuantidade['resultado'];
            
            //$Aliquotas = $this->retornaAliquotas($D009_Id, $T003_Id, false);

            $T003 = mysql_query("SELECT T003A_Flag_Conversao_Unidade
                                   FROM T003 
                              LEFT JOIN T003A ON T003_Id = T003A_T003_Id
                                  WHERE T003_Id = '{$T003_Id}'");
            $mT003 = mysql_fetch_array($T003);
        }

        $D001  = mysql_query("SELECT D001_Id,
                                  D082_Id,
                                  D001_Codigo_Produto,
                                  D049_Origem_Mercadoria,
                                  D001_Flag_Tipo,
                                  D049_Flag_Nacional_Importado,
                                  D001_Cubagem_Unitaria,
                                  T001_Id,
                                  D009_Preco_Pauta_ST
                             FROM D001
                        LEFT JOIN D049 ON D049_D001_Id=D001_Id
                        LEFT JOIN D009 ON D009_D049_Id=D049_Id
                        LEFT JOIN T001 ON T001_Id=D009_T001_Id
                        LEFT JOIN D082 ON D082_Id=D049_D082_Id
                            WHERE D009_Id='$T004_D009_Id'");
        $mD001 = mysql_fetch_array($D001);
        
        $T004_D001_Id        = $mD001['D001_Id'];
        $T004_Codigo_Produto = $mD001['D001_Codigo_Produto'];
        $T004_Preco_Pauta_ST = $mD001['D009_Preco_Pauta_ST'];
        
        $C007  = mysql_query("SELECT *
                             FROM C007
                            WHERE C007_Id={$g['usuarioAtual']}");
        $mC007 = mysql_fetch_array($C007);
        
        $T004  = mysql_query("SELECT T004_Item
                                      FROM T004
                                     WHERE T004_T003_Id='$T003_Id'
                                  order by T004_Item desc
                                     limit 1");
        $mT004 = mysql_fetch_array($T004);
        
        $T004_Item = $mT004['T004_Item'] + 1;
        
        $cClausula = " SELECT ";
        $cClausula .= " D009_Id, ";
        $cClausula .= " D049_Id, ";
        $cClausula .= " D001_Id, ";
        $cClausula .= " D005_Flag_ST, ";
        $cClausula .= " D009_Valor_Custo_Unitario, ";
        $cClausula .= " IFNULL(D009_Flag_Preco_Tabelado,'') as D009_Flag_Preco_Tabelado, ";
        $cClausula .= " D049_Origem_Mercadoria, ";
        $cClausula .= " D009_Origem_Mercadoria, ";
        $cClausula .= " D001_Flag_Pre_Cadastro ";
        $cClausula .= " FROM D001 ";
        $cClausula .= " LEFT JOIN D049 ON D001_Id=D049_D001_Id ";
        $cClausula .= " LEFT JOIN D009 ON D049_Id=D009_D049_Id ";
        $cClausula .= " LEFT JOIN D005 ON D005_Id=D001_D005_Id ";
        $cClausula .= " WHERE D009_Id='$T004_D009_Id' ";
        $T004  = mysql_query($cClausula);
        $mT004 = mysql_fetch_array($T004);
        
        //Caso a origem da mercadoria esteja preenchida no D009, utilizamos este campo
        if($mT004['D009_Origem_Mercadoria'] != ''){
            $mT004['D049_Origem_Mercadoria'] = $mT004['D009_Origem_Mercadoria'];
        }

        $erro = mysql_error();
        if (!empty($erro) && !$D009_Id) {
            return "Erro consulta custo produto: $erro";
        }
        
        if (($mT004['D009_Id']) <= 0) {
            return "Erro: Produto não encontrado";
        }

        require_once('bibliotecas/classes/SMTProduto.php');
        $SMTProduto = new SMTProduto();
        $T004_Descricao_Produto = $SMTProduto->descricaoConversao($T004_Descricao_Produto, $T004A_Quantidade_Conversao, $T004_D001_Id);
        //$T004_Descricao_Produto = mysql_real_escape_string($T004_Descricao_Produto); já faz o escape no incio, realizando mais uma vez da problema

        // verifica qual preço usar, baseado na lista preço do orçamento ou do cliente
        $listPreco    = mysql_query("SELECT T003_Flag_Lista_Preco, 
                                            T003_D024_Id, 
                                            T003_C004_Id, 
                                            T003_Flag_ACP, 
                                            T003_C004_Id_Estoque, 
                                            T003_Casas_Decimais_Preco,
                                            D148_D018_Id 
                                       FROM T003 
                                  LEFT JOIN D148 ON D148_Id = T003_D148_Id_Entrega 
                                      WHERE T003_Id='{$T003_Id}'");
        $mLista_Preco = mysql_fetch_array($listPreco);
        $D024_Id      = $mLista_Preco['T003_D024_Id'];
        
        $contextoCFOP = [
            'ufEnderecoEntrega' => $mLista_Preco['D148_D018_Id']
        ];

        //$cfop = $this->retornaCFOP($mLista_Preco['T003_D024_Id'], $T004_D009_Id, $mLista_Preco['T003_C004_Id'], $T004_Flag_Destino_Produto, false, false, false, $mLista_Preco['T003_C004_Id_Estoque']);
        //$demonstrativoCFOP = $this->retornaCFOP($mLista_Preco['T003_D024_Id'], $T004_D009_Id, $mLista_Preco['T003_C004_Id'], $T004_Flag_Destino_Produto, '', true, false, $mLista_Preco['T003_C004_Id_Estoque']);
        $cfop = $this->retornaCFOPContexto($mLista_Preco['T003_D024_Id'], $T004_D009_Id, $mLista_Preco['T003_C004_Id'], $T004_Flag_Destino_Produto, false, false, false, $mLista_Preco['T003_C004_Id_Estoque'], false, $contextoCFOP);
        $demonstrativoCFOP = $this->retornaCFOPContexto($mLista_Preco['T003_D024_Id'], $T004_D009_Id, $mLista_Preco['T003_C004_Id'], $T004_Flag_Destino_Produto, '', true, false, $mLista_Preco['T003_C004_Id_Estoque'], false, $contextoCFOP);

        $T004_D006_Id = $cfop;
        
        $Aliquotas = $this->retornaAliquotas($T004_D009_Id, $T003_Id, false, false, false, false, false, $cfop);
        
        $demonstrativoT001 = $mD001['T001_Id'];
        //////////////////////////////////////////////////////////////////////////////////////
        // Método retornaPrecoVenda atualizado com demonstrativo unico

        //Buscando as aliquotas digitadas na tela de montagem do ipv no orçamento
        $T250 = mysql_query("SELECT SUM(T250_Aliquota-T250_Aliquota_Original) AS AliquotasIPV 
                               FROM T250 
                              WHERE T250_T003_Id = '{$T003_Id}' 
                                AND T250_Flag_Altera_Preco='S' 
                                AND T250_Grupo='IPV'");
        $mT250 = mysql_fetch_array($T250);
        $TotalPorcentagemIPV = $mT250['AliquotasIPV'];

        //Buscando as aliquotas digitadas na tela de montagem do icf no orçamento
        $T250icf = mysql_query("SELECT SUM(T250_Aliquota-T250_Aliquota_Original) AS AliquotasICF 
                                  FROM T250 
                                 WHERE T250_T003_Id = '{$T003_Id}' 
                                   AND T250_Flag_Altera_Preco='S' 
                                   AND T250_Grupo='ICF'");
        $mT250icf = mysql_fetch_array($T250icf);
        $TotalPorcentagemICF = $mT250icf['AliquotasICF'];

        $dadosPrecoVenda         = $CAD002->retornaPrecoVenda($mT004['D009_Id'], $mLista_Preco['T003_Flag_Lista_Preco'], $D024_Id, false, $cfop, $Aliquotas['Aliquota_ICMS'], false, false, $mLista_Preco['T003_Casas_Decimais_Preco'], $Aliquotas['Aliquota_PIS'], $Aliquotas['Aliquota_COFINS'], $Aliquotas['Aliquota_IPI'], $Aliquotas['ST_VA'], $Aliquotas['Percentual_Reducao_ICMS_ST'], $Aliquotas['ST_ICMS_Interno'], $Aliquotas['Percentual_Reducao_ICMS'], $Aliquotas['ICMS_Inter_Estadual'], $Aliquotas['Aliquota_IRPJ'], $Aliquotas['Aliquota_CSLL'], 0 , $TotalPorcentagemIPV, $TotalPorcentagemICF);
        $Preco_Tabelado          = $g['C031']['mantemPrecoItemComoTabela'] == 'S' ? ($T004_Valor_Preco_Unitario > gCorrigeNumeroInverte($dadosPrecoVenda[0]) ? $T004_Valor_Preco_Unitario : gCorrigeNumeroInverte($dadosPrecoVenda[0])) : gCorrigeNumeroInverte($dadosPrecoVenda[0]);
        $demonstrativoPrecoVenda = mysql_real_escape_string(serialize($dadosPrecoVenda[1]));
        $custoUnitario           = $dadosPrecoVenda[4];


        if ($T004_Aliquota_IPI > 0 || $Aliquotas['Aliquota_IPI']) {
            $D088_Codigo_ST = '50';
        } else {
            $D088_Codigo_ST = '53';
        }
        
       
        // Buscando o percentual de tributos (lei transparências)           
        $D149  = mysql_query("SELECT D149_Percentual_Trib_Transparencia FROM D149 WHERE D149_Id='{$Aliquotas['D149_Id']}'");
        $mD149 = mysql_fetch_array($D149);

        $D006  = mysql_query("SELECT D006_Percentual_Trib_Transparencia, 
                                     D088_Codigo_ST, 
                                     D006_Percentual_Desconto_CFOP,
                                     D006_Flag_Destaca_Desconto_NF 
                                FROM D006 
                           LEFT JOIN D088 ON D088_Id=D006_D088_Id 
                               WHERE D006_Id='{$T004_D006_Id}'");

        $mD006 = mysql_fetch_array($D006);
        $D088_Codigo_ST = $mD006['D088_Codigo_ST'];

        if($mD006['D006_Percentual_Desconto_CFOP'] > 0 && $mD006['D006_Flag_Destaca_Desconto_NF'] == 'N'){
            $T004_Percentual_Desconto += $mD006['D006_Percentual_Desconto_CFOP'];
        }

        if (!empty($mD149['D149_Percentual_Trib_Transparencia']) AND $mD149['D149_Percentual_Trib_Transparencia'] > 0) {
            $T004_Percentual_Trib_Transparencia = $mD149['D149_Percentual_Trib_Transparencia'];
        } else if (!empty($mD006['D006_Percentual_Trib_Transparencia']) AND $mD006['D006_Percentual_Trib_Transparencia'] > 0) {
            $T004_Percentual_Trib_Transparencia = $mD006['D006_Percentual_Trib_Transparencia'];
        } else {
            $T004_Percentual_Trib_Transparencia = 0;
        }

        if ($Aliquotas['D005_Id'] == '') {
            $demonstrativoNCM = $FIS003->demonstrativoNCM($T004_D005_Id);
        } else {
            $demonstrativoNCM = $FIS003->demonstrativoNCM($Aliquotas['D005_Id']);
        }

        if ($T004_Valor_Preco_Unitario <= 0) {
            $T004_Valor_Preco_Unitario = $Preco_Tabelado;
        }

        if ($T004_Percentual_Desconto > 0) {            
            $T004_Valor_Preco_Unitario = $T004_Valor_Preco_Unitario - ($T004_Valor_Preco_Unitario * ($T004_Percentual_Desconto / 100));            
            if ($Preco_Tabelado > 0) {
                $T004_Percentual_Desconto     = ((1 - ($T004_Valor_Preco_Unitario / $Preco_Tabelado)) * 100);
                $T004_Valor_Desconto_Unitario = (($Preco_Tabelado * ($T004_Valor_Preco_Unitario / 100)));
            } else {
                $Preco_Tabelado = $T004_Valor_Preco_Unitario;
            }            
        } else {            
            if ($Preco_Tabelado > 0) {
                $T004_Percentual_Desconto     = ((1 - ($T004_Valor_Preco_Unitario / $Preco_Tabelado)) * 100);
                $T004_Valor_Desconto_Unitario = (($Preco_Tabelado * ($T004_Valor_Preco_Unitario / 100)));
            } else {
                $Preco_Tabelado = $T004_Valor_Preco_Unitario;
            }            
        }

        //////////////////////////////////////////////////////////////////////////////////////
        //Métodos que montam os demonstrativos do preço do histórico e da tabela do fornecedor
        $precoTabela                      = $CAD002->cad002RetornaPrecoTabela($mT004['D009_Id'], $mLista_Preco['T003_Flag_Lista_Preco'], $D024_Id, $mLista_Preco['T003_Flag_ACP'], $cfop, $Aliquotas['Aliquota_ICMS'], $Aliquotas['Aliquota_PIS'], $Aliquotas['Aliquota_COFINS'], $Aliquotas['Aliquota_IPI'], 0, 0, $Aliquotas['Aliquota_IRPJ'], $Aliquotas['Aliquota_CSLL']);
        $demonstrativoPrecoTabela         = $precoTabela[1];
        $valorPrecoTabela                 = gCorrigeNumeroInverte($precoTabela[0]);

        $precoCompra                      = $CAD002->cad002RetornaPrecoUltimaCompra($mT004['D009_Id'], $mLista_Preco['T003_Flag_Lista_Preco'], $D024_Id, $mLista_Preco['T003_Flag_ACP'], $cfop, $Aliquotas['Aliquota_ICMS'], 4, $Aliquotas['Aliquota_PIS'], $Aliquotas['Aliquota_COFINS'], $Aliquotas['Aliquota_IPI']);
        $demonstrativo                    = $precoCompra[1];
        $valorPrecoCompra                 = gCorrigeNumeroInverte($precoCompra[0]);

        $demonstrativoCustoTabela         = $CAD002->cad002demonstrativoCustoTabela($mT004['D009_Id']);


        $sqlD009Transf = mysql_query("SELECT D009_Id FROM D009 WHERE D009_D049_Id='{$mT004['D049_Id']}' AND D009_C004_Id = '{$g['C031']['C004PrecoTransferenciaGridProdutos']}'");
        $resD009Transf = mysql_fetch_array($sqlD009Transf);
        $dadosPrecoTransferencia          = $CAD002->cad002RetornaPrecoTransferencia($resD009Transf['D009_Id'], $g['C031']['C004PrecoTransferenciaGridProdutos'], $g['C004']['C004_D024_Id'], $T003_Id);
        $valorPrecoTransferencia          = gCorrigeNumeroInverte($dadosPrecoTransferencia[0]);
        $demonstrativoPrecoTransferencia  = $dadosPrecoTransferencia[1];
        $valorCustoTransferencia          = $dadosPrecoTransferencia[4];
        $origemProdutoTransferencia       = $dadosPrecoTransferencia[5];
        // Demonstrativo
        $demonstrativo = mysql_real_escape_string(serialize($demonstrativo));
        $demonstrativoPrecoTabela = mysql_real_escape_string(serialize($demonstrativoPrecoTabela));
        $demonstrativoPrecoTransferencia = mysql_real_escape_string(serialize($demonstrativoPrecoTransferencia));
        $demonstrativoCFOP = mysql_real_escape_string($demonstrativoCFOP);
        $demonstrativoNCM = mysql_real_escape_string($demonstrativoNCM);
        $demonstrativoCustoTabela = mysql_real_escape_string($demonstrativoCustoTabela);

// Carlos Carvalho

        $cClausula = " insert into T004 (";
        $cClausula .= " T004_C004_Id, ";
        $cClausula .= " T004_Item, ";
        $cClausula .= " T004_Codigo_Produto, ";
        $cClausula .= " T004_Codigo_Substituto, ";
        $cClausula .= " T004_Descricao_Produto, ";
        $cClausula .= " T004_T003_Id, ";
        $cClausula .= " T004_D024_Id, ";
        $cClausula .= " T004_D009_Id, ";
        $cClausula .= " T004_D037_Id, ";
        $cClausula .= " T004_D082_Id, ";
        $cClausula .= " T004_Quantidade, ";
        $cClausula .= " T004_Valor_Custo_Unitario, ";
        $cClausula .= " T004_Valor_Preco_Unitario, ";
        $cClausula .= " T004_Valor_Preco_Sem_Desconto_Unitario, ";
        $cClausula .= " T004_Valor_Preco_Original, ";
        $cClausula .= " T004_Valor_Preco_Original_Bruto, ";
        $cClausula .= " T004_Percentual_Desconto, ";
        $cClausula .= " T004_Valor_Desconto_Unitario, ";
        $cClausula .= " T004_Percentual_Desconto_Bruto, ";
        $cClausula .= " T004_Valor_Desconto_Unitario_Bruto, ";
        $cClausula .= " T004_IPV, ";
        $cClausula .= " T004_Peso_Unitario, ";
        $cClausula .= " T004_Peso_Bruto, ";
        $cClausula .= " T004_D005_Id,";
        $cClausula .= " T004_Classificacao_Fiscal,";
        $cClausula .= " T004_D006_Id,";
        $cClausula .= " T004_Aliquota_IPI,";
        $cClausula .= " T004_Aliquota_ICMS,";
        $cClausula .= " T004_Percentual_Reducao_ICMS,";
        $cClausula .= " T004_Percentual_Reducao_ICMS_ST,";
        $cClausula .= " T004_ST_VA,";
        $cClausula .= " T004_Flag_ST,";
        $cClausula .= " T004_Flag_Situacao_Tributaria,";
        $cClausula .= " T004_ICMS_Inter_Estadual,";
        $cClausula .= " T004_ST_ICMS_Interno,";
        $cClausula .= " T004_ICMS_Inter_Estadual_MVA,";
        $cClausula .= " T004_ST_ICMS_Interno_MVA,";
        $cClausula .= " T004_Flag_Isento_PIS,";
        $cClausula .= " T004_Aliquota_PIS,";
        $cClausula .= " T004_Situacao_Tributaria_PIS,";
        $cClausula .= " T004_Flag_Isento_COFINS,";
        $cClausula .= " T004_Aliquota_COFINS, ";
        $cClausula .= " T004_Flag_Preco_Especial, ";
        $cClausula .= " T004_Situacao_Tributaria_COFINS, ";
        $cClausula .= " T004_Situacao_Tributaria_IPI, ";
        $cClausula .= " T004_D059_Id, ";
        $cClausula .= " T004_Observacao, ";
        $cClausula .= " T004_Numero_Pedido_Compra, ";
        
        if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
            $cClausula .= " T004_Item_Pedido_Compra, ";
            $cClausula .= " T004_Valor_Preco_Bruto, ";
        } else {
            $cClausula .= " T004_Item_Pedido_Compra, ";
        }
        $cClausula .= " T004_Cubagem_Unitaria, ";
        $cClausula .= " T004_Percentual_Trib_Transparencia, ";
        $cClausula .= " T004_Flag_Pre_Cadastro, ";
        $cClausula .= " T004_D144_Id, ";
        $cClausula .= " T004_Flag_Origem_Produto, ";
        $cClausula .= " T004_Demonstrativo_Preco, ";
        $cClausula .= " T004_Demonstrativo_Preco_Tabela, ";
        $cClausula .= " T004_Demonstrativo_Preco_Transferencia, ";
        $cClausula .= " T004_Valor_Preco_Compra, ";
        $cClausula .= " T004_Valor_Preco_Tabela, ";
        $cClausula .= " T004_Valor_Preco_Transferencia, ";
        $cClausula .= " T004_Valor_Custo_Transferencia, ";
        $cClausula .= " T004_Flag_Origem_Produto_Transferencia, ";
        $cClausula .= " T004_T001_Id, ";
        $cClausula .= " T004_Observacao_Entrega, ";
        $cClausula .= " T004_Flag_Destino_Produto, ";
        $cClausula .= " T004_D006_Id_Info, ";
        $cClausula .= " T004_D005_Id_Info, ";
        $cClausula .= " T004_Demonstrativo_Custo_Tabela, ";
        $cClausula .= " T004_Demonstrativo_Preco_Venda, ";
        $cClausula .= " T004_Aliquota_IRPJ, ";
        $cClausula .= " T004_Flag_Isento_IRPJ,";
        $cClausula .= " T004_Aliquota_CSLL, ";
        $cClausula .= " T004_Flag_Isento_CSLL, ";
        $cClausula .= " T004_Percentual_Base_Operacao_Propria, ";
        $cClausula .= " T004_UF_ICMS_Substituicao_Devido, ";
        $cClausula .= " T004_Percentual_Trib_Federal, ";
        $cClausula .= " T004_Percentual_Trib_Estadual, ";
        $cClausula .= " T004_Percentual_Trib_Municipal, ";
        $cClausula .= " T004_Aliquota_ICMS_Credito, ";
        $cClausula .= " T004_Preco_Pauta_ST, ";
        $cClausula .= " T004_Codigo_Local, ";
        $cClausula .= " T004_Percentual_Partilha_ICMS_Destino, ";
        $cClausula .= " T004_Percentual_Partilha_ICMS_Origem ";
        $cClausula .= " ) VALUES ( ";
        $cClausula .= " '{$g['empresaAtual']}', ";
        $cClausula .= " '{$T004_Item}', ";
        $cClausula .= " '{$T004_Codigo_Produto}', ";
        $cClausula .= " '{$T004_Codigo_Substituto}', ";
        $cClausula .= " '{$T004_Descricao_Produto}', ";
        $cClausula .= " '{$T003_Id}', ";
        $cClausula .= " '{$D024_Id}', ";
        $cClausula .= " '{$T004_D009_Id}', ";
        $cClausula .= " '{$T004_D037_Id}', ";
        $cClausula .= " '{$Aliquotas['D082_Id']}', ";
        $cClausula .= " '{$T004_Quantidade}', ";
        $cClausula .= " '{$custoUnitario}', ";
        $cClausula .= " '{$T004_Valor_Preco_Unitario}', ";
        $cClausula .= " '{$Preco_Tabelado}', ";
        $cClausula .= " '{$Preco_Tabelado}', ";
        $cClausula .= " '{$Preco_Tabelado}', ";
        $cClausula .= " '{$T004_Percentual_Desconto}', ";
        $cClausula .= " '{$T004_Valor_Desconto_Unitario}', ";
        $cClausula .= " '{$T004_Percentual_Desconto}', ";
        $cClausula .= " '{$T004_Valor_Desconto_Unitario}', ";
        $cClausula .= " '{$T004_Valor_Preco_Unitario}/{$mT004['D009_Valor_Custo_Unitario']}', ";
        $cClausula .= " '{$Aliquotas['D001_Peso_Unitario_Kg']}', ";
        $cClausula .= " '{$Aliquotas['D001_Peso_Unitario_Bruto']}', ";
        if ($Aliquotas['D005_Id'] == '') {
            $cClausula .= " '{$T004_D005_Id}',";
        } else {
            $cClausula .= " '{$Aliquotas['D005_Id']}',";
        }
        $cClausula .= " '{$Aliquotas['Classificacao_Fiscal']}',";
        $cClausula .= " '{$T004_D006_Id}',";
        //$cClausula .= " '{$mT004['Aliquota_IPI']}',";
        if ($T004_Aliquota_IPI <= 0) {
            $cClausula .= " '{$Aliquotas['Aliquota_IPI']}',";
        } else {
            $cClausula .= " '{$T004_Aliquota_IPI}',";
        }
        $cClausula .= " '{$Aliquotas['Aliquota_ICMS']}',";
        $cClausula .= " '{$Aliquotas['Percentual_Reducao_ICMS']}',";
        $cClausula .= " '{$Aliquotas['Percentual_Reducao_ICMS_ST']}',";
        $cClausula .= " '{$Aliquotas['ST_VA']}',";
        $cClausula .= " '{$Aliquotas['Flag_ST']}',";
        $cClausula .= " '{$Aliquotas['Flag_Situacao_Tributaria']}',";
        $cClausula .= " '{$Aliquotas['ICMS_Inter_Estadual']}',";
        $cClausula .= " '{$Aliquotas['ST_ICMS_Interno']}',";
        $cClausula .= " '{$Aliquotas['ST_ICMS_Inter_Estadual_MVA']}',";
        $cClausula .= " '{$Aliquotas['ST_ICMS_Interno_MVA']}',";
        $cClausula .= " '{$Aliquotas['Flag_Isento_PIS']}',";
        $cClausula .= " '{$Aliquotas['Aliquota_PIS']}',";
        $cClausula .= " '{$Aliquotas['Situacao_Tributaria_PIS']}',";
        $cClausula .= " '{$Aliquotas['Flag_Isento_COFINS']}',";
        $cClausula .= " '{$Aliquotas['Aliquota_COFINS']}', ";
        $cClausula .= " '',";
        $cClausula .= " '{$Aliquotas['Situacao_Tributaria_COFINS']}',";
        $cClausula .= " '{$D088_Codigo_ST}',";
        $cClausula .= " '{$Aliquotas['D059_Id']}',";
        $cClausula .= " '$T004_Observacao', ";
        $cClausula .= " '$T004_Numero_Pedido_Compra', ";
        if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
            $cClausula .= " '{$T004_Item_Pedido_Compra}', ";
            $cClausula .= " '{$T004_Valor_Preco_Unitario}', ";
        } else {
            $cClausula .= " '{$T004_Item_Pedido_Compra}', ";
        }
        $cClausula .= " '{$mD001['D001_Cubagem_Unitaria']}', ";
        $cClausula .= " '{$T004_Percentual_Trib_Transparencia}', ";
        $cClausula .= " '{$mT004['D001_Flag_Pre_Cadastro']}', ";
        $cClausula .= " '{$T004_D144_Id}', ";
        $cClausula .= " '{$mT004['D049_Origem_Mercadoria']}', ";
        $cClausula .= " '{$demonstrativo}', ";
        $cClausula .= " '{$demonstrativoPrecoTabela}', ";
        $cClausula .= " '{$demonstrativoPrecoTransferencia}', ";
        $cClausula .= " '{$valorPrecoCompra}', ";
        $cClausula .= " '{$valorPrecoTabela}', ";
        $cClausula .= " '{$valorPrecoTransferencia}', ";
        $cClausula .= " '{$valorCustoTransferencia}', ";
        $cClausula .= " '{$origemProdutoTransferencia}', ";
        $cClausula .= " '{$demonstrativoT001}', ";
        $cClausula .= " '{$T004_Observacao_Entrega}', ";
        $cClausula .= " '{$T004_Flag_Destino_Produto}', ";
        $cClausula .= " '{$demonstrativoCFOP}', ";
        $cClausula .= " '{$demonstrativoNCM}', ";
        $cClausula .= " '{$demonstrativoCustoTabela}', ";
        $cClausula .= " '{$demonstrativoPrecoVenda}', ";
        $cClausula .= " '{$Aliquotas['Aliquota_IRPJ']}', ";
        $cClausula .= " '{$Aliquotas['Flag_Isento_IRPJ']}',";
        $cClausula .= " '{$Aliquotas['Aliquota_CSLL']}', ";
        $cClausula .= " '{$Aliquotas['Flag_Isento_CSLL']}',";
        $cClausula .= " '{$Aliquotas['Percentual_Base_Operacao_Propria']}',";
        $cClausula .= " '{$Aliquotas['UF_ICMS_Substituicao_Devido']}',";
        $cClausula .= " '{$Aliquotas['Percentual_Trib_Federal']}',";
        $cClausula .= " '{$Aliquotas['Percentual_Trib_Estadual']}',";
        $cClausula .= " '{$Aliquotas['Percentual_Trib_Municpal']}',";
        $cClausula .= " '{$Aliquotas['Aliquota_ICMS_Credito']}',";
        $cClausula .= " '{$T004_Preco_Pauta_ST}', ";
        $cClausula .= " '{$T004_Codigo_Local}', ";
        $cClausula .= " '{$Aliquotas['Percentual_Partilha_ICMS_Destino']}', ";
        $cClausula .= " '{$Aliquotas['Percentual_Partilha_Origem']}' ";
        $cClausula .= " ); ";
        mysql_query($cClausula);
        $T004_Id = $g['mysqlLastId'];
        $cErro   = mysql_error();
        if (!empty($cErro)) {
            return "Erro: $cErro";
        } else {

            if (!empty($T004_Codigo_Substituto)) {
                /*
                $D083 = mysql_query("SELECT D083_Id,D083_Flag_Cliente,D083_Codigo_Produto_Fornecedor FROM D083 WHERE D083_D001_Id='{$mT004['D001_Id']}' AND D083_D049_Id='{$mT004['D049_Id']}' AND D083_D024_Id='$D024_Id'");
                if (mysql_num_rows($D083) <= 0) {
                    mysql_query("insert into D083 (D083_D049_Id,D083_D001_Id,D083_D024_Id,D083_Data_Cadastro,D083_Flag_Cliente,D083_Codigo_Produto_Fornecedor) VALUES ('{$mT004['D049_Id']}','{$mT004['D001_Id']}','$D024_Id',curdate(),'S','$T004_Codigo_Substituto')");
                } else {
                    $mD083 = mysql_fetch_array($D083);
                    mysql_query("UPDATE D083 SET D083_Flag_Cliente='S',D083_Codigo_Produto_Fornecedor='$T004_Codigo_Substituto' WHERE D083_Id='{$mD083['D083_Id']}'");
                }
                */
                require_once("bibliotecas/classes/CAD002.php");
                $CAD002 = new CAD002();
                $CAD002->definirCodigoFornecedor($mT004['D009_Id'], $D024_Id, $T004_Codigo_Substituto, '', true, false);
            }
            
            mysql_query("UPDATE T004A 
                      LEFT JOIN T004 ON T004_Id = T004A_T004_Id
                            SET T004A_T066_Id                  = '{$T004A_T066_Id}',
                                T004A_Data_Entrega             = '{$T004A_Data_Entrega}',
                                T004A_Quantidade_Conversao     = '{$T004A_Quantidade_Conversao}',
                                T004A_Quantidade_Comercial     = IF('{$mT003['T003A_Flag_Conversao_Unidade']}' = 'S', T004_Quantidade, 0.00),
                                T004A_Preco_Unitario_Comercial = IF('{$mT003['T003A_Flag_Conversao_Unidade']}' = 'S', T004_Valor_Preco_Unitario, 0.00),
                                T004A_Altura                   = '{$T004A_Altura}',
                                T004A_Largura                  = '{$T004A_Largura}',
                                T004A_Comprimento              = '{$T004A_Comprimento}',
                                T004A_Resultado                = '{$T004A_Resultado}',
                                T004A_Observacao_Interna       = '{$T004A_Observacao_Interna}',
                                T004A_Codigo_Obra              = '{$T004A_Codigo_Obra}',
                                T004A_D006_Id_Info             = '{$demonstrativoCFOP}',
                                T004A_Percentual_FCP           = '{$Aliquotas['Percentual_FCP']}',
                                T004A_Percentual_Desconto_CFOP = '{$mD006['D006_Percentual_Desconto_CFOP']}'

                          WHERE T004A_T004_Id = '{$T004_Id}'");
            //Atualiza as aliquotas com o D006 correto do T004
            $this->atualizarAliquotas($T003_Id, false, $T004_Id);
            $cSql = mysql_query("call T004_Gravar_Totalizacao_4('$T004_Id');");
            if (empty($g['mcpQuoteCreateBatch'])) {
                $cSql = mysql_query("call T003_Gravar_Totalizacao_4('$T003_Id');");
            }
        }

        //$this->processoOrcamento($T004_Id);
        // caso o produto possua serviços associados, insere estes serviços na T145
        $result  = mysql_query("SELECT D001_Id FROM D009 LEFT JOIN D049 ON D049_Id=D009_D049_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id WHERE D009_Id='{$T004_D009_Id}'");
        $retorno = mysql_fetch_array($result);
        $D001_Id = $retorno['D001_Id'];
        $result  = mysql_query("SELECT * FROM D135 LEFT JOIN D112 ON D112_Id=D135_D112_Id WHERE D135_D001_Id='{$D001_Id}'");
        
        if (mysql_num_rows($result) > 0) {
            mysql_query("SET AUTOCOMMIT=0");
            mysql_query("START TRANSACTION");
            while ($D135 = mysql_fetch_array($result)) {
                $cClausula = " insert into T145 (";
                $cClausula .= " T145_T003_Id, ";
                $cClausula .= " T145_D112_Id, ";
                $cClausula .= " T145_D110_Id, ";
                $cClausula .= " T145_T004_Id, ";
                $cClausula .= " T145_Titulo, ";
                $cClausula .= " T145_Descricao, ";
                $cClausula .= " T145_Tempo_Estimado, ";
                $cClausula .= " T145_Quantidade, ";
                $cClausula .= " T145_Quantidade_Confirmada, ";
                $cClausula .= " T145_Valor_Unitario, ";
                $cClausula .= " T145_Valor_Total, ";
                $cClausula .= " T145_D116_Id ";
                $cClausula .= " ) VALUES ( ";
                $cClausula .= " '$T003_Id', ";
                $cClausula .= " '{$D135['D135_D112_Id']}', ";
                $cClausula .= " '{$D135['D112_D110_Id']}', ";
                $cClausula .= " '$T004_Id', ";
                $cClausula .= " '{$D135['D112_Titulo']}', ";
                $cClausula .= " '{$D135['D112_Descricao']}', ";
                $cClausula .= " '{$D135['D112_Tempo_Estimado']}', ";
                $cClausula .= " '1', ";
                $cClausula .= " '1', ";
                $cClausula .= " '{$D135['D112_Valor_Unitario']}', ";
                $cClausula .= " '{$D135['D112_Valor_Unitario']}', ";
                $cClausula .= " '{$idVeiculoOrcamento}' ";
                $cClausula .= " ) ";
                mysql_query($cClausula);
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return 'falha ao copiar os serviços do produto';
                }
                
                $T145_Id = $g['mysqlLastId'];
                
                $sql   = "SELECT * FROM D113 WHERE D113_D112_Id='{$D135['D135_D112_Id']}'";
                $array = mysql_query($sql);
                
                while ($row = mysql_fetch_assoc($array)) {
                    // D113_D112_Id, D113_D110_Id, D113_Titulo, D113_Descricao
                    $sql = "INSERT INTO T146 (T146_T145_Id, T146_Titulo, T146_Descricao) VALUES ('" . mysql_real_escape_string($T145_Id) . "', '"
                    //.mysql_real_escape_string($row['D113_D110_Id'])."', '"
                        . mysql_real_escape_string($row['D113_Titulo']) . "', '" . mysql_real_escape_string($row['D113_Descricao']) . "');";
                    mysql_query($sql);
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return 'Erro: ' . mysql_error();
                    }
                }
                
                $sql   = "SELECT * FROM D114 WHERE D114_D112_Id='{$D135['D135_D112_Id']}'";
                $array = mysql_query($sql);
                
                while ($row = mysql_fetch_assoc($array)) {
                    $sql = "INSERT INTO T152 (T152_D037_Id, T152_D009_Id, T152_T145_Id, T152_Quantidade) VALUES ('" . mysql_real_escape_string($row['D114_D037_Id']) . "', '" . mysql_real_escape_string($row['D114_D009_Id']) . "', '" . mysql_real_escape_string($T145_Id) . "', '" . mysql_real_escape_string($row['D114_Quantidade']) . "');";
                    mysql_query($sql);
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return 'Erro: ' . mysql_error();
                    }
                }
            }

            mysql_query("COMMIT");
        }
        
        mysql_query("UPDATE T004 SET T004_Valor_Preco_Bruto=T004_Valor_Preco_Bruto WHERE T004_Id='$T004_Id'");
        $this->processoOrcamento($T004_Id);
        return true;
    }
    
    /**
     * excluirServico
     *
     * @param String $T145_Id
     * @return String/bool
     */      
    public function excluirServico($T145_Id)
    {
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        mysql_query("DELETE FROM T146 WHERE T146_T145_Id='$T145_Id'");
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return 'falha na exclusão dos itens do serviço...';
        }
        mysql_query("DELETE FROM T152 WHERE T152_T145_Id='$T145_Id'");
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return 'falha na exclusão dos materiais do serviço...';
        }
        mysql_query("DELETE FROM T145 WHERE T145_Id='$T145_Id'");
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return 'falha na exclusão do serviço...';
        }
        
        mysql_query("COMMIT");
        
        return true;    
    }
    
    /**
     * excluirProduto
     *
     * @param String $T004_Id
     * @return String/bool
     */    
    public function excluirProduto($T004_Id)
    {
        $T004_Id = (int)$T004_Id;
        $produto = mysql_query("SELECT T004_T003_Id, D006_Flag_Destaca_Desconto_NF FROM T004 LEFT JOIN D006 ON D006_Id=T004_D006_Id WHERE T004_Id='{$T004_Id}'");
        if (!$produto || !($mProduto = mysql_fetch_array($produto))) {
            return 'falha na localização do produto...';
        }

        $T003_Id = (int)$mProduto['T004_T003_Id'];
        $atualizarDescontoAposExclusao = $mProduto['D006_Flag_Destaca_Desconto_NF'] === 'S';

        $result = mysql_query("SELECT * FROM T145 WHERE T145_T004_Id='{$T004_Id}'");
        if (!$result) {
            return 'falha na localização dos serviços do produto...';
        }
        if (mysql_num_rows($result) > 0) {
            while ($T145 = mysql_fetch_array($result)) {
                if ($this->excluirServico($T145['T145_Id']) !== true) {
                    return 'falha na exclusão dos serviços do produto...';
                }
            }
        }

        // T004A has no foreign-key cascade in installations that use this flow.
        mysql_query("DELETE FROM T004A WHERE T004A_T004_Id='{$T004_Id}'");
        if (mysql_error()) {
            return 'falha na exclusão dos dados auxiliares do produto...';
        }

        // The item must still exist while the legacy routine discovers its quote.
        if ($this->processarNumeroItemOrcamento($T004_Id, '0') !== true) {
            return 'falha na renumeração dos itens do orçamento...';
        }

        mysql_query("DELETE FROM T004 WHERE T004_Id='{$T004_Id}'");
        if (mysql_error()) {
            return 'falha na exclusão do produto...';
        }

        if ($atualizarDescontoAposExclusao) {
            $this->aplicaDescontoCFOP('T004', 'T003', 0, $T003_Id, 'S');
            if (mysql_error()) {
                return 'falha na atualização do desconto da CFOP...';
            }
        }

        mysql_query("CALL T003_Gravar_Totalizacao_4('{$T003_Id}')");
        if (mysql_error()) {
            return 'falha na totalização do orçamento...';
        }

        return true;
        
    }
    
    /**
     * cancelarProduto
     *
     * @param String $T004_Id
     * @return String/bool
     */    
    public function cancelarProduto($T004_Id)
    {
        $result = mysql_query("SELECT * FROM T145 WHERE T145_T004_Id='{$T004_Id}'");
        if (mysql_num_rows($result) > 0) {
            while ($T145 = mysql_fetch_array($result)) {
                $this->excluirServico($T145['T145_Id']);
            }
        }
        
        mysql_query("UPDATE T004 SET T004_Flag_Cancelado='S' WHERE T004_Id='{$T004_Id}'");
        if (mysql_error()) {
            return 'falha no cancelamento do produto...';
        } else {
            return true;
        }
    }
    
    /**
     * cancelarOrcamento
     *
     * @param Form $form
     * @return String/bool
     */    
    public function cancelarOrcamento($form)
    {
        $T003_Id                           = $form->campoValorEnviado('T003_Id');
        $T003_Observacao_Orcamento_Perdido = $form->campoValorEnviado('T003_Observacao_Orcamento_Perdido');
        $T003_D047_Id                      = $form->campoValorEnviado('T003_D047_Id');
        
        if (empty($T003_Id)) {
            return "T003_Id não existe";
        }
        
        //-- ADICIONA BARRA INVERTIDA PARA NAO DAR ERRO NAS ASPAS:
        $T003_Observacao_Orcamento_Perdido = AddSlashes($T003_Observacao_Orcamento_Perdido);
        
        //-- ELIMINA OS ESPAï¿½OS DOS CAMPOS:
        $T003_Observacao_Orcamento_Perdido = Trim($T003_Observacao_Orcamento_Perdido);
        
        if (empty($T003_Observacao_Orcamento_Perdido)) {
            return "Preencha o campo Observação.";
        }
        
        if (strlen(trim($T003_Observacao_Orcamento_Perdido)) < 20) {
            return "A observação deve ter mo mínimo 20 caracteres";
        }
        
        $cClausula = " UPDATE T004 SET ";
        $cClausula .= " T004_Flag_Cancelado='S'  ";
        $cClausula .= " WHERE T004_T003_Id='{$T003_Id}' ";
        $cSql  = mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            return "Ocorreu um erro ao tentar alterar o registro<br />Erro:$cErro<br />Clausula:$cClausula";
        }
        
        $cClausula = " UPDATE T003 SET ";
        $cClausula .= " T003_Observacao_Orcamento_Perdido='{$T003_Observacao_Orcamento_Perdido}',  ";
        $cClausula .= " T003_D047_Id='{$T003_D047_Id}',  ";
        $cClausula .= " T003_Flag_Perdido='S'  ";
        $cClausula .= " WHERE T003_Id='{$T003_Id}' ";
        $cSql  = mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            return "Ocorreu um erro ao tentar alterar o registro<br />Erro:$cErro<br />Clausula:$cClausula";
        } else {
            return true;
        }
    }
    
    /**
     * calcularParcelaCombo
     *
     * @param String $parcela
     * @param String $valorTotal
     * @param String $tipoJuros
     * @param String $taxaJuros
     * @param String $parcelasIsentas
     * @return String
     */    
    public function calcularParcelaCombo($parcela, $valorTotal, $tipoJuros, $taxaJuros, $parcelasIsentas)
    {
        global $g;
        if ($parcela <= $parcelasIsentas) {
            return gCorrigeNumero($valorTotal / $parcela);
        } else {
            if ($tipoJuros == 'S') {
                return gJurosSimples($valorTotal, $taxaJuros, $parcela);
            } else if ($tipoJuros == 'C') {
                return gJurosCompostos($valorTotal, $taxaJuros, $parcela);
            }
        }
        
    }

    /**
     * populaComboPgtoParcelas
     *
     * @param String $parcela
     * @param String $valorTotal
     * @param String $tipoJuros
     * @param String $taxaJuros
     * @param String $parcelasIsentas
     * @return String
     */    
    public function populaComboPgtoParcelas($dadosTabela)
    {
        global $g;
       // pega as configurações de juros da empresa atual
        $C004=mysql_query("SELECT C004_Tipo_Juros,C004_Taxa_Juros,C004_Parcelas_Isentas_Juros FROM C004 WHERE C004_Id='{$g['empresaAtual']}'");
        $mC004=mysql_fetch_array($C004);
        $CNPJ_Empresa_Corrente = substr($mC004['C004_Cnpj'], 0, -8);

        $tipoJuros=$mC004['C004_Tipo_Juros'];
        $taxaJuros=$mC004['C004_Taxa_Juros'];
        $parcelasIsentas=$mC004['C004_Parcelas_Isentas_Juros'];
        
        $sqlQuery  = mysql_query("SELECT {$dadosTabela['campos']} FROM {$dadosTabela['tabela']} WHERE {$dadosTabela['where']}");
        $sqlQueryResult = mysql_fetch_array($sqlQuery);

        $valorTotalSelect = $sqlQueryResult['valorTotal'];
    
        //GERANDO SIMULAÇÃO PARCELAS
        $select = array();
        $select[] = array('value' => '0', 'title' => 'A COMBINAR');
        for ($i=1; $i<=$g['C031']['numeroMaxParcelas']; $i++) {
            $valorParcela = $this->calcularParcelaCombo($i,$valorTotalSelect,$tipoJuros,$taxaJuros,$parcelasIsentas);
            if ($i>$parcelasIsentas){
                $valorTotal = gCorrigeNumero($i*gCorrigeNumeroInverte($valorParcela));
                $valorParcela.="*";
            }else{
                $valorTotal = gCorrigeNumero($valorTotalOrcamento);
            }

            if($i == $sqlQueryResult['pgtoParcelas']){
                $select[] = array('value' => $i, 'title' => str_pad($i,2,"0",STR_PAD_LEFT) . " x" . " - " .$valorParcela." = ".gCorrigeNumero($valorTotalSelect), 'selected'=>true);
            }else{
                $select[] = array('value' => $i, 'title' => str_pad($i,2,"0",STR_PAD_LEFT) . " x" . " - " .$valorParcela." = ".gCorrigeNumero($valorTotalSelect));
            }
        }
        return $select;
    }

    // CÓDIGOS QUE BUSCA E RETORNA OS SERVIÇOS, NÃO ESTA SENDO MAIS UTILIZADO DEIXADO COMO BACKUP PARA CASO SEJA REATIVADO
// //-------------------------------------------------------------------------------
//         //SELECIONANDO OS SERVIÇOS DESSE PEDIDO PARA CALCULAR O TOTAL
//         $sql = "SELECT T147_Valor_Total FROM T147 where T147_T005_Id='{$T005_Id}'";
//         $T147_sql = mysql_query($sql);

//         $totalServicos=0;
//         // LOOPING PARA CALCULAR O TOTAL DOS SERVIÇOS SOMADO COM TOTAL DO PEDIDO
//         while($T147 = mysql_fetch_array($T147_sql)){
//             $totalServicos+=$T147['T147_Valor_Total'];
//         }

// //----------------------------------------------------------------------------------------------
//         $sql = "SELECT T145_Quantidade_Confirmada,T145_Valor_Unitario FROM T145 where T145_T003_Id='{$T003_Id}'";
//         $T145_sql = mysql_query($sql);

//         // // LOOPING PARA CALCULAR O TOTAL DE SERVIÇOS CONFIRMADOS PARA ESSE ORÇAMENTO
//         $totalServicos=0;
//         while($T145 = mysql_fetch_array($T145_sql)){
//             $totalServicos+=$T145['T145_Quantidade_Confirmada'] * $T145['T145_Valor_Unitario'];
//         }

// //--------------------------------------------------------
//         //SELECIONANDO OS SERVIÇOS DESSE NOTA PARA CALCULAR O TOTAL
//         $sql = "SELECT T203_Valor_Total FROM T203 where T203_T007_Id='{$T007_Id}'";
//         $T203_sql = mysql_query($sql);

//         $totalServicos=0;
//         // LOOPING PARA CALCULAR O TOTAL DOS SERVIÇOS SOMADO COM TOTAL DO PEDIDO
//         while($T203 = mysql_fetch_array($T203_sql)){
//             $totalServicos+=$T203['T203_Valor_Total'];
//         }

//         //SOMANDO O TOTAL DO ORÇ/PED/NOT COM O TOTAL DOS SERVIÇOS
//         $valorTotal = $totalServicos + $sqlQueryResult['valorTotal'];

    /**
     * inserirParcela
     *
     * @param Form $form
     * @return String/bool
     */    
    public function xinserirParcela($form)
    {
        global $g;
        
        $T003_Id        = $form['T003_Id'];
        $intervaloDias  = $form['IntervalorDias'];
        $portador       = $form['D027_Id'];
        $Total_Parcelas = $form['Total_Parcelas'];
        
        $diaFixo                = $form['diaFixo'];
        $primeiroVencimentoNum  = $form['primeiroVencimentoNum'];
        $primeiroVencimentoData = $form['primeiroVencimentoData'];
        
        $primeiroVencimentoNum = empty($primeiroVencimentoNum) ? $g['conf']->camposValorPadrao('primeiroVencimentoNum') : $primeiroVencimentoNum;
        $intervaloDias         = empty($intervaloDias) ? $g['conf']->camposValorPadrao('IntervalorDias') : $intervaloDias;
        
        
        // se foi definido um intervalo de dias
        if ($intervaloDias > 0) {
            $valorNovo  = 0;
            $query      = "SELECT * FROM T003 WHERE T003_Id='{$T003_Id}'";
            $retorno    = mysql_query($query);
            $linha      = mysql_fetch_assoc($retorno);
            $valorTotal = $linha['T003_Valor_Total'];
            
            $query            = "SELECT * FROM T089 WHERE T089_T003_Id='{$T003_Id}' ORDER BY T089_Id DESC";
            $retorno          = mysql_query($query);
            $linha            = mysql_fetch_assoc($retorno);
            $prazoUltima      = $linha['T089_Prazos_Dias'];
            $numeroUltima     = $linha['T089_Numero_Parcela'];
            $numeroNova       = $numeroUltima + 1;
            $vencimentoUltima = $linha['T089_Data_Vencimento'];
            // se a parcela a ser inserida é a primeira, pega a data informada no campo "Primeiro Vencimento"
            // se não, a data é a ultima + intervalo de dias informado.
            if (mysql_num_rows($retorno) == 0) {
                if (trim($primeiroVencimentoNum) != "") {
                    $prazoNovo = $primeiroVencimentoNum;
                } else if (trim($primeiroVencimentoData) != "") {
                    $date1     = new \DateTime(date("Y-m-d"));
                    $date2     = new \DateTime(gCorrigeDataInverte($primeiroVencimentoData));
                    $interval  = $date1->diff($date2);
                    $prazoNovo = $interval->days;
                }
                $valorNovo = $valorTotal;
            } else {
                $prazoNovo = $prazoUltima + $intervaloDias;
                $valorNovo = 0;
            }
            
            $cClausula = "insert into T089 (";
            $cClausula .= "T089_T003_Id,";
            $cClausula .= "T089_D027_Id,";
            $cClausula .= "T089_Numero_Parcela,";
            $cClausula .= "T089_Prazos_Dias,";
            $cClausula .= "T089_Data_Vencimento,";
            $cClausula .= "T089_Valor_Parcela";
            $cClausula .= ") VALUES (";
            $cClausula .= "'$T003_Id',";
            $cClausula .= "'$portador',";
            $cClausula .= "'$numeroNova',";
            $cClausula .= "'$prazoNovo',";
            $cClausula .= "DATE_ADD(current_date(),INTERVAL $prazoNovo DAY),";
            $cClausula .= "'$valorNovo'";
            $cClausula .= ")";
            mysql_query($cClausula);
            $erro = mysql_error();
            // ou se foi colocado um dia fixo   
        } else if ($diaFixo > 0) {
            
            if ($diaFixo > 31)
                return "Por favor, insira um dia válido";
            
            $valorNovo  = 0;
            $query      = "SELECT * FROM T003 WHERE T003_Id='{$T003_Id}'";
            $retorno    = mysql_query($query);
            $linha      = mysql_fetch_assoc($retorno);
            $valorTotal = $linha['T003_Valor_Total'];
            
            $query            = "SELECT * FROM T089 WHERE T089_T003_Id='{$T003_Id}' ORDER BY T089_Id DESC";
            $retorno          = mysql_query($query);
            $linha            = mysql_fetch_assoc($retorno);
            $prazoUltima      = $linha['T089_Prazos_Dias'];
            $numeroUltima     = $linha['T089_Numero_Parcela'];
            $numeroNova       = $numeroUltima + 1;
            $vencimentoUltima = $linha['T089_Data_Vencimento'];
            if (mysql_num_rows($retorno) == 0) {
                if (trim($primeiroVencimentoNum) != "") {
                    $date     = strtotime("+$primeiroVencimentoNum day", strtotime(date("Y-m-d")));
                    $dataNova = date("Y-m-d", $date);
                } else if (trim($primeiroVencimentoData) != "") {
                    $dataNova = gCorrigeDataInverte($primeiroVencimentoData);
                }
                $valorNovo = $valorTotal;
            } else {
                $ano = date("Y", strtotime($vencimentoUltima));
                $mes = date("m", strtotime($vencimentoUltima));
                $mes++;
                if ($mes == 13) {
                    $mes = 1;
                    $ano++;
                }
                $dataSomar = "$ano-$mes-$diaFixo";
                $validou   = checkdate($mes, $diaFixo, $ano);
                while (!$validou) {
                    $diaFixo--;
                    $validou = checkdate($mes, $diaFixo, $ano);
                }
                $dataNova  = "$ano-$mes-$diaFixo";
                $valorNovo = 0;
            }
            
            $cClausula = "insert into T089 (";
            $cClausula .= "T089_T003_Id,";
            $cClausula .= "T089_D027_Id,";
            $cClausula .= "T089_Numero_Parcela,";
            $cClausula .= "T089_Data_Vencimento,";
            $cClausula .= "T089_Prazos_Dias,";
            $cClausula .= "T089_Valor_Parcela";
            $cClausula .= ") VALUES (";
            $cClausula .= "'$T003_Id',";
            $cClausula .= "'$portador',";
            $cClausula .= "'$numeroNova',";
            $cClausula .= "'$dataNova',";
            $cClausula .= "DATEDIFF('$dataNova',current_date()),";
            $cClausula .= "'$valorNovo'";
            $cClausula .= ")";
            mysql_query($cClausula);
            $erro = mysql_error();
        }
        
        if (empty($erro)) {
            return true;
        } else {
            return false;
        }    
    }

    /**
     * inserirParcela
     *
     * @param Form $form
     * @return String/bool
     */    
    public function inserirParcela($dadosTabela)
    {
        global $g;

        $retornoTabela    = mysql_query("SELECT {$dadosTabela['campos']} FROM {$dadosTabela['tabela']} WHERE {$dadosTabela['where']}");
        $linhaTabela      = mysql_fetch_assoc($retornoTabela);

        $retornoPgto    = mysql_query("SELECT {$dadosTabela['camposPgto']} FROM {$dadosTabela['tabelaPgto']} WHERE {$dadosTabela['wherePgto']} ORDER BY {$dadosTabela['tabelaPgto']}_Id DESC");
        $linhaPgto     = mysql_fetch_assoc($retornoPgto);

        $portador       = $linhaTabela['D027_Id'];
        $Total_Parcelas = $linhaTabela['Total_Parcelas'];
        $primeiroVencimentoNum  = $linhaTabela['primeiroVencimentoNum'];
        $primeiroVencimentoData = $linhaTabela['primeiroVencimentoData'];
        $valorTotal = $linhaTabela['valorTotal'];
        $prazoUltima      = $linhaPgto['prazoDias'];
        $numeroUltima     = $linhaPgto['numeroParcela'];
        $numeroNova       = $numeroUltima + 1;
        $vencimentoUltima = $linhaPgto['dataVencimento'];
        $diasVctoTipo           = $linhaTabela['diasVctoTipo'];

        $Campo_Prazos_Dias = "";
        $Campo_Data_Vencimento = "";
        $valorNovo  = 0;
        $IntervalorDias=0;
        $diaFixo=0;
        if($diasVctoTipo=="INTERVALO"){
            $intervaloDias = $linhaTabela['valorIntervaloDiaFixo'];
        }else{
            $diaFixo = $linhaTabela['valorIntervaloDiaFixo'];
        }   

        // se foi definido um intervalo de dias
        if ($intervaloDias > 0) {
            // se a parcela a ser inserida é a primeira, pega a data informada no campo "Primeiro Vencimento"
            // se não, a data é a ultima + intervalo de dias informado.
            if (mysql_num_rows($retornoPgto) == 0) {
                if (trim($primeiroVencimentoNum) != "") {
                    $prazoNovo = $primeiroVencimentoNum;
                } else if (trim($primeiroVencimentoData) != "") {
                    $date1     = new \DateTime(date("Y-m-d"));
                    $date2     = new \DateTime($primeiroVencimentoData);
                    $interval  = $date1->diff($date2);
                    $prazoNovo = $interval->days;
                }
                $valorNovo = $valorTotal;
            } else {
                $prazoNovo = $prazoUltima + $intervaloDias;
                $valorNovo = 0;
            }

            $Campo_Prazos_Dias = "'$prazoNovo',";
            $Campo_Data_Vencimento = "DATE_ADD(current_date(),INTERVAL $prazoNovo DAY),";
            // ou se foi colocado um dia fixo   
        } else if ($diaFixo > 0) {
            
            if ($diaFixo > 31){
                return "Por favor, insira um dia válido";
            }
            
            if (mysql_num_rows($retornoPgto) == 0) {
                if (trim($primeiroVencimentoNum) != "") {
                    $date     = strtotime("+$primeiroVencimentoNum day", strtotime(date("Y-m-d")));
                    $dataNova = date("Y-m-d", $date);
                } else if (trim($primeiroVencimentoData) != "") {
                    $dataNova = $primeiroVencimentoData;
                }
                $valorNovo = $valorTotal;
            } else {
                $ano = date("Y", strtotime($vencimentoUltima));
                $mes = date("m", strtotime($vencimentoUltima));
                $mes++;
                if ($mes == 13) {
                    $mes = 1;
                    $ano++;
                }
                $dataSomar = "$ano-$mes-$diaFixo";
                $validou   = checkdate($mes, $diaFixo, $ano);
                while (!$validou) {
                    $diaFixo--;
                    $validou = checkdate($mes, $diaFixo, $ano);
                }
                $dataNova  = "$ano-$mes-$diaFixo";
                $valorNovo = 0;
            }

            $Campo_Prazos_Dias = "DATEDIFF('$dataNova',current_date()),";
            $Campo_Data_Vencimento = "'$dataNova',";
        }

        $cClausula = "insert into {$dadosTabela['tabelaPgto']} (";
        $cClausula .= "{$dadosTabela['tabelaPgto']}_{$dadosTabela['tabela']}_Id,";
        $cClausula .= "{$dadosTabela['tabelaPgto']}_D027_Id,";
        $cClausula .= "{$dadosTabela['tabelaPgto']}_Numero_Parcela,";
        $cClausula .= "{$dadosTabela['tabelaPgto']}_Data_Vencimento,";
        $cClausula .= "{$dadosTabela['tabelaPgto']}_Prazos_Dias,";
        $cClausula .= "{$dadosTabela['tabelaPgto']}_Valor_Parcela";
        $cClausula .= ") VALUES (";
        $cClausula .= "'{$dadosTabela['tabelaId']}',";
        $cClausula .= "'$portador',";
        $cClausula .= "'$numeroNova',";
        $cClausula .= "$Campo_Data_Vencimento";
        $cClausula .= "$Campo_Prazos_Dias";
        $cClausula .= "'$valorNovo'";
        $cClausula .= ")";
        mysql_query($cClausula);
        $erro = mysql_error();
        log("cClausula:".$cClausula);
        if (empty($erro)) {
            return true;
        } else {
            return $erro;
        }    
    }
    
    /**
     * motivoPedidoLiberacao
     *
     * @param String $T003_Id
     * @param bool $restricaoAtual
     * @return String
     */    
    public function motivoPedidoLiberacao($T003_Id, $restricaoAtual = false)
    {
        global $g;
        $r_acaoId = $T003_Id;
        
        //Calcula Desconto por alçada
        $descontoOrcamento          = $this->calculaPercentualDescontoOrcamento($r_acaoId);
        $descontoVendedor           = gCorrigeNumeroInverte($g['conf']->ini('alcadaDescontoVendedor'));
        $descontoSupervisor         = gCorrigeNumeroInverte($g['conf']->ini('alcadaDescontoSupervisor'));
        //Calcula prazo médio por alçada
        $prazoMedioAlcadaVendedor   = $this->calculaPrazoMedioAlcadaVendedor($r_acaoId);
        $prazoMedioAlcadaSupervisor = $this->calculaPrazoMedioAlcadaSupervisor($r_acaoId);
        $retorno                    = mysql_query("SELECT ifnull(AVG(T089_Prazos_Dias),0.00) as PrazoMedio FROM T089 WHERE T089_T003_Id='{$r_acaoId}'");
        $T003                       = mysql_fetch_array($retorno);
        $retornoT003                = mysql_query("SELECT T003_Valor_Total_Produtos,
                                        T003_Valor_Total_Produtos_Confirmado,
                                        T003_Liberou_Alcada_Valor_Total,
                                        T003_Liberou_Alcada_C007_Id,
                                        T003_Liberou_Alcada_Data,
                                        T003_Liberou_Alcada_Desconto,
                                        T003_Liberou_Alcada_Prazo_Medio 
                                        FROM T003 
                                        WHERE T003_Id='{$r_acaoId}'");
        $mT003                      = mysql_fetch_array($retornoT003);
        
        $prazoMedio         = $T003['PrazoMedio'];
        $retornoSaldoMinimo = $this->verificaFuturoSaldoProduto($r_acaoId, $restricaoAtual);
        
        $totalConfirmado = $mT003['T003_Valor_Total_Produtos_Confirmado'];
        $totalOrcamento  = $mT003['T003_Valor_Total_Produtos'];
        $total           = '';
        if ($totalConfirmado <= 0) {
            $total = $totalOrcamento;
        } else {
            $total = $totalConfirmado;
        }
        
        $motivoPedidoLiberacao = "";
        if ($retornoSaldoMinimo > 0) {
            $motivoPedidoLiberacao .= "*Limite de estoque mínimo ultrapassado.";
        }
        //Verificações de alçada
        if (($descontoOrcamento <= $descontoVendedor) && ($prazoMedio <= $prazoMedioAlcadaVendedor) && ($retornoSaldoMinimo == 0)) {
            
            // NÃO PRECISA DE LIBERAÇÃO DE DESCONTO E PRAZO MÉDIO
            
        } elseif (($descontoOrcamento <= $descontoSupervisor) && ($prazoMedio <= $prazoMedioAlcadaSupervisor)) {
            //Constrói variável motivo liberação
            if ($mT003['T003_Liberou_Alcada_C007_Id'] > 0 && $mT003['T003_Liberou_Alcada_Data'] > 0 && ($mT003['T003_Liberou_Alcada_Desconto'] >= 0 || $mT003['T003_Liberou_Alcada_Prazo_Medio'] >= 0)) {
                
                if ($mT003['T003_Liberou_Alcada_Valor_Total'] != $total || $mT003['T003_Liberou_Alcada_Prazo_Medio'] != $prazoMedio) {
                    
                    if (($descontoOrcamento > $descontoVendedor) && $g['C007']['C007_Flag_Vendedor'] == 'V') {
                        
                        $motivoPedidoLiberacao .= "\n*Desconto usado é maior que o permitido. ";
                        
                    }
                    
                    if (($prazoMedio > $prazoMedioAlcadaVendedor) && $g['C007']['C007_Flag_Vendedor'] == 'V') {
                        
                        $motivoPedidoLiberacao .= "\n*Prazo médio é maior que o permitido. ";
                        
                    }
                }
            }
            // CASO NÃO TENHA ENTRADO NENHUMA VEZ NA LIBERAÇÃO
        } else if (($descontoOrcamento > $descontoVendedor) && $g['C007']['C007_Flag_Vendedor'] == 'V' && $mT003['T003_Liberou_Alcada_Valor_Total'] != $total) {
            
            $motivoPedidoLiberacao .= "\n*Desconto usado é maior que o permitido. ";
            
        } else if (($prazoMedio > $prazoMedioAlcadaVendedor) && $g['C007']['C007_Flag_Vendedor'] == 'V' && $mT003['T003_Liberou_Alcada_Prazo_Medio'] != $prazoMedio) {
            
            $motivoPedidoLiberacao .= "\n*Prazo médio é maior que o permitido. ";
            
        } else {
            //Constrói variável motivo liberação
            if ($mT003['T003_Liberou_Alcada_C007_Id'] > 0 && $mT003['T003_Liberou_Alcada_Data'] > 0 && ($mT003['T003_Liberou_Alcada_Desconto'] >= 0 || $mT003['T003_Liberou_Alcada_Prazo_Medio'] >= 0)) {
                
                if ($mT003['T003_Liberou_Alcada_Valor_Total'] != $total || $mT003['T003_Liberou_Alcada_Prazo_Medio'] != $prazoMedio) {
                    
                    if (($descontoOrcamento > $descontoSupervisor) && $g['C007']['C007_Flag_Vendedor'] != 'D') {
                        
                        $motivoPedidoLiberacao .= "\n*Desconto usado é maior que o permitido. ";
                        
                    }
                    if (($prazoMedio > $prazoMedioAlcadaSupervisor) && $g['C007']['C007_Flag_Vendedor'] != 'D') {
                        
                        $motivoPedidoLiberacao .= "\n*Prazo médio é maior que o permitido. ";
                        
                    }
                }
            }
        }
        return $motivoPedidoLiberacao;
    }
    
    /**
     * calcularParcelaSimulacao
     *
     * @param Form $form
     * @return String
     */     
    public function calcularParcelaSimulacao($form)
    {
        global $g;
        $T003_Id     = $form['T003_Id'];
        $numParcelas = $form['Total_Parcelas'];
        
        if ($numParcelas == "0") {
            return '';
        }
        
        $query      = "SELECT * FROM T003 WHERE T003_Id='{$T003_Id}'";
        $retorno    = mysql_query($query);
        $linha      = mysql_fetch_assoc($retorno);
        $valorTotal = $linha['T003_Valor_Total'];
        
        return gCorrigeNumero($valorTotal / $numParcelas);
    }
    
    /**
     * VEN001atualizarParcelas
     *
     * @param Form $form
     * @return String/bool
     */     
    public function xVEN001atualizarParcelas($form)
    {
        global $g;
    
        $T003_Id                = $form->campoValorEnviado('T003_Id');
        $Total_Parcelas         = $form->campoValorEnviado('Total_Parcelas');
        $IntervalorDias         = $form->campoValorEnviado('IntervalorDias');
        $diaFixo                = $form->campoValorEnviado('diaFixo');
        $primeiroVencimentoNum  = $form->campoValorEnviado('primeiroVencimentoNum');
        $primeiroVencimentoData = $form->campoValorEnviado('primeiroVencimentoData');
        $numeroParcelas         = $form->campoValorEnviado('Total_Parcelas');
        $D027_Id                = $form->campoValorEnviado('Portador_D027_Id');
        $primeiroVencimentoTipo = $form->campoValorEnviado('selecionaPrimeiroVencimento');
        $diasVctoTipo           = $form->campoValorEnviado('selecionaDias');
        $campoUsarFreteParcela  = strtoupper($form->campoValorEnviado('usarFreteParcela'));
        $campoUsarSTParcela     = strtoupper($form->campoValorEnviado('usarSTParcela'));
        $campoUsarIPIParcela    = strtoupper($form->campoValorEnviado('usarIPIParcela'));

        if (($primeiroVencimentoData != "") || (!empty($primeiroVencimentoData))) {
            $primeiroVencmentoValor = $primeiroVencimentoData;

        } else if (($primeiroVencimentoNum != "") || (!empty($primeiroVencimentoNum))) {
            $primeiroVencmentoValor = $primeiroVencimentoNum;
        }else{
            return "Preencha o campo 1º Vencimento.";
        }
        
        if (($IntervalorDias != "") || (!empty($IntervalorDias))) {
            $diasVctoValor = $IntervalorDias;
        } else if (($diaFixo != "") || (!empty($diaFixo))) {
            $diasVctoValor = $diaFixo;
        }
        if ($diasVctoTipo == "I") {
            $diasVctoTipo = "Intervalo";
        } else if ($diasVctoTipo == "F") {
            $diasVctoTipo = "Dia Fixo";
        }
        
        $cSql_T003 = mysql_query("SELECT * FROM T003 WHERE T003_Id='{$T003_Id}'");
        $mT003     = mysql_fetch_array($cSql_T003);
        
        // se for "a combinar" remove as parcelas que ja existem
        if ($numeroParcelas == '0') {
            mysql_query("delete T089 FROM T089 WHERE T089_T003_Id='{$T003_Id}'");
            if (mysql_error()) {
                return mysql_error();
            } else {
                return true;
            }
        }
        
        $cSql_T089 = mysql_query("SELECT * FROM T089 LEFT JOIN D027 ON D027_Id=T089_D027_Id WHERE T089_T003_Id='{$T003_Id}'");
        while ($mT089 = mysql_fetch_array($cSql_T089)) {
            $mPrazoAntigo[$mT089['T089_Numero_Parcela']]    = $mT089['T089_Prazos_Dias'];
            $mPortadorAntigo[$mT089['T089_Numero_Parcela']] = $mT089['T089_D027_Id'];
        }
        
        mysql_query("delete T089 FROM T089 WHERE T089_T003_Id='{$T003_Id}'");
         
        $Valor_Total_IPI       = $mT003['T003_Valor_Total_IPI'];
        $Valor_Total_Orcamento = $mT003['T003_Valor_Total'];
        
        //Global descontinuada 06/01/2015
        //$usarFretePrimeiraParcela = $g['C031']['usarFretePrimeiraParcela'];
        if ($campoUsarFreteParcela == "ON") {
            $Valor_Total_Orcamento = ($Valor_Total_Orcamento - $mT003['T003_Valor_Frete']);
        }
        
        if ($campoUsarSTParcela == "ON") {
            $Valor_Total_Orcamento = ($Valor_Total_Orcamento - $mT003['T003_Valor_ST']);
        }
        
        if ($campoUsarIPIParcela == "ON") {
            $Valor_Total_Orcamento = ($Valor_Total_Orcamento - $mT003['T003_Valor_Total_IPI']);
        }
        
        if ($Total_Parcelas > 0) {
            //$Valor_Titulo_Parcela = ( $Valor_Total_Orcamento - $Valor_Total_IPI ) / $Total_Parcelas;      
            //$Valor_Total_Titulo = ( ( round($Valor_Titulo_Parcela,2) * $Total_Parcelas ) + $Valor_Total_IPI );
            
            // verificar se deve adicionar juro na parcela 
            $C004              = mysql_query("SELECT * FROM C004 WHERE C004_Id='{$g['empresaAtual']}'");
            $mC004             = mysql_fetch_array($C004);
            $tipoJuros         = $mC004['C004_Tipo_Juros'];
            $taxaJuros         = $mC004['C004_Taxa_Juros'];
            $taxaJurosParcela  = 0.00;
            $valorJurosParcela = 0.00;
            $parcelasIsentas   = $mC004['C004_Parcelas_Isentas_Juros'];
            if ($Total_Parcelas > $parcelasIsentas) {
                $taxaJurosParcela = $taxaJuros;
                if ($taxaJuros > 0.00) {
                    if ($tipoJuros == 'S')
                        $Valor_Titulo_Parcela = gCorrigeNumeroInverte(gJurosSimples($Valor_Total_Orcamento, $taxaJuros, $Total_Parcelas));
                    else if ($tipoJuros == 'C')
                        $Valor_Titulo_Parcela = gCorrigeNumeroInverte(gJurosCompostos($Valor_Total_Orcamento, $taxaJuros, $Total_Parcelas));
                    $valorJurosParcela = $Valor_Titulo_Parcela - ($Valor_Total_Orcamento / $Total_Parcelas);
                } else {
                    $Valor_Titulo_Parcela = ($Valor_Total_Orcamento) / $Total_Parcelas;
                }
            } else {
                $Valor_Titulo_Parcela = ($Valor_Total_Orcamento) / $Total_Parcelas;
            }
            
            $Valor_Total_Titulo         = ((round($Valor_Titulo_Parcela, 2) * $Total_Parcelas));
            $Valor_Total_Titulo         = round($Valor_Total_Titulo, 2);
            $Diferenca_Primeira_Parcela = 0; // criado pra evitar erro de undefined no for
            if (($Valor_Total_Titulo != $Valor_Total_Orcamento) && ($Total_Parcelas <= $parcelasIsentas)) {
                $Diferenca_Primeira_Parcela = $Valor_Total_Orcamento - $Valor_Total_Titulo;
            }
        }
        // se foi definido um intervalo de dias
        if ($IntervalorDias > 0) {
            // variavel para guardar a diferença de dias entre o intervalo de dias e o dia da primeira parcela  
            $acrescimo = 0;
            for ($x = 1; $x <= $Total_Parcelas; $x++) {
                if ($x == 1) {
                    //$Valor_Titulo = ($Valor_Titulo_Parcela + $Valor_Total_IPI) + $Diferenca_Primeira_Parcela;
                    $Valor_Titulo = ($Valor_Titulo_Parcela) + $Diferenca_Primeira_Parcela;
                } else {
                    $Valor_Titulo = $Valor_Titulo_Parcela;
                }
                if ($D027_Id > 0) {
                    $mPortadorAntigo[$x] = $D027_Id;
                }
                
                // se for a primeira parcela
                if ($x == 1) {
                    // se o primeiro vencimento foi preenchido em dias, atribui ele
                    if (trim($primeiroVencimentoNum) != "") {
                        $mPrazoAntigo[$x] = $primeiroVencimentoNum;
                        $acrescimo        = $primeiroVencimentoNum;
                        // se o primeiro vencimento foi preenchido com uma data, transforma em intervalo e atribui
                    } else if (trim($primeiroVencimentoData) != "") {
                        $date1            = new \DateTime(date("Y-m-d"));
                        $date2            = new \DateTime(gCorrigeDataInverte($primeiroVencimentoData));
                        $interval         = $date1->diff($date2);
                        $mPrazoAntigo[$x] = $interval->format('%R%a');
                        $acrescimo        = $interval->format('%R%a');
                    }
                } else {
                    $mPrazoAntigo[$x] = (($IntervalorDias * ($x - 1)) + $acrescimo);
                }
                
                if ($x == 1 && $campoUsarFreteParcela == "ON") {
                    $Valor_Titulo += $mT003['T003_Valor_Frete'];
                }
                
                if ($x == 1 && $campoUsarSTParcela == "ON") {
                    $Valor_Titulo += $mT003['T003_Valor_ST'];
                }
                if ($x == 1 && $campoUsarIPIParcela == "ON") {
                    $Valor_Titulo += $mT003['T003_Valor_Total_IPI'];
                }
                
                $cClausula = "insert into T089 (";
                $cClausula .= "T089_T003_Id,";
                $cClausula .= "T089_D027_Id,";
                $cClausula .= "T089_Numero_Parcela,";
                $cClausula .= "T089_Prazos_Dias,";
                $cClausula .= "T089_Data_Vencimento,";
                $cClausula .= "T089_Taxa_Juros,";
                $cClausula .= "T089_Valor_Juros,";
                $cClausula .= "T089_Valor_Parcela";
                $cClausula .= ") VALUES (";
                $cClausula .= "'$T003_Id',";
                $cClausula .= "'$mPortadorAntigo[$x]',";
                $cClausula .= "'$x',";
                $cClausula .= "'$mPrazoAntigo[$x]',";
                $cClausula .= "DATE_ADD(current_date(),INTERVAL $mPrazoAntigo[$x] DAY),";
                $cClausula .= "'$taxaJurosParcela',";
                $cClausula .= "'$valorJurosParcela',";
                $cClausula .= "'$Valor_Titulo'";
                $cClausula .= ")";
                mysql_query($cClausula);
                log("Insert:".$cClausula);
                if (mysql_error()) {
                    return mysql_error();
                }
            }
            // ou se foi colocado um dia fixo   
        } else if ($diaFixo > 0) {
            if ($diaFixo > 31)
                return "Por favor, insira um dia válido";
            $mes = date("m");
            $ano = date("Y");
            
            $diaFixoOriginal = $diaFixo;
            
            $validou = checkdate($mes, $diaFixo, $ano);
            
            while (!$validou) {
                $diaFixo--;
                $validou = checkdate($mes, $diaFixo, $ano);
            }
            $primeiraData = "$ano-$mes-$diaFixo";
            
            for ($x = 1; $x <= $Total_Parcelas; $x++) {
                if ($x == 1) {
                    //$Valor_Titulo = ($Valor_Titulo_Parcela + $Valor_Total_IPI) + $Diferenca_Primeira_Parcela;
                    $Valor_Titulo = ($Valor_Titulo_Parcela) + $Diferenca_Primeira_Parcela;
                } else {
                    $Valor_Titulo = $Valor_Titulo_Parcela;
                }
                if ($D027_Id > 0) {
                    $mPortadorAntigo[$x] = $D027_Id;
                }
                
                $dataVencimento = "0";
                $dataPrazoDias  = date("Y-m-d");
                $numMeses       = $x;
                // se for a primeira parcela
                if ($x == 1) {
                    // se o primeiro vencimento foi preenchido em dias, soma esses dias a data atual
                    if (trim($primeiroVencimentoNum) != "") {
                        $date           = strtotime("+$primeiroVencimentoNum day", strtotime(date("Y-m-d")));
                        $dataVencimento = date("Y-m-d", $date);
                        // se o primeiro vencimento foi preenchido com uma data, atribui
                    } else if (trim($primeiroVencimentoData) != "") {
                        $dataVencimento = gCorrigeDataInverte($primeiroVencimentoData);
                    }
                    $dataPrazoDias = $dataVencimento;

                    // looping feito para quando o dia fixo é menor do que o intervalo dias da primeira parcela e fica no mesmo mes
                    $dataAux       = date("Y-m-d", strtotime("+1 month", strtotime($primeiraData)));
                    $mes1          = date("m", strtotime($dataAux));
                    $mes2          = date("m", strtotime($dataPrazoDias));
                    while ((strtotime($dataPrazoDias) > strtotime($dataAux)) || ($mes1 == $mes2)) {
                        $dataAux      = date("Y-m-d", strtotime("+1 month", strtotime($dataAux)));
                        $mes1         = date("m", strtotime($dataAux));
                        $primeiraData = date("Y-m-d", strtotime("+1 month", strtotime($primeiraData)));
                    }
                    $numMeses = "0";

                    if($campoUsarFreteParcela == "ON") {
                        $Valor_Titulo += $mT003['T003_Valor_Frete'];
                    }
                    
                    if($campoUsarSTParcela == "ON") {
                        $Valor_Titulo += $mT003['T003_Valor_ST'];
                    }
                    
                    if($campoUsarIPIParcela == "ON") {
                        $Valor_Titulo += $mT003['T003_Valor_Total_IPI'];
                    }
                } else {
                    $dataVencimento = $primeiraData;
                    $numMeses       = $x - 1;
                    // calcula a data de vencimento para depois
                    // poder calcular os dias até o vencimento da parcela (T090_Prazos_Dias)                        
                    $tempo          = $numMeses * 30;
                    $tempo          = "+$tempo days";
                    $date           = strtotime($tempo, strtotime($dataVencimento));
                    
                    $diaFixo = $diaFixoOriginal;
                    $validou = checkdate(date("m", $date), $diaFixo, date("Y", $date));
                    while (!$validou) {
                        $diaFixo--;
                        $validou = checkdate(date("m", $date), $diaFixo, date("Y", $date));
                    }
                    
                    $dataPrazoDias = date("Y-m-" . $diaFixo, $date);
                    $numMeses      = $x - 1;
                }
                log("3 - dataPrazoDias:".$dataPrazoDias);
                log("3 - dataVencimento:".$x);

                $cClausula = "insert into T089 (";
                $cClausula .= "T089_T003_Id,";
                $cClausula .= "T089_D027_Id,";
                $cClausula .= "T089_Numero_Parcela,";
                $cClausula .= "T089_Data_Vencimento,";
                $cClausula .= "T089_Prazos_Dias,";
                $cClausula .= "T089_Taxa_Juros,";
                $cClausula .= "T089_Valor_Juros,";
                $cClausula .= "T089_Valor_Parcela";
                $cClausula .= ") VALUES (";
                $cClausula .= "'$T003_Id',";
                $cClausula .= "'$mPortadorAntigo[$x]',";
                $cClausula .= "'$x',";
                $cClausula .= "'$dataPrazoDias',";
                //$cClausula.="DATE_ADD('$dataVencimento',INTERVAL $numMeses MONTH),";
                $cClausula .= "DATEDIFF('$dataPrazoDias',current_date()),";
                log("3 - dataVencimento:".$x);

                $cClausula .= "'$taxaJurosParcela',";
                $cClausula .= "'$valorJurosParcela',";
                $cClausula .= "'$Valor_Titulo'";
                $cClausula .= ")";
                mysql_query($cClausula);
                log("Insert:".$cClausula);

                if (mysql_error()) {
                    return mysql_error();
                }
            }
        }
        
        $sqlT003 = <<<EOT
            UPDATE T003 SET
            T003_Pgto_Parcelas='{$Total_Parcelas}',
            T003_Pgto_Primeiro_Vcto_Tipo='{$primeiroVencimentoTipo}',
            T003_Pgto_Primeiro_Vcto_Valor='{$primeiroVencmentoValor}',
            T003_Pgto_Dias_Vcto_Tipo='{$diasVctoTipo}',
            T003_Pgto_Dias_Vcto_Valor='{$diasVctoValor}',
            T003_Pgto_Portador='{$D027_Id}',
            T003_Pgto_Data=CURRENT_TIMESTAMP(),
            T003_Pgto_C007_Id='{$g['usuarioAtual']}'
            WHERE T003_Id='{$T003_Id}'
EOT;
        mysql_query("call T003_Gravar_Totalizacao_4('{$T003_Id}')");
        $resultT003 = mysql_query($sqlT003);
        if (mysql_error()) {
            return mysql_error();
        }
        return true;
    }

    /**
     * VEN001atualizarParcelas
     *
     * @param Form $form
     * @return String/bool
     */     
    public function xxatualizarParcelas($T003_Id=false)
    {
        global $g;

        $cSql_T003 = mysql_query("SELECT T003_Pgto_Primeiro_Vcto_Tipo,
                                         T003_Pgto_Primeiro_Vcto_Valor,
                                         T003_Pgto_Primeiro_Vcto_Valor_Data,
                                         T003_Pgto_Dias_Vcto_Tipo,
                                         T003_Pgto_Dias_Vcto_Valor,
                                         T003_D027_Id,
                                         T003_Flag_Frete_Primeira_Parcela,
                                         T003_Flag_ST_Primeira_Parcela,
                                         T003_Flag_IPI_Primeira_Parcela,
                                         T003_Pgto_Parcelas,
                                         T003_Valor_Total_IPI,
                                         T003_Valor_Total,
                                         T003_Valor_Frete,
                                         T003_Valor_ST
                                     FROM T003 WHERE T003_Id='{$T003_Id}'");
        $mT003     = mysql_fetch_array($cSql_T003);

        $Total_Parcelas         = $mT003['T003_Pgto_Parcelas'];
        $diasVctoTipo           = $mT003['T003_Pgto_Dias_Vcto_Tipo'];
        $campoUsarFreteParcela  = $mT003['T003_Flag_Frete_Primeira_Parcela'];
        $campoUsarSTParcela     = $mT003['T003_Flag_ST_Primeira_Parcela'];
        $campoUsarIPIParcela    = $mT003['T003_Flag_Frete_Primeira_Parcela'];
        $D027_Id                = $mT003['T003_D027_Id'];
        $primeiroVencimentoNum  = $mT003['T003_Pgto_Primeiro_Vcto_Valor'];
        $primeiroVencimentoData = $mT003['T003_Pgto_Primeiro_Vcto_Valor_Data'];
        $primeiroVencimentoTipo = $mT003['T003_Pgto_Primeiro_Vcto_Tipo'];
        log("buscando daqui");
        $IntervalorDias=0;
        $diaFixo=0;
        if($diasVctoTipo=="INTERVALO"){
            $IntervalorDias         = $mT003['T003_Pgto_Dias_Vcto_Valor'];
        }else{
            $diaFixo                = $mT003['T003_Pgto_Dias_Vcto_Valor'];
        }
        log("alexa - primeiro vencimento data:".$primeiroVencimentoData);
        if (($primeiroVencimentoTipo=="DATA" && $primeiroVencimentoData=="0000-00-00") || ($primeiroVencimentoTipo=="DIAS" && empty($primeiroVencimentoNum))) {
            return "Preencha o campo 1º Vencimento.";
        }
        
        // se for "a combinar" remove as parcelas que ja existem
        if ($numeroParcelas == '0') {
            mysql_query("delete T089 FROM T089 WHERE T089_T003_Id='{$T003_Id}'");
            if (mysql_error()) {
                return mysql_error();
            } else {
                return true;
            }
        }
        
        $cSql_T089 = mysql_query("SELECT * FROM T089 LEFT JOIN D027 ON D027_Id=T089_D027_Id WHERE T089_T003_Id='{$T003_Id}'");
        while ($mT089 = mysql_fetch_array($cSql_T089)) {
            $mPrazoAntigo[$mT089['T089_Numero_Parcela']]    = $mT089['T089_Prazos_Dias'];
            $mPortadorAntigo[$mT089['T089_Numero_Parcela']] = $mT089['T089_D027_Id'];
        }
        
        mysql_query("delete T089 FROM T089 WHERE T089_T003_Id='{$T003_Id}'");
         
        $Valor_Total_IPI       = $mT003['T003_Valor_Total_IPI'];
        $Valor_Total_Orcamento = $mT003['T003_Valor_Total'];
        
        //Global descontinuada 06/01/2015
        //$usarFretePrimeiraParcela = $g['C031']['usarFretePrimeiraParcela'];
        if ($campoUsarFreteParcela == "ON") {
            $Valor_Total_Orcamento = ($Valor_Total_Orcamento - $mT003['T003_Valor_Frete']);
        }
        
        if ($campoUsarSTParcela == "ON") {
            $Valor_Total_Orcamento = ($Valor_Total_Orcamento - $mT003['T003_Valor_ST']);
        }
        
        if ($campoUsarIPIParcela == "ON") {
            $Valor_Total_Orcamento = ($Valor_Total_Orcamento - $mT003['T003_Valor_Total_IPI']);
        }
        
        if ($Total_Parcelas > 0) {
            
            // verificar se deve adicionar juro na parcela 
            $C004              = mysql_query("SELECT * FROM C004 WHERE C004_Id='{$g['empresaAtual']}'");
            $mC004             = mysql_fetch_array($C004);
            $tipoJuros         = $mC004['C004_Tipo_Juros'];
            $taxaJuros         = $mC004['C004_Taxa_Juros'];
            $taxaJurosParcela  = 0.00;
            $valorJurosParcela = 0.00;
            $parcelasIsentas   = $mC004['C004_Parcelas_Isentas_Juros'];
            if ($Total_Parcelas > $parcelasIsentas) {
                $taxaJurosParcela = $taxaJuros;
                if ($taxaJuros > 0.00) {
                    if ($tipoJuros == 'S')
                        $Valor_Titulo_Parcela = gCorrigeNumeroInverte(gJurosSimples($Valor_Total_Orcamento, $taxaJuros, $Total_Parcelas));
                    else if ($tipoJuros == 'C')
                        $Valor_Titulo_Parcela = gCorrigeNumeroInverte(gJurosCompostos($Valor_Total_Orcamento, $taxaJuros, $Total_Parcelas));
                    $valorJurosParcela = $Valor_Titulo_Parcela - ($Valor_Total_Orcamento / $Total_Parcelas);
                } else {
                    $Valor_Titulo_Parcela = ($Valor_Total_Orcamento) / $Total_Parcelas;
                }
            } else {
                $Valor_Titulo_Parcela = ($Valor_Total_Orcamento) / $Total_Parcelas;
            }
            
            $Valor_Total_Titulo         = ((round($Valor_Titulo_Parcela, 2) * $Total_Parcelas));
            $Valor_Total_Titulo         = round($Valor_Total_Titulo, 2);
            $Diferenca_Primeira_Parcela = 0; // criado pra evitar erro de undefined no for
            if (($Valor_Total_Titulo != $Valor_Total_Orcamento) && ($Total_Parcelas <= $parcelasIsentas)) {
                $Diferenca_Primeira_Parcela = $Valor_Total_Orcamento - $Valor_Total_Titulo;
            }
        }
        // se foi definido um intervalo de dias
        if ($IntervalorDias > 0) {
            // variavel para guardar a diferença de dias entre o intervalo de dias e o dia da primeira parcela  
            $acrescimo = 0;
            for ($x = 1; $x <= $Total_Parcelas; $x++) {
                if ($x == 1) {
                    //$Valor_Titulo = ($Valor_Titulo_Parcela + $Valor_Total_IPI) + $Diferenca_Primeira_Parcela;
                    $Valor_Titulo = ($Valor_Titulo_Parcela) + $Diferenca_Primeira_Parcela;
                } else {
                    $Valor_Titulo = $Valor_Titulo_Parcela;
                }
                if ($D027_Id > 0) {
                    $mPortadorAntigo[$x] = $D027_Id;
                }
                
                // se for a primeira parcela
                if ($x == 1) {
                    // se o primeiro vencimento foi preenchido em dias, atribui ele
                    if (trim($primeiroVencimentoNum) != "") {
                        $mPrazoAntigo[$x] = $primeiroVencimentoNum;
                        $acrescimo        = $primeiroVencimentoNum;
                        // se o primeiro vencimento foi preenchido com uma data, transforma em intervalo e atribui
                    } else if (trim($primeiroVencimentoData) != "") {
                        $date1            = new \DateTime(date("Y-m-d"));
                        $date2            = new \DateTime($primeiroVencimentoData);
                        $interval         = $date1->diff($date2);
                        $mPrazoAntigo[$x] = $interval->format('%R%a');
                        $acrescimo        = $interval->format('%R%a');
                    }
                } else {
                    $mPrazoAntigo[$x] = (($IntervalorDias * ($x - 1)) + $acrescimo);
                }
                
                if ($x == 1 && $campoUsarFreteParcela == "ON") {
                    $Valor_Titulo += $mT003['T003_Valor_Frete'];
                }
                
                if ($x == 1 && $campoUsarSTParcela == "ON") {
                    $Valor_Titulo += $mT003['T003_Valor_ST'];
                }
                if ($x == 1 && $campoUsarIPIParcela == "ON") {
                    $Valor_Titulo += $mT003['T003_Valor_Total_IPI'];
                }
                
                $cClausula = "insert into T089 (";
                $cClausula .= "T089_T003_Id,";
                $cClausula .= "T089_D027_Id,";
                $cClausula .= "T089_Numero_Parcela,";
                $cClausula .= "T089_Prazos_Dias,";
                $cClausula .= "T089_Data_Vencimento,";
                $cClausula .= "T089_Taxa_Juros,";
                $cClausula .= "T089_Valor_Juros,";
                $cClausula .= "T089_Valor_Parcela";
                $cClausula .= ") VALUES (";
                $cClausula .= "'$T003_Id',";
                $cClausula .= "'$mPortadorAntigo[$x]',";
                $cClausula .= "'$x',";
                $cClausula .= "'$mPrazoAntigo[$x]',";
                $cClausula .= "DATE_ADD(current_date(),INTERVAL $mPrazoAntigo[$x] DAY),";
                $cClausula .= "'$taxaJurosParcela',";
                $cClausula .= "'$valorJurosParcela',";
                $cClausula .= "'$Valor_Titulo'";
                $cClausula .= ")";
                mysql_query($cClausula);
                log("Insert:".$cClausula);
                if (mysql_error()) {
                    return mysql_error();
                }
            }
            // ou se foi colocado um dia fixo   
        } else if ($diaFixo > 0) {
            if ($diaFixo > 31)
                return "Por favor, insira um dia válido";
            $mes = date("m");
            $ano = date("Y");
            
            $diaFixoOriginal = $diaFixo;
            
            $validou = checkdate($mes, $diaFixo, $ano);
            
            while (!$validou) {
                $diaFixo--;
                $validou = checkdate($mes, $diaFixo, $ano);
            }
            $primeiraData = "$ano-$mes-$diaFixo";
            
            for ($x = 1; $x <= $Total_Parcelas; $x++) {
                if ($x == 1) {
                    //$Valor_Titulo = ($Valor_Titulo_Parcela + $Valor_Total_IPI) + $Diferenca_Primeira_Parcela;
                    $Valor_Titulo = ($Valor_Titulo_Parcela) + $Diferenca_Primeira_Parcela;
                } else {
                    $Valor_Titulo = $Valor_Titulo_Parcela;
                }
                if ($D027_Id > 0) {
                    $mPortadorAntigo[$x] = $D027_Id;
                }
                
                $dataVencimento = "0";
                $dataPrazoDias  = date("Y-m-d");
                $numMeses       = $x;
                // se for a primeira parcela
                if ($x == 1) {
                    // se o primeiro vencimento foi preenchido em dias, soma esses dias a data atual
                    if (trim($primeiroVencimentoNum) != "") {
                        $date           = strtotime("+$primeiroVencimentoNum day", strtotime(date("Y-m-d")));
                        $dataVencimento = date("Y-m-d", $date);
                        // se o primeiro vencimento foi preenchido com uma data, atribui
                    } else if (trim($primeiroVencimentoData) != "") {
                        $dataVencimento = $primeiroVencimentoData;
                    }
                    $dataPrazoDias = $dataVencimento;

                    // looping feito para quando o dia fixo é menor do que o intervalo dias da primeira parcela e fica no mesmo mes
                    $dataAux       = date("Y-m-d", strtotime("+1 month", strtotime($primeiraData)));
                    $mes1          = date("m", strtotime($dataAux));
                    $mes2          = date("m", strtotime($dataPrazoDias));
                    while ((strtotime($dataPrazoDias) > strtotime($dataAux)) || ($mes1 == $mes2)) {
                        $dataAux      = date("Y-m-d", strtotime("+1 month", strtotime($dataAux)));
                        $mes1         = date("m", strtotime($dataAux));
                        $primeiraData = date("Y-m-d", strtotime("+1 month", strtotime($primeiraData)));
                    }
                    $numMeses = "0";

                    if($campoUsarFreteParcela == "ON") {
                        $Valor_Titulo += $mT003['T003_Valor_Frete'];
                    }
                    
                    if($campoUsarSTParcela == "ON") {
                        $Valor_Titulo += $mT003['T003_Valor_ST'];
                    }
                    
                    if($campoUsarIPIParcela == "ON") {
                        $Valor_Titulo += $mT003['T003_Valor_Total_IPI'];
                    }
                } else {
                    $dataVencimento = $primeiraData;
                    $numMeses       = $x - 1;
                    // calcula a data de vencimento para depois
                    // poder calcular os dias até o vencimento da parcela (T090_Prazos_Dias)                        
                    $tempo          = $numMeses * 30;
                    $tempo          = "+$tempo days";
                    $date           = strtotime($tempo, strtotime($dataVencimento));
                    
                    $diaFixo = $diaFixoOriginal;
                    $validou = checkdate(date("m", $date), $diaFixo, date("Y", $date));
                    while (!$validou) {
                        $diaFixo--;
                        $validou = checkdate(date("m", $date), $diaFixo, date("Y", $date));
                    }
                    
                    $dataPrazoDias = date("Y-m-" . $diaFixo, $date);
                    $numMeses      = $x - 1;
                }
                log("3 - dataPrazoDias:".$dataPrazoDias);
                log("3 - dataVencimento:".$x);

                $cClausula = "insert into T089 (";
                $cClausula .= "T089_T003_Id,";
                $cClausula .= "T089_D027_Id,";
                $cClausula .= "T089_Numero_Parcela,";
                $cClausula .= "T089_Data_Vencimento,";
                $cClausula .= "T089_Prazos_Dias,";
                $cClausula .= "T089_Taxa_Juros,";
                $cClausula .= "T089_Valor_Juros,";
                $cClausula .= "T089_Valor_Parcela";
                $cClausula .= ") VALUES (";
                $cClausula .= "'$T003_Id',";
                $cClausula .= "'$mPortadorAntigo[$x]',";
                $cClausula .= "'$x',";
                $cClausula .= "'$dataPrazoDias',";
                //$cClausula.="DATE_ADD('$dataVencimento',INTERVAL $numMeses MONTH),";
                $cClausula .= "DATEDIFF('$dataPrazoDias',current_date()),";
                log("3 - dataVencimento:".$x);

                $cClausula .= "'$taxaJurosParcela',";
                $cClausula .= "'$valorJurosParcela',";
                $cClausula .= "'$Valor_Titulo'";
                $cClausula .= ")";
                mysql_query($cClausula);
                log("Insert:".$cClausula);

                if (mysql_error()) {
                    return mysql_error();
                }
            }
        }
        mysql_query("call T003_Gravar_Totalizacao_4('{$T003_Id}')");
        return true;
    }
    
    /**
     * atualizarParcelas
     *
     * @param array dadosTabela
     * @return String/bool
     */     
    public function atualizarParcelas($dadosTabela)
    {
        global $g;
        $dataEmissaoString = $dadosTabela['tabela'] . "_Data_Emissao";
        $cClausulaSelect = "SELECT {$dadosTabela['campos']}, {$dataEmissaoString} FROM {$dadosTabela['tabela']} WHERE {$dadosTabela['where']}";
        $cSql = mysql_query($cClausulaSelect);
        $sqlQueryResult    = mysql_fetch_array($cSql);

        $Total_Parcelas         = $sqlQueryResult['Total_Parcelas'];
        $diasVctoTipo           = $sqlQueryResult['diasVctoTipo'];
        $campoUsarFreteParcela  = $sqlQueryResult['campoUsarFreteParcela'];
        $campoUsarSTParcela     = $sqlQueryResult['campoUsarSTParcela'];
        $campoUsarIPIParcela    = $sqlQueryResult['campoUsarIPIParcela'];
        $D027_Id                = $sqlQueryResult['D027_Id'];
        $primeiroVencimentoNum  = $sqlQueryResult['primeiroVencimentoNum'];
        $primeiroVencimentoData = $sqlQueryResult['primeiroVencimentoData'];
        $primeiroVencimentoTipo = $sqlQueryResult['primeiroVencimentoTipo'];
        $dataEmissao            = $sqlQueryResult[$dataEmissaoString];
        $valorIntervaloDiaFixo  = $sqlQueryResult['valorIntervaloDiaFixo'];

        if($g['C031']['dataPrimeiroVencimentoPadrao']=="E"){
            $dataCalculo = $dataEmissao;
        }else{
            $dataCalculo = date("Y-m-d");
        }

        // Verifica se o campo "Dias Vencimento" é maior que zero e diferente de vazio
        if (empty($valorIntervaloDiaFixo) && $Total_Parcelas > 1) {
            return "O valor do campo \"Dias Vencimento\" deve ser maior que zero.";
        }

        // Verificando se a data é inferior à data de emissão
        if ($primeiroVencimentoTipo == "DATA" && $primeiroVencimentoData < $dataEmissao) {
            return "A data do 1º vencimento não pode ser menor que a data de emissão.";
        }

        // Verificando se o vencimento é menor que zero
        if ($primeiroVencimentoNum < 0) {
            return "Os dias do 1º vencimento não podem ser negativos.";
        }

        $IntervalorDias=0;
        $diaFixo=0;
        if($diasVctoTipo=="INTERVALO"){
            $IntervalorDias         = $sqlQueryResult['valorIntervaloDiaFixo'];
        }else{
            $diaFixo                = $sqlQueryResult['valorIntervaloDiaFixo'];
        }

        if (($primeiroVencimentoTipo=="DATA" && $primeiroVencimentoData=="0000-00-00") || ($primeiroVencimentoTipo=="DIAS" && $primeiroVencimentoNum == "")) {
            return "Preencha o campo 1º Vencimento.";
        }

        if ($Total_Parcelas == 1) {
            $diaFixo = 1;
        }

        // se for "a combinar" remove as parcelas que ja existem
        if ($Total_Parcelas == '0') {
            mysql_query("DELETE {$dadosTabela['tabelaPgto']} FROM {$dadosTabela['tabelaPgto']} WHERE {$dadosTabela['wherePgto']}");
            if (mysql_error()) {
                return mysql_error();
            } else {
                return true;
            }
        }
        
        //PEGANDO OS VALORES ANTERIORES DAS PARCELAS
        $cSqlPgto = mysql_query("SELECT {$dadosTabela['camposPgto']}  FROM {$dadosTabela['tabelaPgto']} LEFT JOIN D027 ON D027_Id={$dadosTabela['tabelaPgto']}_D027_Id WHERE {$dadosTabela['wherePgto']}");
        while ($sqlQueryPgtoResult = mysql_fetch_array($cSqlPgto)) {
            $mPrazoAntigo[$sqlQueryPgtoResult['numeroParcela']]    = $sqlQueryPgtoResult['T089_Prazos_Dias'];
            $mPortadorAntigo[$sqlQueryPgtoResult['numeroParcela']] = $sqlQueryPgtoResult['T089_D027_Id'];
        }
        
        //DELETANDO AS PARCELAS ATUAIS
        mysql_query("DELETE {$dadosTabela['tabelaPgto']} FROM {$dadosTabela['tabelaPgto']} WHERE {$dadosTabela['wherePgto']}");
         
        $Valor_Total_IPI       = $sqlQueryResult['valorTotalIPI'];
        $Valor_Total_Orcamento = $sqlQueryResult['valorTotal'];
        
        //CALCULO DO IP, FRETE OU ST NA PRIMEIRA PARCELA
        if ($campoUsarFreteParcela == "S") {
            $Valor_Total_Orcamento = ($Valor_Total_Orcamento - $sqlQueryResult['valorTotalFrete']);
        }
        
        if ($campoUsarSTParcela == "S") {
            $Valor_Total_Orcamento = ($Valor_Total_Orcamento - $sqlQueryResult['valorTotalST']);
        }
        
        if ($campoUsarIPIParcela == "S") {
            $Valor_Total_Orcamento = ($Valor_Total_Orcamento - $sqlQueryResult['valorTotalIPI']);
        }

        //SE NA COMBO FOR SELECIONADA MAIS DE UMA PARCELA
        if ($Total_Parcelas > 0) {            
            // verificar se deve adicionar juro na parcela 
            $C004              = mysql_query("SELECT C004_Tipo_Juros,C004_Taxa_Juros,C004_Parcelas_Isentas_Juros FROM C004 WHERE C004_Id='{$g['empresaAtual']}'");
            $mC004             = mysql_fetch_array($C004);
            $tipoJuros         = $mC004['C004_Tipo_Juros'];
            $taxaJuros         = $mC004['C004_Taxa_Juros'];
            $taxaJurosParcela  = 0.00;
            $valorJurosParcela = 0.00;
            $parcelasIsentas   = $mC004['C004_Parcelas_Isentas_Juros'];
            if ($Total_Parcelas > $parcelasIsentas) {
                $taxaJurosParcela = $taxaJuros;
                if ($taxaJuros > 0.00) {
                    if ($tipoJuros == 'S')
                        $Valor_Titulo_Parcela = gCorrigeNumeroInverte(gJurosSimples($Valor_Total_Orcamento, $taxaJuros, $Total_Parcelas));
                    else if ($tipoJuros == 'C')
                        $Valor_Titulo_Parcela = gCorrigeNumeroInverte(gJurosCompostos($Valor_Total_Orcamento, $taxaJuros, $Total_Parcelas));
                    $valorJurosParcela = $Valor_Titulo_Parcela - ($Valor_Total_Orcamento / $Total_Parcelas);
                } else {
                    $Valor_Titulo_Parcela = ($Valor_Total_Orcamento) / $Total_Parcelas;
                }
            } else {
                $Valor_Titulo_Parcela = ($Valor_Total_Orcamento) / $Total_Parcelas;
            }
            
            $Valor_Total_Titulo         = ((round($Valor_Titulo_Parcela, 2) * $Total_Parcelas));
            $Valor_Total_Titulo         = round($Valor_Total_Titulo, 2);
            $Diferenca_Primeira_Parcela = 0; // criado pra evitar erro de undefined no for
            if (($Valor_Total_Titulo != $Valor_Total_Orcamento) && ($Total_Parcelas <= $parcelasIsentas || $taxaJuros <= 0.00)) {
                $Diferenca_Primeira_Parcela = $Valor_Total_Orcamento - $Valor_Total_Titulo;
            }
        }

        $acrescimo = 0;
        $Campo_Data_Vencimento = "";
        $Campo_Prazos_Dias = "";
        //INICIO DO LAÇO FOR QUE CALCULA E INSERE CADA PARCELA
        for ($x = 1; $x <= $Total_Parcelas; $x++) {
            //Alimentando o portador
            if ($D027_Id > 0) {
                $mPortadorAntigo[$x] = $D027_Id;
            }

           if ($diaFixo > 0) {
                if ($diaFixo > 31){ return "Por favor, insira um dia válido"; }
               
                $mes = date("m");
                $ano = date("Y");
                
                $diaFixoOriginal = $diaFixo;
                
                $validou = checkdate($mes, $diaFixo, $ano);
                
                while (!$validou) {
                    $diaFixo--;
                    $validou = checkdate($mes, $diaFixo, $ano);
                }
                $dataVencimento = "0";
                $dataPrazoDias  = date("Y-m-d");
                $numMeses       = $x;
            }

            //VERIFICANDO SE É A PRIMEIRA PARCELA
            if ($x == 1) {
                //VERIFICA SE É DENTRO DA PRIMEIRA PARCELA ESTA SETADO COM INTERVALO DIAS
                if ($IntervalorDias > 0) {
                    $Valor_Titulo = ($Valor_Titulo_Parcela) + $Diferenca_Primeira_Parcela;
                    // SE O PRIMEIRO VENCIMENTO FOI PREENCHIDO EM DIAS, ATRIBUI ELE
                    if (trim($primeiroVencimentoNum) != "") {
                        $mPrazoAntigo[$x] = $primeiroVencimentoNum;
                        $acrescimo        = $primeiroVencimentoNum;
                        // SE O PRIMEIRO VENCIMENTO FOI PREENCHIDO COM UMA DATA, TRANSFORMA EM INTERVALO E ATRIBUI
                    } else if (trim($primeiroVencimentoData) != "") {
                        $date1            = new \DateTime($dataCalculo);
                        $date2            = new \DateTime($primeiroVencimentoData);
                        $interval         = $date1->diff($date2);
                        $mPrazoAntigo[$x] = $interval->format('%R%a');
                        $acrescimo        = $interval->format('%R%a');
                    }
                    $Campo_Prazos_Dias = "'$mPrazoAntigo[$x]'";
                    $Campo_Data_Vencimento = "DATE_ADD('$dataCalculo',INTERVAL $mPrazoAntigo[$x] DAY)";
                //VERIFICA SE É DENTRO DA PRIMEIRA PARCELA ESTA SETADO COM DIA FIXO
                } else if ($diaFixo > 0) {
                    $Valor_Titulo = ($Valor_Titulo_Parcela) + $Diferenca_Primeira_Parcela;

                    // SE O PRIMEIRO VENCIMENTO FOI PREENCHIDO EM DIAS, SOMA ESSES DIAS A DATA ATUAL
                    if (trim($primeiroVencimentoNum) != "") {
                        $date           = strtotime("+$primeiroVencimentoNum day", strtotime($dataCalculo));
                        $dataVencimento = date("Y-m-d", $date);
                        // se o primeiro vencimento foi preenchido com uma data, atribui
                    } else if (trim($primeiroVencimentoData) != "") {
                        $dataVencimento = $primeiroVencimentoData;
                    }
                    $dataPrazoDias = $dataVencimento;

                    // LOOPING FEITO PARA QUANDO O DIA FIXO É MENOR DO QUE O INTERVALO DIAS DA PRIMEIRA PARCELA E FICA NO MESMO MES
                    $dataAux       = date("Y-m-d", strtotime("+1 month", strtotime($primeiraData)));
                    $mes1          = date("m", strtotime($dataAux));
                    $mes2          = date("m", strtotime($dataPrazoDias));
                    while ((strtotime($dataPrazoDias) > strtotime($dataAux)) || ($mes1 == $mes2)) {
                        $dataAux      = date("Y-m-d", strtotime("+1 month", strtotime($dataAux)));
                        $mes1         = date("m", strtotime($dataAux));
                        $primeiraData = date("Y-m-d", strtotime("+1 month", strtotime($primeiraData)));
                    }
                    $numMeses = "0";
                    $Campo_Prazos_Dias = "DATEDIFF('$dataPrazoDias','$dataCalculo')";
                    $Campo_Data_Vencimento = "'$dataPrazoDias'";
                }

                //CALCULOS REALIZADOS CASO SEJA A PRIMEIRA PARCELA E AS FLAG ESTEJAM PREENCHIDAS COM S
                if($campoUsarFreteParcela == "S") {
                    $Valor_Titulo += $sqlQueryResult['valorTotalFrete'];
                }
                
                if($campoUsarSTParcela == "S") {
                    $Valor_Titulo += $sqlQueryResult['valorTotalST'];
                }
                
                if($campoUsarIPIParcela == "S") {
                    $Valor_Titulo += $sqlQueryResult['valorTotalIPI'];
                }

            //VERIFICANDO SE NÃO É A PRIMEIRA PARCELA 
            } else {
                //VERIFICANDO SE NAS OUTRAS PARCELAS ESTA SETADO INTERVALO
                if ($IntervalorDias > 0) {
                    $Valor_Titulo = $Valor_Titulo_Parcela;
                    $mPrazoAntigo[$x] = (($IntervalorDias * ($x - 1)) + $acrescimo);
                    $Campo_Prazos_Dias = "'$mPrazoAntigo[$x]'";
                    $Campo_Data_Vencimento = "DATE_ADD('$dataCalculo',INTERVAL $mPrazoAntigo[$x] DAY)";
                } else if ($diaFixo > 0) {
                    $Valor_Titulo = $Valor_Titulo_Parcela;

                    $dataVencimento = $primeiraData;
                    $numMeses       = $x - 1;
                    // calcula a data de vencimento para depois
                    // poder calcular os dias até o vencimento da parcela (T090_Prazos_Dias)                        
                    $tempo          = $numMeses * 30;
                    $tempo          = "+$tempo days";
                    $date           = strtotime($tempo, strtotime($dataVencimento));
                    
                    $diaFixo = $diaFixoOriginal;
                    $validou = checkdate(date("m", $date), $diaFixo, date("Y", $date));
                    while (!$validou) {
                        $diaFixo--;
                        $validou = checkdate(date("m", $date), $diaFixo, date("Y", $date));
                    }
                    
                    $dataPrazoDias = date("Y-m-" . $diaFixo, $date);
                    $numMeses      = $x - 1;

                    $Campo_Prazos_Dias = "DATEDIFF('$dataPrazoDias','$dataCalculo')";
                    $Campo_Data_Vencimento = "'$dataPrazoDias'";
                }
            }

            $cClausula = "insert into {$dadosTabela['tabelaPgto']} (";
            $cClausula .= "{$dadosTabela['tabelaPgto']}_{$dadosTabela['tabela']}_Id,";
            $cClausula .= "{$dadosTabela['tabelaPgto']}_D027_Id,";
            $cClausula .= "{$dadosTabela['tabelaPgto']}_Numero_Parcela,";
            $cClausula .= "{$dadosTabela['tabelaPgto']}_Prazos_Dias,";
            $cClausula .= "{$dadosTabela['tabelaPgto']}_Data_Vencimento,";
            $cClausula .= "{$dadosTabela['tabelaPgto']}_Taxa_Juros,";
            $cClausula .= "{$dadosTabela['tabelaPgto']}_Valor_Juros,";
            $cClausula .= "{$dadosTabela['tabelaPgto']}_Valor_Parcela";
            $cClausula .= ") VALUES (";
            $cClausula .= "'{$dadosTabela['tabelaId']}',";
            $cClausula .= "'$mPortadorAntigo[$x]',";
            $cClausula .= "'$x',";
            $cClausula .= "$Campo_Prazos_Dias,";
            $cClausula .= "$Campo_Data_Vencimento,";
            $cClausula .= "'$taxaJurosParcela',";
            $cClausula .= "'$valorJurosParcela',";
            $cClausula .= "'$Valor_Titulo'";
            $cClausula .= ")";

            mysql_query($cClausula);
            log("Insert:".$cClausula);
            if (mysql_error()) {
                return mysql_error();
            }
        }
        return true;
    }

    /**
     * ven001AtualizarParcelasGrid
     *
     * @param String $T089_Id
     * @param String $campoEditado
     * @param String $valorAntigo
     * @param String $novoValor
     * @return String/bool
     */    
    //NÃO SERÁ MAIS UTILIZADO, SERÁ SEPARADO EM OUTRAS FUNÇÕES 
    public function xven001AtualizarParcelasGrid($T089_Id, $campoEditado, $valorAntigo, $novoValor)
    {
        
        $query            = "SELECT * FROM T089 LEFT JOIN T003 ON T089_T003_Id='{$T089_Id}' WHERE T089_Id='{$T089_Id}'";
        $retorno          = mysql_query($query);
        $linha            = mysql_fetch_assoc($retorno);
        $dataVelhaT003    = $linha['T003_Data_Emissao'];
        $prazosDiasAntigo = $linha['T089_Prazos_Dias'];
        
        if ($campoEditado == 'T089_Prazos_Dias') {
            // if($valorAntigo == $novoValor){
            // return "O prazo é o mesmo";
            // }
            
            $date = new \DateTime($dataVelhaT003);
            $date->modify('+' . $novoValor . ' days');
            $query = "UPDATE T089 SET T089_Data_Vencimento='{$date->format('Y-m-d')}' WHERE T089_Id='{$T089_Id}'";
            mysql_query($query);
            
        } else if ($campoEditado == 'T089_Data_Vencimento') {
            
            $dataAntiga = gCorrigeDataInverte($valorAntigo);
            $dataNova   = gCorrigeDataInverte($novoValor);
            
            $timeStampDataNova   = strtotime($dataNova);
            $timeStampDataAntiga = strtotime($dataAntiga);
            
            
            if ($timeStampDataAntiga == $timeStampDataNova) {
                return 'Datas Iguais';
            } else if ($timeStampDataAntiga < $timeStampDataNova) {
                $novaData = ($timeStampDataNova - $timeStampDataAntiga);
                $dias     = date('d', $novaData);
                
                $total = ($prazosDiasAntigo + $dias);
                $query = "UPDATE T089 SET T089_Prazos_Dias='{$total}' WHERE T089_Id='{$T089_Id}'";
                mysql_query($query);
                return true;
            } else if ($timeStampDataAntiga > $timeStampDataNova) {
                $novaData = ($timeStampDataAntiga - $timeStampDataNova);
                $dias     = date('d', $novaData);
                $total    = ($prazosDiasAntigo - $dias);
                $query    = "UPDATE T089 SET T089_Prazos_Dias='{$total}' WHERE T089_Id='{$T089_Id}'";
                mysql_query($query);
                return true;
            }
            
        } else if ($campoEditado == 'T089_Valor_Parcela') {
            
            $query      = "SELECT * FROM T089 WHERE T089_Id='{$T089_Id}'";
            $retorno    = mysql_query($query);
            $linha      = mysql_fetch_assoc($retorno);
            $T003_Id    = $linha['T089_T003_Id'];
            $numParcela = $linha['T089_Numero_Parcela'];
            
            /* total antigo, pegando o valor do orçamento
            $query = "SELECT T003_Valor_Total FROM T003 WHERE T003_Id='{$T003_Id}'";
            $retorno=mysql_query($query);       
            $linha = mysql_fetch_assoc($retorno);   
            $valorTotal=$linha['T003_Valor_Total'];
            */

            // pega o valor total com juros (se houver)
            // sem a parcela alterada, e no fim soma com o valor antigo, para pegar o valor total com juros original
            $query      = "SELECT SUM(T089_Valor_Parcela) FROM T089 WHERE T089_T003_Id='{$T003_Id}' AND T089_Id!='{$T089_Id}'";
            $retorno    = mysql_query($query);
            $linha      = mysql_fetch_assoc($retorno);
            $valorTotal = $linha['SUM(T089_Valor_Parcela)'] + gCorrigeNumeroInverte($valorAntigo);
            
            $query                  = "SELECT SUM(T089_Valor_Parcela) FROM T089 WHERE T089_T003_Id='{$T003_Id}' AND T089_Numero_Parcela<='{$numParcela}'";
            $retorno                = mysql_query($query);
            $linha                  = mysql_fetch_assoc($retorno);
            $valorParcelasRestantes = $linha['SUM(T089_Valor_Parcela)'];
            $valorRestante          = $valorTotal - $valorParcelasRestantes;
            
            $query             = "SELECT * FROM T089 WHERE T089_T003_Id='{$T003_Id}' AND T089_Numero_Parcela>'{$numParcela}'";
            $retorno           = mysql_query($query);
            $parcelasRestantes = mysql_num_rows($retorno);
            //se ainda tiverem parcelas depois da editada, divide o valor restante entre elas
            if ($parcelasRestantes != 0) {
                $valorParcelasRestantes = $valorRestante / $parcelasRestantes;
                if ($valorParcelasRestantes < 0) {
                    $query = "UPDATE T089 SET T089_Valor_Parcela='{$valorAntigo}' WHERE T089_Id='{$T089_Id}'";
                    mysql_query($query);
                    return 'O valor inserido irá gerar parcelas negativas... Favor alterar novamente';
                }
                
                while ($linha = mysql_fetch_assoc($retorno)) {
                    $query = "UPDATE T089 SET T089_Valor_Parcela='{$valorParcelasRestantes}' WHERE T089_Id='{$linha['T089_Id']}'";
                    mysql_query($query);
                }
            }
            return true;    
        }
        return true;
    }

    /**
     * atualizarPrazoGridPagamento
     * @param Array $dadosTabela
     */    
    public function atualizarPrazoGridPagamento($dadosTabela)
    {        
        $retorno          = mysql_query("SELECT {$dadosTabela['campos']} FROM {$dadosTabela['tabela']} LEFT JOIN {$dadosTabela['tabelaPai']} ON {$dadosTabela['tabela']}_{$dadosTabela['tabelaPai']}_Id={$dadosTabela['tabelaPai']}_Id WHERE {$dadosTabela['where']}");
        $linha            = mysql_fetch_assoc($retorno);
        $dataVelha        = $linha['dataEmissao'];
        $numeroParcela = $linha['numeroParcela'];
        $tabelaPaiId = $linha['tabelaPaiId'];

        $retorno = mysql_query("SELECT {$dadosTabela['tabela']}_Id,{$dadosTabela['tabela']}_Prazos_Dias
                                  FROM {$dadosTabela['tabela']} 
                                 WHERE {$dadosTabela['tabela']}_{$dadosTabela['tabelaPai']}_Id='$tabelaPaiId' 
                                   AND (({$dadosTabela['tabela']}_Prazos_Dias                 >'{$dadosTabela['novoValor']}' 
                                   AND {$dadosTabela['tabela']}_Numero_Parcela                <'{$numeroParcela}')  
                                    OR ({$dadosTabela['tabela']}_Prazos_Dias                  <'{$dadosTabela['novoValor']}' 
                                   AND {$dadosTabela['tabela']}_Numero_Parcela                >'{$numeroParcela}'))");
        $numRows   = mysql_num_rows($retorno);
    
        // Verificar valores negativos na coluna prazo
        if ($dadosTabela['novoValor'] < 0) {
            mysql_query("UPDATE {$dadosTabela['tabela']} SET {$dadosTabela['tabela']}_Prazos_Dias='{$dadosTabela['valorAntigo']}' WHERE {$dadosTabela['where']}");
            return 'O prazo digitado não pode ser negativo.';
        } else if ($numRows > 0) {
            //UPDATE REALIZADO PARA CASO DE ERRO RETORNAR AO VALOR ANTERIOR AO DIGITADO
            //NECESSÁRIO POIS O COLUNAEDITAR NO GRID MUDA O VALOR DO CAMPO ANTES DE CHAMAR O CALLBACK
            mysql_query("UPDATE {$dadosTabela['tabela']} SET {$dadosTabela['tabela']}_Prazos_Dias='{$dadosTabela['valorAntigo']}' WHERE {$dadosTabela['where']}");
            return 'Prazo digitado não pode ser menor que os prazos anteriores e nem maior que prazos posteriores.';
        }else{
            $date = new \DateTime($dataVelha);
            $data = $date->modify('+' . $dadosTabela['novoValor'] . ' days');

            $query = "UPDATE {$dadosTabela['tabela']} SET {$dadosTabela['tabela']}_Data_Vencimento='{$date->format('Y-m-d')}' WHERE {$dadosTabela['where']}";
            mysql_query($query);  
            
            return true;
        }
    }

    /**
     * atualizarVencimentoGridPagamento
     * @param Array $dadosTabela
     */    
    public function atualizarVencimentoGridPagamento($dadosTabela)
    { 
        $retorno          = mysql_query("SELECT {$dadosTabela['campos']} FROM {$dadosTabela['tabela']} LEFT JOIN {$dadosTabela['tabelaPai']} ON {$dadosTabela['tabela']}_{$dadosTabela['tabelaPai']}_Id={$dadosTabela['tabelaPai']}_Id WHERE {$dadosTabela['where']}");
        $linha            = mysql_fetch_assoc($retorno);
        $prazosDiasAntigo = $linha['prazoDias'];
        $tabelaPaiId = $linha['tabelaPaiId'];
        $numeroParcela = $linha['numeroParcela'];

        $dataAntiga = gCorrigeDataInverte($dadosTabela['valorAntigo']);
        $dataNova   = gCorrigeDataInverte($dadosTabela['novoValor']);
        $dataEmissao = $linha['dataEmissao'];

        $retorno = mysql_query("SELECT {$dadosTabela['tabela']}_Id,{$dadosTabela['tabela']}_Data_Vencimento 
                                  FROM {$dadosTabela['tabela']} 
                                 WHERE {$dadosTabela['tabela']}_{$dadosTabela['tabelaPai']}_Id='$tabelaPaiId' 
                                   AND (({$dadosTabela['tabela']}_Data_Vencimento             >'$dataNova' 
                                   AND {$dadosTabela['tabela']}_Numero_Parcela                <'{$numeroParcela}')  
                                    OR ({$dadosTabela['tabela']}_Data_Vencimento              <'$dataNova' 
                                   AND {$dadosTabela['tabela']}_Numero_Parcela                >'{$numeroParcela}'))");

        $numRows   = mysql_num_rows($retorno);
        
        // To check if the first parcel date is less than the issue date
        if ($dataNova < $dataEmissao) {
            mysql_query("UPDATE {$dadosTabela['tabela']} SET {$dadosTabela['tabela']}_Data_Vencimento='{$dataAntiga}' WHERE {$dadosTabela['where']}");
            return 'Data selecionada não pode ser menor que a data de emissão.';
        } elseif ($numRows > 0) {
            //UPDATE REALIZADO PARA CASO DE ERRO RETORNAR AO VALOR ANTERIOR AO DIGITADO
            //NECESSÁRIO POIS O COLUNAEDITAR NO GRID MUDA O VALOR DO CAMPO ANTES DE CHAMAR O CALLBACK
            log("UPDATE {$dadosTabela['tabela']} SET {$dadosTabela['tabela']}_Data_Vencimento='{$dataAntiga}' WHERE {$dadosTabela['where']}");
            mysql_query("UPDATE {$dadosTabela['tabela']} SET {$dadosTabela['tabela']}_Data_Vencimento='{$dataAntiga}' WHERE {$dadosTabela['where']}");
            return 'Data selecionada não pode ser menor que as datas anteriores e nem maior que as datas posteriores.';
        } else {

            $timeStampDataNova   = strtotime($dataNova);
            $timeStampDataAntiga = strtotime($dataAntiga);

            if ($timeStampDataAntiga < $timeStampDataNova) {
                $novaData = ($timeStampDataNova - $timeStampDataAntiga);
                $dias     = date('d', $novaData);
                $total = ($prazosDiasAntigo + $dias);
            } else if ($timeStampDataAntiga > $timeStampDataNova) {
                $novaData = ($timeStampDataAntiga - $timeStampDataNova);
                $dias     = date('d', $novaData);
                $total    = ($prazosDiasAntigo - $dias);
            }
            mysql_query("UPDATE {$dadosTabela['tabela']} SET {$dadosTabela['tabela']}_Prazos_Dias='{$total}' WHERE {$dadosTabela['where']}");
            return true;
        } 
    }

   /**
     * atualizarValorParcelaGridPagamento
     * @param Array $dadosTabela
     */  
    public function atualizarValorParcelaGridPagamento($dadosTabela)
    {        
        $retorno     = mysql_query("SELECT {$dadosTabela['campos']} FROM {$dadosTabela['tabela']} LEFT JOIN {$dadosTabela['tabelaPai']} ON {$dadosTabela['tabela']}_{$dadosTabela['tabelaPai']}_Id={$dadosTabela['tabelaPai']}_Id WHERE {$dadosTabela['where']}");
        $linha       = mysql_fetch_assoc($retorno);
        $tabelaPaiId = $linha['tabelaPaiId'];
        $numParcela  = $linha['numeroParcela'];
        
        // Somando valor de todas as parcelas não sendo a parcela atualizada
        $retorno    = mysql_query("SELECT SUM({$dadosTabela['tabela']}_Valor_Parcela) as valorParcela FROM {$dadosTabela['tabela']} WHERE {$dadosTabela['tabela']}_{$dadosTabela['tabelaPai']}_Id='{$tabelaPaiId}' AND {$dadosTabela['tabela']}_Id!='{$dadosTabela['tabelaId']}'");
        $linha      = mysql_fetch_assoc($retorno);
        $valorTotal = $linha['valorParcela'] + gCorrigeNumeroInverte($dadosTabela['valorAntigo']);

        // Somando valor da parcela atualizada e anteriores
        $retorno                = mysql_query("SELECT SUM({$dadosTabela['tabela']}_Valor_Parcela) as valorParcelasRestantes FROM {$dadosTabela['tabela']} WHERE {$dadosTabela['tabela']}_{$dadosTabela['tabelaPai']}_Id='{$tabelaPaiId}' AND {$dadosTabela['tabela']}_Numero_Parcela<='{$numParcela}'");
        $linha                  = mysql_fetch_assoc($retorno);
        $valorParcelasRestantes = $linha['valorParcelasRestantes'];
        $valorRestante          = $valorTotal - $valorParcelasRestantes;
        
        $retorno           = mysql_query("SELECT {$dadosTabela['tabela']}_Id as idParcela FROM {$dadosTabela['tabela']} WHERE {$dadosTabela['tabela']}_{$dadosTabela['tabelaPai']}_Id='{$tabelaPaiId}' AND {$dadosTabela['tabela']}_Numero_Parcela>'{$numParcela}'");
        $parcelasRestantes = mysql_num_rows($retorno);

        //se ainda tiverem parcelas depois da editada, divide o valor restante entre elas
        if ($parcelasRestantes != 0) {
            $valorParcelasRestantes = round($valorRestante / $parcelasRestantes,2);

            // Verifica se tem difença a mais ou menos de valor nas parcelas restantes 
            $verificaParcelasRestantes = $valorParcelasRestantes * $parcelasRestantes;
            $verificaDiferenca         = $valorRestante - $verificaParcelasRestantes;

            if ($valorParcelasRestantes < 0) {
                $query = "UPDATE {$dadosTabela['tabela']} SET {$dadosTabela['tabela']}_Valor_Parcela='{$valorAntigo}' WHERE {$dadosTabela['tabela']}_Id='{$dadosTabela['tabelaId']}'";
                mysql_query($query);
                return 'O valor inserido irá gerar parcelas negativas... Favor alterar novamente';
            }
            
            while ($linha = mysql_fetch_assoc($retorno)) {
                $valorParcela = $valorParcelasRestantes;
                if($verificaDiferenca != 0){
                    $valorParcela += $verificaDiferenca;
                    $verificaDiferenca = 0;
                }
                $query = "UPDATE {$dadosTabela['tabela']} SET {$dadosTabela['tabela']}_Valor_Parcela='{$valorParcela}' WHERE {$dadosTabela['tabela']}_Id='{$linha['idParcela']}'";
                mysql_query($query);
            }
        }
        return true;    
    }

    
    /**
     * gravarDesconto
     *
     * @param String $T003_Id
     * @param String $T003_Valor_Desconto
     * @param String $T003_Valor_Desconto_Confirmado
     * @param String $Campo
     * @return String/bool
     */     
    public function gravarDesconto($T003_Id, $T003_Valor_Desconto, $T003_Valor_Desconto_Confirmado, $Campo)
    { 
        require_once "old/lib/funcoes.php";
        
        if (empty($T003_Id)) {
            return "Nao encontrado o T003_Id.";
        }
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        $sql = <<<EOT
            SELECT sum(T004_Valor_Total_Preco_Sem_Desconto) as Total_Sem_Desconto,
            sum(T004_Valor_Total_Preco_Sem_Desconto_Confirmado) as Total_Sem_Desconto_Confirmado
            FROM T004 WHERE T004_T003_Id='$T003_Id' AND T004_Flag_Preco_Especial!='O'
EOT;
        $cSql_T004 = mysql_query($sql);
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return '1. Erro: ' . mysql_error();
        }
        $mT004 = mysql_fetch_array($cSql_T004);
        if ($Campo == 'T003_Percentual_Desconto') {
            $Valor = formata_valor_gravar($Valor, 5);
            $sql   = <<<EOT
                "UPDATE T003 
                 SET T003_Percentual_Desconto='$Valor',
                        T003_Percentual_Desconto_Confirmado='$Valor',
                        T003_Valor_Desconto=(T003_Percentual_Desconto/100)*{$mT004['Total_Sem_Desconto']}
                  WHERE T003_Id='$T003_Id'"
EOT;
            mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return '2. Erro: ' . mysql_error();
            }
        } else if ($Campo == 'T003_Valor_Desconto') {
            $T003_Valor_Desconto = formata_valor_gravar($T003_Valor_Desconto, 3);
            $sql = <<<EOT
               UPDATE T003 
               SET T003_Valor_Desconto='$T003_Valor_Desconto',
                   T003_Valor_Desconto_Confirmado='$T003_Valor_Desconto',
                   T003_Percentual_Desconto=(T003_Valor_Desconto/{$mT004['Total_Sem_Desconto']})*100,
                   T003_Percentual_Desconto_Confirmado=(T003_Valor_Desconto/{$mT004['Total_Sem_Desconto']})*100
             WHERE T003_Id='$T003_Id'
EOT;
            mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return '3. Erro: ' . mysql_error();
            }
            $cSql_T003 = mysql_query("SELECT * FROM T003 WHERE T003_Id='$T003_Id'");
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return '4. Erro: ' . mysql_error();
            }
            
        } else if ($Campo == 'T003_Percentual_Desconto_Confirmado') {
            $Valor = formata_valor_gravar($Valor, 5);
            $sql   = <<<EOT
                UPDATE T003 
                   SET T003_Percentual_Desconto_Confirmado='$Valor',
                       T003_Valor_Desconto_Confirmado=(T003_Percentual_Desconto_Confirmado/100)*{$mT004['Total_Sem_Desconto_Confirmado']}
                 WHERE T003_Id='$T003_Id'
EOT;
            mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return '5. Erro: ' . mysql_error();
            }
        } else if ($Campo == 'T003_Valor_Desconto_Confirmado') {
            $T003_Valor_Desconto_Confirmado = formata_valor_gravar($T003_Valor_Desconto_Confirmado, 3);
            $sql                            = <<<EOT
           UPDATE T003 
           SET T003_Valor_Desconto_Confirmado='$T003_Valor_Desconto_Confirmado',
               T003_Percentual_Desconto_Confirmado=(T003_Valor_Desconto_Confirmado/{$mT004['Total_Sem_Desconto_Confirmado']})*100
           WHERE T003_Id='$T003_Id'
EOT;
            mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return '6. Erro: ' . mysql_error();
            }
            
        }
        $cSql_T004 = <<<EOT
        SELECT T004_Id FROM T004 WHERE T004_T003_Id='$T003_Id'
EOT;
        $cSql_T004 = mysql_query($cSql_T004);
        while ($mT004 = mysql_fetch_array($cSql_T004)) {
            $sql = <<<EOT
              call T004_Gravar_Totalizacao_4('{$mT004['T004_Id']}')
EOT;
            mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return '7. Erro: ' . mysql_error();
            }
        }
        $sql = <<<EOT
        call T003_Gravar_Totalizacao_4('$T003_Id')
EOT;
        mysql_query($sql);
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return '8. Erro: ' . mysql_error();
        } else {
            mysql_query("COMMIT");
            return true;
        }
    }
    
    /**
     * gravarObservacao
     *
     * @param Form $form
     * @param String $T003_Id
     * @return String/bool
     */
    public function gravarObservacao($form, $T003_Id)
    {
        $obs    = $form['T003_Observacao_Pagamento'];
        $sql    = "UPDATE T003 SET T003_Observacao_Pagamento='{$obs}' WHERE T003_Id='$T003_Id'";
        $result = mysql_query($sql);
        if (mysql_error()) {
            return mysql_error();
        } else {
            return true;
        }
        
    }
    
    /**
     * importarItensOrcamento
     *
     * @param Form $form
     * @return String/bool
     */
    public function importarItensOrcamento($form)
    {        
        global $g;
        $T003_Id        = $form->campoValorEnviado('T003_Id');
        $T003_Id_Origem = $form->campoValorEnviado('T003_Id_Origem');
        
        if (empty($T003_Id)) {
            return "Faltou o parametro T003_Id";
        }
        if (empty($T003_Id_Origem)) {
            return "Digite o numero do orçamento";
        }
        
        $cSql_T003 = mysql_query("SELECT * FROM T003 WHERE T003_Id='$T003_Id'");
        $mT003     = mysql_fetch_array($cSql_T003);
        
        /////////////////////////////////////////////////////////////////////////////////
        //-- VERIFICA SE O ITEM EXISTEM NA EMPRESA DE DESTINO
        /////////////////////////////////////////////////////////////////////////////////
        $cSql_T004 = mysql_query("SELECT t2.D009_Id,
                                     D001_Codigo_Produto,
                                     D002_Descricao_Produto
                                FROM T004
                           LEFT JOIN D009 as t1 ON T004_D009_Id=t1.D009_Id
                           LEFT JOIN D049 ON D049_Id=t1.D009_D049_Id
                           LEFT JOIN D001 ON D001_Id=D049_D001_Id
                           LEFT JOIN D002 ON D002_Id=D001_D002_Id
                           LEFT JOIN D009 as t2 ON D049_Id=t2.D009_D049_Id AND t2.D009_C004_Id='{$g['empresaAtual']}'
                               WHERE T004_T003_Id='$T003_Id'
                                 AND t2.D009_Id=0");
        while ($mT004 = mysql_fetch_array($cSql_T004)) {
            $mSemCadastro[] = "{$mT004['D001_Codigo_Produto']} - {$mT004['D002_Descricao_Produto']}";
        }
        if (mysql_num_rows($cSql_T004) > 0) {
            $SemCadastro = implode("/p", $mSemCadastro);
            return "Existem itens sem cadastro na empresa " . $g['empesaAtual'] . " que estão sendo importados do orçamento " . $T003_Id_Origem . "<br />" . $SemCadastro;
        }
                
        $cSql_T004 = mysql_query("SELECT * FROM T004 WHERE T004_T003_Id='{$T003_Id_Origem}' ORDER BY T004_Item");
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        while ($linhaT004 = mysql_fetch_array($cSql_T004)) {
           
            $D009 = mysql_query("SELECT D0092.D009_Id AS D009_Id
                                   FROM D009 AS D0091
                              LEFT JOIN D009 AS D0092 ON D0092.D009_D049_Id=D0091.D009_D049_Id AND D0092.D009_C004_Id='{$g['empresaAtual']}'
                                  WHERE D0091.D009_Id='{$linhaT004['T004_D009_Id']}'"); 
            $mD009 = mysql_fetch_assoc($D009);

            $erro = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return $erro;
            }

            if ($mD009['D009_Id']<=0){
                if (!empty($erro)) {
                    mysql_query("ROLLBACK");
                    return "Erro ao encontrar o D009_Id da empresa atual, favor informar o suporte técnico";
                }
            }

            $linhaT004['T004_D009_Id'] = $mD009['D009_Id'];

            $linhaT004['T004_Codigo_Produto'] = mysql_real_escape_string($linhaT004['T004_Codigo_Produto']);
            $linhaT004['T004_Codigo_Substituto'] = mysql_real_escape_string($linhaT004['T004_Codigo_Substituto']);
            $linhaT004['T004_Descricao_Produto'] = mysql_real_escape_string($linhaT004['T004_Descricao_Produto']);
            $linhaT004['T004_Observacao'] = mysql_real_escape_string($linhaT004['T004_Observacao']);

            $dadosItem = mysql_query("select max(T004_Item) as item from T004 where T004_T003_Id = '{$T003_Id}'");
            $varItem   = mysql_fetch_array($dadosItem);
            $T004_Item = $varItem['item'] + 1;

            $insertT004 = <<<EOT
            insert into T004 (
                T004_C004_Id,
                T004_Item,
                T004_Codigo_Produto,
                T004_Codigo_Substituto,
                T004_Descricao_Produto,
                T004_T003_Id,
                T004_D024_Id,
                T004_D009_Id,
                T004_D037_Id,
                T004_D082_Id,
                T004_Quantidade,
                T004_Valor_Custo_Unitario,
                T004_Valor_Preco_Unitario,
                T004_Valor_Preco_Sem_Desconto_Unitario,
                T004_Valor_Preco_Original,
                T004_Percentual_Desconto,
                T004_Valor_Desconto_Unitario,
                T004_IPV,
                T004_Peso_Unitario,
                T004_D005_Id,
                T004_Classificacao_Fiscal,
                T004_D006_Id,
                T004_Aliquota_IPI,
                T004_Aliquota_ICMS,
                T004_Percentual_Reducao_ICMS,
                T004_Percentual_Reducao_ICMS_ST,
                T004_ST_VA,
                T004_Flag_ST,
                T004_Flag_Situacao_Tributaria,
                T004_ICMS_Inter_Estadual,
                T004_ST_ICMS_Interno,
                T004_Flag_Isento_PIS,
                T004_Aliquota_PIS,
                T004_Situacao_Tributaria_PIS,
                T004_Flag_Isento_COFINS,
                T004_Aliquota_COFINS,
                T004_Flag_Preco_Especial,
                T004_Situacao_Tributaria_COFINS,
                T004_Situacao_Tributaria_IPI,
                T004_D059_Id,
                T004_Observacao,
                T004_Numero_Pedido_Compra,
                T004_Item_Pedido_Compra,
                T004_Flag_Destino_Produto,
                T004_Flag_Origem_Produto
            ) VALUES (
                '{$linhaT004['T004_C004_Id']}',
                '{$T004_Item}',
                '{$linhaT004['T004_Codigo_Produto']}',
                '{$linhaT004['T004_Codigo_Substituto']}',
                '{$linhaT004['T004_Descricao_Produto']}',
                '{$T003_Id}',
                '{$linhaT004['T004_D024_Id']}',
                '{$linhaT004['T004_D009_Id']}',
                '{$linhaT004['T004_D037_Id']}',
                '{$linhaT004['T004_D082_Id']}',
                '{$linhaT004['T004_Quantidade']}',
                '{$linhaT004['T004_Valor_Custo_Unitario']}',
                '{$linhaT004['T004_Valor_Preco_Unitario']}',
                '{$linhaT004['T004_Valor_Preco_Sem_Desconto_Unitario']}',
                '{$linhaT004['T004_Valor_Preco_Original']}',
                '{$linhaT004['T004_Percentual_Desconto']}',
                '{$linhaT004['T004_Valor_Desconto_Unitario']}',
                '{$linhaT004['T004_IPV']}',
                '{$linhaT004['T004_Peso_Unitario']}',
                '{$linhaT004['T004_D005_Id']}',
                '{$linhaT004['T004_Classificacao_Fiscal']}',
                '{$linhaT004['T004_D006_Id']}',
                '{$linhaT004['T004_Aliquota_IPI']}',
                '{$linhaT004['T004_Aliquota_ICMS']}',
                '{$linhaT004['T004_Percentual_Reducao_ICMS']}',
                '{$linhaT004['T004_Percentual_Reducao_ICMS_ST']}',
                '{$linhaT004['T004_ST_VA']}',
                '{$linhaT004['T004_Flag_ST']}',
                '{$linhaT004['T004_Flag_Situacao_Tributaria']}',
                '{$linhaT004['T004_ICMS_Inter_Estadual']}',
                '{$linhaT004['T004_ST_ICMS_Interno']}',
                '{$linhaT004['T004_Flag_Isento_PIS']}',
                '{$linhaT004['T004_Aliquota_PIS']}',
                '{$linhaT004['T004_Situacao_Tributaria_PIS']}',
                '{$linhaT004['T004_Flag_Isento_COFINS']}',
                '{$linhaT004['T004_Aliquota_COFINS']}',
                '{$linhaT004['T004_Flag_Preco_Especial']}',
                '{$linhaT004['T004_Situacao_Tributaria_COFINS']}',
                '{$linhaT004['T004_Situacao_Tributaria_IPI']}',
                '{$linhaT004['T004_D059_Id']}',
                '{$linhaT004['T004_Observacao']}',
                '{$linhaT004['T004_Numero_Pedido_Compra']}',
                '{$linhaT004['T004_Item_Pedido_Compra']}',
                '{$linhaT004['T004_Flag_Destino_Produto']}',
                '{$linhaT004['T004_Flag_Origem_Produto']}'
            );
EOT;
            mysql_query($insertT004);
            $erro = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return $erro;
            }
            
            $T004Novo = $g['mysqlLastId'];

            $T066_Id = $this->locacaoItemValidadeOrcamento($linhaT004['T004_D009_Id']);

            $T004A = mysql_query("SELECT T004A_Id FROM T004A WHERE T004A_T004_Id = '{$T004Novo}'");
            if(mysql_num_rows($T004A) > 0){
                mysql_query("UPDATE T004A SET T004A_T066_Id = '{$T066_Id}' WHERE T004A_T004_Id = '{$T004Novo}'");
            } else {
                mysql_query("INSERT INTO T004A (T004A_T004_Id, T004A_T066_Id) VALUES ('{$T004Novo}', '{$T066_Id}')");
            }
            
            $sqlT145 = mysql_query("SELECT * FROM T145 WHERE T145_T004_Id='{$linhaT004['T004_Id']}'");
            
            while ($linhaT145 = mysql_fetch_array($sqlT145)) {
                
                $cClausula = <<<EOT
                    insert into T145 (
                        T145_T003_Id,
                        T145_D112_Id,
                        T145_D110_Id,
                        T145_T004_Id,
                        T145_Titulo,
                        T145_Descricao,
                        T145_Tempo_Estimado,
                        T145_Quantidade,
                        T145_Quantidade_Confirmada,
                        T145_Valor_Unitario,
                        T145_Valor_Total,
                        T145_D116_Id
                    ) VALUES (
                        '{$T003_Id}',
                        '{$linhaT145['T145_D112_Id']}',
                        '{$linhaT145['T145_D110_Id']}',
                        '{$T004Novo}',
                        '{$linhaT145['T145_Titulo']}',
                        '{$linhaT145['T145_Descricao']}',
                        '{$linhaT145['T145_Tempo_Estimado']}',
                        '{$linhaT145['T145_Quantidade']}',
                        '{$linhaT145['T145_Quantidade_Confirmada']}',
                        '{$linhaT145['T145_Valor_Unitario']}',
                        '{$linhaT145['T145_Valor_Total']}',
                        '{$mT003['T003_D116_Id']}'
                    )
EOT;
                
                mysql_query($cClausula);
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return 'falha ao copiar os serviços do produto';
                }
            }
            
            $this->ven001atualizarCFOP($T003_Id, $T004Novo);
            $retorno = $this->atualizarAliquotas($T003_Id, false, $T004Novo);
            mysql_query("call T004_Gravar_Totalizacao_4($T004Novo)");
            
        }
        
        mysql_query("call T003_Gravar_Totalizacao_4($T003_Id)");
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return 'falha ao gravar totalização';
        }
        
        mysql_query("COMMIT");

        
        if ($retorno === true) {
            return true;
        } else {
            return $retorno;
        }
    }
    
    /**
     * importarItensOrcamentoEmEtapas
     *
     * @param Array $dados - Array com dados necessários para importação dos itens do orçamento
     * @param Array $cacheAnterior - Array com dados do cache da execução anterior
     * @return String|Bool
     */
    public function importarItensOrcamentoEmEtapas($dados, $cacheAnterior = false) {
        global $g;
        $T004_Id        = $dados['T004_Id'];
        $T003_Id        = $dados['T003_Id'];
        $T003_Id_Origem = $dados['T003_Id_Origem'];

        if (empty($T004_Id)) { return "Faltou o parametro T004_Id"; }
        if (empty($T003_Id)) { return "Faltou o parametro T003_Id"; }
        if (empty($T003_Id_Origem)) { return "Digite o numero do orçamento"; }
        
        // Verifica se o item existe na empresa de destino
        $semCad = mysql_query("SELECT
                                    D001_Codigo_Produto,
                                    D002_Descricao_Produto
                                FROM T004
                                    LEFT JOIN D009 as T1 ON T004_D009_Id = T1.D009_Id
                                    LEFT JOIN D049 ON D049_Id = T1.D009_D049_Id
                                    LEFT JOIN D001 ON D001_Id = D049_D001_Id
                                    LEFT JOIN D002 ON D002_Id = D001_D002_Id
                                    LEFT JOIN D009 as T2 ON D049_Id = T2.D009_D049_Id AND T2.D009_C004_Id = '{$g['empresaAtual']}'
                                WHERE T004_T003_Id = '{$T003_Id}'
                                    AND T2.D009_Id = 0");
                                    
        while ($semCadT004 = mysql_fetch_assoc($semCad)) { $semCadastro[] = "{$semCadT004['D001_Codigo_Produto']} - {$semCadT004['D002_Descricao_Produto']}"; }

        if (mysql_num_rows($semCad) > 0) {
            $semCadastro = implode("/p", $semCadastro);
            return "Existem itens sem cadastro na empresa {$g['empesaAtual']} que estão sendo importados do orçamento {$T003_Id_Origem}<br>{$semCadastro}";
        }
                
        // Busca os dados do item
        $sqlT004 = "SELECT 
                        T004_D009_Id,
                        T004_Codigo_Produto,
                        T004_Codigo_Substituto,
                        T004_Descricao_Produto,
                        T004_Observacao,
                        T004_C004_Id,
                        T004_Item,
                        T004_T003_Id,
                        T004_D024_Id,
                        T004_D037_Id,
                        T004_D082_Id,
                        T004_Quantidade,
                        T004_Valor_Custo_Unitario,
                        T004_Valor_Preco_Unitario,
                        T004_Valor_Preco_Sem_Desconto_Unitario,
                        T004_Valor_Preco_Original,
                        T004_Percentual_Desconto,
                        T004_Valor_Desconto_Unitario,
                        T004_IPV,
                        T004_Peso_Unitario,
                        T004_D005_Id,
                        T004_Classificacao_Fiscal,
                        T004_D006_Id,
                        T004_Aliquota_IPI,
                        T004_Aliquota_ICMS,
                        T004_Percentual_Reducao_ICMS,
                        T004_Percentual_Reducao_ICMS_ST,
                        T004_ST_VA,
                        T004_Flag_ST,
                        T004_Flag_Situacao_Tributaria,
                        T004_ICMS_Inter_Estadual,
                        T004_ST_ICMS_Interno,
                        T004_Flag_Isento_PIS,
                        T004_Aliquota_PIS,
                        T004_Situacao_Tributaria_PIS,
                        T004_Flag_Isento_COFINS,
                        T004_Aliquota_COFINS,
                        T004_Flag_Preco_Especial,
                        T004_Situacao_Tributaria_COFINS,
                        T004_Situacao_Tributaria_IPI,
                        T004_D059_Id,
                        T004_Numero_Pedido_Compra,
                        T004_Item_Pedido_Compra,
                        T004_Flag_Destino_Produto,
                        T004_Flag_Origem_Produto
                    FROM T004 
                    WHERE T004_Id = '{$T004_Id}' 
                    ORDER BY T004_Item";
        $T004 = mysql_fetch_assoc(mysql_query($sqlT004));

        $sqlD009 = "SELECT D0092.D009_Id AS D009_Id FROM D009 AS D0091
                        LEFT JOIN D009 AS D0092 ON D0092.D009_D049_Id = D0091.D009_D049_Id AND D0092.D009_C004_Id='{$g['empresaAtual']}'
                    WHERE D0091.D009_Id='{$T004['T004_D009_Id']}'"; 
        
        $D009 = mysql_fetch_assoc(mysql_query($sqlD009));
        if ($erro = mysql_error()) { return $erro;}

        if ($D009['D009_Id'] <= 0){
            if (!empty($erro)) {
                return "Erro ao encontrar o D009_Id da empresa atual, favor informar o suporte técnico";
            }
        }

        $T004['T004_D009_Id'] = $D009['D009_Id'];
        $T004['T004_Codigo_Produto'] = mysql_real_escape_string($T004['T004_Codigo_Produto']);
        $T004['T004_Codigo_Substituto'] = mysql_real_escape_string($T004['T004_Codigo_Substituto']);
        $T004['T004_Descricao_Produto'] = mysql_real_escape_string($T004['T004_Descricao_Produto']);
        $T004['T004_Observacao'] = mysql_real_escape_string($T004['T004_Observacao']);

        $varItem   = mysql_fetch_assoc(mysql_query("SELECT max(T004_Item) AS item FROM T004 WHERE T004_T003_Id = '{$T003_Id}'"));
        $T004_Item = $varItem['item'] + 1;
        
        // Monta insert na T004
        $insT004 = [
            "T004_C004_Id"                              => $T004['T004_C004_Id'],
            "T004_Item"                                 => $T004_Item,
            "T004_Codigo_Produto"                       => $T004['T004_Codigo_Produto'],
            "T004_Codigo_Substituto"                    => $T004['T004_Codigo_Substituto'],
            "T004_Descricao_Produto"                    => $T004['T004_Descricao_Produto'],
            "T004_T003_Id"                              => $T003_Id,
            "T004_D024_Id"                              => $T004['T004_D024_Id'],
            "T004_D009_Id"                              => $T004['T004_D009_Id'],
            "T004_D037_Id"                              => $T004['T004_D037_Id'],
            "T004_D082_Id"                              => $T004['T004_D082_Id'],
            "T004_Quantidade"                           => $T004['T004_Quantidade'],
            "T004_Valor_Custo_Unitario"                 => $T004['T004_Valor_Custo_Unitario'],
            "T004_Valor_Preco_Unitario"                 => $T004['T004_Valor_Preco_Unitario'],
            "T004_Valor_Preco_Sem_Desconto_Unitario"    => $T004['T004_Valor_Preco_Sem_Desconto_Unitario'],
            "T004_Valor_Preco_Original"                 => $T004['T004_Valor_Preco_Original'],
            "T004_Percentual_Desconto"                  => $T004['T004_Percentual_Desconto'],
            "T004_Valor_Desconto_Unitario"              => $T004['T004_Valor_Desconto_Unitario'],
            "T004_IPV"                                  => $T004['T004_IPV'],
            "T004_Peso_Unitario"                        => $T004['T004_Peso_Unitario'],
            "T004_D005_Id"                              => $T004['T004_D005_Id'],
            "T004_Classificacao_Fiscal"                 => $T004['T004_Classificacao_Fiscal'],
            "T004_D006_Id"                              => $T004['T004_D006_Id'],
            "T004_Aliquota_IPI"                         => $T004['T004_Aliquota_IPI'],
            "T004_Aliquota_ICMS"                        => $T004['T004_Aliquota_ICMS'],
            "T004_Percentual_Reducao_ICMS"              => $T004['T004_Percentual_Reducao_ICMS'],
            "T004_Percentual_Reducao_ICMS_ST"           => $T004['T004_Percentual_Reducao_ICMS_ST'],
            "T004_ST_VA"                                => $T004['T004_ST_VA'],
            "T004_Flag_ST"                              => $T004['T004_Flag_ST'],
            "T004_Flag_Situacao_Tributaria"             => $T004['T004_Flag_Situacao_Tributaria'],
            "T004_ICMS_Inter_Estadual"                  => $T004['T004_ICMS_Inter_Estadual'],
            "T004_ST_ICMS_Interno"                      => $T004['T004_ST_ICMS_Interno'],
            "T004_Flag_Isento_PIS"                      => $T004['T004_Flag_Isento_PIS'],
            "T004_Aliquota_PIS"                         => $T004['T004_Aliquota_PIS'],
            "T004_Situacao_Tributaria_PIS"              => $T004['T004_Situacao_Tributaria_PIS'],
            "T004_Flag_Isento_COFINS"                   => $T004['T004_Flag_Isento_COFINS'],
            "T004_Aliquota_COFINS"                      => $T004['T004_Aliquota_COFINS'],
            "T004_Flag_Preco_Especial"                  => $T004['T004_Flag_Preco_Especial'],
            "T004_Situacao_Tributaria_COFINS"           => $T004['T004_Situacao_Tributaria_COFINS'],
            "T004_Situacao_Tributaria_IPI"              => $T004['T004_Situacao_Tributaria_IPI'],
            "T004_D059_Id"                              => $T004['T004_D059_Id'],
            "T004_Observacao"                           => $T004['T004_Observacao'],
            "T004_Numero_Pedido_Compra"                 => $T004['T004_Numero_Pedido_Compra'],
            "T004_Item_Pedido_Compra"                   => $T004['T004_Item_Pedido_Compra'],
            "T004_Flag_Destino_Produto"                 => $T004['T004_Flag_Destino_Produto'],
            "T004_Flag_Origem_Produto"                  => $T004['T004_Flag_Origem_Produto'],
        ];

        $insertT004 = "INSERT INTO T004 (" . implode(", ", array_keys($insT004)) . ") VALUES ('" . implode("', '", $insT004) . "')";
        
        mysql_query("START TRANSACTION");
        mysql_query($insertT004);
        if ($erro = mysql_error()) { mysql_query("ROLLBACK"); return $erro; }
        $T004_Id_Novo = $g['mysqlLastId'];

        // Acoes apos incluir item
        $T066_Id = $this->locacaoItemValidadeOrcamento($T004['T004_D009_Id']);

        $T004A = mysql_query("SELECT T004A_Id FROM T004A WHERE T004A_T004_Id = '{$T004_Id_Novo}'");
        
        if(mysql_num_rows($T004A) > 0) {
            $query = "UPDATE T004A SET T004A_T066_Id = '{$T066_Id}' WHERE T004A_T004_Id = '{$T004_Id_Novo}'";
        } else {
            $query = "INSERT INTO T004A (T004A_T004_Id, T004A_T066_Id) VALUES ('{$T004_Id_Novo}', '{$T066_Id}')";
        }

        mysql_query($query);
        if ($erro = mysql_error()) { mysql_query("ROLLBACK"); return $erro; }
        
        $mT003   = mysql_fetch_assoc(mysql_query("SELECT T003_D116_Id FROM T003 WHERE T003_Id = '$T003_Id'"));
        $sqlT145 = mysql_query("SELECT * FROM T145 WHERE T145_T004_Id = '{$T004['T004_Id']}'");
        while ($linhaT145 = mysql_fetch_assoc($sqlT145)) {
            $insT145 = [
                "T145_T003_Id"                  => $T003_Id,
                "T145_D112_Id"                  => $linhaT145['T145_D112_Id'],
                "T145_D110_Id"                  => $linhaT145['T145_D110_Id'],
                "T145_T004_Id"                  => $T004_Id_Novo,
                "T145_Titulo"                   => $linhaT145['T145_Titulo'],
                "T145_Descricao"                => $linhaT145['T145_Descricao'],
                "T145_Tempo_Estimado"           => $linhaT145['T145_Tempo_Estimado'],
                "T145_Quantidade"               => $linhaT145['T145_Quantidade'],
                "T145_Quantidade_Confirmada"    => $linhaT145['T145_Quantidade_Confirmada'],
                "T145_Valor_Unitario"           => $linhaT145['T145_Valor_Unitario'],
                "T145_Valor_Total"              => $linhaT145['T145_Valor_Total'],
                "T145_D116_Id"                  => $mT003['T003_D116_Id'],
            ];

            $insertT145 = "INSERT INTO T145 (" . implode(", ", array_keys($insT145)) . ") VALUES ('" . implode("', '", $insT145) . "')";
            mysql_query($insertT145);
            if ($erro = mysql_error()) { mysql_query("ROLLBACK"); return "Falha ao copiar os serviços do produto: <br> {$erro}"; }
        }
        
        // Atualiza dados do item
        $this->ven001atualizarCFOP($T003_Id, $T004_Id_Novo);
        $retorno = $this->atualizarAliquotas($T003_Id, false, $T004_Id_Novo);
        mysql_query("CALL T004_Gravar_Totalizacao_4({$T004_Id_Novo})");

        // Se for o ultimo T004 a ser processado atualiza o cabeçalho do orçamento
        if (!empty($dados['Ultimo_T004'])) {
            mysql_query("CALL T003_Gravar_Totalizacao_4({$T003_Id})");
            if ($erro = mysql_error()) { mysql_query("ROLLBACK"); return "Falha ao gravar totalização: <br> {$erro}"; }
        }

        mysql_query("COMMIT");
        return ($retorno === true) ? true : $retorno;
    }
    
    /**
     * calculaPercentualDescontoOrcamento
     *
     * @param String $T003_Id
     * @return String
     */ 
    public function calculaPercentualDescontoOrcamento($T003_Id)
    {    
        global $g;
        
        $retorno  = mysql_query("SELECT * FROM T003 WHERE T003_Id='{$T003_Id}'");
        $T003     = mysql_fetch_array($retorno);
        $total    = '';
        $desconto = ''; // percentual de desconto
        if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
            $totalConfirmado = $T003['T003_Valor_Total_Confirmado'];
            $totalOrcamento  = $T003['T003_Valor_Total'];
            if ($totalConfirmado <= 0) {
                $total = $totalOrcamento;
                if ($total <= 0) {
                    return 0;
                }
                $val      = '0.00';
                $retorno  = mysql_query("SELECT 
                                            sum(T004_Valor_Preco_Original_Bruto*T004_Quantidade) as Total_Orcamento, 
                                            sum(T004_Valor_Preco_Bruto*T004_Quantidade) - ifnull(T003_Valor_Desconto_Manual,0) as Total_Tabela 
                                            FROM T004 
                                            LEFT JOIN T003 ON T003_Id = T004_T003_Id
                                            WHERE T004_T003_Id='{$T003_Id}'");
                $mT004    = mysql_fetch_array($retorno);
                if($mT004['Total_Orcamento'] > 0){
                    $desconto = 1 - ($mT004['Total_Tabela'] / $mT004['Total_Orcamento']); // calcular o percentual de desconto dos itens confirmados   
                }         
                
                $desconto = $desconto * 100;
                
            } else {
                $total    = $totalConfirmado;
                $val      = '0.00';
                $retorno  = mysql_query("SELECT 
                                            sum(T004_Valor_Preco_Original_Bruto*T004_Quantidade_Confirmada) as Total_Orcamento, 
                                            sum(T004_Valor_Preco_Bruto*T004_Quantidade_Confirmada) - ifnull(T003_Valor_Desconto_Manual,0) as Total_Tabela 
                                            FROM T004 
                                            LEFT JOIN T003 ON T003_Id = T004_T003_Id
                                            WHERE T004_T003_Id='{$T003_Id}'");
                $mT004    = mysql_fetch_array($retorno);
                if($mT004['Total_Orcamento'] > 0){
                    $desconto = 1 - ($mT004['Total_Tabela'] / $mT004['Total_Orcamento']); // calcular o percentual de desconto dos itens confirmados            
                }
                                
                $desconto = $desconto * 100;
            }
            
            return $desconto;
        } else {
           
            $totalConfirmado = $T003['T003_Valor_Total_Produtos_Confirmado'];
            $totalOrcamento  = $T003['T003_Valor_Total_Produtos'];
            if ($totalConfirmado <= 0) {
                $total = $totalOrcamento;
                if ($total <= 0) {
                    return 0;
                }
                $val      = '0.00';
                $retorno  = mysql_query("SELECT 
                                            sum(T004_Valor_Preco_Original*T004_Quantidade) as Total_Orcamento, 
                                            sum(T004_Valor_Preco_Unitario*T004_Quantidade) - ifnull(T003_Valor_Desconto_Manual,0) as Total_Tabela 
                                            FROM T004 
                                            LEFT JOIN T003 ON T003_Id = T004_T003_Id
                                            WHERE T004_T003_Id='{$T003_Id}'");
                $mT004    = mysql_fetch_array($retorno);
                
                // Verifica os itens que possuem clube de benefício a ajusta o Total_Tabela
                // Com o desconto fixo do clube de beneficios
                $valoresClubeBeneficio = $this->retiraValorDescontoClubeBeneficios($T003_Id,false);
                $mT004['Total_Tabela'] += $valoresClubeBeneficio;
                // Fim verificação clube de benefícios

                if($mT004['Total_Orcamento'] > 0){
                    $desconto = 1 - ($mT004['Total_Tabela'] / $mT004['Total_Orcamento']); // calcular o percentual de desconto dos itens confirmados            
                }

                $desconto = $desconto * 100;
                
            } else {
                $total    = $totalConfirmado;
                $val      = '0.00';
                $retorno  = mysql_query("SELECT sum(T004_Valor_Preco_Original*T004_Quantidade_Confirmada) as Total_Orcamento, 
                                                sum(T004_Valor_Preco_Unitario*T004_Quantidade_Confirmada) - ifnull(T003_Valor_Desconto_Manual,0) as Total_Tabela 
                                           FROM T004 
                                      LEFT JOIN T003 ON T003_Id = T004_T003_Id
                                          WHERE T004_T003_Id='{$T003_Id}'");
                $mT004    = mysql_fetch_array($retorno);

                // Verifica os itens que possuem clube de benefício a ajusta o Total_Tabela 
                // Acrescentando o valor desse desconto fixo
                $valoresClubeBeneficio = $this->retiraValorDescontoClubeBeneficios($T003_Id,true);
                $mT004['Total_Tabela'] += $valoresClubeBeneficio;
                // Fim verificação clube de benefícios

                if($mT004['Total_Orcamento'] > 0){
                    $desconto = 1 - ($mT004['Total_Tabela'] / $mT004['Total_Orcamento']); // calcular o percentual de desconto dos itens confirmados            
                }
                
                $desconto = $desconto * 100;
            }
            
            return $desconto;
        }
    }

    /**
     * retiraValorDescontoClubeBeneficios
     *
     * @param String $T003_Id
     * @return String
     */ 
    public function retiraValorDescontoClubeBeneficios($T003_Id)
    {
        global $g;
        // Utilizado no APP GRUPO DE DESCONTO
        return 0;
    }
    
    /**
     * retornaMaiorDescontoOrcamento
     *
     * @param String $T003_Id
     * @return String
     */ 
    public function retornaMaiorDescontoOrcamento($T003_Id)
    {
        global $g;
        if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
            
            $T003MaiorDesconto  = mysql_query("SELECT T004_Percentual_Desconto_Bruto FROM T003 LEFT JOIN T004 ON T004_T003_Id=T003_Id WHERE T004_T003_Id='{$T003_Id}' ORDER BY T004_Percentual_Desconto_Bruto DESC LIMIT 1");
            $mT003MaiorDesconto = mysql_fetch_array($T003MaiorDesconto);
            $maiorDesconto      = $mT003MaiorDesconto['T004_Percentual_Desconto_Bruto'];
        } else {
            $T003MaiorDesconto  = mysql_query("SELECT T004_Percentual_Desconto FROM T003 LEFT JOIN T004 ON T004_T003_Id=T003_Id WHERE T004_T003_Id='{$T003_Id}' ORDER BY T004_Percentual_Desconto_Bruto DESC LIMIT 1");
            $mT003MaiorDesconto = mysql_fetch_array($T003MaiorDesconto);
            $maiorDesconto      = $mT003MaiorDesconto['T004_Percentual_Desconto'];
        }
        return $maiorDesconto;
    }
    
    /**
     * verificaAlcada
     *
     * @param String $T003_Id
     * @param bool $viaTablet
     * @return String/bool
     */ 
    public function verificaAlcada($T003_Id, $viaTablet = false)
    {
        
        global $g;
        $retorno = mysql_query("SELECT * FROM T003 WHERE T003_Id='{$T003_Id}'");
        $T003    = mysql_fetch_array($retorno);
        
        // Quando for via tablet, (sigma) é dado o desconto no valor total do orçamento (else)
        if ($viaTablet == false) {
            // chama o método que retona o desconto aplicado no valor total dos produtos
            $desconto = $this->calculaPercentualDescontoOrcamento($T003_Id);
            
            // Pega o valor total dos produtos do orçamento
            $totalConfirmado  = $T003['T003_Valor_Total_Produtos_Confirmado'];
            $totalOrcamento   = $T003['T003_Valor_Total_Produtos'];
            //die("Total Produtos: ".$totalOrcamento);
            $total            = '';
            $prazoMedioAlcada = '';
            
            // verifica se há produto confirmado. Caso sim, ele usa apenas os produtos confirmado (T003_Valor_Total_Produtos_Confirmado)
            if ($totalConfirmado <= 0) {
                $total = $totalOrcamento;
            } else {
                $total = $totalConfirmado;
            }
        } else {
            
            // pega o valor do desconto manual que é aplicado no valor total do orçamento
            @$desconto = ($T003['T003_Valor_Desconto_Manual'] * 100) / ($T003['T003_Valor_Desconto_Manual'] + $T003['T003_Valor_Total']);
            
            // Pega o valor total do orçamento
            $total = $T003['T003_Valor_Total'];
        }
        
        $retorno = mysql_query("SELECT * FROM C007 WHERE C007_Id='{$g['usuarioAtual']}'");
        $C007    = mysql_fetch_array($retorno);
        
        // verifica se o prazo médio das parcelas geradas para este orçamento é menor ou igual ao $prazoMedioAlcada (FUNÇÃO AVG() RETORNA A MÉDIA DOS VALORES, RETORNA UM REGISTRO APENAS, COMO NO GROUP BY)
        $retorno = mysql_query("SELECT AVG(T089_Prazos_Dias) as Prazo_Medio,COUNT(T089_Id) as Numero_Parcelas FROM T089 WHERE T089_T003_Id='{$T003_Id}'");
        $T089    = mysql_fetch_array($retorno);
        if (mysql_num_rows($retorno) == 0) {
            $prazoMedio = 0;
        } else {
            $prazoMedio     = $T089['Prazo_Medio'];
            $numeroParcelas = $T089['Numero_Parcelas'];
        }
        
        
        /**
         * O IF ABAIXO É RESPONSÁVEL POR TRATAR DO DESCONTO POR PRODUTO (CASO A VARIÁVEL GLOBAL FOR IGUAL A 'S')
         */
        
        $usarAlcadaPorProduto = $g['conf']->ini('usarAlcadaPorProduto');
        if ($usarAlcadaPorProduto == "S") {
            
            $alcadaUsuario = 'D';
            
            // CASO A EMPRESA USE O PREÇO BRUTO, AS VERIFICAÇÕES SÃO FEITAS EM CIMA DO DESCONTO BRUTO
            if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
                $campoUsar = "T004_Percentual_Desconto_Bruto";
            } else {
                $campoUsar = "T004_Percentual_Desconto";
            }
            
            // verifica o tipo do usuário atual. (Vendedor, supervisor) e atribui o valor do desconto permitido que é configurado nas variáveis globais
            if ($C007['C007_Flag_Vendedor'] == 'V') {
                
                $alcadaUsuario = $g['conf']->ini('alcadaDescontoVendedorPorProduto');
                
            } else if ($C007['C007_Flag_Vendedor'] == 'S') {
                
                $alcadaUsuario = $g['conf']->ini('alcadaDescontoSupervisorPorProduto');
                
            } else if ($C007['C007_Flag_Vendedor'] != 'D') {
                
                // Retonar false e exibe a mensagem que ele não tem permissão para gerar orçamentos
                return false;
            }
            
            // se o usuario for diretor, nao passa por liberação
            if ($alcadaUsuario == 'D') {
                return true;
            }
            
            
            $aT003 = mysql_query("SELECT {$campoUsar} FROM T003 LEFT JOIN T004 ON T004_T003_Id=T003_Id WHERE T004_T003_Id='{$T003_Id}' ORDER BY {$campoUsar} DESC LIMIT 1");
            $mT003 = mysql_fetch_array($aT003);
            
            $mT003[$campoUsar] = number_format($mT003[$campoUsar], 2, ".", " ");
            $alcadaUsuario     = number_format($alcadaUsuario, 2, ".", " ");
            
            //die("Liberou alçada valor: ". $T003['T003_Liberou_Alcada_Valor_Total'] . " Total orçamento: ". $total);
            
            
            //die("Alçada usuário: " . $alcadaUsuario . " Campo usar: " . $mT003[$campoUsar]);
            // CASO JÁ TENHA PASSADO PELA LIBERAÇÃO (DADOS DA LIBERAÇÃO PREENCHIDOS)
            if ($T003['T003_Liberou_Alcada_C007_Id'] > 0 && $T003['T003_Liberou_Alcada_Data'] > 0 && ($T003['T003_Liberou_Alcada_Desconto'] >= 0 || $T003['T003_Liberou_Alcada_Prazo_Medio'] >= 0)) {
                
                if ($T003['T003_Liberou_Alcada_Valor_Total'] == $total) {
                    
                    return true;
                    
                } else {
                    
                    // VERIFICA SE A ALÇADA DO USUÁRIOATUAL É MENOR OU IGUAL AO MAIOR DESCONTO DADO
                    if (($alcadaUsuario < $mT003[$campoUsar]) || $T003['T003_Liberou_Alcada_Valor_Total'] != $total) {
                        
                        return "O desconto dado no orçamento exige liberação. Pedir liberação? (1)";
                    }
                }
                
            } else {
                
                // VERIFICA SE A ALÇADA DO USUÁRIOATUAL É MENOR OU IGUAL AO MAIOR DESCONTO DADO
                if (($alcadaUsuario < $mT003[$campoUsar])) {
                    
                    return "O desconto dado no orçamento exige liberação. Pedir liberação? (2)";
                }
            }
        }
        
        
        
        /**
         * TIPO ALÇADA
         * 
         * TIPO ALÇADA 1:
         * Verificação de alçada com base no tipo de usuário (Vendedor, supervisor. Diretor é sempre liberado). O desconto e prazo médio permitido
         * é cedido ao tipo de usuário, enquanto a alçada tipo 2 é com base no tipo de usuário e quantidade de parcelas
         * 
         * TIPO ALÇADA 2:
         * Verificação de alçada com base no tipo de usuários e quantidade de parcelas. O desconto e prazo médio permitido é cedido de acordo com a quantidade de parcelas.
         */
        $tipoAlcada = $g['conf']->ini('usarAlcadaTipo');
        
        if ($tipoAlcada == '1') {
            // verificação do desconto
            $alcadaUsuario = 'D';
            
            // verifica o tipo do usuário atual. (Vendedor, supervisor) e atribui o valor do desconto permitido que é configurado nas variáveis globais
            if ($C007['C007_Flag_Vendedor'] == 'V') {
                
                $alcadaUsuario = $g['conf']->ini('alcadaDescontoVendedor');
                
            } else if ($C007['C007_Flag_Vendedor'] == 'S') {
                
                $alcadaUsuario = $g['conf']->ini('alcadaDescontoSupervisor');
                
            } else if ($C007['C007_Flag_Vendedor'] != 'D') {
                
                // Retonar false e exibe a mensagem que ele não tem permissão para gerar orçamentos
                return false;
            }
            
            // se o usuario for diretor, nao passa por liberação 
            if ($alcadaUsuario == 'D') {
                return true;
            }
            
            //verifica alçada necessário para Liberação. Esta verificação é feita apenas para imprimir na mensagem de liberação
            if ($desconto > 0 && $desconto <= $g['conf']->ini('alcadaDescontoSupervisor')) {
                $alcadaNecessaria = "<b>SUPERVISOR</b>";
            } else {
                $alcadaNecessaria = "<b>DIRETOR</b>";
            }
            
            // Verifica se o desconto dado for maior que o permitido nas configurações
            if ($desconto > $alcadaUsuario) {
                
                // Se o desconto dado for maior que o permitido nas configurações, verifica se o orçamento já foi aprovado (com base nos campos das alçadas. Nome_Campo_Alcada > 0)
                if ($T003['T003_Liberou_Alcada_C007_Id'] > 0 && $T003['T003_Liberou_Alcada_Data'] > 0 && ($T003['T003_Liberou_Alcada_Desconto'] >= 0 || $T003['T003_Liberou_Alcada_Prazo_Medio'] >= 0)) {
                    
                    // Se o orçamento já foi aprovado, verifica se foi aprovado para o valor total atual. Ou seja, se não foi alterado após a aprovação
                    if ($T003['T003_Liberou_Alcada_Valor_Total'] == $total) {
                        return true;
                    } else {
                        
                        // Caso não tenha sido aprovado para o valor total atual do orçamento, retorna a mensagem de liberação
                        return 'A liberação deste orçamento exige alçada de ' . $alcadaNecessaria . ', pedir liberação?';
                    }
                } else {
                    
                    // Caso o orçamento nunca tenha sido aprovado, retorna a mensagem de liberação
                    return 'A liberação deste orçamento exige alçada de ' . $alcadaNecessaria . ', pedir liberação?';
                }
            }
            
            /**
             * Verificação do prazo médio
             * 
             * Retorna o prazo médio com base no tipo de usuário (vendedor, diretor)
             */
            $prazoMedioAlcada = $this->calculaPrazoMedioAlcada($T003_Id, $viaTablet);
            
            // verifica se o prazo médio da forma de pagametno do orçamento é maior que o permitido ao usuário (retorno do método calculaPrazoMedioAlcada())
            if ($prazoMedio > $prazoMedioAlcada) {
                
                // O prazo médio sendo maior que o permitido, verifica se o orçamento já foi aprovado para a condições atuais do orçamento
                if ($T003['T003_Liberou_Alcada_C007_Id'] > 0 && $T003['T003_Liberou_Alcada_Data'] > 0 && ($T003['T003_Liberou_Alcada_Desconto'] >= 0 || $T003['T003_Liberou_Alcada_Prazo_Medio'] >= 0)) {
                    
                    // caso o orçamento já tenha sido aprovado verifica se for aprovado com o prazo médio atual (verifica se não sofreu alterações).
                    if ($T003['T003_Liberou_Alcada_Prazo_Medio'] == $prazoMedio) {
                        return true;
                    } else {
                        
                        // caso o orçamento tenha sofrido alterações após a aprovação
                        return 'A liberação deste orçamento exige alçada de ' . $alcadaNecessaria . ', pedir liberação?';
                    }
                } else {
                    // caso o orçamento nunca tenha sido aprovado.
                    return 'A liberação deste orçamento exige alçada de ' . $alcadaNecessaria . ', pedir liberação?';
                }
            }
        } elseif ($tipoAlcada == '2') {
            
            $alcadaUsuario = 'D';
            
            // Verufuca qual tipo de usuário atual para definir regras
            if ($C007['C007_Flag_Vendedor'] == 'V') {
                $regrasUsuario = $g['conf']->ini('regrasVendedorPorParcelas');
                $alcadaUsuario = 'V';
            } else if ($C007['C007_Flag_Vendedor'] == 'S') {
                $regrasUsuario = $g['conf']->ini('regrasSupervisorPorParcelas');
                $alcadaUsuario = 'S';
            } else if ($C007['C007_Flag_Vendedor'] != 'D') {
                return false;
            }
            // se o usuario for diretor, nao passa por liberação 
            if ($alcadaUsuario == 'D') {
                return true;
            }
            
            /**
             *  VARIÁVEIS
             * 
             * $regrasUsuario       -> array com configurações por empresas setado no arquivo global
             * $prazoMedio          -> prazo médio da forma de pagamento do orçamento
             * $numeroParcelas      -> quantidade de parcelas da forma de pagamento do orçamento
             * $descontoMaximoAlcada    -> será o desconto permitido para o usuário atual neste orçamento
             */
            
            $descontoMaximoAlcada = 0;
            foreach ($regrasUsuario as $regra) {
                
                // verifica condições do orçamento e regras para definir o desconto permitido
                if ($prazoMedio <= $regra['pm'] && $numeroParcelas <= $regra['parcelas']) {
                    $descontoMaximoAlcada = $regra['desconto'];
                    break;
                }
            }
            
            $descontoMaximoAlcada = gCorrigeNumeroInverte($descontoMaximoAlcada);
            $desconto             = number_format($desconto, 2);
            
            // Verifica se o desconto dado for maior que o permitido nas configurações
            if ($desconto > $descontoMaximoAlcada) {
                // já liberou este orçamento?
                if ($T003['T003_Liberou_Alcada_C007_Id'] > 0 && $T003['T003_Liberou_Alcada_Data'] > 0 && ($T003['T003_Liberou_Alcada_Valor_Total'] >= 0 || $T003['T003_Liberou_Alcada_Prazo_Medio'] >= 0)) {
                    // já liberou com este prazo médio e com este desconto?
                    if (number_format($T003['T003_Liberou_Alcada_Valor_Total'], 2) == number_format($total, 2) && number_format($T003['T003_Liberou_Alcada_Prazo_Medio'], 0) == number_format($prazoMedio, 0)) {
                        return true;
                    } else {
                        return 'A liberação deste orçamento exige maior alçada, pedir liberação?(3)' . $T003['T003_Liberou_Alcada_Valor_Total'] . ' ' . $total . ' ' . $T003['T003_Liberou_Alcada_Prazo_Medio'] . ' ' . $prazoMedio;
                    }
                } else {
                    return 'A liberação deste orçamento exige maior alçada, pedir liberação?(4)';
                }
            }
        }
        
        // Verificação de açada por saldo mínimo de produto
        $retornoSaldo = $this->verificaFuturoSaldoProduto($T003_Id);
        if ($retornoSaldo > 0) {
            return 'A liberação deste orçamento exige maior alçada, pedir liberação?(5)';
        }
        return true;
    }
    
    /**
     * verificaFuturoSaldoProduto
     *
     * @param String $T003_Id
     * @param bool $restricaoAtual
     * @return String
     */     
    public function verificaFuturoSaldoProduto($T003_Id, $restricaoAtual = false)
    {
        global $g;
        $saldoMinimo = $g['C031']['alcadaQuantidadeMinimaEstoque'];
        if ($saldoMinimo <= 0) {
            return 0;
        }
        $sqlT004 = mysql_query("SELECT if(T004_Quantidade_Confirmada>0,T004_Quantidade_Confirmada,T004_Quantidade) as QuantidadeItens, 
                                        D009_Quantidade_Estoque_Liquido,
                                        T004_Liberou_Alcada_Saldo_Futuro,
                                        C007_Flag_Vendedor, 
                                        (D009_Quantidade_Estoque_Liquido-(if(T004_Quantidade_Confirmada>0,T004_Quantidade_Confirmada,T004_Quantidade))) as SaldoFuturo 
                                        FROM T004 
                                        LEFT JOIN D009 ON D009_Id=T004_D009_Id 
                                        LEFT JOIN T003 ON T003_Id=T004_T003_Id 
                                        LEFT JOIN C007 ON C007_Id=T003_C007_Id_Vendedor_Interno 
                                        WHERE T004_T003_Id='{$T003_Id}'");
        
        $retorno = 0;
        if ($restricaoAtual == false) {
            //Neste caso verifica se já foi aprovado
            while ($rowT004 = mysql_fetch_array($sqlT004)) {
                if (($rowT004['SaldoFuturo'] < $saldoMinimo) && ($rowT004['T004_Liberou_Alcada_Saldo_Futuro'] != $rowT004['SaldoFuturo']) && $rowT004['C007_Flag_Vendedor'] == 'V') {
                    $retorno += 1;
                }
            }
        } else {
            while ($rowT004 = mysql_fetch_array($sqlT004)) {
                if (($rowT004['SaldoFuturo'] < $saldoMinimo) && $rowT004['C007_Flag_Vendedor'] == 'V') {
                    $retorno += 1;
                }
            }
        }
        return $retorno;
    }
    
    /**
     * calculaPrazoMedioAlcada
     *
     * @param String $T003_Id
     * @param bool $restricaoAtual
     * @return String
     */     
    public function calculaPrazoMedioAlcada($T003_Id, $viaTablet)
    {      
        global $g;
        $retorno = mysql_query("SELECT * FROM C007 WHERE C007_Id='{$g['usuarioAtual']}'");
        $C007    = mysql_fetch_array($retorno);
        
        $retorno = mysql_query("SELECT * FROM T003 WHERE T003_Id='{$T003_Id}'");
        $T003    = mysql_fetch_array($retorno);
        
        $totalConfirmado  = $T003['T003_Valor_Total_Produtos_Confirmado'];
        $totalOrcamento   = $T003['T003_Valor_Total_Produtos'];
        $total            = '';
        $prazoMedioAlcada = '';
        if ($viaTablet == false) {
            if ($totalConfirmado <= 0) {
                $total = $totalOrcamento;
            } else {
                $total = $totalConfirmado;
            }
        } else {
            $total = $T009['T003_Valor_Total'];
        }
        
        if ($C007['C007_Flag_Vendedor'] == 'V') {
            $regrasUsuario = $g['conf']->ini('regrasVendedor');
        } else if ($C007['C007_Flag_Vendedor'] == 'S') {
            $regrasUsuario = $g['conf']->ini('regrasSupervisor');
        }
        
        foreach ($regrasUsuario as $regra) {
            if ($total <= $regra['valor']) {
                $prazoMedioAlcada = $regra['pm'];
                break;
            }
        }
        
        if ($prazoMedioAlcada == '')
            $prazoMedioAlcada = 0;
        
        return $prazoMedioAlcada;
        
    }

    /**
     * calculaPrazoMedioAlcadaVendedor
     *
     * @param String $T003_Id
     * @param bool $restricaoAtual
     * @return String
     */    
    public function calculaPrazoMedioAlcadaVendedor($T003_Id, $viaTablet = false)
    {     
        global $g;
        
        $retorno          = mysql_query("SELECT * FROM T003 WHERE T003_Id='{$T003_Id}'");
        $T003             = mysql_fetch_array($retorno);
        $totalConfirmado  = $T003['T003_Valor_Total_Produtos_Confirmado'];
        $totalOrcamento   = $T003['T003_Valor_Total_Produtos'];
        $total            = '';
        $prazoMedioAlcada = '';
        if ($viaTablet == false) {
            if ($totalConfirmado <= 0) {
                $total = $totalOrcamento;
            } else {
                $total = $totalConfirmado;
            }
        } else {
            $total = $T003['T003_Valor_Total'];
        }
        $regrasUsuario = $g['conf']->ini('regrasVendedor');
        
        foreach ($regrasUsuario as $regra) {
            if ($total <= $regra['valor']) {
                $prazoMedioAlcada = $regra['pm'];
                break;
            }
        }
        
        if ($prazoMedioAlcada == '') {
            $prazoMedioAlcada = 0;
        }
        
        return $prazoMedioAlcada;
    }
    
    /**
     * calculaPrazoMedioAlcadaSupervisor
     *
     * @param String $D024_Id
     * @return String
     */     
    public function calculaPrazoMedioAlcadaSupervisor($T003_Id, $viaTablet = false)
    {
        global $g;
        
        $retorno          = mysql_query("SELECT * FROM T003 WHERE T003_Id='{$T003_Id}'");
        $T003             = mysql_fetch_array($retorno);
        $totalConfirmado  = $T003['T003_Valor_Total_Produtos_Confirmado'];
        $totalOrcamento   = $T003['T003_Valor_Total_Produtos'];
        $total            = '';
        $prazoMedioAlcada = '';
        if ($viaTablet == false) {
            if ($totalConfirmado <= 0) {
                $total = $totalOrcamento;
            } else {
                $total = $totalConfirmado;
            }
        } else {
            $total = $T003['T003_Valor_Total'];
        }
        
        $regrasUsuario = $g['conf']->ini('regrasSupervisor');
        
        
        foreach ($regrasUsuario as $regra) {
            if ($total <= $regra['valor']) {
                $prazoMedioAlcada = $regra['pm'];
                break;
            }
        }
        
        if ($prazoMedioAlcada == '') {
            $prazoMedioAlcada = 0;
        }
        
        return $prazoMedioAlcada;   
    }
    
    /**
     * enviarParaAnalise
     *
     * @param String $T003_Id
     * @param String $alcada
     * @return String/bool
     */
    public function enviarParaAnalise($T003_Id, $alcada)
    {
        mysql_query("UPDATE T003 SET T003_Flag_Status_Orcamento='L', T003_Flag_Status_Liberacao='1',T003_Flag_PDF_Impresso='N' WHERE T003_Id='{$T003_Id}'");
        if (mysql_error()) {
            return mysql_error();
        } else {
            return true;
        }
    }
    
/*$tabelaCabecalho = tabela anterior (T003, T005, T007)
  $idTabelaCabecalho = id da tabela pai
  $tabelaProduto = tabela atual (edição) (T004, T006, T008)
  $desconto = percentual desconto
  $campoAplicarDesconto = sobre onde será aplicado o desconto (tabela, produto)*/
    public function VEN001aplicarDesconto($tabelaCabecalho, $idTabelaCabecalho, $tabelaProduto, $desconto, $campoAplicarDesconto) {
        global $g;

        // Verificação inicial para ver se é possível aplicar o desconto
            $arrayParam = array('tabelaCabecalho' => $tabelaCabecalho, 'idTabelaCabecalho' => $idTabelaCabecalho, 'tabelaProduto' => $tabelaProduto, 'desconto' => $desconto, 'campoAplicarDesconto' => $campoAplicarDesconto);
            if($verificaDesconto = $this->VerificaDesconto($arrayParam))
            {
                return $verificaDesconto;
            }
        // -----------------------------------------------------------

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        $Conteudo_Campo_Editado = $desconto;
        $fk = $tabelaProduto . "_" . $tabelaCabecalho . "_Id";
        $sql = "SELECT * FROM $tabelaProduto LEFT JOIN $tabelaCabecalho ON {$tabelaCabecalho}_Id = {$fk} WHERE $fk = $idTabelaCabecalho";
        
        $result = mysql_query($sql);
        $error = mysql_error();
        if (!empty($error)) {
            mysql_query("ROLLBACK");
            return "Erro 1: " . $error;
        }

        if (!empty($Conteudo_Campo_Editado)) {
            $Conteudo_Campo_Editado = gCorrigeNumeroInverte($Conteudo_Campo_Editado, 10);
        } else {
            $Conteudo_Campo_Editado = 0;
        }

        $id = $tabelaProduto . '_Id';
        $Gravar_Totalizacao_4 = $tabelaProduto . '_Gravar_Totalizacao_4';
        
        if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {

            $Valor_Preco_Bruto             = $tabelaProduto . '_Valor_Preco_Bruto';
            $Percentual_Desconto_Bruto     = $tabelaProduto . '_Percentual_Desconto_Bruto';
            $Valor_Preco_Original_Bruto    = $tabelaProduto . '_Valor_Preco_Original_Bruto';
            $Valor_Desconto_Unitario_Bruto = $tabelaProduto . '_Valor_Desconto_Unitario_Bruto';
            while ($mTabela = mysql_fetch_array($result)) {

                if ($campoAplicarDesconto == "tabela") {
                    // Se o desconto for em cima do preço de tabela, será aplicado o desconto com base no campo Valor_Preco_Original_Bruto
                    if (number_format($Conteudo_Campo_Editado, 2) != number_format($mTabela['$Percentual_Desconto_Bruto'], 2)) {
                        $sql = "UPDATE $tabelaProduto SET $Percentual_Desconto_Bruto     = $Conteudo_Campo_Editado,
                                                          $Valor_Preco_Bruto             = $Valor_Preco_Original_Bruto-($Valor_Preco_Original_Bruto*($Conteudo_Campo_Editado/100)),
                                                          $Valor_Desconto_Unitario_Bruto = ($Valor_Preco_Original_Bruto*($Conteudo_Campo_Editado/100))
                                                    WHERE $id = {$mTabela[$id]}";
                        mysql_query($sql);

                        $error = mysql_error();
                        if (!empty($error)) {
                            mysql_query("ROLLBACK");
                            return "Erro 2: " . $error;
                        }
                    }

                    $cSql = mysql_query("call $Gravar_Totalizacao_4($mTabela[$id]);");
                    
                    $error = mysql_error();
                    if (!empty($error)) {
                        mysql_query("ROLLBACK");
                        return "Erro 3: " . $error;
                    }
                } else {
                    // Se o desconto for em cima do preço do pedido, será aplicado o desconto com base no campo $Valor_Preco_Bruto
                    $sql = "UPDATE $tabelaProduto SET $Valor_Preco_Bruto             = $Valor_Preco_Bruto-($Valor_Preco_Bruto*($Conteudo_Campo_Editado/100)),
                                                      $Percentual_Desconto_Bruto     = if ($Valor_Preco_Original_Bruto > 0, (1-($Valor_Preco_Bruto/$Valor_Preco_Original_Bruto))*100, 0),
                                                      $Valor_Desconto_Unitario_Bruto = if ($Valor_Preco_Original_Bruto > 0, ($Valor_Preco_Original_Bruto-$Valor_Preco_Bruto), 0)
                                                WHERE $id = $mTabela[$id]";
                    mysql_query($sql);
                    $erro = mysql_error();
                    
                    if (!empty($erro)) {
                        mysql_query("ROLLBACK");
                        return "Erro 4: " . mysql_error();
                    }

                    $cSql = mysql_query("call $Gravar_Totalizacao_4($mTabela[$id]);");

                    $error = mysql_error();
                    if (!empty($error)) {
                        mysql_query("ROLLBACK");
                        return "Erro 5: " . $error;
                    }
                }
            }
        } else {
            while ($mTabela = mysql_fetch_array($result)) {

                $casasDecimaisPreco = $mTabela["{$tabelaCabecalho}_Casas_Decimais_Preco"];
                if(empty($casasDecimaisPreco) || $casasDecimaisPreco <= 0){
                    $casasDecimaisPreco = 4;
                }

                $Percentual_Desconto               = $tabelaProduto . '_Percentual_Desconto';
                $Valor_Preco_Original              = $tabelaProduto . '_Valor_Preco_Original';
                $Valor_Preco_Unitario              = $tabelaProduto . '_Valor_Preco_Unitario';
                $Valor_Desconto_Unitario           = $tabelaProduto . '_Valor_Desconto_Unitario';
                $Valor_Preco_Sem_Desconto_Unitario = $tabelaProduto . '_Valor_Preco_Sem_Desconto_Unitario';


                if ($campoAplicarDesconto == "tabela") {

                    // Verifica o percentual de desconto do clube de desconto (APP GRUPO DESCONTO)
                    $percentualDescontoClube = $this->verificaDescontoClubeBeneficio($tabelaCabecalho,$mTabela[$id]);
                    
                    if ($tabelaCabecalho == "T003") {
                        $sql = "UPDATE $tabelaProduto SET $Percentual_Desconto     = $Conteudo_Campo_Editado + $percentualDescontoClube,
                                                          $Valor_Preco_Unitario    = round($Valor_Preco_Original-($Valor_Preco_Original*(($Conteudo_Campo_Editado + $percentualDescontoClube)/100)), $casasDecimaisPreco),
                                                          $Valor_Desconto_Unitario = $Valor_Preco_Original*(($Conteudo_Campo_Editado + $percentualDescontoClube)/100)
                                                    WHERE $id = $mTabela[$id]";
                    } else { // T005 & T007
                        $sql = "UPDATE $tabelaProduto SET $Percentual_Desconto               = $Conteudo_Campo_Editado + $percentualDescontoClube,
                                                          $Valor_Preco_Sem_Desconto_Unitario = round($Valor_Preco_Original-($Valor_Preco_Original*(($Conteudo_Campo_Editado + $percentualDescontoClube)/100)), $casasDecimaisPreco),
                                                          $Valor_Preco_Unitario              = $Valor_Preco_Sem_Desconto_Unitario,
                                                          $Valor_Desconto_Unitario           = ($Valor_Preco_Original*(($Conteudo_Campo_Editado + $percentualDescontoClube)/100))
                                                    WHERE $id = $mTabela[$id]";
                    }
                    mysql_query($sql);
                    
                    $error = mysql_error();
                    if (!empty($error)) {
                        mysql_query("ROLLBACK");
                        return "Erro 6: " . $error;
                    }

                    $cSql = mysql_query("call $Gravar_Totalizacao_4($mTabela[$id]);");

                    $error = mysql_error();
                    if (!empty($error)) {
                        mysql_query("ROLLBACK");
                        return "Erro 7: " . $error;
                    }
                } else {

                    if ($tabelaCabecalho == "T003") {                       
                        $sql = "UPDATE $tabelaProduto 
                                        SET $Percentual_Desconto = if($Valor_Preco_Original > 0, 100-(($Valor_Preco_Unitario-($Valor_Preco_Unitario*($Conteudo_Campo_Editado/100)))/$Valor_Preco_Original)*100,0)
                                    WHERE $id = $mTabela[$id]";
                    } else { // T005 & T007
                        $sql = "UPDATE $tabelaProduto SET 
                                                          $Valor_Preco_Sem_Desconto_Unitario = round($Valor_Preco_Sem_Desconto_Unitario-($Valor_Preco_Sem_Desconto_Unitario*($Conteudo_Campo_Editado/100)), $casasDecimaisPreco),
                                                          $Valor_Preco_Unitario              = $Valor_Preco_Sem_Desconto_Unitario,
                                                          $Percentual_Desconto               = if($Valor_Preco_Original > 0, (1-($Valor_Preco_Sem_Desconto_Unitario/$Valor_Preco_Original))*100, 10),
                                                          $Valor_Desconto_Unitario           = if($Valor_Preco_Original > 0, ($Valor_Preco_Original-$Valor_Preco_Sem_Desconto_Unitario), 10)
                                                    WHERE $id = $mTabela[$id]";
                    }
                    mysql_query($sql);
               
                    $error = mysql_error();
                    if (!empty($error)) {
                        mysql_query("ROLLBACK");
                        return "Erro 8: " . $error;
                    }

                    $cSql = mysql_query("call $Gravar_Totalizacao_4($mTabela[$id]);");

                    $error = mysql_error();
                    if (!empty($error)) {
                        mysql_query("ROLLBACK");
                        return "Erro 9: " . $error;
                    }
                }
            }

            if($g['C031']['utilizaUnidadeConversaoVenda'] == 'S'){
                // Se utilizar unidade conversão vai recalcular os campos de valor comercial ao aplicar desconto
                require_once('bibliotecas/classes/CAD002.php');
                $CAD002 = new CAD002();                
                $conversao = mysql_query("SELECT {$id} as IdProduto 
                                            FROM {$tabelaProduto}
                                       LEFT JOIN {$tabelaCabecalho} ON {$tabelaCabecalho}_Id = {$tabelaProduto}_{$tabelaCabecalho}_Id
                                       LEFT JOIN {$tabelaCabecalho}A ON {$tabelaCabecalho}_Id = {$tabelaCabecalho}A_{$tabelaCabecalho}_Id
                                           WHERE {$fk} = {$idTabelaCabecalho}
                                             AND {$tabelaCabecalho}A_Flag_Conversao_Unidade = 'S'");

                while($mConversao = mysql_fetch_array($conversao)){
                    if($tabelaProduto == 'T004'){
                        $CAD002->conversaoUnidadeProduto($mConversao['IdProduto'], false, false, true);
                    }elseif($tabelaProduto == 'T006'){
                        $CAD002->conversaoUnidadeProduto(false, $mConversao['IdProduto'], false, true);
                    }elseif($tabelaProduto == 'T008'){
                        $CAD002->conversaoUnidadeProduto(false, false, $mConversao['IdProduto'], true);
                    }
                }
            }
        }

        mysql_query("COMMIT");
        return true;
    }

    private function calcularValoresAplicarDescontoEmEtapas($mTabela, $desconto, $campoAplicarDesconto, $tabelaCabecalho, $tabelaProduto) {
        global $g;

        $retorno = array(
            'itemId' => $mTabela["{$tabelaProduto}_Id"],
            'descontoNormalizado' => $desconto,
            'campoAplicarDesconto' => $campoAplicarDesconto,
            'modoPreco' => $g['C031']['precopadraoVendaProduto'],
            'atualizarConversao' => false,
            'campos' => array()
        );

        if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
            if ($campoAplicarDesconto == 'tabela') {
                if (number_format($desconto, 2) == number_format($mTabela["{$tabelaProduto}_Percentual_Desconto_Bruto"], 2)) {
                    $retorno['campos'] = array();
                    return $retorno;
                }

                $valorPrecoBruto = $mTabela["{$tabelaProduto}_Valor_Preco_Original_Bruto"] - ($mTabela["{$tabelaProduto}_Valor_Preco_Original_Bruto"] * ($desconto / 100));
                $valorDescontoUnitarioBruto = ($mTabela["{$tabelaProduto}_Valor_Preco_Original_Bruto"] * ($desconto / 100));

                $retorno['campos'] = array(
                    "{$tabelaProduto}_Percentual_Desconto_Bruto" => $desconto,
                    "{$tabelaProduto}_Valor_Preco_Bruto" => $valorPrecoBruto,
                    "{$tabelaProduto}_Valor_Desconto_Unitario_Bruto" => $valorDescontoUnitarioBruto
                );

                return $retorno;
            }

            $valorPrecoBruto = $mTabela["{$tabelaProduto}_Valor_Preco_Bruto"] - ($mTabela["{$tabelaProduto}_Valor_Preco_Bruto"] * ($desconto / 100));
            $percentualDescontoBruto = ($mTabela["{$tabelaProduto}_Valor_Preco_Original_Bruto"] > 0)
                ? (1 - ($valorPrecoBruto / $mTabela["{$tabelaProduto}_Valor_Preco_Original_Bruto"])) * 100
                : 0;
            $valorDescontoUnitarioBruto = ($mTabela["{$tabelaProduto}_Valor_Preco_Original_Bruto"] > 0)
                ? ($mTabela["{$tabelaProduto}_Valor_Preco_Original_Bruto"] - $valorPrecoBruto)
                : 0;

            $retorno['campos'] = array(
                "{$tabelaProduto}_Valor_Preco_Bruto" => $valorPrecoBruto,
                "{$tabelaProduto}_Percentual_Desconto_Bruto" => $percentualDescontoBruto,
                "{$tabelaProduto}_Valor_Desconto_Unitario_Bruto" => $valorDescontoUnitarioBruto
            );

            return $retorno;
        }

        $casasDecimaisPreco = $mTabela["{$tabelaCabecalho}_Casas_Decimais_Preco"];
        if (empty($casasDecimaisPreco) || $casasDecimaisPreco <= 0) {
            $casasDecimaisPreco = 4;
        }

        if ($campoAplicarDesconto == 'tabela') {
            $percentualDescontoClube = $this->verificaDescontoClubeBeneficio($tabelaCabecalho, $mTabela["{$tabelaProduto}_Id"]);
            $percentualDesconto = $desconto + $percentualDescontoClube;
            if ($tabelaCabecalho == 'T003') {
                $retorno['campos'] = array(
                    "{$tabelaProduto}_Percentual_Desconto" => $percentualDesconto,
                    "{$tabelaProduto}_Valor_Preco_Unitario" => round($mTabela["{$tabelaProduto}_Valor_Preco_Original"] - ($mTabela["{$tabelaProduto}_Valor_Preco_Original"] * ($percentualDesconto / 100)), $casasDecimaisPreco),
                    "{$tabelaProduto}_Valor_Desconto_Unitario" => $mTabela["{$tabelaProduto}_Valor_Preco_Original"] * ($percentualDesconto / 100)
                );
            } else {
                $retorno['campos'] = array(
                    "{$tabelaProduto}_Percentual_Desconto" => $percentualDesconto,
                    "{$tabelaProduto}_Valor_Preco_Sem_Desconto_Unitario" => round($mTabela["{$tabelaProduto}_Valor_Preco_Original"] - ($mTabela["{$tabelaProduto}_Valor_Preco_Original"] * ($percentualDesconto / 100)), $casasDecimaisPreco),
                    "{$tabelaProduto}_Valor_Preco_Unitario" => round($mTabela["{$tabelaProduto}_Valor_Preco_Original"] - ($mTabela["{$tabelaProduto}_Valor_Preco_Original"] * ($percentualDesconto / 100)), $casasDecimaisPreco),
                    "{$tabelaProduto}_Valor_Desconto_Unitario" => $mTabela["{$tabelaProduto}_Valor_Preco_Original"] * ($percentualDesconto / 100)
                );
            }
        } else {
            if ($tabelaCabecalho == 'T003') {
                $retorno['campos'] = array(
                    "{$tabelaProduto}_Percentual_Desconto" => ($mTabela["{$tabelaProduto}_Valor_Preco_Original"] > 0)
                        ? 100 - ((($mTabela["{$tabelaProduto}_Valor_Preco_Unitario"] - ($mTabela["{$tabelaProduto}_Valor_Preco_Unitario"] * ($desconto / 100))) / $mTabela["{$tabelaProduto}_Valor_Preco_Original"]) * 100)
                        : 0
                );
            } else {
                $valorPrecoSemDescontoUnitario = round($mTabela["{$tabelaProduto}_Valor_Preco_Sem_Desconto_Unitario"] - ($mTabela["{$tabelaProduto}_Valor_Preco_Sem_Desconto_Unitario"] * ($desconto / 100)), $casasDecimaisPreco);
                $retorno['campos'] = array(
                    "{$tabelaProduto}_Valor_Preco_Sem_Desconto_Unitario" => $valorPrecoSemDescontoUnitario,
                    "{$tabelaProduto}_Valor_Preco_Unitario" => $valorPrecoSemDescontoUnitario,
                    "{$tabelaProduto}_Percentual_Desconto" => ($mTabela["{$tabelaProduto}_Valor_Preco_Original"] > 0)
                        ? (1 - ($valorPrecoSemDescontoUnitario / $mTabela["{$tabelaProduto}_Valor_Preco_Original"])) * 100
                        : 10,
                    "{$tabelaProduto}_Valor_Desconto_Unitario" => ($mTabela["{$tabelaProduto}_Valor_Preco_Original"] > 0)
                        ? ($mTabela["{$tabelaProduto}_Valor_Preco_Original"] - $valorPrecoSemDescontoUnitario)
                        : 10
                );
            }
        }

        $retorno['atualizarConversao'] = $g['C031']['utilizaUnidadeConversaoVenda'] == 'S' && $mTabela["{$tabelaCabecalho}A_Flag_Conversao_Unidade"] == 'S';

        return $retorno;
    }

    private function finalizaAplicarDescontoEmEtapas($cache) {
        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        if (!empty($cache['contexto']['itensConversao'])) {
            foreach ($cache['contexto']['itensConversao'] as $itemId) {
                if ($cache['contexto']['tabelaProduto'] == 'T004') {
                    $CAD002->conversaoUnidadeProduto($itemId, false, false, true);
                } elseif ($cache['contexto']['tabelaProduto'] == 'T006') {
                    $CAD002->conversaoUnidadeProduto(false, $itemId, false, true);
                } elseif ($cache['contexto']['tabelaProduto'] == 'T008') {
                    $CAD002->conversaoUnidadeProduto(false, false, $itemId, true);
                }
                $error = mysql_error();
                if (!empty($error)) {
                    mysql_query("ROLLBACK");
                    return "Erro ao recalcular conversão do item {$itemId}: {$error}";
                }
            }
        }

        mysql_query("CALL {$cache['contexto']['tabelaCabecalho']}_Gravar_Totalizacao_4('{$cache['contexto']['idTabelaCabecalho']}')");
        $error = mysql_error();
        if (!empty($error)) {
            mysql_query("ROLLBACK");
            return "Erro ao gravar totalização do cabeçalho: {$error}";
        }

        mysql_query("COMMIT");
        return true;
    }

    private function rollbackAplicarDescontoEmEtapas($cache, $mensagemErroOriginal) {
        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        $itensProcessados = array_reverse($cache['itensProcessados']);
        foreach ($itensProcessados as $itemProcessado) {
            if (!empty($itemProcessado['snapshot'])) {
                $set = array();
                foreach ($itemProcessado['snapshot'] as $campo => $valor) {
                    $set[] = "{$campo} = '" . mysql_real_escape_string($valor) . "'";
                }

                if (!empty($set)) {
                    mysql_query("UPDATE {$cache['contexto']['tabelaProduto']} SET " . implode(', ', $set) . " WHERE {$cache['contexto']['tabelaProduto']}_Id = '{$itemProcessado['itemId']}'");
                    $error = mysql_error();
                    if (!empty($error)) {
                        mysql_query("ROLLBACK");
                        return "{$mensagemErroOriginal}<br>Erro ao reverter o item {$itemProcessado['itemId']}: {$error}";
                    }
                }
            }

            if (!empty($cache['contexto']['itensConversao'][$itemProcessado['itemId']])) {
                if ($cache['contexto']['tabelaProduto'] == 'T004') {
                    $CAD002->conversaoUnidadeProduto($itemProcessado['itemId'], false, false, true);
                } elseif ($cache['contexto']['tabelaProduto'] == 'T006') {
                    $CAD002->conversaoUnidadeProduto(false, $itemProcessado['itemId'], false, true);
                } elseif ($cache['contexto']['tabelaProduto'] == 'T008') {
                    $CAD002->conversaoUnidadeProduto(false, false, $itemProcessado['itemId'], true);
                }

                $error = mysql_error();
                if (!empty($error)) {
                    mysql_query("ROLLBACK");
                    return "{$mensagemErroOriginal}<br>Erro ao reverter conversão do item {$itemProcessado['itemId']}: {$error}";
                }
            }

            mysql_query("CALL {$cache['contexto']['tabelaProduto']}_Gravar_Totalizacao_4({$itemProcessado['itemId']})");
            $error = mysql_error();
            if (!empty($error)) {
                mysql_query("ROLLBACK");
                return "{$mensagemErroOriginal}<br>Erro ao reverter totalização do item {$itemProcessado['itemId']}: {$error}";
            }
        }

        mysql_query("CALL {$cache['contexto']['tabelaCabecalho']}_Gravar_Totalizacao_4('{$cache['contexto']['idTabelaCabecalho']}')");
        $error = mysql_error();
        if (!empty($error)) {
            mysql_query("ROLLBACK");
            return "{$mensagemErroOriginal}<br>Erro ao reverter totalização do cabeçalho: {$error}";
        }

        mysql_query("COMMIT");
        return $mensagemErroOriginal . "<br>As alterações realizadas nas etapas anteriores foram revertidas.";
    }

    /**
     * validaAplicarDescontoEmEtapas
     *
     * @param Array $params
     * @return Bool|String
     */
    public function validaAplicarDescontoEmEtapas($params) {
        global $g;
        if (empty($params['tabelaCabecalho']) || empty($params['idTabelaCabecalho']) || empty($params['tabelaProduto'])) {
            return "Parâmetros obrigatórios não informados para aplicar desconto em etapas.";
        }

        if (
            ($params['tabelaCabecalho'] == 'T003' && $params['tabelaProduto'] != 'T004') ||
            ($params['tabelaCabecalho'] == 'T005' && $params['tabelaProduto'] != 'T006') ||
            ($params['tabelaCabecalho'] == 'T007' && $params['tabelaProduto'] != 'T008') ||
            !in_array($params['tabelaCabecalho'], array('T003', 'T005', 'T007'))
        ) {
            return "O desconto em etapas está disponível somente para orçamento, pedido e nota fiscal com as tabelas compatíveis.";
        }

        if ($params['campoAplicarDesconto'] != 'tabela' && $params['campoAplicarDesconto'] != 'pedido') {
            return "Base de desconto inválida, por favor verifique.";
        }

        if ($verificaDesconto = $this->VerificaDesconto($params)) {
            return $verificaDesconto;
        }

        $sql = mysql_query("SELECT {$params['tabelaProduto']}_Id FROM {$params['tabelaProduto']} WHERE {$params['tabelaProduto']}_{$params['tabelaCabecalho']}_Id = '{$params['idTabelaCabecalho']}'");
        if (mysql_num_rows($sql) <= 0) {
            return "Nenhum item foi encontrado para aplicar desconto, por favor verifique!";
        }

        $arquivoCache = "/tmp/ven001-aplicar-desconto-etapas-{$g['usuarioAtual']}-{$params['tabelaCabecalho']}-{$params['idTabelaCabecalho']}";
        if (file_exists($arquivoCache)) {
            @unlink($arquivoCache);
        }

        return true;
    }

    /**
     * VEN001aplicarDescontoEmEtapas
     *
     * @param Array $dados
     * @param Array|Bool $cacheAnterior
     * @return Bool|String
     */
    public function VEN001aplicarDescontoEmEtapas($dados, $cacheAnterior = false) {
        global $g;
        $mapaTabelas = array(
            'T003' => 'T004',
            'T005' => 'T006',
            'T007' => 'T008'
        );

        $tabelaCabecalho = false;
        foreach ($mapaTabelas as $header => $item) {
            if (!empty($dados["{$header}_Id"]) && !empty($dados["{$item}_Id"])) {
                $tabelaCabecalho = $header;
                break;
            }
        }

        if ($tabelaCabecalho === false) {
            return "Parâmetros do cabeçalho/item não encontrados para aplicar desconto em etapas.";
        }

        $tabelaProduto = $mapaTabelas[$tabelaCabecalho];

        $dados['Desconto_Aplicar'] = !empty($dados['Desconto_Aplicar']) ? gCorrigeNumeroInverte($dados['Desconto_Aplicar'], 10) : 0;

        $sql = "SELECT
                    {$tabelaProduto}_Id,
                    {$tabelaProduto}_{$tabelaCabecalho}_Id,
                    {$tabelaProduto}_Percentual_Desconto,
                    {$tabelaProduto}_Percentual_Desconto_Bruto,
                    {$tabelaProduto}_Valor_Preco_Unitario,
                    {$tabelaProduto}_Valor_Preco_Original,
                    {$tabelaProduto}_Valor_Preco_Bruto,
                    {$tabelaProduto}_Valor_Preco_Original_Bruto,
                    {$tabelaProduto}_Valor_Preco_Sem_Desconto_Unitario,
                    {$tabelaProduto}_Valor_Desconto_Unitario,
                    {$tabelaProduto}_Valor_Desconto_Unitario_Bruto,
                    {$tabelaCabecalho}_Casas_Decimais_Preco,
                    {$tabelaCabecalho}A_Flag_Conversao_Unidade
                FROM {$tabelaProduto}
                    LEFT JOIN {$tabelaCabecalho} ON {$tabelaCabecalho}_Id = {$tabelaProduto}_{$tabelaCabecalho}_Id
                    LEFT JOIN {$tabelaCabecalho}A ON {$tabelaCabecalho}A_{$tabelaCabecalho}_Id = {$tabelaCabecalho}_Id
                WHERE {$tabelaProduto}_Id = '{$dados["{$tabelaProduto}_Id"]}'
                LIMIT 1";
        $mTabela = mysql_fetch_assoc(mysqli_query($sql));
        $arquivoCache = "/tmp/ven001-aplicar-desconto-etapas-{$g['usuarioAtual']}-{$tabelaCabecalho}-{$dados["{$tabelaCabecalho}_Id"]}";
        if (empty($mTabela["{$tabelaProduto}_Id"])) {
            if (file_exists($arquivoCache)) {
                @unlink($arquivoCache);
            }
            return "Item {$dados["{$tabelaProduto}_Id"]} não encontrado para aplicar desconto em etapas.";
        }

        $cache = false;
        if (file_exists($arquivoCache)) {
            $cache = @unserialize(file_get_contents($arquivoCache));
            $cache = is_array($cache) ? $cache : false;
        }

        if ($cache === false) {
            $cache = array(
                'contexto' => array(
                    'tabelaCabecalho' => $tabelaCabecalho,
                    'tabelaProduto' => $tabelaProduto,
                    'idTabelaCabecalho' => $dados["{$tabelaCabecalho}_Id"],
                    'campoAplicarDesconto' => $dados['Campo_Aplicar_Desconto'],
                    'desconto' => $dados['Desconto_Aplicar'],
                    'itensConversao' => array()
                ),
                'itensProcessados' => array()
            );
        }

        $calculo = $this->calcularValoresAplicarDescontoEmEtapas($mTabela, $dados['Desconto_Aplicar'], $dados['Campo_Aplicar_Desconto'], $tabelaCabecalho, $tabelaProduto);
        if ($calculo['atualizarConversao']) {
            $cache['contexto']['itensConversao'][$dados["{$tabelaProduto}_Id"]] = $dados["{$tabelaProduto}_Id"];
        }

        $snapshot = array();
        foreach (array_keys($calculo['campos']) as $campo) {
            $snapshot[$campo] = $mTabela[$campo];
        }

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        if (!empty($calculo['campos'])) {
            $set = array();
            foreach ($calculo['campos'] as $campo => $valor) {
                $set[] = "{$campo} = '" . mysql_real_escape_string($valor) . "'";
            }

            mysql_query("UPDATE {$tabelaProduto} SET " . implode(', ', $set) . " WHERE {$tabelaProduto}_Id = '{$dados["{$tabelaProduto}_Id"]}'");
            $error = mysql_error();
            if (!empty($error)) {
                mysql_query("ROLLBACK");
                if (file_exists($arquivoCache)) {
                    @unlink($arquivoCache);
                }
                if (!empty($cache['itensProcessados'])) {
                    return $this->rollbackAplicarDescontoEmEtapas($cache, "Erro ao aplicar desconto no item {$dados["{$tabelaProduto}_Id"]}: {$error}");
                }
                return "Erro ao aplicar desconto no item {$dados["{$tabelaProduto}_Id"]}: {$error}";
            }
        }

        mysql_query("CALL {$tabelaProduto}_Gravar_Totalizacao_4({$dados["{$tabelaProduto}_Id"]})");
        $error = mysql_error();
        if (!empty($error)) {
            mysql_query("ROLLBACK");
            if (file_exists($arquivoCache)) {
                @unlink($arquivoCache);
            }
            if (!empty($cache['itensProcessados'])) {
                return $this->rollbackAplicarDescontoEmEtapas($cache, "Erro ao gravar totalização do item {$dados["{$tabelaProduto}_Id"]}: {$error}");
            }
            return "Erro ao gravar totalização do item {$dados["{$tabelaProduto}_Id"]}: {$error}";
        }

        mysql_query("COMMIT");

        $cache['itensProcessados'][] = array(
            'itemId' => $dados["{$tabelaProduto}_Id"],
            'snapshot' => $snapshot
        );

        if (file_put_contents($arquivoCache, serialize($cache)) === false) {
            if (file_exists($arquivoCache)) {
                @unlink($arquivoCache);
            }
            return $this->rollbackAplicarDescontoEmEtapas($cache, "Erro ao salvar o cache do desconto em etapas.");
        }

        if ((int) ($dados['Ultimo_Item'] ?? $dados["Ultimo_{$tabelaProduto}"] ?? 0) !== 1) {
            return true;
        }

        $retorno = $this->finalizaAplicarDescontoEmEtapas($cache);
        if ($retorno !== true) {
            $retorno = $this->rollbackAplicarDescontoEmEtapas($cache, $retorno);
        }
        if (file_exists($arquivoCache)) {
            @unlink($arquivoCache);
        }

        return $retorno;
    }

    public function VEN001aplicarDescontoReais($tabelaCabecalho, $idTabelaCabecalho, $tabelaProduto, $descontoReais) {
        global $g;
        $this->VEN001aplicarDesconto($tabelaCabecalho, $idTabelaCabecalho, $tabelaProduto, '0,0000000000', 'tabela');
        $Valor_Total = $tabelaCabecalho . '_Valor_Total'; // T00X_Valor_Total
        $id = $tabelaCabecalho . '_Id'; // T00X_Id
        $tabelaCabecalhoX = mysql_query("SELECT $Valor_Total FROM $tabelaCabecalho WHERE $id = '{$idTabelaCabecalho}'");
        $mtabelaCabecalhoX = mysql_fetch_array($tabelaCabecalhoX);
        $desconto = gCorrigeNumeroInverte($descontoReais);
        $percentualDesconto = gCorrigeNumero(($desconto/$mtabelaCabecalhoX[$Valor_Total]) * 100, 10);
        return $this->VEN001aplicarDesconto($tabelaCabecalho, $idTabelaCabecalho, $tabelaProduto, $percentualDesconto, 'tabela');
    }
    
    public function VEN001aplicarDescontoTotal($tabelaCabecalho, $idTabelaCabecalho, $tabelaProduto, $descontoValorTotal) {
        global $g;
        
        $this->VEN001aplicarDesconto($tabelaCabecalho, $idTabelaCabecalho, $tabelaProduto, '0,0000000000', 'tabela'); // Reset percentage discount

        $id = $tabelaCabecalho.'_Id';
        $valor_Total = $tabelaCabecalho.'_Valor_Total';
        $valor_Total_Produtos_Sem_Desconto = $tabelaCabecalho.'_Valor_Total_Produtos_Sem_Desconto';
        $result = mysql_query("SELECT $valor_Total, $valor_Total_Produtos_Sem_Desconto FROM $tabelaCabecalho WHERE $id = '{$idTabelaCabecalho}'");
        $mResult = mysql_fetch_array($result);
        $desconto = gCorrigeNumeroInverte($descontoValorTotal);

        if ($desconto > 0) {
            $desconto = $mResult[$valor_Total] - $desconto;
            $percentualDesconto = gCorrigeNumero(($desconto*100) / $mResult[$valor_Total], 10);
        }

        return $this->VEN001aplicarDesconto($tabelaCabecalho, $idTabelaCabecalho, $tabelaProduto, $percentualDesconto, 'tabela');
    }

    /**
     * ven001AplicarPercentualDesconto
     *
     * @param String $T004_Id
     * @param String $campoEditado
     * @param String $valorAntigo
     * @param String $novoValor
     * @return String
     */    
    public function ven001AplicarPercentualDesconto($T004_Id, $campoEditado, $valorAntigo, $novoValor)
    {
        global $g;
        if ($T004_Id <= 0) {
            return "T004_Id nao existe!";
        }
        $Campo_Editado          = $campoEditado;
        $Conteudo_Campo_Editado = $novoValor;
        
        if (empty($Campo_Editado)) {
            return "Campo_Editado nao existe!";
        }
        
        if ($T004_Id <= 0) {
            return "T004_Id nao existe!";
        }
        
        $C007  = mysql_query("SELECT * FROM C007 WHERE C007_Id='{$g['usuarioAtual']}'");
        $mC007 = mysql_fetch_array($C007);
        
        $cC004 = mysql_query("SELECT * FROM C004 WHERE C004_Id='{$g['empresaAtual']}'");
        $mC004 = mysql_fetch_array($cC004);
        
        $cClausula = " SELECT ";
        $cClausula .= " *  ";
        $cClausula .= " FROM T004 ";
        $cClausula .= " LEFT JOIN D009 ON D009_Id=T004_D009_Id ";
        $cClausula .= " LEFT JOIN D049 ON D049_Id=D009_D049_Id ";
        $cClausula .= " LEFT JOIN D001 ON D001_Id=D049_D001_Id ";
        $cClausula .= " LEFT JOIN D037 ON D037_Id=T004_D037_Id ";
        $cClausula .= " WHERE T004_Id='$T004_Id' ";
        $T004  = mysql_query("$cClausula");
        $mT004 = mysql_fetch_array($T004);
        
        if ($Campo_Editado == 'T004_Valor_Preco_Unitario') {
            
            $Conteudo_Campo_Editado = gCorrigeNumeroInverte($Conteudo_Campo_Editado);
            
            if ($Conteudo_Campo_Editado < $mT004['T004_Valor_Custo_Unitario']) {
                //return 'Preço inválido';
            }
            
            if ($mT004['T004_Valor_Preco_Original'] > 0) {
                $campoUpdate = 'T004_Valor_Preco_Unitario';
            } else {
                $campoUpdate = 'T004_Valor_Preco_Sem_Desconto_Unitario';
            }
            mysql_query("UPDATE T004 SET {$campoUpdate}='{$Conteudo_Campo_Editado}',
                                        T004_Percentual_Desconto=if(T004_Valor_Preco_Original>0,(1-(T004_Valor_Preco_Unitario/T004_Valor_Preco_Original))*100,0),
                                        T004_Valor_Desconto_Unitario=if(T004_Valor_Preco_Original>0,(T004_Valor_Preco_Original*($Conteudo_Campo_Editado/100)),0)
                              WHERE T004_Id='$T004_Id'");
            
        } elseif ($Campo_Editado == 'T004_Valor_Preco_Bruto') {
            $Conteudo_Campo_Editado = gCorrigeNumeroInverte($Conteudo_Campo_Editado, $mC004['C004_Casas_Decimais_Preco'] + 1);

            mysql_query("UPDATE T004 SET T004_Valor_Preco_Bruto='{$Conteudo_Campo_Editado}',
                                         T004_Percentual_Desconto_Bruto=if(T004_Valor_Preco_Original_Bruto>0,(1-(T004_Valor_Preco_Bruto/T004_Valor_Preco_Original_Bruto))*100,0),
                                         T004_Valor_Desconto_Unitario_Bruto=if(T004_Valor_Preco_Original_Bruto>0,(T004_Valor_Preco_Original_Bruto*($Conteudo_Campo_Editado/100)),0)
                                   WHERE T004_Id='$T004_Id'");                                   
        } elseif ($Campo_Editado == 'T004_Percentual_Desconto') {
            //return "$T004_Id, $campoEditado, $valorAntigo, $novoValor, {$mT004['T004_Percentual_Desconto']}";
            //T004_Valor_Preco_Unitario
            if (!empty($Conteudo_Campo_Editado)) {
                $Conteudo_Campo_Editado = gCorrigeNumeroInverte($Conteudo_Campo_Editado, 5);
            } else {
                $Conteudo_Campo_Editado = 0;
            }
            //return "1: ".number_format($Conteudo_Campo_Editado,2)."<br />2: ".number_format($mT004['T004_Percentual_Desconto'],2);
            
            if (($mT004['T004_Valor_Preco_Original'] - ($mT004['T004_Valor_Preco_Original'] * ($Conteudo_Campo_Editado / 100))) < $mT004['T004_Valor_Custo_Unitario']) {
                //return "Desconto inválido";
            }
            mysql_query("UPDATE T004 SET T004_Percentual_Desconto='$Conteudo_Campo_Editado',
                                        T004_Valor_Preco_Unitario=T004_Valor_Preco_Original-(T004_Valor_Preco_Original*($Conteudo_Campo_Editado/100)),
                                        T004_Valor_Desconto_Unitario=(T004_Valor_Preco_Original*($Conteudo_Campo_Editado/100))
                                  WHERE T004_Id='$T004_Id'");

            if($g['C031']['utilizaUnidadeConversaoVenda'] == 'S'){
                $T003A = mysql_query("SELECT T003A_Id
                                        FROM T003A
                                       WHERE T003A_T003_Id = '{$mT004['T004_T003_Id']}'
                                         AND T003A_Flag_Conversao_Unidade = 'S'");

                if(mysql_num_rows($T003A) > 0 ){
                    // Se utiliza unidade conversão precisa atualizar os dados da tabela estendida
                    require_once('bibliotecas/classes/CAD002.php');
                    $CAD002 = new CAD002();
                    $CAD002->conversaoUnidadeProduto($T004_Id, false, false, true);
                }          
            }
            
        } elseif ($Campo_Editado == 'T004_Percentual_Desconto_Bruto') {
            //return "$T004_Id, $campoEditado, $valorAntigo, $novoValor, {$mT004['T004_Percentual_Desconto']}";
            if (!empty($Conteudo_Campo_Editado)) {
                $Conteudo_Campo_Editado = formata_valor_gravar($Conteudo_Campo_Editado, 6);
            } else {
                $Conteudo_Campo_Editado = 0;
            }
            //return "1: ".number_format($valorAntigo,2)."<br />2: ".number_format($mT004['T004_Percentual_Desconto_Bruto'],2);
            
            if (number_format($Conteudo_Campo_Editado, 2) != number_format($mT004['T004_Percentual_Desconto_Bruto'], 2)) {
                if (($mT004['T004_Valor_Preco_Original_Bruto'] - ($mT004['T004_Valor_Preco_Original_Bruto'] * ($Conteudo_Campo_Editado / 100))) < $mT004['T004_Valor_Custo_Unitario']) {
                    //return "Desconto inválido";
                }
                mysql_query("UPDATE T004 SET T004_Percentual_Desconto_Bruto='$Conteudo_Campo_Editado',
                                            T004_Valor_Preco_Bruto=T004_Valor_Preco_Original_Bruto-(T004_Valor_Preco_Original_Bruto*($Conteudo_Campo_Editado/100)),
                                            T004_Valor_Desconto_Unitario_Bruto=(T004_Valor_Preco_Original_Bruto*($Conteudo_Campo_Editado/100))
                                      WHERE T004_Id='$T004_Id'");
            }
            
        } elseif ($Campo_Editado == 'T004_Peso_Unitario') {
            
            $Conteudo_Campo_Editado = formata_valor_gravar($Conteudo_Campo_Editado, 4);
            
            mysql_query("UPDATE T004 SET $Campo_Editado='$Conteudo_Campo_Editado'
                               WHERE T004_Id='$T004_Id'");
            
        } elseif ($Campo_Editado == 'T004_Hora_Entrega') {
            mysql_query("UPDATE T004 SET $Campo_Editado='$Conteudo_Campo_Editado'
                               WHERE T004_Id='$T004_Id'");
            
        } elseif ($Campo_Editado == 'T004_Observacao') {
            mysql_query("UPDATE T004 SET $Campo_Editado='$Conteudo_Campo_Editado'
                               WHERE T004_Id='$T004_Id'");
        } elseif ($Campo_Editado == 'T004_D037_Id') {
            
            $cClausula = " SELECT ";
            $cClausula .= " D009_Id, ";
            $cClausula .= " D037_Unidade, ";
            $cClausula .= " T004_Peso_Unitario, ";
            $cClausula .= " T004_Quantidade ";
            $cClausula .= " FROM T004 ";
            $cClausula .= " LEFT JOIN D009 ON D009_Id=T004_D009_Id ";
            $cClausula .= " LEFT JOIN D049 ON D049_Id=D009_D049_Id ";
            $cClausula .= " LEFT JOIN D001 ON D001_Id=D049_D001_Id ";
            $cClausula .= " LEFT JOIN D037 ON D037_Id='$Conteudo_Campo_Editado'";
            $cClausula .= " WHERE T004_Id='$T004_Id' ";
            $T004  = mysql_query("$cClausula");
            $mT004 = mysql_fetch_array($T004);
            
            mysql_query("UPDATE T004 SET $Campo_Editado='$Conteudo_Campo_Editado'
                               WHERE T004_Id='$T004_Id'");
            
        } else if ($Campo_Editado == 'T004_Codigo_Substituto') {
            
            $Conteudo_Campo_Editado = strtoupper($Conteudo_Campo_Editado);
            $Conteudo_Campo_Editado = addslashes($Conteudo_Campo_Editado);
            
            mysql_query("UPDATE T004 SET $Campo_Editado='$Conteudo_Campo_Editado'
                                  WHERE T004_Id='$T004_Id'");
            
            $D001  = mysql_query("SELECT D001_Id,
                                     D049_Id,
                                     D009_Id
                                FROM T004
                           LEFT JOIN D009 ON D009_Id=T004_D009_Id
                           LEFT JOIN D049 ON D049_Id=D009_D049_Id
                           LEFT JOIN D001 ON D001_Id=D049_D001_Id
                               WHERE T004_Id='$T004_Id'");
            $mD001 = mysql_fetch_array($D001);

            /*            
            $D083 = mysql_query("SELECT D083_Id,D083_Flag_Cliente,D083_Codigo_Produto_Fornecedor FROM D083 WHERE D083_D001_Id='{$mD001['D001_Id']}' AND D083_D049_Id='{$mD001['D049_Id']}' AND D083_D024_Id='$D024_Id'");
            if (mysql_num_rows($D083) <= 0) {
                mysql_query("insert into D083 (D083_D049_Id,D083_D001_Id,D083_D024_Id,D083_Data_Cadastro,D083_Flag_Cliente,D083_Codigo_Produto_Fornecedor) VALUES ('{$mD001['D049_Id']}','{$mD001['D001_Id']}','$D024_Id',curdate(),'S','$Conteudo_Campo_Editado')");
            } else {
                $mD083 = mysql_fetch_array($D083);
                mysql_query("UPDATE D083 SET D083_Flag_Cliente='S',D083_Codigo_Produto_Fornecedor='$Conteudo_Campo_Editado' WHERE D083_Id='{$mD083['D083_Id']}'");
            }
            */
            require_once("bibliotecas/classes/CAD002.php");
            $CAD002 = new CAD002();
            $CAD002->definirCodigoFornecedor($mD001['D009_Id'], $D024_Id, $Conteudo_Campo_Editado, '', true, false);
        } else {
            
            $Conteudo_Campo_Editado = strtoupper($Conteudo_Campo_Editado);
            $Conteudo_Campo_Editado = addslashes($Conteudo_Campo_Editado);

            mysql_query("UPDATE T004 SET $Campo_Editado='$Conteudo_Campo_Editado'
                                  WHERE T004_Id='$T004_Id'");
            
        }
        
        if ($Campo_Editado == 'T004_Item') {
            $T004  = mysql_query("SELECT T004_Item FROM T004 WHERE T004_Id='$T004_Id'");
            $mT004 = mysql_fetch_array($T004);
            
            if ($mT004['T004_Item'] > $Conteudo_Campo_Editado) {
                mysql_query("UPDATE T004 SET T004_Reordenar=0 WHERE T004_Id='$T004_Id'");
            } else {
                mysql_query("UPDATE T004 SET T004_Reordenar=2 WHERE T004_Id='$T004_Id'");
            }
        }
        
        $cSql = mysql_query("call T004_Gravar_Totalizacao_4('$T004_Id');");
        //$cSql=sql_query("call T003_Gravar_Totalizacao('$CRM_T003_Id');");
        return true;
    }

    /**
     * retornaCFOPContexto
     *
     * @param String $D024_Id
     * @param String $D009_Id
     * @param String $C004_Id
     * @param String $Destino
     * @param String $Origem
     * @param Bool $retornaLog
     * @param mixed ...$dadosExtras (1º extra pode ser array de contexto)
     * @return String/bool
     */     
    public function retornaCFOPContexto($D024_Id, $D009_Id = null, $C004_Id = null, $Destino = null, $Origem = null, $retornaLog = false, $OrigemPedido = null, $C004_Id_Estoque=null, $T008_Id = null, ...$dadosExtras)
    {

        /**
         * ======================================================================
         *  WRAPPER DE CONTEXTO PARA CÁLCULO DE CFOP (SEM ALTERAR retornaCFOP)
         * ======================================================================
         *
         * Esta função NÃO altera a lógica de cálculo do CFOP.
         * Ela existe para INJETAR UM CONTEXTO TEMPORÁRIO na execução de
         * retornaCFOP(), SEM alterar a assinatura original (evita quebrar
         * clientes com personalização/override e chamadas espalhadas no sistema).
         *
         * Por que existe:
         * - retornaCFOP() é função central/crítica e é chamada em vários fluxos
         * - alguns clientes têm override/personalização dessa função
         * - adicionar parâmetro novo na assinatura é arriscado (compatibilidade)
         *
         * Como funciona:
         * - Aceita os mesmos parâmetros de retornaCFOP()
         * - Permite passar dados adicionais via "...$dadosExtras"
         * - Convenção: se o 1º extra for um array, ele será usado como CONTEXTO
         *   (ex.: UF de entrega, flags temporárias, dados auxiliares de decisão)
         * - Armazena este contexto no global "$parametrosExtrasRetornaCFOP"
         * - Chama retornaCFOP() com a assinatura original
         * - Garante limpeza do global ao final usando try/finally
         *   (evita vazamento de contexto entre execuções/requests)
         *
         * IMPORTANTE / REGRAS:
         * - Não alterar a assinatura de retornaCFOP()
         * - O global "$parametrosExtrasRetornaCFOP" só deve ser lido dentro de
         *   retornaCFOP() (ou funções diretamente chamadas por ela) e apenas
         *   durante esta execução
         * - Não usar esse mecanismo para chamadas aninhadas/recursivas sem uma
         *   estrutura de “pilha” (esta implementação sobrescreve o valor global)
         *
         * Exemplo:
         *   $this->retornaCFOPContexto(
         *       $D024_Id, $D009_Id, $C004_Id, $Destino, $Origem, $retornaLog,
         *       $OrigemPedido, $C004_Id_Estoque, $T008_Id,
         *       ['ifUfEntrega' => '15', 'xxxxxx' => 'xxxxxx']
         *   );
         * ======================================================================
         */
                
        global $g, $parametrosExtrasRetornaCFOP;


        // Convenção: se passar um array no 1º extra, ele é o contexto
        $contexto = (isset($dadosExtras[0]) && is_array($dadosExtras[0])) ? $dadosExtras[0] : [];
        $parametrosExtrasRetornaCFOP = $contexto;

        try {
            return $this->retornaCFOP(
                $D024_Id,
                $D009_Id,
                $C004_Id,
                $Destino,
                $Origem,
                $retornaLog,
                $OrigemPedido,
                $C004_Id_Estoque,
                $T008_Id
            );
        } finally {
            unset($parametrosExtrasRetornaCFOP);
        }
    } 
    
    /**
     * retornaCFOP
     *
     * @param String $D024_Id
     * @param String $D009_Id
     * @param String $C004_Id
     * @param String $Destino
     * @param String $Origem
     * @param Bool $retornaLog
     * @return String/bool
     */     
    public function retornaCFOP($D024_Id, $D009_Id = null, $C004_Id = null, $Destino = null, $Origem = null, $retornaLog = false, $OrigemPedido = null, $C004_Id_Estoque=null, $T008_Id = null)
    {
        global $g, $parametrosExtrasRetornaCFOP;
        $tipo;
        $C004_Id = ($C004_Id == null) ? $g['empresaAtual'] : $C004_Id;
        $Retido = 'N';
        
        if ($T008_Id === null) {
            // Verifica se existe Saldo para retirar
            $sqlT289 = mysql_query("SELECT T289_Id FROM T289 WHERE T289_D009_Id = '{$D009_Id}' AND T289_Quantidade_Saldo > 0 AND T289_Valor_Base_ICMS_Substituicao_Unitario > 0");
            $mT289   = mysql_fetch_array($sqlT289);
            if ($mT289['T289_Id'] > 0){
                $Retido = 'S';
            }
        } else {
            $sqlT290 = mysql_query("SELECT T290_Id FROM T290 LEFT JOIN T289 ON T289_Id=T290_T289_Id WHERE T290_T008_Id = '{$T008_Id}' AND T289_Valor_Base_ICMS_Substituicao_Unitario > 0");
            $mT290   = mysql_fetch_array($sqlT290);
            if ($mT290['T290_Id'] > 0 ){
                // Verifica se o que foi pesquisado, possui ST 
                $Retido = 'S';
            } 
        }

        if ($D009_Id) {
            $tipo    = 'I';
            $retorno = mysql_query("SELECT D024_Flag_Contribuinte,
                                           D024_Flag_Pessoa_Fisica_Juridica,
                                           D024_Flag_Revenda_Consumidor,
                                           D024_Flag_ICMS_Substituicao_Tributaria_Substituto,
                                           D024_D006_Id,
                                           D018_UF,    
                                           D018_Id,                            
                                           D053_Flag_ST,
                                           D001_Flag_Tipo,
                                           D001_Peso_Unitario_Kg,
                                           D001_D060_Id,
                                           D001_D003_Id,
                                           D049_Flag_Nacional_Importado,
                                           D049_Origem_Mercadoria,
                                           D149_Flag_ST,
                                           D149_UF_ST,
                                           D149_Id,
                                           D005_Id,
                                           D005_Classificacao_Fiscal,
                                           C004_Codigo_Regime_Tributario,
                                           C004_D018_Id,
                                           C004_Id,
                                           D024_Codigo_Regime_Tributario,
                                           D049_Flag_Tipo
                                      FROM D024
                                 LEFT JOIN C004 ON C004_Id={$C004_Id}
                                 LEFT JOIN D018 ON D018_Id=D024_D018_Id
                                 LEFT JOIN D053 ON D053_D018_Id=D018_Id
                                 LEFT JOIN D009 ON D009_Id={$D009_Id}
                                 LEFT JOIN D049 ON D049_Id=D009_D049_Id
                                 LEFT JOIN D001 ON D001_Id=D049_D001_Id
                                 LEFT JOIN D005 ON D005_Id=D001_D005_Id
                                 LEFT JOIN D149 ON D005_Id=D149_D005_Id AND D149_C004_Id={$C004_Id}
                                     WHERE D024_Id={$D024_Id} AND D053_C004_Id={$C004_Id}
            ");
        } else {
            return 0;
        }
            
        $registro = mysql_fetch_array($retorno);
        
        if ($Origem != NULL) {
            $registro['D049_Origem_Mercadoria'] = $Origem;
        }

        if ($OrigemPedido != null && $OrigemPedido > 0) {
            if($C004_Id != $OrigemPedido){
                $D137_Origem_Pedido = 'E';
            } else {
                $D137_Origem_Pedido = 'I';
            }
        } else {
            $D137_Origem_Pedido = '';
        }

        if ($registro['D024_Flag_ICMS_Substituicao_Tributaria_Substituto']=='S'){
            $registro['D149_Flag_ST'] = 'N';
        }
        
        $D137_C004_Id = $registro['C004_Id'];
        
        $D137_C004_Codigo_Regime_Tributario = $registro['C004_Codigo_Regime_Tributario'];
        $D137_D024_Codigo_Regime_Tributario = $registro['D024_Codigo_Regime_Tributario'];
        
        $D137_Flag_Revenda_Consumidor = $registro['D024_Flag_Revenda_Consumidor'];
        
        // o valor do tipo "não contribuinte" que no cad cliente é 0 foi substituido por 9 
        $D137_D024_Flag_Contribuinte = $registro['D024_Flag_Contribuinte'];
        if ($D137_D024_Flag_Contribuinte == 0){
            $D137_D024_Flag_Contribuinte = 9;
        }
        
        if ($registro['D018_Id'] == $registro['C004_D018_Id']){
            $D137_Dentro_Fora_Estado = 'D';
        } else {
            $D137_Dentro_Fora_Estado = 'F';
        }

        $D137_UF_Tem_ST = $registro['D053_Flag_ST'];

        if ($registro['D149_Flag_ST']) {
            $D137_NCM_Tem_ST = $registro['D149_Flag_ST'];
        } else {
            $D137_NCM_Tem_ST = "";
        }

        $D137_D005_Id = $registro['D005_Id'];

        if ($registro['D024_Flag_Pessoa_Fisica_Juridica'] == "F"){
            $D137_Pessoa_Fisica_Juridica = "PF";
        } elseif ($registro['D024_Flag_Pessoa_Fisica_Juridica'] == "J") {
            $D137_Pessoa_Fisica_Juridica = "PJ";
        } elseif ($registro['D024_Flag_Pessoa_Fisica_Juridica'] == "I") {
            $D137_Pessoa_Fisica_Juridica = "PI";
        }
        
        /*if ($g['C031']['multimarcas'] == 'S') {
            if ($registro['D049_Flag_Nacional_Importado'] == "N") {
                $D137_Produzido_Adquirido = "A";
            } else if ($registro['D049_Flag_Nacional_Importado'] == "I") {
                $D137_Produzido_Adquirido = "P";
            } else {
                $D137_Produzido_Adquirido = "";
            }
        } else {
            $D137_Produzido_Adquirido = $registro['D049_Flag_Tipo'];
            if ($registro['D001_Flag_Tipo'] == "COMPRADO") {
                $D137_Produzido_Adquirido = "A";
            } else if ($registro['D001_Flag_Tipo'] == "PRODUZIDO") {
                $D137_Produzido_Adquirido = "P";
            } else{
                $D137_Produzido_Adquirido = "";
            }
        }*/

        if($g['C031']['venderEstoqueMatriz'] == 'S'){
            if($C004_Id_Estoque == $g['empresaAtual']){
                $D137_Estoque_Origem = "D";
            } else {
                $D137_Estoque_Origem = "F";
            }
        }

        if($Destino == 6){
            // Se for TROCA, busca as regras somente de troca
            $Extra = " AND D137_Flag_Destino_Produto=6 ";
        } else if ($Destino == 5){
            // Se for BONIFICAÇÂO, busca somente as regras de bonificação
            $Extra = " AND D137_Flag_Destino_Produto=5 ";
        } else if (!empty($Destino)){
            // Se no item da venda estiver outro destino diferente de TROCA ou BONIFICACAO, buscar apenas as regras diferentes de TROCA ou BONIFICACAO.
            $Extra = " AND (D137_Flag_Destino_Produto != 5 AND D137_Flag_Destino_Produto != 6)";
        } else {
            $Extra = "";
        }
        
        $retorno = mysql_query("SELECT *, 
                                       D137_Quantidade_Regras(D137_Id) AS qtdeRegras 
                                  FROM D137 
                             LEFT JOIN D006 ON D006_Id=D137_D006_Id 
                                 WHERE D137_Flag_Inativar='N' 
                                   AND D006_Flag_Inativo='N' 
                                       {$Extra} 
                              ORDER BY qtdeRegras DESC");
        $validas;
        $valida;
        $err = "";
        $log = "Início Log D009_Id: {$D009_Id} <br />";
        $arrayDemo = Array();
        $cont = 0;
        while ($regras = mysql_fetch_assoc($retorno)) {
                $cont++;
                $iconeAbrirJanela = <<<EOT
                if([[permissaoRegras]]){

                    abrirJanela(false, divIdRootAberto(), '[[divId]]', unique(), '', 'Editar Regra', '/fis/fis001/content/fis001content03/', '&acaoId=' + encodeURIComponent('{$regras['D137_Id']}') + '&tabela=D137', [600,550]);
                }
EOT;
                $log .= "<br />Entrou while D137_Id: {$regras['D137_Id']} - {$regras['D006_Codigo_CFOP']} - {$regras['D006_Descricao']}<br />";
                //$demonstrativoDadosRegra = "<h3><font color=\"green\">Regra {$regras['D137_Id']} ({$regras['qtdeRegras']}): {$regras['D006_Id']}-{$regras['D006_Codigo_CFOP']} - {$regras['D006_Descricao']}</font><span class=\"gear\" style=\"float: right; cursor: pointer; width: 16px; height: 16px; right: 0; position: relative;\" onclick=\"{$iconeAbrirJanela} event.preventDefault(); return false;\" /></h3><div style='font-size:12px;padding:10px;'>";
                $demonstrativoDadosRegra = "<h3>{$cont}º) {$regras['D006_Codigo_CFOP']}-{$regras['D006_Descricao']} (ID-{$regras['D006_Id']}) <font color=red> (-) </font><span class=\"gear\" style=\"float: right; cursor: pointer; width: 16px; height: 16px; right: 0; position: relative;\" onclick=\"{$iconeAbrirJanela} event.preventDefault(); return false;\" /></h3><div style='font-size:12px;padding:10px;'>";
                $demonstrativoRegra = "";
                $arrayDemo[] = array();
                $err = "";
                $descricaoRegra = " <b>Regra CFOP (ID-{$regras['D137_Id']}) - Prioridade {$regras['qtdeRegras']}</b><br /><br />";            
                
                // Código cliente(separar por vírgula)
                if (trim($regras['D137_Empresas_Destinatario']) != "") {
                    $log .= "ENTROU IF D137_Empresas_Destinatario != ''<br />";
        
                    $regras['D137_Empresas_Destinatario'] = str_replace(' ','',$regras['D137_Empresas_Destinatario']);
 
                    //$pos = strpos($regras['D137_Empresas_Destinatario'], $D024_Id . ',');
                    $pos = strpos(',' . $regras['D137_Empresas_Destinatario'] . ',' , ',' . $D024_Id . ',');
                    
                    if ($pos === false) {
                        $log .= " Entrou CONTINUE D137_Empresas_Destinatario D137_Id='{$regras['D137_Id']}'<br />";
                        $demonstrativoRegra .= "<b><font color=red>(-)</font> Código cliente, no cadastro do cliente, </b> não confere com o campo especificado nesta regra CFOP.<br />";
                        /*$arrayDemo[] = array(
                            'order' => $regras['qtdeRegras'],
                            'texto' => str_replace('green', 'red', $demonstrativoDadosRegra).$descricaoRegra."Código cliente, em clientes, confere com o campo especificado nesta regra CFOP.</div>"
                        );*/
                        $err .= '1';
                        //continue;
                    }else{
                        $demonstrativoRegra .= "<b><font color=green>(+)</font> Código cliente, no cadastro do cliente, </b>confere com o campo especificado nesta regra CFOP.<br />";
                    }
                }

                // Preferencial sobre CFOP Cliente 
                if ($registro['D024_D006_Id'] > 0 AND $regras['D137_Flag_Preferencial_Sobre_D024_D006_Id'] != "S") {
                    $log .= "Entrou IF D024_D006_Id > AND D137_Flag_Preferencial_Sobre_D024_D006_Id != S  D137_Id='{$regras['D137_Id']}'<br />";
                    $demonstrativoRegra .= "<b><font color=red>(-)</font></b> Cliente possui CFOP preferencial (Preferencial sobre CFOP Cliente). <br />";
                    /*$arrayDemo[] = array(
                        'order' => $regras['qtdeRegras'],
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra).$descricaoRegra."Cliente possui CFOP preferencial (Preferencial sobre CFOP Cliente)</div>"
                    );*/
                    $err .= '1';
                    //continue;
                }
                
                // ID das empresas origem(separar por vírgula):
                if (trim($regras['D137_Empresas']) != "") {
                    $log .= "Entrou IF D137_Empresas != ''<br />";
                    $pos = strpos($regras['D137_Empresas'], $D137_C004_Id . ',');
                    if ($pos === false) {
                        $log .= " Entrou CONTINUE D137_Empresas D137_Id='{$regras['D137_Id']}' D137_C004_Id: {$D137_C004_Id}<br />";
                        $demonstrativoRegra .= "<b><font color=red>(-)</font> ID empresa, no cadastro da empresa,</b> não confere com o campo especificado nesta regra CFOP. <br />";
                        /*$arrayDemo[] = array(
                            'order' => $regras['qtdeRegras'],
                            'texto' => str_replace('green', 'red', $demonstrativoDadosRegra).$descricaoRegra."ID empresa, em empresas, não confere com o campo especificado na regra CFOP.</div>"
                        );*/
                        $err .= '1';
                        //continue;
                    }else{
                        $demonstrativoRegra .= "<b><font color=green>(+)</font> ID empresa, no cadastro da empresa, </b>confere com o campo especificado nesta regra CFOP.<br />";
                    }
                }

                // Empresa: 
                if ($regras['D137_C004_Id'] > 0) {
                    $log = "Entrou IF D137_C004_Id > 0 D137_Id='{$regras['D137_Id']}'<br />";
                    if ($regras['D137_C004_Id'] != $D137_C004_Id) {
                        $log .= "Entrou CONTINUE D137_C004_Id: {$regras['D137_C004_Id']} != D137_C004_Id: {$D137_C004_Id} D137_Id='{$regras['D137_Id']}'<br />";
                        $demonstrativoRegra .= "<b><font color=red>(-)</font> ID empresa, no cadastro da empresa, </b> não confere com o campo especificado nesta regra CFOP.<br />";
                        /*$arrayDemo[] = array(
                            'order' => $regras['qtdeRegras'],
                            'texto' => str_replace('green', 'red', $demonstrativoDadosRegra).$descricaoRegra."<b>- Id empresa, no cadastro da empresa, </b> não confere com o especificado nesta regra CFOP.</div>"
                        );*/
                        $err .= '1';
                        //continue;
                    }else{
                        $demonstrativoRegra .= "<b><font color=green>(+)</font> ID empresa, no cadastro da empresa, </b>confere com o campo  especificado nesta regra CFOP.<br />";
                    }
                }

                //if (trim($regras['D137_Flag_Revenda_Consumidor']) != "") {
                //    $log .= "Entrou D137_Flag_Revenda_Consumidor != '' D137_Id='{$regras['D137_Id']}'<br />";
                //    $demonstrativoRegra .= "Cliente revendedor/consumidor<br />";

                //    $pos = strpos($regras['D137_Flag_Revenda_Consumidor'], $D137_Flag_Revenda_Consumidor . ',');
                //    if ($pos === false) {
                //        $log .= "Entrou CONTINUE D137_Flag_Revenda_Consumidor. D137_Id='{$regras['D137_Id']}'<br />";
                //        $demonstrativoRegra = "";
                //        $arrayDemo[] = array(
                //            'order' => $regras['qtdeRegras'],
                //            'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."Cliente não revendedor/consumidor</div>"
                //        );
                //        $err .= '0';
                //        continue;
                //    }
                //}

                // Regime Tributário Emitente: 
                if ($regras['D137_C004_Codigo_Regime_Tributario'] != "") {
                    $log .= "Entrou D137_C004_Codigo_Regime_Tributario != 0 D137_Id='{$regras['D137_Id']}'<br />";
                    if ($regras['D137_C004_Codigo_Regime_Tributario'] != $D137_C004_Codigo_Regime_Tributario) {
                        $log .= "Entrou CONTINUE D137_C004_Codigo_Regime_Tributario: {$regras['D137_C004_Codigo_Regime_Tributario']} != D137_C004_Codigo_Regime_Tributario: {$D137_C004_Codigo_Regime_Tributario} D137_Id='{$regras['D137_Id']}'<br />";
                        $demonstrativoRegra .= "<b><font color=red>(-)</font> CRT emitente, no cadastro da empresa, </b>não confere com o campo especificado nesta regra CFOP.<br />";
                        /*$arrayDemo[] = array(
                            'order' => $regras['qtdeRegras'],
                            'texto' => str_replace('green', 'red', $demonstrativoDadosRegra).$descricaoRegra."<b>- CRT emitente, no cadastro da empresa, </b>não confere com especificado nesta regra CFOP.</div>"
                        );*/
                        $err .= '1';
                        //continue;
                    }else{
                        $demonstrativoRegra .= "<b><font color=green>(+)</font> CRT emitente, no cadastro da empresa, </b>confere com o campo especificado nesta regra CFOP.<br />";
                    }
                }

                // Regime Tributário Destinatário:
                if (!empty($regras['D137_D024_Codigo_Regime_Tributario'])) {
                    $log .= "Entrou D137_D024_Codigo_Regime_Tributario != 0 D137_Id='{$regras['D137_Id']}'<br />";
                    log("Regras D137_D024_Codigo_Regime_Tributario: ".$regras['D137_D024_Codigo_Regime_Tributario'].' D137_D024_Codigo_Regime_Tributario'.$D137_D024_Codigo_Regime_Tributario);
                    if ($regras['D137_D024_Codigo_Regime_Tributario'] != $D137_D024_Codigo_Regime_Tributario) {
                        
                        $log .= "Entrou CONTINUE D137_D024_Codigo_Regime_Tributario: {$regras['D137_D024_Codigo_Regime_Tributario']} != D137_D024_Codigo_Regime_Tributario: {$D137_D024_Codigo_Regime_Tributario} D137_Id='{$regras['D137_Id']}'<br />";
                        $demonstrativoRegra .= "<b><font color=red>(-)</font> CRT destinatário, no cadastro do cliente, </b>não confere com o campo especificado nesta regra CFOP. <br />";
                        /*$arrayDemo[] = array(
                            'order' => $regras['qtdeRegras'],
                            'texto' => str_replace('green', 'red', $demonstrativoDadosRegra).$descricaoRegra."<b>- CRT destinatário, no cadastro do cliente, </b>não confere com o especifado nesta regra CFOP.</div>"
                        );*/
                        $err .= '1';
                        //continue;
                    }else{
                        $demonstrativoRegra .= "<b><font color=green>(+)</font>  CRT destinatário, no cadastro do cliente, </b>confere com o campo especificado nesta regra CFOP.<br />";
                    }
                }

                // Tipo Contribuinte:
                if ($regras['D137_D024_Flag_Contribuinte'] != "") {
                    $log .= "Entrou D137_D024_Flag_Contribuinte != 0 D137_Id='{$regras['D137_Id']}'<br />";
                    if ($regras['D137_D024_Flag_Contribuinte'] != $D137_D024_Flag_Contribuinte) {
                        $log .= "Entrou CONTINUE D137_D024_Flag_Contribuinte: {$regras['D137_D024_Flag_Contribuinte']} != D137_D024_Flag_Contribuinte: {$D137_D024_Flag_Contribuinte} D137_Id='{$regras['D137_Id']}'<br />";
                        $demonstrativoRegra .= "<b><font color=red>(-)</font> Tipo contribuinte, no cadastro do cliente, </b> não confere com o campo especificado nesta regra CFOP. <br />";
                        /*$arrayDemo[] = array(
                            'order' => $regras['qtdeRegras'],
                            'texto' => str_replace('green', 'red', $demonstrativoDadosRegra).$descricaoRegra."<b>- Tipo contribuinte, no cadastro do cliente, </b>não confere com o especificado nesta CFOP.</div>"
                        );*/
                        $err .= '2';
                        //continue;
                    }else{
                        $demonstrativoRegra .= "<b><font color=green>(+)</font> Tipo contribuinte, no cadastro do cliente, </b> confere com o campo especificado nesta regra CFOP.<br />";
                    }
                }            
                
                // Dentro/Fora Estado e valida por endereço de entrega
                if ($regras['D137_Dentro_Fora_Estado'] != "" && $regras['D137_Flag_Valida_Regra_Endereco_Entrega'] == "S") {
                    $validaDentroFora = $D137_Dentro_Fora_Estado;
                    $ufEntrega = $parametrosExtrasRetornaCFOP['ufEnderecoEntrega'] ?? 0;
                    if((!isset($parametrosExtrasRetornaCFOP) || (isset($parametrosExtrasRetornaCFOP) && $ufEntrega <= 0))){
                        $log .= "Entrou CONTINUE D137_Dentro_Fora_Estado: {$regras['D137_Dentro_Fora_Estado']} != D137_Dentro_Fora_Estado: {$validaDentroFora} D137_Id='{$regras['D137_Id']}'<br />";
                        $demonstrativoRegra .= "<b><font color=red>(-)</font> Regra 'Dentro/Fora UF' valida pelo end.Entrega,</b> e não foi definido um end.Entrega na venda <br />";
                        /*$arrayDemo[] = array(
                            'order' => $regras['qtdeRegras'],
                            'texto' => str_replace('green', 'red', $demonstrativoDadosRegra).$descricaoRegra."<b>- $mensagemDentroFora, </b>não confere com o campo 'Dentro/Fora Estado' especificado nesta regra CFOP.</div>"
                        );*/
                        $err .= '3';
                    } else {
                        if(isset($parametrosExtrasRetornaCFOP) && $ufEntrega > 0){
                            if ($ufEntrega == $registro['C004_D018_Id']){
                                $validaDentroFora = 'D';
                            } else {
                                $validaDentroFora = 'F';
                            }
                        } 

                        $log .= "Entrou D137_Dentro_Fora_Estado != '' D137_Id='{$regras['D137_Id']}'<br />";
                        if ($regras['D137_Dentro_Fora_Estado'] != $validaDentroFora) {
                            $log .= "Entrou CONTINUE D137_Dentro_Fora_Estado: {$regras['D137_Dentro_Fora_Estado']} != D137_Dentro_Fora_Estado: {$validaDentroFora} D137_Id='{$regras['D137_Id']}'<br />";
                            //$demonstrativoRegra .= "<b><font color=red>(-)</font> UF End. Entrega, na venda, </b>não confere com o campo 'Dentro/Fora UF' especificado nesta regra CFOP . <br />";
                            $demonstrativoRegra .= "<b><font color=red>(-)</font> UF cliente, no end.Entrega, </b>não confere com o campo 'Dentro/Fora UF' + 'End.Entrega'  especificado nesta regra CFOP. <br />";
                            /*$arrayDemo[] = array(
                                'order' => $regras['qtdeRegras'],
                                'texto' => str_replace('green', 'red', $demonstrativoDadosRegra).$descricaoRegra."<b>- $mensagemDentroFora, </b>não confere com o campo 'Dentro/Fora Estado' especificado nesta regra CFOP.</div>"
                            );*/
                            $err .= '3';
                            //continue;
                        }else{
                            //$demonstrativoRegra .= "<b><font color=green>(+)</font> UF End. Entrega, na venda, </b>confere com o campo 'Dentro/Fora UF' especificado nesta regra CFOP.<br />";
                            $demonstrativoRegra .= "<b><font color=green>(+)</font> UF cliente, no end.Entrega, </b>confere com o campo 'Dentro/Fora UF' + 'End.Entrega' especificado nesta regra CFOP.<br />";
                        }
                    }
                } elseif ($regras['D137_Dentro_Fora_Estado'] != "") {
                    // Dentro/Fora Estado
                    $clienteEstado = ($D137_Dentro_Fora_Estado == 'D') ? 'dentro' : 'fora';
                    $regraEstado = ($regras['D137_Dentro_Fora_Estado'] == 'D') ? 'dentro' : 'fora';
                    $log .= "Entrou D137_Dentro_Fora_Estado != '' D137_Id='{$regras['D137_Id']}'<br />";
                    if ($regras['D137_Dentro_Fora_Estado'] != $D137_Dentro_Fora_Estado) {
                        $log .= "Entrou CONTINUE D137_Dentro_Fora_Estado: {$regras['D137_Dentro_Fora_Estado']} != D137_Dentro_Fora_Estado: {$D137_Dentro_Fora_Estado} D137_Id='{$regras['D137_Id']}'<br />";
                        $demonstrativoRegra .= "<b><font color=red>(-)</font> UF cliente, no cadastro do cliente, </b>não confere com o campo 'Dentro/Fora UF' especificado nesta regra CFOP. <br />";
                        /*$arrayDemo[] = array(
                            'order' => $regras['qtdeRegras'],
                            'texto' => str_replace('green', 'red', $demonstrativoDadosRegra).$descricaoRegra."<b>- UF cliente, no cadastro do cliente, </b>não confere com o campo 'Dentro/Fora Estado' especificado nesta regra CFOP.</div>"
                        );*/
                        $err .= '3';
                        //continue;
                    }else{
                        $demonstrativoRegra .= "<b><font color=green>(+)</font> UF cliente, no cadastro do cliente, </b>confere com o campo 'Dentro/Fora UF' especificado nesta regra CFOP.<br />";
                    }
                }

                // Estados Específicos(separar por vírgula):
                if (trim($regras['D137_Estados_Especificos']) != "") {
                    $log .= "Entrou D137_Estados_Especificos != '' D137_Id='{$regras['D137_Id']}'<br />";
                    $pos = strpos($regras['D137_Estados_Especificos'], $registro['D018_UF']);
                    if ($pos === false) {
                        $log .= "Entrou CONTINUE D137_Estados_Especificos: {$regras['D137_Estados_Especificos']} != D137_Estados_Especificos: {$D137_Estados_Especificos} D137_Id='{$regras['D137_Id']}'<br />";
                        $demonstrativoRegra .= "<b><font color=red>(-)</font> UF cliente, no cadastro do cliente, </b>não confere com o campo especificado nesta regra CFOP.<br />";
                        /*$arrayDemo[] = array(
                            'order' => $regras['qtdeRegras'],
                            'texto' => str_replace('green', 'red', $demonstrativoDadosRegra).$descricaoRegra."<b>- UF do cliente, no cadastro do cliente, </b>não confere com o especificado nesta regra CFOP.</div>"
                        );*/
                        $err .= '4';
                        //continue;
                    }else{
                        $demonstrativoRegra .= "<b><font color=green>(+)</font> UF cliente, no cadastro do cliente, </b>confere com o campo especificado nesta regra CFOP.<br />";
                    }
                }

                // UF tem ST?
                if ($regras['D137_UF_Tem_ST'] != "") {
                    $UFClienteST = ($D137_UF_Tem_ST == 'S') ? 'possuí' : 'não possuí';
                    $UFRegraST = ($regras['D137_UF_Tem_ST'] == 'S') ? 'possuí' : 'não possuí';
                    $log .= "Entrou D137_UF_Tem_ST != '' D137_Id='{$regras['D137_Id']}'<br />";
                    if ($regras['D137_UF_Tem_ST'] != $D137_UF_Tem_ST) {
                        $log .= "Entrou CONTINUE D137_UF_Tem_ST: {$regras['D137_UF_Tem_ST']} != D137_UF_Tem_ST: {$D137_UF_Tem_ST} D137_Id='{$regras['D137_Id']}'<br />";
                        $demonstrativoRegra .= "<b><font color=red>(-)</font> ST, no cadastro do ICMS, </b>não confere com o campo especificado nesta regra CFOP. <br />";
                        /*$arrayDemo[] = array(
                            'order' => $regras['qtdeRegras'],
                            'texto' => str_replace('green', 'red', $demonstrativoDadosRegra).$descricaoRegra."<b>- ST, no cadastro do ICMS, </b>não confere com o campo especificado nesta regra CFOP.</div>"
                        );*/
                        $err .= '5';
                        //continue;
                    }else{
                        $demonstrativoRegra .= "<b><font color=green>(+)</font> ST, no cadastro do ICMS, </b>confere com o campo especificado nesta regra CFOP.<br />";
                    }
                }

                // Pessoa:
                if ($regras['D137_Pessoa_Fisica_Juridica'] != "") {
                    if($D137_Pessoa_Fisica_Juridica == 'PF') {
                        $pessoaCliente = 'física';
                    } else if($D137_Pessoa_Fisica_Juridica == 'PJ') {
                        $pessoaCliente = 'jurídica';
                    } else {
                        $pessoaCliente = 'internacional';
                    }

                    if($regras['D137_Pessoa_Fisica_Juridica'] == 'PF') {
                        $pessoaRegra = 'física';
                    } else if($regras['D137_Pessoa_Fisica_Juridica'] == 'PJ') {
                        $pessoaRegra = 'jurídica';
                    } else {
                        $pessoaRegra = 'internacional';
                    }

                    $log .= "Entrou D137_Pessoa_Fisica_Juridica != '' D137_Id='{$regras['D137_Id']}'<br />";
                    if ($regras['D137_Pessoa_Fisica_Juridica'] != $D137_Pessoa_Fisica_Juridica) {
                        $log .= "Entrou CONTINUE D137_Pessoa_Fisica_Juridica: {$regras['D137_Pessoa_Fisica_Juridica']} != D137_Pessoa_Fisica_Juridica: {$D137_Pessoa_Fisica_Juridica} D137_Id='{$regras['D137_Id']}'<br />";
                        $demonstrativoRegra .= "<b><font color=red>(-)</font> Pessoa destinatário, no cadastro do cliente, </b>não confere com o campo especificado nesta regra CFOP. <br />";
                        /*$arrayDemo[] = array(
                            'order' => $regras['qtdeRegras'],
                            'texto' => str_replace('green', 'red', $demonstrativoDadosRegra).$descricaoRegra."<b>- Pessoa destinatário, no cadastro do cliente, </b>não confere com o campo especificado nesta regra CFOP.</div>"
                        );*/
                        $err .= '6';
                        //continue;
                    }else{
                        $demonstrativoRegra .= "<b><font color=green>(+)</font> Pessoa destinatário, no cadastro do cliente, </b>confere com o campo especificado nesta regra CFOP.<br />";
                    }
                }

                // NCM tem ST?:
                if ($regras['D137_NCM_Tem_ST'] != "") {
                    $NCMST = ($D137_NCM_Tem_ST == 'S') ? 'possuí ST' : 'não possuí ST';
                    $regraST = ($regras['D137_NCM_Tem_ST'] == 'S') ? 'possuí ST' : 'não possuí ST';

                    if ($registro['D024_Flag_ICMS_Substituicao_Tributaria_Substituto']=='S'){
                        $log .= "Entrou D137_NCM_Tem_ST != '' D137_Id='{$regras['D137_Id']}' *** Cadastro cliente isento ST <br />";
                        $demonstrativoRegra .= "Cliente Isento ST<br />";
                    }

                    $log .= "Entrou D137_NCM_Tem_ST != '' D137_Id='{$regras['D137_Id']}'<br />";
                    if ($regras['D137_NCM_Tem_ST'] != $D137_NCM_Tem_ST) {
                        $log .= "Entrou CONTINUE D137_NCM_Tem_ST: {$regras['D137_NCM_Tem_ST']} != D137_NCM_Tem_ST: {$D137_NCM_Tem_ST} D137_Id='{$regras['D137_Id']}'<br />";
                        $demonstrativoRegra .= "<b><font color=red>(-)</font> ST, no cadastro da NCM, </b>não confere com o campo especificado nesta regra CFOP.<br />";
                        /*$arrayDemo[] = array(
                            'order' => $regras['qtdeRegras'],
                            'texto' => str_replace('green', 'red', $demonstrativoDadosRegra).$descricaoRegra."<b>- ST, no cadastro da NCM, </b>não confere com o campo especificado nesta regra CFOP.</div>"
                        );*/
                        $err .= '7';
                        //continue;
                    }else{
                        $demonstrativoRegra .= "<b><font color=green>(+)</font> ST, no castro da NCM, </b>confere com o campo especificado nesta regra CFOP.<br />";
                    }
                }

                // Convênio NCM/UF ST?:
                if ($regras['D137_Flag_UF_Convenio_NCM_ST'] == "S") {
                    if($registro['D149_UF_ST'] != ""){
                        $log .= "Entrou D137_Flag_UF_Convenio_NCM_ST != '' D137_Id='{$regras['D137_Id']}'<br />";
                        $pos = strpos($registro['D149_UF_ST'], $registro['D018_UF']);
                        if ($pos === false) {
                            $log .= "Entrou CONTINUE D149_UF_ST D137_Id='{$regras['D137_Id']}'<br />";
                            $demonstrativoRegra .= "<b><font color=red>(-)</font> Convênio NCM/UF ST Vendas, no cadastro da NCM, </b>não confere com o campo especificado nesta regra CFOP. <br />";
                            /*$arrayDemo[] = array(
                                'order' => $regras['qtdeRegras'],
                                'texto' => str_replace('green', 'red', $demonstrativoDadosRegra).$descricaoRegra."<b>- Convênio NCM/UF ST Vendas, no cadastro da NCM, </b>não confere com o especificado nesta regra CFOP.</div>"
                            );*/
                            $err .= '10';
                            //continue;
                        }else{
                            $demonstrativoRegra .= "<b><font color=green>(+)</font> Convênio NCM/UF ST Vendas, no cadastro da NCM, </b>confere com o campo especificado nesta regra CFOP.<br />";
                        }
                    }
                }

                //if ($regras['D137_Flag_UF_Convenio_NCM_ST'] == "N") {
                //    $log .= "Entrou D137_Flag_UF_Convenio_NCM_ST== 'N' D137_Id='{$regras['D137_Id']}'<br />";
                //    $demonstrativoRegra .= "Estado do cliente não possuí convenio NCM/UF ST<br />";
                //
                //    $pos = strpos($regras['D149_UF_ST'], $registro['D018_UF']);
                //    if ($pos === true) {
                //        $log .= "Entrou CONTINUE D149_UF_ST: {$regras['D149_UF_ST']} != D149_UF_ST: {$D149_UF_ST} D137_Id='{$regras['D137_Id']}'<br />";
                //        $demonstrativoRegra = "";
                //        $arrayDemo[] = array(
                //            'order' => $regras['qtdeRegras'],
                //            'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."Estado do cliente está específicado no campo UF ST da NCM</div>"
                //        );
                //        $err .= '10';
                //        continue;
                //    }
                //}

                // Tpo Produto:
                if ($regras['D137_Produzido_Adquirido'] != "") {               
                    $log .= "Entrou D137_Produzido_Adquirido != '' D137_Id='{$regras['D137_Id']}'<br />";
                    // Industrialização segue as regras fiscais de Produzido.
                    $tipoProdutoRegra = ($registro['D049_Flag_Tipo'] == 'IN') ? 'PD' : $registro['D049_Flag_Tipo'];
                    if ($regras['D137_Produzido_Adquirido'] != $tipoProdutoRegra) {
                        $log .= "Entrou CONTINUE D137_Produzido_Adquirido: {$regras['D137_Produzido_Adquirido']} != D137_Produzido_Adquirido: {$D137_Produzido_Adquirido} D137_Id='{$regras['D137_Id']}'<br />";
                        $demonstrativoRegra .= "<b><font color=red>(-)</font> Tipo Produto, no cadastro do produto, </b>não confere com o campo especificado nesta regra CFOP. <br />";
                        /*$arrayDemo[] = array(
                            'order' => $regras['qtdeRegras'],
                            'texto' => str_replace('green', 'red', $demonstrativoDadosRegra).$descricaoRegra."<b>- Tipo Produto, no cadastro do produto, </b>não confere com o especificado na regra CFOP.</div>"
                        );*/
                        $err .= '8';
                        //continue;
                    }else{
                        $demonstrativoRegra .= "<b><font color=green>(+)</font> Tipo Produto, no cadastro do produto, </b>confere com o campo especificado nesta regra CFOP.<br />";
                    }
                }

                // Tpo IPI:
                if ($regras['D137_Flag_Tipo_IPI'] != "") {               
                    $log .= "Entrou D137_Flag_Tipo_IPI != '' D137_Id='{$regras['D137_Id']}'<br />";
                    $regras['D137_Flag_Tipo_IPI'] = ($regras['D137_Flag_Tipo_IPI'] == 1) ? 'PRODUZIDO' : 'COMPRADO';

                    if ($regras['D137_Flag_Tipo_IPI'] != $registro['D001_Flag_Tipo']) {
                        $log .= "Entrou CONTINUE D137_Flag_Tipo_IPI: {$regras['D137_Flag_Tipo_IPI']} != D137_Flag_Tipo_IPI: {$registro['D001_Flag_Tipo']} D137_Id='{$regras['D137_Id']}'<br />";
                        $demonstrativoRegra .= "<b><font color=red>(-)</font> Tipo IPI, no cadastro do produto, </b>não confere com o campo especificado nesta regra CFOP. <br />";
                        /*$arrayDemo[] = array(
                            'order' => $regras['qtdeRegras'],
                            'texto' => str_replace('green', 'red', $demonstrativoDadosRegra).$descricaoRegra."<b>- Tipo Produto, no cadastro do produto, </b>não confere com o especificado na regra CFOP.</div>"
                        );*/
                        $err .= '8';
                        //continue;
                    }else{
                        $demonstrativoRegra .= "<b><font color=green>(+)</font> Tipo IPI, no cadastro do produto, </b>confere com o campo especificado nesta regra CFOP.<br />";
                    }
                }

                // Origem Mercadoria (separar por virgula)
                if (trim($regras['D137_Origem_Mercadoria']) != "") {
                    $log .= "ENTROU IF D137_Origem_Mercadoria != ''<br />";
                    $pos = strpos($regras['D137_Origem_Mercadoria'], $registro['D049_Origem_Mercadoria'] . ',');
                    if ($pos === false) {
                        $log .= " Entrou CONTINUE D137_Origem_Mercadoria D137_Id='{$regras['D137_Id']}'<br />";
                        $demonstrativoRegra .= "<b><font color=red>(-)</font> Origem mercadoria, </b>não confere com o campo especificado nesta regra CFOP. <br/>";
                        /*$arrayDemo[] = array(
                            'order' => $regras['qtdeRegras'],
                            'texto' => str_replace('green', 'red', $demonstrativoDadosRegra).$descricaoRegra."<b>- Origem mercadoria </b>não confere com o especificado nesta regra CFOP.</div>"
                        );*/
                        $err .= '1';
                        //continue;
                    }else{
                        $demonstrativoRegra .= "<b><font color=green>(+)</font> Origem mercadoria, </b>confere com o campo especificado nesta regra CFOP.<br />";
                    }
                }

                // Peso mínimo Kg: e Peso máximo Kg: 
                if ($regras['D137_Peso_Minimo'] > 0 OR $regras['D137_Peso_Maximo'] > 0) {
                    $log .= "Entrou D137_Peso_Minimo >= '0' OR {$regras['D137_Peso_Maximo']}>0 D137_Id='{$regras['D137_Id']}'<br />";
                    if ($registro['D001_Peso_Unitario_Kg'] < $regras['D137_Peso_Minimo'] OR $registro['D001_Peso_Unitario_Kg'] > $regras['D137_Peso_Maximo']) {
                        $log .= "Entrou CONTINUE D001_Peso_Unitario_Kg: {$regras['D001_Peso_Unitario_Kg']} != D001_Peso_Unitario_Kg: {$D001_Peso_Unitario_Kg} D137_Id='{$regras['D137_Id']}'<br />";
                        $demonstrativoRegra .= "<b><font color=red>(-)</font> Peso Unitário Líq Kg, no cadastro do produto, </b> não confere com o intervalo especificado nesta regra CFOP. <br />";
                        /*$arrayDemo[] = array(
                            'order' => $regras['qtdeRegras'],
                            'texto' => str_replace('green', 'red', $demonstrativoDadosRegra).$descricaoRegra."<b>- Peso Unitário Líq Kg, no cadastro do produto, </b>confere com o intervalo especificado nesta regra CFOP.</div>"
                        );*/
                        $err .= '8';
                        //continue;
                    }else{
                        $demonstrativoRegra .= "<b><font color=green>(+)</font> Peso Unitário Líquido Kg, no cadastro do produto, </b>confere com o intervalo especificado nesta regra CFOP.<br />";
                    }
                }

                // NCM(separar por vírgula):
                if (trim($regras['D137_NCM']) != "") {
                    $log .= "Entrou D137_NCM != '' D137_Id='{$regras['D137_Id']}'<br />";
                    $pos = strpos($regras['D137_NCM'], $registro['D005_Classificacao_Fiscal']);
                    if ($pos === false){
                        $pos = strpos(','.$regras['D137_NCM'].',', ','.$registro['D149_Id'].',');
                        log(','.$regras['D137_NCM'].',', ','.$registro['D149_Id'].',');
                    }
                    if ($pos === false) {
                        $log .= "Entrou CONTINUE D137_NCM D137_Id='{$regras['D137_Id']}'<br />";
                        $demonstrativoRegra .= "<b><font color=red>(-)</font> NCM, no cadastro do produto, </b>não confere com o campo especificado nesta regra CFOP. <br />";
                        /*$arrayDemo[] = array(
                            'order' => $regras['qtdeRegras'],
                            'texto' => str_replace('green', 'red', $demonstrativoDadosRegra).$descricaoRegra."<b>- NCM, no cadastro do produto, </b>não confere com o campo especificado nesta regra CFOP.</div>"
                        );*/
                        $err .= '4';
                        //continue;
                    }else{
                        $demonstrativoRegra .= "<b><font color=green>(+)</font> NCM, no cadastro do produto, </b>confere com o campo especificado nesta regra CFOP.<br />";
                    }
                }

                // Destino Mercadoria:
                if ($regras['D137_Flag_Destino_Produto'] != "") {
                    $log .= "Entrou D137_Flag_Destino_Produto != '' D137_Id='{$regras['D137_Id']}'<br />";
                    if ($regras['D137_Flag_Destino_Produto'] != $Destino) {
                        
                        $log .= "Entrou CONTINUE D137_Flag_Destino_Produto: {$regras['D137_Flag_Destino_Produto']} != D137_Flag_Destino_Produto: {$Destino} D137_Id='{$regras['D137_Id']}'<br />";
                        $demonstrativoRegra .= "<b><font color=red>(-)</font> Destino do produto,</b> não confere com o campo especificado nesta regra CFOP. <br />";
                        /*$arrayDemo[] = array(
                            'order' => $regras['qtdeRegras'],
                            'texto' => str_replace('green', 'red', $demonstrativoDadosRegra).$descricaoRegra."<b>- O destino do produto,</b> não confere com o campo especificado nesta regra CFOP.</div>"
                        );*/
                        $err .= '9';
                        //continue;
                    }else{
                        $demonstrativoRegra .= "<b><font color=green>(+)</font> O destino do produto,</b> confere com o campo especificado nesta regra CFOP.<br />";
                    }
                }

                // Origem Pedido
                if ($regras['D137_Flag_Origem_Pedido'] != "") {
                    $log .= "Entrou D137_Flag_Origem_Pedido != '' D137_Id='{$regras['D137_Id']}'<br />";
                    if ($regras['D137_Flag_Origem_Pedido'] != $D137_Origem_Pedido) {
                        
                        $log .= "Entrou CONTINUE D137_Flag_Origem_Pedido: {$regras['D137_Flag_Origem_Pedido']} != D137_Flag_Origem_Pedido: {$D137_Origem_Pedido} D137_Id='{$regras['D137_Id']}'<br />";
                        $demonstrativoRegra .= "<b><font color=red>(-)</font>A origem do pedido é diferente da regra. <br />";
                        /*$arrayDemo[] = array(
                            'order' => $regras['qtdeRegras'],
                            'texto' => str_replace('green', 'red', $demonstrativoDadosRegra).$descricaoRegra."A origem do pedido é diferente da regra</div>"
                        );*/
                        $err .= '9';
                        //continue;
                    }else{
                        $demonstrativoRegra .= "<b><font color=green>(+)</font>A origem do pedido é a mesma da regra<br />";
                    }
                }

                // NCM: 
                if ($regras['D137_D005_Id'] > 0) {
                    $log .= "Entrou D137_D005_Id >0 D137_Id='{$regras['D137_Id']}'<br />";
                    if ($regras['D137_D005_Id'] != $D137_D005_Id) {
                        $log .= "Entrou CONTINUE D137_D005_Id: {$regras['D137_D005_Id']} != D137_D005_Id: {$D137_D005_Id} D137_Id='{$regras['D137_Id']}'<br />";
                        $demonstrativoRegra .= "<b><font color=red>(-)</font> NCM, no cadastro do produto, </b>não confere com o campo especificado nesta regra CFOP. <br/>";
                        /*$arrayDemo[] = array(
                            'order' => $regras['qtdeRegras'],
                            'texto' => str_replace('green', 'red', $demonstrativoDadosRegra).$descricaoRegra."<b>- NCM, no cadastro do produto, </b>não confere com o campo especificado nesta regra CFOP.</div>"
                        );*/
                        $err .= '9';
                        //continue;
                    }else{
                        $demonstrativoRegra .= "<b><font color=green>(+)</font> NCM, no cadastro do produto, </b>confere com o campo especificado nesta regra CFOP.<br />";
                    }
                }

                // Origem Estoque
                if ($regras['D137_Origem_Estoque'] != ""){
                    $log.="Entrou D137_Origem_Estoque != '' D137_Id='{$regras['D137_Id']}'<br />";
                    if ($regras['D137_Origem_Estoque']!=$D137_Estoque_Origem){
                        $log.="Entrou CONTINUE D137_Origem_Estoque: {$regras['D137_Origem_Estoque']} != D137_Origem_Estoque: {$D137_Estoque_Origem} D137_Id='{$regras['D137_Id']}'<br />";
                        $demonstrativoRegra .= "<b><font color=red>(-)</font>Origem do estoque diferente nesta regra CFOP. <br />";
                        /*$arrayDemo[] = array(
                            'order' => $regras['qtdeRegras'],
                            'texto' => str_replace('green', 'red', $demonstrativoDadosRegra).$descricaoRegra."Origem do estoque diferente da regra</div>"
                        );*/
                        $err.='9';
                        //continue;                   
                    }else{
                        $demonstrativoRegra .= "<b><font color=green>(+)</font>Origem do estoque específicada nesta regra CFOP<br />";
                    }
                }

                // ICMS ST Retido
                if ($regras['D137_ST_Retido'] != ''){
                    $log.="Entrou D137_ST_Retido != '' D137_Id='{$regras['D137_Id']}'<br />";
                    if ($Retido != $regras['D137_ST_Retido']){
                        $log.="Entrou CONTINUE D137_ST_Retido: {$regras['D137_ST_Retido']} != D137_ST_Retido: {$D137_ST_Retido} D137_Id='{$regras['D137_Id']}'<br />";
                        $demonstrativoRegra .= "<b><font color=red>(-)</font> ST, o produto não possui saldo com ST na compra. <br />";
                        $err.='10';
                        /*$arrayDemo[] = array(
                            'order' => $regras['qtdeRegras'],
                            'texto' => str_replace('green', 'red', $demonstrativoDadosRegra).$descricaoRegra."Origem do estoque diferente da regra</div>"
                        );*/
                        //continue;                   
                    }else{
                        $demonstrativoRegra .= "<b><font color=green>(+)</font> ST, o produto possui saldo com ST na compra. <br />";
                    }
                }

                // Setor produto (separar por vírgula):
                if (trim($regras['D137_D060_Ids']) != "") {
                    $log .= "Entrou D137_D060_Ids != '' D137_Id='{$regras['D137_Id']}'<br />";

                    // Pega os Ids dos setores na CFOP _________
                    $setores = explode(',', $regras['D137_D060_Ids']);

                    // Se o Setor do produto NÃO estiver no array ele entra
                    if (!in_array($registro['D001_D060_Id'], $setores)) {
                        $log .= "Entrou CONTINUE D137_D060_Ids: {$regras['D137_D060_Ids']} != D137_D060_Ids: {$registro['D001_D060_Id']} D137_Id='{$regras['D137_Id']}'<br />";
                        $demonstrativoRegra .= "<b><font color=red>(-)</font> Setor produto, no cadastro do produto, </b>não confere com o campo especificado nesta regra CFOP.<br />";
                        /*$arrayDemo[] = array(
                            'order' => $regras['qtdeRegras'],
                            'texto' => str_replace('green', 'red', $demonstrativoDadosRegra).$descricaoRegra."<b>- UF do cliente, no cadastro do cliente, </b>não confere com o especificado nesta regra CFOP.</div>"
                        );*/
                        $err .= '11';
                        //continue;
                    }else{
                        $demonstrativoRegra .= "<b><font color=green>(+)</font> Setor produto, no cadastro do produto, </b>confere com o campo especificado nesta regra CFOP.<br />";
                    }
                }           
                
                // Grupo produto (separar por vírgula):
                if (trim($regras['D137_D003_Ids']) != "") {
                    $log .= "Entrou D137_D003_Ids != '' D137_Id='{$regras['D137_Id']}'<br />";
                    $pos = strpos($regras['D137_D003_Ids'], $registro['D001_D003_Id']);
                    if ($pos === false) {
                        $log .= "Entrou CONTINUE D137_D003_Ids: {$regras['D137_D003_Ids']} != D137_D003_Ids: {$registro['D001_D003_Id']} D137_Id='{$regras['D137_Id']}'<br />";
                        $demonstrativoRegra .= "<b><font color=red>(-)</font> Grupo produto, no cadastro do produto, </b>não confere com o campo especificado nesta regra CFOP.<br />";
                        /*$arrayDemo[] = array(
                            'order' => $regras['qtdeRegras'],
                            'texto' => str_replace('green', 'red', $demonstrativoDadosRegra).$descricaoRegra."<b>- UF do cliente, no cadastro do cliente, </b>não confere com o especificado nesta regra CFOP.</div>"
                        );*/
                        $err .= '11';
                        //continue;
                    }else{
                        $demonstrativoRegra .= "<b><font color=green>(+)</font> Grupo produto, no cadastro do produto, </b>confere com o campo especificado nesta regra CFOP.<br />";
                    }
                }   


                if(empty($err)){
                    log("%%%%Err:".$err);
                    $arrayDemo[] = array(
                        'D006_Id' => $regras['D137_D006_Id'],
                        'order' => $regras['qtdeRegras']+100,
                        'texto' => str_replace(' (-) ', '<font color=green> (+) </font>', $demonstrativoDadosRegra).$descricaoRegra.$demonstrativoRegra."</div>"
                    );
                    
                    $log .= "Aplicou essa regra<br />";
                    $validas[] = $regras['D137_Id'];
                    $valida    = $regras['D137_D006_Id'];
                }else{
                    $descricaoRegra .= "<b><font color = red> Regra não utilizada </font></b> por conta das divergências <b><font color=red>(-)</font></b> abaixo:<br /><br />";
                    
                    $arrayDemo[] = array(
                        'D006_Id' => $regras['D137_D006_Id'],
                        'order' => $regras['qtdeRegras']+100,                                  
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra).$descricaoRegra.$demonstrativoRegra."</div>"
                    ); 
                }
        }
        $log = $log . " <br />TOTAL REGRAS ENCONTRADAS: " . count($validas);
        log($log);

       /* usort($arrayDemo, function($a, $b) {
            if ($a['order'] == $b['order']) {
                return 0;
            }
            return ($a['order'] < $b['order']) ? 1 : -1;
        });*/
                    
        
        if (count($validas) == 0) {
            if ($registro['D024_D006_Id'] > 0) {
                $valida = $registro['D024_D006_Id'];
            }
            $valida = false;
        } else if(count($validas) > 1){
            $qtde_regras = 0;
            $valida      = 0;
            for ($i = 0; $i < count($validas); $i++) {
                $D137  = mysql_query("SELECT D137_Quantidade_Regras('{$validas[$i]}') as qtde_regras FROM D137 LEFT JOIN D006 ON D006_Id=D137_D006_Id WHERE D137_Flag_Inativar='N' AND D006_Flag_Inativo='N' limit 1");
                $mD137 = mysql_fetch_array($D137);
                
                if ($mD137['qtde_regras'] > $qtde_regras) {
                    $D137_1      = mysql_query("SELECT D137_D006_Id FROM D137 WHERE D137_Id='{$validas[$i]}'");
                    $mD137_1     = mysql_fetch_array($D137_1);
                    $qtde_regras = $mD137['qtde_regras'];
                    $valida      = $mD137_1['D137_D006_Id'];
                }
            }
        }
    
        /*foreach($arrayDemo as $key => $value){
            if(!empty($value['D006_Id']) && $value['D006_Id'] == $valida){
                array_unshift($arrayDemo, $arrayDemo[$key]);
                unset($arrayDemo[$key+1]);
            }
        }*/

        $demonstrativo = "<script>$(function() {\$(\"#demonstrativoCFOP\").accordion({collapsible: true, autoHeight: false, icons: false, active: false}); });</script><div id=\"demonstrativoCFOP\">";
        foreach ($arrayDemo as $key => $value) {
            $value['texto'] = str_replace('ID-'.$valida.') ', 'ID-'.$valida.') <font style="color:white;background:green">&nbspUTILIZADA&nbsp;</font> ',$value['texto']);
            $demonstrativo .= $value['texto'];
        }
    
        $demonstrativo .= "</div><br /><b>TOTAL REGRAS VÁLIDAS: " . count($validas) . "</b>";
        $demonstrativo .= "</div><br /><b>ID CFOP UTILIZADO: " . $valida . "</b>";
        $demonstrativo .= "<b style=\"float: right\">".date("d/m/Y H:i:s")."</b><br />";
        if($retornaLog == true){
            return $demonstrativo;
        } else {
            return $valida;
        }
    }
    
    /**
     * alteraLista
     *
     * @param String $T003_Id
     * @param String $lista
     * @return true
     */     
    public function alteraLista($T003_Id, $lista)
    {
        global $g;
        mysql_query("UPDATE T003 SET T003_Flag_Lista_Preco = '{$lista}' WHERE T003_Id = '{$T003_Id}'");
        
        $T004 = mysql_query("SELECT * FROM T004 WHERE T004_T003_Id = '{$T003_Id}'");
        
        while ($row = mysql_fetch_array($T004)) {
            $sql  = "SELECT (D009_Preco_{$lista}) as preco FROM D009 WHERE D009_Id = '{$row['T004_D009_Id']}'";
            $D009 = mysql_query($sql);
            $res  = mysql_fetch_array($D009);
            if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
                $sql = "UPDATE T004 SET T004_Valor_Preco_Original_Bruto = '{$res['preco']}',
                                    T004_Percentual_Desconto_Bruto=if(T004_Valor_Preco_Original_Bruto>0,(1-(T004_Valor_Preco_Bruto/T004_Valor_Preco_Original_Bruto))*100,0),
                                    T004_Valor_Desconto_Unitario_Bruto=if(T004_Valor_Preco_Original_Bruto>0,(T004_Valor_Preco_Original_Bruto*('{$res['preco']}'/100)),0)
                                    WHERE T004_Id = '{$row['T004_Id']}'";
                mysql_query($sql);
            } else {
                $sql = "UPDATE T004 SET T004_Valor_Preco_Original = '{$res['preco']}',
                                    T004_Percentual_Desconto = if(T004_Valor_Preco_Original>0,(1-(T004_Valor_Preco_Unitario/T004_Valor_Preco_Original))*100,0),
                                    T004_Valor_Desconto_Unitario = if(T004_Valor_Preco_Original>0,(T004_Valor_Preco_Original*('{$res['preco']}'/100)),0)
                                    WHERE T004_Id = '{$row['T004_Id']}'";
                mysql_query($sql);
            }
        }
        mysqli_query("CALL T003_Gravar_Totalizacao_4('{$T003_Id}')");
        return true;
    }
    
    /**
     * updateCheckboxDividir
     *
     * @param String $id
     * @param String $selected
     * @param String $TXXX
     * @return true
     */     
    public function updateCheckboxDividir($id, $selected, $TXXX)
    {
        global $g;
        
        $id       = mysql_real_escape_string($id);
        $selected = mysql_real_escape_string($selected);
        $TXXX     = mysql_real_escape_string($TXXX);
        
        mysql_query("UPDATE {$TXXX} SET {$TXXX}_Flag_Dividir='$selected' WHERE {$TXXX}_Id='{$id}'");
        return true;
    }
    
    /**
     * VEN001MudarFilial
     *
     * @param String $T003_Id_Antigo
     * @param String $C004_Id_Novo
     * @return true
     */     
    public function VEN001MudarFilial($T003_Id_Antigo, $C004_Id_Novo)
    {
        global $g, $novoT003_Id;
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        $D024  = mysql_query("SELECT T003_D024_Id, T003_C004_Id_Estoque, T003_C004_Id FROM T003 WHERE T003_Id='{$T003_Id_Antigo}'");
        $mD024 = mysql_fetch_array($D024);
        
        //Caso o estoque seja do C004 do orçamento, utilizamos o C004 da empresa a qual o orçamento foi transferido, caso contrario usamos o estoque que estava setado no orçamento
        $T003_C004_Id_Estoque = ($mD024['T003_C004_Id_Estoque'] == $mD024['T003_C004_Id']) ? $C004_Id_Novo : $mD024['T003_C004_Id_Estoque'];

        $D006_Id = $this->retornaCFOP($mD024['T003_D024_Id'], false, $C004_Id_Novo, false, false, false, false, $T003_C004_Id_Estoque);
        
        $sqlUpT003    = "UPDATE T003 SET T003_C004_Id='{$C004_Id_Novo}', T003_D006_Id='{$D006_Id}' WHERE T003_Id='{$T003_Id_Antigo}'";
        $resultUpT003 = mysql_query($sqlUpT003);
        
        if (!$resultUpT003) {
            mysql_query("ROLLBACK");
            return '1' . mysql_error();
        }
        
        // Nesse método (mudar filial) não é criado um novo pedido, é apenas alterado o C004
        $novoT003_Id = $T003_Id_Antigo;
        
        // Seleciona os itens do pedido para ver se existe algum marcado
        $sqlT004    = "SELECT * FROM T004 WHERE T004_T003_Id='{$T003_Id_Antigo}';";
        $resultT004 = mysql_query($sqlT004);
        
        // Usa a SQL da T004 feita no início do método para fazer UPDATE dos teins que foram selecionados para gerar outro pedido 
        while ($linhaT004 = mysql_fetch_array($resultT004)) {
            /**
             * Buscando o D009 correto para a empresa selecionada.
             */
            $sqlD049    = "SELECT D009_D049_Id FROM D009 WHERE D009_Id='{$linhaT004['T004_D009_Id']}'";
            $resultD049 = mysql_query($sqlD049);
            $linhaD049  = mysql_fetch_array($resultD049);
            $erroSQL    = mysql_error();
            if (!empty($erroSQL)) {
                mysql_query("ROLLBACK");
                return '2...' . $erroSQL;
            }
            
            $sqlD009    = "SELECT D009_Id FROM D009 WHERE D009_D049_Id='{$linhaD049['D009_D049_Id']}' AND D009_C004_Id='{$C004_Id_Novo}'";
            $resultD009 = mysql_query($sqlD009);
            $linhaD009  = mysql_fetch_array($resultD009);
            $erroSQL    = mysql_error();
            if (!empty($erroSQL)) {
                mysql_query("ROLLBACK");
                return '3' . $erroSQL;
            }
            if (empty($linhaD009['D009_Id'])) {
                mysql_query("ROLLBACK");
                return 'D009_Id não encontrado';
            }
            
            /**
             * Buscando a CFOP correta para cada item
             */
            $D006_Id = $this->retornaCFOP($mD024['T003_D024_Id'], $linhaD009['D009_Id'], $C004_Id_Novo, false, false, false, false, $T003_C004_Id_Estoque);
            
            /**
             * Faz UPDATE nos itens do pedido alterando o T005, C004 (id da empresa nova) e o D009 da nova empresa
             */
            $upT004 = "UPDATE T004 SET T004_C004_Id='{$C004_Id_Novo}', T004_D009_Id='{$linhaD009['D009_Id']}', T004_D006_Id='{$D006_Id}' WHERE T004_Id='{$linhaT004['T004_Id']}'";
            mysql_query($upT004);
            $erroSQL = mysql_error();
            if (!empty($erroSQL)) {
                mysql_query("ROLLBACK");
                return '4' . $erroSQL;
            }
            //mysql_query("call T006_Gravar_Totalizacao_4('{$linhaT006['T006_Id']}')");
        }
        
        /**
         *  selecionando todas as trocas do pedido
         */
        $sqlT138    = "SELECT * FROM T138 WHERE T138_T003_Id='{$T003_Id_Antigo}';";
        $resultT138 = mysql_query($sqlT138);
        while ($linhaT138 = mysql_fetch_array($resultT138)) {
            
            /**
             * Buscando o D009 correto para a empresa selecionada.
             */
            $sqlD049    = "SELECT D009_D049_Id FROM D009 WHERE D009_Id='{$linhaT138['T138_D009_Id']}'";
            $resultD049 = mysql_query($sqlD049);
            $linhaD049  = mysql_fetch_array($resultD049);
            $erroSQL    = mysql_error();
            if (!empty($erroSQL)) {
                mysql_query("ROLLBACK");
                return '2...' . $erroSQL;
            }
            
            $sqlD009    = "SELECT D009_Id FROM D009 WHERE D009_D049_Id='{$linhaD049['D009_D049_Id']}' AND D009_C004_Id='{$C004_Id_Novo}'";
            $resultD009 = mysql_query($sqlD009);
            $linhaD009  = mysql_fetch_array($resultD009);
            $erroSQL    = mysql_error();
            if (!empty($erroSQL)) {
                mysql_query("ROLLBACK");
                return '3' . $erroSQL;
            }
            if (empty($linhaD009['D009_Id'])) {
                mysql_query("ROLLBACK");
                return '2. D009_Id não encontrado';
            }
            
            $upT138     = "UPDATE T138 SET T138_C004_Id='{$C004_Id_Novo}', T138_D009_Id='{$linhaD009['D009_Id']}' WHERE T138_Id='{$linhaT138['T138_Id']}'";
            $resultT138 = mysql_query($upT138);
            if (!$resultT138) {
                mysql_query("ROLLBACK");
                return '4' . mysql_error();
            }
        }
        //mysql_query("call T005_Gravar_Totalizacao_4('{$T005_Id_Novo}')");
        $ret = $this->atualizarAliquotas($novoT003_Id, $C004_Id_Novo);
        if ($ret === true) {
            mysql_query("COMMIT");
            return true;
        } else {
            mysql_query("ROLLBACK");
            return $ret;
        }
    }
    
    /**
     * VEN001DividirOrcamento
     *
     * @param String $T003_Id_Antigo
     * @param String $C004_Id_Novo
     * @return bool
     */     
    public function VEN001DividirOrcamento($T003_Id_Antigo, $C004_Id_Novo)
    {
        global $g, $novoT003_Id;
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        // seleciona o cabeçalho do orçamento para inserir um cópia dele com o novo C004 
        $T003  = mysql_query("SELECT * FROM T003 WHERE T003_Id='{$T003_Id_Antigo}'");
        $mT003 = mysql_fetch_assoc($T003);
        
        // Buscando a CFOP para o cabeçalho do orçamento
        $D006_Id = $this->retornaCFOP($mT003['T003_D024_Id'], false, $C004_Id_Novo, false, false, false, false, $mT003['T003_C004_Id_Estoque']);
        
        $campos  = "";
        $valores = "";
        foreach ($mT003 as $key => $value) {
            if ($key == 'T003_Id') {
                continue;
            } elseif ($key == 'T003_C004_Id') {
                
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string($C004_Id_Novo) . "',";
                
            } elseif ($key == 'T003_D006_Id') {
                
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string($D006_Id) . "',";
                
            } else {
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string($value) . "',";
            }
        }
        $campos  = trim($campos, ', ');
        $valores = trim($valores, ',');
        $insT003 = "INSERT INTO T003 ({$campos}) VALUES ({$valores})";
        
        // executa a inserção na T005 com o  novo
        $resultInsT003 = mysql_query($insT003);
        if (!$resultInsT003) {
            mysql_query("ROLLBACK");
            die($insT005);
            return '1' . mysql_error();
        }
        
        $novoT003_Id = $g['mysqlLastId'];
        
        // Seleciona os itens do pedido para ver se existe algum marcado
        $sqlT004    = "SELECT * FROM T004 WHERE T004_T003_Id='{$T003_Id_Antigo}' AND T004_Flag_Dividir='S';";
        $resultT004 = mysql_query($sqlT004);
        
        // Usa a SQL da T004 feita no início do método para fazer UPDATE dos teins que foram selecionados para gerar outro pedido 
        while ($linhaT004 = mysql_fetch_array($resultT004)) {
            /**
             * Buscando o D009 correto para a empresa selecionada.
             */
            $sqlD049    = "SELECT D009_D049_Id FROM D009 WHERE D009_Id='{$linhaT004['T004_D009_Id']}'";
            $resultD049 = mysql_query($sqlD049);
            $linhaD049  = mysql_fetch_array($resultD049);
            $erroSQL    = mysql_error();
            if (!empty($erroSQL)) {
                mysql_query("ROLLBACK");
                return '2...' . $erroSQL;
            }
            
            $sqlD009    = "SELECT D009_Id FROM D009 WHERE D009_D049_Id='{$linhaD049['D009_D049_Id']}' AND D009_C004_Id='{$C004_Id_Novo}'";
            $resultD009 = mysql_query($sqlD009);
            $linhaD009  = mysql_fetch_array($resultD009);
            $erroSQL    = mysql_error();
            if (!empty($erroSQL)) {
                mysql_query("ROLLBACK");
                return '3' . $erroSQL;
            }
            if (empty($linhaD009['D009_Id'])) {
                mysql_query("ROLLBACK");
                return 'D009_Id não encontrado';
            }
            
            /**
             * Buscando a CFOP correta para cada item
             */
            $D006_Id = $this->retornaCFOP($mT003['T003_D024_Id'], $linhaD009['D009_Id'], $C004_Id_Novo, false, false, false, false, $mT003['T003_C004_Id_Estoque']);
            
            /**
             * Faz UPDATE nos itens do pedido alterando o T003, C004 (id da empresa nova) e o D009 da nova empresa
             */
            $upT004 = "UPDATE T004 SET T004_T003_Id='{$novoT003_Id}', T004_C004_Id='{$C004_Id_Novo}', T004_Flag_Dividir='N', T004_D009_Id='{$linhaD009['D009_Id']}', T004_D006_Id='{$D006_Id}' WHERE T004_Id='{$linhaT004['T004_Id']}'";
            mysql_query($upT004);
            $erroSQL = mysql_error();
            if (!empty($erroSQL)) {
                mysql_query("ROLLBACK");
                return '4' . $erroSQL;
            }
            //mysql_query("call T004_Gravar_Totalizacao_4('{$linhaT004['T006_Id']}')");
        }
        
        /**
         *  selecionando todos os serviços do pedido que tenham marcado a flag dividir 
         */
        $sqlT145    = <<<EOT
        SELECT * FROM T145 WHERE T145_T003_Id='{$T003_Id_Antigo}' AND T145_Flag_Dividir='S';
EOT;
        $resultT145 = mysql_query($sqlT145);
        while ($linhaT145 = mysql_fetch_array($resultT145)) {
            $upT145       = "UPDATE T145 SET T145_T003_Id='{$novoT003_Id}', T145_Flag_Dividir='N' WHERE T145_Id='{$linhaT145['T145_Id']}'";
            $resultUpT145 = mysql_query($upT145);
            if (!$resultUpT145) {
                mysql_query("ROLLBACK");
                return '3' . mysql_error();
            }
        }
        
        /**
         *  selecionando todas as trocas do pedido
         */
        $sqlT138    = "SELECT * FROM T138 WHERE T138_T003_Id='{$T003_Id_Antigo}' AND T138_Flag_Dividir='S';";
        $resultT138 = mysql_query($sqlT138);
        while ($linhaT138 = mysql_fetch_array($resultT138)) {
            
            /**
             * Buscando o D009 correto para a empresa selecionada.
             */
            $sqlD049    = "SELECT D009_D049_Id FROM D009 WHERE D009_Id='{$linhaT138['T138_D009_Id']}'";
            $resultD049 = mysql_query($sqlD049);
            $linhaD049  = mysql_fetch_array($resultD049);
            $erroSQL    = mysql_error();
            if (!empty($erroSQL)) {
                mysql_query("ROLLBACK");
                return '2...' . $erroSQL;
            }
            
            $sqlD009    = "SELECT D009_Id FROM D009 WHERE D009_D049_Id='{$linhaD049['D009_D049_Id']}' AND D009_C004_Id='{$C004_Id_Novo}'";
            $resultD009 = mysql_query($sqlD009);
            $linhaD009  = mysql_fetch_array($resultD009);
            $erroSQL    = mysql_error();
            if (!empty($erroSQL)) {
                mysql_query("ROLLBACK");
                return '3' . $erroSQL;
            }
            if (empty($linhaD009['D009_Id'])) {
                mysql_query("ROLLBACK");
                return '2. D009_Id não encontrado';
            }
            
            $upT138      = "UPDATE T138 SET T138_T003_Id='{$novoT003_Id}', T138_Flag_Dividir='N', T138_C004_Id='{$C004_Id_Novo}', T138_D009_Id='{$linhaD009['D009_Id']}' WHERE T138_Id='{$linhaT138['T138_Id']}'";
            $xresultT138 = mysql_query($upT138);
            if (!$xresultT138) {
                mysql_query("ROLLBACK");
                return '4' . mysql_error();
            }
        }
        mysql_query("call T003_Gravar_Totalizacao_4('{$novoT003_Id}')");
        mysql_query("call T003_Gravar_Totalizacao_4('{$T003_Id_Antigo}')");
        
        $ret = $this->atualizarAliquotas($novoT003_Id, $C004_Id_Novo);
        if ($ret === true) {
            mysql_query("COMMIT");
            return true;
        } else {
            mysql_query("ROLLBACK");
            return $ret;
        }
    }
    
    /**
     * retornaltimoPrecoVenda
     *
     * @param String $D024_Id
     * @param String $D009_Id
     * @return String
     */
    public function retornaltimoPrecoVenda($D024_Id, $D009_Id, $T003_Id = 0, $T005_Id = 0)
    {
        global $g;


        if ($g['C031']['exibirHistoricoFormVendaUltimoOrcamento'] == 'N' &&
            $g['C031']['exibirHistoricoFormVendaUltimoPedido'] == 'N' &&
            $g['C031']['exibirHistoricoFormVendaUltimaNotaFiscal'] == 'N') {

            return "Histórico desabilitado";

        }
        
        $retorno = "";
       
        if (empty($D024_Id)) {
            return "D024_Id não recebido";
        } else if (empty($D009_Id)) {
            return "D009_Id não recebido";
        }

        if($D009_Id > 0){
            $sqlD009 = mysql_query("SELECT D009_D049_Id FROM D009 WHERE D009_Id = '{$D009_Id}'");
            $resD009 = mysql_fetch_array($sqlD009);
            $D049_Id = $resD009['D009_D049_Id'];
        }
        
        if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
            $precoOrcamento = "T004_Valor_Preco_Bruto";
            $precoPedido    = "T006_Valor_Preco_Bruto";
        } else {
            $precoOrcamento = "T004_Valor_Preco_Unitario";
            $precoPedido    = "T006_Valor_Preco_Sem_Desconto_Unitario";
        }
        
        $D024  = mysql_query("SELECT D024_Cnpj_Matriz FROM D024 WHERE D024_Id='{$D024_Id}'");
        $mD024 = mysql_fetch_array($D024);
        if (!empty($mD024['D024_Cnpj_Matriz'])) {
            $extra = " OR D024_Cnpj_Matriz='{$mD024['D024_Cnpj_Matriz']}' ";
        } else {
            $extra = "";
        }

        if ($g['C031']['exibirHistoricoFormVendaUltimoOrcamento'] == 'S') {

            $extraT003 = ($T003_Id > 0) ? " AND T003_Id != '{$T003_Id}'" : "";
    
            // Selecionando o último orçamento desse D009 para esse D024
            $T003  = mysql_query("SELECT T003_Id, 
                        D037_Unidade, 
                        T004_Quantidade, 
                        $precoOrcamento,
                        T003_Data_Emissao,
                        C007_Primeiro_Nome,
                        T003_C004_Id,
                        D018_UF,
                        T003_Casas_Decimais_Preco
                        FROM T004
                        LEFT JOIN T003 ON T003_Id=T004_T003_Id
                        LEFT JOIN D037 ON D037_Id=T004_D037_Id
                        LEFT JOIN D024 ON D024_Id=T003_D024_Id
                        LEFT JOIN C007 ON C007_Id=T003_C007_Id_Vendedor_Interno
                        LEFT JOIN D009 ON D009_Id=T004_D009_Id
                        LEFT JOIN C004 ON C004_Id=D009_C004_Id
                        LEFT JOIN D018 ON D018_Id=C004_D018_Id
                    WHERE D009_D049_Id='{$D049_Id}'
                        AND (T003_D024_Id='{$D024_Id}'{$extra})
                        {$extraT003}
                        ORDER BY T003_Id DESC LIMIT 1");
            $mT003 = mysql_fetch_assoc($T003);
            $mT003['C007_Primeiro_Nome'] = trim(substr($mT003['C007_Primeiro_Nome'], 0, 8));

            if (mysql_num_rows($T003) > 0) {
                // Caso exista algum orçamento já é retornado o mais recente
                $mT003[$precoOrcamento]     = gCorrigeNumero($mT003[$precoOrcamento],$mT003['T003_Casas_Decimais_Preco']);
                $mT003['T003_Data_Emissao'] = gCorrigeData($mT003['T003_Data_Emissao']);
                $mT003['T004_Quantidade']   = gCorrigeNumero($mT003['T004_Quantidade']);
                
                $retorno .= "{$mT003['T003_Data_Emissao']} R\$ {$mT003[$precoOrcamento]} Qt:{$mT003['T004_Quantidade']} O:{$mT003['T003_Id']} {$mT003['C007_Primeiro_Nome']} {$mT003['D018_UF']}\n";
            }
        }

        if ($g['C031']['exibirHistoricoFormVendaUltimoPedido'] == 'S') {

            $extraT005 = ($T005_Id > 0) ? " AND T005_Id != '{$T005_Id}'" : "";

            // Selecionando o último pedido desse D009 para esse D024
            $T005  = mysql_query("SELECT T005_Id, 
                            D037_Unidade, 
                            T006_Quantidade, 
                            $precoPedido, 
                            T005_Data_Emissao, 
                            T005_Nome_Status AS StatusPedido, 
                            IFNULL(T007_Numero_Nota_Fiscal,0) AS NumeroNota,
                            C007_Primeiro_Nome,
                            T005_C004_Id,
                            D018_UF,
                            T005_Casas_Decimais_Preco
                        FROM T006
                        LEFT JOIN T005 ON T005_Id=T006_T005_Id
                        LEFT JOIN D037 ON D037_Id=T006_D037_Id
                        LEFT JOIN T007 ON T007_T005_Id=T005_Id
                        LEFT JOIN D024 ON D024_Id=T005_D024_Id
                        LEFT JOIN C007 ON C007_Id=T005_C007_Id_Vendedor_Interno
                        LEFT JOIN D009 ON D009_Id=T006_D009_Id
                        LEFT JOIN C004 ON C004_Id=D009_C004_Id
                        LEFT JOIN D018 ON D018_Id=C004_D018_Id
                        WHERE D009_D049_Id='{$D049_Id}'
                        AND (T005_D024_Id='{$D024_Id}'{$extra})
                        AND T005_Flag_Status!=8
                        {$extraT005}
                        ORDER BY T005_Id DESC LIMIT 1");
            $mT005 = mysql_fetch_assoc($T005);
            $mT005['C007_Primeiro_Nome'] = trim(substr($mT005['C007_Primeiro_Nome'], 0, 8));
        
            if (mysql_num_rows($T005) > 0) {
                // Caso exista algum pedido, já é retornado o valor mais recente
                $mT005[$precoPedido]        = gCorrigeNumero($mT005[$precoPedido],$mT005['T005_Casas_Decimais_Preco']);
                $mT005['T005_Data_Emissao'] = gCorrigeData($mT005['T005_Data_Emissao']);
                $mT005['T006_Quantidade']   = gCorrigeNumero($mT005['T006_Quantidade']);
                
                $retorno .= "{$mT005['T005_Data_Emissao']} R\$ {$mT005[$precoPedido]} Qt:{$mT005['T006_Quantidade']} P:{$mT005['T005_Id']} {$mT005['C007_Primeiro_Nome']} {$mT005['D018_UF']}\n";
            } 
        }

        if ($g['C031']['exibirHistoricoFormVendaUltimaNotaFiscal'] == 'S') {

            // Seleciona a última NF desse D009 para esse D024
            $T007  = mysql_query("SELECT T007_Id, 
                            T007_Flag_ACP,
                            D037_Unidade, 
                            T008_Quantidade, 
                            T008_Valor_Preco_Sem_Desconto_Unitario, 
                            T007_Numero_Nota_Fiscal,
                            T007_Data_Emissao,
                            C007_Primeiro_Nome,
                            T007_C004_Id,
                            D018_UF,
                            T007_Casas_Decimais_Preco
                        FROM T008
                        LEFT JOIN T007 ON T007_Id=T008_T007_Id
                        LEFT JOIN D037 ON D037_Id=T008_D037_Id
                        LEFT JOIN D024 ON D024_Id=T007_D024_Id
                        LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno
                        LEFT JOIN D009 ON D009_Id=T008_D009_Id
                        LEFT JOIN C004 ON C004_Id=D009_C004_Id
                        LEFT JOIN D018 ON D018_Id=C004_D018_Id
                        WHERE D009_D049_Id='{$D049_Id}'
                        AND (T007_D024_Id='{$D024_Id}'{$extra})
                        ORDER BY T007_Id DESC LIMIT 1");
            $mT007 = mysql_fetch_assoc($T007);

            if($mT007['T007_Flag_ACP'] == '6'){
                $tipoDocumento = 'N';
            } else if($mT007['T007_Flag_ACP'] == '3'){
                $tipoDocumento = 'A';
            } else if($mT007['T007_Flag_ACP'] == '3'){
                $tipoDocumento = 'C';
            }

            $mT007['C007_Primeiro_Nome'] = trim(substr($mT007['C007_Primeiro_Nome'], 0, 8));

            if (mysql_num_rows($T007) > 0) {

                // Caso exista alguma nota, já é retornado o valor mais recente
                $mT007['T008_Valor_Preco_Sem_Desconto_Unitario'] = gCorrigeNumero($mT007['T008_Valor_Preco_Sem_Desconto_Unitario'],$mT007['T007_Casas_Decimais_Preco']);
                $mT007['T007_Data_Emissao']                      = gCorrigeData($mT007['T007_Data_Emissao']);
                $mT007['T008_Quantidade']                        = gCorrigeNumero($mT007['T008_Quantidade']);
                
                $retorno .= "{$mT007['T007_Data_Emissao']} R\$ {$mT007['T008_Valor_Preco_Sem_Desconto_Unitario']} Qt:{$mT007['T008_Quantidade']} {$tipoDocumento}:{$mT007['T007_Numero_Nota_Fiscal']} {$mT007['C007_Primeiro_Nome']} {$mT007['D018_UF']}";

            } else {

                // Seleciona a última NF desse D009 para esse D024 pelo historico
                $T001  = mysql_query("SELECT T001_Id, 
                                T001_Quantidade, 
                                T001_Valor_Preco_Unitario, 
                                T001_Descricao_Operacao,
                                T001_Data_Lancamento,
                                C007_Primeiro_Nome,
                                T001_C004_Id,
                                D018_UF
                        FROM T001
                            LEFT JOIN D024 ON D024_Id=T001_D024_Id_Cliente
                            LEFT JOIN T008 ON T008_Id=T001_T008_Id
                            LEFT JOIN T007 ON T007_Id=T008_T007_Id
                            LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno
                            LEFT JOIN D009 ON D009_Id=T001_D009_Id
                            LEFT JOIN C004 ON C004_Id=D009_C004_Id
                            LEFT JOIN D018 ON D018_Id=C004_D018_Id
                            WHERE (D024_Id='{$D024_Id}' {$extra})
                            AND D009_D049_Id='{$D049_Id}'
                            AND T001_Flag_Operacao='S'
                            ORDER BY T001_Id DESC LIMIT 1");
                $mT001 = mysql_fetch_assoc($T001);
                $mT001['C007_Primeiro_Nome'] = trim(substr($mT001['C007_Primeiro_Nome'], 0, 8));

                if (mysql_num_rows($T001) > 0) {
                    // Caso exista alguma nota, já é retornado o valor mais recente
                    $mT001['T001_Valor_Preco_Unitario'] = gCorrigeNumero($mT001['T001_Valor_Preco_Unitario'],$g['C004']['C004_Casas_Decimais_Preco']);
                    $mT001['T001_Data_Lancamento']      = gCorrigeData($mT001['T001_Data_Lancamento']);
                    $mT001['T001_Quantidade']           = gCorrigeNumero($mT001['T001_Quantidade']);
                    
                    $retorno .= "{$mT001['T001_Data_Lancamento']} R\$ {$mT001['T001_Valor_Preco_Unitario']} Qt:{$mT001['T001_Quantidade']} {$mT001['T001_Descricao_Operacao']} {$mT001['C007_Primeiro_Nome']} {$mT001['D018_UF']}";
                }
                
            } 

        }
      
        if($retorno == ''){
            return "Primeira venda";
        } else {
            return  $retorno;
        }
    }

    /**
     * duplicarPedidosSelecionados
     *
     * @param String $dias
     * @return String
     */    
    public function xduplicarPedidosSelecionados($dias)
    {
        global $g;
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        $sqlT005 = mysql_query("SELECT * FROM T005 ORDER BY T005_Processo_Id DESC LIMIT 1");
        $resT005 = mysql_fetch_array($sqlT005);
        $sqlC007 = mysql_query("SELECT * FROM C007 WHERE C007_Id = '{$g['usuarioAtual']}'");
        $resC007 = mysql_fetch_array($sqlC007);
        $sqlC004 = mysql_query("SELECT * FROM C004 WHERE C004_Id = '{$g['empresaAtual']}'");
        $resC004 = mysql_fetch_array($sqlC004);
        
        $processoLog = 'Duplicação de Pedido<br />';
        $processoLog .= 'Usuario: ' . $resC007['C007_Primeiro_Nome'] . '(' . $g['usuarioAtual'] . ')<br />';
        $processoLog .= 'Empresa: ' . $resC004['C004_Nome_Abreviado'] . '(' . $g['empresaAtual'] . ')<br />';
        $processoLog .= 'Intervalo de dias: ' . $dias . '<br />';
        $processoLog .= 'Data: ' . date('d/m/Y', time()) . ' - Hora: ' . date('H:i:s', time());
        
        $lastProcessoId = $resT005['T005_Processo_Id'] + 1;
        
        $AllT005 = mysql_query("SELECT * FROM T005 WHERE T005_Flag_Marcado='S'");
        while ($mT005 = mysql_fetch_assoc($AllT005)) {
            //$Data_Emissao = strtotime($mT005['T005_Data_Emissao']);
            
            $Data_Emissao = date("Y-m-d", strtotime(date("Y-m-d", strtotime($mT005['T005_Data_Emissao'])) . " +{$dias} day"));
            //$Data_Entrega = date("Y-m-d",strtotime(date("Y-m-d", strtotime($mT005['T005_Data_Emissao'])) . " +{$dias} day"));
            
            $T005_Id_Antigo = $mT005['T005_Id'];
            $campos         = "";
            $valores        = "";
            foreach ($mT005 as $key => $value) {
                if ($key == 'T005_Id') {
                    continue;
                } elseif ($key == 'T005_Data_Emissao') {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($Data_Emissao) . "',";
                } elseif ($key == 'T005_Data_Saida') {
                    $campos .= $key . ", ";
                    $valores .= "CURDATE(),";
                } elseif ($key == 'T005_Flag_Status') {
                    $campos .= $key . ", ";
                    $valores .= "'0',";
                } elseif ($key == 'T005_Nome_Status') {
                    $campos .= $key . ", ";
                    $valores .= "T005_Status_Pedido (T005_Flag_Status, 1), ";
                } else {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($value) . "',";
                }
            }
            $campos  = trim($campos, ', ');
            $valores = trim($valores, ',');
            $insT005 = "INSERT INTO T005 ({$campos}) VALUES ({$valores})";
            $resT005 = mysql_query($insT005);
            if (!$resT005) {
                mysql_query("ROLLBACK");
                return "Erro 1: " . mysql_error();
            }
            $T005_Id_Novo = $g['mysqlLastId'];
            mysql_query("UPDATE T005 SET T005_Processo_Id = '{$lastProcessoId}', T005_Processo_Log = '{$processoLog}' WHERE T005_Id = '{$T005_Id_Novo}'");
            
            // Selecionando todos os itens do pedido
            $T006 = mysql_query("SELECT * FROM T006 WHERE T006_T005_Id='{$T005_Id_Antigo}'");
            
            while ($mT006 = mysql_fetch_assoc($T006)) {
                $campos  = "";
                $valores = "";
                foreach ($mT006 as $key => $value) {
                    if ($key == 'T006_Id') {
                        continue;
                    } elseif ($key == "T006_T005_Id") {
                        $campos .= $key . ", ";
                        $valores .= "'" . mysql_real_escape_string($T005_Id_Novo) . "',";
                    } elseif ($key == "T006_C004_Id_Estoque") {
                        $campos .= $key . ", ";
                        $valores .= "NULL,";
                    } else {
                        $campos .= $key . ", ";
                        $valores .= "'" . mysql_real_escape_string($value) . "',";
                    }
                }
                $campos  = trim($campos, ', ');
                $valores = trim($valores, ',');
                $insT006 = "INSERT INTO T006 ({$campos}) VALUES ({$valores})";
                $resT006 = mysql_query($insT006);
                if (!$resT006) {
                    mysql_query("ROLLBACK");
                    return "Erro 2: " . mysql_error();
                }
            }
            
            // Selecionando todos os itens do pedido
            $T090 = mysql_query("SELECT * FROM T090 WHERE T090_T005_Id='{$T005_Id_Antigo}'");
            while ($mT090 = mysql_fetch_assoc($T090)) {
                $campos  = "";
                $valores = "";
                foreach ($mT090 as $key => $value) {
                    if ($key == 'T090_Id') {
                        continue;
                    } else if ($key == "T090_T005_Id") {
                        $campos .= $key . ", ";
                        $valores .= "'" . mysql_real_escape_string($T005_Id_Novo) . "',";
                    } else if ($key == "T090_Data_Vencimento") {
                        $campos .= $key . ", ";
                        $valores .= "DATE_ADD('$Data_Emissao', INTERVAL {$mT090['T090_Prazos_Dias']} DAY),";
                    } else {
                        $campos .= $key . ", ";
                        $valores .= "'" . mysql_real_escape_string($value) . "',";
                    }
                }
                $campos  = trim($campos, ', ');
                $valores = trim($valores, ',');
                $insT006 = "INSERT INTO T090 ({$campos}) VALUES ({$valores})";
                $resT006 = mysql_query($insT006);
                if (!$resT006) {
                    mysql_query("ROLLBACK");
                    return "Erro 2: " . mysql_error();
                }
            }
        }
        mysql_query("UPDATE T005 SET T005_Flag_Marcado='N'");
        mysql_query("COMMIT");
        return true;
    }
    
    /**
     * VEN001ClientesProcessar
     *
     * @return true
     */     
    public function VEN001ClientesProcessar()
    {
        global $g;
        
        $D024 = mysql_query("SELECT * FROM D024");
        while ($mD024 = mysql_fetch_array($D024)) {
            mysql_query("UPDATE D024 SET D024_Flag_Comercial='I' WHERE D024_Id='{$mD024['D024_Id']}'");
        }
        
        $D024 = mysql_query("SELECT * FROM D024 WHERE ((SELECT max(T003_Data_Emissao) FROM T003 WHERE T003_D024_Id=D024_Id)>=date_sub(curdate(), interval 90 day) 
                             OR  (SELECT max(T005_Data_Emissao) FROM T005 WHERE T005_D024_Id=D024_Id)>=date_sub(curdate(), interval 90 day) 
                             OR  (SELECT max(T007_Data_Emissao) FROM T007 WHERE T007_D024_Id=D024_Id)>=date_sub(curdate(), interval 90 day)
                 OR  (SELECT max(D085_Data) FROM D085 WHERE D085_D024_Id=D024_Id)>=date_sub(curdate(), interval 90 day))");
        while ($mD024 = mysql_fetch_array($D024)) {
            mysql_query("UPDATE D024 SET D024_Flag_Comercial='A' WHERE D024_Id='{$mD024['D024_Id']}'");
        }
        
        /*
        $sqlD024 = mysql_query("SELECT * FROM D024 WHERE ((SELECT max(T007_Data_Emissao) FROM T007 WHERE T007_D024_Id=D024_Id) >= date_sub(curdate(), interval 30 day))");
        while($D024 = mysql_fetch_array($sqlD024)){
        mysql_query("UPDATE D024 SET D024_Flag_Liberado = 'N' WHERE D024_Id='{$D024['D024_Id']}'");
        }
        */
        return true;
    }
    
    /**
     * observacaoEntrega
     *
     * @param String $D009_Id
     * @param String $T004_Quantidade
     * @param String $T004_Codigo_Produto
     * @return String/bool
     */     
    public function observacaoEntrega($D009_Id, $T004_Quantidade, $T004_Codigo_Produto)
    {
        global $g;
        $T004_Codigo_Produto = trim($T004_Codigo_Produto);
        $T004_Quantidade     = formata_valor_gravar($T004_Quantidade, 3);
        
        
        if (($T004_Quantidade > 0) AND (!empty($T004_Codigo_Produto))) {
            $cD009 = mysql_query("SELECT (SELECT sum((ET.D009_Quantidade_Estoque_Real-D009_Quantidade_Pedido(ET.D009_Id,1))+D009_Quantidade_Montagem(ET.D009_Id)) FROM D009 as ET WHERE ET.D009_D049_Id=D049_Id AND ET.D009_C004_Id<>'{$g['empresaAtual']}') as D009_Estoque_Lojas,
                                    (SELECT sum((ET.D009_Quantidade_Estoque_Real-D009_Quantidade_Pedido(ET.D009_Id,1))+D009_Quantidade_Montagem(ET.D009_Id)) FROM D009 as ET WHERE ET.D009_Id='{$D009_Id}') as D009_Estoque_Local
                                FROM D001
                          LEFT JOIN D002 ON D002_Id=D001_D002_Id
                          LEFT JOIN D049 ON D049_D001_Id=D001_Id
                          LEFT JOIN D009 ON D009_D049_Id=D049_Id
                          LEFT JOIN D024 ON D024_Id=D049_D024_Id
                              WHERE D001_Codigo_Produto='{$T004_Codigo_Produto}'
                                AND (SELECT sum(T1.D009_Quantidade_Estoque_Real)
                                FROM D009 as T1
                          LEFT JOIN D049 as T2 ON T2.D049_Id=T1.D009_D049_Id
                              WHERE T2.D049_D001_Id=D001_Id
                                AND T2.D049_D024_Id=D024_Id)>0
                                AND D009_C004_Id='{$g['empresaAtual']}'");
            
            $mD009 = mysql_fetch_array($cD009);
            if (mysql_num_rows($cD009) == 0) {
                $T004_Observacao_Entrega = 'INDISPONIVEL';
            } else if ($mD009['D009_Estoque_Local'] >= $T004_Quantidade) {
                $T004_Observacao_Entrega = 'IMEDIATO';
            } else if (($mD009['D009_Estoque_Local'] > 0) AND ($T004_Quantidade > $mD009['D009_Estoque_Local'])) {
                $T004_Observacao_Entrega = $mD009['D009_Estoque_Local'] . ' IMDTO';
                if ($mD009['D009_Estoque_Lojas'] > 0) {
                    $T004_Observacao_Entrega .= '/';
                    $T004_Observacao_Entrega .= ($T004_Quantidade - $mD009['D009_Estoque_Local']) . ' 24 HS';
                }
            } else if ($mD009['D009_Estoque_Lojas'] > 0) {
                $T004_Observacao_Entrega = '24 HORAS';
            } else {
                $T004_Observacao_Entrega = 'INDISPONIVEL';
            }
            return $T004_Observacao_Entrega;
        }
        return false;
    }
    
    /**
     * retornaAliquotas
     *
     * @param String $D009_Id
     * @param String $T003_Id
     * @param String $T005_Id
     * @param String $T007_Id
     * @param String $T004_Id
     * @param String $T006_Id
     * @param String $T008_Id
     * @param String $CFOP
     * @return String/array
     */     
    public function retornaAliquotas($D009_Id, $T003_Id = false, $T005_Id = false, $T007_Id = false, $T004_Id = false, $T006_Id = false, $T008_Id = false, $CFOP = false, $D024_Id_Enviado = false)
    {
        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();
        global $g;
        
        $mProduto = array();
        
        if ($T003_Id > 0) {
            $cClausula  = " SELECT ";
            $cClausula .= " D018_UF,";
            $cClausula .= " D053_ST_ICMS_Inter_Estadual,";
            $cClausula .= " D053_ST_ICMS_Interno,";
            $cClausula .= " D053_ST_ICMS_Inter_Estadual,";
            $cClausula .= " C004_D018_Id,";
            $cClausula .= " D024_D018_Id,";
            $cClausula .= " D053_Percentual_FCP,";
            $cClausula .= " C004_Percentual_PIS,";
            $cClausula .= " C004_Percentual_COFINS,";
            $cClausula .= " C004_Percentual_IRPJ,";
            $cClausula .= " C004_Percentual_CSLL,";
            $cClausula .= " C004_IPV_Padrao_Sem_Lucro,";
            $cClausula .= " T003_D006_Id,";
            $cClausula .= " T003_D024_Id, ";
            $cClausula .= " T003_C004_Id, ";
            $cClausula .= " D024_D018_Id, ";
            $cClausula .= " T003_Flag_Acp, ";
            $cClausula .= " D024_Flag_Revenda_Consumidor, ";
            $cClausula .= " D024_Codigo_Regime_Tributario, ";
            $cClausula .= " D024_Flag_Contribuinte, ";
            $cClausula .= " T134_ICMS, ";
            $cClausula .= " T003_Percentual_Comissao, ";
            $cClausula .= " D024_Inscricao_Estadual, ";  
            $cClausula .= " D024_Flag_Pessoa_Fisica_Juridica, ";
            $cClausula .= " D053_Aliquota_ICMS_1, ";
            $cClausula .= " D053_Aliquota_ICMS_2, ";
            $cClausula .= " D053_Aliquota_ICMS_Importado, ";
            $cClausula .= " D053_Aliquota_ICMS_Importado_2, ";
            $cClausula .= " D053_Flag_Ajusta_MVA, ";
            $cClausula .= " T003_D148_Id_Entrega ";
            $cClausula .= " FROM T003 ";
            $cClausula .= " LEFT JOIN D024 ON D024_Id=T003_D024_Id ";
            $cClausula .= " LEFT JOIN D018 ON D018_Id=D024_D018_Id ";
            $cClausula .= " LEFT JOIN D053 ON D053_D018_Id=D024_D018_Id AND D053_C004_Id=T003_C004_Id ";
            $cClausula .= " LEFT JOIN C004 ON C004_Id=T003_C004_Id ";
            $cClausula .= " LEFT JOIN T134 ON T134_Id=T003_T134_Id ";
            $cClausula .= " LEFT JOIN D006 ON D006_Id=T003_D006_Id ";
            $cClausula .= " WHERE T003_Id='{$T003_Id}' ";
            
            $Cab   = mysql_query($cClausula);
            $cErro = mysql_error();
            
            if (!empty($cErro)) {
                return "{$cErro}<br />{$cClausula}";
            }
            $mCab = mysql_fetch_array($Cab);
            
            $D024_Id                  = $mCab['T003_D024_Id'];
            $C004_Id                  = $mCab['T003_C004_Id'];
            $D018_Id                  = $mCab['D024_D018_Id'];
            $Flag_Acp                 = $mCab['T003_Flag_Acp'];
            $Flag_Revenda_Consumidor  = $mCab['D024_Flag_Revenda_Consumidor'];
            $Codigo_Regime_Tributario = $mCab['D024_Codigo_Regime_Tributario'];
            $Flag_Contribuinte        = $mCab['D024_Flag_Contribuinte'];
            $Percentual_Comissao      = $mCab['T003_Percentual_Comissao'];
            $Aliquota_ICMS_Credito    = $mCab['T134_ICMS'];
            $enderecoEntrega          = $mCab['T003_D148_Id_Entrega'];
            
            $cClausula = " SELECT T004_D006_Id, T004_Flag_Origem_Produto,T004_Flag_Destino_Produto FROM T004 WHERE T004_Id='{$T004_Id}'";
            $sqlT004   = mysql_query($cClausula);
            $cErro     = mysql_error();
            if (!empty($cErro)) {
                return "{$cErro}<br />{$cClausula}";
            }

            $T004                 = mysql_fetch_array($sqlT004);
            $D006_Id              = $T004['T004_D006_Id'];
            $Flag_Origem_Produto  = $T004['T004_Flag_Origem_Produto'];
            $Flag_Destino_Produto = $T004['T004_Flag_Destino_Produto'];
            
        } else if ($T005_Id > 0) {
            $cClausula  = " SELECT";
            $cClausula .= " T005_D024_Id, ";
            $cClausula .= " D018_UF, ";
            $cClausula .= " D053_ST_ICMS_Inter_Estadual, ";
            $cClausula .= " D053_ST_ICMS_Interno, ";
            $cClausula .= " D024_D018_Id, ";
            $cClausula .= " C004_D018_Id, ";
            $cClausula .= " D053_Percentual_FCP, ";
            $cClausula .= " C004_Percentual_PIS, ";
            $cClausula .= " C004_Percentual_COFINS, ";
            $cClausula .= " C004_Percentual_IRPJ, ";
            $cClausula .= " C004_Percentual_CSLL, ";
            $cClausula .= " C004_IPV_Padrao_Sem_Lucro, ";
            $cClausula .= " D053_Percentual_FCP, ";
            $cClausula .= " T005_C004_Id, ";
            $cClausula .= " D024_D018_Id, ";
            $cClausula .= " T005_Flag_ACP, ";
            $cClausula .= " T134_ICMS, ";
            $cClausula .= " D024_Flag_Revenda_Consumidor, ";
            $cClausula .= " D024_Codigo_Regime_Tributario, ";
            $cClausula .= " D024_Flag_Contribuinte, ";
            $cClausula .= " T005_Percentual_Comissao, ";
            $cClausula .= " D024_Inscricao_Estadual, ";
            $cClausula .= " D024_Flag_Pessoa_Fisica_Juridica, ";   
            $cClausula .= " D053_Aliquota_ICMS_1, ";
            $cClausula .= " D053_Aliquota_ICMS_2, ";
            $cClausula .= " D053_Aliquota_ICMS_Importado, ";
            $cClausula .= " D053_Aliquota_ICMS_Importado_2, ";     
            $cClausula .= " D053_Flag_Ajusta_MVA, ";
            $cClausula .= " T005_D148_Id_Entrega ";   
            $cClausula .= " FROM T005 ";
            $cClausula .= " LEFT JOIN D024 ON D024_Id=T005_D024_Id ";
            $cClausula .= " LEFT JOIN D018 ON D018_Id=D024_D018_Id ";
            $cClausula .= " LEFT JOIN D053 ON D053_D018_Id=D024_D018_Id AND D053_C004_Id=T005_C004_Id ";
            $cClausula .= " LEFT JOIN C004 ON C004_Id=T005_C004_Id ";
            $cClausula .= " LEFT JOIN T134 ON T134_Id=T005_T134_Id ";
            $cClausula .= " LEFT JOIN D006 ON D006_Id=T005_D006_Id ";
            $cClausula .= " WHERE T005_Id='$T005_Id' ";
            
            $Cab   = mysql_query($cClausula);
            $cErro = mysql_error();
            
            if (!empty($cErro)) {
                return "{$cErro}<br />{$cClausula}";
            }
            $mCab = mysql_fetch_array($Cab);
            
            $D024_Id                  = $mCab['T005_D024_Id'];
            $C004_Id                  = $mCab['T005_C004_Id'];
            $D018_Id                  = $mCab['D024_D018_Id'];
            $Flag_Acp                 = $mCab['T005_Flag_ACP'];
            $Flag_Revenda_Consumidor  = $mCab['D024_Flag_Revenda_Consumidor'];
            $Codigo_Regime_Tributario = $mCab['D024_Codigo_Regime_Tributario'];
            $Flag_Contribuinte        = $mCab['D024_Flag_Contribuinte'];
            $Percentual_Comissao      = $mCab['T005_Percentual_Comissao'];
            $Aliquota_ICMS_Credito    = $mCab['T134_ICMS'];
            $enderecoEntrega          = $mCab['T005_D148_Id_Entrega'];

            $sqlT006 = mysql_query("SELECT T006_Flag_Origem_Produto,T006_D006_Id,T006_Flag_Destino_Produto FROM T006 WHERE T006_Id = '{$T006_Id}'");
            $T006    = mysql_fetch_array($sqlT006);
            $cErro   = mysql_error();
            if (!empty($cErro)) {
                return "{$cErro}<br />{$cClausula}";
            }

            $Flag_Origem_Produto   = $T006['T006_Flag_Origem_Produto'];
            $D006_Id               = $T006['T006_D006_Id'];
            $Flag_Destino_Produto  = $T006['T006_Flag_Destino_Produto'];
            
        } else if ($T007_Id > 0) {
            
            $cClausula = " SELECT ";
            $cClausula .= " T007_D024_Id, ";
            $cClausula .= " T007_C004_Id, ";
            $cClausula .= " D024_D018_Id, ";
            $cClausula .= " D018_UF, ";
            $cClausula .= " D053_ST_ICMS_Inter_Estadual, ";
            $cClausula .= " D053_ST_ICMS_Interno, ";
            $cClausula .= " C004_D018_Id, ";
            $cClausula .= " D053_Percentual_FCP, ";
            $cClausula .= " C004_Percentual_PIS, ";
            $cClausula .= " C004_Percentual_COFINS, ";
            $cClausula .= " C004_Percentual_IRPJ, ";
            $cClausula .= " C004_Percentual_CSLL, ";
            $cClausula .= " C004_IPV_Padrao_Sem_Lucro, ";
            $cClausula .= " D053_Percentual_FCP, ";
            $cClausula .= " T007_Flag_ACP, ";
            $cClausula .= " D024_Flag_Revenda_Consumidor, ";
            $cClausula .= " D024_Codigo_Regime_Tributario, ";
            $cClausula .= " D024_Flag_Contribuinte, ";
            $cClausula .= " T007_Percentual_Comissao_Representante, ";
            $cClausula .= " T134_ICMS, ";
            $cClausula .= " ifnull(D006_Codigo_Beneficio_Fiscal,'') as D006_Codigo_Beneficio_Fiscal, ";
            $cClausula .= " D006_Valor_Codigo_Beneficio_Fiscal, ";
            $cClausula .= " ifnull(D024_Codigo_Beneficio_Fiscal,'') as D024_Codigo_Beneficio_Fiscal, ";
            $cClausula .= " D024_Inscricao_Estadual, ";      
            $cClausula .= " D024_Flag_Pessoa_Fisica_Juridica, ";   
            $cClausula .= " D053_Aliquota_ICMS_1, ";
            $cClausula .= " D053_Aliquota_ICMS_2, ";
            $cClausula .= " D053_Aliquota_ICMS_Importado, ";
            $cClausula .= " D053_Aliquota_ICMS_Importado_2, ";
            $cClausula .= " D053_Flag_Ajusta_MVA, ";
            $cClausula .= " T007_D148_Id_Entrega ";              
            $cClausula .= " FROM T007 ";
            $cClausula .= " LEFT JOIN D024 ON D024_Id=T007_D024_Id ";
            $cClausula .= " LEFT JOIN D018 ON D018_Id=D024_D018_Id ";
            $cClausula .= " LEFT JOIN D053 ON D053_D018_Id=D024_D018_Id AND D053_C004_Id=T007_C004_Id ";
            $cClausula .= " LEFT JOIN C004 ON C004_Id=T007_C004_Id ";
            $cClausula .= " LEFT JOIN T134 ON T134_Id=T007_T134_Id ";
            $cClausula .= " LEFT JOIN D006 ON D006_Id=T007_D006_Id ";
            $cClausula .= " WHERE T007_Id='$T007_Id' ";
            
            $Cab   = mysql_query($cClausula);
            $cErro = mysql_error();
            
            if (!empty($cErro)) {
                return "{$cErro}<br />{$cClausula}";
            }
            $mCab = mysql_fetch_array($Cab);
            
            $D024_Id                  = $mCab['T007_D024_Id'];
            $C004_Id                  = $mCab['T007_C004_Id'];
            $D018_Id                  = $mCab['D024_D018_Id'];
            $Flag_Acp                 = $mCab['T007_Flag_ACP'];
            $Flag_Revenda_Consumidor  = $mCab['D024_Flag_Revenda_Consumidor'];
            $Codigo_Regime_Tributario = $mCab['D024_Codigo_Regime_Tributario'];
            $Flag_Contribuinte        = $mCab['D024_Flag_Contribuinte'];
            $Percentual_Comissao      = $mCab['T007_Percentual_Comissao_Representante'];
            $Aliquota_ICMS_Credito    = $mCab['T134_ICMS'];
            $enderecoEntrega          = $mCab['T007_D148_Id_Entrega'];

            $Flag_Origem_Produto  = '';
            $D006_Id              = '';
            $Flag_Destino_Produto = '';

            if($T008_Id){   
                             
                $sqlT008 = mysql_query("SELECT T008_D006_Id,T008_Flag_Situacao_Tributaria,substring(T008_Flag_Situacao_Tributaria,1,1) as T008_Flag_Origem_Produto,T008_Flag_Destino_Produto FROM T008 WHERE T008_Id = '{$T008_Id}'");
                $cErro   = mysql_error();
                if (!empty($cErro)) {
                    return "{$cErro}<br />{$cClausula}";
                }
                $T008 = mysql_fetch_array($sqlT008);

                $Flag_Origem_Produto  = $T008['T008_Flag_Origem_Produto'];
                $D006_Id              = $T008['T008_D006_Id'];
                $Flag_Destino_Produto = $T008['T008_Flag_Destino_Produto'];

            }else if($T006_Id){

                $sqlT006 = mysql_query("SELECT T006_Flag_Origem_Produto,T006_D006_Id,T006_Flag_Destino_Produto FROM T006 WHERE T006_Id = '{$T006_Id}'");
                $T006    = mysql_fetch_array($sqlT006);
                $cErro   = mysql_error();
                if (!empty($cErro)) {
                    return "{$cErro}<br />{$cClausula}";
                }
    
                $Flag_Origem_Produto  = $T006['T006_Flag_Origem_Produto'];
                $D006_Id              = $T006['T006_D006_Id'];
                $Flag_Destino_Produto = $T006['T006_Flag_Destino_Produto'];
            }
            
        } else {
            // Nenhum dado recebido
        }
        if($CFOP > 0) {
            $D006_Id = $CFOP;
        }

        if(empty($D024_Id) && !empty($D024_Id_Enviado)){
            $D024_Id = $D024_Id_Enviado;
            $sqlD009 = mysql_query("SELECT D009_C004_Id FROM D009 WHERE D009_Id = '{$D009_Id}'");
            $mD009 = mysql_fetch_array($sqlD009);
            $C004_Id = $mD009['D009_C004_Id'];

            $cClausula = " SELECT ";
            $cClausula .= " D024_D018_Id, ";
            $cClausula .= " D018_UF, ";
            $cClausula .= " D053_ST_ICMS_Inter_Estadual, ";
            $cClausula .= " D053_ST_ICMS_Interno, ";
            $cClausula .= " C004_D018_Id, ";
            $cClausula .= " D053_Percentual_FCP, ";
            $cClausula .= " C004_Percentual_PIS, ";
            $cClausula .= " C004_Percentual_COFINS, ";
            $cClausula .= " C004_Percentual_IRPJ, ";
            $cClausula .= " C004_Percentual_CSLL, ";
            $cClausula .= " C004_IPV_Padrao_Sem_Lucro, ";
            $cClausula .= " D053_Percentual_FCP, ";
            $cClausula .= " D024_Flag_Revenda_Consumidor, ";
            $cClausula .= " D024_Codigo_Regime_Tributario, ";
            $cClausula .= " D024_Flag_Contribuinte, ";
            $cClausula .= " ifnull(D006_Codigo_Beneficio_Fiscal,'') as D006_Codigo_Beneficio_Fiscal, ";
            $cClausula .= " D006_Valor_Codigo_Beneficio_Fiscal, ";
            $cClausula .= " ifnull(D024_Codigo_Beneficio_Fiscal,'') as D024_Codigo_Beneficio_Fiscal, ";
            $cClausula .= " D024_Inscricao_Estadual, ";   
            $cClausula .= " D024_Flag_Pessoa_Fisica_Juridica, ";   
            $cClausula .= " D053_Aliquota_ICMS_1, ";
            $cClausula .= " D053_Aliquota_ICMS_2, ";
            $cClausula .= " D053_Aliquota_ICMS_Importado, ";
            $cClausula .= " D053_Aliquota_ICMS_Importado_2, ";     
            $cClausula .= " D053_Flag_Ajusta_MVA ";
            $cClausula .= " FROM D024 ";
            $cClausula .= " LEFT JOIN D018 ON D018_Id=D024_D018_Id ";
            $cClausula .= " LEFT JOIN D053 ON D053_D018_Id=D024_D018_Id AND D053_C004_Id=$C004_Id ";
            $cClausula .= " LEFT JOIN C004 ON C004_Id=$C004_Id ";
            $cClausula .= " LEFT JOIN D006 ON D006_Id=$D006_Id ";
            $cClausula .= " WHERE D024_Id='$D024_Id' ";
            $Cab   = mysql_query($cClausula);
            $cErro = mysql_error();
            
            if (!empty($cErro)) {
                return "{$cErro}<br />{$cClausula}";
            }
            $mCab = mysql_fetch_array($Cab);

            $D018_Id                  = $mCab['D024_D018_Id'];
            $Flag_Revenda_Consumidor  = $mCab['D024_Flag_Revenda_Consumidor'];
            $Codigo_Regime_Tributario = $mCab['D024_Codigo_Regime_Tributario'];
            $Flag_Contribuinte        = $mCab['D024_Flag_Contribuinte'];
            $Flag_Acp                 = 6;
        }
        
        $cClausula = " SELECT ";
        $cClausula .= " D006_D088_Id, ";
        $cClausula .= " D006_Codigo_Enq_Legal_IPI, ";
        $cClausula .= " D006_D059_Id, ";
        $cClausula .= " D006_D088_Id_Sem_Aliquota, ";
        $cClausula .= " D006_Flag_Ajustar_MVA, ";
        $cClausula .= " D006_Flag_Isento_COFINS, ";
        $cClausula .= " D006_Flag_Isento_CSLL, ";
        $cClausula .= " D006_Flag_Isento_ICMS, ";
        $cClausula .= " D006_Flag_Isento_IRPJ, ";
        $cClausula .= " D006_Flag_Isento_PIS, ";
        $cClausula .= " D006_Flag_ST_VA, ";
        $cClausula .= " D006_Flag_Substituicao_Tributaria, ";
        $cClausula .= " D006_Flag_Venda_Compra_Outros, ";
        $cClausula .= " D006_Motivo_Desoneracao_ICMS, ";
        $cClausula .= " D006_Percentual_Base_Operacao_Propria, ";
        $cClausula .= " D006_Percentual_Partilha_ICMS_Destino, ";
        $cClausula .= " D006_Percentual_Partilha_ICMS_Origem, ";
        $cClausula .= " D006_Reducao_MVA, ";
        $cClausula .= " D006_Situacao_Tributaria_COFINS, ";
        $cClausula .= " D006_Situacao_Tributaria_PIS, ";
        $cClausula .= " D006_Somar_FCP_ICMS_Interno, ";
        $cClausula .= " D006_ST_ICMS_Inter_Estadual, ";
        $cClausula .= " D006_ST_ICMS_Inter_Estadual_MVA, ";
        $cClausula .= " D006_ST_ICMS_Interno, ";
        $cClausula .= " D006_ST_ICMS_Interno_MVA, ";
        $cClausula .= " D006_ST_ICMS_Interno_Reducao, ";
        $cClausula .= " D006_ST_MVA, ";
        $cClausula .= " D006_UF_ICMS_Substituicao_Devido, ";
        $cClausula .= " D059_Codigo_ST, ";
        $cClausula .= " D059_Id, ";
        $cClausula .= " D088_Com_Aliquota.D088_Codigo_ST as D088_Codigo_ST, ";
        $cClausula .= " D088_Sem_Aliquota.D088_Codigo_ST as D088_Codigo_ST_Sem_Aliquota, ";
        $cClausula .= " D006_Modalidade_ICMS_Substituicao, ";
        $cClausula .= " D006_Codigo_Beneficio_Fiscal, ";
        $cClausula .= " D006_Valor_Codigo_Beneficio_Fiscal, ";
        $cClausula .= " D006_Flag_Calcula_FCP, ";
        $cClausula .= " D006_Modalidade_ICMS, ";
        $cClausula .= " D006_Flag_Calcula_FCP_ST, ";
        $cClausula .= " D006_Flag_Calcula_FCP_Retido, ";
        $cClausula .= " D006_Reducao_Aliquota_IPI, ";
        $cClausula .= " D006_Percentual_Reducao_PIS, ";
        $cClausula .= " D006_Percentual_Reducao_COFINS, ";
        $cClausula .= " D006_Percentual_Reducao_Base_DIFAL, ";
        $cClausula .= " D006_Percentual_FCP, ";
        $cClausula .= " D006_Flag_Isento_ICMS_Credito, ";
        $cClausula .= " D006_Aliquota_PIS, ";
        $cClausula .= " D006_Aliquota_COFINS, ";
        $cClausula .= " D006_Flag_Endereco_Aliquota_Icms_Interno_Difal, ";
        /* [REFORMA TRIBUTÁRIA] - Gabriel Cegantini - 27/11/2025
        ================================================================================ */
        $cClausula .= " D006_Situacao_Tributaria_CBS_IBS, ";
        $cClausula .= " D006_Classificacao_Tributaria_CBS_IBS, ";
        $cClausula .= " D006_Situacao_Tributaria_CBS_IBS_Regular, ";
        $cClausula .= " D006_Classificacao_Tributaria_CBS_IBS_Regular, ";
        /* ================================================================================ */
        /* Campos para NT 2019.001 - Criação e Atualização de Regras de Validação
        Versão 1.64 – Setembro de 2024, por solicitação da Smartness
        Gabriel Cegantini - 02/10/2025
        ================================================================================ */
        $cClausula .= " D006_Codigo_Beneficio_Credito_Presumido, ";
        $cClausula .= " D006_Percentual_Beneficio_Credito_Presumido, ";
        $cClausula .= " D006_Codigo_Beneficio_Reducao_Base_ICMS ";
        /* ================================================================================ */
        $cClausula .= " FROM D006 ";
        $cClausula .= " LEFT JOIN D059 ON D059_Id=D006_D059_Id ";
        $cClausula .= " LEFT JOIN D088 as D088_Com_Aliquota ON D088_Com_Aliquota.D088_Id=D006_D088_Id ";
        $cClausula .= " LEFT JOIN D088 as D088_Sem_Aliquota ON D088_Sem_Aliquota.D088_Id=D006_D088_Id_Sem_Aliquota ";
        $cClausula .= " WHERE D006_Id='{$D006_Id}'";
        log($cClausula);
        $D006 = mysql_query($cClausula);
        if (!empty($cErro)) {
            return "$cErro<br />$cClausula";
        }
        $mD006 = mysql_fetch_array($D006);

        if($mD006['D006_Codigo_Beneficio_Fiscal'] == 'S'){
            $Codigo_Beneficio_Fiscal  = $mCab['D024_Codigo_Beneficio_Fiscal'];
        }
        
        if(empty($Codigo_Beneficio_Fiscal) && !empty($mD006['D006_Valor_Codigo_Beneficio_Fiscal'])){
            $Codigo_Beneficio_Fiscal  = $mD006['D006_Valor_Codigo_Beneficio_Fiscal'];
        }

        $reducaoAliquotaIPI = $mD006['D006_Reducao_Aliquota_IPI'];
        
        $cClausula = " SELECT ";
        $cClausula .= " D001_Flag_Pre_Cadastro, ";
        $cClausula .= " D001_Peso_Unitario_Bruto, ";
        $cClausula .= " D009_Origem_Mercadoria, ";
        $cClausula .= " D018_UF, ";
        $cClausula .= " D049_D082_Id, ";
        $cClausula .= " D149_Aliquota_COFINS, ";
        $cClausula .= " D149_Aliquota_CSLL, ";
        $cClausula .= " D149_Aliquota_IRPJ, ";
        $cClausula .= " D149_Aliquota_PIS, ";
        $cClausula .= " D149_Codigo_CEST, ";
        $cClausula .= " D149_Flag_ST, ";
        $cClausula .= " D149_Id, ";
        $cClausula .= " D149_Regras_MVA, ";
        $cClausula .= " D049_D001_Id, ";
        $cClausula .= " D009_D049_Id, ";
        $cClausula .= " D024_D018_Id, ";
        $cClausula .= " D001_D005_Id, ";
        $cClausula .= " D149_D005_Id, ";
        $cClausula .= " D149_D088_Id, ";
        $cClausula .= " D009_Id, ";
        $cClausula .= " D001_Id, ";
        $cClausula .= " D049_Id, ";
        $cClausula .= " D001_Codigo_Produto, ";
        $cClausula .= " D001_Descricao_Produto, ";
        $cClausula .= " D001_D037_Id, ";
        $cClausula .= " D009_Valor_Custo_Unitario, ";
        $cClausula .= " D009_Flag_Preco_Tabelado, ";
        $cClausula .= " D041_Valor_Preco_Consumidor, ";
        $cClausula .= " D041_Valor_Base, ";
        $cClausula .= " D041_Valor_Preco_Revenda, ";
        $cClausula .= " D024_Horario_Entrega, ";
        $cClausula .= " D001_Peso_Unitario_Kg, ";
        $cClausula .= " D001_Peso_Total_Kg, ";
        $cClausula .= " D001_Qte_Embalagem, ";
        $cClausula .= " D037_Unidade, ";
        $cClausula .= " D037_Id, ";
        $cClausula .= " D001_Flag_Tipo, ";
        $cClausula .= " D049_Origem_Mercadoria, ";
        $cClausula .= " IFNULL(D009_Origem_Mercadoria, '') as D009_Origem_Mercadoria, ";
        $cClausula .= " D009_IPI_3('$C004_Id',D009_Id,'$D024_Id','$D006_Id','1','$Flag_Acp') as Aliquota_IPI, ";
        $cClausula .= " D009_IPI_3('$C004_Id',D009_Id,'$D024_Id','$D006_Id','2','$Flag_Acp') as Percentual_Reducao_IPI, ";
        $cClausula .= " D009_IPV_ICMS('$C004_Id','$D024_Id',D009_Id,'$D006_Id','$D018_Id','$Flag_Revenda_Consumidor','$Percentual_Comissao','$Flag_Acp',2) as Aliquota_ICMS, ";
        $cClausula .= " D009_IPV_ICMS('$C004_Id','$D024_Id',D009_Id,'$D006_Id','$D018_Id','$Flag_Revenda_Consumidor','$Percentual_Comissao','$Flag_Acp',3) as Percentual_Reducao_ICMS, ";
        $cClausula .= " D005_Id, ";
        $cClausula .= " D049_Flag_Substituicao_Tributaria, ";
        $cClausula .= " D049_Flag_Nacional_Importado, ";
        $cClausula .= " D005_Flag_ST, ";
        $cClausula .= " D005_Classificacao_Fiscal, ";
        $cClausula .= " D088_Codigo_ST, ";
        $cClausula .= " D088_Id, ";
        $cClausula .= " D005_Aliquota_PIS, ";
        $cClausula .= " D005_Aliquota_COFINS, ";
        $cClausula .= " D005_Aliquota_COFINS, ";
        /* [REFORMA TRIBUTÁRIA] - Gabriel Cegantini 24/11/2025
        ================================================================================ */
        $cClausula .= " D005_Situacao_Tributaria_CBS_IBS, ";
        $cClausula .= " D005_Classificacao_Tributaria_CBS_IBS, ";
        $cClausula .= " D005_Situacao_Tributaria_CBS_IBS_Regular, ";
        $cClausula .= " D005_Classificacao_Tributaria_CBS_IBS_Regular, ";
        /* ================================================================================ */
        $cClausula .= " D149_Valor_VA, ";
        $cClausula .= " D009_Valor_Custo_ST_Unitario, ";
        $cClausula .= " D009_Valor_Custo_ST_Medio_Unitario, ";
        $cClausula .= " D149_Indicador_Escala, ";
        $cClausula .= " D149_Flag_Isento_PIS, ";
        $cClausula .= " D149_Situacao_Tributaria_PIS, ";
        $cClausula .= " D149_Flag_Isento_COFINS, ";
        $cClausula .= " D149_Situacao_Tributaria_COFINS ";
        $cClausula .= " FROM D001 as D1";
        $cClausula .= " LEFT JOIN D049 ON D001_Id=D049_D001_Id ";
        $cClausula .= " LEFT JOIN D009 ON D049_Id=D009_D049_Id ";
        $cClausula .= " LEFT JOIN D041 ON D041_D049_Id=D049_Id ";
        $cClausula .= " LEFT JOIN D037 ON D037_Id=D001_D037_Id ";
        $cClausula .= " LEFT JOIN D024 ON D024_Id={$D024_Id} ";
        $cClausula .= " LEFT JOIN D018 ON D018_Id=D024_D018_Id ";
        $cClausula .= " LEFT JOIN D005 ON D005_Id=D001_D005_Id OR D005_Id = (SELECT T025_D005_Id FROM D001 LEFT JOIN T025 ON T025_D015_Id=D001_D015_Id AND T025_D002_Id=D001_D002_Id WHERE D001_Id=D1.D001_Id LIMIT 1)";
        $cClausula .= " LEFT JOIN D149 ON D005_Id=D149_D005_Id AND D149_C004_Id={$C004_Id} ";
        $cClausula .= " LEFT JOIN D088 ON D088_Id=D149_D088_Id ";
        $cClausula .= " WHERE D009_Id='$D009_Id' GROUP BY D009_Id";
        $Produto  = mysql_query($cClausula);
        $mProduto = mysql_fetch_array($Produto);
        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();
        
        if($mProduto['D009_Origem_Mercadoria'] != ''){
            $mProduto['D049_Origem_Mercadoria'] = $mProduto['D009_Origem_Mercadoria'];
        }

        if (!empty($Flag_Origem_Produto) || $Flag_Origem_Produto == 0) {
            $mProduto['D049_Origem_Mercadoria'] = $Flag_Origem_Produto;
        }

        //Verifica as exceções de valor de MVA da NCM
        /*
        if (!empty($mProduto['D149_Regras_MVA'])) {
            preg_match_all("/(MVA|MVAA) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA'], $D149_Regras_MVA);
            $valoresRegras = $D149_Regras_MVA[3];
            if (in_array($mProduto['D018_UF'], $D149_Regras_MVA[2])) {
                $regra                     = array_search($mProduto['D018_UF'], $D149_Regras_MVA[2]);
                $mProduto['D149_Valor_VA'] = str_replace(",", ".", $valoresRegras[$regra]);
            }
        }*/

        // Se calcula DIFAL e a regra da CFOP é para usar aliquota ICMS INT da UF do endereço de entrega, verifica se o endereço padrão e entrega tem a mesma UF
        if($enderecoEntrega > 0 && $mD006['D006_Flag_Endereco_Aliquota_Icms_Interno_Difal'] == 'E' && $mD006['D006_Percentual_Partilha_ICMS_Destino'] > 0){
            $D148 = mysql_query("SELECT D018_UF,
                                        D053_ST_ICMS_Interno
                                   FROM D148
                              LEFT JOIN D018 ON D018_Id = D148_D018_Id
                              LEFT JOIN D053 ON D053_D018_Id=D148_D018_Id AND D053_C004_Id='{$C004_Id}'
                                  WHERE D148_Id = '{$enderecoEntrega}'");
            $mD148 = mysql_fetch_array($D148);
            
            if($mCab['D018_UF'] != $mD148['D018_UF']){
                $mCab['D053_ST_ICMS_Interno'] = $mD148['D053_ST_ICMS_Interno'];
            }
        }

        $Aliquota_ICMS           = $CAD002->retornaICMS($C004_Id, $D024_Id, $mProduto['D009_Id'], $D006_Id, $D018_Id, $Flag_Revenda_Consumidor, $Percentual_Comissao, $Flag_Acp, '2', $Flag_Destino_Produto, $mProduto['D049_Origem_Mercadoria']);
        $Percentual_Reducao_ICMS = $CAD002->retornaICMS($C004_Id, $D024_Id, $mProduto['D009_Id'], $D006_Id, $D018_Id, $Flag_Revenda_Consumidor, $Percentual_Comissao, $Flag_Acp, '3', $Flag_Destino_Produto, $mProduto['D049_Origem_Mercadoria']);


        // Quando a CFOP é isenta de ICMS, o sistema vai gerar o valor do ICMS nos campos com extenção "Oculto", para que estes valores possam ser utilizados na observação da nota
        // Os parametros finais = 22 ou 33 é para retornar as aliquotas mesmo que a CFOP seja isenta
        // Marcelo de Paula - 18-09-2014
        $Aliquota_ICMS_Oculto           = $CAD002->retornaICMS($C004_Id, $D024_Id, $mProduto['D009_Id'], $D006_Id, $D018_Id, $Flag_Revenda_Consumidor, $Percentual_Comissao, $Flag_Acp, '22', $Flag_Destino_Produto, $mProduto['D049_Origem_Mercadoria']);
        $Percentual_Reducao_ICMS_Oculto = $CAD002->retornaICMS($C004_Id, $D024_Id, $mProduto['D009_Id'], $D006_Id, $D018_Id, $Flag_Revenda_Consumidor, $Percentual_Comissao, $Flag_Acp, '33', $Flag_Destino_Produto, $mProduto['D049_Origem_Mercadoria']);

        $mProduto['Aliquota_ICMS']           = $Aliquota_ICMS;
        $mProduto['Percentual_Reducao_ICMS'] = $Percentual_Reducao_ICMS;

        // Só entra aqui se na tabela ICMS (D053) o D053_ST_ICMS_Inter_Estadual estiver 0. 
        // Esta validação do ICMS é a mesma do programa CAD002->retornaICMS, se for alterada la precisa alterar aqui também (10/11/2021)
        if($mCab['D053_ST_ICMS_Inter_Estadual'] <= 0){

            if((($mProduto['D049_Origem_Mercadoria'] == '1' || $mProduto['D049_Origem_Mercadoria'] == '2' || $mProduto['D049_Origem_Mercadoria'] == '3' || $mProduto['D049_Origem_Mercadoria'] == '8') AND $mCab['D024_D018_Id'] != $mCab['C004_D018_Id'] AND $mD006['D006_Flag_Isento_ICMS'] != 'S') AND ($mCab['D024_Inscricao_Estadual'] == '' OR $mCab['D024_Inscricao_Estadual'] == 'ISENTO' OR $mCab['D024_Flag_Pessoa_Fisica_Juridica'] == 'F')) {
                $mCab['D053_ST_ICMS_Inter_Estadual'] = $mCab['D053_Aliquota_ICMS_Importado'];
            } else if((($mProduto['D049_Origem_Mercadoria'] == '1' || $mProduto['D049_Origem_Mercadoria'] == '2' || $mProduto['D049_Origem_Mercadoria'] == '3' || $mProduto['D049_Origem_Mercadoria'] == '8') AND $mCab['D024_D018_Id'] != $mCab['C004_D018_Id'] AND $mD006['D006_Flag_Isento_ICMS'] != 'S') AND (($mCab['D024_Inscricao_Estadual'] != '' AND $mCab['D024_Inscricao_Estadual'] != 'ISENTO') OR $mCab['D024_Flag_Pessoa_Fisica_Juridica'] == 'J')) {
                $mCab['D053_ST_ICMS_Inter_Estadual'] = $mCab['D053_Aliquota_ICMS_Importado_2'];
            } else if ($mCab['D024_Inscricao_Estadual'] == '' OR $mCab['D024_Inscricao_Estadual'] == 'ISENTO' OR $mCab['D024_Flag_Pessoa_Fisica_Juridica'] == 'F') {
                $mCab['D053_ST_ICMS_Inter_Estadual'] = $mCab['D053_Aliquota_ICMS_1'];
            } else if (($mCab['D024_Inscricao_Estadual'] != '' AND $mCab['D024_Inscricao_Estadual'] != 'ISENTO') OR $mCab['D024_Flag_Pessoa_Fisica_Juridica'] == 'J') {
                $mCab['D053_ST_ICMS_Inter_Estadual'] = $mCab['D053_Aliquota_ICMS_2'];
            } 
        }

	    //Verifica as exceções de valor de MVA e ICMS da NCM
	    if (!empty($mProduto['D149_Regras_MVA'])) {
            //1 Regra - MVA ajustado por UF
            preg_match_all("/(MVA|MVAA) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA'], $D149_Regras_MVA);
            $valoresRegras = $D149_Regras_MVA[3];
            if (in_array($mCab['D018_UF'], $D149_Regras_MVA[2])) {
                $regra = array_search($mProduto['D018_UF'], $D149_Regras_MVA[2]);
                if (!empty($valoresRegras[$regra])) {
                    $mProduto['D149_Valor_VA'] = str_replace(",", ".", $valoresRegras[$regra]);
                }
            } else if (in_array("XX", $D149_Regras_MVA[2])) {
                $regra = array_search("XX", $D149_Regras_MVA[2]);
                if (!empty($valoresRegras[$regra])) {
                    $mProduto['D149_Valor_VA'] = str_replace(",", ".", $valoresRegras[$regra]);
                }
            }
            preg_match_all("/(MVACRT".$Codigo_Regime_Tributario."|MVAA) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA'], $D149_Regras_MVA);
            $valoresRegras = $D149_Regras_MVA[3];
            if (in_array($mCab['D018_UF'], $D149_Regras_MVA[2])) {
                $regra = array_search($mProduto['D018_UF'], $D149_Regras_MVA[2]);
                if (!empty($valoresRegras[$regra])) {
                    $mProduto['D149_Valor_VA'] = str_replace(",", ".", $valoresRegras[$regra]);
                }
            } else if (in_array("XX", $D149_Regras_MVA[2])) {
                $regra = array_search("XX", $D149_Regras_MVA[2]);
                if (!empty($valoresRegras[$regra])) {
                    $mProduto['D149_Valor_VA'] = str_replace(",", ".", $valoresRegras[$regra]);
                }
            }
	        if ($mProduto['Aliquota_ICMS']>0) {
		        log("/(ICM|ICMS) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims {$mProduto['D149_Regras_MVA']}, {$D149_Regras_ICM}");
		        preg_match_all("/(ICM|ICMS) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA'], $D149_Regras_ICM);
		        $valoresRegras = $D149_Regras_ICM[3];
		        if (in_array($mCab['D018_UF'], $D149_Regras_ICM[2])) {
		            $regra = array_search($mProduto['D018_UF'], $D149_Regras_ICM[2]);
		            if (!empty($valoresRegras[$regra])) {
		                $mProduto['Aliquota_ICMS'] = str_replace(",", ".", $valoresRegras[$regra]);
		            }
		        } else if (in_array('XX', $D149_Regras_ICM[2])) {
		            $regra = array_search('XX', $D149_Regras_ICM[2]);
		            if (!empty($valoresRegras[$regra])) {
		                $mProduto['Aliquota_ICMS'] = str_replace(",", ".", $valoresRegras[$regra]);
		            }
		        }
		    }
	        
	        preg_match_all("/(ICMIE|ICMSIE) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA'], $D149_Regras_ICM);
	        $valoresRegras = $D149_Regras_ICM[3];
	        if (in_array($mCab['D018_UF'], $D149_Regras_ICM[2])) {
	            $regra = array_search($mProduto['D018_UF'], $D149_Regras_ICM[2]);
	            if (!empty($valoresRegras[$regra])) {
	                $mCab['D053_ST_ICMS_Inter_Estadual'] = str_replace(",", ".", $valoresRegras[$regra]);
	            }
	        } else if (in_array('XX', $D149_Regras_ICM[2])) {
	            $regra = array_search('XX', $D149_Regras_ICM[2]);
	            if (!empty($valoresRegras[$regra])) {
	                $mCab['D053_ST_ICMS_Inter_Estadual'] = str_replace(",", ".", $valoresRegras[$regra]);
	            }
	        }
	        
	        preg_match_all("/(ICMINT|ICMSINT) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA'], $D149_Regras_ICM);
	        $valoresRegras = $D149_Regras_ICM[3];
	        if (in_array($mCab['D018_UF'], $D149_Regras_ICM[2])) {
	            $regra = array_search($mProduto['D018_UF'], $D149_Regras_ICM[2]);
	            if (!empty($valoresRegras[$regra])) {
	                $mCab['D053_ST_ICMS_Interno'] = str_replace(",", ".", $valoresRegras[$regra]);
	            }
	        } else if (in_array('XX', $D149_Regras_ICM[2])) {
	            $regra = array_search('XX', $D149_Regras_ICM[2]);
	            if (!empty($valoresRegras[$regra])) {
	                $mCab['D053_ST_ICMS_Interno'] = str_replace(",", ".", $valoresRegras[$regra]);
	            }
	        }

            //****************************
            // ICMS por tipo contribuinte
            //****************************
            if ($mProduto['Aliquota_ICMS']>0) {
                preg_match_all("/(ICM".$Flag_Contribuinte."|ICMS".$Flag_Contribuinte.") +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA'], $D149_Regras_ICM);
                $valoresRegras = $D149_Regras_ICM[3];
                if (in_array($mCab['D018_UF'], $D149_Regras_ICM[2])) {
                    $regra = array_search($mProduto['D018_UF'], $D149_Regras_ICM[2]);
                    if (!empty($valoresRegras[$regra])) {
                        $mProduto['Aliquota_ICMS'] = str_replace(",", ".", $valoresRegras[$regra]);
                    }
                } else if (in_array('XX', $D149_Regras_ICM[2])) {
                    $regra = array_search('XX', $D149_Regras_ICM[2]);
                    if (!empty($valoresRegras[$regra])) {
                        $mProduto['Aliquota_ICMS'] = str_replace(",", ".", $valoresRegras[$regra]);
                    }
                }
            }
            preg_match_all("/(ICMIE".$Flag_Contribuinte."|ICMSIE".$Flag_Contribuinte.") +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA'], $D149_Regras_ICM);
            $valoresRegras = $D149_Regras_ICM[3];
            if (in_array($mCab['D018_UF'], $D149_Regras_ICM[2])) {
                $regra = array_search($mProduto['D018_UF'], $D149_Regras_ICM[2]);
                if (!empty($valoresRegras[$regra])) {
                    $mCab['D053_ST_ICMS_Inter_Estadual'] = str_replace(",", ".", $valoresRegras[$regra]);
                }
            } else if (in_array('XX', $D149_Regras_ICM[2])) {
                $regra = array_search('XX', $D149_Regras_ICM[2]);
                if (!empty($valoresRegras[$regra])) {
                    $mCab['D053_ST_ICMS_Inter_Estadual'] = str_replace(",", ".", $valoresRegras[$regra]);
                }
            }
            preg_match_all("/(ICMINT".$Flag_Contribuinte."|ICMSINT".$Flag_Contribuinte.") +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA'], $D149_Regras_ICM);
            $valoresRegras = $D149_Regras_ICM[3];
            if (in_array($mCab['D018_UF'], $D149_Regras_ICM[2])) {
                $regra = array_search($mProduto['D018_UF'], $D149_Regras_ICM[2]);
                if (!empty($valoresRegras[$regra])) {
                    $mCab['D053_ST_ICMS_Interno'] = str_replace(",", ".", $valoresRegras[$regra]);
                }
            } else if (in_array('XX', $D149_Regras_ICM[2])) {
                $regra = array_search('XX', $D149_Regras_ICM[2]);
                if (!empty($valoresRegras[$regra])) {
                    $mCab['D053_ST_ICMS_Interno'] = str_replace(",", ".", $valoresRegras[$regra]);
                }
            }

            if ($mProduto['Aliquota_IPI'] > 0) {
		        preg_match_all("/(REDIPIALI|REDIPIALI) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA'], $D149_Regras_IPI);
		        $valoresRegras = $D149_Regras_IPI[3];
		        if (in_array($mCab['D018_UF'], $D149_Regras_IPI[2])) {
		            $regra = array_search($mProduto['D018_UF'], $D149_Regras_IPI[2]);
		            if (!empty($valoresRegras[$regra])) {
		                $reducaoAliquotaIPI = str_replace(",", ".", $valoresRegras[$regra]);
		            }
		        } else if (in_array('XX', $D149_Regras_IPI[2])) {
		            $regra = array_search('XX', $D149_Regras_IPI[2]);
		            if (!empty($valoresRegras[$regra])) {
		                $reducaoAliquotaIPI = str_replace(",", ".", $valoresRegras[$regra]);
		            }
		        }

                preg_match_all("/(IPI|IPI) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA'], $D149_Regras_IPI);
		        $valoresRegras = $D149_Regras_IPI[3];
		        if (in_array($mCab['D018_UF'], $D149_Regras_IPI[2])) {
		            $regra = array_search($mProduto['D018_UF'], $D149_Regras_IPI[2]);
		            if (!empty($valoresRegras[$regra])) {
		                $mProduto['Aliquota_IPI'] = str_replace(",", ".", $valoresRegras[$regra]);
		            }
		        } else if (in_array('XX', $D149_Regras_IPI[2])) {
		            $regra = array_search('XX', $D149_Regras_IPI[2]);
		            if (!empty($valoresRegras[$regra])) {
		                $mProduto['Aliquota_IPI'] = str_replace(",", ".", $valoresRegras[$regra]);
		            }
		        }
		    }
	    }

        if (($mProduto['D049_Origem_Mercadoria'] == '1' || $mProduto['D049_Origem_Mercadoria'] == '2' || $mProduto['D049_Origem_Mercadoria'] == '3') && $mCab['D024_D018_Id'] != $mCab['C004_D018_Id'] && $mD006['D006_Flag_Isento_ICMS'] != 'S') {
            if (!empty($mProduto['D149_Regras_MVA'])) {    
                if ($mProduto['Aliquota_ICMS']>0) { 
                    preg_match_all("/(ICMIMP|ICMSIMP) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA'], $D149_Regras_ICM);
                    $valoresRegras = $D149_Regras_ICM[3];
                    if (in_array($mCab['D018_UF'], $D149_Regras_ICM[2])) {
                        $regra = array_search($mProduto['D018_UF'], $D149_Regras_ICM[2]);
                        if (!empty($valoresRegras[$regra])) {
                            $mProduto['Aliquota_ICMS'] = str_replace(",", ".", $valoresRegras[$regra]);
                            $mCab['D053_ST_ICMS_Inter_Estadual'] = $mProduto['Aliquota_ICMS'] ;
                        }
                    } else if (in_array('XX', $D149_Regras_ICM[2])) {
                        $regra = array_search('XX', $D149_Regras_ICM[2]);
                        if (!empty($valoresRegras[$regra])) {
                            $mProduto['Aliquota_ICMS'] = str_replace(",", ".", $valoresRegras[$regra]);
                            $mCab['D053_ST_ICMS_Inter_Estadual'] = $mProduto['Aliquota_ICMS'] ;
                        }
                    }
                    $Aliquota_ICMS_Oculto = $mProduto['Aliquota_ICMS'] ;
                }
                preg_match_all("/(MVAIMP|MVAAIMP) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA'], $D149_Regras_MVA);
                $valoresRegras = $D149_Regras_MVA[3];
                if (in_array($mCab['D018_UF'], $D149_Regras_MVA[2])) {
                    $regra = array_search($mProduto['D018_UF'], $D149_Regras_MVA[2]);
                    if (!empty($valoresRegras[$regra])) {
                        $mProduto['D149_Valor_VA'] = str_replace(",", ".", $valoresRegras[$regra]);
                    }
                } else if (in_array("XX", $D149_Regras_MVA[2])) {
                    $regra = array_search("XX", $D149_Regras_MVA[2]);
                    if (!empty($valoresRegras[$regra])) {
                        $mProduto['D149_Valor_VA'] = str_replace(",", ".", $valoresRegras[$regra]);
                    }
                }
            }
        }
        
        if($mD006['D006_Flag_Calcula_FCP'] == 'S' || $mD006['D006_Flag_Calcula_FCP_ST'] == 'S' || $mD006['D006_Flag_Calcula_FCP_Retido'] == 'S'){
            
            //$mCab['D053_Percentual_FCP'] = $mCab['D053_Percentual_FCP'];
            if($mD006['D006_Percentual_FCP'] > 0){
                $mCab['D053_Percentual_FCP'] = $mD006['D006_Percentual_FCP'];
            }
        }else{
            $mCab['D053_Percentual_FCP'] = 0;
        }
        
                
        if ($mD006['D006_ST_ICMS_Inter_Estadual'] > 0)
            $mCab['D053_ST_ICMS_Inter_Estadual'] = $mD006['D006_ST_ICMS_Inter_Estadual'];
        if ($mD006['D006_ST_ICMS_Interno'] > 0)
            $mCab['D053_ST_ICMS_Interno'] = $mD006['D006_ST_ICMS_Interno'];

        if($mD006['D006_Somar_FCP_ICMS_Interno'] == 'S'){
            $mCab['D053_ST_ICMS_Interno'] = $mCab['D053_ST_ICMS_Interno'] + $mCab['D053_Percentual_FCP'];
        }

        $D053_ST_ICMS_Inter_Estadual = $mCab['D053_ST_ICMS_Inter_Estadual'];
        $D053_ST_ICMS_Interno        = $mCab['D053_ST_ICMS_Interno'];

        if ($mD006['D006_ST_ICMS_Inter_Estadual_MVA'] > 0)
            $mCab['D053_ST_ICMS_Inter_Estadual'] = $mD006['D006_ST_ICMS_Inter_Estadual_MVA'];
        if ($mD006['D006_ST_ICMS_Interno_MVA'] > 0)
            $mCab['D053_ST_ICMS_Interno'] = $mD006['D006_ST_ICMS_Interno_MVA'];
        
        if ($mD006['D006_ST_MVA'] > 0)
            $mProduto['D149_Valor_VA'] = $mD006['D006_ST_MVA'];
        
        if ($mProduto['D149_Valor_VA'] > 0 and $mD006['D006_Flag_Ajustar_MVA']!='N' and $mCab['D053_Flag_Ajusta_MVA'] !='N') {
            $Variavel_A                = 1 + ($mProduto['D149_Valor_VA'] / 100);
            $Variavel_B                = (1 - ($mCab['D053_ST_ICMS_Inter_Estadual'] / 100)) / (1 - ($mCab['D053_ST_ICMS_Interno'] / 100));
            $Variavel_AB               = (($Variavel_A * $Variavel_B) - 1) * 100;
            $mProduto['D149_Valor_VA'] = $Variavel_AB;
        }

        $mCab['D053_ST_ICMS_Inter_Estadual'] = $D053_ST_ICMS_Inter_Estadual;
        $mCab['D053_ST_ICMS_Interno']        = $D053_ST_ICMS_Interno;
        
        if ($mD006['D006_Flag_ST_VA'] == "N") {
            $mProduto['D149_Valor_VA'] = 0;
        }
        if ((($Flag_Acp != '1') AND ($Flag_Acp != '6')) OR ($mD006['D006_Flag_Substituicao_Tributaria'] != 'S')) {
            $mProduto['D149_Flag_ST']  = "N";
            $mProduto['D149_Valor_VA'] = 0;
        }
        
        if ($mD006['D006_Flag_Substituicao_Tributaria'] == 'S' && $mProduto['D149_Flag_ST'] == 'S') {
            $Flag_ST = 'S';
        } else {
            $Flag_ST                               = 'N';
            //$mCab['D053_ST_ICMS_Interno']          = 0;
            //$mCab['D053_ST_ICMS_Inter_Estadual']   = 0;
            //$mD006['D006_ST_ICMS_Interno_Reducao'] = 0;
            //$mProduto['D149_Valor_VA']             = 0;
        }

        //Aplica a redução de MVA
        if ($mD006['D006_Reducao_MVA'] > 0) {
            //$mProduto['D149_Valor_VA'] = $mProduto['D149_Valor_VA'] - (( ($mD006['D006_Reducao_MVA'] / 100) * $mProduto['D149_Valor_VA']) / 100);
            $mProduto['D149_Valor_VA'] = $mProduto['D149_Valor_VA'] * ((100 - $mD006['D006_Reducao_MVA']) / 100);
        }
        
        $Flag_Situacao_Tributaria = '0' . $mD006['D059_Codigo_ST'];
        
        
        // Verificando a Situação tributária
        if ((!empty($mProduto['D049_Origem_Mercadoria']) OR $mProduto['D049_Origem_Mercadoria'] == '0') AND $mProduto['D049_Origem_Mercadoria'] != '') {
            $Flag_Situacao_Tributaria = $mProduto['D049_Origem_Mercadoria'] . $mD006['D059_Codigo_ST'];
            
        } else if (!empty($mProduto['D049_Flag_Nacional_Importado'])) {
            if ($mProduto['D049_Flag_Nacional_Importado'] == "N") {
                $Flag_Situacao_Tributaria = '0' . $mD006['D059_Codigo_ST'];
            } else {
                $Flag_Situacao_Tributaria = '1' . $mD006['D059_Codigo_ST'];
            }
        } else if (!empty($mProduto['D001_Flag_Tipo'])) {
            if ($mProduto['D001_Flag_Tipo'] == "PRODUZIDO") {
                $Flag_Situacao_Tributaria = '1' . $mD006['D059_Codigo_ST'];
            } else {
                $Flag_Situacao_Tributaria = '0' . $mD006['D059_Codigo_ST'];
            }
        }

        if($mD006['D006_Flag_Isento_ICMS'] == 'D' && ($mD006['D006_D059_Id'] == '5' || $mD006['D006_D059_Id'] == '6' || $mD006['D006_D059_Id'] == '7')){
            $mProduto['Aliquota_ICMS'] = 0;
        }

        if($mD006['D006_Flag_Isento_ICMS_Credito'] == 'S' && $Aliquota_ICMS_Credito > 0){
            $Aliquota_ICMS_Credito = 0;
        }


        // Aliquota PIS e COFINS
        // Verifica CFOP e NCM, sendo NCM preferencial se estiver preenchida
        if ($mD006['D006_Aliquota_PIS'] > 0) {
            $mCab['C004_Percentual_PIS'] = $mD006['D006_Aliquota_PIS'];
        }
       
        if ($mD006['D006_Aliquota_COFINS'] > 0) {
            $mCab['C004_Percentual_COFINS'] = $mD006['D006_Aliquota_COFINS'];
        }
        
        if ($mProduto['D149_Aliquota_PIS'] > 0) {
            $mCab['C004_Percentual_PIS'] = $mProduto['D149_Aliquota_PIS'];
        }
       
        if ($mProduto['D149_Aliquota_COFINS'] > 0) {
            $mCab['C004_Percentual_COFINS'] = $mProduto['D149_Aliquota_COFINS'];
        }
        // Fim Aliquota PIS e COFINS

        if ($mProduto['D149_Aliquota_IRPJ'] > 0) {
            $mCab['C004_Percentual_IRPJ'] = $mProduto['D149_Aliquota_IRPJ'];
        }
        
        if ($mProduto['D149_Aliquota_CSLL'] > 0) {
            $mCab['C004_Percentual_CSLL'] = $mProduto['D149_Aliquota_CSLL'];
        }
        
        // Buscado dados dos tributos da lei da transparência
        
        // Buscando o percentual de tributos (lei transparências)
        $D149a  = mysql_query("SELECT D149_Percentual_Trib_Transparencia FROM D149 WHERE D149_Id='{$mProduto['D149_Id']}'");
        $mD149a = mysql_fetch_array($D149a);
        
        $D006a  = mysql_query("SELECT D006_Percentual_Trib_Transparencia FROM D006 WHERE D006_Id='{$D006_Id}'");
        $mD006a = mysql_fetch_array($D006a);
        
        if (!empty($mD149a['D149_Percentual_Trib_Transparencia']) AND $mD149a['D149_Percentual_Trib_Transparencia'] > 0) {
            
            $Percentual_Trib_Transparencia = $mD149a['D149_Percentual_Trib_Transparencia'];
            
        } else if (!empty($mD006a['D006_Percentual_Trib_Transparencia']) AND $mD006a['D006_Percentual_Trib_Transparencia'] > 0) {
            
            $Percentual_Trib_Transparencia = $mD006a['D006_Percentual_Trib_Transparencia'];
            
        } else {
            
            $Percentual_Trib_Transparencia = 0;
        }

        if ($mD006['D006_Flag_Venda_Compra_Outros']=='V' and $mProduto['D088_Id']>0){
            $mD006['D088_Codigo_ST'] = $mProduto['D088_Codigo_ST'];
        } else if ($mProduto['Aliquota_IPI']<=0 and $mD006['D006_D088_Id_Sem_Aliquota']>0){
            $mD006['D088_Codigo_ST'] = $mD006['D088_Codigo_ST_Sem_Aliquota'];
        }

        // Verifica se tem reducao na aliquota do IPI
        if($mProduto['Aliquota_IPI'] > 0 && $reducaoAliquotaIPI > 0){
            $mProduto['Aliquota_IPI'] = $mProduto['Aliquota_IPI'] * ((100 - $reducaoAliquotaIPI) / 100);
        }

        // Buscando impostos alíquotas lei da transparência, tabela D165
        $D165=mysql_query("SELECT * FROM D165 WHERE D165_UF='{$mProduto['D018_UF']}' AND D165_Classificacao_Fiscal='{$mProduto['D005_Classificacao_Fiscal']}'");
        $mD165=mysql_fetch_array($D165);

        if($mProduto['D049_Origem_Mercadoria']=='0' || $mProduto['D049_Origem_Mercadoria']=='3' || $mProduto['D049_Origem_Mercadoria']=='4' || $mProduto['D049_Origem_Mercadoria']=='5' || $mProduto['D049_Origem_Mercadoria']=='8'){
            // Nacional
            $Percentual_Trib_Federal = $mD165['D165_Percentual_Trib_Federal_Nacional'];
        } else {
            // Importado
            $Percentual_Trib_Federal = $mD165['D165_Percentual_Trib_Federal_Importado'];
        }
        $Percentual_Trib_Estadual = $mD165['D165_Percentual_Trib_Estadual'];
        $Percentual_Trib_Municipal = $mD165['D165_Percentual_Trib_Municipal'];

        // Chamado 34796 - Quando a operação for venda e os parâetros de PIS e COFINS estiverem definidos na NCM, irá priorizar da NCM e não mais da CFOP
        if($mD006['D006_Flag_Venda_Compra_Outros'] == 'V'){
            
            if(!empty($mProduto['D149_Flag_Isento_PIS'])){
                $mD006['D006_Flag_Isento_PIS'] = $mProduto['D149_Flag_Isento_PIS'];
            }

            if(!empty($mProduto['D149_Situacao_Tributaria_PIS'])){
                $mD006['D006_Situacao_Tributaria_PIS'] = $mProduto['D149_Situacao_Tributaria_PIS'];
            }

            if(!empty($mProduto['D149_Flag_Isento_COFINS'])){
                $mD006['D006_Flag_Isento_COFINS'] = $mProduto['D149_Flag_Isento_COFINS'];
            }

            if(!empty($mProduto['D149_Situacao_Tributaria_COFINS'])){
                $mD006['D006_Situacao_Tributaria_COFINS'] = $mProduto['D149_Situacao_Tributaria_COFINS'];
            }
        }

        if($mD006['D006_Flag_Isento_PIS'] == 'S'){
            $mD006['D006_Percentual_Reducao_PIS'] = 0;
        }
        if($mD006['D006_Flag_Isento_COFINS'] == 'S'){
            $mD006['D006_Percentual_Reducao_COFINS'] = 0;
        }

        $mCab['notaFiscal'] = ($T007_Id === false ) ? 'N' : 'S';
        $this->excessoesAliquotas($mProduto,$mCab,$mD006);

        // $mD006['D006_UF_ICMS_Substituicao_Devido'];
        if ($mD006['D006_UF_ICMS_Substituicao_Devido'] == 'VD') {
            $UF_ICMS_Substituicao_Devido = $mCab['D018_UF'];
        } else {
            $UF_ICMS_Substituicao_Devido = $mD006['D006_UF_ICMS_Substituicao_Devido'];
        }

        // Definindo o array de retorno
        $array = array();

        $array['D005_Id']                                  = $mProduto['D005_Id'];
        $array['D149_Id']                                  = $mProduto['D149_Id'];
        $array['Classificacao_Fiscal']                     = $mProduto['D005_Classificacao_Fiscal'];
        $array['D006_Id']                                  = $D006_Id;
        $array['Aliquota_IPI']                             = $mProduto['Aliquota_IPI'];
        $array['Aliquota_ICMS']                            = $mProduto['Aliquota_ICMS'];
        $array['Aliquota_ICMS_Oculto']                     = $Aliquota_ICMS_Oculto;
        $array['Percentual_Reducao_ICMS']                  = $mProduto['Percentual_Reducao_ICMS'];
        $array['Percentual_Reducao_ICMS_Oculto']           = $Percentual_Reducao_ICMS_Oculto;
        $array['Percentual_Reducao_ICMS_ST']               = $mD006['D006_ST_ICMS_Interno_Reducao'];
        $array['ST_VA']                                    = $mProduto['D149_Valor_VA'];
        $array['Flag_ST']                                  = $Flag_ST;
        $array['Flag_Situacao_Tributaria']                 = $Flag_Situacao_Tributaria;
        $array['ICMS_Inter_Estadual']                      = $mCab['D053_ST_ICMS_Inter_Estadual'];
        $array['ST_ICMS_Interno']                          = $mCab['D053_ST_ICMS_Interno'];
        $array['ST_ICMS_Inter_Estadual_MVA']               = $mD006['D006_ST_ICMS_Inter_Estadual_MVA'];
        $array['ST_ICMS_Interno_MVA']                      = $mD006['D006_ST_ICMS_Interno_MVA'];
        $array['Flag_Isento_PIS']                          = $mD006['D006_Flag_Isento_PIS'];
        $array['Aliquota_PIS']                             = $mCab['C004_Percentual_PIS'];
        $array['Percentual_Reducao_PIS']                   = $mD006['D006_Percentual_Reducao_PIS'];
        $array['Situacao_Tributaria_PIS']                  = $mD006['D006_Situacao_Tributaria_PIS'];
        $array['Flag_Isento_COFINS']                       = $mD006['D006_Flag_Isento_COFINS'];
        $array['Aliquota_COFINS']                          = $mCab['C004_Percentual_COFINS'];
        $array['Percentual_Reducao_COFINS']                = $mD006['D006_Percentual_Reducao_COFINS'];
        $array['Percentual_Reducao_Base_DIFAL']            = $mD006['D006_Percentual_Reducao_Base_DIFAL'];
        $array['Situacao_Tributaria_COFINS']               = $mD006['D006_Situacao_Tributaria_COFINS'];
        $array['Situacao_Tributaria_IPI']                  = $mD006['D088_Codigo_ST'];
        $array['D059_Id']                                  = $mD006['D059_Id'];
        $array['D082_Id']                                  = $mProduto['D049_D082_Id'];
        $array['Valor_Custo_Unitario']                     = $mProduto['D009_Valor_Custo_Unitario'];
        $array['D001_Peso_Unitario_Kg']                    = $mProduto['D001_Peso_Unitario_Kg'];
        $array['D001_Peso_Unitario_Bruto']                 = $mProduto['D001_Peso_Unitario_Bruto'];
        $array['Percentual_Reducao_IPI']                   = $mProduto['Percentual_Reducao_IPI'];
        $array['Aliquota_ICMS_Credito']                    = $Aliquota_ICMS_Credito;
        $array['Percentual_Trib_Transparencia']            = $Percentual_Trib_Transparencia;
        $array['Flag_Pre_Cadastro']                        = $mProduto['D001_Flag_Pre_Cadastro'];
        $array['Valor_Custo_ST_Unitario']                  = $mProduto['D009_Valor_Custo_ST_Unitario'];
        $array['Valor_Custo_ST_Medio_Unitario']            = $mProduto['D009_Valor_Custo_ST_Medio_Unitario'];
        $array['Motivo_Desoneracao_ICMS']                  = $mD006['D006_Motivo_Desoneracao_ICMS'];
        $array['IPV_Sem_Lucro']                            = $mCab['C004_IPV_Padrao_Sem_Lucro'];
        $array['Aliquota_IRPJ']                            = $mCab['C004_Percentual_IRPJ'];
        $array['Flag_Isento_IRPJ']                         = $mD006['D006_Flag_Isento_IRPJ'];
        $array['Aliquota_CSLL']                            = $mCab['C004_Percentual_CSLL'];
        $array['Flag_Isento_CSLL']                         = $mD006['D006_Flag_Isento_CSLL'];
        
        $array['Percentual_Partilha_ICMS_Destino']         = $mD006['D006_Percentual_Partilha_ICMS_Destino'];
        $array['Percentual_Partilha_ICMS_Origem']          = $mD006['D006_Percentual_Partilha_ICMS_Origem'];
        $array['Codigo_CEST']                              = $mProduto['D149_Codigo_CEST'];
        $array['Percentual_FCP']                           = $mCab['D053_Percentual_FCP'];
        $array['Codigo_Enq_Legal_IPI']                     = $mD006['D006_Codigo_Enq_Legal_IPI'];
        $array['UF_ICMS_Substituicao_Devido']              = $UF_ICMS_Substituicao_Devido;
        $array['Percentual_Base_Operacao_Propria']         = $mD006['D006_Percentual_Base_Operacao_Propria'];
        $array['Percentual_Trib_Federal']                  = $Percentual_Trib_Federal;
        $array['Percentual_Trib_Estadual']                 = $Percentual_Trib_Estadual;
        $array['Percentual_Trib_Municipal']                = $Percentual_Trib_Municipal;
        $array['D149_Indicador_Escala']                    = $mProduto['D149_Indicador_Escala'];
        $array['D006_Codigo_Beneficio_Fiscal']             = $Codigo_Beneficio_Fiscal;
        $array['Modalidade_ICMS_Substituicao']             = $mD006['D006_Modalidade_ICMS_Substituicao'];
        $array['Modalidade_ICMS']                          = $mD006['D006_Modalidade_ICMS'];
        $array['EIP001_Trib_Interna_UF_Dest_Reducao_Base'] = $mProduto['EIP001_Trib_Interna_UF_Dest_Reducao_Base'];
        /* Campos para NT 2019.001 - Criação e Atualização de Regras de Validação
        Versão 1.64 – Setembro de 2024, por solicitação da Smartness
        Gabriel Cegantini - 02/10/2025
        ================================================================================ */
        $array['Codigo_Beneficio_Credito_Presumido']       = $mD006['D006_Codigo_Beneficio_Credito_Presumido'];
        $array['Percentual_Beneficio_Credito_Presumido']   = $mD006['D006_Percentual_Beneficio_Credito_Presumido'];
        $array['Codigo_Beneficio_Reducao_Base_ICMS']       = $mD006['D006_Codigo_Beneficio_Reducao_Base_ICMS'];
        /* ================================================================================ */
        /* [REFORMA TRIBUTÁRIA] - Gabriel Cegantini 24/11/2025
        ================================================================================ */
        // Pega as aliquotas da reforma tributaria baseada na classificação tributária CBS/IBS selecionada dentro da "NCM - Vale para todas as empresas" e retorna para serem salvas
        require_once('bibliotecas/classes/ReformaTributaria.php');
        $reformaTributaria = new ReformaTributaria();

        /* Sistema de prioridade, se estiver preenchido na CFOP, prioriza a situação fiscal da CFOP,
        se não, utiliza a situação fiscal da NCM */
        if (!empty($mD006['D006_Situacao_Tributaria_CBS_IBS']) AND !empty($mD006['D006_Classificacao_Tributaria_CBS_IBS'])) {
            // Pega as alíquotas e seta situação fiscal da: Tributação normal
            $aliquotasReformaTributaria = $reformaTributaria->getAliquotas($mD006['D006_Classificacao_Tributaria_CBS_IBS'] ?? "");
            $array['Situacao_Tributaria_CBS_IBS']       = $mD006['D006_Situacao_Tributaria_CBS_IBS'];
            $array['Classificacao_Tributaria_CBS_IBS']  = $mD006['D006_Classificacao_Tributaria_CBS_IBS'];
            
            // Pega as alíquotas e seta situação fiscal da: Tributação regular
            $aliquotasReformaTributariaRegular = $reformaTributaria->getAliquotas($mD006['D006_Classificacao_Tributaria_CBS_IBS_Regular'] ?? "");
            $array['Situacao_Tributaria_CBS_IBS_Regular']       = $mD006['D006_Situacao_Tributaria_CBS_IBS_Regular'];
            $array['Classificacao_Tributaria_CBS_IBS_Regular']  = $mD006['D006_Classificacao_Tributaria_CBS_IBS_Regular'];
        } else {
            // Pega as alíquotas e seta situação fiscal da: Tributação normal
            $aliquotasReformaTributaria = $reformaTributaria->getAliquotas($mProduto['D005_Classificacao_Tributaria_CBS_IBS'] ?? "");
            $array['Situacao_Tributaria_CBS_IBS']       = $mProduto['D005_Situacao_Tributaria_CBS_IBS'];
            $array['Classificacao_Tributaria_CBS_IBS']  = $mProduto['D005_Classificacao_Tributaria_CBS_IBS'];

            // Pega as alíquotas e seta situação fiscal da: Tributação regular
            $aliquotasReformaTributariaRegular = $reformaTributaria->getAliquotas($mProduto['D005_Classificacao_Tributaria_CBS_IBS_Regular'] ?? "");
            $array['Situacao_Tributaria_CBS_IBS_Regular']       = $mProduto['D005_Situacao_Tributaria_CBS_IBS_Regular'];
            $array['Classificacao_Tributaria_CBS_IBS_Regular']  = $mProduto['D005_Classificacao_Tributaria_CBS_IBS_Regular'];
        }

        // Monta a tributação normal da reforma
        $array['Aliquota_CBS']                                  = $aliquotasReformaTributaria['aliquotaCbs'];
        $array['Aliquota_IBS_UF']                               = $aliquotasReformaTributaria['aliquotaIbsUf'];
        $array['Aliquota_IBS_Municipal']                        = $aliquotasReformaTributaria['aliquotaIbsMunicipal'];
        $array['Percentual_Reducao_CBS']                        = $aliquotasReformaTributaria['percentualReducaoCbs'];
        $array['Percentual_Reducao_IBS_UF']                     = $aliquotasReformaTributaria['percentualReducaoIbsUf'];
        $array['Percentual_Reducao_IBS_Municipal']              = $aliquotasReformaTributaria['percentualReducaoIbsMunicipal'];
        $array['Percentual_Diferimento_CBS']                    = $aliquotasReformaTributaria['percentualDiferimentoCbs'];
        $array['Percentual_Diferimento_IBS_UF']                 = $aliquotasReformaTributaria['percentualDiferimentoIbsUf'];
        $array['Percentual_Diferimento_IBS_Municipal']          = $aliquotasReformaTributaria['percentualDiferimentoIbsMunicipal'];

        // Monta a tributação regular da reforma
        $array['Aliquota_CBS_Regular']                          = $aliquotasReformaTributariaRegular['aliquotaCbs'];
        $array['Aliquota_IBS_UF_Regular']                       = $aliquotasReformaTributariaRegular['aliquotaIbsUf'];
        $array['Aliquota_IBS_Municipal_Regular']                = $aliquotasReformaTributariaRegular['aliquotaIbsMunicipal'];
        $array['Percentual_Reducao_CBS_Regular']                = $aliquotasReformaTributariaRegular['percentualReducaoCbs'];
        $array['Percentual_Reducao_IBS_UF_Regular']             = $aliquotasReformaTributariaRegular['percentualReducaoIbsUf'];
        $array['Percentual_Reducao_IBS_Municipal_Regular']      = $aliquotasReformaTributariaRegular['percentualReducaoIbsMunicipal'];
        /* ================================================================================ */

        log("Retorno ind Escala = ". $array['D006_Codigo_Beneficio_Fiscal']);
        // Zera tributos quando for ACP
        if ($Flag_Acp == 3) {
	        $array['Aliquota_IPI']                     = 0;
	        $array['Aliquota_ICMS']                    = 0;
	        $array['Aliquota_ICMS_Oculto']             = 0;
	        $array['Percentual_Reducao_ICMS']          = 0;
	        $array['Percentual_Reducao_ICMS_Oculto']   = 0;
	        $array['Percentual_Reducao_ICMS_ST']       = 0;
	        $array['ST_VA']                            = 0;
	        $array['Flag_ST']                          = 'N';
            // Comentado Andrey 12/04/2017 para não colocar mais N e manter a origem do produto mesmo quando for acp (o primeiro caracter do Flag_Situacao_Tributaria é a origem da mercadoria)
	        //$array['Flag_Situacao_Tributaria']         = 'N';
	        $array['ICMS_Inter_Estadual']              = 0;
	        $array['ST_ICMS_Interno']                  = 0;
	        $array['ST_ICMS_Inter_Estadual_MVA']       = 0;
	        $array['ST_ICMS_Interno_MVA']              = 0;
	        $array['Flag_Isento_PIS']                  = 'S';
	        $array['Aliquota_PIS']                     = 0;
            $array['Percentual_Reducao_PIS']           = 0;
	        $array['Flag_Isento_COFINS']               = 'S';
	        $array['Aliquota_COFINS']                  = 0;
            $array['Percentual_Reducao_COFINS']        = 0;
	        $array['Percentual_Reducao_IPI']           = 0;
	        $array['Aliquota_ICMS_Credito']            = 0;
	        $array['Percentual_Trib_Transparencia']    = 0;
	        $array['Aliquota_IRPJ']                    = 0;
	        $array['Flag_Isento_IRPJ']                 = 'S';
	        $array['Aliquota_CSLL']                    = 0;
	        $array['Flag_Isento_CSLL']                 = 'S';
	        $array['Percentual_Partilha_ICMS_Destino'] = 0;
	        $array['Percentual_Partilha_ICMS_Origem']  = 0;
            $array['Percentual_FCP']                   = 0;
            $array['UF_ICMS_Substituicao_Devido']      = 0;
	        $array['UF_ICMS_Substituicao_Devido']      = 0;
	    }
        log("ALIQUOTA DE CREDITO NO RETORNA ALIQUOTAS: ".$array['Aliquota_ICMS_Credito']);

        mysql_free_result($Cab);
        mysql_free_result($sqlT004);
        mysql_free_result($sqlT006);
        mysql_free_result($sqlT008);
        mysql_free_result($sqlD009);
        mysql_free_result($D006);
        mysql_free_result($Produto);

        return $array;
    }

    /**
     * cotarProduto
     *
     * @param Form $form
     * @return true
     */     
    public function cotarProduto($form)
    {
        global $g;
        $T004_Id                       = $form->campoValorEnviado('T189_T004_Id');
        $D009_Id                       = $form->campoValorEnviado('T189_D009_Id');
        $T189_Observacao               = $form->campoValorEnviado('T189_Observacao');
        $C007_Id                       = $g['usuarioAtual'];
        //Busca dados da T004 para inserir na Tabela de Requisição
        $sql                           = "SELECT * FROM T004 
                LEFT JOIN T003 ON T003_Id = T004_T003_Id
                LEFT JOIN D024 ON D024_Id = T003_D024_Id
                LEFT JOIN D009 ON D009_Id = T004_D009_Id
                WHERE T004_Id = {$T004_Id}";
        $row                           = mysql_fetch_assoc(mysql_query($sql));
        //Insere dados na Tabela de Requisição
        $row['T004_Descricao_Produto'] = mysql_real_escape_string($row['T004_Descricao_Produto']);
        $T189_Observacao               = mysql_real_escape_string($T189_Observacao);
        $row['D024_Nome_Empresa']      = mysql_real_escape_string($row['D024_Nome_Empresa']);
        $row['T004_Descricao_Produto'] = mysql_real_escape_string($row['T004_Descricao_Produto']);
        $T189_Observacao               = mysql_real_escape_string($T189_Observacao);
        $row['D024_Nome_Empresa']      = mysql_real_escape_string($row['D024_Nome_Empresa']);
        
        $row['D009_Quantidade_Estoque_Liquido'] = $row['D009_Quantidade_Estoque_Liquido'] < 0 ? 0 : $row['D009_Quantidade_Estoque_Liquido'];
        
        $quantidade = $row['T004_Quantidade'] - $row['D009_Quantidade_Estoque_Liquido'];
        
        if ($quantidade <= 0) {
            $quantidade = 0;
        }
        $sql = <<<EOT
            INSERT INTO 
            T189 
                (T189_T004_Id, T189_T006_Id, T189_D009_Id, T189_C007_Id, T189_Data_Criado, T189_Descricao_Produto, T189_Quantidade, T189_Observacao, T189_Detalhes, T189_Flag_Tipo) 
            VALUES 
                ('{$T004_Id}', '', '{$D009_Id}', '{$C007_Id}', CURRENT_TIMESTAMP(), '{$row['T004_Descricao_Produto']}', '{$quantidade}', '{$T189_Observacao}', 'Orcamento Nº: {$row['T003_Id']} - {$row['D024_Nome_Empresa']}', 'C');
EOT;
        mysql_query($sql);
        return true;
    }
    
    /**
     * verificaAlcadaNovo
     *
     * @param String $T003_Id
     * @param String $T005_Id
     * @return bool
     */
    public function verificaAlcadaNovo($T003_Id = false, $T005_Id = false, $acao=false)
    {
        global $g;
        /**
         * Código PHP personalizável em cada empresa para verificação de alçadas
         *
         * Retornos:
         *     TRUE:   Quando o usuário tem permissão para prosseguir (Perfil diretor ou aprovado nas alçadas)
         *     FALSE:  Quando o usuário não tem permissão para prosseguir
         *     STRING: Letra informando qual alçada necessária para prosseguir
         */
        if ($T003_Id > 0) {
            $T003                       = mysql_query("SELECT * 
                                                         FROM T003
                                                    LEFT JOIN T003A ON T003_Id = T003A_T003_Id 
                                                        WHERE T003_Id='{$T003_Id}'");
            $mCabecalho                 = mysql_fetch_array($T003);

            $Flag_Status_Liberacao      = $mCabecalho['T003_Flag_Status_Liberacao'];
            $Liberou_Alcada_Desconto    = $mCabecalho['T003_Liberou_Alcada_Desconto'];
            $Liberou_Alcada_Prazo_Medio = $mCabecalho['T003_Liberou_Alcada_Prazo_Medio'];
            $flagAcp                    = $mCabecalho['T003_Flag_Acp'];
            $freteRequerAlcada          = $mCabecalho['T003A_Flag_Frete_Requer_Alcada'];
            $rateiaFreteNoCusto         = $mCabecalho['T003A_Flag_Ratear_Frete_Custo'];
            $valorFreteCalculado        = $mCabecalho['T003_Valor_Frete_Calculado'];
            $valorFreteDigitado         = $mCabecalho['T003_Valor_Frete'];
            $liberouAlcadaFrete         = $mCabecalho['T003A_Liberou_Alcada_Frete'];

            $T004   = mysql_query("SELECT * FROM T004 WHERE T004_T003_Id='{$T003_Id}'");
            $mItens = mysql_fetch_array($T004);

            $T004            = mysql_query("SELECT AVG(T004_Percentual_Desconto_Bruto) FROM T004 WHERE T004_T003_Id='{$T003_Id}'");
            $mTotal_Desconto = mysql_fetch_array($T004);
            $Total_Desconto  = $this->calculaPercentualDescontoOrcamento($T003_Id);
            $Total_Desconto  = number_format($Total_Desconto, 2);

            $T004            = mysql_query("SELECT T004_Percentual_Desconto_Bruto FROM T004 WHERE T004_T003_Id='{$T003_Id}' ORDER BY T004_Percentual_Desconto_Bruto DESC;");
            $mMaior_Desconto = mysql_fetch_array($T004);
            $Maior_Desconto  = number_format($mMaior_Desconto['T004_Percentual_Desconto_Bruto'], 2);

            $valorTotal         = $mCabecalho['T003_Valor_Total'];
            $tabela             = 'T003';
            $id                 = $T003_Id;
            $valorTotalLiberado = $mCabecalho['T003_Liberou_Alcada_Valor_Total'];
            $prazoMedioLiberado = $mCabecalho['T003_Liberou_Alcada_Prazo_Medio'];
            $descontoLiberado   = $mCabecalho['T003_Liberou_Alcada_Desconto'];
            $parcelasLiberado   = $mCabecalho['T003_Liberou_Parcelas_Pagamento'];
            $portadorLiberado   = $mCabecalho['T003_Liberou_Portador_Pagamento'];

            $T089             = mysql_query("SELECT DISTINCT D027_Portador FROM T089 LEFT JOIN D027 ON D027_Id = T089_D027_Id WHERE T089_T003_Id='{$T003_Id}'");
            $numeroPortadores = mysql_num_rows($T089);
            $mPortadores      = mysql_fetch_array($T089);
            $portador         = trim($mPortadores['D027_Portador']);

            $T089      = mysql_query("SELECT COUNT(*) AS Parcelas FROM T089 WHERE T089_T003_Id='{$T003_Id}'");
            $mParcelas = mysql_fetch_array($T089);
            $parcelas  = $mParcelas['Parcelas'];

            $T089       = mysql_query("SELECT AVG(T089_Prazos_Dias) AS PrazoMedio FROM T089 WHERE T089_T003_Id='{$T003_Id}'");
            $mPrazos    = mysql_fetch_array($T089);
            $prazoMedio = number_format($mPrazos['PrazoMedio'], 2);

        } else if ($T005_Id > 0) {

            require_once("bibliotecas/classes/VEN002.php");
            $VEN002 = new VEN002();

            $T005                       = mysql_query("SELECT * 
                                                         FROM T005 
                                                    LEFT JOIN T005A ON T005_Id = T005A_T005_Id
                                                        WHERE T005_Id='{$T005_Id}'");
            $mCabecalho                 = mysql_fetch_array($T005);
            
            $Flag_Status_Liberacao      = $mCabecalho['T005_Flag_Status_Liberacao'];
            $Liberou_Alcada_Desconto    = $mCabecalho['T005_Liberou_Alcada_Desconto'];
            $Liberou_Alcada_Prazo_Medio = $mCabecalho['T005_Liberou_Alcada_Prazo_Medio'];
            $flagAcp                    = $mCabecalho['T005_Flag_ACP'];
            $freteRequerAlcada          = $mCabecalho['T005A_Flag_Frete_Requer_Alcada'];
            $rateiaFreteNoCusto         = $mCabecalho['T005A_Flag_Ratear_Frete_Custo'];
            $valorFreteCalculado        = $mCabecalho['T005_Valor_Frete_Calculado'];
            $valorFreteDigitado         = $mCabecalho['T005_Valor_Frete'];
            $liberouAlcadaFrete         = $mCabecalho['T005A_Liberou_Alcada_Frete'];

            $T006   = mysql_query("SELECT * FROM T006 WHERE T006_T005_Id='{$T005_Id}'");
            $mItens = mysql_fetch_array($T006);

            $T006            = mysql_query("SELECT AVG(T006_Percentual_Desconto_Bruto) FROM T006 WHERE T006_T005_Id='{$T005_Id}'");
            $mTotal_Desconto = mysql_fetch_array($T006);
            $Total_Desconto  = $VEN002->calculaPercentualDescontoPedido($T005_Id);
            $Total_Desconto  = number_format($Total_Desconto, 2);

            $T006            = mysql_query("SELECT T006_Percentual_Desconto_Bruto FROM T006 WHERE T006_T005_Id='{$T005_Id}' ORDER BY T006_Percentual_Desconto_Bruto DESC;");
            $mMaior_Desconto = mysql_fetch_array($T006);
            $Maior_Desconto  = number_format($mMaior_Desconto['T006_Percentual_Desconto_Bruto'], 2);

            if ($mCabecalho['T005_Flag_Status'] > 0 && $mCabecalho['T005_Flag_Status'] != '' && $mCabecalho['T005_Flag_Status'] != 14 && $mCabecalho['T005_Flag_Status'] != 15 && $mCabecalho['T005_Flag_Status'] != 13) {
                return true;
            }

            $valorTotal         = $mCabecalho['T005_Valor_Total'];
            $tabela             = 'T005';
            $id                 = $T005_Id;
            $valorTotalLiberado = $mCabecalho['T005_Liberou_Alcada_Valor_Total'];
            $prazoMedioLiberado = $mCabecalho['T005_Liberou_Alcada_Prazo_Medio'];
            $descontoLiberado   = $mCabecalho['T005_Liberou_Alcada_Desconto'];
            $parcelasLiberado   = $mCabecalho['T005_Liberou_Parcelas_Pagamento'];
            $portadorLiberado   = $mCabecalho['T005_Liberou_Portador_Pagamento'];

            $T090             = mysql_query("SELECT DISTINCT D027_Portador FROM T090 LEFT JOIN D027 ON D027_Id = T090_D027_Id WHERE T090_T005_Id='{$T005_Id}'");
            $numeroPortadores = mysql_num_rows($T090);
            $mPortadores      = mysql_fetch_array($T090);
            $portador         = trim($mPortadores['D027_Portador']);

            $T090      = mysql_query("SELECT COUNT(*) AS Parcelas FROM T090 WHERE T090_T005_Id='{$T005_Id}'");
            $mParcelas = mysql_fetch_array($T090);
            $parcelas  = $mParcelas['Parcelas'];

            $T090       = mysql_query("SELECT AVG(T090_Prazos_Dias) AS PrazoMedio FROM T090 WHERE T090_T005_Id='{$T005_Id}'");
            $mPrazos    = mysql_fetch_array($T090);
            $prazoMedio = number_format($mPrazos['PrazoMedio'], 2);

        }

        if($g['C031']['usarAlcada'] == 'N' && $T003_Id > 0){
            return true;
        }

        $C007   = mysql_query("SELECT * FROM C007 WHERE C007_Id='{$g['usuarioAtual']}'");
        $mC007  = mysql_fetch_array($C007);
        $perfil = $mC007['C007_Flag_Vendedor'];

        $msgRegras = "<br> REGRAS:<br><br>";

        if($perfil=='N' || $perfil=='V' || $perfil=='C'){

            // REGRAS PARA DIRETOR
            for($i = 1;$i <= 10;$i++){
                $i = str_pad($i, 2, '0', STR_PAD_LEFT);
                if (!empty($g['C031']['alcadaVenda_valorInicial'.$i]) && !empty($g['C031']['alcadaVenda_valorFinal'.$i])){
                    $msgRegras .= "DE R$ ".gCorrigeNumero($g['C031']['alcadaVenda_valorInicial'.$i])." ATÉ ".gCorrigeNumero($g['C031']['alcadaVenda_valorFinal'.$i]);
                    if(!empty($g['C031']['alcadaVenda_prazoMedio'.$i])){
                        $msgRegras .= " - ATÉ ".$g['C031']['alcadaVenda_prazoMedio'.$i] . " DIA(S)";
                    }
                    if(!empty($g['C031']['alcadaVenda_qtdeParcelas'.$i])){
                        $msgRegras .= " - MÁXIMO ".$g['C031']['alcadaVenda_qtdeParcelas'.$i]." PARCELA(S)";
                    }
                    if(!empty($g['C031']['alcadaVenda_portador'.$i])){
                        $msgRegras .= " - (".strtoupper($g['C031']['alcadaVenda_portador'.$i]).")";
                    }
                    $msgRegras .= "<br>";
                }
            }
        } else if ($perfil =='S') {
            // REGRAS PARA SUPERVISOR
            for($i = 1;$i <= 10;$i++){
                $i = str_pad($i, 2, '0', STR_PAD_LEFT);
                if (!empty($g['C031']['alcadaVendaSupervisor_valorInicial'.$i]) && !empty($g['C031']['alcadaVendaSupervisor_valorFinal'.$i])){
                    $msgRegras .= "DE R$ ".gCorrigeNumero($g['C031']['alcadaVendaSupervisor_valorInicial'.$i])." ATÉ ".gCorrigeNumero($g['C031']['alcadaVendaSupervisor_valorFinal'.$i]);
                    if(!empty($g['C031']['alcadaVendaSupervisor_prazoMedio'.$i])){
                        $msgRegras .= " - ATÉ ".$g['C031']['alcadaVendaSupervisor_prazoMedio'.$i] . " DIA(S)";
                    }
                    if(!empty($g['C031']['alcadaVendaSupervisor_qtdeParcelas'.$i])){
                        $msgRegras .= " - MÁXIMO ".$g['C031']['alcadaVendaSupervisor_qtdeParcelas'.$i]." PARCELA(S)";
                    }
                    if(!empty($g['C031']['alcadaVendaSupervisor_portador'.$i])){
                        $msgRegras .= " - (".strtoupper($g['C031']['alcadaVendaSupervisor_portador'.$i]).")";
                    }
                    $msgRegras .= "<br>";
                }
            }
        }
     
        if ($perfil == "D") {
            mysqli_query("UPDATE {$tabela} SET {$tabela}_Flag_Status_Liberacao=0, {$tabela}_Flag_Alcada='{$alcada}' WHERE {$tabela}_Id='{$id}'");
            return true;
        }

        if ($flagAcp == "4") {
            // mysql_query("UPDATE {$tabela} SET {$tabela}_Flag_Status_Liberacao=0, {$tabela}_Flag_Alcada='V' WHERE {$tabela}_Id='{$id}'");
            // return true;
        }

        $msg    = '';
        $msgLiberado    = 'Motivo não caiu na liberação (Alçadas): <br /><br />';

        /*
         * Verifica forma de pagamento
         * Quando no orçamento, só verifica se a configuração global "obrigatorioGerarParcelaOrcamento" estiver como SIM
         */
        $g['C031']['alcadaVenda_portador01'] = strtoupper($g['C031']['alcadaVenda_portador01']);
        $g['C031']['alcadaVenda_portador02'] = strtoupper($g['C031']['alcadaVenda_portador02']);
        $g['C031']['alcadaVenda_portador03'] = strtoupper($g['C031']['alcadaVenda_portador03']);
        $g['C031']['alcadaVenda_portador04'] = strtoupper($g['C031']['alcadaVenda_portador04']);
        $g['C031']['alcadaVenda_portador05'] = strtoupper($g['C031']['alcadaVenda_portador05']);
        $g['C031']['alcadaVenda_portador06'] = strtoupper($g['C031']['alcadaVenda_portador06']);
        $g['C031']['alcadaVenda_portador07'] = strtoupper($g['C031']['alcadaVenda_portador07']);
        $g['C031']['alcadaVenda_portador08'] = strtoupper($g['C031']['alcadaVenda_portador08']);
        $g['C031']['alcadaVenda_portador09'] = strtoupper($g['C031']['alcadaVenda_portador09']);
        $g['C031']['alcadaVenda_portador10'] = strtoupper($g['C031']['alcadaVenda_portador10']);
        
        if(($T003_Id > 0 && $g['C031']['obrigatorioGerarParcelaOrcamento'] == 'S') || $T005_Id > 0){
            if($perfil=='N' || $perfil=='V' || $perfil=='C'){
                $alcada = "S";
                if ($valorTotal <= 0) {
                    $alcada = "D";
                    $msg .= "O Valor total da venda deve ser maior que 0.<br />";
                } else if (!empty($g['C031']['alcadaVenda_valorInicial01']) && !empty($g['C031']['alcadaVenda_valorFinal01']) && $valorTotal >= $g['C031']['alcadaVenda_valorInicial01'] && $valorTotal <= $g['C031']['alcadaVenda_valorFinal01']) {
                    if(!empty($g['C031']['alcadaVenda_qtdeParcelas01']) && $parcelas > $g['C031']['alcadaVenda_qtdeParcelas01']){
                        $msg .= "A forma de pagamento possui mais de {$g['C031']['alcadaVenda_qtdeParcelas01']} parcela(s).<br />";
                    } else {
                        if($g['C031']['alcadaVenda_qtdeParcelas01'] > 0){
                            $msgLiberado .= "A forma de pagamento se enquadra em {$g['C031']['alcadaVenda_qtdeParcelas01']} parcela(s).<br />";
                        }
                    }
                    if(!empty($g['C031']['alcadaVenda_prazoMedio01']) && $prazoMedio > $g['C031']['alcadaVenda_prazoMedio01']){
                        $msg .= "O prazo de pagamento está maior que {$g['C031']['alcadaVenda_prazoMedio01']} dia(s).<br />";
                    } else {
                        if($g['C031']['alcadaVenda_prazoMedio01'] > 0){
                            $msgLiberado .= "O prazo de pagamento se enquadra {$g['C031']['alcadaVenda_prazoMedio01']} dia(s).<br />";
                        }
                    }
                    if (!empty($g['C031']['alcadaVenda_portador01']) && strpos($g['C031']['alcadaVenda_portador01'], $portador) === false) {
                        $msg .= 'O portador "'.$portador.'" deve constar em "'.$g['C031']['alcadaVenda_portador01'].'".<br />';
                    } else {
                        if($g['C031']['alcadaVenda_portador01'] > 0){
                            $msgLiberado .= "O portador se enquadra em {$g['C031']['alcadaVenda_portador01']}.<br />";
                        }
                    }
                } else if (!empty($g['C031']['alcadaVenda_valorInicial02']) && !empty($g['C031']['alcadaVenda_valorFinal02']) && $valorTotal >= $g['C031']['alcadaVenda_valorInicial02'] && $valorTotal <= $g['C031']['alcadaVenda_valorFinal02']) {
                    if(!empty($g['C031']['alcadaVenda_qtdeParcelas02']) && $parcelas > $g['C031']['alcadaVenda_qtdeParcelas02']){
                        $msg .= "A forma de pagamento possui mais de {$g['C031']['alcadaVenda_qtdeParcelas02']} parcela(s).<br />";
                    } else {
                        if($g['C031']['alcadaVenda_qtdeParcelas02'] > 0){
                            $msgLiberado .= "A forma de pagamento se enquadra em {$g['C031']['alcadaVenda_qtdeParcelas02']} parcela(s).<br />";
                        }
                    }
                    if(!empty($g['C031']['alcadaVenda_prazoMedio02']) && $prazoMedio > $g['C031']['alcadaVenda_prazoMedio02']){
                        $msg .= "O prazo de pagamento está maior que {$g['C031']['alcadaVenda_prazoMedio02']} dia(s).<br />";
                    } else {
                        if($g['C031']['alcadaVenda_prazoMedio02'] > 0){
                            $msgLiberado .= "O prazo de pagamento se enquadra em {$g['C031']['alcadaVenda_prazoMedio02']} dia(s).<br />";
                        }
                    }
                    if (!empty($g['C031']['alcadaVenda_portador02']) && strpos($g['C031']['alcadaVenda_portador02'], $portador) === false) {
                        $msg .= 'O portador "'.$portador.'" deve constar em "'.$g['C031']['alcadaVenda_portador02'].'".<br />';
                    } else {
                        if($g['C031']['alcadaVenda_portador02'] > 0){
                            $msgLiberado .= "O portador da forma de pagamento se enquadra em {$g['C031']['alcadaVenda_portador02']}.<br />";
                        }
                    }
                } else if (!empty($g['C031']['alcadaVenda_valorInicial03']) && !empty($g['C031']['alcadaVenda_valorFinal03']) && $valorTotal >= $g['C031']['alcadaVenda_valorInicial03'] && $valorTotal <= $g['C031']['alcadaVenda_valorFinal03']) {
                    if(!empty($g['C031']['alcadaVenda_qtdeParcelas03']) && $parcelas > $g['C031']['alcadaVenda_qtdeParcelas03']){
                        $msg .= "A forma de pagamento possui mais de {$g['C031']['alcadaVenda_qtdeParcelas03']} parcela(s).<br />";
                    } else {
                        if($g['C031']['alcadaVenda_qtdeParcelas03'] > 0){
                            $msgLiberado .= "A forma de pagamento se enquadra em {$g['C031']['alcadaVenda_qtdeParcelas03']} parcela(s).<br />";
                        }
                    }
                    if(!empty($g['C031']['alcadaVenda_prazoMedio03']) && $prazoMedio > $g['C031']['alcadaVenda_prazoMedio03']){
                        $msg .= "O prazo de pagamento está maior que {$g['C031']['alcadaVenda_prazoMedio03']} dia(s).<br />";
                    } else {
                        if($g['C031']['alcadaVenda_prazoMedio03'] > 0){
                            $msgLiberado .= "O prazo de pagamento se enquadra em {$g['C031']['alcadaVenda_prazoMedio03']} dia(s).<br />";
                        }
                    }
                    if (!empty($g['C031']['alcadaVenda_portador03']) && strpos($g['C031']['alcadaVenda_portador03'], $portador) === false) {
                        $msg .= 'O portador "'.$portador.'" deve constar em "'.$g['C031']['alcadaVenda_portador03'].'".<br />';
                    } else {
                        if($g['C031']['alcadaVenda_portador03'] > 0){
                            $msgLiberado .= "O portador da forma de pagamento se enquadra em {$g['C031']['alcadaVenda_portador03']}.<br />";
                        }
                    }
                } else if (!empty($g['C031']['alcadaVenda_valorInicial04']) && !empty($g['C031']['alcadaVenda_valorFinal04']) && $valorTotal >= $g['C031']['alcadaVenda_valorInicial04'] && $valorTotal <= $g['C031']['alcadaVenda_valorFinal04']) {
                    if(!empty($g['C031']['alcadaVenda_qtdeParcelas04']) && $parcelas > $g['C031']['alcadaVenda_qtdeParcelas04']){
                        $msg .= "A forma de pagamento possui mais de {$g['C031']['alcadaVenda_qtdeParcelas04']} parcela(s).<br />";
                    } else {
                        if($g['C031']['alcadaVenda_qtdeParcelas04'] > 0){
                            $msgLiberado .= "A forma de pagamento se enquadra em {$g['C031']['alcadaVenda_qtdeParcelas04']} parcela(s).<br />";
                        }
                    }
                    if(!empty($g['C031']['alcadaVenda_prazoMedio04']) && $prazoMedio > $g['C031']['alcadaVenda_prazoMedio04']){
                        $msg .= "O prazo de pagamento está maior que {$g['C031']['alcadaVenda_prazoMedio04']} dia(s).<br />";
                    } else {
                        if($g['C031']['alcadaVenda_prazoMedio04'] > 0){
                            $msgLiberado .= "O prazo de pagamento se enquadra em {$g['C031']['alcadaVenda_prazoMedio04']} dia(s).<br />";
                        }
                    }
                    if (!empty($g['C031']['alcadaVenda_portador04']) && strpos($g['C031']['alcadaVenda_portador04'], $portador) === false) {
                        $msg .= 'O portador "'.$portador.'" deve constar em "'.$g['C031']['alcadaVenda_portador04'].'".<br />';
                    } else {
                        if($g['C031']['alcadaVenda_portador04'] > 0){
                            $msgLiberado .= "O portador da forma de pagamento se enquadra em {$g['C031']['alcadaVenda_portador04']}.<br />";
                        }
                    }
                } else if (!empty($g['C031']['alcadaVenda_valorInicial05']) && !empty($g['C031']['alcadaVenda_valorFinal05']) && $valorTotal >= $g['C031']['alcadaVenda_valorInicial05'] && $valorTotal <= $g['C031']['alcadaVenda_valorFinal05']) {
                    if(!empty($g['C031']['alcadaVenda_qtdeParcelas05']) && $parcelas > $g['C031']['alcadaVenda_qtdeParcelas05']){
                        $msg .= "A forma de pagamento possui mais de {$g['C031']['alcadaVenda_qtdeParcelas05']} parcela(s).<br />";
                    } else {
                        if($g['C031']['alcadaVenda_qtdeParcelas05'] > 0){
                            $msgLiberado .= "A forma de pagamento se enquadra em {$g['C031']['alcadaVenda_qtdeParcelas05']} parcela(s).<br />";
                        }
                    }
                    if(!empty($g['C031']['alcadaVenda_prazoMedio05']) && $prazoMedio > $g['C031']['alcadaVenda_prazoMedio05']){
                        $msg .= "O prazo de pagamento está maior que {$g['C031']['alcadaVenda_prazoMedio05']} dia(s).<br />";
                    } else {
                        if($g['C031']['alcadaVenda_prazoMedio05'] > 0){
                            $msgLiberado .= "O prazo de pagamento se enquadra em {$g['C031']['alcadaVenda_prazoMedio05']} dia(s).<br />";
                        }
                    }
                    if (!empty($g['C031']['alcadaVenda_portador05']) && strpos($g['C031']['alcadaVenda_portador05'], $portador) === false) {
                        $msg .= 'O portador "'.$portador.'" deve constar em "'.$g['C031']['alcadaVenda_portador05'].'".<br />';
                    } else {
                        if($g['C031']['alcadaVenda_portador05'] > 0){
                            $msgLiberado .= "O portador da forma de pagamento se enquadra em {$g['C031']['alcadaVenda_portador05']}.<br />";
                        }
                    }
                } else if (!empty($g['C031']['alcadaVenda_valorInicial06']) && !empty($g['C031']['alcadaVenda_valorFinal06']) && $valorTotal >= $g['C031']['alcadaVenda_valorInicial06'] && $valorTotal <= $g['C031']['alcadaVenda_valorFinal06']) {
                    if(!empty($g['C031']['alcadaVenda_qtdeParcelas06']) && $parcelas > $g['C031']['alcadaVenda_qtdeParcelas06']){
                        $msg .= "A forma de pagamento possui mais de {$g['C031']['alcadaVenda_qtdeParcelas06']} parcela(s).<br />";
                    } else {
                        if($g['C031']['alcadaVenda_qtdeParcelas06'] > 0){
                            $msgLiberado .= "A forma de pagamento se enquadra em {$g['C031']['alcadaVenda_qtdeParcelas06']} parcela(s).<br />";
                        }
                    }
                    if(!empty($g['C031']['alcadaVenda_prazoMedio06']) && $prazoMedio > $g['C031']['alcadaVenda_prazoMedio06']){
                        $msg .= "O prazo de pagamento está maior que {$g['C031']['alcadaVenda_prazoMedio06']} dia(s).<br />";
                    } else {
                        if($g['C031']['alcadaVenda_prazoMedio06'] > 0){
                            $msgLiberado .= "O prazo de pagamento se enquadra em {$g['C031']['alcadaVenda_prazoMedio06']} dia(s).<br />";
                        }
                    }
                    if (!empty($g['C031']['alcadaVenda_portador06']) && strpos($g['C031']['alcadaVenda_portador06'], $portador) === false) {
                        $msg .= 'O portador "'.$portador.'" deve constar em "'.$g['C031']['alcadaVenda_portador06'].'".<br />';
                    } else {
                        if($g['C031']['alcadaVenda_portador06'] > 0){
                            $msgLiberado .= "O portador da forma de pagamento se enquadra {$g['C031']['alcadaVenda_portador06']}.<br />";
                        }
                    }
                } else if (!empty($g['C031']['alcadaVenda_valorInicial07']) && !empty($g['C031']['alcadaVenda_valorFinal07']) && $valorTotal >= $g['C031']['alcadaVenda_valorInicial07'] && $valorTotal <= $g['C031']['alcadaVenda_valorFinal07']) {
                    if(!empty($g['C031']['alcadaVenda_qtdeParcelas07']) && $parcelas > $g['C031']['alcadaVenda_qtdeParcelas07']){
                        $msg .= "A forma de pagamento possui mais de {$g['C031']['alcadaVenda_qtdeParcelas07']} parcela(s).<br />";
                    } else {
                        if($g['C031']['alcadaVenda_qtdeParcelas07'] > 0){
                            $msgLiberado .= "A forma de pagamento se enquadra em {$g['C031']['alcadaVenda_qtdeParcelas07']} parcela(s).<br />";
                        }
                    }
                    if(!empty($g['C031']['alcadaVenda_prazoMedio07']) && $prazoMedio > $g['C031']['alcadaVenda_prazoMedio07']){
                        $msg .= "O prazo de pagamento está maior que {$g['C031']['alcadaVenda_prazoMedio07']} dia(s).<br />";
                    } else {
                        if($g['C031']['alcadaVenda_prazoMedio07'] > 0){
                            $msgLiberado .= "O prazo de pagamento se enquadra em {$g['C031']['alcadaVenda_prazoMedio07']} dia(s).<br />";
                        }
                    }
                    if (!empty($g['C031']['alcadaVenda_portador07']) && strpos($g['C031']['alcadaVenda_portador07'], $portador) === false) {
                        $msg .= 'O portador "'.$portador.'" deve constar em "'.$g['C031']['alcadaVenda_portador07'].'".<br />';
                    } else {
                        if($g['C031']['alcadaVenda_portador07'] > 0){
                            $msgLiberado .= "O portador da forma de pagamento se enquadra em {$g['C031']['alcadaVenda_portador07']}.<br />";
                        }
                    }
                } else if (!empty($g['C031']['alcadaVenda_valorInicial08']) && !empty($g['C031']['alcadaVenda_valorFinal08']) && $valorTotal >= $g['C031']['alcadaVenda_valorInicial08'] && $valorTotal <= $g['C031']['alcadaVenda_valorFinal08']) {
                    if(!empty($g['C031']['alcadaVenda_qtdeParcelas08']) && $parcelas > $g['C031']['alcadaVenda_qtdeParcelas08']){
                        $msg .= "A forma de pagamento possui mais de {$g['C031']['alcadaVenda_qtdeParcelas08']} parcela(s).<br />";
                    } else {
                        if($g['C031']['alcadaVenda_qtdeParcelas08'] > 0){
                            $msgLiberado .= "A forma de pagamento se enquadra em {$g['C031']['alcadaVenda_qtdeParcelas08']} parcela(s).<br />";
                        }
                    }
                    if(!empty($g['C031']['alcadaVenda_prazoMedio08']) && $prazoMedio > $g['C031']['alcadaVenda_prazoMedio08']){
                        $msg .= "O prazo de pagamento está maior que {$g['C031']['alcadaVenda_prazoMedio08']} dia(s).<br />";
                    } else {
                        if($g['C031']['alcadaVenda_prazoMedio08'] > 0){
                            $msgLiberado .= "O prazo de pagamento se enquadra {$g['C031']['alcadaVenda_prazoMedio08']} dia(s).<br />";
                        }
                    }
                    if (!empty($g['C031']['alcadaVenda_portador08']) && strpos($g['C031']['alcadaVenda_portador08'], $portador) === false) {
                        $msg .= 'O portador "'.$portador.'" deve constar em "'.$g['C031']['alcadaVenda_portador08'].'".<br />';
                    } else {
                        if($g['C031']['alcadaVenda_portador08'] > 0){
                            $msgLiberado .= "O portador da forma de pagamento se enquadra {$g['C031']['alcadaVenda_portador08']}.<br />";
                        }
                    }
                } else if (!empty($g['C031']['alcadaVenda_valorInicial09']) && !empty($g['C031']['alcadaVenda_valorFinal09']) && $valorTotal >= $g['C031']['alcadaVenda_valorInicial09'] && $valorTotal <= $g['C031']['alcadaVenda_valorFinal09']) {
                    if(!empty($g['C031']['alcadaVenda_qtdeParcelas09']) && $parcelas > $g['C031']['alcadaVenda_qtdeParcelas09']){
                        $msg .= "A forma de pagamento possui mais de {$g['C031']['alcadaVenda_qtdeParcelas09']} parcela(s).<br />";
                    } else {
                        if($g['C031']['alcadaVenda_qtdeParcelas09'] > 0){
                            $msgLiberado .= "A forma de pagamento se enquadra em {$g['C031']['alcadaVenda_qtdeParcelas09']} parcela(s).<br />";
                        }
                    }
                    if(!empty($g['C031']['alcadaVenda_prazoMedio09']) && $prazoMedio > $g['C031']['alcadaVenda_prazoMedio09']){
                        $msg .= "O prazo de pagamento está maior que {$g['C031']['alcadaVenda_prazoMedio09']} dia(s).<br />";
                    } else {
                        if($g['C031']['alcadaVenda_prazoMedio09'] > 0){
                            $msgLiberado .= "O prazo de pagamento se enquadra em {$g['C031']['alcadaVenda_prazoMedio09']} dia(s).<br />";
                        }
                    }
                    if (!empty($g['C031']['alcadaVenda_portador09']) && strpos($g['C031']['alcadaVenda_portador09'], $portador) === false) {
                        $msg .= 'O portador "'.$portador.'" deve constar em "'.$g['C031']['alcadaVenda_portador09'].'".<br />';
                    } else {
                        if($g['C031']['alcadaVenda_portador09'] > 0){
                            $msgLiberado .= "O portador da forma de pagamento se enquadra em {$g['C031']['alcadaVenda_portador09']}.<br />";
                        }
                    }
                } else if (!empty($g['C031']['alcadaVenda_valorInicial10']) && !empty($g['C031']['alcadaVenda_valorFinal10']) && $valorTotal >= $g['C031']['alcadaVenda_valorInicial10'] && $valorTotal <= $g['C031']['alcadaVenda_valorFinal10']) {
                    if(!empty($g['C031']['alcadaVenda_qtdeParcelas10']) && $parcelas > $g['C031']['alcadaVenda_qtdeParcelas10']){
                        $msg .= "A forma de pagamento possui mais de {$g['C031']['alcadaVenda_qtdeParcelas10']} parcela(s).<br />";
                    } else {
                        if($g['C031']['alcadaVenda_qtdeParcelas10'] > 0){
                            $msgLiberado .= "A forma de pagamento se enquadra em {$g['C031']['alcadaVenda_qtdeParcelas10']} parcela(s).<br />";
                        }
                    }
                    if(!empty($g['C031']['alcadaVenda_prazoMedio10']) && $prazoMedio > $g['C031']['alcadaVenda_prazoMedio10']){
                        $msg .= "O prazo de pagamento está maior que {$g['C031']['alcadaVenda_prazoMedio10']} dia(s).<br />";
                    } else {
                        if($g['C031']['alcadaVenda_prazoMedio10'] > 0){
                            $msgLiberado .= "O prazo de pagamento se enquadra em {$g['C031']['alcadaVenda_prazoMedio10']} dia(s).<br />";
                        }
                    }
                    if (!empty($g['C031']['alcadaVenda_portador10']) && strpos($g['C031']['alcadaVenda_portador10'], $portador) === false) {
                        $msg .= 'O portador "'.$portador.'" deve constar em "'.$g['C031']['alcadaVenda_portador10'].'".<br />';
                    } else {
                        if($g['C031']['alcadaVenda_portador10'] > 0){
                            $msgLiberado .= "O portador da forma de pagamento se enquadra em {$g['C031']['alcadaVenda_portador10']}.<br />";
                        }
                    }

                } else {
                    $msgLiberado .= "O Valor total da venda é maior que 0.<br />";
                }

                if($freteRequerAlcada == 'S'){
                    // Essa validação se o frete precisa de análise está sendo feita no APP REGRAS FRETE VENDA
                    // FELIPE CARRANO 11/11/2022
                    $rateiaFreteNoCusto     = ($rateiaFreteNoCusto == 'N') ? 'não rateado no custo' : 'rateado no custo';
                    $msgValorFreteCalculado = gCorrigeNumero($valorFreteCalculado);
                    $msgValorFreteDigitado  = gCorrigeNumero($valorFreteDigitado);

                    $msg .= "Frete por conta remetente. Calculado: R$ {$msgValorFreteCalculado} - Digitado: R$ {$msgValorFreteDigitado} ({$rateiaFreteNoCusto})<br />";
                }

                if ($msg == '') {
                    $alcada = "V";
                }
            }

            if($perfil=='S' || $alcada=='S'){
                $msgSup='';
                $msgSupLiberado='Motivo não caiu na liberação (Alçadas): <br />';
                $alcada = "D";
                if ($valorTotal <= 0) {
                    $alcada = "D";
                    $msgSup .= "O Valor total da venda deve ser maior que 0.<br />";
                } else if (!empty($g['C031']['alcadaVendaSupervisor_valorInicial01']) && !empty($g['C031']['alcadaVendaSupervisor_valorFinal01']) && $valorTotal >= $g['C031']['alcadaVendaSupervisor_valorInicial01'] && $valorTotal <= $g['C031']['alcadaVendaSupervisor_valorFinal01']) {
                    if(!empty($g['C031']['alcadaVendaSupervisor_qtdeParcelas01']) && $parcelas > $g['C031']['alcadaVendaSupervisor_qtdeParcelas01']){
                        $msgSup .= "A forma de pagamento possui mais de {$g['C031']['alcadaVendaSupervisor_qtdeParcelas01']} parcela(s).<br />";
                    } else {
                        if($g['C031']['alcadaVendaSupervisor_qtdeParcelas01'] > 0){
                            $msgSupLiberado .= "A forma de pagamento se enquadra em {$g['C031']['alcadaVendaSupervisor_qtdeParcelas01']} parcela(s).<br />";
                        }
                    }
                    if(!empty($g['C031']['alcadaVendaSupervisor_prazoMedio01']) && $prazoMedio > $g['C031']['alcadaVendaSupervisor_prazoMedio01']){
                        $msgSup .= "O prazo de pagamento está maior que {$g['C031']['alcadaVendaSupervisor_prazoMedio01']} dia(s).<br />";
                    } else {
                        if($g['C031']['alcadaVendaSupervisor_prazoMedio01'] > 0){
                            $msgSupLiberado .= "O prazo de pagamento se enquadra {$g['C031']['alcadaVendaSupervisor_prazoMedio01']} dia(s).<br />";
                        }
                    }
                    if (!empty($g['C031']['alcadaVendaSupervisor_portador01']) && strpos($g['C031']['alcadaVendaSupervisor_portador01'], $portador) === false) {
                        $msgSup .= "O portador da forma de pagamento deve ser {$g['C031']['alcadaVendaSupervisor_portador01']}.<br />";
                    } else {
                        if($g['C031']['alcadaVendaSupervisor_portador01'] > 0){
                            $msgSupLiberado .= 'O portador "'.$portador.'" da forma de pagamento deve ser "'.$g['C031']['alcadaVendaSupervisor_portador01'].'".<br />';
                        }
                    }
                } else if (!empty($g['C031']['alcadaVendaSupervisor_valorInicial02']) && !empty($g['C031']['alcadaVendaSupervisor_valorFinal02']) && $valorTotal >= $g['C031']['alcadaVendaSupervisor_valorInicial02'] && $valorTotal <= $g['C031']['alcadaVendaSupervisor_valorFinal02']) {
                    if(!empty($g['C031']['alcadaVendaSupervisor_qtdeParcelas02']) && $parcelas > $g['C031']['alcadaVendaSupervisor_qtdeParcelas02']){
                        $msgSup .= "A forma de pagamento possui mais de {$g['C031']['alcadaVendaSupervisor_qtdeParcelas02']} parcela(s).<br />";
                    } else {
                        if($g['C031']['alcadaVendaSupervisor_qtdeParcelas02'] > 0){
                            $msgSupLiberado .= "A forma de pagamento se enquadra em {$g['C031']['alcadaVendaSupervisor_qtdeParcelas02']} parcela(s).<br />";
                        }
                    }
                    if(!empty($g['C031']['alcadaVendaSupervisor_prazoMedio02']) && $prazoMedio > $g['C031']['alcadaVendaSupervisor_prazoMedio02']){
                        $msgSup .= "O prazo de pagamento está maior que {$g['C031']['alcadaVendaSupervisor_prazoMedio02']} dia(s).<br />";
                    } else {
                        if($g['C031']['alcadaVendaSupervisor_prazoMedio02'] > 0){
                            $msgSupLiberado .= "O prazo de pagamento se enquadra em {$g['C031']['alcadaVendaSupervisor_prazoMedio02']} dia(s).<br />";
                        }
                    }
                    if (!empty($g['C031']['alcadaVendaSupervisor_portador02']) && strpos($g['C031']['alcadaVendaSupervisor_portador02'], $portador) === false) {
                        $msgSup .= "O portador da forma de pagamento deve ser {$g['C031']['alcadaVendaSupervisor_portador02']}.<br />";
                    } else {
                        if($g['C031']['alcadaVendaSupervisor_portador02'] > 0){
                            $msgSupLiberado .= 'O portador "'.$portador.'" da forma de pagamento deve ser "'.$g['C031']['alcadaVendaSupervisor_portador02'].'".<br />';
                        }
                    }
                } else if (!empty($g['C031']['alcadaVendaSupervisor_valorInicial03']) && !empty($g['C031']['alcadaVendaSupervisor_valorFinal03']) && $valorTotal >= $g['C031']['alcadaVendaSupervisor_valorInicial03'] && $valorTotal <= $g['C031']['alcadaVendaSupervisor_valorFinal03']) {
                    if(!empty($g['C031']['alcadaVendaSupervisor_qtdeParcelas03']) && $parcelas > $g['C031']['alcadaVendaSupervisor_qtdeParcelas03']){
                        $msgSup .= "A forma de pagamento possui mais de {$g['C031']['alcadaVendaSupervisor_qtdeParcelas03']} parcela(s).<br />";
                    } else {
                        if($g['C031']['alcadaVendaSupervisor_qtdeParcelas03'] > 0){
                            $msgSupLiberado .= "A forma de pagamento se enquadra em {$g['C031']['alcadaVendaSupervisor_qtdeParcelas03']} parcela(s).<br />";
                        }
                    }
                    if(!empty($g['C031']['alcadaVendaSupervisor_prazoMedio03']) && $prazoMedio > $g['C031']['alcadaVendaSupervisor_prazoMedio03']){
                        $msgSup .= "O prazo de pagamento está maior que {$g['C031']['alcadaVendaSupervisor_prazoMedio03']} dia(s).<br />";
                    } else {
                        if($g['C031']['alcadaVendaSupervisor_prazoMedio03'] > 0){
                            $msgSupLiberado .= "O prazo de pagamento se enquadra em {$g['C031']['alcadaVendaSupervisor_prazoMedio03']} dia(s).<br />";
                        }
                    }
                    if (!empty($g['C031']['alcadaVendaSupervisor_portador03']) && strpos($g['C031']['alcadaVendaSupervisor_portador03'], $portador) === false) {
                        $msgSup .= "O portador da forma de pagamento deve ser {$g['C031']['alcadaVendaSupervisor_portador03']}.<br />";
                    } else {
                        if($g['C031']['alcadaVendaSupervisor_portador03'] > 0){
                            $msgSupLiberado .= 'O portador "'.$portador.'" da forma de pagamento deve ser "'.$g['C031']['alcadaVendaSupervisor_portador03'].'".<br />';
                        }
                    }
                } else if (!empty($g['C031']['alcadaVendaSupervisor_valorInicial04']) && !empty($g['C031']['alcadaVendaSupervisor_valorFinal04']) && $valorTotal >= $g['C031']['alcadaVendaSupervisor_valorInicial04'] && $valorTotal <= $g['C031']['alcadaVendaSupervisor_valorFinal04']) {
                    if(!empty($g['C031']['alcadaVendaSupervisor_qtdeParcelas04']) && $parcelas > $g['C031']['alcadaVendaSupervisor_qtdeParcelas04']){
                        $msgSup .= "A forma de pagamento possui mais de {$g['C031']['alcadaVendaSupervisor_qtdeParcelas04']} parcela(s).<br />";
                    } else {
                        if($g['C031']['alcadaVendaSupervisor_qtdeParcelas04'] > 0){
                            $msgSupLiberado .= "A forma de pagamento se enquadra em {$g['C031']['alcadaVendaSupervisor_qtdeParcelas04']} parcela(s).<br />";
                        }
                    }
                    if(!empty($g['C031']['alcadaVendaSupervisor_prazoMedio04']) && $prazoMedio > $g['C031']['alcadaVendaSupervisor_prazoMedio04']){
                        $msgSup .= "O prazo de pagamento está maior que {$g['C031']['alcadaVendaSupervisor_prazoMedio04']} dia(s).<br />";
                    } else {
                        if($g['C031']['alcadaVendaSupervisor_prazoMedio04'] > 0){
                            $msgSupLiberado .= "O prazo de pagamento se enquadra em {$g['C031']['alcadaVendaSupervisor_prazoMedio04']} dia(s).<br />";
                        }
                    }
                    if (!empty($g['C031']['alcadaVendaSupervisor_portador04']) && strpos($g['C031']['alcadaVendaSupervisor_portador04'], $portador) === false) {
                        $msgSup .= "O portador da forma de pagamento deve ser {$g['C031']['alcadaVendaSupervisor_portador04']}.<br />";
                    } else {
                        if($g['C031']['alcadaVendaSupervisor_portador04'] > 0){
                            $msgSupLiberado .= 'O portador "'.$portador.'" da forma de pagamento deve ser "'.$g['C031']['alcadaVendaSupervisor_portador04'].'".<br />';
                        }
                    }
                } else if (!empty($g['C031']['alcadaVendaSupervisor_valorInicial05']) && !empty($g['C031']['alcadaVendaSupervisor_valorFinal05']) && $valorTotal >= $g['C031']['alcadaVendaSupervisor_valorInicial05'] && $valorTotal <= $g['C031']['alcadaVendaSupervisor_valorFinal05']) {
                    if(!empty($g['C031']['alcadaVendaSupervisor_qtdeParcelas05']) && $parcelas > $g['C031']['alcadaVendaSupervisor_qtdeParcelas05']){
                        $msgSup .= "A forma de pagamento possui mais de {$g['C031']['alcadaVendaSupervisor_qtdeParcelas05']} parcela(s).<br />";
                    } else {
                        if($g['C031']['alcadaVendaSupervisor_qtdeParcelas05'] > 0){
                            $msgSupLiberado .= "A forma de pagamento se enquadra em {$g['C031']['alcadaVendaSupervisor_qtdeParcelas05']} parcela(s).<br />";
                        }
                    }
                    if(!empty($g['C031']['alcadaVendaSupervisor_prazoMedio05']) && $prazoMedio > $g['C031']['alcadaVendaSupervisor_prazoMedio05']){
                        $msgSup .= "O prazo de pagamento está maior que {$g['C031']['alcadaVendaSupervisor_prazoMedio05']} dia(s).<br />";
                    } else {
                        if($g['C031']['alcadaVendaSupervisor_prazoMedio05'] > 0){
                            $msgSupLiberado .= "O prazo de pagamento se enquadra em {$g['C031']['alcadaVendaSupervisor_prazoMedio05']} dia(s).<br />";
                        }
                    }
                    if (!empty($g['C031']['alcadaVendaSupervisor_portador05']) && strpos($g['C031']['alcadaVendaSupervisor_portador05'], $portador) === false) {
                        $msgSup .= "O portador da forma de pagamento deve ser {$g['C031']['alcadaVendaSupervisor_portador05']}.<br />";
                    } else {
                        if($g['C031']['alcadaVendaSupervisor_portador05'] > 0){
                            $msgSupLiberado .= 'O portador "'.$portador.'" da forma de pagamento deve ser "'.$g['C031']['alcadaVendaSupervisor_portador05'].'".<br />';
                        }
                    }
                } else if (!empty($g['C031']['alcadaVendaSupervisor_valorInicial06']) && !empty($g['C031']['alcadaVendaSupervisor_valorFinal06']) && $valorTotal >= $g['C031']['alcadaVendaSupervisor_valorInicial06'] && $valorTotal <= $g['C031']['alcadaVendaSupervisor_valorFinal06']) {
                    if(!empty($g['C031']['alcadaVendaSupervisor_qtdeParcelas06']) && $parcelas > $g['C031']['alcadaVendaSupervisor_qtdeParcelas06']){
                        $msgSup .= "A forma de pagamento possui mais de {$g['C031']['alcadaVendaSupervisor_qtdeParcelas06']} parcela(s).<br />";
                    } else {
                        if($g['C031']['alcadaVendaSupervisor_qtdeParcelas06'] > 0){
                            $msgSupLiberado .= "A forma de pagamento se enquadra em {$g['C031']['alcadaVendaSupervisor_qtdeParcelas06']} parcela(s).<br />";
                        }
                    }
                    if(!empty($g['C031']['alcadaVendaSupervisor_prazoMedio06']) && $prazoMedio > $g['C031']['alcadaVendaSupervisor_prazoMedio06']){
                        $msgSup .= "O prazo de pagamento está maior que {$g['C031']['alcadaVendaSupervisor_prazoMedio06']} dia(s).<br />";
                    } else {
                        if($g['C031']['alcadaVendaSupervisor_prazoMedio06'] > 0){
                            $msgSupLiberado .= "O prazo de pagamento se enquadra em {$g['C031']['alcadaVendaSupervisor_prazoMedio06']} dia(s).<br />";
                        }
                    }
                    if (!empty($g['C031']['alcadaVendaSupervisor_portador06']) && strpos($g['C031']['alcadaVendaSupervisor_portador06'], $portador) === false) {
                        $msgSup .= "O portador da forma de pagamento deve ser {$g['C031']['alcadaVendaSupervisor_portador06']}.<br />";
                    } else {
                        if($g['C031']['alcadaVendaSupervisor_portador06'] > 0){
                            $msgSupLiberado .= 'O portador "'.$portador.'" da forma de pagamento deve ser "'.$g['C031']['alcadaVendaSupervisor_portador06'].'".<br />';
                        }
                    }
                } else if (!empty($g['C031']['alcadaVendaSupervisor_valorInicial07']) && !empty($g['C031']['alcadaVendaSupervisor_valorFinal07']) && $valorTotal >= $g['C031']['alcadaVendaSupervisor_valorInicial07'] && $valorTotal <= $g['C031']['alcadaVendaSupervisor_valorFinal07']) {
                    if(!empty($g['C031']['alcadaVendaSupervisor_qtdeParcelas07']) && $parcelas > $g['C031']['alcadaVendaSupervisor_qtdeParcelas07']){
                        $msgSup .= "A forma de pagamento possui mais de {$g['C031']['alcadaVendaSupervisor_qtdeParcelas07']} parcela(s).<br />";
                    } else {
                        if($g['C031']['alcadaVendaSupervisor_qtdeParcelas07'] > 0){
                            $msgSupLiberado .= "A forma de pagamento se enquadra em {$g['C031']['alcadaVendaSupervisor_qtdeParcelas07']} parcela(s).<br />";
                        }
                    }
                    if(!empty($g['C031']['alcadaVendaSupervisor_prazoMedio07']) && $prazoMedio > $g['C031']['alcadaVendaSupervisor_prazoMedio07']){
                        $msgSup .= "O prazo de pagamento está maior que {$g['C031']['alcadaVendaSupervisor_prazoMedio07']} dia(s).<br />";
                    } else {
                        if($g['C031']['alcadaVendaSupervisor_prazoMedio07'] > 0){
                            $msgSupLiberado .= "O prazo de pagamento se enquadra em {$g['C031']['alcadaVendaSupervisor_prazoMedio07']} dia(s).<br />";
                        }
                    }
                    if (!empty($g['C031']['alcadaVendaSupervisor_portador07']) && strpos($g['C031']['alcadaVendaSupervisor_portador07'], $portador) === false) {
                        $msgSup .= "O portador da forma de pagamento deve ser {$g['C031']['alcadaVendaSupervisor_portador07']}.<br />";
                    } else {
                        if($g['C031']['alcadaVendaSupervisor_portador07'] > 0){
                            $msgSupLiberado .= 'O portador "'.$portador.'" da forma de pagamento deve ser "'.$g['C031']['alcadaVendaSupervisor_portador07'].'".<br />';
                        }
                    }
                } else if (!empty($g['C031']['alcadaVendaSupervisor_valorInicial08']) && !empty($g['C031']['alcadaVendaSupervisor_valorFinal08']) && $valorTotal >= $g['C031']['alcadaVendaSupervisor_valorInicial08'] && $valorTotal <= $g['C031']['alcadaVendaSupervisor_valorFinal08']) {
                    if(!empty($g['C031']['alcadaVendaSupervisor_qtdeParcelas08']) && $parcelas > $g['C031']['alcadaVendaSupervisor_qtdeParcelas08']){
                        $msgSup .= "A forma de pagamento possui mais de {$g['C031']['alcadaVendaSupervisor_qtdeParcelas08']} parcela(s).<br />";
                    } else {
                        if($g['C031']['alcadaVendaSupervisor_qtdeParcelas08'] > 0){
                            $msgSupLiberado .= "A forma de pagamento se enquadra {$g['C031']['alcadaVendaSupervisor_qtdeParcelas08']} parcela(s).<br />";
                        }
                    }
                    if(!empty($g['C031']['alcadaVendaSupervisor_prazoMedio08']) && $prazoMedio > $g['C031']['alcadaVendaSupervisor_prazoMedio08']){
                        $msgSup .= "O prazo de pagamento está maior que {$g['C031']['alcadaVendaSupervisor_prazoMedio08']} dia(s).<br />";
                    } else {
                        if($g['C031']['alcadaVendaSupervisor_prazoMedio08'] > 0){
                            $msgSupLiberado .= "O prazo de pagamento se enquadra em {$g['C031']['alcadaVendaSupervisor_prazoMedio08']} dia(s).<br />";
                        }
                    }
                    if (!empty($g['C031']['alcadaVendaSupervisor_portador08']) && strpos($g['C031']['alcadaVendaSupervisor_portador08'], $portador) === false) {
                        $msgSup .= "O portador da forma de pagamento deve ser {$g['C031']['alcadaVendaSupervisor_portador08']}.<br />";
                    } else {
                        if($g['C031']['alcadaVendaSupervisor_portador08'] > 0){
                            $msgSupLiberado .= 'O portador "'.$portador.'" da forma de pagamento deve ser "'.$g['C031']['alcadaVendaSupervisor_portador08'].'".<br />';
                        }
                    }
                } else if (!empty($g['C031']['alcadaVendaSupervisor_valorInicial09']) && !empty($g['C031']['alcadaVendaSupervisor_valorFinal09']) && $valorTotal >= $g['C031']['alcadaVendaSupervisor_valorInicial09'] && $valorTotal <= $g['C031']['alcadaVendaSupervisor_valorFinal09']) {
                    if(!empty($g['C031']['alcadaVendaSupervisor_qtdeParcelas09']) && $parcelas > $g['C031']['alcadaVendaSupervisor_qtdeParcelas09']){
                        $msgSup .= "A forma de pagamento possui mais de {$g['C031']['alcadaVendaSupervisor_qtdeParcelas09']} parcela(s).<br />";
                    } else {
                        if($g['C031']['alcadaVendaSupervisor_qtdeParcelas09'] > 0){
                            $msgSupLiberado .= "A forma de pagamento se enquadra em {$g['C031']['alcadaVendaSupervisor_qtdeParcelas09']} parcela(s).<br />";
                        }
                    }
                    if(!empty($g['C031']['alcadaVendaSupervisor_prazoMedio09']) && $prazoMedio > $g['C031']['alcadaVendaSupervisor_prazoMedio09']){
                        $msgSup .= "O prazo de pagamento está maior que {$g['C031']['alcadaVendaSupervisor_prazoMedio09']} dia(s).<br />";
                    } else {
                        if($g['C031']['alcadaVendaSupervisor_prazoMedio09'] > 0){
                            $msgSupLiberado .= "O prazo de pagamento se enquadra em {$g['C031']['alcadaVendaSupervisor_prazoMedio09']} dia(s).<br />";
                        }
                    }
                    if (!empty($g['C031']['alcadaVendaSupervisor_portador09']) && strpos($g['C031']['alcadaVendaSupervisor_portador09'], $portador) === false) {
                        $msgSup .= "O portador da forma de pagamento deve ser {$g['C031']['alcadaVendaSupervisor_portador09']}.<br />";
                    } else {
                        if($g['C031']['alcadaVendaSupervisor_portador09'] > 0){
                            $msgSupLiberado .= 'O portador "'.$portador.'" da forma de pagamento deve ser "'.$g['C031']['alcadaVendaSupervisor_portador09'].'".<br />';
                        }
                    }
                } else if (!empty($g['C031']['alcadaVendaSupervisor_valorInicial10']) && !empty($g['C031']['alcadaVendaSupervisor_valorFinal10']) && $valorTotal >= $g['C031']['alcadaVendaSupervisor_valorInicial10'] && $valorTotal <= $g['C031']['alcadaVendaSupervisor_valorFinal10']) {
                    if(!empty($g['C031']['alcadaVendaSupervisor_qtdeParcelas10']) && $parcelas > $g['C031']['alcadaVendaSupervisor_qtdeParcelas10']){
                        $msgSup .= "A forma de pagamento possui mais de {$g['C031']['alcadaVendaSupervisor_qtdeParcelas10']} parcela(s).<br />";
                    } else {
                        if($g['C031']['alcadaVendaSupervisor_qtdeParcelas10'] > 0){
                            $msgSupLiberado .= "A forma de pagamento se enquadra em {$g['C031']['alcadaVendaSupervisor_qtdeParcelas10']} parcela(s).<br />";
                        }
                    }
                    if(!empty($g['C031']['alcadaVendaSupervisor_prazoMedio10']) && $prazoMedio > $g['C031']['alcadaVendaSupervisor_prazoMedio10']){
                        $msgSup .= "O prazo de pagamento está maior que {$g['C031']['alcadaVendaSupervisor_prazoMedio10']} dia(s).<br />";
                    } else {
                        if($g['C031']['alcadaVendaSupervisor_prazoMedio10'] > 0){
                            $msgSupLiberado .= "O prazo de pagamento se enquadra em {$g['C031']['alcadaVendaSupervisor_prazoMedio10']} dia(s).<br />";
                        }
                    }
                    if (!empty($g['C031']['alcadaVendaSupervisor_portador10']) && strpos($g['C031']['alcadaVendaSupervisor_portador10'], $portador) === false) {
                        $msgSup .= "O portador da forma de pagamento deve ser {$g['C031']['alcadaVendaSupervisor_portador10']}.<br />";
                    } else {
                        if($g['C031']['alcadaVendaSupervisor_portador10'] > 0){
                            $msgSupLiberado .= 'O portador "'.$portador.'" da forma de pagamento deve ser "'.$g['C031']['alcadaVendaSupervisor_portador10'].'".<br />';
                        }
                    }
                } else {
                    $msgSupLiberado .= "O Valor total da venda é maior que 0.<br />";
                }

                if ($msgSup == '') {
                    $alcada = "S";
                } else {
                    $msg = $msgSup;
                    $msgLiberado = $msgSupLiberado;
                }
            }
        }

        list($aplicarNovaRegraDesconto, $novasRegras) = $this->aplicaRegraParaDesconto($T003_Id, $T005_Id);

        if(!$aplicarNovaRegraDesconto) {
            //Para vendas à vista, utilizamos o percentual de desconto máximo para vendas com prazo médio zero
            if($parcelas==1 and $prazoMedio <= 7){
                $msgRegras .= "DESCONTO FOR ATÉ ".gCorrigeNumero($g['C031']['alcadaVendaSupervisor_descontoMinimoSupervisorAVista'])."%";
            } else {
                $msgRegras .= "DESCONTO FOR ATÉ ".gCorrigeNumero($g['C031']['alcadaVendaSupervisor_descontoMinimoSupervisor'])."%";
            }
            
            //Caso a venda seja à vista, utilizamos a alçada para vendas à vista para verificar o percentual de desconto aplicado
            if($parcelas==1 and $prazoMedio <= 7){
                if ($alcada != 'D' and $Total_Desconto > $g['C031']['alcadaVendaSupervisor_descontoMinimoSupervisorAVista'] and $Total_Desconto <= $g['C031']['alcadaVendaSupervisor_descontoMaximoSupervisorAVista']) {
                    $alcada = "S";
                    $msg .= "O desconto {$Total_Desconto}% maior que {$g['C031']['alcadaVendaSupervisor_descontoMinimoSupervisorAVista']}% até {$g['C031']['alcadaVendaSupervisor_descontoMaximoSupervisorAVista']}%.";
                } else {
                    if($g['C031']['alcadaVendaSupervisor_descontoMinimoSupervisorAVista'] > 0|| $g['C031']['alcadaVendaSupervisor_descontoMaximoSupervisorAVista'] > 0){
                        $msgLiberado .= "O desconto {$Total_Desconto}% não é maior que {$g['C031']['alcadaVendaSupervisor_descontoMinimoSupervisorAVista']}% até {$g['C031']['alcadaVendaSupervisor_descontoMaximoSupervisorAVista']}%.<br />";
                    }
                }

                if ($Total_Desconto > $g['C031']['alcadaVendaSupervisor_descontoMaximoSupervisorAVista']) {
                    $alcada = "D";
                    $msg .= "O desconto {$Total_Desconto}% é maior que {$g['C031']['alcadaVendaSupervisor_descontoMaximoSupervisorAVista']}%.";
                } else {
                    if($g['C031']['alcadaVendaSupervisor_descontoMaximoSupervisorAVista'] > 0){
                        $msgLiberado .= "O desconto {$Total_Desconto}% não é maior que {$g['C031']['alcadaVendaSupervisor_descontoMaximoSupervisorAVista']}%.<br />";
                    }
                }
            } else {
                if ($alcada != 'D' and $Total_Desconto > $g['C031']['alcadaVendaSupervisor_descontoMinimoSupervisor'] and $Total_Desconto <= $g['C031']['alcadaVendaSupervisor_descontoMaximoSupervisor']) {
                    $alcada = "S";
                    $msg .= "O desconto {$Total_Desconto}% maior que {$g['C031']['alcadaVendaSupervisor_descontoMinimoSupervisor']}% até {$g['C031']['alcadaVendaSupervisor_descontoMaximoSupervisor']}%.";
                } else {
                    if($g['C031']['alcadaVendaSupervisor_descontoMinimoSupervisor'] > 0 || $g['C031']['alcadaVendaSupervisor_descontoMaximoSupervisor'] > 0){
                        $msgLiberado .= "O desconto {$Total_Desconto}% não é maior que {$g['C031']['alcadaVendaSupervisor_descontoMinimoSupervisor']}% até {$g['C031']['alcadaVendaSupervisor_descontoMaximoSupervisor']}%.<br />";
                    }
                }

                if ($Total_Desconto > $g['C031']['alcadaVendaSupervisor_descontoMaximoSupervisor']) {
                    $alcada = "D";
                    $msg .= "O desconto {$Total_Desconto}% é maior que {$g['C031']['alcadaVendaSupervisor_descontoMaximoSupervisor']}%.";
                } else {
                    if($g['C031']['alcadaVendaSupervisor_descontoMaximoSupervisor'] > 0){
                        $msgLiberado .= "O desconto {$Total_Desconto}% não é maior que {$g['C031']['alcadaVendaSupervisor_descontoMaximoSupervisor']}%.<br />";
                    }
                }
            }
        } else {
            $msgRegras .= $novasRegras['msgRegras'];
            $msg .= $novasRegras['msg'];
            $msgLiberado .= $novasRegras['msgLiberado'];
            $alcada = !empty($novasRegras['alcada']) ? $novasRegras['alcada'] : $alcada;
        }

        if (!empty($msg)) {
            $msg = "<b><br><font style=\"color:red\">Motivo caiu na liberação (Alçadas): <br />" . $msg . "</b></font>" . $msgRegras;
        }

        if (!empty($msgLiberado)) {
            $msgLiberado = "<b><br><font style=\"color:red\">" . $msgLiberado . "</b></font>" . $msgRegras;
        }

        $retorno = true;
        // Venda já passou pela liberação?
        if ($Flag_Status_Liberacao == "2") {
            // Valor total da venda liberado é diferente do valor total da venda atual?
            if (number_format($Valor_Total_Venda, 2) != number_format($Liberou_Alcada_Valor_Total, 2)) {
                $msg     = "VALOR TOTAL DA VENDA LIBERADO É DIFERENTE DO VALOR TOTAL DA VENDA ATUAL. Valor Liberado:" . number_format($Liberou_Alcada_Valor_Total, 2) . " Valor da Venda: " . number_format($Valor_Total_Venda, 2);
                $retorno = false;
            } else {
                if($Liberou_Alcada_Valor_Total > 0 || $Valor_Total_Venda > 0){
                    $msgLiberado     .= "<br />O valor total da venda liberado é igual ao valor total da venda atual. Valor Liberado:" . number_format($Liberou_Alcada_Valor_Total, 2) . " Valor da Venda: " . number_format($Valor_Total_Venda, 2)."<br />";
                }
            }

            // Valor total do desconto liberado é diferente do total do desconto atual?
            if (number_format($Total_Desconto, 2) > number_format($Liberou_Alcada_Desconto, 2)) {
                $msg     = "Valor total do desconto liberado é menor que o total do desconto atual. Desconto Liberado: " . number_format($Liberou_Alcada_Desconto, 2) . " Desconto na venda:" . number_format($Total_Desconto, 2);
                $retorno = false;
            } else {
                if($Liberou_Alcada_Desconto > 0 || $Total_Desconto > 0){
                    $msgLiberado .= "<br />Valor total do desconto liberado é maior ou igual ao total do desconto atual. Desconto Liberado: " . number_format($Liberou_Alcada_Desconto, 2) . " Desconto na venda:" . number_format($Total_Desconto, 2)."<br />";
                }
            }

            // prazo médio liberado é diferente do prazo médio atual?
            if (number_format($prazoMedio, 2) != number_format($Liberou_Alcada_Prazo_Medio, 2)) {
                $msg     = "O prazo médio liberado difere do prazo médio atual da venda. Prazo médio liberado: " . number_format($Liberou_Alcada_Prazo_Medio, 2) . " Prazo médio atual:" . number_format($prazoMedio, 2);
                $retorno = false;
            } else {
                if($Liberou_Alcada_Prazo_Medio > 0 || $prazoMedio > 0){
                    $msgLiberado .= "<br />O prazo médio liberado é igual ao prazo médio atual da venda. Prazo médio liberado: " . number_format($Liberou_Alcada_Prazo_Medio, 2) . " Prazo médio atual:" . number_format($prazoMedio, 2)."<br />";
                }
            }

            // frete liberado é diferente do frete atual?
            if ($freteRequerAlcada == 'S' && number_format($valorFreteDigitado, 2) != number_format($liberouAlcadaFrete, 2)) {
                $liberouAlcadaFrete = gCorrigeNumero($liberouAlcadaFrete);
                $msg     = "O valor do frete liberado difere do valor do frete atual da venda. Frete liberado: {$liberouAlcadaFrete} Frete atual: {$valorFreteDigitado}";
                $retorno = false;
            } else {
                if($freteRequerAlcada == 'S' && $valorFreteDigitado > 0 || $liberouAlcadaFrete > 0){
                    $liberouAlcadaFrete = gCorrigeNumero($liberouAlcadaFrete);
                    $msgLiberado .= "<br />O frete liberado é igual ao frete médio atual da venda. Frete liberado: {$liberouAlcadaFrete} Frete atual: {$valorFreteDigitado}<br />";
                }
            }

            if (!empty($msg)) {
                $msg = "<b><br><font style=\"color:red\">Motivo caiu para análise (alçadas): <br />" . $msg . "</b></font>" . $msgRegras;
            }
            mysqli_query("UPDATE {$tabela} SET {$tabela}_Motivo_Pedido_Liberacao='{$msg}' WHERE {$tabela}_Id='{$id}'");
            
            if ($retorno === true) {
                return true;
            }
        }

        $msg = '<br />Perfil usuário: '.$perfil. ' - Alçada requerida: '.$alcada . '<br />'.$msg;

        if ($alcada == "S" && $perfil != "D" && $perfil != 'S') {
            mysqli_query("UPDATE {$tabela} SET {$tabela}_Flag_Alcada='{$alcada}',{$tabela}_Motivo_Pedido_Liberacao='{$msg}', {$tabela}_Flag_Status_Liberacao=4 WHERE {$tabela}_Id='{$id}'");
        } else if ($alcada == "D" && $perfil != "D") {
            mysqli_query("UPDATE {$tabela} SET {$tabela}_Flag_Alcada='{$alcada}',{$tabela}_Motivo_Pedido_Liberacao='{$msg}', {$tabela}_Flag_Status_Liberacao=4 WHERE {$tabela}_Id='{$id}'");
        } else {
             // Entra somente se o msg não liberado for não vazio e o Status liberação = 2
            if (!empty($msgLiberado)) {
                if($tabela=='T005'){
                    mysqli_query("UPDATE T005 SET T005_Motivo_Pedido_Nao_Liberacao='{$msgLiberado}' WHERE T005_Id='{$id}'");

                    if($acao=='imprimir'){
                        $msgAcao="Pedido impresso";
                    } else if($acao=='faturar'){
                        $msgAcao="Faturou pedido";
                    } else if($acao=='enviarEmail'){
                        $msgAcao="Envio de e-mail";
                    }
                    // Verifica caso já tenha um registro desses no histório, não gera de novo.
                    $T178 = mysql_query("SELECT * FROM T178 WHERE T178_Descricao='{$msgLiberado}' AND T178_T005_Id='{$id}'");
                    if(mysql_num_rows($T178) <= 0){
                        mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'{$msgAcao}','')");
                        mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'{$msgLiberado}','')");
                    }
                }
            }
            mysqli_query("UPDATE {$tabela} SET {$tabela}_Flag_Alcada='{$alcada}',{$tabela}_Motivo_Pedido_Liberacao='{$msg}', {$tabela}_Flag_Status_Liberacao=0 WHERE {$tabela}_Id='{$id}'");
        }
        return true;
    }

    /* Função trigger de personalização para pular a verificação de alçada*/
    public function ven001TriggerPersonalizadaPularVerificacaoAlcada($T003_Id, $T005_Id)
    {
        /* 
            return true -> pula a verificação da alçada
            return false -> mantém a verificação da alçada
        */

        return false;
    }

    /**
     * ven001atualizarCFOP
     *
     * @param String $T003_Id
     * @return String
     */
    public function ven001atualizarCFOP($T003_Id, $T004_Id = false)
    {
        global $g;
        //Verifica se foi passado um T004 específico
        if($T004_Id !== false){
            $extra = "AND T004_Id = '{$T004_Id}'";
        }

        $sqlT003 = mysql_query("SELECT T004_Id,
                                       T003_D024_Id,
                                       T004_D009_Id,
                                       T003_C004_Id, 
                                       T004_Flag_Destino_Produto,
                                       T003_C004_Id_Estoque,
                                       D148_D018_Id 
                                  FROM T003 
                             LEFT JOIN T004 ON T003_Id = T004_T003_Id 
                             LEFT JOIN D148 ON D148_Id = T003_D148_Id_Entrega
                                 WHERE T003_Id = '{$T003_Id}' {$extra}");

        while ($T003 = mysql_fetch_array($sqlT003)) {
            //$D006_Id           = $this->retornaCFOP($T003['T003_D024_Id'], $T003['T004_D009_Id'], $T003['T003_C004_Id'], $T003['T004_Flag_Destino_Produto'], false, false, false, $T003['T003_C004_Id_Estoque']);
            //$demonstrativoCFOP = $this->retornaCFOP($T003['T003_D024_Id'], $T003['T004_D009_Id'], $T003['T003_C004_Id'], $T003['T004_Flag_Destino_Produto'], NULL, true, false, $T003['T003_C004_Id_Estoque']);
            $contextoCFOP = [
                'ufEnderecoEntrega' => $T003['D148_D018_Id']
            ];            
            $D006_Id           = $this->retornaCFOPContexto($T003['T003_D024_Id'], $T003['T004_D009_Id'], $T003['T003_C004_Id'], $T003['T004_Flag_Destino_Produto'], false, false, false, $T003['T003_C004_Id_Estoque'], false, $contextoCFOP);
            $demonstrativoCFOP = $this->retornaCFOPContexto($T003['T003_D024_Id'], $T003['T004_D009_Id'], $T003['T003_C004_Id'], $T003['T004_Flag_Destino_Produto'], NULL, true, false, $T003['T003_C004_Id_Estoque'], false, $contextoCFOP);
            $demonstrativoCFOP = mysql_real_escape_string($demonstrativoCFOP);
            
            mysql_query("UPDATE T004
                      LEFT JOIN D009 ON D009_Id=T004_D009_Id
                      LEFT JOIN D049 ON D049_Id=D009_D049_Id
                      LEFT JOIN D001 ON D001_Id=D049_D001_Id
                            SET T004_D006_Id = '{$D006_Id}', T004_D006_Id_Info = '{$demonstrativoCFOP}', T004_Flag_Pre_Cadastro=D001_Flag_Pre_Cadastro WHERE T004_Id = '{$T003['T004_Id']}'");

            // Grava demonstartivo na estendida também, clientes que estavam com muitas regras não mostrava completo, campo na estentida aceita mais caracteres
            mysqli_query("UPDATE T004A SET T004A_D006_Id_Info = '{$demonstrativoCFOP}' WHERE T004A_T004_Id = '{$T003['T004_Id']}'");

            //Atualiza as alíquotas
            //O método de atualizar alíquotas foi adpatado para executar em etapas, agora a sua chamada se encontra no form (Lígia Nakazato 26/06/2018)
           // $retorno = $this->atualizarAliquotas($T003_Id, false, $T003['T004_Id']);
        }
        if ($D006_Id > 0) {
            mysql_query("UPDATE T003 SET T003_D006_Id='{$D006_Id}' WHERE T003_Id='{$T003_Id}'");
        }
        return true;
    }

    /**
     * ven001atualizarCFOPManual
     *
     * @param String $T003_Id
     * @param String $D006_Id
     * @return String
     */
    public function ven001atualizarCFOPManual($T003_Id, $D006_Id)
    {
        $T003 = mysql_query("UPDATE T003 SET T003_D006_Id = '{$D006_Id}' WHERE T003_Id = '{$T003_Id}'");
        if (mysql_error($T003)) {
            return "Erro de SQL:  1: " . mysql_error();
        }
        $T004 = mysql_query("UPDATE T004 SET T004_D006_Id = '{$D006_Id}' WHERE T004_T003_Id = '{$T003_Id}'");
        if (mysql_error($T004)) {
            return "Erro de SQL:  1: " . mysql_error();
        }

        // $retorno = $this->atualizarAliquotas($T003_Id);
        // return $retorno;
        return true;
    }

    /**
     * calcularComissao
     * (T004/T006/T008)
     * @param String $acaoId
     * @param String $tabela
     * @return array(percentualComissao, valorComissao)
     */
    public function calcularComissao($acaoId, $tabela)
    {
        //Por padrão o método de calcularComissão não será mais realizado e retornará sempre 0.0 - Lígia Nakazato 04/09/2018
        // switch ($tabela) {
        //     case 'T004':
        //         $sql = mysql_query("SELECT T004_Valor_Total_Preco as valorTotalPreco, T004_Percentual_Desconto as percentualDesconto FROM T004 WHERE T004_Id = '{$acaoId}'");
        //     break;
        //     case 'T006':
        //         $sql = mysql_query("SELECT T006_Valor_Total_Preco as valorTotalPreco, T006_Percentual_Desconto as percentualDesconto FROM T006 WHERE T006_Id = '{$acaoId}'");
        //     break;
        //     case 'T008':
        //         $sql = mysql_query("SELECT T008_Valor_Total_Preco as valorTotalPreco, T008_Percentual_Desconto as percentualDesconto FROM T008 WHERE T008_Id = '{$acaoId}'");
        //     break;
        // }
        // $row = mysql_fetch_array($sql);
        // $percentualDesconto = $row['percentualDesconto'];
        // $valorTotalPreco    = $row['valorTotalPreco'];

        // if ($percentualDesconto <= -7.00) {
        //     $percentualComissao = 5.00;
        // } else if ($percentualDesconto <= -6.42) {
        //     $percentualComissao = 4.75;
        // } else if ($percentualDesconto <= -5.83) {
        //     $percentualComissao = 4.50;
        // } else if ($percentualDesconto <= -5.25) {
        //     $percentualComissao = 4.25;
        // } else if ($percentualDesconto <= -4.67) {
        //     $percentualComissao = 4.00;
        // } else if ($percentualDesconto <= -4.08) {
        //     $percentualComissao = 3.75;
        // } else if ($percentualDesconto <= -3.50) {
        //     $percentualComissao = 3.50;
        // } else if ($percentualDesconto <= -2.92) {
        //     $percentualComissao = 3.25;
        // } else if ($percentualDesconto <= -2.33) {
        //     $percentualComissao = 3.00;
        // } else if ($percentualDesconto <= -1.75) {
        //     $percentualComissao = 2.75;
        // } else if ($percentualDesconto <= -1.17) {
        //     $percentualComissao = 2.50;
        // } else if ($percentualDesconto <= -0.58) {
        //     $percentualComissao = 2.25;
        // } else if ($percentualDesconto <= -0.00) {
        //     $percentualComissao = 2.00;
        // } else if ($percentualDesconto <= 0.58) {
        //     $percentualComissao = 1.75;
        // } else if ($percentualDesconto <= 1.17) {
        //     $percentualComissao = 1.50;
        // } else if ($percentualDesconto <= 1.75) {
        //     $percentualComissao = 1.25;
        // } else if ($percentualDesconto <= 2.33) {
        //     $percentualComissao = 1.00;
        // } else if ($percentualDesconto <= 2.92) {
        //     $percentualComissao = 0.75;
        // } else if ($percentualDesconto <= 3.50) {
        //     $percentualComissao = 0.50;
        // } else {
        //     $percentualComissao = 0.50;
        // }

        // if ($percentualComissao > 5.00) {
        //     $percentualComissao = 5.00;
        // } elseif ($percentualComissao < 0.50) {
        //     $percentualComissao = 0.50;
        // } 
        
        // $valorComissao                = $valorTotalPreco * ($percentualComissao / 100);
        // $return                       = array();
        // $return['percentualComissao'] = $percentualComissao;
        // $return['valorComissao']      = $valorComissao;
        // return $return;
        $return                       = array();
        $return['percentualComissao'] = 0;
        $return['valorComissao']      = 0;
        return $return;
    }
    
    /**
     * calcularComissaoTotal
     *
     * @param String $acaoId_Item
     * @param String $tabela
     * @return array(percentualComissao, valorComissao)
     */    
    public function calcularComissaoTotal($acaoId, $tabela)
    {
        /* VARIAVEIS DISPONIVEIS PARA CALCULO DE COMISSÃO 
        * percentualComissaoRepresentante (Representante)
        * percentualComissaoExterno (Vendedor Externo)
        * percentualComissaoUsuario (Usuario)
        */
        //Por padrão o método de calcularComissãoTotal não será mais realizado e retornará sempre 0.0 - Lígia Nakazato 04/09/2018
        // switch ($tabela) {
        //     case 'T003':
        //         $sql = mysql_query("SELECT SUM(T004_Valor_Comissao) as valorComissao, (SUM(T004_Valor_Comissao)/T003_Valor_Total_Produtos)*100 as percentualComissao, 
        //                                    T003_Valor_Total_Produtos as valorTotalProdutos, 
        //                                    T003_Percentual_Comissao_Representante as percentualComissaoRepresentante,
        //                                    T003_Percentual_Comissao_Externo as percentualComissaoExterno,
        //                                    T003_Percentual_Comissao_Usuario as percentualComissaoUsuario,
        //                                    T003_Percentual_Comissao_Manual  as percentualComissaoManual
        //                                 FROM T004 LEFT JOIN T003 ON T003_Id=T004_T003_Id WHERE T004_T003_Id = '{$acaoId}'");
        //     break;
        //     case 'T005':
        //         $sql = mysql_query("SELECT SUM(T006_Valor_Comissao) as valorComissao, (SUM(T006_Valor_Comissao)/T005_Valor_Total_Produtos)*100 as percentualComissao, 
        //                                    T005_Valor_Total_Produtos as valorTotalProdutos,
        //                                    T005_Percentual_Comissao_Representante as percentualComissaoRepresentante,
        //                                    T005_Percentual_Comissao_Externo as percentualComissaoExterno,
        //                                    T005_Percentual_Comissao_Usuario as percentualComissaoUsuario,
        //                                    T005_Percentual_Comissao_Manual  as  percentualComissaoManual
        //                                 FROM T006 LEFT JOIN T005 ON T005_Id=T006_T005_Id WHERE T006_T005_Id = '{$acaoId}'");
        //     break;
        //     case 'T007':
        //         $sql = mysql_query("SELECT IF(T007_Numero_Protocolo_Nfe != '', (T007_Valor_Total_Produtos*(T007_Percentual_Comissao/100)), SUM(T008_Valor_Comissao)) as valorComissao, 
        //                                   IF(T007_Numero_Protocolo_Nfe != '', T007_Percentual_Comissao, (SUM(T008_Valor_Comissao)/T007_Valor_Total_Produtos)*100) as percentualComissao, 
        //                                   T007_Valor_Total_Produtos as valorTotalProdutos,
        //                                   T007_Percentual_Comissao_Representante as percentualComissaoRepresentante,
        //                                   T007_Percentual_Comissao_Externo as percentualComissaoExterno,
        //                                   T007_Percentual_Comissao_Usuario as percentualComissaoUsuario,
        //                                   T007_Percentual_Comissao_Manual  as  percentualComissaoManual
        //                                 FROM T008 LEFT JOIN T007 ON T007_Id=T008_T007_Id WHERE T008_T007_Id = '{$acaoId}'");
        //     break;
        //     case 'T002':
        //         $sql = mysql_query("SELECT T002_Valor_Duplicata as valorTotalProdutos,
        //                                   T002_Percentual_Comissao_Representante as percentualComissaoRepresentante,
        //                                   T002_Percentual_Comissao_Externo as percentualComissaoExterno,
        //                                   T002_Percentual_Comissao_Usuario as percentualComissaoUsuario
        //                                 FROM T002 WHERE T002_Id = '{$acaoId}'");
        //     break;
        // }
        // $row = mysql_fetch_assoc($sql);
        // if ($row['percentualComissaoManual']>0) {
        //     $row['percentualComissao'] = $row['percentualComissaoManual'];
        // }

        // $retorno                                    = array();
        // $retorno['percentualComissao']              = $row['percentualComissao'];
        // $retorno['valorComissao']                   = $row['valorComissao'];
        // $retorno['percentualComissaoRepresentante'] = $row['percentualComissaoRepresentante'];
        // $retorno['valorComissaoRepresentante']      = $row['valorTotalProdutos'] * ($row['percentualComissaoRepresentante']/100);
        // $retorno['percentualComissaoExterno']       = $row['percentualComissaoExterno'];
        // $retorno['valorComissaoExterno']            = $row['valorTotalProdutos'] * ($row['percentualComissaoExterno']/100);
        // $retorno['percentualComissaoUsuario']       = $row['percentualComissaoUsuario'];
        // $retorno['valorComissaoUsuario']            = $row['valorTotalProdutos'] * ($row['percentualComissaoUsuario']/100);
        // $retorno['percentualComissaoSupervisor']    = $row['percentualComissaoSupervisor']; ** apenas na tabela estendida T002A (CR)
        // $retorno['valorComissaoSupervisor']         = $row['valorTotalProdutos'] * ($row['percentualComissaoSupervisor']/100);  ** apenas na tabela estendida T002A (CR)
        // return $retorno;
        $retorno                                    = array();
        $retorno['percentualComissao']              = 0;
        $retorno['valorComissao']                   = 0;
        $retorno['percentualComissaoRepresentante'] = 0;
        $retorno['valorComissaoRepresentante']      = 0;
        $retorno['percentualComissaoExterno']       = 0;
        $retorno['valorComissaoExterno']            = 0;
        $retorno['percentualComissaoUsuario']       = 0;
        $retorno['valorComissaoUsuario']            = 0;
        $retorno['percentualComissaoSupervisor']    = 0;
        $retorno['valorComissaoSupervisor']         = 0;
        return $retorno;
    }

    /**
     * CRM001IncluirAgendaGeral
     *
     * @param Form $form
     * @param String $extra
     * @return String/bool
     */    
    public function CRM001IncluirAgendaGeral($form, $extra)
    {
        global $g;
        
        $extra = base64_decode(urldecode($extra));
        
        $D085_C007_Id_Inclusao      = $form->campoValorEnviado('D085_C007_Id_Inclusao');
        $D085_Data                  = gCorrigeDataInverte($form->campoValorEnviado('D085_Data'));
        $D085_Data_Retorno          = gCorrigeDataInverte($form->campoValorEnviado('D085_Data_Retorno'));
        $D085_Data_Previsao_Retorno = gCorrigeDataInverte($form->campoValorEnviado('D085_Data_Previsao_Retorno'));
        $D085_Mensagem              = $form->campoValorEnviado('D085_Mensagem');
        $D085_Flag_Manual           = $form->campoValorEnviado('D085_Flag_Manual');
        $D085_C007_Id               = $form->campoValorEnviado('D085_C007_Id');
        
        $extra = gInsertExtraWhere($extra, "D024_Flag_Enviar_Agenda='S'");
        
        $D024 = <<<EOT
        SELECT * FROM D024 {$extra}
EOT;
        $D024 = mysql_query($D024);
        if (mysql_error($D024)) {
            return "Erro de SQL:  1: " . mysql_error();
        }
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        while ($mD024 = mysql_fetch_array($D024)) {
            
            mysql_query("
                    INSERT INTO D085 (
                            D085_D024_Id,
                            D085_C007_Id,
                            D085_Data,
                            D085_Mensagem,
                            D085_Flag_Manual,
                            D085_Data_Previsao_Retorno,
                            D085_C007_Id_Inclusao
                        ) VALUES (
                            '{$mD024['D024_Id']}',
                            '{$D085_C007_Id}',
                            '{$D085_Data}',
                            '{$D085_Mensagem}',
                            '{$D085_Flag_Manual}',
                            '{$D085_Data_Previsao_Retorno}',
                            '{$D085_C007_Id_Inclusao}'
                        );
                ");
            $erro = mysql_error();
            if ($erro) {
                return "Erro: " . $erro;
                mysql_query("ROLLBACK");
            }
            
        }
        mysql_query("COMMIT");
        return true;
    }
    
    /**
     * duplicar_ven001FormProdutos
     *
     * @param String $divId
     * @param String $divIdRoot
     * @param String $tabela
     * @param String $T003_Id_Antigo
     * @return String
     */     
    public function duplicar_ven001FormProdutos($divId, $divIdRoot, $tabela, $T003_Id_Antigo)
    {
        global $g;

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        // Selecionando o orçamento
        $T003  = mysql_query("SELECT * FROM T003 WHERE T003_Id='{$T003_Id_Antigo}'");
        $mT003 = mysql_fetch_assoc($T003);
        
        if (mysql_num_rows($T003) == 0) {           
            $retorno = <<<EOT
                var \$buttons = {
                    "Fechar": function() { $(this).dialog("destroy").remove(); }
                };
                dialogConfirm("Erro", "Orçamento não encontrado", \$buttons);
EOT;
            return $retorno;
        }
        
        $campos  = "";
        $valores = "";
        foreach ($mT003 as $key => $value) {
            if ($key == 'T003_Id') {
                continue;
            } elseif ($key == 'T003_Flag_Perdido') {
                continue;
            } elseif ($key == 'T003_Observacao_Orcamento_Perdido') {
                continue;
            } elseif($key == 'T003_Data_Emissao'){
                $campos.=$key.", ";
                $valores.= "curdate(),";            
            } elseif($key == 'T003_Flag_Status_Orcamento'){
                $campos.=$key.", ";
                $valores.= "'P',";            
            }  else {
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string($value) . "',";
            }
        }
        $campos  = trim($campos, ', ');
        $valores = trim($valores, ',');
        $insT003 = "INSERT INTO T003 ({$campos}) VALUES ({$valores})";
        $resT003 = mysql_query($insT003);
        if (!$resT003) {
            mysql_query("ROLLBACK");
            return "Erro 1: " . mysql_error();
        }
        $T003_Id_Novo = $g['mysqlLastId'];

        $T003A  = mysql_query("SELECT * FROM T003A WHERE T003A_T003_Id='{$T003_Id_Antigo}'");
        $mT003A = mysql_fetch_assoc($T003A);

        $campos  = "";
        $valores = "";
        foreach ($mT003A as $key => $value) {
            if ($key == 'T003A_Id') {
                continue;
            } elseif ($key == 'T003A_T003_Id') {
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string($T003_Id_Novo) . "',";
            } elseif ($key == 'T003A_Hora_Inclusao') {
                $campos .= $key . ", ";
                $valores .= "'" . date("H:i:s") . "',";                
            } elseif ($key == 'T003A_T003_Id_Pai') {
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string($T003_Id_Novo) . "',";             
            } elseif ($key == 'T003A_Status_Revisao') {
                continue;              
            } else {
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string($value) . "',";
            }
        }
        $campos  = trim($campos, ', ');
        $valores = trim($valores, ',');
        $insT003A = "INSERT INTO T003A ({$campos}) VALUES ({$valores})";
        $resT003A = mysql_query($insT003A);
        if (!$resT003A) {
            mysql_query("ROLLBACK");
            return "Erro 1: " . mysql_error();
        }
        
        // Selecionando todos os itens do orçamento
        $T004 = mysql_query("SELECT * FROM T004 WHERE T004_T003_Id='{$T003_Id_Antigo}'");
        
        while ($mT004 = mysql_fetch_assoc($T004)) {
            $campos  = "";
            $valores = "";
            foreach ($mT004 as $key => $value) {
                if ($key == 'T004_Id') {
                    continue;
                } elseif ($key == 'T004_Flag_Cancelado' || $key == 'T004_T219_Id' || $key == 'T004_Valor_Preco_Cotacao' || $key == 'T004_Demonstrativo_Preco_Cotacao') {
                    continue;
                } elseif ($key == "T004_T003_Id") {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($T003_Id_Novo) . "',";
                } else {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($value) . "',";
                }
            }
            $campos  = trim($campos, ', ');
            $valores = trim($valores, ',');
            $insT004 = "INSERT INTO T004 ({$campos}) VALUES ({$valores})";
            $resT004 = mysql_query($insT004);
            if (!$resT004) {
                mysql_query("ROLLBACK");
                return "Erro 2: " . mysql_error();
            }

            $T004_Id_Novo = $g['mysqlLastId'];

            //Necessário excluir porque na T004.php cria um registro no insertAfter, porém não tem os dados reais
            mysql_query("DELETE FROM T004A WHERE T004A_T004_Id = '{$T004_Id_Novo}' ");
            
            $T004A = mysql_query("SELECT * FROM T004A WHERE T004A_T004_Id='{$mT004['T004_Id']}'");
            $mT004A = mysql_fetch_assoc($T004A);
            $campos  = "";
            $valores = "";
            foreach ($mT004A as $key => $value) {

                if ($key == 'T004A_Id') {
                    continue;
                } elseif ($key == "T004A_T004_Id") {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($T004_Id_Novo) . "',";
                } elseif ($key == "T004A_Flag_Solicitar_Prazo_Compras") {
                    continue;
                }elseif ($key == "T004A_JSON") {
                    continue;
                } elseif ($key == "T004A_Flag_Notificacao") {
                    continue;
                } else {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($value) . "',";
                }
            }

            $campos  = trim($campos, ', ');
            $valores = trim($valores, ',');
            $insT004A = "INSERT INTO T004A ({$campos}) VALUES ({$valores})";
            $sql = mysqli_query($insT004A);
            $erro = mysql_error();
            
            if (!$sql) {
                mysql_query("ROLLBACK");
                return "Erro 6: $erro" ;
            }
        }
        
        // Ajusta para o novo preço
        if($g['C031']['duplicarPedidoOrcamentoAtualizaPrecoTabela'] == 'S'){
            $this->atualizarPrecoTabela($T003_Id_Novo);
            $T004 = mysql_query("SELECT T004_Id,
                                        T004_Valor_Preco_Original
                                   FROM T004
                                  WHERE T004_T003_Id = '{$T003_Id_Novo}'");

            while($mT004 = mysql_fetch_array($T004)){
                mysql_query("UPDATE T004 SET T004_Valor_Preco_Unitario = T004_Valor_Preco_Original, T004_Percentual_Desconto = 0 WHERE T004_Id = '{$mT004['T004_Id']}'");
            }
        }
        
        $T003_Id = $T003_Id_Novo;
        mysql_query("COMMIT");
        $novoId = uniqid();
        return "$('#{$divId}').closest('.ui-dialog-content').dialog('close'); abrirJanela(false, '{$divIdRoot}', '{$divId}' ,'{$novoId}', '', 'Editar Orçamento', '/ven/ven001/content/ven001ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent('{$T003_Id}') + '&tabela=T003', [990,605]);";
    }

    /**
     * verificaDadosOrcamento
     *
     * @param String $T003_Id
     * @return String/Bool
     */ 
    public function verificaDadosOrcamento($T003_Id)
    {
        global $g;
        $retorno = '';
        $origensValidas = array(0,1,2,3,4,5,6,7,8);

        $sql = mysql_query("SELECT T004_Codigo_Produto, IFNULL(T004_D006_Id,0) AS T004_D006_Id, IFNULL(T004_D005_Id,0) AS T004_D005_Id, T004_Flag_Origem_Produto FROM T004 WHERE T004_T003_Id = '{$T003_Id}'");
        while($row = mysql_fetch_array($sql)){
            $temp = '';
            if($row['T004_D006_Id'] <= 0){
                $temp .= "<b>".$row['T004_Codigo_Produto'] . "</b> está com a CFOP em branco";
            }
            if($row['T004_D005_Id'] <= 0){
                $temp .= ($temp == '') ? "<b>".$row['T004_Codigo_Produto'] . "</b> está com a NCM em branco" : ", com a NCM em branco";
            }
            if(!is_numeric($row['T004_Flag_Origem_Produto']) || $row['T004_Flag_Origem_Produto'] == '' || !in_array($row['T004_Flag_Origem_Produto'], $origensValidas)){
                $temp .= ($temp == '') ? "<b>".$row['T004_Codigo_Produto'] . "</b> a origem da mercadoria não é valida " : ", e a origem da mercadoria não é valida ";
                $temp .= "(origens válidas ".implode(", ", $origensValidas).")";
            }
            $temp .= ($temp != '') ? '.<br>' : '';
            if($temp != ''){
                $retorno .= $temp;
            }
        }

        if($retorno != ''){
            return "Verifique os itens abaixo: <br><br>" . $retorno;
        }
        return true;
    }

    /**
     * processarNumeroItemOrcamento
     *
     * @param String $T004_Id
     * @return Bool
     */
    public function processarNumeroItemOrcamento($T004_Id, $novoNumeroItem)
    {
        global $g;
        $ultimoItem = 0;
        $contadorItem = 1;
        $sql = mysql_query("SELECT T004_Item, T004_Id, T004_Codigo_Substituto FROM T004 WHERE T004_T003_Id = (SELECT T004_T003_Id FROM T004 WHERE T004_Id = '{$T004_Id}') AND T004_Id != '{$T004_Id}' ORDER BY T004_Item");
        while($row = mysql_fetch_array($sql)){
            if($contadorItem == $novoNumeroItem){ $contadorItem++; }
            mysqli_query("UPDATE T004 SET T004_Item = '{$contadorItem}' WHERE T004_Id = '{$row['T004_Id']}'");
            $contadorItem++;
        }
        return true;
    }

    /**
     * incluirSubItensOrcamento
     *
     * @param String $T003_Id
     * @param String $D009_Id
     * @param String $quantidade
     * @return Bool
     */
    public function incluirSubItensOrcamento($T003_Id, $D009_Id, $quantidade)
    {
        global $g;
        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();
        require_once('bibliotecas/classes/GFormWrap.php');

        $sqlT003 = mysql_query("SELECT * FROM T003 WHERE T003_Id = '{$T003_Id}'");
        $rowT003 = mysql_fetch_array($sqlT003);

        $sqlD042 = mysql_query("SELECT * FROM D042
                                    LEFT JOIN D049 ON D049_Id=D042_D049_Id_Itens
                                    LEFT JOIN D082 ON D049_D082_Id=D082_Id
                                    LEFT JOIN D001 ON D001_Id=D049_D001_Id
                                    LEFT JOIN D009 ON D009_D049_Id=D042_D049_Id_Itens
                                        WHERE D042_D049_Id = (SELECT D009_D049_Id FROM D009 WHERE D009_Id = '{$D009_Id}') and D009_C004_Id = '{$g['empresaAtual']}'");
        while($rowD042 = mysql_fetch_array($sqlD042)){
            $quantidadeTotal = $rowD042['D042_Quantidade']*$quantidade;
            $D006_Id   = $this->retornaCFOP($rowT003['T003_D024_Id'], $row['D009_Id'], $rowT003['T003_C004_Id'], $rowT003['T003_Flag_Destino_Produto'], false, false, false, $rowT003['T003_C004_Id_Estoque']);
            $aliquotas = $this->retornaAliquotas($rowD042['D009_Id'], $T003_Id, false, false, false, false, false, $D006_Id);
            $preco     = $CAD002->retornaPrecoVenda($rowD042['D009_Id'], $rowT003['T003_Flag_Lista_Preco'], $rowT003['T003_D024_Id'], $rowT003['T003_Flag_Acp'], $D006_Id, $aliquotas['Aliquota_ICMS'], 0, 0, $rowT003['T003_Casas_Decimais_Preco'], $aliquotas['Aliquota_PIS'], $aliquotas['Aliquota_COFINS'], $aliquotas['Aliquota_IPI'], $aliquotas['ST_VA'], $aliquotas['Percentual_Reducao_ICMS_ST'], $aliquotas['ST_ICMS_Interno'], $aliquotas['Percentual_Reducao_ICMS'], $aliquotas['ICMS_Inter_Estadual'], $aliquotas['Aliquota_IRPJ'], $aliquotas['Aliquota_CSLL']);

            if($D006_Id <= 0){
                $D006_Id = $rowT003['T003_D006_Id'];
            }

            $form = new GFormWrap(array(
                'T004_D009_Id' => $rowD042['D009_Id'],
                'T004_Quantidade' => gCorrigeNumero($quantidadeTotal,3),
                'T004_T003_Id' => $T003_Id,
                'T004_Valor_Preco_Sem_Desconto_Unitario' => $preco[0],
                'T004_D005_Id' => $rowD042['D001_D005_Id'],
                'T004_D037_Id' => $rowD042['D001_D037_Id'],
                'T004_Descricao_Produto' => mysql_real_escape_string($rowD042['D001_Descricao_Produto']),
                'T004_Codigo_Produto' => $rowD042['D001_Codigo_Produto'],
                'T004_D006_Id' => $D006_Id,
                'T004_Aliquota_IPI' => $aliquotas['Aliquota_IPI'],
                'T004_Flag_Destino_Produto' => $rowT003['T003_Flag_Destino_Produto']

            ));
            $retorno = $this->incluirProduto($form);
        }

        return true;
    }

    /**
     * incluirProdutosPesquisaGridOrcamento
     *
     * @param String $T003_Id
     * @return Bool
     */
    public function incluirProdutosPesquisaGridOrcamento($T003_Id){
        global $g;
        require_once('bibliotecas/classes/GFormWrap.php');
        $sqlT003 = mysql_query("SELECT * FROM T003 LEFT JOIN D024 ON D024_Id=T003_D024_Id WHERE T003_Id = '{$T003_Id}'");
        $rowT003 = mysql_fetch_array($sqlT003);

        if(!empty($rowT003['D024_Flag_Lista_Preco'])){
            $campoListaPreco = "D009_Preco_{$rowT003['D024_Flag_Lista_Preco']}";
        } else {
            $campoListaPreco = "D009_Preco_1";
        }

        $sqlT198 = mysql_query("SELECT * 
                                  FROM T198 
                                 WHERE T198_D024_Id = '{$rowT003['T003_D024_Id']}' 
                                   AND T198_C007_Id = '{$g['usuarioAtual']}' 
                                   AND T198_Quantidade > 0");

        while($rowT198 = mysql_fetch_array($sqlT198)){

            if($rowT198['T198_Quantidade'] <= 0) { 
                continue; 
            }

            $sqlD001 = mysql_query("SELECT * 
                                      FROM D001 
                                 LEFT JOIN D049 ON D001_Id=D049_D001_Id 
                                 LEFT JOIN D009 ON D049_Id=D009_D049_Id 
                                     WHERE D009_Id = '{$rowT198['T198_D009_Id']}'");

            $rowD001 = mysql_fetch_array($sqlD001);

            $D006_Id = $this->retornaCFOP($rowT003['T003_D024_Id'], $rowT198['T198_D009_Id'], $rowT003['T003_C004_Id'], $rowT003['T003_Flag_Destino_Produto'], false, false, false, $rowT003['T003_C004_Id_Estoque']);

            $precoUnitario = 0;
            if($rowT198['T198_Valor_Preco_Sem_Desconto_Unitario'] > 0) {
                $precoUnitario = gCorrigeNumero($rowT198['T198_Valor_Preco_Sem_Desconto_Unitario']);
            } else if($rowD001[$campoListaPreco] > 0) {
                $precoUnitario = gCorrigeNumero($rowD001[$campoListaPreco]);
            }

            $D160 = mysql_query("SELECT D160_Valor_Preco 
                                   FROM D160 
                                  WHERE D160_D024_Id = '{$rowT003['T003_D024_Id']}'  
                                    AND D160_D009_Id = '{$rowT198['T198_D009_Id']}'");
                                    
            if(mysql_num_rows($D160) == 1){
                $mD160 = mysql_fetch_array($D160);
                if($mD160['D160_Valor_Preco'] > 0){
                    $precoUnitario = gCorrigeNumero($mD160['D160_Valor_Preco']);
                }
            }
            
            $T066_Id = $this->locacaoItemValidadeOrcamento($rowT198['T198_D009_Id']);

            $form = new GFormWrap(array(
                'T004_D009_Id'                           => $rowT198['T198_D009_Id'],
                'T004_Quantidade'                        => gCorrigeNumero($rowT198['T198_Quantidade']),
                'T004A_Quantidade_Conversao'             => gCorrigeNumero($rowT198['T198_Quantidade_Conversao']),
                'T004_Valor_Preco_Sem_Desconto_Unitario' => $precoUnitario,
                'T004_D037_Id'                           => $rowD001['D001_D037_Id'],
                'T004_Descricao_Produto'                 => $rowD001['D001_Descricao_Produto'],
                'T004_Codigo_Produto'                    => $rowD001['D001_Codigo_Produto'],
                'T004_D006_Id'                           => $D006_Id,
                'T004_T003_Id'                           => $T003_Id,
                'T004_D005_Id'                           => $rowD001['D001_D005_Id'],
                'T004_Flag_Destino_Produto'              => $rowT003['T003_Flag_Destino_Produto'],
                'T004A_T066_Id'                          => $T066_Id,
            ));

            $this->incluirProduto($form);
        }

        mysql_query("DELETE FROM T198 WHERE (T198_D024_Id = '{$rowT003['T003_D024_Id']}' AND T198_C007_Id = '{$g['usuarioAtual']}') OR T198_D009_Id IS NULL");

        return true;
    }

    /**
     * incluirProdutosPesquisaGridOrcEtapas
     *
     * @param Array $dados - Array com os ids da T198 e T003
     * @param Array|Bool $cacheAnterior - Array com dados da execução anterior
     * @return Bool
     */
    public function incluirProdutosPesquisaGridOrcEtapas($dados, $cacheAnterior = false) {
        global $g;
        require_once('bibliotecas/classes/GFormWrap.php');

        // Pegando os dados para a inclusao
        $sqlT003 = "SELECT T003_D024_Id, D024_Flag_Lista_Preco as flagPreco, T003_C004_Id, T003_Flag_Destino_Produto, T003_C004_Id_Estoque
                    FROM T003 
                        LEFT JOIN D024 ON D024_Id = T003_D024_Id 
                    WHERE T003_Id = '{$dados['T003_Id']}'";
        $T003 = mysql_fetch_assoc(mysqli_query($sqlT003));

        $sqlT198 = "SELECT T198_Quantidade, T198_D009_Id, T198_Valor_Preco_Sem_Desconto_Unitario, T198_Quantidade_Conversao
                    FROM T198 
                    WHERE T198_Id = '{$dados['T198_Id']}'";
        $T198 = mysql_fetch_assoc(mysqli_query($sqlT198));

        $sqlD001 = "SELECT 
                        D009_Preco_1,
                        D009_Preco_2,
                        D009_Preco_3,
                        D009_Preco_4,
                        D009A_Preco_5,
                        D001_D037_Id,
                        D001_Descricao_Produto,
                        D001_Codigo_Produto,
                        D001_D005_Id
                    FROM D001 
                        LEFT JOIN D049 ON D001_Id = D049_D001_Id
                        LEFT JOIN D009 ON D049_Id = D009_D049_Id
                        LEFT JOIN D001A ON D001A_Id = D001A_D001_Id 
                        LEFT JOIN D009A ON D009A_Id = D009A_D009_Id
                    WHERE D009_Id = '{$T198['T198_D009_Id']}'";

        $D001 = mysql_fetch_assoc(mysqli_query($sqlD001));

        // Validações e ajustes nos dados
        if($T198['T198_Quantidade'] <= 0) { 
            mysql_query("DELETE FROM T198 WHERE T198_Id = '{$dados['T198_Id']}'");
            return true; 
        }
        
        if(!empty($T003['flagPreco'])) {
            $campoListaPreco = ($T003['flagPreco'] > 5) ? "D009A_Preco_{$T003['flagPreco']}" : "D009_Preco_{$T003['flagPreco']}";
        } else {
            $campoListaPreco = "D009_Preco_1";
        }

        $precoUnitario = 0;
        if($T198['T198_Valor_Preco_Sem_Desconto_Unitario'] > 0) {
            $precoUnitario = gCorrigeNumero($T198['T198_Valor_Preco_Sem_Desconto_Unitario']);

        } else if($D001[$campoListaPreco] > 0) {
            $precoUnitario = gCorrigeNumero($D001[$campoListaPreco]);
        }

        $sqlD160 = mysqli_query("SELECT D160_Valor_Preco FROM D160 WHERE D160_D024_Id = '{$T003['T003_D024_Id']}' AND D160_D009_Id = '{$T198['T198_D009_Id']}'");
                                
        if (mysql_num_rows($sqlD160) == 1) {
            $D160 = mysql_fetch_assoc($sqlD160);
            if($D160['D160_Valor_Preco'] > 0) {
                $precoUnitario = gCorrigeNumero($D160['D160_Valor_Preco']);
            }
        }
            
        $idCFOP = $this->retornaCFOP($T003['T003_D024_Id'], $T198['T198_D009_Id'], $T003['T003_C004_Id'], $T003['T003_Flag_Destino_Produto'], false, false, false, $T003['T003_C004_Id_Estoque']);
        
        $idLoc = $this->locacaoItemValidadeOrcamento($T198['T198_D009_Id']);

        // Monta o form para a inclusão
        $form = new GFormWrap(array(
            'T004_D009_Id'                           => $T198['T198_D009_Id'],
            'T004_Quantidade'                        => gCorrigeNumero($T198['T198_Quantidade']),
            'T004A_Quantidade_Conversao'             => gCorrigeNumero($T198['T198_Quantidade_Conversao']),
            'T004_Valor_Preco_Sem_Desconto_Unitario' => $precoUnitario,
            'T004_D037_Id'                           => $D001['D001_D037_Id'],
            'T004_Descricao_Produto'                 => $D001['D001_Descricao_Produto'],
            'T004_Codigo_Produto'                    => $D001['D001_Codigo_Produto'],
            'T004_D006_Id'                           => $idCFOP,
            'T004_T003_Id'                           => $dados['T003_Id'],
            'T004_D005_Id'                           => $D001['D001_D005_Id'],
            'T004_Flag_Destino_Produto'              => $T003['T003_Flag_Destino_Produto'],
            'T004A_T066_Id'                          => $idLoc,
        ));

        $this->incluirProduto($form);

        mysql_query("DELETE FROM T198 WHERE T198_Id = '{$dados['T198_Id']}'");

        return true;
    }

    /**
     * retornaCFOPTabelaFornecedor
     *
     * @param String $D024_Id
     * @param String $D069_Id
     * @param String $C004_Id
     * @param String $Destino
     * @param String $Origem
     * @param Bool $retornaLog
     * @return String/bool
     */
    public function retornaCFOPTabelaFornecedor($D024_Id, $D069_Id = null, $C004_Id = null, $Destino = null, $Origem = null, $retornaLog = false, $OrigemPedido = null, $C004_Id_Estoque=null)
    {
        global $g;
        $C004_Id = ($C004_Id == null) ? $g['empresaAtual'] : $C004_Id;

        $retorno = mysql_query("SELECT
                                D024_Flag_Contribuinte,
                                D024_Flag_Pessoa_Fisica_Juridica,
                                D024_Flag_Revenda_Consumidor,
                                D024_D006_Id,
                                D018_UF,
                                D018_Id,
                                D053_Flag_ST,
                                D069_Peso_Unitario,
                                D069_Flag_Origem_Produto,
                                D149_Flag_ST,
                                D149_UF_ST,
                                D005_Id,
                                D005_Classificacao_Fiscal,
                                C004_Codigo_Regime_Tributario,
                                C004_D018_Id,
                                C004_Id,
                                D024_Codigo_Regime_Tributario
                            FROM D024
                                LEFT JOIN C004 ON C004_Id={$C004_Id}
                                LEFT JOIN D018 ON D018_Id=D024_D018_Id
                                LEFT JOIN D053 ON D053_D018_Id=D018_Id
                                LEFT JOIN D069 ON D069_Id={$D069_Id}
                                LEFT JOIN D005 ON D005_Id=D069_D005_Id
                                LEFT JOIN D149 ON D005_Id=D149_D005_Id AND D149_C004_Id={$C004_Id}
                            WHERE
                                D024_Id={$D024_Id} AND D053_C004_Id={$C004_Id}");
        $registro = mysql_fetch_array($retorno);

        if ($Origem != NULL) {
            $registro['D069_Flag_Origem_Produto'] = $Origem;
        }

        if ($OrigemPedido != null && $OrigemPedido > 0) {
            if($C004_Id != $OrigemPedido){
                $D137_Origem_Pedido = 'E';
            } else {
                $D137_Origem_Pedido = 'I';
            }
        } else {
            $D137_Origem_Pedido = '';
        }

        $D137_C004_Id = $registro['C004_Id'];

        $D137_C004_Codigo_Regime_Tributario = $registro['C004_Codigo_Regime_Tributario'];
        $D137_D024_Codigo_Regime_Tributario = $registro['D024_Codigo_Regime_Tributario'];

        $D137_Flag_Revenda_Consumidor = $registro['D024_Flag_Revenda_Consumidor'];

        // o valor do tipo "não contribuinte" que no cad cliente é 0 foi substituido por 9
        $D137_D024_Flag_Contribuinte = $registro['D024_Flag_Contribuinte'];
        if ($D137_D024_Flag_Contribuinte == 0){
            $D137_D024_Flag_Contribuinte = 9;
        }

        if ($registro['D018_Id'] == $registro['C004_D018_Id']){
            $D137_Dentro_Fora_Estado = 'D';
        } else {
            $D137_Dentro_Fora_Estado = 'F';
        }

        $D137_UF_Tem_ST = $registro['D053_Flag_ST'];

        if ($registro['D149_Flag_ST']) {
            $D137_NCM_Tem_ST = $registro['D149_Flag_ST'];
        } else {
            $D137_NCM_Tem_ST = "";
        }

        $D137_D005_Id = $registro['D005_Id'];

        if ($registro['D024_Flag_Pessoa_Fisica_Juridica'] == "F"){
            $D137_Pessoa_Fisica_Juridica = "PF";
        } elseif ($registro['D024_Flag_Pessoa_Fisica_Juridica'] == "J") {
            $D137_Pessoa_Fisica_Juridica = "PJ";
        } elseif ($registro['D024_Flag_Pessoa_Fisica_Juridica'] == "I") {
            $D137_Pessoa_Fisica_Juridica = "PI";
        }

        $D137_Produzido_Adquirido = "";
/*
        if ($g['C031']['multimarcas'] == 'S') {
            if ($registro['D049_Flag_Nacional_Importado'] == "N") {
                $D137_Produzido_Adquirido = "A";
            } else if ($registro['D049_Flag_Nacional_Importado'] == "I") {
                $D137_Produzido_Adquirido = "P";
            } else {
                $D137_Produzido_Adquirido = "";
            }
        } else {
            if ($registro['D001_Flag_Tipo'] == "COMPRADO") {
                $D137_Produzido_Adquirido = "A";
            } else if ($registro['D001_Flag_Tipo'] == "PRODUZIDO") {
                $D137_Produzido_Adquirido = "P";
            } else {
                $D137_Produzido_Adquirido = "";
            }
        }
*/

        if($g['C031']['venderEstoqueMatriz'] == 'S'){
            if($C004_Id_Estoque == $g['empresaAtual']){
                $D137_Estoque_Origem = "D";
            } else {
                $D137_Estoque_Origem = "F";
            }
        }

        $retorno = mysql_query("SELECT *, D137_Quantidade_Regras(D137_Id) as qtdeRegras FROM D137 LEFT JOIN D006 ON D006_Id=D137_D006_Id WHERE D137_Flag_Inativar='N'");
        $validas;
        $valida;
        $err;
        $demonstrativoRegra = "";
        $log = "Início Log D069_Id: {$D069_Id} <br />";
        $arrayDemo = Array();
        while ($regras = mysql_fetch_assoc($retorno)) {
            $iconeAbrirJanela = <<<EOT
                if([[permissaoRegras]]){

                    abrirJanela(false, divIdRootAberto(), '[[divId]]', unique(), '', 'Editar Regra', '/fis/fis001/content/fis001content03/', '&acaoId=' + encodeURIComponent('{$regras['D137_Id']}') + '&tabela=D137', [600,550]);
                }
EOT;
            $log .= "<br />Entrou while D137_Id: {$regras['D137_Id']} - {$regras['D006_Codigo_CFOP']} - {$regras['D006_Descricao']}<br />";
            $demonstrativoDadosRegra = "<h3><font color=\"green\">Regra {$regras['D137_Id']} ({$regras['qtdeRegras']}): {$regras['D006_Id']}-{$regras['D006_Codigo_CFOP']} - {$regras['D006_Descricao']}</font><span class=\"gear\" style=\"float: right; cursor: pointer; width: 16px; height: 16px; right: 0; position: relative;\" onclick=\"{$iconeAbrirJanela} event.preventDefault(); return false;\" /></h3><div>";

            if (trim($regras['D137_Empresas_Destinatario']) != "") {
                $log .= "ENTROU IF D137_Empresas_Destinatario != ''<br />";
                $demonstrativoRegra .= "Cliente específicado nesta regra CFOP<br />";

                $pos = strpos($regras['D137_Empresas_Destinatario'], $D024_Id . ',');
                if ($pos === false) {
                    $log .= " Entrou CONTINUE D137_Empresas_Destinatario D137_Id='{$regras['D137_Id']}'<br />";
                    $demonstrativoRegra = "";
                    $arrayDemo[] = array(
                        'order' => $regras['qtdeRegras'],
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."Cliente não específicado na regra</div>"
                    );
                    $err .= '0';
                    continue;
                }
            }

            if ($registro['D024_D006_Id'] > 0 AND $regras['D137_Flag_Preferencial_Sobre_D024_D006_Id'] != "S") {
                $log .= "Entrou IF D024_D006_Id > AND D137_Flag_Preferencial_Sobre_D024_D006_Id != S  D137_Id='{$regras['D137_Id']}'<br />";
                $demonstrativoRegra .= "Cliente possuí CFOP preferencial<br />";
                $err .= '0';
                continue;
            }

            if (trim($regras['D137_Empresas']) != "") {
                $log .= "Entrou IF D137_Empresas != ''<br />";
                $demonstrativoRegra .= "Empresa específicada na regra<br />";

                $pos = strpos($regras['D137_Empresas'], $D137_C004_Id . ',');
                if ($pos === false) {
                    $log .= " Entrou CONTINUE D137_Empresas D137_Id='{$regras['D137_Id']}' D137_C004_Id: {$D137_C004_Id}<br />";
                    $demonstrativoRegra = "";
                    $arrayDemo[] = array(
                        'order' => $regras['qtdeRegras'],
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."Empresa não específicada na regra</div>"
                    );
                    $err .= '0';
                    continue;
                }
            }
            if ($regras['D137_C004_Id'] > 0) {
                $log = "Entrou IF D137_C004_Id > 0 D137_Id='{$regras['D137_Id']}'<br />";
                $demonstrativoRegra .= "Regra específica da empresa<br />";

                if ($regras['D137_C004_Id'] != $D137_C004_Id) {
                    $log .= "Entrou CONTINUE D137_C004_Id: {$regras['D137_C004_Id']} != D137_C004_Id: {$D137_C004_Id} D137_Id='{$regras['D137_Id']}'<br />";
                    $demonstrativoRegra = "";
                    $arrayDemo[] = array(
                        'order' => $regras['qtdeRegras'],
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."Regra específica para outra empresa</div>"
                    );
                    $err .= '0';
                    continue;
                }
            }
            if ($regras['D137_C004_Codigo_Regime_Tributario'] != "0") {
                $log .= "Entrou D137_C004_Codigo_Regime_Tributario != 0 D137_Id='{$regras['D137_Id']}'<br />";
                $demonstrativoRegra .= "Regime tributário da empresa específicado na regra<br />";

                if ($regras['D137_C004_Codigo_Regime_Tributario'] != $D137_C004_Codigo_Regime_Tributario) {

                    $log .= "Entrou CONTINUE D137_C004_Codigo_Regime_Tributario: {$regras['D137_C004_Codigo_Regime_Tributario']} != D137_C004_Codigo_Regime_Tributario: {$D137_C004_Codigo_Regime_Tributario} D137_Id='{$regras['D137_Id']}'<br />";
                    $demonstrativoRegra = "";
                    $arrayDemo[] = array(
                        'order' => $regras['qtdeRegras'],
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."Regime tributário da empresa diferente da regra</div>"
                    );
                    $err .= '1';
                    continue;
                }
            }
            if (!empty($regras['D137_D024_Codigo_Regime_Tributario'])) {
                $log .= "Entrou D137_D024_Codigo_Regime_Tributario != 0 D137_Id='{$regras['D137_Id']}'<br />";
                $demonstrativoRegra .= "Regime tributário do destinatário específicado na regra<br />";
                log("Regras D137_D024_Codigo_Regime_Tributario: ".$regras['D137_D024_Codigo_Regime_Tributario'].' D137_D024_Codigo_Regime_Tributario'.$D137_D024_Codigo_Regime_Tributario);
                if ($regras['D137_D024_Codigo_Regime_Tributario'] != $D137_D024_Codigo_Regime_Tributario) {

                    $log .= "Entrou CONTINUE D137_D024_Codigo_Regime_Tributario: {$regras['D137_D024_Codigo_Regime_Tributario']} != D137_D024_Codigo_Regime_Tributario: {$D137_D024_Codigo_Regime_Tributario} D137_Id='{$regras['D137_Id']}'<br />";
                    $demonstrativoRegra = "";
                    $arrayDemo[] = array(
                        'order' => $regras['qtdeRegras'],
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."Regime tributário do destinatário diferente da regra</div>"
                    );
                    $err .= '1';
                    continue;
                }
            }
            if ($regras['D137_D024_Flag_Contribuinte'] != "0") {
                $log .= "Entrou D137_D024_Flag_Contribuinte != 0 D137_Id='{$regras['D137_Id']}'<br />";
                $demonstrativoRegra .= "Tipo contribuinte do cliente na regra<br />";

                if ($regras['D137_D024_Flag_Contribuinte'] != $D137_D024_Flag_Contribuinte) {
                    $log .= "Entrou CONTINUE D137_D024_Flag_Contribuinte: {$regras['D137_D024_Flag_Contribuinte']} != D137_D024_Flag_Contribuinte: {$D137_D024_Flag_Contribuinte} D137_Id='{$regras['D137_Id']}'<br />";
                    $demonstrativoRegra = "";
                    $arrayDemo[] = array(
                        'order' => $regras['qtdeRegras'],
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."Tipo contribuinte do cliente diferente da regra</div>"
                    );
                    $err .= '2';
                    continue;
                }
            }
            if ($regras['D137_Dentro_Fora_Estado'] != "") {
                $clienteEstado = ($D137_Dentro_Fora_Estado == 'D') ? 'dentro' : 'fora';
                $regraEstado = ($regras['D137_Dentro_Fora_Estado'] == 'D') ? 'dentro' : 'fora';

                $log .= "Entrou D137_Dentro_Fora_Estado != '' D137_Id='{$regras['D137_Id']}'<br />";
                $demonstrativoRegra .= "Cliente de {$clienteEstado} do estado<br />";
                if ($regras['D137_Dentro_Fora_Estado'] != $D137_Dentro_Fora_Estado) {
                    $log .= "Entrou CONTINUE D137_Dentro_Fora_Estado: {$regras['D137_Dentro_Fora_Estado']} != D137_Dentro_Fora_Estado: {$D137_Dentro_Fora_Estado} D137_Id='{$regras['D137_Id']}'<br />";
                    $demonstrativoRegra = "";
                    $arrayDemo[] = array(
                        'order' => $regras['qtdeRegras'],
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."Cliente de {$clienteEstado} do estado, regra para {$regraEstado}</div>"
                    );
                    $err .= '3';
                    continue;
                }
            }
            if (trim($regras['D137_Estados_Especificos']) != "") {
                $log .= "Entrou D137_Estados_Especificos != '' D137_Id='{$regras['D137_Id']}'<br />";
                $demonstrativoRegra .= "Estado do cliente está na regra<br />";

                $pos = strpos($regras['D137_Estados_Especificos'], $registro['D018_UF']);
                if ($pos === false) {
                    $log .= "Entrou CONTINUE D137_Estados_Especificos: {$regras['D137_Estados_Especificos']} != D137_Estados_Especificos: {$D137_Estados_Especificos} D137_Id='{$regras['D137_Id']}'<br />";
                    $demonstrativoRegra = "";
                    $arrayDemo[] = array(
                        'order' => $regras['qtdeRegras'],
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."Estado do cliente não está na regra</div>"
                    );
                    $err .= '4';
                    continue;
                }
            }
            if ($regras['D137_UF_Tem_ST'] != "") {
                $UFClienteST = ($D137_UF_Tem_ST == 'S') ? 'possuí' : 'não possuí';
                $UFRegraST = ($regras['D137_UF_Tem_ST'] == 'S') ? 'possuí' : 'não possuí';

                $log .= "Entrou D137_UF_Tem_ST != '' D137_Id='{$regras['D137_Id']}'<br />";
                $demonstrativoRegra .= "Estado do cliente {$UFClienteST} ST<br />";
                if ($regras['D137_UF_Tem_ST'] != $D137_UF_Tem_ST) {
                    $log .= "Entrou CONTINUE D137_UF_Tem_ST: {$regras['D137_UF_Tem_ST']} != D137_UF_Tem_ST: {$D137_UF_Tem_ST} D137_Id='{$regras['D137_Id']}'<br />";
                    $demonstrativoRegra = "";
                    $arrayDemo[] = array(
                        'order' => $regras['qtdeRegras'],
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."Estado do cliente {$UFClienteST} ST e a regra {$UFRegraST}</div>"
                    );
                    $err .= '5';
                    continue;
                }
            }
            if ($regras['D137_Pessoa_Fisica_Juridica'] != "") {
                if($D137_Pessoa_Fisica_Juridica == 'PF') {
                    $pessoaCliente = 'física';
                } else if($D137_Pessoa_Fisica_Juridica == 'PJ') {
                    $pessoaCliente = 'jurídica';
                } else {
                    $pessoaCliente = 'internacional';
                }

                if($regras['D137_Pessoa_Fisica_Juridica'] == 'PF') {
                    $pessoaRegra = 'física';
                } else if($regras['D137_Pessoa_Fisica_Juridica'] == 'PJ') {
                    $pessoaRegra = 'jurídica';
                } else {
                    $pessoaRegra = 'internacional';
                }

                $log .= "Entrou D137_Pessoa_Fisica_Juridica != '' D137_Id='{$regras['D137_Id']}'<br />";
                $demonstrativoRegra .= "Cliente é pessoa {$pessoaCliente}<br />";
                if ($regras['D137_Pessoa_Fisica_Juridica'] != $D137_Pessoa_Fisica_Juridica) {
                    $log .= "Entrou CONTINUE D137_Pessoa_Fisica_Juridica: {$regras['D137_Pessoa_Fisica_Juridica']} != D137_Pessoa_Fisica_Juridica: {$D137_Pessoa_Fisica_Juridica} D137_Id='{$regras['D137_Id']}'<br />";
                    $demonstrativoRegra = "";
                    $arrayDemo[] = array(
                        'order' => $regras['qtdeRegras'],
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."Cliente é pessoa {$pessoaCliente} e a regra é para pessoa {$pessoaRegra}</div>"
                    );
                    $err .= '6';
                    continue;
                }
            }

            // as proximas duas regras se aplicam apenas se for item
            if ($regras['D137_NCM_Tem_ST'] != "") {
                $NCMST = ($D137_NCM_Tem_ST == 'S') ? 'possuí ST' : 'não possuí ST';
                $regraST = ($regras['D137_NCM_Tem_ST'] == 'S') ? 'possuí ST' : 'não possuí ST';

                $log .= "Entrou D137_NCM_Tem_ST != '' D137_Id='{$regras['D137_Id']}'<br />";
                $demonstrativoRegra .= "NCM do produto {$NCMST}<br />";
                if ($regras['D137_NCM_Tem_ST'] != $D137_NCM_Tem_ST) {
                    $log .= "Entrou CONTINUE D137_NCM_Tem_ST: {$regras['D137_NCM_Tem_ST']} != D137_NCM_Tem_ST: {$D137_NCM_Tem_ST} D137_Id='{$regras['D137_Id']}'<br />";
                    $demonstrativoRegra = "";
                    $arrayDemo[] = array(
                        'order' => $regras['qtdeRegras'],
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."NCM do produto {$NCMST} e a regra {$regraST}</div>"
                    );
                    $err .= '7';
                    continue;
                }
            }
            if ($regras['D137_Flag_UF_Convenio_NCM_ST'] == "S") {
                if($registro['D149_UF_ST'] != ""){
                    $log .= "Entrou D137_Flag_UF_Convenio_NCM_ST != '' D137_Id='{$regras['D137_Id']}'<br />";
                    $demonstrativoRegra .= "Estado do cliente possuí convenio NCM/UF ST<br />";
                    $pos = strpos($registro['D149_UF_ST'], $registro['D018_UF']);
                    if ($pos === false) {
                        $log .= "Entrou CONTINUE D149_UF_ST D137_Id='{$regras['D137_Id']}'<br />";
                        $demonstrativoRegra = "";
                        $arrayDemo[] = array(
                            'order' => $regras['qtdeRegras'],
                            'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."Estado do cliente não específicado no campo UF ST da NCM</div>"
                        );
                        $err .= '10';
                        continue;
                    }
                }
            }
            if ($regras['D137_Peso_Minimo'] > 0 OR $regras['D137_Peso_Maximo'] > 0) {
                $log .= "Entrou D137_Peso_Minimo >= '0' OR {$regras['D137_Peso_Maximo']}>0 D137_Id='{$regras['D137_Id']}'<br />";
                $demonstrativoRegra .= "O peso do produto está de acordo com o específicado<br />";

                if ($registro['D069_Peso_Unitario'] < $regras['D137_Peso_Minimo'] OR $registro['D069_Peso_Unitario'] > $regras['D137_Peso_Maximo']) {
                    $log .= "Entrou CONTINUE D069_Peso_Unitario: {$regras['D069_Peso_Unitario']} != D069_Peso_Unitario: {$D069_Peso_Unitario} D137_Id='{$regras['D137_Id']}'<br />";
                    $demonstrativoRegra = "";
                    $arrayDemo[] = array(
                        'order' => $regras['qtdeRegras'],
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."O peso do produto ultrapassa os valores da regra</div>"
                    );
                    $err .= '8';
                    continue;
                }
            }
            if (trim($regras['D137_NCM']) != "") {
                $log .= "Entrou D137_NCM != '' D137_Id='{$regras['D137_Id']}'<br />";
                $demonstrativoRegra .= "NCM do produto específicada na regra<br />";

                $pos = strpos($regras['D137_NCM'], $registro['D005_Classificacao_Fiscal']);
                if ($pos === false) {
                    $log .= "Entrou CONTINUE D137_NCM D137_Id='{$regras['D137_Id']}'<br />";
                    $demonstrativoRegra = "";
                    $arrayDemo[] = array(
                        'order' => $regras['qtdeRegras'],
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."NCM do produto não foi específicada na regra</div>"
                    );
                    $err .= '4';
                    continue;
                }
            }
            if ($regras['D137_Flag_Destino_Produto'] != "") {
                $log .= "Entrou D137_Flag_Destino_Produto != '' D137_Id='{$regras['D137_Id']}'<br />";
                $demonstrativoRegra .= "O destino do produto é o mesmo da regra<br />";

                if ($regras['D137_Flag_Destino_Produto'] != $Destino) {

                    $log .= "Entrou CONTINUE D137_Flag_Destino_Produto: {$regras['D137_Flag_Destino_Produto']} != D137_Flag_Destino_Produto: {$Destino} D137_Id='{$regras['D137_Id']}'<br />";
                    $demonstrativoRegra = "";
                    $arrayDemo[] = array(
                        'order' => $regras['qtdeRegras'],
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."O destino do produto é diferente da regra</div>"
                    );
                    $err .= '9';
                    continue;
                }
            }

            if ($regras['D137_Flag_Origem_Pedido'] != "") {
                $log .= "Entrou D137_Flag_Origem_Pedido != '' D137_Id='{$regras['D137_Id']}'<br />";
                $demonstrativoRegra .= "A origem do pedido é a mesma da regra<br />";

                if ($regras['D137_Flag_Origem_Pedido'] != $D137_Origem_Pedido) {

                    $log .= "Entrou CONTINUE D137_Flag_Origem_Pedido: {$regras['D137_Flag_Origem_Pedido']} != D137_Flag_Origem_Pedido: {$D137_Origem_Pedido} D137_Id='{$regras['D137_Id']}'<br />";
                    $demonstrativoRegra = "";
                    $arrayDemo[] = array(
                        'order' => $regras['qtdeRegras'],
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."A origem do pedido é diferente da regra</div>"
                    );
                    $err .= '9';
                    continue;
                }
            }
            
            if ($regras['D137_D005_Id'] > 0) {
                $log .= "Entrou D137_D005_Id >0 D137_Id='{$regras['D137_Id']}'<br />";
                $demonstrativoRegra .= "NCM do produto específicada na regra<br />";

                if ($regras['D137_D005_Id'] != $D137_D005_Id) {
                    $log .= "Entrou CONTINUE D137_D005_Id: {$regras['D137_D005_Id']} != D137_D005_Id: {$D137_D005_Id} D137_Id='{$regras['D137_Id']}'<br />";
                    $demonstrativoRegra = "";
                    $arrayDemo[] = array(
                        'order' => $regras['qtdeRegras'],
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."NCM do produto diferente da regra</div>"
                    );
                    $err .= '9';
                    continue;
                }
            }

            if ($regras['D137_Origem_Estoque'] != ""){
                $log.="Entrou D137_Origem_Estoque != '' D137_Id='{$regras['D137_Id']}'<br />";
                $demonstrativoRegra .= "Origem do estoque específicada na regra<br />";

                if ($regras['D137_Origem_Estoque']!=$D137_Estoque_Origem){
                    $log.="Entrou CONTINUE D137_Origem_Estoque: {$regras['D137_Origem_Estoque']} != D137_Origem_Estoque: {$D137_Estoque_Origem} D137_Id='{$regras['D137_Id']}'<br />";
                    $demonstrativoRegra = "";
                    $arrayDemo[] = array(
                        'order' => $regras['qtdeRegras'],
                        'texto' => str_replace('green', 'red', $demonstrativoDadosRegra)."Origem do estoque diferente da regra</div>"
                    );
                    $err.='9';
                    continue;
                }
            }

            $arrayDemo[] = array(
                'D006_Id' => $regras['D137_D006_Id'],
                'order' => $regras['qtdeRegras']+100,
                'texto' => $demonstrativoDadosRegra.$demonstrativoRegra."</div>"
            );

            $log .= "Aplicou essa regra<br />";
            $validas[] = $regras['D137_Id'];
            $valida    = $regras['D137_D006_Id'];
        }
        $log = $log . " <br />TOTAL REGRAS ENCONTRADAS: " . count($validas);
        log($log);

        usort($arrayDemo, function($a, $b) {
            if ($a['order'] == $b['order']) {
                return 0;
            }
            return ($a['order'] < $b['order']) ? 1 : -1;
        });

        if (count($validas) == 0) {
            if ($registro['D024_D006_Id'] > 0) {
                $valida = $registro['D024_D006_Id'];
            }
            $valida = false;
        } else if(count($validas) > 1){
            $qtde_regras = 0;
            $valida      = 0;
            for ($i = 0; $i < count($validas); $i++) {
                $D137  = mysql_query("SELECT D137_Quantidade_Regras('{$validas[$i]}') as qtde_regras FROM D137 limit 1");
                $mD137 = mysql_fetch_array($D137);
                if ($mD137['qtde_regras'] > $qtde_regras) {
                    $D137_1      = mysql_query("SELECT D137_D006_Id FROM D137 WHERE D137_Id='{$validas[$i]}'");
                    $mD137_1     = mysql_fetch_array($D137_1);
                    $qtde_regras = $mD137['qtde_regras'];
                    $valida      = $mD137_1['D137_D006_Id'];
                }
            }
        }

        foreach($arrayDemo as $key => $value){
            if(!empty($value['D006_Id']) && $value['D006_Id'] == $valida){
                array_unshift($arrayDemo, $arrayDemo[$key]);
                unset($arrayDemo[$key+1]);
            }
        }
        $demonstrativo = "<script>$(function() {\$(\"#demonstrativoCFOP\").accordion({collapsible: true, autoHeight: false, icons: false}); });</script><div id=\"demonstrativoCFOP\">";
        foreach ($arrayDemo as $key => $value) {
            $demonstrativo .= $value['texto'];
        }

        $demonstrativo .= "</div><br /><b>TOTAL REGRAS VÁLIDAS: " . count($validas) . "</b>";
        $demonstrativo .= "</div><br /><b>ID CFOP UTILIZADO: " . $valida . "</b>";
        $demonstrativo .= "<b style=\"float: right\">".date("d/m/Y H:i:s")."</b><br />";
        if($retornaLog == true){
            return $demonstrativo;
        } else {
            return $valida;
        }
    }

    /**
     * retornaAliquotasTabelaFornecedor
     *
     * @param String $D069_Id
     * @param String $T003_Id
     * @param String $T005_Id
     * @param String $T007_Id
     * @param String $D006_Id
     * @param String $D024_Id_Enviado
     * @return String/array
     */
    public function retornaAliquotasTabelaFornecedor($D069_Id, $T003_Id = false, $T005_Id = false, $T007_Id = false, $D006_Id = false, $D024_Id_Enviado = false)
    {
        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();
        global $g;

        log("Retorna aliquotas D069_Id = {$D069_Id}");

        $mProduto = array();

        if ($T003_Id > 0) {
            $cClausula = " SELECT * ";
            $cClausula .= " FROM T003 ";
            $cClausula .= " LEFT JOIN D024 ON D024_Id=T003_D024_Id ";
            $cClausula .= " LEFT JOIN D018 ON D018_Id=D024_D018_Id ";
            $cClausula .= " LEFT JOIN D053 ON D053_D018_Id=D024_D018_Id AND D053_C004_Id=T003_C004_Id ";
            $cClausula .= " LEFT JOIN C004 ON C004_Id=T003_C004_Id ";
            $cClausula .= " LEFT JOIN D006 ON D006_Id=T003_D006_Id ";
            $cClausula .= " WHERE T003_Id='{$T003_Id}' ";

            $Cab   = mysql_query($cClausula);
            $cErro = mysql_error();

            if (!empty($cErro)) {
                return "{$cErro}<br />{$cClausula}";
            }
            $mCab = mysql_fetch_array($Cab);

            $D024_Id                 = $mCab['T003_D024_Id'];
            $C004_Id                 = $mCab['T003_C004_Id'];
            $D018_Id                 = $mCab['D024_D018_Id'];
            $Flag_Acp                = $mCab['T003_Flag_Acp'];
            $Flag_Revenda_Consumidor = $mCab['D024_Flag_Revenda_Consumidor'];
            $Flag_Contribuinte       = $mCab['T003_Flag_Contribuinte'];
            $Percentual_Comissao     = $mCab['T003_Percentual_Comissao'];
            $Aliquota_ICMS_Credito   = "";

        } else if ($T005_Id > 0) {
            $cClausula = " SELECT * ";
            $cClausula .= " FROM T005 ";
            $cClausula .= " LEFT JOIN D024 ON D024_Id=T005_D024_Id ";
            $cClausula .= " LEFT JOIN D018 ON D018_Id=D024_D018_Id ";
            $cClausula .= " LEFT JOIN D053 ON D053_D018_Id=D024_D018_Id AND D053_C004_Id=T005_C004_Id ";
            $cClausula .= " LEFT JOIN C004 ON C004_Id=T005_C004_Id ";
            $cClausula .= " LEFT JOIN D006 ON D006_Id=T005_D006_Id ";
            $cClausula .= " WHERE T005_Id='$T005_Id' ";

            $Cab   = mysql_query($cClausula);
            $cErro = mysql_error();

            if (!empty($cErro)) {
                return "{$cErro}<br />{$cClausula}";
            }
            $mCab = mysql_fetch_array($Cab);

            $D024_Id                 = $mCab['T005_D024_Id'];
            $C004_Id                 = $mCab['T005_C004_Id'];
            $D018_Id                 = $mCab['D024_D018_Id'];
            $Flag_Acp                = $mCab['T005_Flag_ACP'];
            $Flag_Revenda_Consumidor = $mCab['D024_Flag_Revenda_Consumidor'];
            $Flag_Contribuinte       = $mCab['D024_Flag_Contribuinte'];
            $Percentual_Comissao     = $mCab['T005_Percentual_Comissao'];
            $Aliquota_ICMS_Credito   = "";

        } else if ($T007_Id > 0) {

            $cClausula = " SELECT * ";
            $cClausula .= " FROM T007 ";
            $cClausula .= " LEFT JOIN D024 ON D024_Id=T007_D024_Id ";
            $cClausula .= " LEFT JOIN D018 ON D018_Id=D024_D018_Id ";
            $cClausula .= " LEFT JOIN D053 ON D053_D018_Id=D024_D018_Id AND D053_C004_Id=T007_C004_Id ";
            $cClausula .= " LEFT JOIN C004 ON C004_Id=T007_C004_Id ";
            $cClausula .= " LEFT JOIN T134 ON T134_Id=T007_T134_Id ";
            $cClausula .= " LEFT JOIN D006 ON D006_Id=T007_D006_Id ";
            $cClausula .= " WHERE T007_Id='$T007_Id' ";

            $Cab   = mysql_query($cClausula);
            $cErro = mysql_error();

            if (!empty($cErro)) {
                return "{$cErro}<br />{$cClausula}";
            }
            $mCab = mysql_fetch_array($Cab);

            $D024_Id                 = $mCab['T007_D024_Id'];
            $C004_Id                 = $mCab['T007_C004_Id'];
            $D018_Id                 = $mCab['D024_D018_Id'];
            $Flag_Acp                = $mCab['T007_Flag_ACP'];
            $Flag_Revenda_Consumidor = $mCab['D024_Flag_Revenda_Consumidor'];
            $Flag_Contribuinte       = $mCab['D024_Flag_Contribuinte'];
            $Percentual_Comissao     = $mCab['T007_Percentual_Comissao'];
            $Aliquota_ICMS_Credito   = $mCab['T134_ICMS'];

        }

        if(empty($D024_Id) && !empty($D024_Id_Enviado)){
            $D024_Id = $D024_Id_Enviado;
            $C004_Id = $g['empresaAtual'];

            $cClausula = " SELECT * FROM D024 ";
            $cClausula .= " LEFT JOIN D018 ON D018_Id=D024_D018_Id ";
            $cClausula .= " LEFT JOIN D053 ON D053_D018_Id=D024_D018_Id AND D053_C004_Id=$C004_Id ";
            $cClausula .= " LEFT JOIN C004 ON C004_Id=$C004_Id ";
            $cClausula .= " WHERE D024_Id='$D024_Id' ";
            $Cab   = mysql_query($cClausula);
            $cErro = mysql_error();

            if (!empty($cErro)) {
                return "{$cErro}<br />{$cClausula}";
            }
            $mCab = mysql_fetch_array($Cab);

            $D018_Id                 = $mCab['D024_D018_Id'];
            $Flag_Revenda_Consumidor = $mCab['D024_Flag_Revenda_Consumidor'];
            $Flag_Contribuinte       = $mCab['D024_Flag_Contribuinte'];
            $Flag_Acp                = 6;
        }

        $cClausula = " SELECT * ";
        $cClausula .= " FROM D006 ";
        $cClausula .= " LEFT JOIN D059 ON D059_Id=D006_D059_Id ";
        $cClausula .= " LEFT JOIN D088 ON D088_Id=D006_D088_Id ";
        $cClausula .= " WHERE D006_Id='{$D006_Id}'";
        $D006 = mysql_query($cClausula);
        if (!empty($cErro)) {
            return "$cErro<br />$cClausula";
        }
        $mD006 = mysql_fetch_array($D006);

        log("Retorna aliquotas - Iniciando query produtos D069_Id = {$D069_Id}");

        $cClausula = " SELECT ";
        $cClausula .= " D069_Id, ";
        $cClausula .= " D005_Id, ";
        $cClausula .= " D149_Id, ";
        $cClausula .= " D070_D082_Id, ";
        $cClausula .= " D018_UF, ";
        $cClausula .= " D069_Valor_Custo_Unitario, ";
        $cClausula .= " D024_Horario_Entrega, ";
        $cClausula .= " D069_Peso_Unitario, ";
        $cClausula .= " D069_Flag_Origem_Produto, ";
        $cClausula .= " D069_IPI_3('{$C004_Id}', D069_Id, '{$D024_Id}', '{$D006_Id}', '1', '{$Flag_Acp}') as Aliquota_IPI, ";
        $cClausula .= " D069_IPI_3('{$C004_Id}', D069_Id, '{$D024_Id}', '{$D006_Id}', '2', '{$Flag_Acp}') as Percentual_Reducao_IPI, ";
        $cClausula .= " D149_Flag_ST, ";
        $cClausula .= " D005_Classificacao_Fiscal, ";
        $cClausula .= " D088_Codigo_ST, ";
        $cClausula .= " D005_Aliquota_PIS, ";
        $cClausula .= " D005_Aliquota_COFINS, ";
        $cClausula .= " D149_Valor_VA, ";
        $cClausula .= " D149_Aliquota_COFINS, ";
        $cClausula .= " D149_Aliquota_CSLL, ";
        $cClausula .= " D149_Aliquota_IRPJ, ";
        $cClausula .= " D149_Aliquota_PIS, ";
        $cClausula .= " D149_Codigo_CEST, ";
        $cClausula .= " D149_Regras_MVA, ";
        $cClausula .= " D069_Valor_ICMS_Substituicao_Retencao ";
        $cClausula .= " FROM D069";
        $cClausula .= " LEFT JOIN D070 ON D070_Id=D069_D070_Id ";
        $cClausula .= " LEFT JOIN D037 ON D037_Id=D069_D037_Id ";
        $cClausula .= " LEFT JOIN D024 ON D024_Id='{$D024_Id}' ";
        $cClausula .= " LEFT JOIN D018 ON D018_Id=D024_D018_Id ";
        $cClausula .= " LEFT JOIN D005 ON D005_Id=D069_D005_Id";
        $cClausula .= " LEFT JOIN D149 ON D005_Id=D149_D005_Id AND D149_C004_Id='{$C004_Id}' ";
        $cClausula .= " LEFT JOIN D088 ON D088_Id=D149_D088_Id ";
        $cClausula .= " WHERE D069_Id='{$D069_Id}' GROUP BY D069_Id";
        $Produto  = mysql_query($cClausula);
        $mProduto = mysql_fetch_array($Produto);
        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();
        log($cClausula);

        //Verifica as exceções de valor de MVA da NCM
        /*
        if (!empty($mProduto['D149_Regras_MVA'])) {
            preg_match_all("/(MVA|MVAA) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA'], $D149_Regras_MVA);
            $valoresRegras = $D149_Regras_MVA[3];
            if (in_array($mProduto['D018_UF'], $D149_Regras_MVA[2])) {
                $regra                     = array_search($mProduto['D018_UF'], $D149_Regras_MVA[2]);
                $mProduto['D149_Valor_VA'] = str_replace(",", ".", $valoresRegras[$regra]);
            }
        }*/

        $Aliquota_ICMS           = $CAD002->retornaICMSTabelaFornecedor($C004_Id, $D024_Id, $mProduto['D069_Id'], $D006_Id, $D018_Id, $Flag_Revenda_Consumidor, $Percentual_Comissao, $Flag_Acp, '2');
        $Percentual_Reducao_ICMS = $CAD002->retornaICMSTabelaFornecedor($C004_Id, $D024_Id, $mProduto['D069_Id'], $D006_Id, $D018_Id, $Flag_Revenda_Consumidor, $Percentual_Comissao, $Flag_Acp, '3');

        $mProduto['Aliquota_ICMS']           = $Aliquota_ICMS;
        $mProduto['Percentual_Reducao_ICMS'] = $Percentual_Reducao_ICMS;

        //Verifica as exceções de valor de MVA e ICMS da NCM
        if (!empty($mProduto['D149_Regras_MVA'])) {
            preg_match_all("/(MVA|MVAA) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA'], $D149_Regras_MVA);
            $valoresRegras = $D149_Regras_MVA[3];
            if (in_array($mCab['D018_UF'], $D149_Regras_MVA[2])) {
                $regra = array_search($mProduto['D018_UF'], $D149_Regras_MVA[2]);
                if (!empty($valoresRegras[$regra])) {
                    $mProduto['D149_Valor_VA'] = str_replace(",", ".", $valoresRegras[$regra]);
                }
            }
            if ($mProduto['Aliquota_ICMS']>0) {
                log("/(ICM|ICMS) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims {$mProduto['D149_Regras_MVA']}, {$D149_Regras_ICM}");
                preg_match_all("/(ICM|ICMS) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA'], $D149_Regras_ICM);
                $valoresRegras = $D149_Regras_ICM[3];
                if (in_array($mCab['D018_UF'], $D149_Regras_ICM[2])) {
                    $regra = array_search($mProduto['D018_UF'], $D149_Regras_ICM[2]);
                    if (!empty($valoresRegras[$regra])) {
                        $mProduto['Aliquota_ICMS'] = str_replace(",", ".", $valoresRegras[$regra]);
                    }
                } else if (in_array('XX', $D149_Regras_ICM[2])) {
                    $regra = array_search('XX', $D149_Regras_ICM[2]);
                    if (!empty($valoresRegras[$regra])) {
                        $mProduto['Aliquota_ICMS'] = str_replace(",", ".", $valoresRegras[$regra]);
                    }
                }
            }
            log("/(ICMIE|ICMSIE) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims {$mProduto['D149_Regras_MVA']}, {$D149_Regras_ICM}");
            preg_match_all("/(ICMIE|ICMSIE) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA'], $D149_Regras_ICM);
            $valoresRegras = $D149_Regras_ICM[3];
            if (in_array($mCab['D018_UF'], $D149_Regras_ICM[2])) {
                $regra = array_search($mProduto['D018_UF'], $D149_Regras_ICM[2]);
                if (!empty($valoresRegras[$regra])) {
                    $mCab['D053_ST_ICMS_Inter_Estadual'] = str_replace(",", ".", $valoresRegras[$regra]);
                }
            } else if (in_array('XX', $D149_Regras_ICM[2])) {
                $regra = array_search('XX', $D149_Regras_ICM[2]);
                if (!empty($valoresRegras[$regra])) {
                    $mCab['D053_ST_ICMS_Inter_Estadual'] = str_replace(",", ".", $valoresRegras[$regra]);
                }
            }
            log("/(ICMINT|ICMSINT) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims {$mProduto['D149_Regras_MVA']}, {$D149_Regras_ICM}");
            preg_match_all("/(ICMINT|ICMSINT) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA'], $D149_Regras_ICM);
            $valoresRegras = $D149_Regras_ICM[3];
            if (in_array($mCab['D018_UF'], $D149_Regras_ICM[2])) {
                $regra = array_search($mProduto['D018_UF'], $D149_Regras_ICM[2]);
                if (!empty($valoresRegras[$regra])) {
                    $mCab['D053_ST_ICMS_Interno'] = str_replace(",", ".", $valoresRegras[$regra]);
                }
            } else if (in_array('XX', $D149_Regras_ICM[2])) {
                $regra = array_search('XX', $D149_Regras_ICM[2]);
                if (!empty($valoresRegras[$regra])) {
                    $mCab['D053_ST_ICMS_Interno'] = str_replace(",", ".", $valoresRegras[$regra]);
                }
            }
        }


        if (($mProduto['D069_Flag_Origem_Produto'] == '1' || $mProduto['D069_Flag_Origem_Produto'] == '2' || $mProduto['D069_Flag_Origem_Produto'] == '3') && $mCab['D024_D018_Id'] != $mCab['C004_D018_Id'] && $mD006['D006_Flag_Isento_ICMS'] != 'S') {
            if (!empty($mProduto['D149_Regras_MVA'])) {
                if ($mProduto['Aliquota_ICMS']>0) {
                    log("/(ICMIMP|ICMSIMP) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims {$mProduto['D149_Regras_MVA']}, {$D149_Regras_ICM}");
                    preg_match_all("/(ICMIMP|ICMSIMP) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA'], $D149_Regras_ICM);
                    $valoresRegras = $D149_Regras_ICM[3];
                    if (in_array($mCab['D018_UF'], $D149_Regras_ICM[2])) {
                        $regra = array_search($mProduto['D018_UF'], $D149_Regras_ICM[2]);
                        if (!empty($valoresRegras[$regra])) {
                            $mProduto['Aliquota_ICMS'] = str_replace(",", ".", $valoresRegras[$regra]);
                        }
                    } else if (in_array('XX', $D149_Regras_ICM[2])) {
                        $regra = array_search('XX', $D149_Regras_ICM[2]);
                        if (!empty($valoresRegras[$regra])) {
                            $mProduto['Aliquota_ICMS'] = str_replace(",", ".", $valoresRegras[$regra]);
                        }
                    }
                }
            }
            $mCab['D053_ST_ICMS_Inter_Estadual'] = $mProduto['Aliquota_ICMS'] ;
            $Aliquota_ICMS_Oculto = $mProduto['Aliquota_ICMS'] ;
        }

        if ($mD006['D006_ST_ICMS_Inter_Estadual'] > 0){
            $mCab['D053_ST_ICMS_Inter_Estadual'] = $mD006['D006_ST_ICMS_Inter_Estadual'];
        }

        if ($mD006['D006_ST_ICMS_Interno'] > 0){
            $mCab['D053_ST_ICMS_Interno'] = $mD006['D006_ST_ICMS_Interno'];
        }

        $D053_ST_ICMS_Inter_Estadual = $mCab['D053_ST_ICMS_Inter_Estadual'];
        $D053_ST_ICMS_Interno        = $mCab['D053_ST_ICMS_Interno'];

        if ($mD006['D006_ST_ICMS_Inter_Estadual_MVA'] > 0){
            $mCab['D053_ST_ICMS_Inter_Estadual'] = $mD006['D006_ST_ICMS_Inter_Estadual_MVA'];
        }

        if ($mD006['D006_ST_ICMS_Interno_MVA'] > 0){
            $mCab['D053_ST_ICMS_Interno'] = $mD006['D006_ST_ICMS_Interno_MVA'];
        }

        if ($mD006['D006_ST_MVA'] > 0)
            $mProduto['D149_Valor_VA'] = $mD006['D006_ST_MVA'];

        if ($mProduto['D149_Valor_VA'] > 0) {
            $Variavel_A                = 1 + ($mProduto['D149_Valor_VA'] / 100);
            $Variavel_B                = (1 - ($mCab['D053_ST_ICMS_Inter_Estadual'] / 100)) / (1 - ($mCab['D053_ST_ICMS_Interno'] / 100));
            $Variavel_AB               = (($Variavel_A * $Variavel_B) - 1) * 100;
            $mProduto['D149_Valor_VA'] = $Variavel_AB;
        }

        $mCab['D053_ST_ICMS_Inter_Estadual'] = $D053_ST_ICMS_Inter_Estadual;
        $mCab['D053_ST_ICMS_Interno']        = $D053_ST_ICMS_Interno;

        if ($mD006['D006_Flag_ST_VA'] == "N") {
            $mProduto['D149_Valor_VA'] = 0;
        }

        if ((($Flag_Acp != '1') AND ($Flag_Acp != '6')) OR ($mD006['D006_Flag_Substituicao_Tributaria'] != 'S')) {
            $mProduto['D149_Flag_ST']  = "N";
            $mProduto['D149_Valor_VA'] = 0;
        }

        if ($mD006['D006_Flag_Substituicao_Tributaria'] == 'S' && $mProduto['D149_Flag_ST'] == 'S') {
            $Flag_ST = 'S';
        } else {
            $Flag_ST                               = 'N';
            //$mCab['D053_ST_ICMS_Interno']          = 0;
            //$mCab['D053_ST_ICMS_Inter_Estadual']   = 0;
            //$mD006['D006_ST_ICMS_Interno_Reducao'] = 0;
            //$mProduto['D149_Valor_VA']             = 0;
        }

        //Aplica a redução de MVA
        if ($mD006['D006_Reducao_MVA'] > 0 && $mProduto['D149_Valor_VA'] > 0) {
            $mProduto['D149_Valor_VA'] = $mProduto['D149_Valor_VA'] - (( ($mD006['D006_Reducao_MVA'] / 100) * $mProduto['D149_Valor_VA']) / 100);
        }

        $Flag_Situacao_Tributaria = '0' . $mD006['D059_Codigo_ST'];
        // Verificando a Situação tributária
        if ((!empty($mProduto['D069_Flag_Origem_Produto']) OR $mProduto['D069_Flag_Origem_Produto'] == '0') AND $mProduto['D069_Flag_Origem_Produto'] != '') {
            $Flag_Situacao_Tributaria = $mProduto['D069_Flag_Origem_Produto'] . $mD006['D059_Codigo_ST'];
        }

        if($mD006['D006_Flag_Isento_ICMS'] == 'D' && ($mD006['D006_D059_Id'] == '5' || $mD006['D006_D059_Id'] == '6' || $mD006['D006_D059_Id'] == '7')){
            $mProduto['Aliquota_ICMS'] = 0;
        }

        if ($mProduto['D149_Aliquota_PIS'] > 0) {
            $mCab['C004_Percentual_PIS'] = $mProduto['D149_Aliquota_PIS'];
        }

        if ($mProduto['D149_Aliquota_COFINS'] > 0) {
            $mCab['C004_Percentual_COFINS'] = $mProduto['D149_Aliquota_COFINS'];
        }

        if ($mProduto['D149_Aliquota_IRPJ'] > 0) {
            $mCab['C004_Percentual_IRPJ'] = $mProduto['D149_Aliquota_IRPJ'];
        }

        if ($mProduto['D149_Aliquota_CSLL'] > 0) {
            $mCab['C004_Percentual_CSLL'] = $mProduto['D149_Aliquota_CSLL'];
        }

        // Buscando o percentual de tributos (lei transparências)
        $D149a  = mysql_query("SELECT D149_Percentual_Trib_Transparencia FROM D149 WHERE D149_Id='{$mProduto['D149_Id']}'");
        $mD149a = mysql_fetch_array($D149a);

        $D006a  = mysql_query("SELECT D006_Percentual_Trib_Transparencia FROM D006 WHERE D006_Id='{$D006_Id}'");
        $mD006a = mysql_fetch_array($D006a);

        if (!empty($mD149a['D149_Percentual_Trib_Transparencia']) AND $mD149a['D149_Percentual_Trib_Transparencia'] > 0) {
            $Percentual_Trib_Transparencia = $mD149a['D149_Percentual_Trib_Transparencia'];
        } else if (!empty($mD006a['D006_Percentual_Trib_Transparencia']) AND $mD006a['D006_Percentual_Trib_Transparencia'] > 0) {
            $Percentual_Trib_Transparencia = $mD006a['D006_Percentual_Trib_Transparencia'];
        } else {
            $Percentual_Trib_Transparencia = 0;
        }

        // Definindo o array de retorno
        $array = array();

        $array['D006_Id']                          = $D006_Id;
        $array['Flag_ST']                          = $Flag_ST;
        $array['D005_Id']                          = $mProduto['D005_Id'];
        $array['D149_Id']                          = $mProduto['D149_Id'];
        $array['Classificacao_Fiscal']             = $mProduto['D005_Classificacao_Fiscal'];
        $array['Aliquota_IPI']                     = $mProduto['Aliquota_IPI'];
        $array['Aliquota_ICMS']                    = $mProduto['Aliquota_ICMS'];
        $array['Percentual_Reducao_ICMS']          = $mProduto['Percentual_Reducao_ICMS'];
        $array['ST_VA']                            = $mProduto['D149_Valor_VA'];
        $array['D082_Id']                          = $mProduto['D070_D082_Id'];
        $array['Valor_Custo_Unitario']             = $mProduto['D069_Valor_Custo_Unitario'];
        $array['D069_Peso_Unitario']               = $mProduto['D069_Peso_Unitario'];
        $array['Percentual_Reducao_IPI']           = $mProduto['Percentual_Reducao_IPI'];
        $array['Valor_Custo_ST_Unitario']          = $mProduto['D069_Valor_ICMS_Substituicao_Retencao'];
        $array['Codigo_CEST']                      = $mProduto['D149_Codigo_CEST'];
        $array['Aliquota_PIS']                     = $mCab['C004_Percentual_PIS'];
        $array['ICMS_Inter_Estadual']              = $mCab['D053_ST_ICMS_Inter_Estadual'];
        $array['ST_ICMS_Interno']                  = $mCab['D053_ST_ICMS_Interno'];
        $array['Aliquota_COFINS']                  = $mCab['C004_Percentual_COFINS'];
        $array['IPV_Sem_Lucro']                    = $mCab['C004_IPV_Padrao_Sem_Lucro'];
        $array['Aliquota_IRPJ']                    = $mCab['C004_Percentual_IRPJ'];
        $array['Aliquota_CSLL']                    = $mCab['C004_Percentual_CSLL'];
        $array['Percentual_FCP']                   = $mCab['D053_Percentual_FCP'];
        $array['Percentual_Reducao_ICMS_ST']       = $mD006['D006_ST_ICMS_Interno_Reducao'];
        $array['ST_ICMS_Inter_Estadual_MVA']       = $mD006['D006_ST_ICMS_Inter_Estadual_MVA'];
        $array['ST_ICMS_Interno_MVA']              = $mD006['D006_ST_ICMS_Interno_MVA'];
        $array['Flag_Isento_PIS']                  = $mD006['D006_Flag_Isento_PIS'];
        $array['Situacao_Tributaria_PIS']          = $mD006['D006_Situacao_Tributaria_PIS'];
        $array['Situacao_Tributaria_COFINS']       = $mD006['D006_Situacao_Tributaria_COFINS'];
        $array['Situacao_Tributaria_IPI']          = $mD006['D088_Codigo_ST'];
        $array['D059_Id']                          = $mD006['D059_Id'];
        $array['Flag_Isento_COFINS']               = $mD006['D006_Flag_Isento_COFINS'];
        $array['Codigo_Enq_Legal_IPI']             = $mD006['D006_Codigo_Enq_Legal_IPI'];
        $array['Motivo_Desoneracao_ICMS']          = $mD006['D006_Motivo_Desoneracao_ICMS'];
        $array['Flag_Isento_IRPJ']                 = $mD006['D006_Flag_Isento_IRPJ'];
        $array['Flag_Isento_CSLL']                 = $mD006['D006_Flag_Isento_CSLL'];
        $array['Percentual_Partilha_ICMS_Destino'] = $mD006['D006_Percentual_Partilha_ICMS_Destino'];
        $array['Percentual_Partilha_ICMS_Origem']  = $mD006['D006_Percentual_Partilha_ICMS_Origem'];
        $array['Aliquota_ICMS_Oculto']             = $Aliquota_ICMS_Oculto;
        $array['Percentual_Reducao_ICMS_Oculto']   = $Percentual_Reducao_ICMS_Oculto;
        $array['Flag_Situacao_Tributaria']         = $Flag_Situacao_Tributaria;
        $array['Aliquota_ICMS_Credito']            = $Aliquota_ICMS_Credito;
        $array['Percentual_Trib_Transparencia']    = $Percentual_Trib_Transparencia;
        return $array;
    }

    /**
     * Funçao que sera utilizada para calcular o valor do frete de acordo com 
     * configuração das variaveis das transportadoras
     */    
    public function calcularFrete($dadosTabela)
    {
        global $g;

        // Funcao calcularFreteNovo utilizada no APP FRETE CALCULADO VENDAS
        // Felipe Carrano 10/11/2022
        $valor_frete_calculado = $this->calcularFreteNovo($dadosTabela['tabela'],$dadosTabela['tabelaId']);
        if($valor_frete_calculado !== true){
            return $valor_frete_calculado;
        }

        $query = mysql_query("SELECT {$dadosTabela['campos']},
                                     D022_Taxa_Frete as porcentagemVenda,
                                     D022_Valor_Frete_Peso as fretePeso,
                                     D022_Valor_Frete_Minimo as freteMinimo,
                                     D022_Valor_Taxa_Pedagio as taxaPedagio,
                                     D022_Valor_Fracao_Pedagio as fracaoPedagio,
                                     D022_Valor_Frete_Cubagem as freteCubagem,
                                     D022_Taxa_ICMS_Dentro_Estado as ICMSDentroEstado,
                                     D022_Taxa_ICMS_Fora_Estado as ICMSForaEstado,
                                     D022_Taxa_GRIS as taxaGRIS
                                FROM {$dadosTabela['tabela']} 
                           LEFT JOIN D022 ON {$dadosTabela['tabela']}_D022_Id=D022_Id 
                               WHERE {$dadosTabela['tabela']}_Id='{$dadosTabela['tabelaId']}'");
        $mQueryResult = mysql_fetch_array($query);

        //VALORES DO CABEÇALHO DA VENDA
        $valor_total   = $mQueryResult['valorTotal'];
        $cubagem_total = $mQueryResult['totalCubagem'];
        $peso_total    = $mQueryResult['pesoTotal'];

        //VALORES CONFIGURADOS NA TRANSPORTADORA
        $porcentagemVenda = $mQueryResult['porcentagemVenda'];
        $fretePeso        = $mQueryResult['fretePeso'];
        $freteMinimo      = $mQueryResult['freteMinimo'];
        $freteCubagem     = $mQueryResult['freteCubagem'];
        $taxaPedagio      = $mQueryResult['taxaPedagio'];
        $fracaoPedagio    = $mQueryResult['fracaoPedagio'];
        $ICMSDentroEstado = $mQueryResult['ICMSDentroEstado'];
        $ICMSForaEstado   = $mQueryResult['ICMSForaEstado'];
        $taxaGRIS         = $mQueryResult['taxaGRIS'];

        // fórmulas
        $calculo_frete_valor    = (($valor_total * $porcentagemVenda)/100);
        $calculo_frete_peso     = ($peso_total * $fretePeso);
        $calculo_frete_tamanho  = ($cubagem_total * $fretePeso * $freteCubagem);

        $valor_frete_parcial     = max($calculo_frete_valor,$calculo_frete_peso,$calculo_frete_tamanho,$freteMinimo);
        
        //Pedagio
        if($fracaoPedagio > 0 ){
            $pedagio = ((($cubagem_total * $freteCubagem)/$fracaoPedagio) * $taxaPedagio);
        }

        //impostos
        if($ICMSDentroEstado>0){
            $calculo_ICMS_dentro = ($valor_frete_parcial * $ICMSDentroEstado)/100;
        }

        if($ICMSForaEstado>0){
            $calculo_ICMS_fora = ($valor_frete_parcial * $ICMSForaEstado)/100;
        }


        if($taxaGRIS>0){
            $calculo_taxa_GRIS = ($valor_total * $taxaGRIS)/100;
        }

        // Valor do frete calculado
        $valor_frete_calculado = $valor_frete_parcial + $pedagio + $calculo_ICMS_fora + $calculo_ICMS_dentro + $calculo_taxa_GRIS;

        return $valor_frete_calculado;
    }

    /**
     * Funçao que sera utilizada para calcular o valor do frete de acordo com 
     * configuração das variaveis das transportadoras novo modelo
     */    
    public function calcularFreteNovo($tabela, $acaoId)
    {
        global $g;

        // Funcao utilizada no APP FRETE CALCULADO VENDAS
        // Felipe Carrano 10/11/2022

        return true;

    }

    public function atualizarPrecoTabela($T003_Id){
        global $g;

        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();

        log("entrou no atualizar preço tabela");

        //Buscando as aliquotas digitadas na tela de montagem do ipv no orçamento
        $T250 = mysql_query("SELECT sum(T250_Aliquota-T250_Aliquota_Original) as AliquotasIPV from T250 WHERE T250_T003_Id = '$T003_Id' AND T250_Flag_Altera_Preco='S' AND T250_Grupo='IPV'");
        $mT250 = mysql_fetch_array($T250);
        $TotalPorcentagemIPV = $mT250['AliquotasIPV'];

        //Buscando as aliquotas digitadas na tela de montagem do icf no orçamento
        $T250icf = mysql_query("SELECT sum(T250_Aliquota-T250_Aliquota_Original) as AliquotasICF from T250 WHERE T250_T003_Id = '$T003_Id' AND T250_Flag_Altera_Preco='S' AND T250_Grupo='ICF'");
        $mT250icf = mysql_fetch_array($T250icf);
        $TotalPorcentagemICF = $mT250icf['AliquotasICF'];
        log("Porcentagem :" . $TotalPorcentagemICF);

        $T004 = mysql_query("SELECT D009_Valor_Custo_Unitario,
                                    D009_ICF_1,
                                    D049_Origem_Mercadoria,
                                    D009_T001_Id,
                                    D009_Id,
                                    D049_Id,
                                    T003_Flag_Lista_Preco,
                                    T003_D024_Id,
                                    T003_Flag_ACP,
                                    T004_D006_Id,
                                    T004_Aliquota_ICMS,
                                    T004_Aliquota_PIS,
                                    T004_Aliquota_COFINS,
                                    T004_Aliquota_IPI,
                                    D009_T001_Id,
                                    T004_T219_Id,
                                    T004_Id,
                                    T219_Flag_Comprar,
                                    T004_Flag_Origem_Produto,
                                    T004_D005_Id,
                                    T004_D006_Id,
                                    T004_T003_Id,
                                    T219_Flag_Origem_Produto,
                                    T219_Valor_Custo_Unitario,
                                    T004_ST_VA,
                                    T004_Percentual_Reducao_ICMS_ST,
                                    T004_ST_ICMS_Interno,
                                    T004_Percentual_Reducao_ICMS,
                                    T004_ICMS_Inter_Estadual,
                                    T004_Aliquota_IRPJ,
                                    T004_Aliquota_CSLL,
                                    T004_Aliquota_CSLL,
                                    T004_Flag_Transferencia,
                                    D009_Quantidade_Estoque_Liquido,
                                    T004_Quantidade,
                                    T004_C004_Id_Transferencia,
                                    T004_Valor_Preco_Original,
                                    T004_Valor_Custo_Unitario,
                                    T004_Valor_Preco_Tabela,
                                    T004_Valor_Preco_Transferencia,
                                    T004_T001_Id,
                                    T006_T219_Id,
                                    T219_T218_Id,
                                    IF(IFNULL(T003_Flag_Frete, 0) = 0, T004_Valor_Frete_Unitario, 0) AS T004_Valor_Frete_Unitario,
                                    T003A_Flag_Ratear_Frete_Custo,
                                    T003_Casas_Decimais_Preco
                                    FROM T004
                                LEFT JOIN T006 ON T006_T004_Id=T004_Id
                                LEFT JOIN D009 ON D009_Id=T004_D009_Id
                                LEFT JOIN D049 ON D049_Id=D009_D049_Id
                                LEFT JOIN T003 ON T003_Id=T004_T003_Id
                                LEFT JOIN T001 ON T001_Id=D009_T001_Id
                                LEFT JOIN T219 ON T219_Id=T004_T219_Id
                                LEFT JOIN T003A ON T003_Id=T003A_T003_Id
                                    WHERE T004_T003_Id='{$T003_Id}'
                                      AND (T006_Id IS NULL OR (IFNULL(T004_T219_Id,0) <= 0 AND T004_Flag_Comprar = 'S'))");


        if(mysql_num_rows($T004) > 0){
            while($mT004 = mysql_fetch_array($T004)) {
               $retorno = $CAD002->atualizaPrecoTabelaOrcamentoSmart($mT004, true,$TotalPorcentagemIPV,$TotalPorcentagemICF);

               if($retorno){
                    $CAD002->atualizaPrecoTabelaOrcamento($mT004, true,$TotalPorcentagemIPV,$TotalPorcentagemICF);
               }
                mysql_query("CALL T004_Gravar_Totalizacao_4('{$mT004['T004_Id']}')");
            }
            $resposta['code'] = true;
        } else {
            $resposta['data'] = "Não foi possível atualizar o preço tabela dos produtos, verifique se os produtos já geraram pedido.";
            $resposta['code'] = false;
        }
        return $resposta;
    
    }

    public function campoFocusFormCampoProcuraProduto() {
        global $g;
        return $g['C031']['editarDescricaoProdutoVenda']=='S' ? 'T004_Descricao_Produto' : 'T004_Quantidade';
    }

    /**
     * verificaEnviaEmailAutomatico
     *
     * @param String $T003_Id
     * @param int $verificaEnviaEmailAutomatico
     * @return bool
     */  
    public function verificaEnviaEmailAutomatico($T003_Id) 
    {
        // funcao chamada no Crontab      
        global $g;
		
        $T003    = mysql_query("SELECT SUM(T089_Valor_Parcela) as TotalParcelas, 
                                       T003_Valor_Total,
                                       D006_Flag_Gera_Contas
                                  FROM T003 
                             LEFT JOIN T089 ON T003_Id = T089_T003_Id 
                             LEFT JOIN D006 on D006_Id = T003_D006_Id
                                 WHERE T003_Id='{$T003_Id}'");
        $mT003 = mysql_fetch_array($T003);

        if($mT003['D006_Flag_Gera_Contas'] == 'R'){

            if($mT003['TotalParcelas'] == 0){
                return "Está sem forma de pagamento";
            }

            if($mT003['T003_Valor_Total'] != $mT003['TotalParcelas']){
                return "Valor das parcelas não confere com o total do orçamento.";
            }

        }

        if($g['C031']['verificaAlcadaOrcamentoEmailImpressao'] == 'S'){
            $this->verificaAlcadaNovo($T003_Id, false, false);
        }

        $T003  = mysql_query("SELECT T003_Flag_Status_Liberacao FROM T003 WHERE T003_Id='{$T003_Id}'");
        $mT003 = mysql_fetch_array($T003);

        if($mT003['T003_Flag_Status_Liberacao'] == 4){
            return "Caiu na análise e precisa de maior alçada";
        }

        return true;
    }

    /**
     * enviarMultiEmailBoleto
     *
     * @param String $T003_Id
     * @param int $enviaEmailAutomatico
     * @return bool
     */     
    public function enviaEmailAutomatico($T003_Id, $aposEmissao = false, $antesVencer = false)
    {
        // funcao chamada no Crontab
        global $g, $confUsuario;
        require_once("bibliotecas/phpmailer/class.phpmailer.php");
        $C007 = mysql_query("SELECT * FROM C007 WHERE C007_Id = '{$g['usuarioAtual']}';");
        $C007 = mysql_fetch_assoc($C007);

        if (empty($C007) || empty($C007['C007_Email_SMTP_Servidor'])) {
            return "Não está definido o email no usuario.";
        }

        // Problema para anexar o PDF, no crontab pega o confUsuario e com isso da problema na hora de criar o PDF do orçamento na funcao montarEmailPdf
        $monta_ = $this->montarEmailPdf($T003_Id);

        //$monta_ = $this->montarEmail($T003_Id);

        if (empty($monta_['Para'])) {
            return "Email do cadastro do cliente deve estar preenchido.";
        }
        
        $corpo = '';
        if($aposEmissao){
            $corpo = $this->corpoEmailEnvioAutomatico($T003_Id,true,false); 

        } else if($antesVencer){
            $corpo = $this->corpoEmailEnvioAutomatico($T003_Id,false,true);
        } 

        if(!empty($corpo)){
            $monta_['Corpo'] = $corpo;
        } 
        

        if (!empty($C007['C007_Email_Assinatura']) && trim($C007['C007_Email_Assinatura']) != '<br>') {
            $monta_['Corpo'] .= "<br /><br />{$C007['C007_Email_Assinatura']}";
        }        
               
        $mail           = new \phpmailer;
        // Mail
        $mail->From     = $C007['C007_Email'];
        $mail->FromName = $C007['C007_Primeiro_Nome'];
        $mail->Host     = $C007['C007_Email_SMTP_Servidor'];
        $mail->Mailer   = "smtp";
        $mail->SMTPAuth = $C007['C007_Email_SMTP_Autenticacao'];
        $mail->Username = $C007['C007_Email_SMTP_Usuario'];
        $mail->Password = $C007['C007_Email_SMTP_Senha'];
        $mail->Subject  = '=?UTF-8?B?'.base64_encode($monta_['Assunto']).'?=';
        $mail->Body     = $monta_['Corpo'];
        $mail->AltBody  = "ALT BODY";
        $mail->CharSet  = 'utf-8';
        $mail->SetLanguage("br");
        $mail->IsHTML(true);

        if ($C007['C007_Email_SMTP_Servidor'] == "smtp.gmail.com")
        {
            $mail->SMTPSecure   = "ssl";
            $mail->Port         = "465";
        }
        if ($C007['C007_Email_SMTP_Servidor'] == "smtp.zoho.com")
        {
            $mail->SMTPSecurity = "ssl";
            $mail->SMTPSecure   = "ssl";
            $mail->Port         = "465";
        }
        if ($C007['C007_Email_SMTP_Servidor'] == "smtp.live.com")
        {
            $mail->SMTPSecure   = "ssl";
        }
        if ($C007['C007_Email_SMTP_Servidor'] == "smtp.office365.com")
        {
            $mail->SMTPSecure   = "tls";
            $mail->SMTPSecurity = 'tls';
        }
        if (strpos($C007['C007_Email_SMTP_Servidor'], "istyle.com.br") !== false)
        {
            $mail->SMTPSecure   = "tls";
        }
        if (!empty($C007['C007_Email_SMTP_Porta']))
        {
            $mail->Port         = $C007['C007_Email_SMTP_Porta']; 
        }
        if (strpos($C007['C007_Email_SMTP_Servidor'], 'yahoo') !== false || strpos($C007['C007_Email_SMTP_Servidor'], 'comercialdex') !== false)
        {
            $mail->SMTPSecure   = "tls";
        }
        if(strpos($C007['C007_Email_SMTP_Servidor'], 'gpimports') !== false or strpos($C007['C007_Email_SMTP_Servidor'], '.gp') !== false) {
                $mail->SMTPSecure = "tls";
                $mail->SMTPSecurity = 'tls';
                //$mail->Port = '465';
        }
        if ($C007['C007_Email']=='nfe@hardness.com.br') {
                $mail->SMTPSecure = "ssl";
        }
        if (strpos($C007['C007_Email_SMTP_Servidor'],"nacomaq") !== false)
        {
            $mail->SMTPSecure   = "ssl";
            $mail->Port         = "465";
        }
        if (strpos($C007['C007_Email_SMTP_Servidor'],"smtp.promotec.ind.br") !== false)
        {
            $mail->Port         = "587";
        }
        if (empty($monta_['Para']) && !empty($monta_['cc'])) {
            $monta_['Para'] = $monta_['cc'];
            $monta_['cc']   = '';
        }
        $todosEmailsEnviados = array();
        $mPara               = preg_split("/[;,]+/", $monta_['Para']);
        for ($x = 0; $x < count($mPara); $x++) {
            $email = $mPara[$x];
            if (strpos($email, '<') !== false) {
                $email = explode('<', $email);
                $email = explode('>', $email[1]);
                $email = $email[0];
            }
            $todosEmailsEnviados[] = strtolower(trim($email));
            $mail->AddAddress($email);
        }
        if (isset($monta_['cc'])) {
            $mPara = preg_split("/[;,]+/", $monta_['cc']);
            for ($x = 0; $x < count($mPara); $x++) {
                $email = $mPara[$x];
                if (strpos($email, '<') !== false) {
                    $email = explode('<', $email);
                    $email = explode('>', $email[1]);
                    $email = $email[0];
                }
                $todosEmailsEnviados[] = strtolower(trim($email));
                $mail->AddCC($email);
            }
        }

        $anexosGravar = array();
        $T139_Estrutura = array('anexosManual' => array());
        foreach ($monta_['Anexos'] as $path) {
            if (isset($monta_['AnexosWeb'])) {
                $anexosGravar[]                   = $path;
                $T139_Estrutura['anexosManual'][] = basename($path);
                $mail->AddAttachment($path, basename($path));
            }
        } 
         
        if ($mail->Send()) {
            //mysql_query("UPDATE T003 SET T117_Data_Envio_Email=now(), T117_Flag_Atualizar = 'N' WHERE T117_Id='{$T117_Id}'");
            
            // Gravar como e-mail enviado
            $T139_De        = mysql_real_escape_string("\"{$C007['C007_Primeiro_Nome']}\" <{$C007['C007_Email']}>");
            $T139_Estrutura = serialize($T139_Estrutura);
            $T139_Para      = !empty($monta_['cc']) ? ($monta_['Para'] . '; ' . $monta_['cc']) : $monta_['Para'];

            $sql =            "INSERT INTO T139 (T139_Pasta, 
                                                 T139_Uid,
                                                 T139_Titulo,
                                                 T139_De,
                                                 T139_Para, 
                                                 T139_Data_Hora, 
                                                 T139_Estrutura,
                                                 T139_Mensagem_Corpo, 
                                                 T139_Flag_Lido, 
                                                 T139_D024_Id, 
                                                 T139_T003_Id, 
                                                 T139_C007_Id                                                 
                                       ) VALUES (
                                                 'S',
                                                 '0',
                                                 '".mysql_real_escape_string($monta_['Assunto'])."',
                                                 '{$T139_De}',
                                                 '".mysql_real_escape_string($T139_Para)."',
                                                 '".date('Y-m-d H:i:s')."',
                                                 '" . mysql_real_escape_string($T139_Estrutura) . "',
                                                 '".mysql_real_escape_string($corpo)."',
                                                 '1',
                                                 '".mysql_real_escape_string($mT003['T003_D024_Id'])."',
                                                 '".mysql_real_escape_string($T003_Id)."',
                                                 '".mysql_real_escape_string($g['usuarioAtual'])."'
                                                );";

            mysqli_query($sql);
            $g['mysqlLastId'] = mysql_insert_id();
            
            // Salvar anexos
            $dir = $g['pathDados'] . 'anexosEmail/';
            is_dir($dir) or mkdir($dir, 0777, true);
            foreach ($anexosGravar as $key => $anexo) {
                $arquivo = "{$dir}{$g['mysqlLastId']}-{$key}";
                copy($anexo, $arquivo);
            }  

             if (isset($monta_['Anexos'])) {
                foreach ($monta_['Anexos'] as $path) {
                    gDeletarArquivo($path);
                }
            }  
            
            return true;

        } else {
            $cErro=str_replace(array("'",'"',"\r","\n"),"",$mail->ErrorInfo);
            $cErro=str_replace(array("<p>","</p>"),"<br />",$cErro);
            $cErro=$cErro . ' ' . $C007['C007_Email_SMTP_Usuario'];
            return $cErro;
        }
    }

    /**
     * corpoEmailEnvioAutomatico
     *
     * @param array $mT003
     * @param int $corpoEmailEnvioAutomatico
     * @return string
     */     
    public function corpoEmailEnvioAutomatico($T003_Id, $aposEmissao = false, $antesVencer = false)
    {
        global $g;

        // Informaçoes para montar o corpo do email
        $T003 = mysql_query("SELECT T003_Id,
                                    T003_Data_Emissao,
                                    D024_Nome_Empresa,
                                    D013_Nome_Contato
                               FROM T003
                          LEFT JOIN D024 ON D024_Id = T003_D024_Id
                          LEFT JOIN D013 ON D013_Id = T003_D013_Id
                              WHERE T003_Id = '{$T003_Id}'");

        $mT003 = mysql_fetch_assoc($T003);

        $dataEmissao = gCorrigeData($mT003['T003_Data_Emissao']);
        if($mT003['D013_Nome_Contato'] != ''){
            $contatoEmpresa = "Prezado {$mT003['D013_Nome_Contato']}/{$mT003['D024_Nome_Empresa']},<br><br>";
        } else {
            $contatoEmpresa = "Prezado {$mT003['D024_Nome_Empresa']},<br><br>";
        }

        if($aposEmissao){
            $corpoEmail = "<font style='font-size:16px'>{$contatoEmpresa}
                           A respeito do seu Orçamento <b>Nº {$mT003['T003_Id']}</b> feito no dia <b>{$dataEmissao}</b>,<br>Caso tenha alguma dúvida ou precise de mais informações estamos disponíveis para te ajudar.</font>";
        } elseif($antesVencer) {
            $corpoEmail = "<font style='font-size:16px'>{$contatoEmpresa}
                           A respeito do seu Orçamento <b>Nº {$mT003['T003_Id']}</b> feito no dia <b>{$dataEmissao}</b>,<br>Ele irá expirar em breve, para garantir suas condições favor nos dê um retorno.</font>";
        }

        return $corpoEmail;

    }

    /**
     * acoesFinaisGerarPedido
     *
     * @param int T003_Id
     * @return boolean
     */     
    
    public function acoesFinaisGerarPedido($T003_Id)
    {
        global $g;

        // Retorna true, essa função é personalizada de acordo com a aplicação
        // 1º utilizacação APP resposta rapida cotação compra - Felipe 21/10/2022

        return true;

    }

    /**
     * excessoesValidaCabecalho
     *
     * @param array
     * @return boolean
     */     
    
     public function excecoesValidaT003($mT003)
     {
         global $g;
 
         // Retorna true, essa função é personalizada por cliente
        return true;
 
     }

    /**
     * excessoesAliquotas
     *
     * @param array
     * @return referencia
     */     
     public function excessoesAliquotas(&$mProduto,&$mCab,&$mD006){
        global $g;

     }
    
     public function gerarAtividadeItemExlusao($T003_Id) {
        global $g;

        $gerarAtividade = $g['C031']['gerarAtividadesManipulacaoItens'] == 'S';
         if ($gerarAtividade) {
            $listaAtividade = '';
            $atividade      = '';

            $sql  = "SELECT T004_Id, ";
            $sql .=       " T004_T003_Id, ";
            $sql .=       " T004_Item, ";
            $sql .=       " T004_Codigo_Produto, ";
            $sql .=       " T004_Descricao_Produto, ";
            $sql .=       " T004_Quantidade, ";
            $sql .=       " T004_Percentual_Desconto, ";
            $sql .=       " T004_Valor_Preco_Bruto ";
            $sql .=   " FROM T004 ";
            $sql .=  " WHERE T004_T003_Id      = '{$T003_Id}' ";
            $sql .=    " AND T004_Flag_Excluir = 1";
            $query = mysql_query($sql);

            while($row = mysql_fetch_Array($query)){
                $T003_Id = $row['T004_T003_Id'];
                $listaAtividade .= "<tr>";
                $listaAtividade .=   "<td style='text-align: center; padding: 0px 5px 0px 5px;'>{$row['T004_Item']}</td>";
                $listaAtividade .=   "<td style='text-align: left;   padding: 0px 5px 0px 5px;'>{$row['T004_Codigo_Produto']} - {$row['T004_Descricao_Produto']}</td>";
                $listaAtividade .=   "<td style='text-align: right;  padding: 0px 5px 0px 5px;'>". gCorrigeNumero($row['T004_Quantidade']) . "</td>";
                $listaAtividade .=   "<td style='text-align: right;  padding: 0px 5px 0px 5px;'>". gCorrigeNumero($row['T004_Valor_Preco_Bruto']) . "</td>";
                $listaAtividade .=   "<td style='text-align: right;  padding: 0px 5px 0px 5px;'>". gCorrigeNumero($row['T004_Percentual_Desconto']) . "</td>";
                $listaAtividade .= "</tr>";
            }

            if (!empty($listaAtividade)) {
                $atividade .= '<div style="background-color: #ff7b47;">';
                $atividade .=   "<span style='padding: 5px 5px 5px 5px;'>Exclusão de item</span>";
                $atividade .=   "<table style='width:100%'>";
                $atividade .=     "<tr>";
                $atividade .=       "<th style='text-align: center; padding: 0px 5px 0px 5px;'>It</th>";
                $atividade .=       "<th style='text-align: left;   padding: 0px 5px 0px 5px;'>Produto</th>";
                $atividade .=       "<th style='text-align: right;  padding: 0px 5px 0px 5px;'>Qtde</th>";
                $atividade .=       "<th style='text-align: right;  padding: 0px 5px 0px 5px;'>Preço</th>";
                $atividade .=       "<th style='text-align: right;  padding: 0px 5px 0px 5px;'>% Desc</th>";
                $atividade .=     "</tr>";
                $atividade .=     $listaAtividade;
                $atividade .=   "</table>";
                $atividade .= "</div>";
            }

            $atividade = mysql_real_escape_string($atividade);

            $sqlT259_Id   = " SELECT D085_T259_Id ";
            $sqlT259_Id  .=   " FROM D085 ";
            $sqlT259_Id  .=  " WHERE D085_T003_Id = {$T003_Id} ";
            $sqlT259_Id  .=  " ORDER BY D085_Id ASC ";
            $sqlT259_Id  .=  " LIMIT 1 "; 
            $queryT259_Id = mysql_query($sqlT259_Id);
            $rowT259_Id   = mysql_fetch_assoc($queryT259_Id);
            $D085_T259_Id = $rowT259_Id['D085_T259_Id'];

            $sqlInsert  = " INSERT INTO d085 ( ";
            $sqlInsert .=   " D085_C007_Id, ";
            $sqlInsert .=   " D085_Data, ";
            $sqlInsert .=   " D085_Hora, ";
            $sqlInsert .=   " D085_Mensagem, ";
            $sqlInsert .=   " D085_C007_Id_Inclusao, ";
            $sqlInsert .=   " D085_T003_Id, ";
            $sqlInsert .=   " D085_T259_Id, ";
            $sqlInsert .=   " D085_Flag_Tipo, ";
            $sqlInsert .=   " D085_Flag_Lido, ";
            $sqlInsert .=   " D085_Flag_Manual ";
            $sqlInsert .= " ) VALUES ( ";
            $sqlInsert .=   " '{$g['usuarioAtual']}', ";
            $sqlInsert .=   " current_date(), ";
            $sqlInsert .=   " current_time(), ";
            $sqlInsert .=   " '$atividade', ";
            $sqlInsert .=   " '{$g['usuarioAtual']}', ";
            $sqlInsert .=   " '{$T003_Id}', ";
            $sqlInsert .=   " '{$D085_T259_Id}', ";
            $sqlInsert .=   " 2, ";
            $sqlInsert .=   " 'N', ";
            $sqlInsert .=   " 'N' ";
            $sqlInsert .= " ); ";

            mysql_query($sqlInsert);
         }
     }

     public function gerarAtividadeItemEdicao($T004_Id, $campoEditado, $valorAntigo, $valorNovo) {
        global $g;

        $gerarAtividade = $g['C031']['gerarAtividadesManipulacaoItens'] == 'S';
         if ($gerarAtividade) {
            $prefixo = '';
            $sufixo  = '';
            switch ($campoEditado) {
                case "T004_Quantidade":
                    $campoLocalizado = 'Quantidade';
                    break;
                case "T004A_Quantidade_Conversao":
                    $campoLocalizado = 'Qtde Conversão';
                    break;
                case "T004_Valor_Preco_Sem_Desconto_Unitario":
                    $campoLocalizado = 'Preço';
                    $prefixo         = 'R$ ';
                  break;
                case "T004_Valor_Preco_Unitario":
                    $campoLocalizado = 'Preço';
                    $prefixo         = 'R$ ';
                    break;
                case "T004_Percentual_Desconto":
                    $campoLocalizado = '% Desconto';
                    $sufixo          = ' %';
                  break;
                case "T004_Percentual_Desconto_Bruto":
                    $campoLocalizado = '% Desconto';
                    $sufixo          = ' %';
                    break;
                default:
                    $campoLocalizado = '';
              }

            if (!empty($campoLocalizado)) {
                $atividade = '';

                $sql  = "SELECT T004_Id, ";
                $sql .=       " T004_T003_Id, ";
                $sql .=       " T004_Item, ";
                $sql .=       " T004_Codigo_Produto, ";
                $sql .=       " T004_Descricao_Produto, ";
                $sql .=       " T004_Quantidade, ";
                $sql .=       " T004_Percentual_Desconto, ";
                $sql .=       " T004_Valor_Preco_Bruto ";
                $sql .=   " FROM T004 ";
                $sql .=  " WHERE T004_Id = '{$T004_Id}' ";
                $query = mysql_query($sql);
                $dados = mysql_fetch_Array($query);
                $T003_Id = $dados['T004_T003_Id'];

                $atividade .= '<div style="background-color: yellow;">';
                $atividade .=   "<span style='padding: 5px 5px 5px 5px;'>Edição de item</span>";
                $atividade .=   "<table style='width:100%'>";
                $atividade .=     "<tr>";
                $atividade .=       "<th style='text-align: center; padding: 0px 5px 0px 5px;'>It</th>";
                $atividade .=       "<th style='text-align: left;   padding: 0px 5px 0px 5px;'>Produto</th>";
                $atividade .=       "<th style='text-align: right;  padding: 0px 5px 0px 5px;'>Campo alterado</th>";
                $atividade .=       "<th style='text-align: right;  padding: 0px 5px 0px 5px;'>Valor antigo</th>";
                $atividade .=       "<th style='text-align: right;  padding: 0px 5px 0px 5px;'>Valor novo</th>";
                $atividade .=     "</tr>";
                $atividade .=     "<tr>";
                $atividade .=       "<td style='text-align: center; padding: 0px 5px 0px 5px;'>{$dados['T004_Item']}</td>";
                $atividade .=       "<td style='text-align: left;   padding: 0px 5px 0px 5px;'>{$dados['T004_Codigo_Produto']} - {$dados['T004_Descricao_Produto']}</td>";
                $atividade .=       "<td style='text-align: right;  padding: 0px 5px 0px 5px;'>". $campoLocalizado . "</td>";
                $atividade .=       "<td style='text-align: right;  padding: 0px 5px 0px 5px;'>". $prefixo . $valorAntigo . $sufixo . "</td>";
                $atividade .=       "<td style='text-align: right;  padding: 0px 5px 0px 5px;'>". $prefixo . $valorNovo   . $sufixo . "</td>";
                $atividade .=     "</tr>";
                $atividade .=   "</table>";
                $atividade .= "</div>";
    
                $atividade = mysql_real_escape_string($atividade);
    
                $sqlT259_Id   = " SELECT D085_T259_Id ";
                $sqlT259_Id  .=   " FROM D085 ";
                $sqlT259_Id  .=  " WHERE D085_T003_Id = {$T003_Id} ";
                $sqlT259_Id  .=  " ORDER BY D085_Id ASC ";
                $sqlT259_Id  .=  " LIMIT 1 "; 
                $queryT259_Id = mysql_query($sqlT259_Id);
                $rowT259_Id   = mysql_fetch_assoc($queryT259_Id);
                $D085_T259_Id = $rowT259_Id['D085_T259_Id'];
    
                $sqlInsert  = " INSERT INTO d085 ( ";
                $sqlInsert .=   " D085_C007_Id, ";
                $sqlInsert .=   " D085_Data, ";
                $sqlInsert .=   " D085_Hora, ";
                $sqlInsert .=   " D085_Mensagem, ";
                $sqlInsert .=   " D085_C007_Id_Inclusao, ";
                $sqlInsert .=   " D085_T003_Id, ";
                $sqlInsert .=   " D085_T259_Id, ";
                $sqlInsert .=   " D085_Flag_Tipo, ";
                $sqlInsert .=   " D085_Flag_Lido, ";
                $sqlInsert .=   " D085_Flag_Manual ";
                $sqlInsert .= " ) VALUES ( ";
                $sqlInsert .=   " '{$g['usuarioAtual']}', ";
                $sqlInsert .=   " current_date(), ";
                $sqlInsert .=   " current_time(), ";
                $sqlInsert .=   " '$atividade', ";
                $sqlInsert .=   " '{$g['usuarioAtual']}', ";
                $sqlInsert .=   " '{$T003_Id}', ";
                $sqlInsert .=   " '{$D085_T259_Id}', ";
                $sqlInsert .=   " 2, ";
                $sqlInsert .=   " 'N', ";
                $sqlInsert .=   " 'N' ";
                $sqlInsert .= " ); ";
    
                mysql_query($sqlInsert);
            }
         }
     }

    public function aplicarQuantidade($T004_Id, $campoEditado, $valorAntigo, $novoValor) {
        global $g;

        $utilizaQuantidadeConversao = $g['C031']['utilizaQuantidadeConversao'] === 'S';
        if (!$utilizaQuantidadeConversao) {
            return true;
        }

        if ($T004_Id <= 0) {
            return "T004_Id nao existe!";
        }
        
        if (empty($campoEditado)) {
            return "Campo_Editado nao existe!";
        }

        $sql  = " SELECT T004_T003_Id, ";
        $sql .=        " T004_Quantidade, ";
        $sql .=        " T004_Descricao_Produto, ";
        $sql .=        " T004A_Id, ";
        $sql .=        " T004A_Quantidade_Conversao, ";
        $sql .=        " D001_Id, ";
        $sql .=        " D001_Peso_Unitario_Kg, ";
        $sql .=        " D002A_Utiliza_Conversao ";
        $sql .=   " FROM T004 ";
        $sql .=        " LEFT JOIN T004A ON T004_Id = T004A_T004_Id ";
        $sql .=        " LEFT JOIN D009  ON D009_Id = T004_D009_Id ";
        $sql .=        " LEFT JOIN D049  ON D049_Id = D009_D049_Id ";
        $sql .=        " LEFT JOIN D001  ON D001_Id = D049_D001_Id ";
        $sql .=        " LEFT JOIN D002  ON D002_Id = D001_D002_Id ";
        $sql .=        " LEFT JOIN D002A ON D002_Id = D002A_D002_Id ";
        $sql .=  " WHERE T004_Id = '$T004_Id' ";
        $query  = mysql_query("$sql");
        $dados  = mysql_fetch_array($query);

        $usaConversao = $dados['D002A_Utiliza_Conversao'] === 'S';
        $T003_Id      = $dados['T004_T003_Id'];

        if ($campoEditado === 'T004_Quantidade') {
            if (!$usaConversao) {
                $sqlUpdate  = " UPDATE T004A SET ";
                $sqlUpdate .=   " T004A_Quantidade_Conversao = {$dados['T004_Quantidade']} ";
                $sqlUpdate .= " WHERE T004A_Id = {$dados['T004A_Id']} ";
                mysql_query($sqlUpdate);
            }
        }

        if ($campoEditado === 'T004A_Quantidade_Conversao') {
            if ($usaConversao) {
                $quantidade = $dados['T004A_Quantidade_Conversao'] * $dados['D001_Peso_Unitario_Kg'];

                require_once('bibliotecas/classes/SMTProduto.php');
                $SMTProduto = new SMTProduto();
                $T004_Descricao_Produto = $SMTProduto->descricaoConversao($dados['T004_Descricao_Produto'], $dados['T004A_Quantidade_Conversao'], $dados['D001_Id']);
                $T004_Descricao_Produto = mysql_real_escape_string($T004_Descricao_Produto);

                $sqlUpdate  = " UPDATE T004 SET ";
                $sqlUpdate .=   " T004_Quantidade        = '{$quantidade}', ";
                $sqlUpdate .=   " T004_Descricao_Produto = '{$T004_Descricao_Produto}' ";
                $sqlUpdate .= " WHERE T004_Id = '{$T004_Id}' ";
                mysql_query($sqlUpdate);
                $cErro   = mysql_error();
            }
        }

        mysql_query("call T004_Gravar_Totalizacao_4('$T004_Id');");
        mysql_query("call T003_Gravar_Totalizacao_4('$T003_Id');");

        return true;
    }

    public function gravarQuantidadePesquisaGridOrcamento($D024_Id, $D009_Id, $campoEditado, $novoValor) {
        global $g;

        if (empty($D024_Id) || empty($D009_Id)) {
            return 'Erro ao alterar a quantidade, por favor entre em contato com o suporte técnico!';
        }

        $quantidade    = gCorrigeNumeroInverte($novoValor);
        $qtdeConversao = $quantidade;

        $utilizaQuantidadeConversao = $g['C031']['utilizaQuantidadeConversao'] === 'S';
        if ($utilizaQuantidadeConversao) {
            $sqlProduto  = " SELECT D001_Id, ";
            $sqlProduto .=        " D001_Peso_Unitario_Kg, ";
            $sqlProduto .=        " D002A_Utiliza_Conversao ";
            $sqlProduto .=   " FROM D009 ";
            $sqlProduto .=        " LEFT JOIN D001  ON D001_Id = D009_D001_Id ";
            $sqlProduto .=        " LEFT JOIN D002  ON D002_Id = D001_D002_Id ";
            $sqlProduto .=        " LEFT JOIN D002A ON D002_Id = D002A_D002_Id ";
            $sqlProduto .=  " WHERE D009_Id = '$D009_Id' ";
            $queryProduto  = mysql_query($sqlProduto);
            $dadosProduto  = mysql_fetch_array($queryProduto);

            $usaConversao = $dadosProduto['D002A_Utiliza_Conversao'] === 'S';

            if ($campoEditado === 'T198_Quantidade') {
                if ($usaConversao) {
                    return 'Este produto utiliza conversão e não é possivel alterar sua quantidade.<br><br>Edite o campo da quantidade conversão.';
                }
            }

            if ($campoEditado === 'T198_Quantidade_Conversao') {
                if ($usaConversao) {
                    $quantidade    = gCorrigeNumeroInverte(gCorrigeNumero($quantidade * $dadosProduto['D001_Peso_Unitario_Kg']));
                    $qtdeConversao = gCorrigeNumeroInverte($novoValor);
                } else {
                    return 'Este produto não utiliza conversão e não é possivel alterar sua quantidade de conversão.<br><br>Edite o campo de quantidade.';
                }
            }
        }

        if ($quantidade > 0) {
            $sqlConsulta  = " SELECT T198_Id ";
            $sqlConsulta .=   " FROM T198 ";
            $sqlConsulta .=  " WHERE T198_D009_Id = '{$D009_Id}' ";
            $sqlConsulta .=    " AND T198_D024_Id = '{$D024_Id}' ";
            $sqlConsulta .=    " AND T198_C007_Id = '{$g['usuarioAtual']}' ";
            $queryConsulta  = mysql_query($sqlConsulta);
            $dadoConsulta  = mysql_fetch_array($queryConsulta);

            if (!empty($dadoConsulta['T198_Id'])) {
                $sqlUpdate  = " UPDATE T198 SET ";
                $sqlUpdate .=   " T198_Quantidade           = '{$quantidade}', ";
                $sqlUpdate .=   " T198_Quantidade_Conversao = '{$qtdeConversao}' ";
                $sqlUpdate .= " WHERE T198_Id = '{$dadoConsulta['T198_Id']}' ";
                mysql_query($sqlUpdate);
            } else {
                $sqlInsert  = " INSERT INTO T198 ( ";
                $sqlInsert .=   " T198_D024_Id, ";
                $sqlInsert .=   " T198_D009_Id, ";
                $sqlInsert .=   " T198_C007_Id, ";
                $sqlInsert .=   " T198_Quantidade, ";
                $sqlInsert .=   " T198_Quantidade_Conversao ";
                $sqlInsert .= " ) VALUES ( ";
                $sqlInsert .=   " '{$D024_Id}', ";
                $sqlInsert .=   " '{$D009_Id}', ";
                $sqlInsert .=   " '{$g['usuarioAtual']}', ";
                $sqlInsert .=   " '{$quantidade}', ";
                $sqlInsert .=   " '{$qtdeConversao}' ";
                $sqlInsert .= " ) ";
                mysql_query($sqlInsert);
            }
        }

        return true;
    }

    /* Função de personalização que faz uma verificação se é possível aplicar desconto caso
        VerificaDesconto retorna false -> é possível dar desconto
        VerificaDesconto retorna string -> não é possível dar desconto(aparece mensagem na tela) */
    public function VerificaDesconto($arrayParam){return false;}

    /* Função trigger que substitui os valores que vão ser preenchidos
    no formulário de inserção de produto, serve somente para personalização */
    public function ven001ModPreencheFormProdutosOrcamento($row, $params = false){return $row;}

    /* Função trigger que substitui os valores que vão ser preenchidos
    no formulário de inserção de produto, serve somente para personalização */
    public function ven001ModPreencheFormProdutosNota($row, $params = false){return $row;}

    /* Função trigger que substitui o porcentagem desconto para as observações do orçamento */
    public function ven001ModObservacaoPercentualDesconto($percentualDesconto, $arrayInfo = false){return $percentualDesconto;}

    /**
     * verificaDescontoClubeBeneficio
     *
     * @param varchar tabelaCabecalho
     * @param int idProduto
     * @return decimal
     */     
    public function verificaDescontoClubeBeneficio($tabelaCabecalho, $idProduto) {
        global $g;

        return 0;
    }

    /**
     * triggerWherePesquisaGrid
     *
     * @param varchar pesquisa
     * @return varchar
     */     
    public function triggerWherePesquisaGrid($pesquisa_item) {
        global $g;

        // Essa função substitui a clausula where montada no pesquisaGrid na busca de produtos em orçamentos e pedidos
        // Wheres fixos que são montados no pesquisaGrid
        // Vendas01 = "WHERE (REPLACE(REPLACE(D001_Codigo_Produto,' ',''),'-','') LIKE REPLACE(REPLACE('{$pesquisa_item}%',' ',''),'-','') OR REPLACE(REPLACE(D083_Codigo_Produto_Fornecedor,' ',''),'-','') LIKE REPLACE(REPLACE('{$pesquisa_item}%',' ',''),'-',''))";
        // Vendas02 = "WHERE ((REPLACE(REPLACE(D001_Codigo_Produto,' ',''),'-','') LIKE REPLACE(REPLACE('%{$pesquisa_item}%',' ',''),'-','') AND REPLACE(REPLACE(D001_Codigo_Produto,' ',''),'-','') NOT LIKE REPLACE(REPLACE('{$pesquisa_item}%',' ',''),'-','')) OR (REPLACE(REPLACE(D083_Codigo_Produto_Fornecedor,' ',''),'-','') LIKE REPLACE(REPLACE('%{$pesquisa_item}%',' ',''),'-','') AND REPLACE(REPLACE(D083_Codigo_Produto_Fornecedor,' ',''),'-','') NOT LIKE REPLACE(REPLACE('{$pesquisa_item}%',' ',''),'-','')))";
        // Vendas03 = "WHERE (REPLACE(REPLACE(D001_Descricao_Produto,' ',''),'-','') LIKE REPLACE(REPLACE('%{$pesquisa_item}%',' ','%'),'-','') OR REPLACE(REPLACE(D001_Descricao_Ingles,' ',''),'-','') LIKE REPLACE(REPLACE('%{$pesquisa_item}%',' ','%'),'-','') OR REPLACE(REPLACE(D082_Marca,' ',''),'-','') LIKE REPLACE(REPLACE('%{$pesquisa_item}%',' ','%'),'-',''))";
        // Vendas04 = "WHERE D001_Codigo_Barras = '{$pesquisa_item}'";

        $caseVendas = array();
        $caseVendas['vendas01'] = '';
        $caseVendas['vendas02'] = '';
        $caseVendas['vendas03'] = '';
        $caseVendas['vendas04'] = '';

        return $caseVendas;
    }

    /**
     * aplicaDescontoCFOP
     *
     * @param varchar pesquisa
     * @return varchar
     */     
    public function aplicaDescontoCFOP($tabelaItem, $tabelaCabecalho, $idItem, $idCabecalho, $destacaDescontoNF) {
        global $g;

        if($destacaDescontoNF == 'S'){

            // Se destacar na nota, vai buscar todos os itens que destacam na NF, fazer o percentual pela quantidade e atualizar o cabeçalho            
            mysqli_query("UPDATE {$tabelaItem}A 
                       LEFT JOIN {$tabelaItem} ON {$tabelaItem}A_{$tabelaItem}_Id = {$tabelaItem}_Id
                       LEFT JOIN D006 ON {$tabelaItem}_D006_Id = D006_Id 
                             SET {$tabelaItem}A_Percentual_Desconto_CFOP = D006_Percentual_Desconto_CFOP
                           WHERE {$tabelaItem}_{$tabelaCabecalho}_Id = '{$idCabecalho}'
                             AND {$tabelaItem}A_Percentual_Desconto_CFOP != D006_Percentual_Desconto_CFOP");

            // Soma os descontos que a CFOP vai destacar na nota
            $descontoCFOP = mysqli_query("SELECT SUM({$tabelaItem}A_Percentual_Desconto_CFOP) as percentualDescontoCFOP,
                                                 COUNT(*) AS totalItens
                                            FROM {$tabelaItem}
                                       LEFT JOIN {$tabelaItem}A ON {$tabelaItem}_Id = {$tabelaItem}A_{$tabelaItem}_Id 
                                       LEFT JOIN D006 ON D006_Id = {$tabelaItem}_D006_Id
                                           WHERE {$tabelaItem}_{$tabelaCabecalho}_Id='{$idCabecalho}'
                                             AND D006_Flag_Destaca_Desconto_NF = 'S'");

            $mDescontoCFOP = mysql_fetch_array($descontoCFOP);

            // Soma o valor de todos os itens, independente da CFOP
            $valorTotal = mysqli_query("SELECT SUM({$tabelaItem}_Valor_Total_Preco) as valorTotalProdutos	
                                          FROM {$tabelaItem}
                                         WHERE {$tabelaItem}_{$tabelaCabecalho}_Id='{$idCabecalho}'");

            $mValorTotal = mysql_fetch_array($valorTotal);

            if($mDescontoCFOP['totalItens'] <= 0){
                // Se foi excluido todos os produtos que tinham desconto, zera no cabeçalho
                $percentualDesconto = 0;
                $valorTotalDesconto = 0;    
            } else {
                $percentualDesconto = $mDescontoCFOP['percentualDescontoCFOP'] / $mDescontoCFOP['totalItens'];
                $valorTotalDesconto = $mValorTotal['valorTotalProdutos'] * ($percentualDesconto / 100);
            }

            if($tabelaCabecalho == 'T003' || $tabelaCabecalho == 'T005'){
                mysqli_query("UPDATE {$tabelaCabecalho} SET {$tabelaCabecalho}_Valor_Desconto_Manual = {$valorTotalDesconto} WHERE {$tabelaCabecalho}_Id = '{$idCabecalho}'");
            } else {
                mysqli_query("UPDATE {$tabelaCabecalho} SET {$tabelaCabecalho}_Valor_Desconto = {$valorTotalDesconto} WHERE {$tabelaCabecalho}_Id = '{$idCabecalho}'");
            }

            mysqli_query("UPDATE {$tabelaCabecalho}A SET {$tabelaCabecalho}A_Percentual_Desconto_CFOP = {$percentualDesconto} WHERE {$tabelaCabecalho}A_{$tabelaCabecalho}_Id = '{$idCabecalho}'");

        } else {
                
            $desconto = mysqli_query("SELECT D006_Percentual_Desconto_CFOP as descontoCFOP,
                                             {$tabelaItem}A_Percentual_Desconto_CFOP as percentualDescontoItemCFOP,
                                             {$tabelaItem}_Valor_Preco_Unitario as valorPrecoUnitario,
                                             {$tabelaItem}_Valor_Preco_Original as valorPrecoOriginal,
                                             {$tabelaItem}_Percentual_Desconto as percentualDescontoItem
                                        FROM {$tabelaItem} 
                                   LEFT JOIN {$tabelaItem}A ON {$tabelaItem}_Id = {$tabelaItem}A_{$tabelaItem}_Id 
                                   LEFT JOIN D006 ON D006_Id = {$tabelaItem}_D006_Id
                                       WHERE {$tabelaItem}_Id = '{$idItem}'");
            $mDesconto = mysql_fetch_array($desconto);          

            if($mDesconto['descontoCFOP'] <= 0 && $mDesconto['percentualDescontoItem'] > 0){
                mysqli_query("UPDATE {$tabelaItem}A SET {$tabelaItem}A_Percentual_Desconto_CFOP = 0 WHERE {$tabelaItem}A_{$tabelaItem}_Id = '{$idItem}'");
                $percentualDesconto = $mDesconto['percentualDescontoItem'] - $mDesconto['percentualDescontoItemCFOP'];

			}elseif($mDesconto['percentualDescontoItem'] < $mDesconto['descontoCFOP']){
				$percentualDesconto = $mDesconto['descontoCFOP'];
			} else {
				$percentualDesconto = $mDesconto['percentualDescontoItem'];
			}

			$valorPrecoUnitario = $mDesconto['valorPrecoOriginal'] - ($mDesconto['valorPrecoOriginal'] * ($percentualDesconto / 100));

            if($tabelaItem == 'T004'){
			    mysqli_query("UPDATE {$tabelaItem} SET {$tabelaItem}_Valor_Preco_Unitario    = '{$valorPrecoUnitario}',
										               {$tabelaItem}_Percentual_Desconto     = '{$percentualDesconto}',
                                                       {$tabelaItem}_Valor_Desconto_Unitario = ({$tabelaItem}_Valor_Preco_Original*($percentualDesconto/100))
									             WHERE {$tabelaItem}_Id = '{$idItem}'");
            } else {
			    mysqli_query("UPDATE {$tabelaItem} SET {$tabelaItem}_Valor_Preco_Sem_Desconto_Unitario = '{$valorPrecoUnitario}',
                                                       {$tabelaItem}_Valor_Preco_Unitario              = {$tabelaItem}_Valor_Preco_Sem_Desconto_Unitario,
										               {$tabelaItem}_Percentual_Desconto               = '{$percentualDesconto}',
                                                       {$tabelaItem}_Valor_Desconto_Unitario           =({$tabelaItem}_Valor_Preco_Original*($percentualDesconto/100))
									             WHERE {$tabelaItem}_Id = '{$idItem}'");
            }

            if($mDesconto['percentualDescontoItemCFOP'] <= 0 && $percentualDesconto){
                mysqli_query("UPDATE {$tabelaItem}A SET {$tabelaItem}A_Percentual_Desconto_CFOP = {$mDesconto['descontoCFOP']} WHERE {$tabelaItem}A_{$tabelaItem}_Id = '{$idItem}' AND {$tabelaItem}A_Percentual_Desconto_CFOP != '{$percentualDesconto}'");
            } 
            
            // Aqui precisa verificar se existe percentual desconto CFOP no cabeçalho, se existir, precisa recalcular,
            // São casos em que o item foi incluído ou alterado o valor
            $descontoCabecalho = mysql_query("SELECT {$tabelaCabecalho}A_Percentual_Desconto_CFOP as percentualDescontoCFOPTotal,
                                                     {$tabelaCabecalho}_Valor_Total_Produtos as valorTotalProdutos
                                                FROM {$tabelaCabecalho}
                                           LEFT JOIN {$tabelaCabecalho}A ON {$tabelaCabecalho}_Id = {$tabelaCabecalho}A_{$tabelaCabecalho}_Id
                                               WHERE {$tabelaCabecalho}_Id = '{$idCabecalho}'");

            $mDescontoCabecalho = mysql_fetch_array($descontoCabecalho);
            if($mDescontoCabecalho['percentualDescontoCFOPTotal'] > 0){
                
                $valorTotalDesconto = $mDescontoCabecalho['valorTotalProdutos'] * ($mDescontoCabecalho['percentualDescontoCFOPTotal'] / 100);               

                if($tabelaCabecalho == 'T003' || $tabelaCabecalho == 'T005'){
                    mysqli_query("UPDATE {$tabelaCabecalho} SET {$tabelaCabecalho}_Valor_Desconto_Manual = {$valorTotalDesconto} WHERE {$tabelaCabecalho}_Id = '{$idCabecalho}'");
                } else {
                    mysqli_query("UPDATE {$tabelaCabecalho} SET {$tabelaCabecalho}_Valor_Desconto = {$valorTotalDesconto} WHERE {$tabelaCabecalho}_Id = '{$idCabecalho}'");
                }
            }
        }

        return true;
    }


    public function aplicaRegraParaDesconto($T003_Id, $T005_Id) {
        global $g;

        $retorno = [
            'msgRegras' => '',
            'msg' => '',
            'msgLiberado' => '',
            'alcada' => ''
        ];
        
        return array(
                false,
                $retorno
            );
    }

    /**
     * locacaoItemValidadeOrcamento
     *
     * @param int D009_Id
     * @return int
     */   
    public function locacaoItemValidadeOrcamento($D009_Id) {
        global $g;

        $T066_Id = 0;
        if($g['C031']['obrigatorioLocacaoItemComValidade'] == 'S'){
            $T066 = mysql_query("SELECT T066_Id,
                                        T066_Quantidade_Estoque_Liquido
                                    FROM T066
                               LEFT JOIN D009 ON D009_Id = T066_D009_Id
                               LEFT JOIN D049 ON D049_Id = D009_D049_Id
                               LEFT JOIN D001 ON D001_Id = D049_D001_Id
                               LEFT JOIN D001A ON D001_Id = D001A_D001_Id
                               LEFT JOIN T066A ON T066_Id = T066A_T066_Id
                                   WHERE T066_D009_Id = '{$D009_Id}'
                                     AND D001A_Flag_Validade = 'S'
                                ORDER BY IF(T066A_Data_Validade = '0000-00-00' OR T066A_Data_Validade IS NULL,'9999-99-99', T066A_Data_Validade) ASC");
            $T066_Id        = 0;
            $T066_Id_Local  = 0;
            $estoqueLocacao = 0;

            while($mT066 = mysql_fetch_array($T066)){

                // Ja seta a primeira locacao com a data de validade mais antiga
                if($T066_Id_Local == 0){
                    $T066_Id_Local  = $mT066['T066_Id'];
                    $estoqueLocacao = $mT066['T066_Quantidade_Estoque_Liquido'];
                }

                // Verifica se a primeira locacao estava com o estoque 0 e se que está vindo tem quantidade de estoque para o orçamento
                if($T066_Id == 0 && $estoqueLocacao <= 0 && $mT066['T066_Quantidade_Estoque_Liquido'] > 0){
                    $T066_Id = $mT066['T066_Id'];
                }
            }

            if($T066_Id == 0){
                $T066_Id = $T066_Id_Local;
            }
        }

        return $T066_Id;
    }

    public function PersonalizacaoTriggerVerificarItemOrcamento($T004_Id, $T004_Quantidade_Confirmada) {
        return true;
    }

    public function PersonalizacaoTriggerVerificaItemOrcamentoEmail($T003_Id){
        return true;
    }

    public function PersonalizacaoTriggerVerificaItemOrcamentoImprimir($T003_Id){
        return true;
    }

    public function acoesFinaisAtualizarOrcamento($T003_Id){
        return true;
    }

    public function opcaoExibirDifalImpressaoOrcamento($mT003) : bool {
        if ($mT003['T003_Valor_Total_Partilha_ICMS_Destino'] > 0) {
            return true;
        }
        return false;
    }

    public function verificaPgtoPorCurvaCliente($T003_Id, $T005_Id){
        global $g;

        return true;
    }

    public function validaPrazoPrecoCotacao($T003_Id){
        global $g;

        // Utilizado no APP  SOLICITA ATUALIZACAO PREÇO COTAÇÃO NO ORÇAMENTO

        return true;
    }
   
    
    public function validaDescontoAplicado($T003_Id){

        global $g;

        // Se utiliza consersao nao validar, vi o cliente Geralfix que utiliza conversao e na query retornava valor mas os calculos estavam certos
        if($g['C031']['utilizaUnidadeConversaoVenda'] == 'S'){
            return true;
        }

        // 22/01/2025 - Felipe Carrano
        // Validação por segurança porque em alguns casos estava zerando o T004_Percentual_Desconto e ajustando o T004_Valor_Preco_Unitario mantendo o valor final do item errado
        // Ainda não foi encontrado o problema

        $validaDesconto = mysql_query("SELECT T004_Codigo_Produto,
                                              T004_Valor_Preco_Original,
                                              T004_Valor_Preco_Unitario,
                                              T004_Percentual_Desconto,
                                              T004_Valor_Desconto_Unitario
                                         FROM T004
                                        WHERE T004_T003_Id = '{$T003_Id}'
                                          AND (T004_Valor_Desconto_Unitario >= 0.05 OR T004_Valor_Desconto_Unitario <= -0.05) 
                                          AND T004_Percentual_Desconto = 0
                                          AND ROUND((T004_Valor_Preco_Unitario * T004_Quantidade),2) != T004_Valor_Total_Preco");
        $retornoDesconto = '';
        while($mValidaDesconto = mysql_fetch_array($validaDesconto)){
            if($mValidaDesconto['T004_Valor_Preco_Original'] > 0){
                $precoCorreto      = gCorrigeNumero($mValidaDesconto['T004_Valor_Preco_Original'] - $mValidaDesconto['T004_Valor_Desconto_Unitario']);
                $percentual        = gCorrigeNumero((1-($precoCorreto/$mValidaDesconto['T004_Valor_Preco_Original'])) * 100);
                $descontoAcrescimo = ($mValidaDesconto['T004_Valor_Desconto_Unitario'] < 0) ? 'Acréscimo' : 'Desconto';
                $descontoUnitario  = ($mValidaDesconto['T004_Valor_Desconto_Unitario'] < 0) ? $mValidaDesconto['T004_Valor_Desconto_Unitario'] * -1 : $mValidaDesconto['T004_Valor_Desconto_Unitario'];
                
                $retornoDesconto .= "<b>Código {$mValidaDesconto['T004_Codigo_Produto']}:</b> Valor atual R$".gCorrigeNumero($mValidaDesconto['T004_Valor_Preco_Unitario'])." / {$descontoAcrescimo} R$ ".gCorrigeNumero($descontoUnitario)." - Valor com {$descontoAcrescimo} : R$ {$precoCorreto} ($percentual%)<br>"; 
            }
        }

        if(!empty($retornoDesconto)){
            return  "Identificamos uma inconsistência nos valores. Os produtos abaixo possuem desconto/acréscimo mas não está sendo aplicado,<br>fazendo com que o valor final do item não esteja fechando. Favor verificar e corrigir conforme necessário.<br><br>".$retornoDesconto;
        }

        return true;
    }

    

    /**
     * associarProduto
     *
     * @param Form $form
     * @return true/string
     */
    public function associarProduto($form)
    {
        global $g;
        $D009_Id = $form->campoValorEnviado('D009_Id');
        $T004_Id = $form->campoValorEnviado('T004_Id');
                
        $cClausula = " select ";
        $cClausula .= " * ";
        $cClausula .= " from D009 ";
        $cClausula .= " left join D049 on D049_Id=D009_D049_Id ";
        $cClausula .= " left join D001 on D001_Id=D049_D001_Id ";
        $cClausula .= " left join D005 on D009_D005_Id(D009_Id)=D005_Id ";
        $cClausula .= " left join D037 on D037_Id=D001_D037_Id ";
        $cClausula .= " where D009_Id='{$D009_Id}' ";

        $T004 = mysql_query("{$cClausula}");
        echo mysql_error();
        $mT004 = mysql_fetch_array($T004);
        if (mysql_num_rows($T004) == 0) {
            return "Erro: O produto não foi encontrado.";
        }
        
        $cClausula = " UPDATE T004 SET ";
        $cClausula .= " T004_D009_Id='{$D009_Id}', ";
        $cClausula .= " T004_Codigo_Produto='$mT004[D001_Codigo_Produto]', ";
        $cClausula .= " T004_Descricao_Produto='$mT004[D001_Descricao_Produto]', ";
        $cClausula .= " T004_D037_Id='$mT004[D001_D037_Id]', ";
        $cClausula .= " T004_C004_Id='{$g['empresaAtual']}', ";
        $cClausula .= " T004_D006_Id='1' ";
        $cClausula .= " where T004_Id='{$T004_Id}' ";
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            return "Erro: Erro ao vincular item.";
        }
        
        return true;
    }
}
