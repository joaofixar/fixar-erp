<?php
namespace hardness;
require_once('bibliotecas/classes/MYTCPDF.PHP');
require_once "old/lib/funcoes.php";
definirClasse('VEN002');
class VEN002_
{
	 /*if(mysql_num_rows($T00_3_5_7) > 0) {
        $erros .= "\n";
        while($mT00_3_5_7 = mysql_fetch_array($T00_3_5_7)){
			/*
                I. Produtos e Serviços da NF-e
                #   ID    Campo   Descrição                                       Tipo    Ocor.       Tam.            Observação
                101 I02   cProd   Código do produto ou serviço                      C      1-1        1-60            Preencher com CFOP, caso se trate de itens não relacionados com mercadorias/produtos e que o contribuinte não possua codificação própria. Formato: ”CFOP9999”
                103 I04   xProd   Descrição do produto ou serviço                   C      1-1        1-120           
                104 I05   NCM     Código NCM com 8 dígitos                          N      1-1        2, 8            Obrigatória informação do NCM completo (8 dígitos).Nota: Em caso de item de serviço ou item que não tenham produto (ex. transferência de crédito, crédito do ativo imobilizado, etc.), informar o valor 00 (dois zeros). (NT 2014/004)
                107 I08   CFOP    Código Fiscal de Operações e Prestações           N      1-1         4              Utilizar Tabela de CFOP.
                108 I09   uCom    Unidade Comercial                                 C      1-1        1-6             Informar a unidade de comercialização do produto.
                109 I10   qCom    Quantidade Comercial                              N      1-1        11v0-4          Informar a quantidade de comercialização do produto (v2.0).
               109a I10a  vUnCom  Valor Unitário de Comercialização                 N      1-1        11v0-10         Informar o valor unitário de comercialização do produto, campo meramente informativo, o contribuinte pode utilizar a precisão desejada (0-10 decimais). Para efeitos de cálculo, o valor unitário será obtido pela divisão do valor do produto pela quantidade comercial. (v2.0)
                110 I11   vProd   Valor Total Bruto dos Produtos ou Serviços        N      1-1        13v2            
                112 I13   uTrib   Unidade Tributável                                C      1-6        
                113 I14   qTrib   Quantidade Tributável                             N      1-1        11v0-4          Informar a quantidade de tributação do produto (v2.0).
               116b I17b  indTot  Se valor do Item entra no valor total da NF-e     N      1-1          1             0=Valor do item (vProd) não compõe o valor total da NF-e; 1=Valor do item (vProd) compõe o valor total da NF-e (vProd) (v2.0)
            */
            // 101 - Valida a quantidade de caracteres do código do produto
            // --------------------------------- produtos não realacionados a mercadorias/produtos....-------------------//
            /*$mT00_3_5_7['codigoProduto'] = trim($mT00_3_5_7['codigoProduto']);
            if((strlen($mT00_3_5_7['codigoProduto']) <1) || (strlen($mT00_3_5_7['codigoProduto']) > 60)){
                $erros .= "-CÓDIGO PRODUTO(".$mT00_3_5_7['codigoProduto'].") ".$mT00_3_5_7['descricaoProduto']." deve conter entre 1-60 dígitos!\n";
            }

            // 103 - Valida o tamanho do campo da descrição do produto
            $mT00_3_5_7['descricaoProduto'] = trim($mT00_3_5_7['descricaoProduto']);
            if((strlen($mT00_3_5_7['descricaoProduto']) < 1) || (strlen($mT00_3_5_7['descricaoProduto']) > 120)){
                $erros .= "-DESCRIÇÃO PRODUTO (". $mT00_3_5_7['codigoProduto']. ") ".$mT00_3_5_7['descricaoProduto']." deve conter entre 1-120 caracteres!\n";
            }

            // 104 - Valida o tamanho do campo da NCM
            $mT00_3_5_7['NCM']=trim($mT00_3_5_7['NCM']);
            if(strlen($mT00_3_5_7['NCM']) != 8){
                $erros .= "-NCM DO PRODUTO (". $mT00_3_5_7['codigoProduto'].") ".$mT00_3_5_7['NCM']." deve conter 8 dígitos!\n";
            }
            
            // 107 - Validar tamanho do campo da CFOP
            $mT00_3_5_7['CFOP'] = $this->retirarCaracteres(trim($mT00_3_5_7['CFOP']));
            if(strlen($mT00_3_5_7['CFOP']) != 4){
                $erros .= "-CFOP DO PRODUTO (". $mT00_3_5_7['codigoProduto'].") ".$mT00_3_5_7['CFOP']." deve conter 4 dígitos!\n";
            }

            // 108 e 112 - Valida tamanho do campo unidade comercial
            $mT00_3_5_7['unidade '] = trim($mT00_3_5_7['unidade ']);
            if((strlen($mT00_3_5_7['unidade']) < 1) || (strlen($mT00_3_5_7['unidade']) > 6)){
                $erros .= "-UNIDADE DO PRODUTO (". $mT00_3_5_7['codigoProduto'].") ".$mT00_3_5_7['unidade']." deve conter entre 1-6 caracteres!\n";
            }

            // 109 e 113 - Valida a quantidade de caracteres da Quantidade Comercial

            // Formatação utilizada em nfe3.class.php
            /*$mT00_3_5_7['quantidade'] = number_format($mT00_3_5_7['quantidade'],4,'.','');  
            
            $casasDecimais =  $this->contaCasasDecimais($mT00_3_5_7['quantidade']);

            if($casasDecimais > 5){
                $erros .= "-QUANTIDADE DO PRODUTO (". $mT00_3_5_7['codigoProduto']. ") " .$mT00_3_5_7['quantidade']. " deve conter no máximo 4 casas decimais!\n";
            }
            if(strlen($mT00_3_5_7['quantidade']) > 11){
                $erros .= "-QUANTIDADE DO PRODUTO (". $mT00_3_5_7['codigoProduto']. ") " .$mT00_3_5_7['quantidade']. " deve conter entre 1-12 dígitos!\n";
            }*/

