<?php
namespace hardness;
/*
* class.exportacao.php
// */  
// $con =@ mysqli_connect("192.168.0.199", "root", "root","e229");
// $dbs =@mysql_select_db("e229", $con) or die (mysql_error());
// $g['conexaoBanco'] = $con;
// require_once('bibliotecas/funcoesGerais.php');

    $api_url_v1 = "https://www.guimepa.com.br/index.php/api/soap?wsdl";     
    $username = 'Marcelo_hardness';
    $password = '7AvEpHEgap?8';
   
    $cli = new \SoapClient($api_url_v1);        
        //retreive session id from login
    $session = $cli->login($username, $password);

    $status_pedido = 'processing';

   // $filter = array(array('order_id' =>array('from' => '1780','to'=>'1785')));
    $filter = array(array('status' => $status_pedido));

    // $listOrders = $cli->call($session, 'sales_order.list',$filter);
    // echo "<pre>";
    // print_r($listOrders);
    // echo "</pre>";

    // $items = (array) $order;
    // $T005['status_pedido']=$order['status'];

    $item = (array) $order;
    //$increment_id = '100002810';
    $increment_id =$_GET['id'];

    $orderInfo = $cli->call($session, 'sales_order.info',$increment_id);
    echo "<pre>";
    print_r($orderInfo); 
    echo "</pre>";

  
    $D024 = array();    
    $T005 = array();
    $D148 = array();
    $T006 = array();
    $T090 = array();
    $T090 = array();

    $Customer_Id = $orderInfo['customer_id'];

    $cliente_dados = $cli->call($session, 'customer.info', "$Customer_Id");

    // echo "<pre>";
    //     print_r($cliente_dados);
    // echo "</pre>";

    //arrays com informações do magento
    $Customer_CNPJ = $orderInfo['customer_cnpj'];
    $Customer_Campo_Dado = $cliente_dados['taxvat'];
    $D024_Inscricao_Estadual = $cliente_dados['ie'];
    $pagamento = (array) $orderInfo['payment'];
    $address_billing = (array) $orderInfo['billing_address'];
    $endereco_cobranca = explode(chr(10),$address_billing['street']);
    $address_shipping = (array) $orderInfo['shipping_address'];
    $endereco_entrega = explode(chr(10),$address_shipping['street']);

    //variaveis internas para validações
    $Id_integracommerce="";
    $marketplace="";
    $contaMarketplace="";
    $metodoPagamento = "";
    $T005_Prazo_Entrega="";
    $transportadora_magento="";
    $D036_Id="";
    $D022_Id="";

    $Customer_Campo_Dado = str_replace(array('.', '/', '-'), '', trim($Customer_Campo_Dado));  
    // retirando dados do cliente     

    if (strlen($Customer_CNPJ)>14) {

        $D024["D024_Cpf"]='';
        $D024["D024_Cnpj"]=str_replace(array('.', '/', '-'), '', trim($Customer_CNPJ));
        $D024["D024_Inscricao_Estadual"]=$D024_Inscricao_Estadual;        

        $D024["D024_Nome_Fantasia"] = $address_billing['company']; 
        $D024["D024_Nome_Empresa"]  = $address_billing['company'];    

    } else {

        if(strlen($Customer_Campo_Dado)<=11){
            $D024["D024_Cpf"]=str_replace(array('.', '/', '-'), '', trim($Customer_Campo_Dado));
            $D024["D024_Cnpj"]='';
            $D024["D024_Inscricao_Estadual"]='';
        }else{
            $D024["D024_Cpf"]='';
            $D024["D024_Cnpj"]=str_replace(array('.', '/', '-'), '', trim($Customer_Campo_Dado));
            $D024["D024_Inscricao_Estadual"]=$D024_Inscricao_Estadual;
            
        }

        $D024["D024_Nome_Fantasia"] = $address_billing['firstname'].' '.$address_billing['middlename'].' '.$address_billing['lastname']; 
        $D024["D024_Nome_Empresa"]  = $address_billing['firstname'].' '.$address_billing['middlename'].' '.$address_billing['lastname'];    
            
    }


    $D024["D024_Endereco"]=$endereco_cobranca[0];
    $D024["D024_Numero"]=$endereco_cobranca[1];
    $D024["D024_Cep"]=$address_billing['postcode'];
    $D024["D024_Complemento"]=trim($endereco_cobranca[2]);
    $D024["D024_Bairro"]=$endereco_cobranca[3];
    $D024["Cidade"]=$address_billing['city'];
    $D024["UF"]=strtoupper ($address_billing['region']);
    $D024['Pais']=$address_billing['country_id'];
    $D024["D024_DDD_Telefone_1"]='';
    $D024["D024_Telefone_1"]=$address_billing['telephone'];
    $D024["D024_DDD_Telefone_2"]='';
    $D024["D024_Telefone_2"]=$address_billing['fax'];
    $D024["D024_E_Mail"]=$address_billing['email'];
    $D024["D024_Web_Site"]="";    
    


    $D148["UF"]=strtoupper($address_shipping['region']);
    $D148["Cidade"]=$address_shipping['city'];
    $D148["Pais"]=$address_shipping['country_id'];
    $D148["D148_Nome"]=$address_shipping['firstname'].' '.$address_shipping['lastname'];
    $D148["D148_Cep"]=$address_shipping['postcode'];
    $D148["D148_Rua"]=$endereco_entrega[0];
    $D148["D148_Numero"]=$endereco_entrega[1];
    $D148["D148_Complemento"]=$endereco_entrega[2];
    $D148["D148_Bairro"]=$endereco_entrega[3];
    $D148["D148_DDD_Telefone"]='';
    $D148["D148_Telefone"]=$address_shipping['telephone'];
    $D148["D148_DDD_Fax"]='';
    $D148["D148_Fax"]=$address_shipping['fax'];
    $D148["D148_Email"]=$address_shipping['email'];
    $D024['D148'] = $D148;

    // echo "method de pagamento:".$pagamento["method"];

    $informacoes_shipping = $orderInfo['shipping_description'];
        $shipping = explode("-",$informacoes_shipping);
        $T005_Prazo_Entrega = trim($shipping[count($shipping)-1]);

        //tratando campo 'comment' que contém as informações sobre o pedido
        $History_Status = (array)$orderInfo['status_history'];

        foreach ($History_Status as $key => $status) {
            $informacoes = (array) $status;
            
            $comment = $informacoes['comment'];
            if(!empty($comment)){
                // echo $comment."<br><br>";
                //explode os comentarios pela linha pulada em um array de varias strings
                $DetalhesComment = explode("<br>", $comment);
               
                foreach ($DetalhesComment as $key => $detalhe) {
                    $informacao = (array) $detalhe;
                    // echo "LINHA:  ".$informacao[0]."<br><br>";

                    //verifica se existe a palavra Id na linha e pega os numeros da linha
                    if(stripos($informacao[0],"Id no MarketPlace")){
                        $Id_integracommerce=trim(preg_replace("/[^0-9]/", " ",$informacao[0]));
                    }

                    //identificando qual é o marketplace e pegando o nome
                    if((stristr($informacao[0],"Canal De Vendas:"))){
                        $palavras = explode(":", $informacao[0]);
                        $marketplace = trim($palavras[count($palavras)-1]);
                        $marketplace = str_replace("</b>","",$marketplace);
                        $marketplace = str_replace("<br>","",$marketplace);
                        $metodoPagamento = $marketplace;
                    }

                    //identificando qual é a conta do marketplace
                    if((stristr($informacao[0],"Nome da Conta no Anymarket:"))){
                        $palavras = explode(":", $informacao[0]);
                        $contaMarketplace = trim($palavras[count($palavras)-1]);
                        $contaMarketplace = str_replace("</b>","",$contaMarketplace);
                        $contaMarketplace = str_replace("<br>","",$contaMarketplace);
                    }

                    //identificando a forma de envio se existir e retirando da string
                    if(stripos($informacao[0],"Forma de Envio")){
                        $palavras = explode(":", $informacao[0]);
                        $transportadora_magento = trim($palavras[count($palavras)-1]);
                        $transportadora_magento = str_replace("</b>","",$transportadora_magento);
                        $transportadora_magento = str_replace("<br>","",$transportadora_magento);
                    }

                    //identificando a data de entrega se existir e retirando da string
                    if(strpos($informacao[0],"Entrega esperada para")){
                        $palavras = explode(" ", $informacao[0]);
                        $T005_Prazo_Entrega = trim($palavras[count($palavras)-2]." ".$palavras[count($palavras)-1]);
                        $T005_Prazo_Entrega = str_replace("</b>","",$T005_Prazo_Entrega);
                    }

                    //verificando se existe uma linha com a forma de pagamento- verifica se existe a palavra parcelas - pega o numero das parcelas da linha
                    if(stripos($informacao[0],"Parcelas")){
                        $parcelas=trim(preg_replace("/[^0-9]/", "",$informacao[0]));
                        // if($parcelas==0){
                        //     $parcelas=1;
                        // }
                    }  
                    
                    //verificando se existe uma linha com a forma de pagamento e pega a palavra
                    if(stripos($informacao[0],"Forma de Pagamento")){
                        $palavras = explode('- Parcelas', $informacao[0] );
                        $palavras = explode(':', $palavras[0]);
                        $Comment_Forma_de_Pagamento = strToUpper(str_replace(' ','',$palavras[1]));
                    }
                }
            }else{
                continue;
            }
        }

        if(($contaMarketplace!=$marketplace) && $contaMarketplace!="null"){
            $marketplace = $marketplace."-".$contaMarketplace;
        }

        $metodoPagamento = $marketplace;

        if(empty($marketplace)){
            $marketplace = "SITE";
        }  

        if(empty($metodoPagamento)){
            $metodoPagamento = $pagamento["method"];
        } 

        //Inserindo o D022_Id dentro do T005_D022_Id a partir da transportadora que veio do magento e o D036 correspondete ao T005_D036_Id
        if(empty($transportadora_magento) || $transportadora_magento=="UNKNOWN"){
            $transportadora_magento = trim($shipping[count($shipping)-2]);
        }

        if(!empty($transportadora_magento)){
            $transportadora_magento = strtoupper($transportadora_magento);

            // $sqlD022 = mysql_query("SELECT D022_Id,D022_Nome_Empresa FROM D022 WHERE D022_Descricao_Ecommerce LIKE '%".$transportadora_magento."%'");
            // echo "<br>SELECT D022_Id,D022_Nome_Empresa FROM D022 WHERE D022_Descricao_Ecommerce LIKE '%".$transportadora_magento."%'";
            // if(mysql_num_rows($sqlD022) > 0){
            //     $rowD022 = mysql_fetch_assoc($sqlD022);
            //     $D022_Id = $rowD022['D022_Id'];
            //     $D022_Nome_Empresa = $rowD022['D022_Nome_Empresa'];
            // }else{
                $D022_Nome_Empresa = "NÃO IDENTIFICADA";
            // }

            // $sqlD036 = mysql_query("SELECT D036_Id FROM D036 WHERE D036_Descricao_Ecommerce LIKE '%".$transportadora_magento."%'");
            // if(mysql_num_rows($sqlD036) > 0){
            //     $rowD036 = mysql_fetch_assoc($sqlD036);
            //     $D036_Id = $rowD036['D036_Id'];
            // }
        }

    //caso nao tenha id do marketplace pega o id do pedido no magento
    if(empty($Id_integracommerce)){
        $Id_integracommerce=$orderInfo['increment_id'];
    }

    if(empty($parcelas)){
        $parcelas=1;
    }

    //populando portador dependendo do marketplace
    if(!empty($metodoPagamento)){
        // $sqlD027 = mysql_query("SELECT D027_Id FROM D027 WHERE D027_Descricao_Ecommerce LIKE '%".$metodoPagamento."%'");
        // if(mysql_num_rows($sqlD027) > 0){
        //     echo "achou um id";
        //     $rowD027 = mysql_fetch_assoc($sqlD027);
        //     $D027_Id = $rowD027['D027_Id'];
        // }    
    } 
    if(empty($D027_Id)){
        $D027_Id="";
    }

    $additional_information = (array) $pagamento['additional_information'];

    $metodoPagamento2 = $additional_information['metodo'];
    $metodoPagamento2 = explode(':',$metodoPagamento2);
    $metodoPagamento2 = strToUpper(trim($metodoPagamento2[1]));
    $metodoPagamento2 = $metodoPagamento."-".$metodoPagamento2;

    //cabeçalho do pedido
    $items = (array) $orderInfo['items'];
    //$T005["T005_Valor_Desconto_Manual"]=$orderInfo['discount_amount'];
    $T005["T005_Prazo_Entrega"]=$T005_Prazo_Entrega;
    $T005["T005_Valor_Total_Produtos"]=$orderInfo['subtotal'];
    $T005["T005_Valor_Total_Produtos_Sem_Desconto"]=$orderInfo['subtotal'];
    $T005["T005_Valor_Total"]=$orderInfo["grand_total"];
    $T005["T005_Valor_Frete"]=$orderInfo['shipping_amount'];
    $T005["T005_Observacao_Nota_Fiscal"]="Codigo pedido magento: ".$orderInfo['increment_id']." Codigo pedido Marketplace:".$Id_integracommerce." ".$pagamento['pagarme_transaction_id']."Dados da entrega:".$D022_Nome_Empresa;
    $T005["Id_Loja"]=$orderInfo['store_id'];
    //dentro do codigo ha um array $lojas que sera alimentado com as possiveis lojas 
    //$T005["T005_Pgto_Portador"]=;//vem do array de $lojas 
    //$T005["T005_Pgto_Primeiro_Vcto_Valor"]=;//vem do array $lojas
    //$T005["T005_Flag_Totalizar_Peso"]=($g['C031']['totalizarPesoVenda'] == 'S') ? 'S' : 'N';
    $T005["T005_Pedido_Ecommerce"]=$orderInfo['order_id'];
    $T005["T005_Id_Pedido_Externo"]=$orderInfo['increment_id'];
    $T005["C007_Id_Vendedor_Interno"] =$g['C031']['C007IdEcommerce'];
    //$T005["T005_Ordem_Compra"]=$orderInfo['order_id'];
    $T005["T005_Ordem_Compra"]=$Id_integracommerce;
    $T005["T005_Flag_Plataforma_Ecommerce"]=3;
    $T005["T005_D022_Id"] = $D022_Id;   
    $T005["T005_D036_Id"] = $D036_Id;        
    $T005["T005_Nome_Transportadora"] = $D022_Nome_Empresa;
    $T005["T005_Canal_Vendas_Ecommerce"] = $marketplace;
    $T005["T005_D027_Id"] = $D027_Id; 
    $T005["Comment_Forma_de_Pagamento"] = $Comment_Forma_de_Pagamento;  
    $T005["Metodo_Pagamento_2"] = $metodoPagamento2;  

    $T090["T090_D027_Id"] = $D027_Id;
    //Tratando parcelas de um pedido
    $string = $pagamento['installment_description'];

    if(!empty($string)){
        // echo "string nao vazia";
        $pagamentos = array();
        if (strpos($string, 'de')){
            $parcelas = substr($string,0,strpos($string,'x'));
            $valor = substr($string,strpos($string,'R$')+2,strpos($string,'sem'));
            $valor = floatval(str_replace(',','.',$valor));
            $numeroparcela = 1;
            $prazo = 30;
            for ($i=0; $i < $parcelas ; $i++) { 
               $vencimento = date('d/m/Y', strtotime("+".(($i+1)*30)." days",strtotime(date('Y-m-d'))));
               //$vencimento = gCorrigeDataInverte($vencimento);
               $T090["T090_Numero_Parcela"]=$numeroparcela;
               $T090["T090_Data_Vencimento"]=$vencimento;
               $T090["T090_Valor_Parcela"]=$valor;
               $T090["T090_Prazos_Dias"]=$prazo;
               $pagamentos[] = $T090;
               $prazo+=30;
               $numeroparcela++;
            }
        }
        else if (strpos($string, 'vista')){
            $valor = substr($string, strpos($string,'R$')+2);
            $valor = str_replace('.','',$valor);
            $valor = str_replace(',','.',$valor);
            $vencimento = date('Y-m-d', strtotime("+30 days"));
            $T090["T090_Prazos_Dias"]=30;
            $T090["T090_Numero_Parcela"]=1;
            $T090["T090_Data_Vencimento"]=$vencimento;
            $T090["T090_Valor_Parcela"]=$valor;
            $pagamentos[] = $T090;
        }

        else{
            $vencimento = date('Y-m-d', strtotime("+30 days"));
            $T090["T090_Prazos_Dias"]=30;
            $T090["T090_Numero_Parcela"]=1;
            $T090["T090_Data_Vencimento"]=$vencimento;
            $T090["T090_Valor_Parcela"]= $T005["T005_Valor_Total"];
            $pagamentos[] = $T090;
        } 
    }else if(!empty($parcelas)){
        //caso o campo installment description esteja em branco e as informações virem pelo campo comment
        //caso o numero de parcelas for maior que 0 faz o loop para inserir cada parcela o valor da parcela é (valortotal/parcelas)

            $valor_parcela = ($T005["T005_Valor_Total"]/$parcelas);
            $valor = round($valor_parcela,2);
            $valor = floatval(str_replace(',','.',$valor));
            $numeroparcela = 1;
            $prazo = 30;
            for ($i=0; $i < $parcelas ; $i++) { 
                   $vencimento = date('d/m/Y', strtotime("+".(($i+1)*30)." days",strtotime(date('Y-m-d'))));
                   //$vencimento = gCorrigeDataInverte($vencimento);
                   $T090["T090_Numero_Parcela"]=$numeroparcela;
                   $T090["T090_Data_Vencimento"]=$vencimento;
                   $T090["T090_Valor_Parcela"]=$valor;
                   $T090["T090_Prazos_Dias"]=$prazo;
                   $pagamentos[] = $T090;
                   $prazo+=30;
                   if($numeroparcela<=$parcelas){
                       $numeroparcela++; 
                   }else{
                        continue;
                   }
                   
                }
        }

