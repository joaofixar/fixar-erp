<?php

	
    $api_url_v1 = "https://www.guimepa.com.br/index.php/api/soap?wsdl";     
    $username = 'Marcelo_hardness';
    $password = '7AvEpHEgap?8';

    $cli = new SoapClient($api_url_v1);        
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

         $Customer_Id = $orderInfo['customer_id'];

        $cliente_dados = $cli->call($session, 'customer.info', "$Customer_Id");
        $Customer_Campo_Dado = $cliente_dados['taxvat'];
        $D024_Inscricao_Estadual = $cliente_dados['ie'];


        echo "<pre>";
             print_r($cliente_dados);
         echo "</pre>";
         
            //retirando dados do cliente        
            if(strlen($Customer_Campo_Dado)<=14){
                $D024["D024_Cpf"]=str_replace(array('.', '/', '-'), '', trim($Customer_Campo_Dado));
                $D024["D024_Cnpj"]='';
                $D024["D024_Inscricao_Estadual"]='';
            }else{
                $D024["D024_Cpf"]='';
                $D024["D024_Cnpj"]=str_replace(array('.', '/', '-'), '', trim($Customer_Campo_Dado));
                $D024["D024_Inscricao_Estadual"]=$D024_Inscricao_Estadual;
            }


            $D024 = array();    
            $T005 = array();
            $D148 = array();
            $T006 = array();
            $T090 = array();

            $address_billing = (array) $orderInfo['billing_address'];
            $endereco_cobranca = explode(chr(10),$address_billing['street']);



            $D024["D024_Nome_Fantasia"]=$address_billing['firstname'].' '.$address_billing['lastname']; 
            $D024["D024_Nome_Empresa"]=$address_billing['firstname'].' '.$address_billing['lastname'];    
            //$D024["Razao_Social"]=$address_billing['firstname'].' '.$address_billing['lastname'];
            $D024["D024_Endereco"]=$endereco_cobranca[0];
            $D024["D024_Numero"]=$endereco_cobranca[1];
            $D024["D024_Cep"]=$address_billing['postcode'];
            $D024["D024_Complemento"]=trim($endereco_cobranca[2]);
            $D024["D024_Bairro"]=$endereco_cobranca[3];
            $D024["Cidade"]=$address_billing['city'];
            $D024["UF"]=strtoupper ($address_billing['region']);
            $D024['Pais']=$address_billing['country_id'];
            $D024["D024_DDD_Telefone_1"]=$address_billing['telephone'];
            $D024["D024_Telefone_1"]='';
            $D024["D024_DDD_Telefone_2"]=$address_billing['fax'];
            $D024["D024_Telefone_2"]='';
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
        
        $D024['D148'] = $D148;
        $pagamento = (array) $orderInfo['payment'];

        //cabeçalho do pedido
        $T005["T005_Valor_Desconto"]=$orderInfo['discount_amount'];
        $T005["T005_Valor_Total_Produtos"]=$orderInfo['subtotal'];
        $T005["T005_Valor_Total_Produtos_Sem_Desconto"]=$orderInfo['subtotal'];
        $T005["T005_Valor_Total"]=$orderInfo["grand_total"];
        $T005["T005_Valor_Frete"]=$orderInfo['shipping_amount'];
        $T005["Id_Loja"]=$orderInfo['store_id'];
        $T005["T005_Pedido_Ecommerce"]=$orderInfo['order_id'];       
        $T005["T005_Id_Pedido_Externo"]=$orderInfo['order_id'];
        $T005["T005_Observacao_Nota_Fiscal"]=$orderInfo['increment_id']." ".$orderInfo['integracommerce_id']." ".$pagamento['pagarme_transaction_id']." ".$orderInfo['shipping_description'];


           // echo "<pre>";
           // print_r($pagamento);
           // echo "</pre>";       


            //tratando canal        
            if ($orderInfo['canal'] == 'Loja') { //se for loja         
               
                if(empty($orderInfo['ext_order_id'])){ 
                //se estiver em branco o campo
                //recebe increment_id 
                $T005["T005_Ordem_Compra"]= "MG - " . $orderInfo['increment_id'];
                }else {
                //se não estiver em branco 
                //recebe MG+EXT_ORDER_ID
                $T005["T005_Ordem_Compra"]= "MG - " . $orderInfo['ext_order_id'];
                }
            }
            else if($orderInfo == 'MercadoLivre')
            { // se for mercado livre(não loja)
                if(empty($orderInfo['ext_order_id'])){ 
                //se estiver em branco o campo
                //recebe increment_id 
                $T005["T005_Ordem_Compra"]= "ML - " . $orderInfo['increment_id'];
                }else {
                //se não estiver em branco 
                //recebe MG+EXT_ORDER_ID
                $T005["T005_Ordem_Compra"]= "ML - " . $orderInfo['ext_order_id'];
                }
            }
            else
            {
                $T005["T005_Ordem_Compra"]= $orderInfo['increment_id'];
            }


        $count=0;
        $produtos = array();
        $itens = (array) $orderInfo['items'];
       

           // echo "<pre>";
           // print_r($itens);
           // echo "</pre>";

        foreach ($itens as $key => $item) {
            $count++;
            $product = (array) $item;   

            //itens do pedido
            $T006["Peso_Unitario"]=$product['weight'];
            $T006["T006_Numero_Pedido_Compra"]=$product['order_id'];
            $T006["T006_Codigo_Produto"]=$product['sku'];
            $T006["T006_Descricao_Produto"]=$product['name'];
            $T006["T006_Quantidade"]=$product['qty_ordered'];
            $T006["T006_Valor_Preco_Sem_Desconto_Unitario"]=$product['price'];
            $T006["Codigo_Barras"]='';
    
            $produtos[] = $T006;

        }

        $T005['D024'] = $D024;
        $T005['T006'] = $produtos;
        
            
            $string = $pagamento['installment_description'];

            $pagamentos = array();
        
            if (strpos($string, 'de')){
                $parcelas = substr($string,0,strpos($string,'x'));
                $valor = substr($string,strpos($string,'R$')+2,strpos($string,'sem'));
                $valor = floatval(str_replace(',','.',$valor));
                $numeroparcela=1;
                for ($i=0; $i < $parcelas ; $i++) { 
                   $vencimento = date('d/m/Y', strtotime("+".($i*30)." days",strtotime(date('Y-m-d'))));
                   $T090["T090_Numero_Parcela"]=$numeroparcela;
                   $T090["T090_Data_Vencimento"]=$vencimento;
                   $T090["T090_Valor_Parcela"]=$valor;
                   $pagamentos[] = $T090;
                   $numeroparcela++;
                }
            }
            else if (strpos($string, 'vista')){
                $valor = substr($string, strpos($string,'R$')+2);
                $valor = str_replace('.','',$valor);
                $valor = str_replace(',','.',$valor);
                $vencimento = date('Y-m-d');
                $T090["T090_Numero_Parcela"]=1;
                $T090["T090_Data_Vencimento"]=$vencimento;
                $T090["T090_Valor_Parcela"]=$valor;
                $pagamentos[] = $T090;
            }

            else{
                $vencimento = date('Y-m-d');
                $T090["T090_Numero_Parcela"]=1;
                $T090["T090_Data_Vencimento"]=$vencimento;
                $T090["T090_Valor_Parcela"]= $T005["T005_Valor_Total"];
                $pagamentos[] = $T090;
            } 



            $Metodo=$pagamento['method'];
            $Metodo=trim($Metodo);
            //tratando campo metodo
            if(strpos($Metodo, 'cc')){
                $T090["Metodo"] = 'CARTAO CREDITO';
            } else if (strpos($Metodo, 'bank')) {
                $T090["Metodo"] = 'TRANSFERENCIA BANCAR';
            } else if (strpos($Metodo, 'boleto')) {
                $T090["Metodo"]  = 'BOLETO BANCÁRIO';
            } else if (strpos($Metodo, 'ondelivery')){
                $T090["Metodo"] = 'DINHEIRO';
            } else if (strpos($Metodo, 'payment')){
                $T090["Metodo"]  = 'DEPÓSITO BANCÁRIO';
            }

            $T005['T090'] = $pagamentos;

        // echo "<pre>";
        // print_r($T090);
        // echo "</pre>";

        echo "<pre>";
        print_r($T005);
        echo "</pre>";
        echo "<hr>";

        // echo "\n\nTeste servidor\n\n";
        // echo print_r($_SERVER);

    

?>