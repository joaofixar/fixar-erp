<?php
namespace hardness;
/*
* class.exportacao.php
*/

definirClasse('MAGENTOSOAP');

Class MagentoSoap_
{   
    public function importacao()
	{
       global $g, $logImportacao;
        log("entrou no importar magentoIntegracommerce");
        // $atualizacao=$g['C031']['IntervaloDeAtualizacao']*60;
        
        // echo '<meta HTTP-EQUIV="refresh" CONTENT="'.$atualizacao.'";URL="http://'.$_SERVER['HTTP_HOST'].'/crm/crm001/outro/Magento1GetOrdersSoap?ajax=true">';

        $msg =  "<h2>Importação Magento</h2><br>";

        $datestart = date('Y-m-d H:i:s');

        require_once("bibliotecas/classes/VEN014.php");
        $VEN014 = new VEN014();

        // $api_url_v1 = "http://www.stellasofia.com.br/api/soap/?wsdl=1";		
        // $username = 'integracao';
        // $password = '1VCPjoqpR7OE';
        // $status_pedido = 'pending';
        

        $api_url_v1 = $g['C031']['MagentoUrlApi'];		
        $username =  $g['C031']['MagentoConsumerKEy'];
        $password = $g['C031']['MagentoConsumerSecret'];
        $status_pedido = $g['C031']['MagentoStatusPedido'];

       
        try
        {
            $cli = new \SoapClient($api_url_v1,array('exceptions' => true));
            $ValidaUrl=1;
        }
        catch ( Exception $e )
        {
            log("achou erro");
            $mgs.="Url está indisponível no momento<br> Contate seu suporte técnico e informe a seguinte mensagem:<br>".$e;
            return $msg;
        }
        log("Testou");
        //retreive session id from login
        $session = $cli->login($username, $password);

        $filter = array(array('status' => $status_pedido));
        // $filtro = array(array('order_id' =>array('from' => '2','to'=>'15')));

        $listOrders = $cli->call($session, 'sales_order.list',$filter);
        $count = 0;
        $fail = 0;
        $sucess = 0;
        $repetido = 0;

        foreach ($listOrders as $key => $order) {
            $count++;
            $item = (array) $order;
            $increment_id = $item['increment_id'];

            if ($VEN014->procuraPedido($increment_id)){
                // Pedido já importado
                $repetido++;
                continue;
            }  

            $orderInfo = $cli->call($session, 'sales_order.info',$increment_id);

            $D024 = array();    
            $T005 = array();
            $D148 = array();
            $T006 = array();
            $T090 = array();
            $T090 = array();

            $customer_Id = $orderInfo['customer_id'];

            if($customer_Id == null || $customer_Id == ""){

                $logImportacao .= "Customer Id não foi identificado no pedido<br>";
                registrarLog(array("T005", 0, $g['empresaAtual'], "ID {$orderInfo['order_id']}", "Customer Id não foi identificado no pedido", $logImportacao, "N", "2"));
                mysqli_query("UPDATE T245 SET T245_Flag_Erro = 'S' WHERE T245_Referencia = 'ID {$orderInfo['order_id']}' AND T245_Tipo_Log = '2'",$g['conexaoBandoAuditoria']);
                echo "Customer Id não foi identificado no pedido";
                continue;
            }

            $cliente_dados = $cli->call($session, 'customer.info', "$customer_Id");
            $Customer_Campo_Dado = $cliente_dados['taxvat'];
            $D024_Inscricao_Estadual = $cliente_dados['ie'];

            //retirando dados do cliente		
                if(strlen($Customer_Campo_Dado)<=11){
                    $D024["D024_Cpf"]=str_replace(array('.', '/', '-'), '', trim($Customer_Campo_Dado));
                    $D024["D024_Cnpj"]='';
                    $D024["D024_Inscricao_Estadual"]='';
                }else{
                    $D024["D024_Cpf"]='';
                    $D024["D024_Cnpj"]=str_replace(array('.', '/', '-'), '', trim($Customer_Campo_Dado));
                    $D024["D024_Inscricao_Estadual"]=$D024_Inscricao_Estadual;
                    
                }
        
            $address_billing = (array) $orderInfo['billing_address'];
            $endereco_cobranca = explode(chr(10),$address_billing['street']);

            if(!empty($address_billing['email'])){
                $email_cliente = $address_billing['email'];
            }else{
                $email_cliente = $cliente_dados['email'];
            }
            
            if((stristr($address_billing['postcode'], "não especificado")) || (stristr($endereco_cobranca[0], "não especificado"))){
                $D024["D024_Endereco"]=$endereco_entrega[0];
                $D024["D024_Numero"]=$endereco_entrega[1];
                $D024["D024_Cep"]=$address_shipping['postcode'];
                $D024["D024_Complemento"]=trim($endereco_entrega[2]);
                $D024["D024_Bairro"]=$endereco_entrega[3];
                $D024["Cidade"]=$address_shipping['city'];
                $D024["UF"]=strtoupper($address_shipping['region']);
                $D024['Pais']=$address_shipping['country_id'];
            }else{
                $D024["D024_Endereco"]=$endereco_cobranca[0];
                $D024["D024_Numero"]=$endereco_cobranca[1];
                $D024["D024_Cep"]=$address_billing['postcode'];
                $D024["D024_Complemento"]=trim($endereco_cobranca[2]);
                $D024["D024_Bairro"]=$endereco_cobranca[3];
                $D024["Cidade"]=$address_billing['city'];
                $D024["UF"]=strtoupper($address_billing['region']);
                $D024['Pais']=$address_billing['country_id'];
            }
            $D024["D024_Nome_Fantasia"] = $address_billing['firstname'].' '.$address_billing['middlename'].' '.$address_billing['lastname']; 
            $D024["D024_Nome_Empresa"]  = $address_billing['firstname'].' '.$address_billing['middlename'].' '.$address_billing['lastname'];    
            //$D024["Razao_Social"]=$address_billing['firstname'].' '.$address_billing['lastname'];
            $D024["D024_DDD_Telefone_1"]='';
            $D024["D024_Telefone_1"]=$address_billing['telephone'];
            $D024["D024_DDD_Telefone_2"]='';
            $D024["D024_Telefone_2"]=$address_billing['fax'];
            $D024["D024_E_Mail"]=$address_billing['email'];
            $D024["D024_Web_Site"]="";
    

    
            $address_shipping = (array) $orderInfo['shipping_address'];
            $endereco_entrega = explode(chr(10),$address_shipping['street']);
    
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

            //tratando campo integracommerce_id
            $Id_integracommerce=$orderInfo['integracommerce_id'];
            $Id_integracommerce = str_replace("LU-"," ",$Id_integracommerce);
        

            //populando portador se magazine luiza
            $descricao=$orderInfo['shipping_description'];

            if(strpos($descricao, 'Magazine')){
                $T090["T090_D027_Id"] = 29;
            }else if($CNOVA){

            } else {
                $T090["T090_D027_Id"] = '';
            } 

            //tratando campo comment
            $History_Status = (array) $orderInfo['status_history'];

            //IDENTIFICANDO SE O PEDIDO VEM DO MARKETPLACE CNOVA 
            $Approved_payment = (array) $History_Status[1];
            $Approved_payment2 = (array) $History_Status[2];

            $Comment = $Approved_payment['comment'];
            $DetalhesComment = explode(":",$Approved_payment['comment']);
            $Comment2 = $Approved_payment2['comment'];
            $DetalhesComment2 = explode(":",$Approved_payment2['comment']);

            //TRATANDO O ID DO PEDIDO PARA UM NUMERO ESPECIFICO DO MARKETPLACE E O PORTADOR PARA DENTRO DO SISTEMA
            if(strpos($Comment, 'CNOVA')){
                $Id_integracommerce = preg_replace("/[^0-9]/", "", $DetalhesComment[1]);
                $marketplace = 'cnova';
            }

            if(strpos($Comment2, 'CNOVA')){
               $Id_integracommerce = preg_replace("/[^0-9]/", "", $DetalhesComment2[1]);
                $marketplace = 'cnova';
            }

            //tratando campo para pegar prazo de entrega
            $T005_Prazo_Entrega=trim($Comment[3]);
            $pagamento = (array) $orderInfo['payment'];


            //cabeçalho do pedido
            $items = (array) $orderInfo['items'];
            //$T005["T005_Valor_Desconto_Manual"]=$orderInfo['discount_amount'];
            $T005["T005_Prazo_Entrega"]=$T005_Prazo_Entrega;
            $T005["T005_Valor_Total_Produtos"]=$orderInfo['subtotal'];
            $T005["T005_Valor_Total_Produtos_Sem_Desconto"]=$orderInfo['subtotal'];
            $T005["T005_Valor_Total"]=$orderInfo["grand_total"];
            $T005["T005_Valor_Frete"]=$orderInfo['shipping_amount'];
            $T005["T005_Observacao_Nota_Fiscal"]=$orderInfo['increment_id']." ".$Id_integracommerce." ".$pagamento['pagarme_transaction_id']." ".$orderInfo['shipping_description'];
            $T005["Id_Loja"]=$orderInfo['store_id'];
            //dentro do codigo ha um array $lojas que sera alimentado com as possiveis lojas 
            //$T005["T005_Pgto_Portador"]=;//vem do array de $lojas 
            //$T005["T005_Pgto_Primeiro_Vcto_Valor"]=;//vem do array $lojas
            //$T005["T005_Flag_Totalizar_Peso"]=($g['C031']['totalizarPesoVenda'] == 'S') ? 'S' : 'N';
            $T005["T005_Pedido_Ecommerce"]=$orderInfo['order_id'];
            $T005["T005_Id_Pedido_Externo"]=$orderInfo['increment_id'];
            $T005["C007_Id_Vendedor_Interno"] = $g['C031']['C007IdEcommerce'];
            log("vendedor configurado:".$T005["C007_Id_Vendedor_Interno"]);


            //$T005["T005_Ordem_Compra"]=$orderInfo['order_id'];
            $T005["T005_Ordem_Compra"]=$Id_integracommerce;
            $T005["T005_Flag_Plataforma_Ecommerce"]=3;
                    
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
                $T006["T006_Numero_Pedido_Compra"]=$product['order_id'];
                $T006["T006_Codigo_Produto"]=$product['sku'];
                $T006["T006_Descricao_Produto"]=$product['name'];
                $T006["T006_Quantidade"]=$product['qty_ordered'];
                $T006["T006_Valor_Preco_Original"]=$product['price'];
                $T006["T006_Valor_Preco_Sem_Desconto_Unitario"]=$product['price']-($product['discount_amount']/$product['qty_ordered']);
                $T006["T006_Valor_Total_Preco_Sem_Desconto"]=$product['price']*$product['qty_ordered'];
                $T006["T006_Percentual_Desconto"]=$product['discount_percent'];
                $T006["Codigo_Barras"]='';
                $T006["T006_Numero_Pedido_Compra"]=$T005["T005_Ordem_Compra"];
                $T006["D082_Marca"] = $mD001['D082_Marca'];
        
                $produtos[] = $T006;
            }

            //populando portador se magazine luiza
            $descricao=$orderInfo['shipping_description'];
            //se o marketplace for magazine luiza, portador é magazine luiza(id 29)
            if(strpos($descricao, 'Magazine')){
                $T090["T090_D027_Id"] = 29;
            } else if (isset($marketplace)){
                   //se o marketplace for cnova, portador é cnova(id 21)
                 $T090["T090_D027_Id"] = 21;
            } else {
                $T090["T090_D027_Id"] = '';
            } 

            //Tratando parcelas de uma compra
            $string = $pagamento['installment_description'];
            $pagamentos = array();
            if (strpos($string, 'de')){
                $parcelas = substr($string,0,strpos($string,'x'));
                $valor = substr($string,strpos($string,'R$')+2,strpos($string,'sem'));
                $valor = floatval(str_replace(',','.',$valor));
                $numeroparcela = 1;
                $prazo = 30;
                for ($i=0; $i < $parcelas ; $i++) { 
                   $vencimento = date('d/m/Y', strtotime("+".(($i+1)*30)." days",strtotime(date('Y-m-d'))));
                   $vencimento = gCorrigeDataInverte($vencimento);
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


            $T005['D024'] = $D024;
            $T005['T006'] = $produtos;
            $T005['T090'] = $pagamentos;
            
            $retorno = $VEN014->cadastrarPedido($T005);  
                        
            if($retorno === false){
                $fracasso .= $T005["T005_Id_Pedido_Externo"] .", ";
                $fail++;
            }else{
                if (!empty($sucesso)) $sucesso.=', ';
                $sucesso .= $T005["T005_Id_Pedido_Externo"] ;
                $sucess++;			
            }       
            
        }   
            $sucesso .= '.';
            $total = $sucess+$fail+$repetido;
           
            
            if ($sucess == "0") {
                $msg .= "<b>Nenhum pedido foi importado.</b><br><br>";  
            } else{
                $msg .= "<b>Pedidos importados com sucesso: </b><br><br>";
                $msg .= "<b>$sucesso</b><br><br>"; 
            }

            $msg .= "<br><br>";
            $msg .= "<b>Pedidos encontrados: $total</b><br>";
            $msg .= "<b>Pedidos importados: $sucess</b><br>";
            $msg .= "<b>Pedidos que já foram importados anteriormente: $repetido</b><br>";
            $msg .= "<b>Pedidos não importados com erro: $fail</b><br><br>";

            $dateend = date('Y-m-d H:i:s');
            $intervalo = $datestart - $dateend;
            $sql = mysql_query("SELECT TIME_FORMAT(SEC_TO_TIME(TIMESTAMPDIFF(SECOND, '{$datestart}', '{$dateend}')),'%H:%i:%s')");
            $duracao = mysql_fetch_array($sql);

            $msg .= "<b>Procedimento durou $duracao[0].</b><br><br>"; 
            
            return $msg;
    }


    public function importacaoAnymarket()
    {
       global $g, $logImportacao;

       log("entrou no importacaoAnymarket");

        $msg =  "<h2>Importação Magento</h2><br>";

        $datestart = date('Y-m-d H:i:s');

        require_once("bibliotecas/classes/VEN014.php");
        $VEN014 = new VEN014();

        // $api_url_v1 = "http://www.stellasofia.com.br/api/soap/?wsdl=1";      
        // $username = 'integracao';
        // $password = '1VCPjoqpR7OE';
        // $status_pedido = 'pending';

        $api_url_v1 = $g['C031']['MagentoUrlApi'];      
        $username =  $g['C031']['MagentoConsumerKEy'];
        $password = $g['C031']['MagentoConsumerSecret'];
        $status_pedido = $g['C031']['MagentoStatusPedido'];
      
        try
        {
            $cli = new \SoapClient($api_url_v1,array('exceptions' => true));
            $ValidaUrl=1;
        }
        catch ( Exception $e )
        {
            log("achou erro");
            $mgs.="Url está indisponível no momento<br> Contate seu suporte técnico e informe a seguinte mensagem:<br>".$e;
            return $msg;
        }
        log("Testou");
        //retreive session id from login
        $session = $cli->login($username, $password);

        $filter = array(array('status' => $status_pedido));
        // $filtro = array(array('order_id' =>array('from' => '2','to'=>'15')));

        $listOrders = $cli->call($session, 'sales_order.list',$filter);
        $count = 0;
        $fail = 0;
        $sucess = 0;
        $repetido = 0;

        foreach ($listOrders as $key => $order) {
            $count++;
            $item = (array) $order;
            $increment_id = $item['increment_id'];

            if ($VEN014->procuraPedido($increment_id)){
                // Pedido já importado
                $repetido++;
                continue;
            }  

            $orderInfo = $cli->call($session, 'sales_order.info',$increment_id);
  
            $D024 = array();    
            $T005 = array();
            $D148 = array();
            $T006 = array();
            $T090 = array();

            $Customer_Id = $orderInfo['customer_id'];

            $cliente_dados = $cli->call($session, 'customer.info', "$Customer_Id");

            //arrays com informações do magento
            $Customer_Campo_Dado = $cliente_dados['taxvat'];
            $D024_Inscricao_Estadual = $cliente_dados['ie'];
            $pagamento = (array) $orderInfo['payment'];
            $address_billing = (array) $orderInfo['billing_address'];
            $endereco_cobranca = explode(chr(10),$address_billing['street']);
            $address_shipping = (array) $orderInfo['shipping_address'];
            $endereco_entrega = explode(chr(10),$address_shipping['street']);
            $informacoes_shipping = $orderInfo['shipping_description'];

            if(!empty($address_billing['email'])){
                $email_cliente = $address_billing['email'];
            }else{
                $email_cliente = $cliente_dados['email'];
            }

            //variaveis internas para validações
            $Id_integracommerce="";
            $marketplace="";
            $T005_Prazo_Entrega="";
            $transportadora_magento="";
            $D036_Id="";
            $D022_Id="";
            $D022_Nome_Empresa = "";

            //retirando dados do cliente                
                if(strlen($Customer_Campo_Dado)<=11){
                    $D024["D024_Cpf"]=str_replace(array('.', '/', '-'), '', trim($Customer_Campo_Dado));
                    $D024["D024_Cnpj"]='';
                    $D024["D024_Inscricao_Estadual"]='';
                }else{
                    $D024["D024_Cpf"]='';
                    $D024["D024_Cnpj"]=str_replace(array('.', '/', '-'), '', trim($Customer_Campo_Dado));
                    $D024["D024_Inscricao_Estadual"]=$D024_Inscricao_Estadual;
                    
                }
        
            if((stristr($address_billing['postcode'], "não especificado")) || (stristr($endereco_cobranca[0], "não especificado"))){
                $D024["D024_Endereco"]=$endereco_entrega[0];
                $D024["D024_Numero"]=$endereco_entrega[1];
                $D024["D024_Cep"]=$address_shipping['postcode'];
                $D024["D024_Complemento"]=trim($endereco_entrega[2]);
                $D024["D024_Bairro"]=$endereco_entrega[3];
                $D024["Cidade"]=$address_shipping['city'];
                $D024["UF"]=strtoupper($address_shipping['region']);
                $D024['Pais']=$address_shipping['country_id'];
            }else{
                $D024["D024_Endereco"]=$endereco_cobranca[0];
                $D024["D024_Numero"]=$endereco_cobranca[1];
                $D024["D024_Cep"]=$address_billing['postcode'];
                $D024["D024_Complemento"]=trim($endereco_cobranca[2]);
                $D024["D024_Bairro"]=$endereco_cobranca[3];
                $D024["Cidade"]=$address_billing['city'];
                $D024["UF"]=strtoupper($address_billing['region']);
                $D024['Pais']=$address_billing['country_id'];
            }

            //VALIDAÇÃO EMAIL CLIENTE
            if(empty($address_billing['email'])){
               $D024["D024_E_Mail"] = $cliente_dados['email'];
            }else{
                $D024["D024_E_Mail"]=$address_billing['email'];
            }

            $D024["D024_Nome_Fantasia"] = $address_billing['firstname'].' '.$address_billing['middlename'].' '.$address_billing['lastname']; 
            $D024["D024_Nome_Empresa"]  = $address_billing['firstname'].' '.$address_billing['middlename'].' '.$address_billing['lastname'];    
            //$D024["Razao_Social"]=$address_billing['firstname'].' '.$address_billing['lastname'];
            $D024["D024_DDD_Telefone_1"]='';
            $D024["D024_Telefone_1"]=$address_billing['telephone'];
            $D024["D024_DDD_Telefone_2"]='';
            $D024["D024_Telefone_2"]=$address_billing['fax'];
            $D024["D024_Web_Site"]="";
    
            //VALIDAÇÃO EMAIL ENTREGA
            if(empty($address_shipping['email'])){
                $D148["D148_Email"] = $cliente_dados['email'];
            }else{
                $D148["D148_Email"]=$address_shipping['email'];
            }

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
            $D024['D148'] = $D148;
           
            if(stristr($pagamento["method"], "moip")){
                $marketplace = "SITE";
            }

            $shipping = explode("-",$informacoes_shipping);
            $transportadora_magento = trim($shipping[count($shipping)-2]);
            $T005_Prazo_Entrega = trim($shipping[count($shipping)-1]);

            //Inserindo o D022_Id dentro do T005_D022_Id a partir da transportadora que veio do magento e o D036 correspondete ao T005_D036_Id
            $transportadora_magento = trim($transportadora_magento);
            $transportadora_magento = strtoupper($transportadora_magento);

            if(!empty($transportadora_magento)){

                $sqlD022 = mysql_query("SELECT D022_Id,D022_Nome_Empresa FROM D022 WHERE D022_Descricao_Ecommerce LIKE '%".$transportadora_magento."%'");
                if(mysql_num_rows($sqlD022) > 0){
                    $rowD022 = mysql_fetch_assoc($sqlD022);
                    $D022_Id = $rowD022['D022_Id'];
                    $D022_Nome_Empresa = $rowD022['D022_Nome_Empresa'];
                }

                $sqlD036 = mysql_query("SELECT D036_Id FROM D036 WHERE D036_Descricao_Ecommerce LIKE '%".$transportadora_magento."%'");
                if(mysql_num_rows($sqlD036) > 0){
                    $rowD036 = mysql_fetch_assoc($sqlD036);
                    $D036_Id = $rowD036['D036_Id'];
                }
            }else{
                $transportadora_magento="NAO IDENTIFICADA";
            }


            //tratando campo 'comment' que contém as informações sobre o pedido
            $History_Status = (array)$orderInfo['status_history'];
            // echo "<pre>";
            // print_r($History_Status);
            // echo "</pre>";
            // echo "<hr>";

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
                        if(stripos($informacao[0],"Id ")){
                            $Id_integracommerce=trim(preg_replace("/[^0-9]/", " ",$informacao[0]));
                        }

                        //identificando qual é o marketplace e pegando o nome
                        if((stristr($informacao[0],"Canal De Vendas:"))){
                            $palavras = explode(":", $informacao[0]);
                            $marketplace = trim($palavras[count($palavras)-1]);
                            $marketplace = str_replace("</b>","",$marketplace);
                            $marketplace = str_replace("<br>","",$marketplace);
                        
                        }else if(empty($marketplace)){
                            $marketplace="NAO IDENTIFICADO";
                        }

                        //identificando a data de entrega se existir e retirando da string
                        if(strpos($informacao[0],"Entrega")){
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
                    }
                }else{
                    // echo "comment vazio<br>";
                    continue;
                }
             
            }
            //caso nao tenha id do marketplace pega o id do pedido no magento
            if(empty($Id_integracommerce)){
                $Id_integracommerce=$orderInfo['increment_id'];
            }

            //populando portador dependendo do marketplace
            if(!empty($marketplace)){               
                $sqlD027 = mysql_query("SELECT D027_Id,D027_Parcelas_Ecommerce FROM D027 WHERE D027_Descricao_Ecommerce LIKE '%".$marketplace."%'");
                if(mysql_num_rows($sqlD027) > 0){
                    $rowD027 = mysql_fetch_assoc($sqlD027);
                    $D027_Id = $rowD027['D027_Id'];
                }else{
                    $D027_Id="";
                }    
            }

            if($rowD027['D027_Parcelas_Ecommerce']>0){
                $parcelas=$rowD027['D027_Parcelas_Ecommerce'];
            }

            if(empty($parcelas) || $parcelas==0){
                $parcelas=1;
            }

            log("parcelas:".$parcelas);
            //cabeçalho do pedido
            $items = (array) $orderInfo['items'];
            //$T005["T005_Valor_Desconto_Manual"]=$orderInfo['discount_amount'];
            $T005["T005_Prazo_Entrega"]=$T005_Prazo_Entrega;
            $T005["T005_Valor_Total_Produtos"]=$orderInfo['subtotal'];
            $T005["T005_Valor_Total_Produtos_Sem_Desconto"]=$orderInfo['subtotal'];
            $T005["T005_Valor_Total"]=$orderInfo["grand_total"];
            $T005["T005_Valor_Frete"]=$orderInfo['shipping_amount'];
            $T005["T005_Observacao_Nota_Fiscal"]="Codigo pedido magento: ".$orderInfo['increment_id']." Codigo pedido Marketplace:".$Id_integracommerce." ".$pagamento['pagarme_transaction_id']."  Dados da entrega:".$orderInfo['shipping_description']."  Transportadora:".$transportadora_magento;
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

            $T090["T090_D027_Id"] = $D027_Id;

            //Tratando parcelas de um pedido
            $string = $pagamento['installment_description'];
            $pagamentos = array();

            if(!empty($string)){

                log("entrou no string do installment_description");
               
                if (strpos($string, 'de')){
                    $parcelas = substr($string,0,strpos($string,'x'));
                    $valor = substr($string,strpos($string,'R$')+2,strpos($string,'sem'));
                    $valor = floatval(str_replace(',','.',$valor));
                    $numeroparcela = 1;
                    $prazo = 30;
                    for ($i=0; $i < $parcelas ; $i++) { 
                       $vencimento = date('d/m/Y', strtotime("+".(($i+1)*30)." days",strtotime(date('Y-m-d'))));
                       $vencimento = gCorrigeDataInverte($vencimento);
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
                log("encontrou parcelas e entrou no T090");
                $valor_parcela = ($T005["T005_Valor_Total"]/$parcelas);
                $valor = round($valor_parcela,2);
                $valor = floatval(str_replace(',','.',$valor));
                $numeroparcela = 1;
                $prazo = 30;
                for ($i=0; $i < $parcelas ; $i++) { 
                    log("pagamento loop T090 $i vez");
                    $vencimento = date('d/m/Y', strtotime("+".(($i+1)*30)." days",strtotime(date('Y-m-d'))));
                    $vencimento = gCorrigeDataInverte($vencimento);
                    $T090["T090_Numero_Parcela"]=$numeroparcela;
                    $T090["T090_Data_Vencimento"]=$vencimento;
                    $T090["T090_Valor_Parcela"]=$valor;
                    $T090["T090_Prazos_Dias"]=$prazo;

                    log("Pedido - ".$T005["T005_Id_Pedido_Externo"]."     inserindo parcelas do portador-Numero da parcela:$numeroparcela   -     Vencimento:$vencimento    -        Valor da parcela:$valor    -       Prazo em dias:$prazo");

                    $prazo+=30;
                    if($numeroparcela<=$parcelas){
                       $numeroparcela++; 
                    }else{
                        continue;
                    }
                    $pagamentos[] = $T090;
                }
            }

            //criando o array que importa os itens do pedido
            $produtos = array();
            $itens = (array) $orderInfo['items'];

            foreach ($itens as $key => $item) {
            
                $product = (array) $item;   

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

                $produtos[] = $T006;
            }

          

            $T005['D024'] = $D024;
            $T005['T006'] = $produtos;
            $T005['T090'] = $pagamentos;
                
            $retorno = $VEN014->cadastrarPedido($T005);  
                        
            if($retorno === false){
                $fracasso .= $T005["T005_Id_Pedido_Externo"] .", ";
                $fail++;
            }else{
                if (!empty($sucesso)) $sucesso.=', ';
                $sucesso .= $T005["T005_Id_Pedido_Externo"] ;
                $sucess++;          
            }       
            
        }   
            $sucesso .= '.';
            $total = $sucess+$fail+$repetido;
           
            
            if ($sucess == "0") {
                $msg .= "<b>Nenhum pedido foi importado.</b><br><br>";  
            } else{
                $msg .= "<b>Pedidos importados com sucesso: </b><br><br>";
                $msg .= "<b>$sucesso</b><br><br>"; 
            }

            $msg .= "<br><br>";
            $msg .= "<b>Pedidos encontrados: $total</b><br>";
            $msg .= "<b>Pedidos importados: $sucess</b><br>";
            $msg .= "<b>Pedidos que já foram importados anteriormente: $repetido</b><br>";
            $msg .= "<b>Pedidos não importados com erro: $fail</b><br><br>";

            $dateend = date('Y-m-d H:i:s');
            $intervalo = $datestart - $dateend;
            $sql = mysql_query("SELECT TIME_FORMAT(SEC_TO_TIME(TIMESTAMPDIFF(SECOND, '{$datestart}', '{$dateend}')),'%H:%i:%s')");
            $duracao = mysql_fetch_array($sql);

            $msg .= "<b>Procedimento durou $duracao[0].</b><br><br>"; 
            
            return $msg;
    }

    /**
     * atualizarPedidoMagento
     * @param $T005_Id string
     * @return bool
     */
    public function atualizarPedidoMagento($T005_Id)
    {
        global $g, $logImportacao,$confUsuario;

        //Criar arquivo texto logTxt
        $Dir = $g['pathDados'] . 'tmp/';
        is_dir($Dir) or mkdir($Dir, 0777, true);
        $arquivoNome = "{$Dir}Magento-{$T005_Id}.txt";
        //@unlink($arquivoNome);
        $h = fopen($arquivoNome, "a");


        $sqlPedido = mysql_query("SELECT T005_Id_Pedido_Externo,
                                         T005_Shipment_Increment_Id_Magento,
                                         T007_Chave_Acesso_Nfe,
                                         T007_Numero_Nota_Fiscal,
                                         CONCAT((DATE_FORMAT(T007_Data_Emissao,'%d/%m/%Y')),' ',T007_Hora_Emissao) AS Data_Hora_Emissao,
                                         T007_Serie,
                                         T007_D024_Id,
                                         T007_Id,
                                         T268_Id,
                                         T268_Codigo_Rastreio,
                                         T007_Nome_Transportadora,
                                         T005_Data_Hora_Envio_Ecommerce 
                                         FROM T005 
                                         LEFT JOIN D036 ON D036_Id=T005_D036_Id 
                                         LEFT JOIN T007 ON T007_T005_Id=T005_Id 
                                         LEFT JOIN D022 ON D022_Id=T007_D022_Id 
                                         LEFT JOIN T268 ON T268_T007_Id=T007_Id 
                                         WHERE T005_Id = '{$T005_Id}'");

        $pedido    = mysql_fetch_array($sqlPedido);

        if(empty($pedido['T005_Id_Pedido_Externo'])){
            return 'Id pedido Magento não informado';
        }
        // if(empty($pedido['T007_Numero_Nota_Fiscal']) || empty($pedido['T007_Chave_Acesso_Nfe']) || empty($pedido['T007_Numero_Protocolo_Nfe'])){
        //     return 'Nota fiscal não ainda transmitida';
        // }
        $id = $pedido['T005_Id_Pedido_Externo'];
        //$id = '';

        $logTxt = "Select dos dados do pedido {$id}\r\n";
        $r = fwrite($h, $logTxt);
        
        $api_url_v1 = $g['C031']['MagentoUrlApi'];      
        $username =  $g['C031']['MagentoConsumerKEy'];
        $password = $g['C031']['MagentoConsumerSecret'];
        $status_pedido = $g['C031']['MagentoStatusPedido'];
        $status_pedido_enviado = $g['C031']['MagentoStatusPedidoEnviado'];
        $notificar_cliente = $g['C031']['NotificarCliente'];
        $comentarios_integrador = $g['C031']['CampoComentarioIntegrador'];
        $comentarios_cliente = $g['C031']['CampoComentarioCliente'];
        $forma_entrega = $g['C031']['MagentoCodigoCarrier'];
        $enviarIntelipost = $g['C031']['IntelipostPlataformaEnviar'];
        
        $logTxt = "forma de entrega:{$forma_entrega}\r\n";
        $r = fwrite($h, $logTxt);
      
        log("forma de entrega:$forma_entrega");

        $result = "";

        if(!empty($forma_entrega)){
            if($pedido['T005_Data_Hora_Envio_Ecommerce']!='0000-00-00 00:00:00' && empty($pedido['T005_Shipment_Increment_Id_Magento'])){
                $result .= "Pedido já enviado e não possui Shipment Increment Id. Informe o suporte técnico!<br/>";
            }
        }

        if(empty($comentarios_cliente) && empty($comentarios_integrador)){
            $result .= "Os comentario de integrador e cliente estão em branco!<br/>";
        }else{
            if (empty($pedido['T007_Chave_Acesso_Nfe'])) {        
                $result .= "Chave de acesso da NFe:".$pedido['T007_Numero_Nota_Fiscal']." está em branco<br/>";
            }

            if (empty($pedido['T007_Numero_Nota_Fiscal'])) {
                $result .= "Numero da NFe está em branco<br/>";
            }

            if (empty($pedido['Data_Hora_Emissao'])) {
                $result .= "Data de emissão NFe:".$pedido['T007_Numero_Nota_Fiscal']." está em branco<br/>";
            }
        }

        if(!empty($result)){
            mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Atualização do faturamento no magento falhou!<br/>Erro:{$result}','')");
            return $result;
        }

        $logTxt = "result:{$result}\r\n";
        $r = fwrite($h, $logTxt);
       
        log("1-entrou no atualizar magento");
        $logTxt = "1-entrou no atualizar magento\r\n";
        $r = fwrite($h, $logTxt);
     

        try
        {
            $cli = new \SoapClient($api_url_v1,array('exceptions' => true));
        }
        catch ( Exception $e )
        {
            $mgs.="Url está indisponível no momento<br> Contate seu suporte técnico e informe a seguinte mensagem:<br>".$e;
            return $msg;
        }

        $logTxt = "2-Testou atualizar pedido no magento\r\n"."3-Id pedido:". $T005_Id."\r\n";
        $r = fwrite($h, $logTxt);
       
        log("2-Testou atualizar pedido no magento");
        log("3-Id pedido:". $T005_Id);

        //retreive session id from login
        $session = $cli->login($username, $password);

        //tratar dados do campo coentario integrador
        /*
            NUMERO DA NOTA:[NUMERO_NOTA]
            CHAVE DE ACESSO:[CHAVE_ACESSO_NOTA]
            NUMERO DE SÉRIE DA NOTA:[NUMERO_SERIE_NOTA]
            DATA DE EMISSÃO:[DATA_EMISSAO_NOTA]
            LINK DANFE:[LINK_DANFE]
            Numero rastreio:[NUMERO_RASTREIO]
            
            [NUMERO_NOTA]
            |[CHAVE_ACESSO_NOTA]|[NUMERO_SERIE_NOTA]|[DATA_EMISSAO_NOTA]|[LINK_DANFE]|[NUMERO_RASTREIO]
        */
        $Query = md5($pedido['T007_D024_Id'].'|'.$pedido['T007_Id']);
        $link_danfe = "{$confUsuario['urlRaiz']}/hardness3/hardness/danfe/imprimir_danfe.php?Query={$Query}&vlrNF=S&T007_Id={$pedido['T007_Id']}";

        $logTxt = "5-data hora:".$pedido['Data_Hora_Emissao']."\r\n";
        $r = fwrite($h, $logTxt);
     
        log("5-data hora:".$pedido['Data_Hora_Emissao']);
   
        $comentarios_integrador = str_replace("[CHAVE_ACESSO_NOTA]",$pedido['T007_Chave_Acesso_Nfe'], $comentarios_integrador);
        $comentarios_integrador = str_replace("[NUMERO_NOTA]",$pedido['T007_Numero_Nota_Fiscal'] , $comentarios_integrador);
        $comentarios_integrador = str_replace("[NUMERO_SERIE_NOTA]",$pedido['T007_Serie'], $comentarios_integrador);
        $comentarios_integrador = str_replace("[DATA_EMISSAO_NOTA]",gCorrigeData($pedido['Data_Hora_Emissao']), $comentarios_integrador);
        $comentarios_integrador = str_replace("[DATA_EMISSAO_NOTA]",gCorrigeData($pedido['Data_Hora_Emissao']), $comentarios_integrador);
        $comentarios_integrador = str_replace("[NUMERO_RASTREIO]",$pedido['T268_Codigo_Rastreio'], $comentarios_integrador);

        //tratar dado campo comentario cliente       
        $comentarios_cliente = str_replace("[CHAVE_ACESSO_NOTA]",$pedido['T007_Chave_Acesso_Nfe'], $comentarios_cliente);
        $comentarios_cliente = str_replace("[NUMERO_NOTA]",$pedido['T007_Numero_Nota_Fiscal'] , $comentarios_cliente);
        $comentarios_cliente = str_replace("[NUMERO_SERIE_NOTA]",$pedido['T007_Serie'], $comentarios_cliente);
        $comentarios_cliente = str_replace("[DATA_EMISSAO_NOTA]",gCorrigeData($pedido['Data_Hora_Emissao']), $comentarios_cliente);
        $comentarios_cliente = str_replace("[LINK_DANFE]",$link_danfe, $comentarios_cliente);
        $comentarios_cliente = str_replace("[NUMERO_RASTREIO]",$pedido['T268_Codigo_Rastreio'], $comentarios_cliente);
        $comentarios_cliente = str_replace("[PEDIDO_EXTERNO]",$pedido['T005_Id_Pedido_Externo'], $comentarios_cliente);

        // log("comentarios_integrador\n".$comentarios_integrador);
        // log("comentarios_cliente\n".$comentarios_cliente);
        // log("pedido status\n".$status_pedido_enviado);
        // log("notificar cliente\n".$notificar_cliente);

        if(!empty($comentarios_integrador)){
            $logTxt = "5.5 - entrou no comentario integrador".$result."\r\n";
            $r = fwrite($h, $logTxt);

            $logTxt = "5.5 - chamando função magento add comment. status pedido enviado:".$status_pedido_enviado." orderIncrementId:".$id. " comment:".$comentarios_integrador."\r\n";
            $r = fwrite($h, $logTxt);

            $result = $cli->call($session, 'sales_order.addComment', array('orderIncrementId' => $id,'status' => $status_pedido_enviado,'comment' => $comentarios_integrador));
            // $result = true;
            log("6-result do integrador".$result);

            $logTxt = "6-result do integrador".$result."\r\n";
            $r = fwrite($h, $logTxt);
           
            if($result===true){
                mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Integrador - Faturamento atualizado no magento com sucesso!','')");
                mysql_query("UPDATE T005 set T005_Data_Hora_Envio_Ecommerce=CURRENT_TIMESTAMP where T005_Id={$T005_Id}");
                 $logTxt = "Integrador - Faturamento atualizado no magento com sucesso!\r\n";
                $r = fwrite($h, $logTxt);
               
            }else{
                mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Integrador - Atualização do faturamento no magento falhou.<br/>Falha na comunicação com magento.','')");
                 $logTxt = "Integrador - Atualização do faturamento no magento falhou.<br/>Falha na comunicação com magento.\r\n";
                $r = fwrite($h, $logTxt);
               
            }
        }
             
        if($notificar_cliente!="1"){
            $notificar_cliente=null;
        }

        if(!empty($comentarios_cliente)){
             $logTxt = "5.5 - entrou no comentario cliente".$result."\r\n";
            $r = fwrite($h, $logTxt);

             $logTxt = "5.5 - chamando função magento add comment cliente. status pedido enviado:".$status_pedido_enviado." orderIncrementId:".$id. " comment:".$comentarios_cliente."\r\n";
            $r = fwrite($h, $logTxt);


            $result = $cli->call($session, 'sales_order.addComment', array('orderIncrementId' => $id,'status' => $status_pedido_enviado,'comment' => $comentarios_cliente, 'notify' => $notificar_cliente));
            log("6-result do cliente".$result);
            // $result = true;

            $logTxt = "6-result do cliente".$result."\r\n";
            $r = fwrite($h, $logTxt);
           

            if($result===true){
                mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Cliente - Faturamento atualizado no magento com sucesso!','')");
                mysql_query("UPDATE T005 set T005_Data_Hora_Envio_Ecommerce=CURRENT_TIMESTAMP where T005_Id={$T005_Id}");
                $logTxt = "Cliente - Faturamento atualizado no magento com sucesso!\r\n";
                $r = fwrite($h, $logTxt);
                
            }else{
                mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Cliente - Atualização do faturamento no magento falhou.<br/>Falha na comunicação com magento.','')");
                $logTxt = "Cliente - Atualização do faturamento no magento falhou.<br/>Falha na comunicação com magento.\r\n";
                $r = fwrite($h, $logTxt);
                
            }
        }

        $codigo_enviado = "";
        if($pedido['T268_Id']>0){
            $codigo_enviado = $pedido['T268_Codigo_Rastreio'];
        }else{
            $codigo_enviado = $pedido['T007_Numero_Nota_Fiscal'];
        }

        $T007_Nome_Transportadora = $pedido["T007_Nome_Transportadora"];

        log("7-codigo_enviado:".$codigo_enviado);

        $logTxt = "7-codigo_enviado:".$codigo_enviado."\r\n";
        $r = fwrite($h, $logTxt);
     
        if(!empty($codigo_enviado)){ 

            if($pedido["T005_Shipment_Increment_Id_Magento"]=="" || $pedido["T005_Shipment_Increment_Id_Magento"]==null){
                $shipmentIncrementId = $cli->call($session, 'order_shipment.create', array('orderIncrementId' => $id));
                mysql_query("UPDATE T005 set T005_Shipment_Increment_Id_Magento={$shipmentIncrementId} where T005_Id={$T005_Id}");
            }else{
                $shipmentIncrementId=$pedido["T005_Shipment_Increment_Id_Magento"];
            }
           
            log("8-shipmentIncrementId:".$shipmentIncrementId);
            $logTxt = "8-shipmentIncrementId:".$shipmentIncrementId."\r\n";
            $r = fwrite($h, $logTxt);
          
           
            if(!empty($shipmentIncrementId)){
                $retorno = $cli->call($session, 'sales_order_shipment.addTrack', array('shipmentIncrementId' => $shipmentIncrementId, 'carrier' => $forma_entrega, 'title' =>  $T007_Nome_Transportadora, 'trackNumber' => $codigo_enviado));   
                // $retorno = true; 
            }else{
                mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Atualização do rastreio no magento falhou!<br/>Erro:Falha de comunicação com magento!','')");

                $logTxt = "Atualização do rastreio no magento falhou!<br/>Erro:Falha de comunicação com magento!\r\n";
                $r = fwrite($h, $logTxt);
              
            }
             
           if($retorno){
                mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Código de rastreio enviado ao magento com sucesso ({$retorno})!','')");
                mysql_query("UPDATE T005 set T005_Data_Hora_Envio_Ecommerce=CURRENT_TIMESTAMP where T005_Id={$T005_Id}");

                $logTxt = "Código de rastreio enviado ao magento com sucesso ({$retorno})!.\r\n";
                $r = fwrite($h, $logTxt);
              
                log("enviou código de rastreio para o magento");

                $logTxt = "enviou código de rastreio para o magento\r\n";
                $r = fwrite($h, $logTxt);
             

                if($enviarIntelipost==1){
                    log("configurado para mandar os dados da intelipost pelo magento");

                    $logTxt = "configurado para mandar os dados da intelipost pelo magento\r\n";
                    $r = fwrite($h, $logTxt);
                  

                    require_once('bibliotecas/webservices/intelipost/apiIntelipost.php');
                    $Intelipost = new Intelipost();

                    //Verifica os pedidos
                    // $retornoIntelipost[0] = "SUCCESS"; 
                    $retornoIntelipost = $Intelipost->criarPedido($T005_Id);

                    if($retornoIntelipost[0]=="SUCCESS"){
                        log("realizou o envio dos dados para o intelipost");
                        $logTxt = "realizou o envio dos dados para o intelipost\r\n";
                        $r = fwrite($h, $logTxt);
                      
                        mysql_query("UPDATE T005 set T005_Data_Envio_Elogistics=CURRENT_TIMESTAMP where T005_Id={$T005_Id}");
                    }
                }

            }else{
                $logTxt = "Atualização do código de rastreio no magento falhou.<br/>Falha na comunicação com magento.\r\n";
                $r = fwrite($h, $logTxt);
                fclose($h);
                mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Atualização do código de rastreio no magento falhou.<br/>Falha na comunicação com magento.','')");
            }
        }else{
            $logTxt = "Atualização do código de rastreio no magento falhou!<br/>Erro:Código de rastreio esta em branco!.\r\n";
            $r = fwrite($h, $logTxt);
           
            mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Atualização do código de rastreio no magento falhou!<br/>Erro:Código de rastreio esta em branco!','')");
        }
        $logTxt = "Finalização do processo result: {$result}!.\r\n";
        $r = fwrite($h, $logTxt);
        fclose($h);
       return $result;
    }

    //---------------------------------------------------FUNÇÕES PARA IMPORTAÇÃO EM ETAPAS----------------------------------------------------

     /**
     * buscarPedidosMagento
     * @return array
     */
    public function buscarPedidosMagento()
    {
        global $g;
        $datestart = date('Y-m-d H:i:s');

        $api_url_v1 = $g['C031']['MagentoUrlApi'];      
        $username =  $g['C031']['MagentoConsumerKEy'];
        $password = $g['C031']['MagentoConsumerSecret'];
        $status_pedido = $g['C031']['MagentoStatusPedido'];

        $cli = new \SoapClient($api_url_v1);        
        
        //Retreive session id from login
        $session = $cli->login($username, $password);

        $filter = array(array('status' => $status_pedido));

        $listOrders = $cli->call($session, 'sales_order.list',$filter);
        $numPedidos = count($listOrders);
        // if($numPedidos<=0){
        //     return 0;
        // }
        return $listOrders;
    }

    public function tratarDadosOrderAnymarket($order)
    {
        global $g;

        $api_url_v1 = $g['C031']['MagentoUrlApi'];      
        $username =  $g['C031']['MagentoConsumerKEy'];
        $password = $g['C031']['MagentoConsumerSecret'];
        $status_pedido = $g['C031']['MagentoStatusPedido'];

        $cli = new \SoapClient($api_url_v1);        
        
        //Retreive session id from login
        $session = $cli->login($username, $password);

        $item = (array) $order;
        $increment_id = $item['increment_id'];

        $orderInfo = $cli->call($session, 'sales_order.info',$increment_id);

        $D024 = array();    
        $T005 = array();
        $D148 = array();
        $T006 = array();
        $T090 = array();

        
        $Customer_Campo_Dado = "";
        $D024_Inscricao_Estadual = "";
        $email_cliente_cadastro = "";

        $Customer_Id = $orderInfo['customer_id'];
        if(!empty($Customer_Id)){
            $cliente_dados = $cli->call($session, 'customer.info', "$Customer_Id");
            $Customer_Campo_Dado = str_replace(array('.', '/', '-'), '',$cliente_dados['taxvat']);
            $D024_Inscricao_Estadual = $cliente_dados['ie'];
            $email_cliente_cadastro = $cliente_dados['email'];
        }

        $pagamento = (array) $orderInfo['payment'];
        $address_billing = (array) $orderInfo['billing_address'];
        $endereco_cobranca = explode(chr(10),$address_billing['street']);
        $address_shipping = (array) $orderInfo['shipping_address'];
        $endereco_entrega = explode(chr(10),$address_shipping['street']);
        $informacoes_shipping = $orderInfo['shipping_description'];
        $Customer_CNPJ = $orderInfo['customer_cnpj'];

        if(!empty($address_billing['email'])){
            $email_cliente = $address_billing['email'];
        }else{
            $email_cliente = $email_cliente_cadastro;
        }

        //variaveis internas para validações
        $Id_integracommerce="";
        $marketplace="";
        $contaMarketplace="";
        $T005_Prazo_Entrega="";
        $transportadora_magento="";
        $D036_Id="";
        $D022_Id="";
        $D022_Nome_Empresa = "";
        $metodoPagamento = "";

        //retirando dados do cliente 
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

        if((stristr($address_billing['postcode'], "não especificado")) || (stristr($endereco_cobranca[0], "não especificado"))){
            $D024["D024_Endereco"]=$endereco_entrega[0];
            $D024["D024_Numero"]=$endereco_entrega[1];
            $D024["D024_Cep"]=$address_shipping['postcode'];
            $D024["D024_Complemento"]=trim($endereco_entrega[2]);
            $D024["D024_Bairro"]=$endereco_entrega[3];
            $D024["Cidade"]=$address_shipping['city'];
            $D024["UF"]=strtoupper($address_shipping['region']);
            $D024['Pais']=$address_shipping['country_id'];
        }else{
            $D024["D024_Endereco"]=$endereco_cobranca[0];
            $D024["D024_Numero"]=$endereco_cobranca[1];
            $D024["D024_Cep"]=$address_billing['postcode'];
            $D024["D024_Complemento"]=trim($endereco_cobranca[2]);
            $D024["D024_Bairro"]=$endereco_cobranca[3];
            $D024["Cidade"]=$address_billing['city'];
            $D024["UF"]=strtoupper($address_billing['region']);
            $D024['Pais']=$address_billing['country_id'];
        }

        //VALIDAÇÃO EMAIL CLIENTE
        if(empty($address_billing['email'])){
           $D024["D024_E_Mail"] = $email_cliente_cadastro;
        }else{
            $D024["D024_E_Mail"]=$address_billing['email'];
        }

        $D024["D024_DDD_Telefone_1"]='';
        $D024["D024_Telefone_1"]=$address_billing['telephone'];
        $D024["D024_DDD_Telefone_2"]='';
        $D024["D024_Telefone_2"]=$address_billing['fax'];
        $D024["D024_Web_Site"]="";
        $D024["C007_Id_Vendedor_Interno"] = $g['C031']['C007IdEcommerce'];
        $D024["D024_Flag_Contribuinte"] = 0;

        //VALIDAÇÃO EMAIL ENTREGA
        if(empty($address_shipping['email'])){
            $D148["D148_Email"] = $email_cliente_cadastro;
        }else{
            $D148["D148_Email"]=$address_shipping['email'];
        }

        $D148["UF"]=strtoupper($address_shipping['region']);
        $D148["Cidade"]=$address_shipping['city'];
        $D148["Pais"]=$address_shipping['country_id'];
        $D148["D148_Nome"]=$address_shipping['firstname'].' '.$address_shipping['middlename'].' '.$address_shipping['lastname'];
        $D148["D148_Cep"]=$address_shipping['postcode'];
        $D148["D148_Rua"]=$endereco_entrega[0];
        $D148["D148_Numero"]=$endereco_entrega[1];
        $D148["D148_Complemento"]=$endereco_entrega[2];
        $D148["D148_Bairro"]=$endereco_entrega[3];
        $D148["D148_DDD_Telefone"]='';
        $D148["D148_Telefone"]=$address_shipping['telephone'];
        $D148["D148_DDD_Fax"]='';
        $D148["D148_Fax"]=$address_shipping['fax'];
        $D024['D148'] = $D148;
       
        $shipping = explode("-",$informacoes_shipping);
        $T005_Prazo_Entrega = trim($shipping[count($shipping)-1]);

        //tratando campo 'comment' que contém as informações sobre o pedido
        $History_Status = (array)$orderInfo['status_history'];

        foreach ($History_Status as $key => $status) {
            $informacoes = (array) $status;
            
            $comment = $informacoes['comment'];
            if(!empty($comment)){
                //explode os comentarios pela linha pulada em um array de varias strings
                $DetalhesComment = explode("<br>", $comment);
               
                foreach ($DetalhesComment as $key => $detalhe) {
                    $informacao = (array) $detalhe;

                    //identificando qual é o marketplace e pegando o nome
                    if((stristr($informacao[0],"Canal De Vendas:"))){
                        $palavras = explode(":", $informacao[0]);
                        $marketplace = trim($palavras[count($palavras)-1]);
                        $marketplace = str_replace("</b>","",$marketplace);
                        $marketplace = str_replace("<br>","",$marketplace);
                    }

                    //verifica se existe a palavra Id na linha e pega os numeros da linha
                    if(stripos($informacao[0],"Id no MarketPlace")){
                        $Id_integracommerce=explode(":",$informacao[0]);
                        $Id_integracommerce=$Id_integracommerce[1];
                        $Id_integracommerce = str_replace("</b>","",$Id_integracommerce);
                    }

                    //identificando a forma de envio se existir e retirando da string
                    if(stripos($informacao[0],"Forma de Envio")){
                        $palavras = explode(":", $informacao[0]);
                        $transportadora_magento = trim($palavras[count($palavras)-1]);
                        $transportadora_magento = str_replace("</b>","",$transportadora_magento);
                        $transportadora_magento = str_replace("<br>","",$transportadora_magento);
                    }

                    //identificando qual é a conta do marketplace
                    if((stristr($informacao[0],"Nome da Conta no Anymarket:"))){
                        $palavras = explode(":", $informacao[0]);
                        $contaMarketplace = trim($palavras[count($palavras)-1]);
                        $contaMarketplace = str_replace("</b>","",$contaMarketplace);
                        $contaMarketplace = str_replace("<br>","",$contaMarketplace);
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
                        log("encontrando Comment_Forma_de_Pagamento: ".$Comment_Forma_de_Pagamento);
                    }
                }
            }else{
                continue;
            }
        }

        if (!strpos(" $marketplace ", "SHOPEE") && !strpos(" $marketplace ", "OLIST_NEW_API-GUIMEPA")) {
            $Id_integracommerce=trim(preg_replace("/[^0-9]/", " ",$Id_integracommerce));
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

            $sqlD022 = mysql_query("SELECT D022_Id,D022_Nome_Empresa FROM D022 WHERE D022_Descricao_Ecommerce LIKE '%".$transportadora_magento."%'");
            if(mysql_num_rows($sqlD022) > 0){
                $rowD022 = mysql_fetch_assoc($sqlD022);
                $D022_Id = $rowD022['D022_Id'];
                $D022_Nome_Empresa = $rowD022['D022_Nome_Empresa'];
            }else{
                $D022_Nome_Empresa = "NÃO IDENTIFICADA";
            }

            $sqlD036 = mysql_query("SELECT D036_Id FROM D036 WHERE D036_Descricao_Ecommerce LIKE '%".$transportadora_magento."%'");
            if(mysql_num_rows($sqlD036) > 0){
                $rowD036 = mysql_fetch_assoc($sqlD036);
                $D036_Id = $rowD036['D036_Id'];
            }
        }

        //caso nao tenha id do marketplace pega o id do pedido no magento
        if(empty($Id_integracommerce)){
            $Id_integracommerce=$orderInfo['increment_id'];
        }

        // Nova busca do portador - 17/08/2021 Felipe
        $additional_information = (array) $pagamento['additional_information'];
        $metodoPagamento2       = $additional_information['metodo'];
        $metodoPagamento2       = explode(':',$metodoPagamento2);
        $metodoPagamento2       = strToUpper(trim($metodoPagamento2[1]));
        $metodoPagamento2       = $metodoPagamento."-".$metodoPagamento2;

        //populando portador dependendo do marketplace
        if(!empty($metodoPagamento2) || !empty($metodoPagamento)){   
            log("entrou no portador");            
            $sqlD027 = mysql_query("SELECT D027_Id,D027_Parcelas_Ecommerce FROM D027 WHERE D027_Descricao_Ecommerce LIKE '%".$metodoPagamento2."%'");
            if(mysql_num_rows($sqlD027) > 0){
                $rowD027 = mysql_fetch_assoc($sqlD027);
                $D027_Id = $rowD027['D027_Id'];
            }else{
                $sqlD027 = mysql_query("SELECT D027_Id,D027_Parcelas_Ecommerce FROM D027 WHERE D027_Descricao_Ecommerce LIKE '%".$metodoPagamento."%'");
                if(mysql_num_rows($sqlD027) > 0){
                    $rowD027 = mysql_fetch_assoc($sqlD027);
                    $D027_Id = $rowD027['D027_Id'];
                }else{
                    $D027_Id="";
                }
            }    
        }

        if($rowD027['D027_Parcelas_Ecommerce']>0){
            $parcelas=$rowD027['D027_Parcelas_Ecommerce'];
        }

        if(empty($parcelas) || $parcelas==0){
            $parcelas=1;
        }
        log("parcelas:".$parcelas);
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
        $T005["C007_Id_Vendedor_Interno"] = $g['C031']['C007IdEcommerce'];
        //$T005["T005_Ordem_Compra"]=$orderInfo['order_id'];
        $T005["T005_Ordem_Compra"]=$Id_integracommerce;
        $T005["T005_Flag_Plataforma_Ecommerce"]=3;
        $T005["T005_D022_Id"] = $D022_Id;   
        $T005["T005_D036_Id"] = $D036_Id;        
        $T005["T005_D027_Id"] = $D027_Id;        
        $T005["T005_Nome_Transportadora"] = $D022_Nome_Empresa;
        $T005["T005_Canal_Vendas_Ecommerce"] = $marketplace;  
        $T005["Comment_Forma_de_Pagamento"] = $Comment_Forma_de_Pagamento;
        
        $T090["T090_D027_Id"] = $D027_Id;

        //Tratando parcelas de um pedido
        $string = $pagamento['installment_description'];
        $pagamentos = array();
        $diferenca_parcelas = 0;
        $total_parcelas = 0;

        if(!empty($string)){

            log("entrou no string do installment_description");
           
            if (strpos($string, 'de')){
                $parcelas = substr($string,0,strpos($string,'x'));
                $valor = substr($string,strpos($string,'R$')+2,strpos($string,'sem'));
                $valor = floatval(str_replace(',','.',$valor));
                $numeroparcela = 1;
                $prazo = 30;
                for ($i=0; $i < $parcelas ; $i++) { 
                   $vencimento = date('d/m/Y', strtotime("+".(($i+1)*30)." days",strtotime(date('Y-m-d'))));
                   $vencimento = gCorrigeDataInverte($vencimento);
                   $T090["T090_Numero_Parcela"]=$numeroparcela;
                   $T090["T090_Data_Vencimento"]=$vencimento;
                   $T090["T090_Valor_Parcela"]=$valor;
                   $T090["T090_Prazos_Dias"]=$prazo;
                   $total_parcelas += $valor; 
                   $pagamentos[] = $T090;
                   $prazo+=30;
                   $numeroparcela++;
                }
            }else if (strpos($string, 'vista')){
                $valor = substr($string, strpos($string,'R$')+2);
                $valor = str_replace('.','',$valor);
                $valor = str_replace(',','.',$valor);
                $vencimento = date('Y-m-d', strtotime("+30 days"));
                $T090["T090_Prazos_Dias"]=30;
                $T090["T090_Numero_Parcela"]=1;
                $T090["T090_Data_Vencimento"]=$vencimento;
                $T090["T090_Valor_Parcela"]=$valor;
                $total_parcelas += $valor; 
                $pagamentos[] = $T090;
            }else{
                $vencimento = date('Y-m-d', strtotime("+30 days"));
                $T090["T090_Prazos_Dias"]=30;
                $T090["T090_Numero_Parcela"]=1;
                $T090["T090_Data_Vencimento"]=$vencimento;
                $T090["T090_Valor_Parcela"]= $T005["T005_Valor_Total"];
                $total_parcelas += $T090["T090_Valor_Parcela"]; 
                $pagamentos[] = $T090;
            } 

        }else if(!empty($parcelas)){
            //caso o campo installment description esteja em branco e as informações virem pelo campo comment
            //caso o numero de parcelas for maior que 0 faz o loop para inserir cada parcela o valor da parcela é (valortotal/parcelas)
            log("encontrou parcelas e entrou no T090");
            $valor_parcela = ($T005["T005_Valor_Total"]/$parcelas);
            $valor = round($valor_parcela,2);
            $valor = floatval(str_replace(',','.',$valor));
            $numeroparcela = 1;
            $prazo = 30;
            for ($i=0; $i < $parcelas ; $i++) { 
                log("pagamento loop T090 $i vez");
                $vencimento = date('d/m/Y', strtotime("+".(($i+1)*30)." days",strtotime(date('Y-m-d'))));
                $vencimento = gCorrigeDataInverte($vencimento);
                $T090["T090_Numero_Parcela"]=$numeroparcela;
                $T090["T090_Data_Vencimento"]=$vencimento;
                $T090["T090_Valor_Parcela"]=$valor;
                $T090["T090_Prazos_Dias"]=$prazo;
                $total_parcelas += $valor; 

                log("Pedido - ".$T005["T005_Id_Pedido_Externo"]."     inserindo parcelas do portador-Numero da parcela:$numeroparcela   -     Vencimento:$vencimento    -        Valor da parcela:$valor    -       Prazo em dias:$prazo");

                $prazo+=30;
                if($numeroparcela<=$parcelas){
                   $numeroparcela++; 
                }else{
                    continue;
                }
                $pagamentos[] = $T090;
            }
        }

        $diferenca_parcelas = round($T005["T005_Valor_Total"] - $total_parcelas,2);
        if($diferenca_parcelas<0){
            $pagamentos[0]['T090_Valor_Parcela'] = $pagamentos[0]['T090_Valor_Parcela'] - abs($diferenca_parcelas);
        }else{
            $pagamentos[0]['T090_Valor_Parcela'] = $pagamentos[0]['T090_Valor_Parcela'] + abs($diferenca_parcelas);
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
            $T006["D082_Marca"]=$mD001['D082_Marca'];

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
                          
        return $T005;    
    }

    public function tratarDadosOrder($order)
    {
        global $g;

        $api_url_v1 = $g['C031']['MagentoUrlApi'];      
        $username =  $g['C031']['MagentoConsumerKEy'];
        $password = $g['C031']['MagentoConsumerSecret'];
        $status_pedido = $g['C031']['MagentoStatusPedido'];

        $cli = new \SoapClient($api_url_v1);        
        
        //Retreive session id from login
        $session = $cli->login($username, $password);

        $item = (array) $order;
        $increment_id = $item['increment_id'];

        $item = (array) $order;
        $increment_id = $item['increment_id'];

        $orderInfo = $cli->call($session, 'sales_order.info',$increment_id);

        $D024 = array();    
        $T005 = array();
        $D148 = array();
        $T006 = array();
        $T090 = array();
        $T090 = array();

        $customer_Id = $orderInfo['customer_id'];

        if($customer_Id == null || $customer_Id == ""){

            $logImportacao .= "Customer Id não foi identificado no pedido<br>";
            registrarLog(array("T005", 0, $g['empresaAtual'], "ID {$orderInfo['order_id']}", "Customer Id não foi identificado no pedido", $logImportacao, "N", "2"));
            mysqli_query("UPDATE T245 SET T245_Flag_Erro = 'S' WHERE T245_Referencia = 'ID {$orderInfo['order_id']}' AND T245_Tipo_Log = '2'",$g['conexaoBandoAuditoria']);
            //echo "Customer Id não foi identificado no pedido";
            return false;
        }

        $cliente_dados = $cli->call($session, 'customer.info', "$customer_Id");
        $Customer_Campo_Dado = $cliente_dados['taxvat'];
        $D024_Inscricao_Estadual = $cliente_dados['ie'];
        $Customer_CNPJ = $orderInfo['customer_cnpj'];

        //retirando dados do cliente        
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

        $address_billing = (array) $orderInfo['billing_address'];
        $endereco_cobranca = explode(chr(10),$address_billing['street']);

        if(!empty($address_billing['email'])){
            $email_cliente = $address_billing['email'];
        }else{
            $email_cliente = $cliente_dados['email'];
        }
        
        if((stristr($address_billing['postcode'], "não especificado")) || (stristr($endereco_cobranca[0], "não especificado"))){
            $D024["D024_Endereco"]=$endereco_entrega[0];
            $D024["D024_Numero"]=$endereco_entrega[1];
            $D024["D024_Cep"]=$address_shipping['postcode'];
            $D024["D024_Complemento"]=trim($endereco_entrega[2]);
            $D024["D024_Bairro"]=$endereco_entrega[3];
            $D024["Cidade"]=$address_shipping['city'];
            $D024["UF"]=strtoupper($address_shipping['region']);
            $D024['Pais']=$address_shipping['country_id'];
        }else{
            $D024["D024_Endereco"]=$endereco_cobranca[0];
            $D024["D024_Numero"]=$endereco_cobranca[1];
            $D024["D024_Cep"]=$address_billing['postcode'];
            $D024["D024_Complemento"]=trim($endereco_cobranca[2]);
            $D024["D024_Bairro"]=$endereco_cobranca[3];
            $D024["Cidade"]=$address_billing['city'];
            $D024["UF"]=strtoupper($address_billing['region']);
            $D024['Pais']=$address_billing['country_id'];
        }
    
        //$D024["Razao_Social"]=$address_billing['firstname'].' '.$address_billing['lastname'];
        $D024["D024_DDD_Telefone_1"]='';
        $D024["D024_Telefone_1"]=$address_billing['telephone'];
        $D024["D024_DDD_Telefone_2"]='';
        $D024["D024_Telefone_2"]=$address_billing['fax'];
        $D024["D024_E_Mail"]=$address_billing['email'];
        $D024["D024_Web_Site"]="";

        $address_shipping = (array) $orderInfo['shipping_address'];
        $endereco_entrega = explode(chr(10),$address_shipping['street']);

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

        //tratando campo integracommerce_id
        $Id_integracommerce=$orderInfo['integracommerce_id'];
        $Id_integracommerce = str_replace("LU-"," ",$Id_integracommerce);
    
        //populando portador se magazine luiza
        $descricao=$orderInfo['shipping_description'];

        if(strpos($descricao, 'Magazine')){
            $T090["T090_D027_Id"] = 29;
        } else {
            $T090["T090_D027_Id"] = '';
        } 

        //tratando campo comment
        $History_Status = (array) $orderInfo['status_history'];

        //IDENTIFICANDO SE O PEDIDO VEM DO MARKETPLACE CNOVA 
        $Approved_payment = (array) $History_Status[1];
        $Approved_payment2 = (array) $History_Status[2];

        $Comment = $Approved_payment['comment'];
        $DetalhesComment = explode(":",$Approved_payment['comment']);
        $Comment2 = $Approved_payment2['comment'];
        $DetalhesComment2 = explode(":",$Approved_payment2['comment']);

        //TRATANDO O ID DO PEDIDO PARA UM NUMERO ESPECIFICO DO MARKETPLACE E O PORTADOR PARA DENTRO DO SISTEMA
        if(strpos($Comment, 'CNOVA')){
            $Id_integracommerce = preg_replace("/[^0-9]/", "", $DetalhesComment[1]);
            $marketplace = 'cnova';
        }

        if(strpos($Comment2, 'CNOVA')){
           $Id_integracommerce = preg_replace("/[^0-9]/", "", $DetalhesComment2[1]);
            $marketplace = 'cnova';
        }

        //tratando campo para pegar prazo de entrega
        $T005_Prazo_Entrega=trim($Comment[3]);
        $pagamento = (array) $orderInfo['payment'];


        //cabeçalho do pedido
        $items = (array) $orderInfo['items'];
        $T005["T005_Prazo_Entrega"]=$T005_Prazo_Entrega;
        $T005["T005_Valor_Total_Produtos"]=$orderInfo['subtotal'];
        $T005["T005_Valor_Total_Produtos_Sem_Desconto"]=$orderInfo['subtotal'];
        $T005["T005_Valor_Total"]=$orderInfo["grand_total"];
        $T005["T005_Valor_Frete"]=$orderInfo['shipping_amount'];
        $T005["T005_Observacao_Nota_Fiscal"]=$orderInfo['increment_id']." ".$Id_integracommerce." ".$pagamento['pagarme_transaction_id']." ".$orderInfo['shipping_description'];
        $T005["Id_Loja"]=$orderInfo['store_id'];
        $T005["T005_Pedido_Ecommerce"]=$orderInfo['order_id'];
        $T005["T005_Id_Pedido_Externo"]=$orderInfo['increment_id'];
        $T005["C007_Id_Vendedor_Interno"] = $g['C031']['C007IdEcommerce'];
        log("vendedor configurado:".$T005["C007_Id_Vendedor_Interno"]);


        //$T005["T005_Ordem_Compra"]=$orderInfo['order_id'];
        $T005["T005_Ordem_Compra"]=$Id_integracommerce;
        $T005["T005_Flag_Plataforma_Ecommerce"]=3;
                
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
            $T006["T006_Numero_Pedido_Compra"]=$product['order_id'];
            $T006["T006_Codigo_Produto"]=$product['sku'];
            $T006["T006_Descricao_Produto"]=$product['name'];
            $T006["T006_Quantidade"]=$product['qty_ordered'];
            $T006["T006_Valor_Preco_Original"]=$product['price'];
            $T006["T006_Valor_Preco_Sem_Desconto_Unitario"]=$product['price']-($product['discount_amount']/$product['qty_ordered']);
            $T006["T006_Valor_Total_Preco_Sem_Desconto"]=$product['price']*$product['qty_ordered'];
            $T006["T006_Percentual_Desconto"]=$product['discount_percent'];
            $T006["Codigo_Barras"]='';
            $T006["T006_Numero_Pedido_Compra"]=$T005["T005_Ordem_Compra"];
            $T006["D082_Marca"]=$mD001['D082_Marca'];
    
            $produtos[] = $T006;
        }

        //populando portador se magazine luiza
        $descricao=$orderInfo['shipping_description'];
        //se o marketplace for magazine luiza, portador é magazine luiza(id 29)
        if(strpos($descricao, 'Magazine')){
            $T090["T090_D027_Id"] = 29;
        } else if (isset($marketplace)){
               //se o marketplace for cnova, portador é cnova(id 21)
             $T090["T090_D027_Id"] = 21;
        } else {
            $T090["T090_D027_Id"] = '';
        } 

        //Tratando parcelas de uma compra
        $string = $pagamento['installment_description'];
        $pagamentos = array();
        if (strpos($string, 'de')){
            $parcelas = substr($string,0,strpos($string,'x'));
            $valor = substr($string,strpos($string,'R$')+2,strpos($string,'sem'));
            $valor = floatval(str_replace(',','.',$valor));
            $numeroparcela = 1;
            $prazo = 30;
            for ($i=0; $i < $parcelas ; $i++) { 
               $vencimento = date('d/m/Y', strtotime("+".(($i+1)*30)." days",strtotime(date('Y-m-d'))));
               $vencimento = gCorrigeDataInverte($vencimento);
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


        $T005['D024'] = $D024;
        $T005['T006'] = $produtos;
        $T005['T090'] = $pagamentos;
                     
        return $T005;    
    }

    //***********Funções de auxilio***********

    // Procura dentro do array $T005(informações do pedido) um campo e verifica se ele tem o valor passado
    function ProcuraCampo($campo, $Pedido_T005, $valor)
    {
        return recursive_array_search($valor, $Pedido_T005, $campo);
    }

    function TirarAcentos($string)
    {
        $string = preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"),$string);
        $string = preg_replace(array("/(ç|Ç)/"), array("C"), $string);
        return $string;
    }

    // Trata qualquer lista de dados numa string para um array
    function TratarDados($dados)
    {
        $dados = strtoupper($dados);
        $dados = explode(";", $dados);
        foreach($dados as $key => $valor)
        {
            $dados[$key] = trim($dados[$key]);
            $dados[$key] = $this->TirarAcentos($dados[$key]);
        }

        return $dados;
    }

    function filtroPedido_EncontrarCidadeMarca($Pedido_T005)
    {
        // Marcas aceitas no filtro das empresas DELTATOP
        $filtros3_marcas = "Eccofer ; Komelon ; Vonder ; Vonder Construtor; 
        Vonder Plus ; nove54 ; HULTAFORS; DWT; AWT; WAP; puma";
            
         // Cidades que são aceitas nessa empresa
        $filtros3_cidades = "curitiba;colombo;sao jose dos pinhais;almirante;ARAUCARIA;PINHAIS;CAMPO LARGO;QUATRO BARRAS;
        CAMPINA GRANDE DO SUL";

        $filtros3_marcas = $this->TratarDados($filtros3_marcas);
        $filtros3_cidades = $this->TratarDados($filtros3_cidades);

        // Variáveis de controle que servem para verificar se encontrou cidade ou marca
        $encontrouCidade = false;
        $cidadeEncontrada = '';
        $encontrouMarca = false;
        $marcaEncontrada = '';

        // Verifica se uma das marcas listadas no $marcas é a mesma do pedido
        foreach($filtros3_marcas as $key => $valor)
        {
            if($this->ProcuraCampo('D082_Marca', $Pedido_T005, $valor))
            {
                $marcaEncontrada = $valor;
                $encontrouMarca = true;
                break;
            }
        }

        //Trata o campo 'Cidade' do pedido pois o mesmo pode vir com ou sem acento maiúsculo ou minusculo
        $Pedido_T005['D024']['Cidade'] = $this->TirarAcentos($Pedido_T005['D024']['Cidade']);
        $Pedido_T005['D024']['Cidade'] = trim($Pedido_T005['D024']['Cidade']);
        $Pedido_T005['D024']['Cidade'] = strtoupper($Pedido_T005['D024']['Cidade']);

        //Verifica se uma das cidades listadas no $cidades é a mesma do pedido
        foreach($filtros3_cidades as $key => $valor)
        {
            //log("Comparação cidade--> Vp: " . $T005['D024']['Cidade'] . "| Vt: $valor");
            if($this->ProcuraCampo('Cidade', $Pedido_T005, $valor))
            {
                $cidadeEncontrada = $valor;
                $encontrouCidade = true;
                break;
            }
        }

        return array('encontrouMarca' => (bool)$encontrouMarca, 'encontrouCidade' => (bool)$encontrouCidade, 'marcaEncontrada' => $marcaEncontrada, 'cidadeEncontrada' => $cidadeEncontrada);
    }

    // Retorna os produtos com comparação de estoque por empresa, além disso pedidos que possuem multiplos produtos iguais são somados para melhor verificação na prioridade de estoque
    function filtroPedido_retornaComparacaoProdutoEstoque($empresas, $Pedido_T005, $D009_Estoque_Tipo = 'liquido')
    {
        switch($D009_Estoque_Tipo)
        {
            case 'liquido':
                $D009_Estoque_Tipo = 'D009_Quantidade_Estoque_Liquido';
                break;
            case 'fisico':
                $D009_Estoque_Tipo = 'D009_Quantidade_Estoque';
                break;
            case 'tabela':
                $D009_Estoque_Tipo = 'D009_Quantidade_Estoque_Tabela';
                break;
            default:
                $D009_Estoque_Tipo = 'D009_Quantidade_Estoque_Liquido';
        }

        $produtos = [];

        // Pega todos os códigos de produto do pedido
        $codigoProdutos = [];
        $marcaProdutos = [];
        foreach($Pedido_T005['T006'] as $key => $value)
        {
            array_push($codigoProdutos, $Pedido_T005['T006'][$key]['T006_Codigo_Produto']);
            array_push($marcaProdutos, strtoupper(trim($Pedido_T005['T006'][$key]['D082_Marca'])));
        }
        $codigoProdutos = array_unique($codigoProdutos);
        $marcaProdutos = array_unique($marcaProdutos);

        // A cada codigo de produto
        foreach($codigoProdutos as $Ikey => $Ivalue)
        {
            // Adiciona um novo produto no array com codigo, quantidade do pedido do produto, e a comparação de produto estoque empresa
            $chaveProd = array_push($produtos, array('codigoProduto' => $Ivalue, 'marcaProduto' => $marcaProdutos[$Ikey], 'quantidadePedido' => 0, 'quantidadeProdutoEmpresa' => array() ));
            $chaveProd = $chaveProd - 1;

            //Atribui os estoques das empresas no array quantidadeProdutoEmpresa
            foreach($empresas as $Jkey => $chaveEmpresa)
            {
                // Faz query no banco de dados para pegar o estoque do produto em todas as empresas
                $D001 = mysql_query("   SELECT $D009_Estoque_Tipo
                                        FROM D001 
                                        LEFT JOIN D049 ON D049_D001_Id=D001_Id
                                        LEFT JOIN D009 ON D009_D049_Id=D049_Id
                                        WHERE D001_Codigo_Produto = '{$Ivalue}'
                                        AND D009_C004_Id = '{$chaveEmpresa}'");
                            
                $mD001 = mysql_fetch_assoc($D001);

                $produtos[$chaveProd]['quantidadeProdutoEmpresa'][$chaveEmpresa] = $mD001[$D009_Estoque_Tipo];
            }

            // Soma as quantidadesPedidos de todos os produtos repetidos
            foreach($Pedido_T005['T006'] as $Jkey => $Jvalue)
            {
                if($Pedido_T005['T006'][$Jkey]['T006_Codigo_Produto'] === $Ivalue)
                {
                    $produtos[$chaveProd]['quantidadePedido'] += $Pedido_T005['T006'][$Jkey]['T006_Quantidade'];
                }
            }
        }

        return $produtos;
    }

    // Função que pega os produtos e o estoque deles por empresa e faz uma soma dos produtos sem estoque para verificar em qual empresa é mais barato
    function filtroPedido_retornaCustoTabela($produtos)
    {
        // Cria um array para somar todos os custos
        $somaCustos = array();

        // Passa por todos os produtos
        foreach($produtos as $i => $iValue)
        {
            // Cada produto tem um array 'quantidadeProdutoEmpresa' cada chave do array existe o estoque daquele produto na tal empresa
            foreach($produtos[$i]['quantidadeProdutoEmpresa'] as $C004 => $jValue)
            {
                // Caso a quantidade do pedido do produto for maior doque o estoque
                if($produtos[$i]['quantidadePedido'] > $jValue)
                {
                    // SQL que pega o custo tabela de cada produto
					$D001 = mysql_query("SELECT D069_Valor_Custo_Unitario
                                         FROM D001 
                                     LEFT JOIN D049 ON D049_D001_Id=D001_Id
                                     LEFT JOIN D009 ON D009_D049_Id=D049_Id
                                     LEFT JOIN D069 ON D009_Id=D069_D009_Id
                                        WHERE D001_Codigo_Produto = '{$produtos[$i]['codigoProduto']}'
                                        AND D009_C004_Id = '{$C004}'");

                    $mD001 = mysql_fetch_assoc($D001);

                    // Evita warnings no php, pois ao utilizar += a chave não está criada.
                    $somaCustos[$C004] = $somaCustos[$C004];
                    $somaCustos[$C004] += $mD001['D069_Valor_Custo_Unitario'];                    
                }
            }
        }

        // Pega a chave(ID da empresa) do menor valor do $somaCustos e atribui ao targetEmpresa
        $targetEmpresa = array_keys($somaCustos , min($somaCustos))[0];

        return array('targetEmpresa' => $targetEmpresa, 'somaCustos' => $somaCustos);
    }

    //Função que verifica quais empresas tem estoque e atribui o estoque numa prioridade, OBS: Falta de estoque = Pelo menos 1 produto não tem estoque na empresa
    //$manipularEstoque variável para depuração de lógica, atribui-se um valor de estoque numa empresa para todos os produtos dela
    function filtroPedido_prioridadeEstoque($empresasArray, $Pedido_T005, $D009_Estoque_Tipo = 'liquido')
    {
        //Variavel que armazena as flags se a empresa tem estoque ou não. Também armazena a prioridade da empresa baseado na posição do array(melhor explicação no último foreach da função)
        $empresasComEstoque = array_fill_keys($empresasArray, true);

        //Inicializa o array produtos. Serve para guardar os produtos com os valores q ele recebeu de estoque de cada empresa para comparação
        $produtos = $this->filtroPedido_retornaComparacaoProdutoEstoque($empresasArray, $Pedido_T005, $D009_Estoque_Tipo);

        foreach($produtos as $Ikey => $Ivalue)
        {
            foreach($produtos[$Ikey]['quantidadeProdutoEmpresa'] as $Jkey => $Jvalue)
            {
                if($produtos[$Ikey]['quantidadePedido'] > $Jvalue)
                {
                    $empresasComEstoque[$Jkey] = false;
                }
            }
        }

        //Loop que verifica qual das empresas da prioridade será escolhida para receber o pedido. OBS: A prioridade é feita pelo array aonde a chave mais baixa(ou mais a esquerda) possui a prioridade mais alta
        //Exemplo: array(DELTATOP, GMK8, GMP) -> a ordem da prioridade é DELTATOP GMK8 e GMP
        foreach($empresasComEstoque as $key => $value)
        {
            // Caso a flag do valor mais baixo no array for true(tem estoque)
            if($value)
            {
                return array('targetEmpresa' => $key, 'tipoEstoque' => $D009_Estoque_Tipo, 'produtos' => $produtos, 'empresasComEstoque' => $empresasComEstoque);
            }
        }

        return array('targetEmpresa' => null, 'tipoEstoque' => $D009_Estoque_Tipo, 'produtos' => $produtos, 'empresasComEstoque' => $empresasComEstoque);
    }

    //Filtro pedidos
    public function filtroPedido($Pedido_T005)
    {
        // Constantes que definem o id das empresas C004_Id(algumas empresas não são utilizadas no código, mas podem ser solicitadas no futuro)
        define('GUIMEPA_CURITIBA_ID', 1);
        define('GMP_ID', 2);
        define('DELTATOP_ID', 3);
        define('GMK8_ID', 4);
        define('GUIMEPA_ITAJAI_ID', 5);

        // Se possível quando for mudado para PHP 8 transformar isso num enum
        $stringEmpresas = array(GUIMEPA_CURITIBA_ID => 'GUIMEPA_CURITIBA', GMK8_ID => 'GMK8', DELTATOP_ID => 'DELTATOP', GMP_ID => 'GMP', GUIMEPA_ITAJAI_ID => 'GUIMEPA_ITAJAI', null => 'N_SELECIONADO');

        $filtro3_Entrada = $this->filtroPedido_EncontrarCidadeMarca($Pedido_T005);

        $finalLog = "FINAL LOG--> PEDIDO: " . $Pedido_T005['T005_Id_Pedido_Externo'] . " | MARCA: " .$filtro3_Entrada['marcaEncontrada'] . " | CIDADE: " . $filtro3_Entrada['cidadeEncontrada'] . " | CANAL VENDAS: " . $Pedido_T005['T005_Canal_Vendas_Ecommerce'];

        // Filtro 1
        if($this->ProcuraCampo('T005_Canal_Vendas_Ecommerce', $Pedido_T005,'MAGAZINE_LUIZA-MAGAZINE LUIZA'))
        {
            $finalLog .= " | FILTRO: 1 | EMPRESA: F1-GMP";
            return array('targetEmpresa' => GMP_ID, 'msgAjax' => 'F1-GMP', 'finalLog' => $finalLog, 'filtroUtilizado' => 'F1 - Canal Vendas = "MAGAZINE_LUIZA-MAGAZINE LUIZA"');    
        }
        //Filtro 2
        else if($this->ProcuraCampo('T005_Canal_Vendas_Ecommerce', $Pedido_T005, 'RENNER-Guimepa') || $this->ProcuraCampo('T005_Canal_Vendas_Ecommerce', $Pedido_T005, 'SUPERCAMPO-GUIMEPA') || $this->ProcuraCampo('T005_Canal_Vendas_Ecommerce', $Pedido_T005, 'OLIST_NEW_API'))
        {
            $finalLog .= " | FILTRO: 2 | CANAL VENDAS: " . $Pedido_T005['T005_Canal_Vendas_Ecommerce'] . "| EMPRESA: F2-GMK8";
            return array('targetEmpresa' => GMK8_ID, 'msgAjax' => 'F2-GMK8', 'finalLog' => $finalLog, 'filtroUtilizado' => 'F2 - Canal Vendas = "RENNER-Guimepa ou SUPERCAMPO-GUIMEPA ou OLIST_NEW_API"');
        }
        //Filtro 3
        else if($filtro3_Entrada['encontrouMarca'] && $filtro3_Entrada['encontrouCidade'])
        {
            //Insira o ID das empresas que vão participar da prioridade de estoque aqui. A prioridade é feita da esquerda para direita exemplo:
            // array(GMK8_Id => '',  DELTATOP_Id => '', GMP_Id => '', GUIMEPA_CURITIBA_ID => '') ----> prioridade 1-GMK8, 2-DELTATOP, 3-GMP, 4-GUIMEPA CURITIBA
            $prioridadeEmpresas = array(DELTATOP_ID, GMP_ID, GMK8_ID);

            $resposta = $this->filtroPedido_prioridadeEstoque($prioridadeEmpresas, $Pedido_T005); //array(DELTATOP_ID => 0, GMP_ID => 1000, GMK8_ID => 0.000, GUIMEPA_ITAJAI_ID => 0));

            // Caso o target da empresa seja nulo(não tem estoque em nenhuma das empresas) verifica tabela OVD entre Deltatop e GMK8 e envia para que tem estoque
            if($resposta['targetEmpresa'] === null)
            {
                $prioridadeEmpresas = array(DELTATOP_ID, GMK8_ID);

                $resposta = $this->filtroPedido_prioridadeEstoque($prioridadeEmpresas, $Pedido_T005, 'tabela');

                // Caso na tabela OVD nenhuma empresa tenha estoque
                if($resposta['targetEmpresa'] === null)
                {
                    $finalLog .= " | FILTRO: 3 | TARGET EMPRESA: DELTATOP-COMPRAR | Resposta prioridade: " . var_export($resposta, true);
                    return array('msgAjax' => 'F3-DELTATOP', 'targetEmpresa' => DELTATOP_ID, 'finalLog' => $finalLog, 'filtroUtilizado' => "F3-1 - Cidade: {$filtro3_Entrada['cidadeEncontrada']} | Marca: {$filtro3_Entrada['marcaEncontrada']} | Sem estoque liquido em DELTATOP/GMP/GMK8. Sem estoque tabela em DELTATOP/GMK8 enviado a DELTATOP");                    
                }
                else
                {
                    $finalLog .= " | FILTRO: 3 | TARGET EMPRESA: ". $stringEmpresas[$resposta['targetEmpresa']] ." | Resposta prioridade: " . var_export($resposta, true);
                    return array('msgAjax' => 'F3-' . $stringEmpresas[$resposta['targetEmpresa']], 'targetEmpresa' => $resposta['targetEmpresa'],'finalLog' => $finalLog, 'filtroUtilizado' => "F3-2 - Cidade: {$filtro3_Entrada['cidadeEncontrada']} | Marca: {$filtro3_Entrada['marcaEncontrada']} | Sem estoque liquido em DELTATOP/GMP/GMK8. Enviado a empresa com estoque tabela entre DELTATOP/GMK8");                   
                }
            }
            // Se não envia para a empresa escolhida
            else
            {
                $finalLog .= " | FILTRO: 3 | TARGET EMPRESA: ". $stringEmpresas[$resposta['targetEmpresa']] ." | Resposta prioridade: " . var_export($resposta, true);
                return array('msgAjax' => 'F3-' . $stringEmpresas[$resposta['targetEmpresa']], 'targetEmpresa' => $resposta['targetEmpresa'],'finalLog' => $finalLog, 'filtroUtilizado' => "F3 - Cidade: {$filtro3_Entrada['cidadeEncontrada']} | Marca: {$filtro3_Entrada['marcaEncontrada']} | Prioridade de estoque: DELTATOP/GMP/GMK8");
            }
            
        }
        //Filtro 4
        else
        {
            /* Listas de marcas que devem ser separadas por empresa caso o filtro 4 precise utilizar o Caso 2
            OBS CASO FOR ADICIONAR UMA MARCA NO CUSTO TABELA ADICIONAR NO $gmk8_marcas também*/
            $gmk8_marcas = "ECCOFER ; KOMELON ; VONDER ; VONDER CONSTRUTOR ; VONDER PLUS ; NOVE54 ; TOYAMA ; VOLLO ;MYMAX ;BLACK+DECKER;  HULTAFORS; DWT; AWT; WAP; PUMA; POWER";
            $gmk8_marcas_custoTabela = "ECCOFER ; KOMELON ; VONDER ; VONDER CONSTRUTOR ; VONDER PLUS ; NOVE54 ; HULTAFORS; DWT; AWT ; WAP; PUMA";

            $deltatop_marcas = "BEL BRINK ; BEL LAZER ; BEL SPORTS ; BELFIX ; BLACK&DECKER ; BUMAFER ; CONDOR ; DEWALT ; GP ; JUNGES ; JUSTICE LEAGUE ; LIVEUP ; MORMAII ; MOTO X ; MULTIKIDS ; MULTILASER ;  MY HEALTH ; PELEGRIN ; PRESTO ; RED NOSE ; STANLEY ; WORKER ; MUNDI; BOXER; BUFFALO";

            $gmk8_marcas = $this->TratarDados($gmk8_marcas);
            $gmk8_marcas_custoTabela = $this->TratarDados($gmk8_marcas_custoTabela);
            $deltatop_marcas = $this->TratarDados($deltatop_marcas);

            // Empresas que entram na lista de prioridade de estoque
            $prioridadeEmpresas = array(GMK8_ID, DELTATOP_ID, GMP_ID);

            // Verifica qual empresa na prioridade deve ser inserida o pedido por estoque
            $resposta = $this->filtroPedido_prioridadeEstoque($prioridadeEmpresas, $Pedido_T005); //, array(GMK8_ID => 0, DELTATOP_ID => 30, GMP_ID => 0));

            // Caso 2(caso nenhuma das empresas tenha estoque para o pedido)
            if($resposta['targetEmpresa'] === null)
            {
                // Precisamos separar os pedidos por duas empresas
                $tem_deltatop = false;
                $tem_gmk8 = false;

                // Verifica se os produtos do pedido tem marcas da deltatop e gmk8
                foreach($resposta['produtos'] as $key => $value)
                {
                    if(in_array($resposta['produtos'][$key]['marcaProduto'], $gmk8_marcas))
                    {
                        $tem_gmk8 = true;
                    }

                    if(in_array($resposta['produtos'][$key]['marcaProduto'], $deltatop_marcas))
                    {
                        $tem_deltatop = true;
                    }
                }

                // Caso o pedido tenha produtos da deltatop e produtos da gmk8 envia para deltatop. Caso só tenha produtos da DELTATOP envia para a deltatop
                if(($tem_deltatop && $tem_gmk8) || $tem_deltatop)
                {
                    //Envia para DELTATOP
                    $finalLog .= ' | FILTRO: 4-CASO2-OPT1 | TARGET EMPRESA: DELTATOP | PRODUTOS: ' . var_export($resposta['produtos'], true);
                    return array('targetEmpresa' => DELTATOP_ID, 'msgAjax' => 'F4-DELTATOP', 'finalLog' => $finalLog, 'filtroUtilizado' => 'F4-1 - Pedido com marcas da DELTATOP e GMK8 enviado a DELTATOP');
                }
                //Caso o pedido tenha somente gmk8
                else if($tem_gmk8)
                {
                    //A variavel gmk8_marcas tem marcas que não existem na gmk8_marcas_CustoTabela é preciso saber se as marcas que não existem no gmk8_marcas_custoTabela está no pedido
                    //Pois as marcas do gmk8_marcas que não estão no gmk8_marcas_custoTabela não podem ser enviadas a deltatop por isso vão direto para a gmk8
                    $diferencaCustoTabela = array_diff($gmk8_marcas, $gmk8_marcas_custoTabela);
                    $tem_gmk8_dif = false;
                    $tem_gmk8_CustoTabela = false;

                    foreach($resposta['produtos'] as $key => $value)
                    {
                        if(in_array($resposta['produtos'][$key]['marcaProduto'], $diferencaCustoTabela))
                        {
                            $tem_gmk8_dif = true;
                        }

                        if(in_array($resposta['produtos'][$key]['marcaProduto'], $gmk8_marcas_custoTabela))
                        {
                            $tem_gmk8_CustoTabela = true;
                        }
                    }

                    if(($tem_gmk8_dif && $tem_gmk8_CustoTabela) || $tem_gmk8_dif)
                    {
                        $finalLog .= ' | FILTRO: 4-CASO2-OPT2 | TARGET EMPRESA: GMK8 | PRODUTOS: ' . var_export($resposta['produtos'], true);
                        return array('targetEmpresa' => GMK8_ID, 'msgAjax' => 'F4-GMK8', 'finalLog' => $finalLog, 'filtroUtilizado' => 'F4-2 - Pedido com marcas somente da GMK8 enviado a GMK8');
                    }
                    else if($tem_gmk8_CustoTabela)
                    {
                        $empresasProdutos = array(GMK8_ID, DELTATOP_ID);

                        //Faz estoque prioridade na D009_Quantidade_Estoque_Tabela
                        $respostaPrioridade = $this->filtroPedido_prioridadeEstoque($empresasProdutos, $Pedido_T005, 'tabela');

                        if($respostaPrioridade['targetEmpresa'] !== null)
                        {
                            $finalLog .= " | FILTRO: 4-CASO2-OPT4 | TARGET EMPRESA: " . $stringEmpresas[$respostaPrioridade['targetEmpresa']] . " | RESPOSTA PRIORIDADE: " . var_export($respostaPrioridade, true);
                            return array('finalLog' => $finalLog, 'msgAjax' => "F4-" . $stringEmpresas[$respostaPrioridade['targetEmpresa']], 'targetEmpresa' => $respostaPrioridade['targetEmpresa'], 'filtroUtilizado' => 'F4-4 - CONSULTA TABELA OVD, enviado a empresa com estoque tabela(GMK8/DELTATOP)');
                        }
                        else
                        {
                            $custoTabelas = $this->filtroPedido_retornaCustoTabela($respostaPrioridade['produtos']);

                            $finalLog .= " | FILTRO: 4-CASO2-OPT3 | TARGET EMPRESA: " . $stringEmpresas[$custoTabelas['targetEmpresa']] . " | RESPOSTA PRIORIDADE: " . var_export($respostaPrioridade, true) . " | CUSTO TABELA: " . var_export($custoTabelas, true);
                            return array('finalLog' => $finalLog, 'msgAjax' => "F4-" . $stringEmpresas[$custoTabelas['targetEmpresa']], 'targetEmpresa' => $custoTabelas['targetEmpresa'], 'filtroUtilizado' => 'F4-3 - CUSTO TABELA, enviado a empresa mais barata(GMK8/DELTATOP)');
                        }
                    }
                }
            }
            // Caso 1
            else
            {
                $finalLog .= " | FILTRO: 4-CASO1 | TARGET EMPRESA: ". $stringEmpresas[$resposta['targetEmpresa']] ." | Resposta prioridade: " . var_export($resposta, true);
                return array('msgAjax' => 'F4-' . $stringEmpresas[$resposta['targetEmpresa']], 'targetEmpresa' => $resposta['targetEmpresa'], 'finalLog' => $finalLog, 'filtroUtilizado' => 'F4 - Prioridade de estoque: GMK8/DELTATOP/GMP');
            }
        }

        $finalLog .= "| PEDIDO RECUSADO PELO FILTRO";
        return array('finalLog' => $finalLog, 'msgAjax' => 'recusado');
    }


}
?>    