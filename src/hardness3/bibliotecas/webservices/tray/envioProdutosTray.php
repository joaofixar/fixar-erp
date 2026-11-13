<?php
namespace hardness;

/* INTEGRAÇÃO E-COMMERCE TRAY
 *
 * URL's da API utilizadas
 * https://api.commerce.tray.com.br/products - POST - Adicionar produtos
 * https://api.commerce.tray.com.br/products/{id} - PUT - Atualizar produtos
 * https://api.commerce.tray.com.br/products/variants - POST - Adicionar variações de produtos
 * https://api.commerce.tray.com.br/products/variants/{id} - PUT - Atualizar variações de produtos
 * 
 */
definirClasse('ENVIOPRODUTOSTRAY');
class EnvioProdutosTray_ {

    private const limitRegs = 50;

    public function __construct() {
    }

    public function executar() {

        require_once('bibliotecas/webservices/tray/conexaoTray.php');
        $conexaoTray = new ConexaoTray();

        if ($conexaoTray->isTudoOk()) {
            $this->enviarProdutos($conexaoTray);
            $this->enviarVariacoes($conexaoTray);
        } 
    }

    private function enviarProdutos($conexaoTray) {
        
        $buscarMais = true;

        // Para ver o progresso: select count(D185_Id), D185_Integrado_Tray from D185 group by D185_Integrado_Tray
        while ($buscarMais) {

            $produtosTray = $this->getProdutos();

            foreach($produtosTray as $produtoTray) {

                $semAlteracoes = false;
                $envioTray = array('Product' => $produtoTray['paraTray']);

                $D185_Integracao_ID = $produtoTray['IDs']["D185_Integracao_ID"];
                $retornoTray = array();

                if (isset($D185_Integracao_ID) && !empty($D185_Integracao_ID) && $D185_Integracao_ID > 0) {
                    $envioTray['Product'] = $this->calcAlteracaoProduto($envioTray['Product'], $D185_Integracao_ID, $conexaoTray);

                    if (!empty($envioTray['Product'])) {
                        $retornoTray = $conexaoTray->execPut("products/{$D185_Integracao_ID}", $envioTray, true);
                    } else {
                        log("D185 de ID integração {$D185_Integracao_ID} não possui alterações a enviar!");
                        $retornoTray = array();
                        $semAlteracoes = true;
                    }
                } else {
                    $retornoTray = $conexaoTray->execPost("products", $envioTray, true);
                }

                if (($semAlteracoes) || (isset($retornoTray['code']) && ($retornoTray['code'] == "201" || $retornoTray['code'] == "200"))) {
                    $produtoTray['deTray'] = $retornoTray;
                    $this->setProdutoIntegrado($produtoTray, "");
                } else {
                    $this->setProdutoIntegrado($produtoTray, \json_encode($retornoTray));
                    log("<b><p style=\"color:red\">[ERRO_PRODUTO]</p></b>");
                }
            }

            $buscarMais = count($produtosTray) == self::limitRegs;

            if ($buscarMais) {
                $conexaoTray = new ConexaoTray();
            }
        }

    }