//criando o array que importa os itens do pedido
    $produtos = array();
    $itens = (array) $orderInfo['items'];

    foreach ($itens as $key => $item) {
        
        $product = (array) $item;   

        $D001 = mysql_query("select D082_Marca 
                                from D001 
                            left join D049 on D049_D001_Id=D001_Id 
                            left join D082 on D082_Id=D049_D082_Id 
                                where D001_Codigo_Produto = '{$product['sku']}'");
        $mD001 = mysql_fetch_assoc($D001);

        //itens do pedido
        $T006["Peso_Unitario"]=$product['weight'];
        $T006["T006_Codigo_Produto"]=$product['sku'];
        $T006["T006_Descricao_Produto"]=$product['name'];
        $T006["T006_Quantidade"]=$product['qty_ordered'];
        $T006["T006_Valor_Preco_Original"]=$product['price'];
        $T006["T006_Valor_Preco_Sem_Desconto_Unitario"]=$product['price']-($product['discount_amount']/$product['qty_ordered']);
        $T006["T006_Valor_Total_Preco_Sem_Desconto"]=$product['price']*$product['qty_ordered'];
        $T006["T006_Percentual_Desconto"]=$product['discount_percent'];
        $T006["Codigo_Barras"]='';
        $T006["T006_Numero_Pedido_Compra"]=$orderInfo['increment_id'];
        $T006["T006_Preco_Venda_Ecommerce"]=$product['price']-($product['discount_amount']/$product['qty_ordered']);
        $T006["T006_Preco_Tabela_Ecommerce"]=$product['price'];
        $T006["T006_Valor_Desconto_Ecommerce"]=$product['discount_amount'];
        $T006["D082_Marca"] = $mD001['D082_Marca'];

        $produtos[] = $T006;
    }

    //Encontrando os dados para incluir os valores com #
    $mFiltro = explode('|',$g['C031']['filtroImportarDadosComHashtag']);        
    log("Inicio filtro hashtag");
    $encontrou = true;
    if (!empty($g['C031']['filtroImportarDadosComHashtag'])) {
        foreach ($mFiltro as $key => $filtro) {
            log('Loop filtro hashtag: ' . $filtro);
            $itemFiltro = explode('=',$filtro);
            $chave = trim($itemFiltro[0]);
            $campo = strToUpper(trim($T005[$chave]));
            $valores = trim($itemFiltro[1]);
            log('Loop filtro chave hashtag: ' . $chave . ' valores ' . $valores);
            $mValor = explode(';', $valores);
            $busca = false;
            foreach ($mValor as $key => $valores) {
                $valores = strToUpper(trim($valores));
                if (strpos(" $campo ",$valores)) {
                    log('Loop filtro encontrou hashtag: ' . "strpos({$campo},{$valores})");
                    $busca = true;
                    $T005["T005_Observacao_Pagamento"] .= "#".$valores." ";
                };
            }
            if (!$busca) {
                $encontrou = false;
                break;
            }
        }	
    }

    $T005['D024'] = $D024;
    $T005['T006'] = $produtos;
    $T005['T090'] = $pagamentos;

    // echo "<pre>";
    // print_r($T090);
    // echo "</pre>";

    echo "<pre>";
    print_r($T005);
    echo "</pre>";
    echo "<hr>";

?>