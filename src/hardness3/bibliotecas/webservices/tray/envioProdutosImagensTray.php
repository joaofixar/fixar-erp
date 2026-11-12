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
definirClasse('ENVIOPRODUTOSIMAGENSTRAY');
class EnvioProdutosImagensTray_ {

    public function __construct() {
    }

    public function executar() {

        require_once('bibliotecas/webservices/tray/conexaoTray.php');
        $conexaoTray = new ConexaoTray();

        if ($conexaoTray->isTudoOk()) {
            $this->enviarImagens($conexaoTray);
        }
    }

    private function enviarImagens($conexaoTray) {
        
        $imagensTray = $this->getImagens();

        foreach($imagensTray as $imagemTray) {

            $erroEnvioD185 = $this->enviarImagensMestre($conexaoTray, $imagemTray);
            $erroEnvioD186 = false; // $this->enviarImagensVariaca($conexaoTray, $imagemTray);

            $this->setImagemIntegrada($imagemTray, $erroEnvioD185 || $erroEnvioD186);
        }
    }

    private function getImagens() {

        global $g;

        $urlPastaProdutos = "{$g['urlRaiz']}/outros/buscarArquivos/imagensProdutos?ajax=true&noEmp=true&p=1&u=63&";

        $C004_Id_Logado = $g['empresaAtual'];
        $imagensTray = array();
        $sql = " SELECT T172_Id, T172_D001_Id, T172_Nome_Arquivo, 
                        D185_Id, D185_Integracao_ID,
                        D186_Id, D186_Integracao_ID
                 FROM T172
                 JOIN D186 ON D186_D001_Id = T172_D001_Id AND COALESCE(D186_Integracao_ID, 0) > 0
                 JOIN D185 ON D185_Id = D186_D185_Id AND COALESCE(D185_Integracao_ID, 0) > 0
                 WHERE T172_Integrado_Tray NOT IN ('S', 'E') ";

        $query = mysql_query($sql);

        while ($imagemERP = mysql_fetch_array($query)) {

            $arrayIdx = $imagemERP['D186_Id'];

            if (array_key_exists($arrayIdx, $imagensTray)) {
                $imagemTray = $imagensTray[$arrayIdx];
            } else {
                $imagemTray = array('IDs' => array(), 'paraTray' => array(), 'deTray' => array());

                $imagemTray['IDs']["D185_Id"]             = $imagemERP['D185_Id'];
                $imagemTray['IDs']["D185_Integracao_ID"]  = $imagemERP['D185_Integracao_ID'];

                $imagemTray['IDs']["D186_Id"]             = $imagemERP['D186_Id'];
                $imagemTray['IDs']["D186_Integracao_ID"]  = $imagemERP['D186_Integracao_ID'];

                $imagemTray['IDs']["T172_D001_Id"]        = $imagemERP['T172_D001_Id'];
            }

            for ($i = 1; $i <= 15; $i++) {
                $nomeCampoImagem = "picture_source_" . $i;

                if  (!isset($imagemTray['paraTray'][$nomeCampoImagem])) {

                    $extensaoImagem = pathinfo($imagemERP['T172_Nome_Arquivo'])['extension'];
                    $urlImagem = $urlPastaProdutos . "&D001Id=" . $imagemERP['T172_D001_Id'] . "&NomeArq=" . $imagemERP['T172_Id'] . "." . $extensaoImagem;

                    $imagemTray['paraTray'][$nomeCampoImagem] = $urlImagem;
                    $imagemTray['IDs']["T172_Id"][] = $imagemERP['T172_Id'];

                    break;
                }
            }

            /*
            $imagemTray['paraTray']['ean']                = $imagemERP['']; // String (120) - Código EAN do produto
            $imagemTray['paraTray']['name']               = $imagemERP['']; // String (200) - Nome do produto
            $imagemTray['paraTray']['ncm']                = $imagemERP['']; // String (8) - Código NCM do produto
            $imagemTray['paraTray']['description']        = $imagemERP['']; // String (4800) - Descrição do produto
            $imagemTray['paraTray']['description_small']  = $imagemERP['']; // String (500) - Descrição resumida do produto
            $imagemTray['paraTray']['price']              = $imagemERP['']; // Decimal - (9) - Preço do produto
            $imagemTray['paraTray']['cost_price']         = $imagemERP['']; // Decimal (9) - Preço de custo do produto
            $imagemTray['paraTray']['promotional_price']  = $imagemERP['']; // Decimal (9) - Preço promocional do produto
            $imagemTray['paraTray']['start_promotion']    = $imagemERP['']; // Date - Data de início da promoção do produto (Formato: aaaa-mm-dd)
            $imagemTray['paraTray']['end_promotion']      = $imagemERP['']; // Date - Data de termino da promoção do produto (Formato: aaaa-mm-dd)
            $imagemTray['paraTray']['ipi_value']          = $imagemERP['']; // Decimal - IPI do produto
            $imagemTray['paraTray']['brand']              = $imagemERP['']; // String (120) - Marca do produto
            $imagemTray['paraTray']['model']              = $imagemERP['']; // String (150) - Modelo do produto
            $imagemTray['paraTray']['weight']             = $imagemERP['']; // Number (9) - Peso do produto
            $imagemTray['paraTray']['length']             = $imagemERP['']; // Number (9) - Comprimento do produto
            $imagemTray['paraTray']['width']              = $imagemERP['']; // Number(9) - Largura do produto
            $imagemTray['paraTray']['height']             = $imagemERP['']; // Number (9) - Altura do produto
            $imagemTray['paraTray']['stock']              = $imagemERP['']; // Number (9) - Estoque do produto
            $imagemTray['paraTray']['category_id']        = $imagemERP['']; // Number - Código da categoria principal do produto
            $imagemTray['paraTray']['available']          = $imagemERP['']; // Number - Produto disponível (Veja Tabela A)
            $imagemTray['paraTray']['availability']       = $imagemERP['']; // String - Disponibilidade do produto
            $imagemTray['paraTray']['availability_days']  = $imagemERP['']; // Number - Dias de disponibilidade do produto
            $imagemTray['paraTray']['reference']          = $imagemERP['']; // String (120) - Referência do produto
            $imagemTray['paraTray']['hot']                = $imagemERP['']; // Number - Produto em destaque (Veja Tabela E)
            $imagemTray['paraTray']['release']            = $imagemERP['']; // Number - Produto lançamento (Veja Tabela D)
            $imagemTray['paraTray']['additional_button']  = $imagemERP['']; // Number - Botão adicional do produto
            $imagemTray['paraTray']['related_categories'] = $imagemERP['']; // Number - Categorias adicionais do produto (separados por vírgula)
            $imagemTray['paraTray']['release_date']       = $imagemERP['']; // Date - Data de lançamento do produto (Formato: aaaa-mm-dd)
            $imagemTray['paraTray']['picture_source_1']   = $imagemERP['']; // String - Imagem principal do produto (Informar a URL para cadastro)
            $imagemTray['paraTray']['picture_source_2']   = $imagemERP['']; // String - Imagem do produto - imagem 2 (Informar a URL para cadastro)
            $imagemTray['paraTray']['picture_source_3']   = $imagemERP['']; // String - Imagem do produto - imagem 3 (Informar a URL para cadastro)
            $imagemTray['paraTray']['picture_source_4']   = $imagemERP['']; // String - Imagem do produto - imagem 4 (Informar a URL para cadastro)
            $imagemTray['paraTray']['picture_source_5']   = $imagemERP['']; // String - Imagem do produto - imagem 5 (Informar a URL para cadastro)
            $imagemTray['paraTray']['picture_source_6']   = $imagemERP['']; // String - Imagem do produto - imagem 6 (Informar a URL para cadastro)
            $imagemTray['paraTray']['metatag']            = $imagemERP['']; // Object - Metatags
            $imagemTray['paraTray']['type']               = $imagemERP['']; // String - Tipo da metatag (Informar o valor keywords)
            $imagemTray['paraTray']['content']            = $imagemERP['']; // String - Dados da metatag
            $imagemTray['paraTray']['local']              = $imagemERP['']; // Number - Local da metatag (Informar p valor 1)
            $imagemTray['paraTray']['virtual_product']    = $imagemERP['']; // String - Se for um produto virtual, significa que ele não possui o envio físico.
            */

            $imagensTray[$arrayIdx] = $imagemTray;
        }

        return $imagensTray;
    }