    private function getProdutos() {

        global $g;

        $C004_Id_Logado = $g['empresaAtual'];
        $produtosTray = array();
        $sql = " SELECT CONCAT(D185_Codigo, 'm') AS D185_Codigo,
                        D185_Id, 
                        D185_Descricao, 
                        D185_Comprimento, 
                        D185_Altura,
                        D185_Largura, 
                        D185_Descricao_Detalhada, 
                        D185_Integracao_ID,
                        (coalesce(D001_Peso_Unitario_Bruto,0) * 1000) as D001_Peso_Unitario_Bruto,
                        D001_Flag_Ativo,
                        D060_Descricao_Grupo,
                        COALESCE(D002_Id_Integracao_Tray, D003_Id_Integracao_Tray) as Categoria_Id_Integracao_Tray,
                        D009_Preco_1,
                        D009_Preco_Loja
                   FROM D185
                   JOIN D186 ON D186_D185_Id = D185_Id
              LEFT JOIN D001 ON D186_D001_Id = D001_Id
              LEFT JOIN D049 ON D049_D001_Id = D001_Id
              LEFT JOIN D009 ON D009_D049_Id = D049_Id AND D009_C004_Id = {$C004_Id_Logado}
              LEFT JOIN D060 ON D001_D060_Id = D060_Id
              LEFT JOIN D002 ON D001_D002_Id = D002_Id
              LEFT JOIN D003 ON D001_D003_Id = D003_Id
                  WHERE D185_Integrado_Tray = 'N'
               GROUP BY D185_Id ";

        $limitRegs = self::limitRegs;
        $sql .= " LIMIT {$limitRegs} ";

        $query = mysql_query($sql);

        while ($produtoERP = mysql_fetch_array($query)) {

            $produtoTray = array('IDs' => array(), 'paraTray' => array(), 'deTray' => array());

            $produtoTray['IDs']["D185_Id"]            = $produtoERP['D185_Id'];
            $produtoTray['IDs']["D185_Integracao_ID"] = $produtoERP['D185_Integracao_ID'];

            $produtoTray['paraTray']['name']               = $produtoERP['D185_Descricao']; // String (200) - Nome do produto

            $description                                   = nl2br($produtoERP['D185_Descricao_Detalhada']); // substituição de "enter" por "<BR>" para quebrar linha no HTML
            $description                                   = "<span style='line-height:1; font-size:14pt; font-family:Tahoma; text-align: justify; text-justify: inter-word;'>{$description}</span>";
            $produtoTray['paraTray']['description']        = $description; // String (4800) - Descrição do produto

            $produtoTray['paraTray']['description_small']  = $produtoERP['D185_Descricao']; // String (500) - Descrição resumida do produto
            $produtoTray['paraTray']['width']              = $produtoERP['D185_Largura']; // Number(9) - Largura do produto
            $produtoTray['paraTray']['height']             = $produtoERP['D185_Altura']; // Number (9) - Altura do produto
            $produtoTray['paraTray']['reference']          = $produtoERP['D185_Codigo']; // String (120) - Referência do produto
            $produtoTray['paraTray']['virtual_product']    = 0; // String - Se for um produto virtual, significa que ele não possui o envio físico.
            $produtoTray['paraTray']['brand']              = $produtoERP['D060_Descricao_Grupo']; // String (120) - Marca do produto

            if ($produtoERP['D001_Flag_Ativo'] == 'S') {
                $produtoTray['paraTray']['available']      = 1; // Number - Produto disponível (Veja Tabela A) // 0 - Indisponível // 1 - Disponível
            } else {
                $produtoTray['paraTray']['available']      = 0; // Number - Produto disponível (Veja Tabela A) // 0 - Indisponível // 1 - Disponível
            }

            $produtoTray['paraTray']['category_id']        = $produtoERP['Categoria_Id_Integracao_Tray']; // Number - Código da categoria principal do produto
            $produtoTray['paraTray']['length']             = $produtoERP['D185_Comprimento']; // Number (9) - Comprimento do produto
            
            $preco = $produtoERP['D009_Preco_1'];
            if($g['C031']['precoEnviadoPlataformaTray'] == '2'){
                $preco = $produtoERP['D009_Preco_Loja'];
            }
            $produtoTray['paraTray']['price']              = $preco; // Decimal - (9) - Preço do produto // Zerado aqui pois preço será enviado na variação

            // Precisa ir em branco para não conflitar com EANs das variações
            $produtoTray['paraTray']['ean']                = ""; // String (120) - Código EAN do produto

            if ($produtoERP['D001_Peso_Unitario_Bruto'] > 0) {
                $produtoTray['paraTray']['weight']        = (float) $produtoERP['D001_Peso_Unitario_Bruto']; // Number (9) - Peso do produto
            }

            /*
            $produtoTray['paraTray']['ean']                = $produtoERP['']; // String (120) - Código EAN do produto
            $produtoTray['paraTray']['name']               = $produtoERP['']; // String (200) - Nome do produto
            $produtoTray['paraTray']['ncm']                = $produtoERP['']; // String (8) - Código NCM do produto
            $produtoTray['paraTray']['description']        = $produtoERP['']; // String (4800) - Descrição do produto
            $produtoTray['paraTray']['description_small']  = $produtoERP['']; // String (500) - Descrição resumida do produto
            $produtoTray['paraTray']['price']              = $produtoERP['']; // Decimal - (9) - Preço do produto
            $produtoTray['paraTray']['cost_price']         = $produtoERP['']; // Decimal (9) - Preço de custo do produto
            $produtoTray['paraTray']['promotional_price']  = $produtoERP['']; // Decimal (9) - Preço promocional do produto
            $produtoTray['paraTray']['start_promotion']    = $produtoERP['']; // Date - Data de início da promoção do produto (Formato: aaaa-mm-dd)
            $produtoTray['paraTray']['end_promotion']      = $produtoERP['']; // Date - Data de termino da promoção do produto (Formato: aaaa-mm-dd)
            $produtoTray['paraTray']['ipi_value']          = $produtoERP['']; // Decimal - IPI do produto
            $produtoTray['paraTray']['brand']              = $produtoERP['']; // String (120) - Marca do produto
            $produtoTray['paraTray']['model']              = $produtoERP['']; // String (150) - Modelo do produto
            $produtoTray['paraTray']['weight']             = $produtoERP['']; // Number (9) - Peso do produto
            $produtoTray['paraTray']['length']             = $produtoERP['']; // Number (9) - Comprimento do produto
            $produtoTray['paraTray']['width']              = $produtoERP['']; // Number(9) - Largura do produto
            $produtoTray['paraTray']['height']             = $produtoERP['']; // Number (9) - Altura do produto
            $produtoTray['paraTray']['stock']              = $produtoERP['']; // Number (9) - Estoque do produto
            $produtoTray['paraTray']['category_id']        = $produtoERP['']; // Number - Código da categoria principal do produto
            $produtoTray['paraTray']['available']          = $produtoERP['']; // Number - Produto disponível (Veja Tabela A)
            $produtoTray['paraTray']['availability']       = $produtoERP['']; // String - Disponibilidade do produto
            $produtoTray['paraTray']['availability_days']  = $produtoERP['']; // Number - Dias de disponibilidade do produto
            $produtoTray['paraTray']['reference']          = $produtoERP['']; // String (120) - Referência do produto
            $produtoTray['paraTray']['hot']                = $produtoERP['']; // Number - Produto em destaque (Veja Tabela E)
            $produtoTray['paraTray']['release']            = $produtoERP['']; // Number - Produto lançamento (Veja Tabela D)
            $produtoTray['paraTray']['additional_button']  = $produtoERP['']; // Number - Botão adicional do produto
            $produtoTray['paraTray']['related_categories'] = $produtoERP['']; // Number - Categorias adicionais do produto (separados por vírgula)
            $produtoTray['paraTray']['release_date']       = $produtoERP['']; // Date - Data de lançamento do produto (Formato: aaaa-mm-dd)
            $produtoTray['paraTray']['picture_source_1']   = $produtoERP['']; // String - Imagem principal do produto (Informar a URL para cadastro)
            $produtoTray['paraTray']['picture_source_2']   = $produtoERP['']; // String - Imagem do produto - imagem 2 (Informar a URL para cadastro)
            $produtoTray['paraTray']['picture_source_3']   = $produtoERP['']; // String - Imagem do produto - imagem 3 (Informar a URL para cadastro)
            $produtoTray['paraTray']['picture_source_4']   = $produtoERP['']; // String - Imagem do produto - imagem 4 (Informar a URL para cadastro)
            $produtoTray['paraTray']['picture_source_5']   = $produtoERP['']; // String - Imagem do produto - imagem 5 (Informar a URL para cadastro)
            $produtoTray['paraTray']['picture_source_6']   = $produtoERP['']; // String - Imagem do produto - imagem 6 (Informar a URL para cadastro)
            $produtoTray['paraTray']['metatag']            = $produtoERP['']; // Object - Metatags
            $produtoTray['paraTray']['type']               = $produtoERP['']; // String - Tipo da metatag (Informar o valor keywords)
            $produtoTray['paraTray']['content']            = $produtoERP['']; // String - Dados da metatag
            $produtoTray['paraTray']['local']              = $produtoERP['']; // Number - Local da metatag (Informar p valor 1)
            $produtoTray['paraTray']['virtual_product']    = $produtoERP['']; // String - Se for um produto virtual, significa que ele não possui o envio físico.
            */

            $produtosTray[] = $produtoTray;
        }

        return $produtosTray;
    }

