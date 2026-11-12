<?php
namespace hardness;

/* INTEGRAÇÃO E-COMMERCE Nuvemshop
 *
 * URL's da API utilizadas
 * https://api.commerce.Nuvemshop.com.br/products - POST - Adicionar produtos
 * 
 */
definirClasse('EnvioProdutosNuvemshop');
class EnvioProdutosNuvemshop_ {

    private const limitRegs = 50;

    public function __construct() {
    }

    public function executar() {

        require_once('bibliotecas/webservices/nuvemshop/conexaoNuvemshop.php');
        $conexaoNuvemshop = new ConexaoNuvemshop();

        if ($conexaoNuvemshop->isTudoOk()) {
            $this->enviarProdutos($conexaoNuvemshop);
        }
    }

    public function executarProduto($idProduto,$tipoAtualizacao) {

        require_once('bibliotecas/webservices/nuvemshop/conexaoNuvemshop.php');
        $conexaoNuvemshop = new ConexaoNuvemshop();

        // TtipoAtualizacao
        // 0 - Completo
        // 1 - Preço e Estoque

        if ($conexaoNuvemshop->isTudoOk()) {
            $this->enviarUmProduto($conexaoNuvemshop,$idProduto,$tipoAtualizacao);
        }
    }

    private function enviarProdutos($conexaoNuvemshop) {

        $produtosNuvemshop = $this->getProdutos("0");

        foreach($produtosNuvemshop as $produtoNuvemshop) {

            $D001A_Id = $produtoNuvemshop['IDs']['D001A_Id'];
            $D001A_Produto_NS_Id = $produtoNuvemshop['IDs']['D001A_Produto_NS_Id'];
            $D001A_Variacao_NS_Id = $produtoNuvemshop['IDs']['D001A_Variacao_NS_Id'];

            $envioNuvemshop = $produtoNuvemshop['paraNS'];
            $putNS = $produtoNuvemshop['putNS'];
            $putProductNS = $produtoNuvemshop['putProductNS'];
            $retornoNS = array();

            if (isset($D001A_Variacao_NS_Id) && !empty($D001A_Variacao_NS_Id) && $D001A_Variacao_NS_Id > 0) {
                
                $retornoNS = $conexaoNuvemshop->execPut("products/{$D001A_Produto_NS_Id}/variants/{$D001A_Variacao_NS_Id}", $putNS);
                $retornoNS = $conexaoNuvemshop->execPut("products/{$D001A_Produto_NS_Id}", $putProductNS);

                $this->setLog($retornoNS,$D001A_Id);
                
            } else {
                $retornoNS = $conexaoNuvemshop->execPost("products",$envioNuvemshop);

                if($retornoNS['id'] > 0){
                    $this->setProdutoIntegrado($retornoNS['id'],$retornoNS['variants'][0]['id'],$D001A_Id);
                    $this->setLog("201",$D001A_Id);
                }else{
                    $this->setLog($retornoNS,$D001A_Id);
                }
            }

        // Aguarda 1s antes de fazer a próxima requisição para eviar erro 429
        usleep(1000000); 
            
        }

    }