            // 109a e 113a - I10a  vUnCom  Valor Unitário de Comercialização N  1-1  11v0-10 
            // Informar o valor unitário de comercialização do produto, campo meramente informativo, 
            // o contribuinte pode utilizar a precisão desejada (0-10 decimais). Para efeitos de cálculo, 
            // o valor unitário será obtido pela divisão do valor do produto pela quantidade comercial. (v2.0)
            /*$mT00_3_5_7['valorUnitarioComercializacao'] = trim($mT00_3_5_7['valorUnitarioComercializacao']);
            $casasDecimais = $this->contaCasasDecimais($mT00_3_5_7['valorUnitarioComercializacao']);
            if($casasDecimais > 11){
                $erros .= "-VALOR UNITÁRIO DO PRODUTO (".$mT00_3_5_7['codigoProduto'].") ".$mT00_3_5_7['valorUnitarioComercializacao']." deve conter no máximo 10 casas decimais!\n";
            }else if((strlen($mT00_3_5_7['valorUnitarioComercializacao'] < 1)) || (strlen($mT00_3_5_7['valorUnitarioComercializacao']) > 12)){
                $erros .= "-VALOR UNITÁRIO DO PRODUTO (".$mT00_3_5_7['codigoProduto'].") ".$mT00_3_5_7['valorUnitarioComercializacao']." deve conter entre 1-11 dígitos, com no máximo 4 casas decimais!\n";
            }*/

            // 110 - Valida tamanho do campo Valor Total Bruto dos Produtos ou Serviços
            /*$mT00_3_5_7['valorTotalBruto'] = trim($mT00_3_5_7['valorTotalBruto']); 
            $casasDecimais = $this->contaCasasDecimais($mT00_3_5_7['valorTotalBruto']);
            if($casasDecimais != 2){
                $erros .= "-VALOR TOTAL BRUTO DO PRODUTO (". $mT00_3_5_7['codigoProduto'].") ". $mT00_3_5_7['valorTotalBruto'] ." deve conter 2 casas decimais!\n";
            }else if(($mT00_3_5_7['valorTotalBruto'] < 0) || ($mT00_3_5_7['valorTotalBruto']) > 99999999999.99){
                $erros .= "-VALOR TOTAL BRUTO DO PRODUTO (". $mT00_3_5_7['codigoProduto'].") ". $mT00_3_5_7['valorTotalBruto'] ." deve conter entre 1-13 dígitos!\n";
            }*/
            
            // 111 - GTIN (Global Trade Item Number) da unidade tributável, antigo código EAN ou código de barras
            // ------------------------ Campo não usado no nf3.class - HARDNESS ---------------------------------//

            // 116 - Indica se valor do Item (vProd) entra no valor total da NF-e (vProd)
            // ------------------- Campo setado manualmente no nf3.class - HARDNESS ----------------------------//

            /*
                I01. Produtos e Serviços / Declaração de Importação
                #   ID    Campo   Descrição                                       Tipo    Ocor.       Tam.            Observação

            */

            /*
                I03. Produtos e Serviços / Grupo de Exportação
                #   ID    Campo   Descrição                                       Tipo    Ocor.       Tam.            Observação

            */

            /*
                I05. Produtos e Serviços / Pedido de Compra
                #   ID    Campo   Descrição                                       Tipo    Ocor.       Tam.            Observação
               128m I60   xPed    Número do Pedido de Compra                        C      0-1        1-15            Informação de interesse do emissor para controle do B2B. (v2.0)
            */

                // 128m- Valida se quando o campo estiver preenchido está preenchido de forma correta 
               /*$dados['numeroPedidoCompra'] = trim($dados['numeroPedidoCompra']); 
                if(empty($mT00_3_5_7['numeroPedidoCompra']) === FALSE ){
                    if((strlen($mT00_3_5_7['numeroPedidoCompra'] < 1)) || (strlen($mT00_3_5_7['numeroPedidoCompra']) > 15)){
                        $erros .= "-NÚMERO DO PEDIDO DO PRODUTO (". $mT00_3_5_7['codigoProduto'].") ".$mT00_3_5_7['numeroPedidoCompra']." deve conter 1-15 dígitos!\n";
                    }   
                }

                // 128n - Item do Pedido de Compra itemPedidoCompra
                // ------------------------- Dúvida refentea qual campo da T004 está relacionado -----------------------//
                $dados['itemPedidoCompra'] = trim($dados['itemPedidoCompra']); 
                if(empty($mT00_3_5_7['itemPedidoCompra']) === FALSE){
                    if(strlen($mT00_3_5_7['itemPedidoCompra'] != 6)) {
                        $erros .= "-ITEM PEDIDO DO PRODUTO (". $mT00_3_5_7['codigoProduto'].") ".$mT00_3_5_7['itemPedidoCompra']." deve conter 6 dígitos!\n";
                    }    
                }

            /*
                I07. Produtos e Serviços / Grupo Diversos
                //------------------------------------ NÃO IMPLEMENTADO NO NF3.CLASS - HARDNESS ----------------------------------//
                #   ID    Campo   Descrição                                       Tipo    Ocor.       Tam.            Observação

            */
        }
    }
}