    private function setProdutoIntegrado($produtoTray, $msgErro) {

        $D185_Id = $produtoTray['IDs']['D185_Id'];

        $sqlD185 = " UPDATE D185 SET 
                        D185_Data_Hora_Ult_Envio = CURRENT_TIMESTAMP(), ";

        if (empty($msgErro)) {

            $sqlD185 .= " D185_Integrado_Tray = 'S',
                          D185_Erro_Ult_Envio = '' ";

            if ($produtoTray['IDs']['D185_Integracao_ID'] == 0 && isset($produtoTray['deTray']['id'])) {
                $D185_Integracao_ID = $produtoTray['deTray']['id'];
                $sqlD185 .= ", D185_Integracao_ID = {$D185_Integracao_ID} ";
            } 
        } else {
            $msgErro = mysql_real_escape_string($msgErro);
            $sqlD185 .= " D185_Integrado_Tray = 'E',
                          D185_Erro_Ult_Envio = '{$msgErro}' ";
        }

        $sqlD185 .= " WHERE D185_Id = {$D185_Id} ";

        log("<b>SQL Update: {$sqlD185}</b>");
        mysql_query($sqlD185);
    }

    private function enviarVariacoes($conexaoTray) {

        $buscarMais = true;

        // Para ver o progresso: select count(D185_Id), D185_Integrado_Tray from D185 group by D185_Integrado_Tray
        while ($buscarMais) {

            $variacoesTray = $this->getVariacoes();

            foreach($variacoesTray as $variacaoTray) {

                $semAlteracoes = false;
                $envioTray = array('Variant' => $variacaoTray['paraTray']);
                $retornoTray = array();

                $D186_Integracao_ID = $variacaoTray['IDs']["D186_Integracao_ID"];

                if (!isset($D186_Integracao_ID) || empty($D186_Integracao_ID) || $D186_Integracao_ID == 0) {
                    $product_id = $variacaoTray['paraTray']['product_id'];
                    $reference  = $variacaoTray['paraTray']['reference'];

                    $idVariacaoTray = $this->getIdVariacaoTray($conexaoTray, $product_id, $reference);
                }

                if (isset($D186_Integracao_ID) && !empty($D186_Integracao_ID) && $D186_Integracao_ID > 0) {

                    $envioTray['Variant'] = $this->calcAlteracaoVariacao($envioTray['Variant'], $D186_Integracao_ID, $conexaoTray);

                    if (!empty($envioTray['Variant'])) {
                        $retornoTray = $conexaoTray->execPut("products/variants/{$D186_Integracao_ID}", $envioTray, true);
                    } else {
                        log("D186 de ID integração {$D186_Integracao_ID} não possui alterações a enviar!");
                        $retornoTray = array();
                        $semAlteracoes = true;
                    }
                } else {
                    $retornoTray = $conexaoTray->execPost("products/variants/", $envioTray, true);
                }

                if (($semAlteracoes) || (isset($retornoTray['code']) && ($retornoTray['code'] == "201" || $retornoTray['code'] == "200"))) {
                    $variacaoTray['deTray'] = $retornoTray;
                    $this->setVariacaoIntegrada($variacaoTray, "");
                } else {
                    $this->setVariacaoIntegrada($variacaoTray, \json_encode($retornoTray));
                    log("<b><p style=\"color:red\">[ERRO_VARIACAO]</p></b>");
                }
            }

            $buscarMais = count($variacoesTray) == self::limitRegs;

            if ($buscarMais) {
                $conexaoTray = new ConexaoTray();
            }
        }

    }