    private function enviarUmProduto($conexaoNuvemshop,$idProduto,$tipoAtualizacao) {

        $produtosNuvemshop = $this->getProdutos($idProduto);

        foreach($produtosNuvemshop as $produtoNuvemshop) {

            $D001A_Id = $produtoNuvemshop['IDs']['D001A_Id'];
            $D001A_Produto_NS_Id = $produtoNuvemshop['IDs']['D001A_Produto_NS_Id'];
            $D001A_Variacao_NS_Id = $produtoNuvemshop['IDs']['D001A_Variacao_NS_Id'];

            $envioNuvemshop = $produtoNuvemshop['paraNS'];
            $putNS = $produtoNuvemshop['putNS'];
            $putProductNS = $produtoNuvemshop['putProductNS'];
            $putProdutoBasico = $produtoNuvemshop['putProdutoBasico'];
            $retornoNS = array();

            if (isset($D001A_Variacao_NS_Id) && !empty($D001A_Variacao_NS_Id) && $D001A_Variacao_NS_Id > 0) {
                
                if($tipoAtualizacao == 0){
                    $retornoNS = $conexaoNuvemshop->execPut("products/{$D001A_Produto_NS_Id}/variants/{$D001A_Variacao_NS_Id}", $putNS);
                    $retornoNS = $conexaoNuvemshop->execPut("products/{$D001A_Produto_NS_Id}", $putProductNS);
                }
                if($tipoAtualizacao == 1){
                    $retornoNS = $conexaoNuvemshop->execPut("products/{$D001A_Produto_NS_Id}/variants/{$D001A_Variacao_NS_Id}", $putProdutoBasico);
                }
                $this->setLog($retornoNS,$D001A_Id);
                
            } else {
                $retornoNS = $conexaoNuvemshop->execPost("products",$envioNuvemshop);

                if($retornoNS['id'] > 0){
                    $this->setProdutoIntegrado($retornoNS['id'],$retornoNS['variants'][0]['id'],$D001A_Id);
                    $this->setLog("201",$D001A_Id);
                }else{
                    $this->setLog($retornoNS,$D001A_Id);
                }
            }

        // Aguarda 1s antes de fazer a próxima requisição para eviar erro 429
        //usleep(1000000); 
            
        }

    }