    private function setImagemIntegrada($imagemTray, $erroEnvio) {

        $T172_Integrado_Tray = $erroEnvio ? "E" : "S";

        foreach ($imagemTray['IDs']["T172_Id"] as $T172_Id) {

            $sqlT172 = " UPDATE T172 SET 
                            T172_Integrado_Tray = '{$T172_Integrado_Tray}'
                         WHERE T172_Id = {$T172_Id} ";

            mysql_query($sqlT172);
        }
    }

    private function enviarImagensMestre($conexaoTray, $imagemTray) {

        $erroEnvio = false;

        $envioTrayD185 = array('Product' => $imagemTray['paraTray']);
        $D185_Integracao_ID = $imagemTray['IDs']["D185_Integracao_ID"];
        $retornoTrayD185 = $conexaoTray->execPut("products/{$D185_Integracao_ID}", $envioTrayD185, true);

        if (!isset($retornoTrayD185['code']) || ($retornoTrayD185['code'] != "201" && $retornoTrayD185['code'] != "200")) {
            log("<b><p style=\"color:red\">[ERRO_IMAGEM_D185]</p></b>");
            $erroEnvio = true;
        }

        return $erroEnvio;
    }

    private function enviarImagensVariaca() {

        $erroEnvio = false;

        $envioTrayD186 = array('Variant' => $imagemTray['paraTray']);
        $D186_Integracao_ID = $imagemTray['IDs']["D186_Integracao_ID"];
        $retornoTrayD186 = $conexaoTray->execPut("products/variants/{$D186_Integracao_ID}", $envioTrayD186, true);

        if (!isset($retornoTrayD186['code']) || ($retornoTrayD186['code'] != "201" && $retornoTrayD186['code'] != "200")) {
            log("<b><p style=\"color:red\">[ERRO_IMAGEM_D186]</p></b>");
            $erroEnvio = true;
        }

        return $erroEnvio;

    }
}