    private function getVariacoes() {

        global $g;
        
        $C004_Id_Logado = $g['empresaAtual'];
        $variacoesTray = array();
        $sql = " SELECT D001_Codigo_Produto, 
                        D001_Flag_Ativo, 
                        D001_Id, 
                        D001_Codigo_Barras,
                        (coalesce(D001_Peso_Unitario_Bruto,0) * 1000) as D001_Peso_Unitario_Bruto,
                        D186_Id, 
                        D186_Integracao_ID, 
                        D186_Atributo_Nome, 
                        D186_Atributo_Valor,
                        D185_Id,
                        D185_Integracao_ID,
                        D009_Preco_1,
                        D009_Preco_Loja,
                        D001A_Quantidade_Estoque_Tray
                   FROM D186 
                   JOIN D185 ON D186_D185_Id = D185_Id
                   JOIN D001 ON D186_D001_Id = D001_Id
              LEFT JOIN D001A ON D001A_D001_Id = D001_Id
              LEFT JOIN D049 ON D049_D001_Id = D001_Id
              LEFT JOIN D009 ON D009_D049_Id = D049_Id AND D009_C004_Id = {$C004_Id_Logado}
              LEFT JOIN D002 ON D001_D002_Id = D002_Id
              LEFT JOIN D003 ON D001_D003_Id = D003_Id
                  WHERE (D186_Integrado_Tray = 'N' OR D185_Integrado_Tray = 'N')
                    AND COALESCE(D185_Integracao_ID, 0) > 0
               ORDER BY D185_Id, D001_Id, D186_Id ";

        $limitRegs = self::limitRegs;
        $sql .= " LIMIT {$limitRegs} ";

        $query = mysql_query($sql);

        while ($variacaoERP = mysql_fetch_array($query)) {

            if (isset($variacaoTray) && 
            $variacaoTray['IDs']['D185_Id'] == $variacaoERP['D185_Id'] &&
            $variacaoTray['IDs']['D001_Id'] == $variacaoERP['D001_Id']) {

                if (!empty($variacaoERP['D186_Atributo_Nome'])) {
                    $sku = array();

                    $sku['type']  = $variacaoERP['D186_Atributo_Nome']; // String - Tipo de sku da variação
                    $sku['value'] = $variacaoERP['D186_Atributo_Valor']; // String - Valor de sku da variação

                    $variacaoTray['paraTray']['Sku'][] = $sku;
                } 

            } else {
                $variacaoTray = array('IDs' => array(), 'paraTray' => array(), 'deTray' => array());

                $variacaoTray['IDs']['D001_Id']                = $variacaoERP['D001_Id'];
                $variacaoTray['IDs']['D185_Id']                = $variacaoERP['D185_Id'];
                $variacaoTray['IDs']["D185_Integracao_ID"]     = $variacaoERP['D185_Integracao_ID'];
                $variacaoTray['IDs']["D186_Id"]                = $variacaoERP['D186_Id'];
                $variacaoTray['IDs']["D186_Integracao_ID"]     = $variacaoERP['D186_Integracao_ID'];

                $variacaoTray['paraTray']['product_id']        = $variacaoERP['D185_Integracao_ID']; // Number - Código do produto principal

                if ($variacaoERP['D001_Codigo_Barras'] == "SEM GTIN") {
                    $variacaoTray['paraTray']['ean']           = ""; // String - EAN da variação
                } else {
                    $variacaoTray['paraTray']['ean']           = $variacaoERP['D001_Codigo_Barras']; // String - EAN da variação
                }

                $preco = $variacaoERP['D009_Preco_1'];
                if($g['C031']['precoEnviadoPlataformaTray'] == '2'){
                    $preco = $variacaoERP['D009_Preco_Loja'];
                }
                $variacaoTray['paraTray']['price']             = $preco; // Decimal - Preço da variação
                                
                if ($variacaoERP['D001_Flag_Ativo'] == "S") {
                    $variacaoERP['D001A_Quantidade_Estoque_Tray'] = ($variacaoERP['D001A_Quantidade_Estoque_Tray'] > 0) ? $variacaoERP['D001A_Quantidade_Estoque_Tray'] : 0;
                    $variacaoTray['paraTray']['stock']         = $variacaoERP['D001A_Quantidade_Estoque_Tray']; // Number - Estoque da variação
                } else {
                    $variacaoTray['paraTray']['stock']         = 0; // Number - Estoque da variação
                }

                $variacaoTray['paraTray']['reference']         = $variacaoERP['D001_Codigo_Produto']; // String - Referência da variação

                if ($variacaoERP['D001_Peso_Unitario_Bruto'] > 0) {
                    $variacaoTray['paraTray']['weight']        = (float) $variacaoERP['D001_Peso_Unitario_Bruto']; // Number - Peso da variação
                }

                $variacaoTray['paraTray']['Sku']               = array();

                $sku = array();

                if (!empty($variacaoERP['D186_Atributo_Nome'])) {
                    $sku['type']  = $variacaoERP['D186_Atributo_Nome']; // String - Tipo de sku da variação
                    $sku['value'] = $variacaoERP['D186_Atributo_Valor']; // String - Valor de sku da variação
                } else {
                    $sku['type']  = 'Unico'; // String - Tipo de sku da variação
                    $sku['value'] = 'Unico'; // String - Valor de sku da variação
                }
    
                $variacaoTray['paraTray']['Sku'][] = $sku;
            }

            /*
            $variacaoTray['paraTray']['product_id']        = $variacaoERP['']; // Number - Código do produto principal
            $variacaoTray['paraTray']['ean']               = $variacaoERP['']; // String - EAN da variação
            $variacaoTray['paraTray']['order']             = $variacaoERP['']; // Number - Ordem da visualização da variação
            $variacaoTray['paraTray']['price']             = $variacaoERP['']; // Decimal - Preço da variação
            $variacaoTray['paraTray']['cost_price']        = $variacaoERP['']; // Decimal - Preço de custo da variação
            $variacaoTray['paraTray']['stock']             = $variacaoERP['']; // Number - Estoque da variação
            $variacaoTray['paraTray']['minimum_stock']     = $variacaoERP['']; // Number - Estoque mínimo da variação
            $variacaoTray['paraTray']['reference']         = $variacaoERP['']; // String - Referência da variação
            $variacaoTray['paraTray']['weight']            = $variacaoERP['']; // Number - Peso da variação
            $variacaoTray['paraTray']['length']            = $variacaoERP['']; // Number - Comprimento da variação
            $variacaoTray['paraTray']['width']             = $variacaoERP['']; // Number - Largura da variação
            $variacaoTray['paraTray']['height']            = $variacaoERP['']; // Number - Altura da variação
            $variacaoTray['paraTray']['start_promotion']   = $variacaoERP['']; // Date - Data de início da promoção da variação (Formato: aaaa-mm-dd)
            $variacaoTray['paraTray']['end_promotion']     = $variacaoERP['']; // Date - Data de termino da promoção da variação (Formato: aaaa-mm-dd)
            $variacaoTray['paraTray']['promotional_price'] = $variacaoERP['']; // Decimal - Preço promocional da variação
            $variacaoTray['paraTray']['type']              = $variacaoERP['']; // String - Tipo de sku da variação
            $variacaoTray['paraTray']['value']             = $variacaoERP['']; // String - Valor de sku da variação
            $variacaoTray['paraTray']['picture_source_1']  = $variacaoERP['']; // String - Imagem principal do produto (URL da imagem) (Obrigatório enviar o product_id)
            $variacaoTray['paraTray']['picture_source_2']  = $variacaoERP['']; // String - Imagem do produto - imagem 2 (URL da imagem) (Obrigatório enviar o product_id)
            $variacaoTray['paraTray']['picture_source_3']  = $variacaoERP['']; // String - Imagem do produto - imagem 3 (URL da imagem) (Obrigatório enviar o product_id)
            $variacaoTray['paraTray']['picture_source_4']  = $variacaoERP['']; // String - Imagem do produto - imagem 4 (URL da imagem) (Obrigatório enviar o product_id)
            $variacaoTray['paraTray']['picture_source_5']  = $variacaoERP['']; // String - Imagem do produto - imagem 5 (URL da imagem) (Obrigatório enviar o product_id)
            $variacaoTray['paraTray']['picture_source_6']  = $variacaoERP['']; // String - Imagem do produto - imagem 6 (URL da imagem) (Obrigatório enviar o product_id)
            $variacaoTray['paraTray']['quantity_sold']     = $variacaoERP['']; // Number - Quantidade vendida da variação
            */

            $arrayIdx = $variacaoTray['IDs']['D185_Id'] . "-" . $variacaoTray['IDs']['D001_Id'];
            $variacoesTray[$arrayIdx] = $variacaoTray;
        }

        return $variacoesTray;
    }