    private function getProdutos($idProduto) {

        global $g;

        $C004_Id_Logado = $g['empresaAtual'];
        $produtosNuvemshop = array();
        $sql = " SELECT D001_Codigo_Produto, 
                        D001_Peso_Unitario_Kg,
                        D001_Descricao_Produto, 
                        D001_Especificacoes,                        
                        D009_Preco_1,
                        D009_Preco_2,
                        D009_Preco_3,
                        D009_Preco_4,
                        D001A_Preco_Nuvemshop,
                        D001A_Id,
                        D001A_Titulo_Nuvemshop,
                        D001A_Descricao_Nuvemshop,
                        D001A_Variacao_NS_Id,
                        D001A_Produto_NS_Id,
                        D001A_Altura,
                        D001A_Largura,
                        D001A_Comprimento,
                        D009_Quantidade_Estoque_Liquido
                   FROM D001
                    LEFT JOIN D049 ON D049_D001_Id = D001_Id
                    LEFT JOIN D009 ON D009_D049_Id = D049_Id AND D009_C004_Id = {$C004_Id_Logado}
                    LEFT JOIN D001A on D001A_D001_Id=D001_Id
                  WHERE D001A_Enviar_Nuvemshop = 'S'";

        if($idProduto > 0){
            $sql .= "AND D001_Id = {$idProduto}";
        }

        //$limitRegs = self::limitRegs;
        //$sql .= " LIMIT {$limitRegs} ";

        $query = mysql_query($sql);

        while ($produtoERP = mysql_fetch_array($query)) {

            // paraNS = Post de produto
            // putNS = Put nas variações
            // putProductNS = Put nos produtos
            // putProdutoBasico = Put nos produtos somente de Estoque e Preço
            // IDs = armazenar os IDs gerais

            $produtoNuvemshop = array('IDs' => array(), 'paraNS' => array(), 'putNS' => array(), 'putProductNS' => array());

            $produtoNuvemshop['IDs']['D001A_Id'] = $produtoERP['D001A_Id'];
            $produtoNuvemshop['IDs']['D001A_Produto_NS_Id'] = $produtoERP['D001A_Produto_NS_Id'];
            $produtoNuvemshop['IDs']['D001A_Variacao_NS_Id'] = $produtoERP['D001A_Variacao_NS_Id'];

            $tituloNuvemshop = !empty($produtoERP['D001A_Titulo_Nuvemshop']) ? $produtoERP['D001A_Titulo_Nuvemshop'] : $produtoERP['D001_Descricao_Produto'];

            $desricaoNuvemshop = !empty($produtoERP['D001A_Descricao_Nuvemshop']) ? $produtoERP['D001A_Descricao_Nuvemshop'] : $produtoERP['D001_Especificacoes'];
            
            // Quando for negativa a quantidade em estoque ele sobe o produto com quantidade 0
            // API não aceita negativos
            $qtdEstoque = $produtoERP['D009_Quantidade_Estoque_Liquido'] < 0 ? 0 : $produtoERP['D009_Quantidade_Estoque_Liquido'];
            
            /*
                Determina o preço a ser utilizado
                Campo preco nuvemshop é usado nos casos de PMA principalmente, ou quando quiser um preço diferente dos usados no sistemas, para usar tem de ativar ele
            */

            $preco = 0;

            if($produtoERP['D001A_Preco_Nuvemshop'] > 0){
                $preco = $produtoERP['D001A_Preco_Nuvemshop'];
            }else{
                if($g['C031']['precoEnviadoNuvemshop'] == '1'){
                    $preco = $produtoERP['D009_Preco_1'];
                }
                if($g['C031']['precoEnviadoNuvemshop'] == '2'){
                    $preco = $produtoERP['D009_Preco_2'];
                }
                if($g['C031']['precoEnviadoNuvemshop'] == '3'){
                    $preco = $produtoERP['D009_Preco_3'];
                }
                if($g['C031']['precoEnviadoNuvemshop'] == '4'){
                    $preco = $produtoERP['D009_Preco_4'];
                }
            }           
            
            // Cria o array para fazer o post dos produtos
            $produtoNuvemshop['paraNS'] = array(
                'name' => array(
                    'pt' => $tituloNuvemshop
                ),
                'description' => array(
                    'pt' => $desricaoNuvemshop
                ),
                'variants' => array(
                    array(
                        'price' => $preco,
                        'stock' => intval($qtdEstoque),
                        'sku' => $produtoERP['D001_Codigo_Produto'],
                        'weight' => $produtoERP['D001_Peso_Unitario_Kg'],                        
                        'height' => $produtoERP['D001A_Altura'],
                        'width' => $produtoERP['D001A_Largura'],
                        'depth' => $produtoERP['D001A_Comprimento']
                    )
                )                
            );

            // Cria o array para fazer PUT nos produtos
            $produtoNuvemshop['putProductNS'] = array(
                'name' => array(
                    'pt' => $tituloNuvemshop
                ),
                'description' => array(
                    'pt' => $desricaoNuvemshop
                )
            );

            // Cria o array para fazer PUT nas variações
            $produtoNuvemshop['putNS'] = array(
                'price' => $preco,
                'stock' => intval($qtdEstoque),
                'weight' => $produtoERP['D001_Peso_Unitario_Kg'],                        
                'height' => $produtoERP['D001A_Altura'],
                'width' => $produtoERP['D001A_Largura'],
                'depth' => $produtoERP['D001A_Comprimento']
            );

            // Cria o array para fazer PUT básico de preço e estoque
            $produtoNuvemshop['putProdutoBasico'] = array(
                'price' => $preco,
                'stock' => intval($qtdEstoque)
            );
            
            $produtosNuvemshop[] = $produtoNuvemshop;
        }

        return $produtosNuvemshop;
    }
    
    private function setProdutoIntegrado($produtoNS,$variacaoNS,$produtoERP) {

        $D001A_Produto_NS_Id = $produtoNS;
        $D001A_Variacao_NS_Id = $variacaoNS;
        $D001A_Id = $produtoERP;

        $sqlD001A = " UPDATE D001A  
                        SET D001A_Produto_NS_Id = {$D001A_Produto_NS_Id},                         
                        D001A_Variacao_NS_Id = {$D001A_Variacao_NS_Id}                         
                        WHERE D001A_Id = {$D001A_Id} ";

        //log("<b>SQL Update: {$sqlD185}</b>");
        mysql_query($sqlD001A);
    }


    private function setLog($code,$produtoERP) {

        $codigo = $code;
        $D001A_Id = $produtoERP;

        $sqlD001A = " UPDATE D001A  
                        SET D001A_Log_Erro = CONCAT('Data :',NOW(),' Codigo',':',{$codigo})                      
                        WHERE D001A_Id = {$D001A_Id} ";

        //log("<b>SQL Update: {$sqlD185}</b>");
        mysql_query($sqlD001A);
    }

}