    private function setVariacaoIntegrada($variacaoTray, $msgErro) {

        $D186_D185_Id = $variacaoTray['IDs']['D185_Id'];
        $D186_D001_Id = $variacaoTray['IDs']['D001_Id'];

        $sqlD186 = "UPDATE D186 SET 
                        D186_Data_Hora_Ult_Envio = CURRENT_TIMESTAMP(), ";

        if (empty($msgErro)) {
            $sqlD186 .= " D186_Integrado_Tray = 'S',
                          D186_Erro_Ult_Envio = '' ";

            if ($variacaoTray['IDs']['D186_Integracao_ID'] == 0 && isset($variacaoTray['deTray']['id'])) {
                $D186_Integracao_ID = $variacaoTray['deTray']['id'];
                $sqlD186 .= ", D186_Integracao_ID = {$D186_Integracao_ID} ";
            } 
        } else {
            $msgErro = mysql_real_escape_string($msgErro);
            $sqlD186 .= " D186_Integrado_Tray = 'E',
                          D186_Erro_Ult_Envio = '{$msgErro}' ";
        }

        $sqlD186 .= " WHERE D186_D185_Id = {$D186_D185_Id}
                      AND   D186_D001_Id = {$D186_D001_Id} ";

        mysql_query($sqlD186);
    }

    public function getIdVariacaoTray($conexaoTray, $product_id, $reference) {

        $idVariacaoTray = 0;
        $retornoGet = $conexaoTray->execGet('products/variants', array('product_id' => $product_id, 'limit' => '50'), true);
        $variacoesTray = $retornoGet['Variants'];

        foreach ($variacoesTray as $variacaoTray) {

            if ($reference == $variacaoTray['Variant']['reference']) {
                $idVariacaoTray = $variacaoTray['Variant']['id'];
            }
        }

        return $idVariacaoTray;
    }

    private function calcAlteracaoProduto($arrayParaTray, $D185_Integracao_ID, $conexaoTray) {

        $arrayDeTray = $conexaoTray->execGet("products/{$D185_Integracao_ID}", array(), true);

        return $this->calcularArrayEnvio($arrayParaTray, $arrayDeTray['Product']);
    }

    private function calcAlteracaoVariacao($arrayParaTray, $D186_Integracao_ID, $conexaoTray) {

        $arrayDeTray = $conexaoTray->execGet("products/variants/{$D186_Integracao_ID}", array(), true);

        return $this->calcularArrayEnvio($arrayParaTray, $arrayDeTray['Variant']);
    }

    private function calcularArrayEnvio($arrayParaTray, $arrayDeTray, $execUpper = false) {

        foreach ($arrayParaTray as $key => $value) {

            if (is_array($value)) {
                $subArray = $this->calcularArrayEnvio($value, $arrayDeTray[$key], true);

                if (empty($subArray)) {
                    unset($arrayParaTray[$key]);
                }
            } else {
                if ($execUpper) {
                    if (strtoupper($value) == strtoupper($arrayDeTray[$key])) {
                        unset($arrayParaTray[$key]);
                    }
                } else {
                    if ($value == $arrayDeTray[$key]) {
                        unset($arrayParaTray[$key]);
                    }
                }
            }
        }

        return $arrayParaTray;
    }

}