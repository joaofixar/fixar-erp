<?php
namespace hardness;

Class Intelipost
{
    public function criarPedido($T005_Id)
    {
    	global $g;

        //teste
        // $apiKey = "d7d07f936c6222607e5b5fc92da85db211a4711d65f8188c564d7d1f76a11d83";        
        // $platform="Hardness";
        // $platformVersion="ERP";
        // $plugin="Plugin-Hardness";
        // $pluginVersion="v1.0";
        // $urlApi = "https://api.intelipost.com.br/api/v1/";

        require_once('bibliotecas/classes/VEN002.php');
        $VEN002 = new VEN002();

        //Criando esse arquivo porque não está sendo possível personalizar por conta da forma que foi feito o nome do arquivo
        //Guimepa precisa ajustar e tem a chamada em vários lugares nas personalizações dos devs do sistema e ficaria complicado eles alterarem
        $retorno = $VEN002->triggerCriarPedidoIntelipost($T005_Id);
        if(is_array($retorno)){
            return $retorno;
        }

        $retorno = array();
        $campos          ="";
    	$apiKey          = $g['C031']['IntelipostApiKey'];        
        $platform        = $g['C031']['IntelipostPlataform'];
        $platformVersion = $g['C031']['IntelipostPlataformVersion'];
        $plugin          = $g['C031']['IntelipostPlugin'];
        $pluginVersion   = $g['C031']['IntelipostPluginVersion'];
        $urlApi          = $g['C031']['IntelipostUrlApi'];        

        $cClausula = <<<EOT
                    SELECT 
                    D024_Id,
                    D148_Id,
                    D024_Cpf,
                    D024_Cnpj,
                    D024_Inscricao_Estadual,
                    T005_Id_Pedido_Externo,
                    T005_C004_Id,
                    D022_Id,
                    D022_Nome_Empresa,
                    T005_Valor_Frete,
                    D027_Portador,
                    T005_Data_Emissao,
                    T005_Hora_Emissao,
                    T268_Data_Hora_Postagem,
                    D024_Nome_Fantasia,
                    D024_Nome_Empresa,
                    D024_E_Mail,
                    D024_DDD_Telefone_2,
                    D024_Telefone_2,
                    D024_DDD_Telefone_1,
                    D024_Telefone_1,
                    D030_Nome_Pais,
                    D018_UF,
                    D020_Nome_Cidade,
                    D148_Rua,
                    D148_Numero,
                    D148_Bairro,
                    D148_Complemento,
                    D148_Cep,
                    T005_Data_Entrega,
                    T007_Quantidade_Volumes,
                    T005_Peso_Total,
                    T268_Codigo_Rastreio,
                    T007_Serie,
                    T007_Numero_Nota_Fiscal,
                    T007_Chave_Acesso_Nfe,
                    T007_Data_Emissao,
                    T007_Valor_Total,
                    T007_Valor_Total_Produtos,
                    T007_Codigo_CFOP,
                    T005_Ordem_Compra,
                    T005_Id,
                    T005_D022_Id,
                    T005_D036_Id,
                    T007_Id,
                    COUNT(T006_Id) QuantidadeProdutos,
                    T178_Data,
                    T005_Prazo_Entrega,
                    C004_Cnpj
                    FROM T005
                    LEFT JOIN T006 ON T006_T005_Id=T005_Id
                    LEFT JOIN C004 ON T005_C004_Id=C004_Id
                    LEFT JOIN D024 ON T005_D024_Id=D024_Id
                    LEFT JOIN D148 ON IF(IFNULL(T005_D148_Id_Entrega, 0) = 0, D148_D024_Id = D024_Id, T005_D148_Id_Entrega = D148_Id)
                    LEFT JOIN D030 ON D148_D030_Id=D030_Id
                    LEFT JOIN D018 ON D148_D018_Id=D018_Id
                    LEFT JOIN D020 ON D148_D020_Id=D020_Id
                    LEFT JOIN D036 ON T005_D036_Id=D036_Id 
                    LEFT JOIN T007 ON T007_T005_Id=T005_Id
                    LEFT JOIN T008 ON T008_T007_Id=T007_Id 
                    LEFT JOIN D022 ON T007_D022_Id=D022_Id 
                    LEFT JOIN T268 ON T268_T007_Id=T007_Id
                    LEFT JOIN T090 ON T090_T005_Id=T005_Id
                    LEFT JOIN D027 ON T090_D027_Id=D027_Id
                    LEFT JOIN T178 ON T178_T005_Id=T005_Id AND T178_Descricao = 'Coletado'
                    WHERE T005_Id={$T005_Id}
EOT;

        $T005 = mysql_query($cClausula);
        $rT005 = mysql_fetch_array($T005);

        $erro = mysql_error();
        if(!empty($erro)){
        	$retorno[0] = "ERROR";
            $retorno[1] = "ERRO MYSQL: $erro";
            die();
        }
        
        $D168 = mysql_query("Select D168_Id,D168_Id_Transportadora_Ecommerce from D168 where D168_D022_Id={$rT005["T005_D022_Id"]} AND D168_D036_Id={$rT005["T005_D036_Id"]}");
        $rD168 = mysql_fetch_array($D168);

        // Essa função pode ser personalizada e retorna por referência os dados modificados
        $this->modificarValoresPosQuerys($rT005, $rD168); 

        $cpf                          = $rT005["D024_Cpf"];
        $cnpj                         = $rT005["D024_Cnpj"];

        $order_number                 = $rT005["T005_Id_Pedido_Externo"];
        $parent_shipment_order_number = " ";
        $customer_shipping_costs      = $rT005["T005_Valor_Frete"];
        $sales_channel                = $rT005["D027_Portador"];

        /* A data do banco de dados vem com horas separado
        exemplo -> "2022-07-21 17:00:00"
        mas o intelipost utiliza o formato -> "2022-07-21T17:00:00-03:00"
        Onde o "-03:00" é o fuso horário
        */
        $created                      = trim($rT005["T005_Data_Emissao"]) . "T" . trim($rT005['T005_Hora_Emissao']) . '-03:00';

        $shipped_date                 = $rT005["T178_Data"];
        $shipment_order_type          = "NORMAL";
        $delivery_method_id           = $rD168["D168_Id_Transportadora_Ecommerce"];

        //end_customer

        /* Código divide nome e sobrenome para enviar
        para a Intelipost */
        $nome = trim($rT005["D024_Nome_Empresa"]);
        if(!empty($cpf))
        {
            $nome = explode(" ", $nome);

            $first_name = array_splice($nome, 0, 1)[0];
            $last_name = implode(" ", $nome);
        }
        else
        {
            $first_name = $nome;
            $last_name = " ";
        }

        $email                       = $rT005["D024_E_Mail"];
        $cellphone                   = $rT005["D024_DDD_Telefone_2"].$rT005["D024_Telefone_2"];
        $phone                       = $rT005["D024_DDD_Telefone_1"].$rT005["D024_Telefone_1"];
        $shipping_country            = $rT005["D030_Nome_Pais"];
        $shipping_state              = $rT005["D018_UF"]; 
        $shipping_city               = $rT005["D020_Nome_Cidade"];
        $shipping_address            = $rT005["D148_Rua"];
        $shipping_number             = $rT005["D148_Numero"];
        $shipping_quarter            = $rT005["D148_Bairro"];
        $shipping_reference          = " ";
        $shipping_additional         = $rT005["D148_Complemento"];
        $shipping_zip_code           = $rT005["D148_Cep"];
        $state_tax_payer_id          = $rT005['D024_Inscricao_Estadual'];        

        $origin_zip_code             = " ";
        $origin_warehouse_code       = " ";
        $origin_federal_tax_payer_id = $rT005["C004_Cnpj"];
        $estimated_delivery_date     = $rT005["T005_Prazo_Entrega"];

        //informações adicionais
        $key  = "";
        $key2 = "";

        //dados do pedido volume
        $name = "";
        $shipment_order_volume_number = $rT005["T007_Quantidade_Volumes"];
        $volume_type_code             = "BOX";
        $weight                       = $rT005["T005_Peso_Total"];
        $width                        = 0;
        $height                       = 0;
        $length                       = 0;
        $products_quantity            = $rT005["QuantidadeProdutos"];
        $products_nature              = "Produtos";
        $tracking_code                = $rT005["T268_Codigo_Rastreio"];

        //dados nota fiscal do pedido
        $shipment_invoice       = $rT005["T007_Id"];
        $invoice_series         = $rT005["T007_Serie"];
        $invoice_number         = $rT005["T007_Numero_Nota_Fiscal"];
        $invoice_key            = $rT005["T007_Chave_Acesso_Nfe"];
        $invoice_date           = $rT005["T007_Data_Emissao"];
        $invoice_total_value    = $rT005["T007_Valor_Total"];
        $invoice_products_value = $rT005["T007_Valor_Total_Produtos"];
        $invoice_cfop           = $rT005["T007_Codigo_CFOP"];

        //numeros de pedidos externos
        $marketplace = $rT005["T005_Ordem_Compra"];
        $sales       = "";
        $plataforma  = $rT005["T005_Id_Pedido_Externo"];
        $erp         = $rT005["T005_Id"];


        //VALIDAÇÔES DE CAMPOS OBRIGATORIOS DO PEDIDO

        if(empty($delivery_method_id)){
            $campos.="Não encontrado transportadora vinculada a intelipost.<br>";
        }

        if(empty($sales_channel)){
            $campos.="Portador do pedido.<br>";
        }

        if(empty($order_number)){
            $order_number = $T005_Id;
        }

        if(empty($customer_shipping_costs)){
            $campos.="Frete do pedido.<br>";
        }

        if(empty($created)){
            $campos.="Data de emissão do pedido.<br>";
        }

        if(empty($weight)){
            $campos.="Peso total.<br>";
        }

        if(empty($products_quantity)){
            $campos.="Quantidade de produtos.<br>";
        }

        //VALIDAÇÕES CAMPOS DOS DADOS DO CLIENTE
        if(empty($rT005["D024_Id"])){
            $campos.="Cliente não vinculado.<br>";
        }

        if(!empty($cnpj)){
            $federal_tax_payer_id = $cnpj;
            $is_company           = "true";
        }

        if(!empty($cpf)){
            $federal_tax_payer_id = $cpf;
            $is_company           = "false";
        }

        if(empty($cpf) && empty($cnpj)){
            $campos.="CPF/CNPJ está em branco.<br>";
        }

        if(empty($first_name)){
            $campos.="Nome Fantasia.<br>";
        }

        if(empty($email)){
            $campos.="Email do cliente.<br>";
        }

        if(empty($phone)){
            $phone = " ";
        }

        if(empty($cellphone)){
            $cellphone = " ";
        }
 
        //VALIDAÇÔES DO ENDEREÇO DO CLIENTE
        if(empty($shipping_address)){
            $campos.="Endereço de entrega.<br>";
        }

        if(empty($shipping_number)){
            $campos.="Nº do endereço de entrega.<br>";
        }

        if(empty($shipping_additional)){
            // $campos.="Complemento do endereço de entrega.<br>";
            $shipping_additional=" ";
        }

        if(empty($shipping_quarter)){
            $campos.="Bairro do endereço de entrega.<br>";
        }

        if(empty($shipping_city)){
            $campos.="Cidade do endereço de entrega.<br>";
        }

        if(empty($shipping_state)){
            $campos.="Estado do endereço de entrega.<br>";
        }

        if(empty($shipping_zip_code)){
            $campos.="CEP do endereço de entrega.<br>";
        }

        if(empty($shipping_country)){
            $campos.="País do endereço de entrega.<br>";
        }

        //VALIDAÇÔES DOS DADOS DA NOTA FISCAL
        if(empty($shipment_invoice)){
            $campos.="Nota fiscal não vinculada.<br>";
        }

        if(empty($shipment_order_volume_number)){
            $campos.="Volume da nota.<br>";
        }

        if(empty($invoice_series)){
            $campos.="Nº serie nota fiscal.<br>";
        }

        if(empty($invoice_number)){
            $campos.="Nº da nota fiscal.<br>";
        }

        if(empty($invoice_key)){
            $campos.="Chave da nota fiscal.<br>";
        }

        if(empty($invoice_date)){
            $campos.="Data de emissão da nota fiscal.<br>";
        }

        if(empty($invoice_total_value)){
            $campos.="Valor total da nota fiscal.<br>";
        }

        if(empty($invoice_products_value)){
            $campos.="Valor total dos produtos da nota fiscal.<br>";
        }

        if(empty($invoice_cfop)){
            $campos.="CFOP da nota fiscal.<br>";
        }

        if(empty($estimated_delivery_date)){
            $campos.="Data de entrega.<br>";
        }else{
            $data = explode(" ", $estimated_delivery_date);
            $estimated_delivery_date = trim($data[0]);
            
            if(stristr($estimated_delivery_date, "/")){
                $mestimated_delivery_date = explode("/", $estimated_delivery_date);
                $estimated_delivery_date = $mestimated_delivery_date[2].'-'.$mestimated_delivery_date[1].'-'.$mestimated_delivery_date[0];
            }

        }

        if(!empty($shipped_date))
        {
            /* A data do banco de dados vem com horas separado
            exemplo -> "2022-07-21 17:00:00"
            mas o intelipost utiliza o formato -> "2022-07-21T17:00:00-03:00"
            Onde o "-03:00" é o fuso horário e o ".000" são os milisegundos
            */
            $shipped_date = str_replace(" ", "T", $shipped_date);
            $shipped_date = trim($shipped_date) . '-03:00';
        }

        $dadosJSON = array(
            "order_number" => $order_number,
            "parent_shipment_order_number" => $parent_shipment_order_number,
            "customer_shipping_costs" => $customer_shipping_costs,
            "sales_channel" => $sales_channel,
            "scheduled" => false,
            "created" => $created,
            "shipped_date" => $shipped_date,
            "shipment_order_type" => $shipment_order_type,
            "delivery_method_id" => $delivery_method_id,
            "end_customer" => array(
                "first_name" => $first_name,
                "last_name" => $last_name,
                "email" => $email,
                "phone" => $phone,
                "cellphone" => $cellphone,
                "is_company" => $is_company,
                "federal_tax_payer_id" => $federal_tax_payer_id,
                "state_tax_payer_id" => $state_tax_payer_id,
                "shipping_country" => $shipping_country,
                "shipping_state" => $shipping_state,
                "shipping_city" => $shipping_city,
                "shipping_address" => $shipping_address,
                "shipping_number" => $shipping_number,
                "shipping_quarter" => $shipping_quarter,
                "shipping_reference" => $shipping_reference,
                "shipping_additional" => $shipping_additional,
                "shipping_zip_code" => $shipping_zip_code
            ),
            "origin_zip_code" => $origin_zip_code,
            "origin_warehouse_code" => $origin_warehouse_code,
            "origin_federal_tax_payer_id" => $origin_federal_tax_payer_id,
            "shipment_order_volume_array" => array(
                array(
                    "name" => $name,
                    "shipment_order_volume_number" => $shipment_order_volume_number,
                    "volume_type_code" => $volume_type_code,
                    "weight" => $weight,
                    "width" => $width,
                    "height" => $height,
                    "length" => $length,
                    "products_quantity" => $products_quantity,
                    "products_nature" => "products_nature",
                    "shipment_order_volume_invoice" => array(
                        "invoice_series" => $invoice_series,
                        "invoice_number" => $invoice_number,
                        "invoice_key" => $invoice_key,
                        "invoice_date" => $invoice_date,
                        "invoice_total_value" => $invoice_total_value,
                        "invoice_products_value" => $invoice_products_value,
                        "invoice_cfop" => $invoice_cfop
                    ),
                    "tracking_code" => $tracking_code
                )
            ),
            "estimated_delivery_date" => $estimated_delivery_date,
            "additional_information" => array(
                "key" => $key,
                "key2" => $key2
            ),
            "external_order_numbers" => array(
                "marketplace" => $marketplace,
                "sales" => $sales,
                "plataforma" => $plataforma,
                "erp" => $erp
            )
        );
        
        $json = json_encode($dadosJSON, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        log("JSON: {$json}");

        if(empty($campos)){
            $curl = curl_init();
            curl_setopt_array($curl, 
                array(
                    CURLOPT_URL => "{$urlApi}shipment_order",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $json,
                    CURLOPT_HTTPHEADER => array(
                        "api-key: {$apiKey}",
                        "content-type: application/json",
                        "platform: {$platform}",
                        "platform-version: {$platformVersion}",
                        "plugin: {$plugin}",
                        "plugin-version: {$pluginVersion}"
                    ),
                )
            );

            $data = curl_exec($curl);
            curl_close($curl);
            $resposta = (array) json_decode($data);

            log("JSON: $json");
            $messages = (array)$resposta["messages"];
            $messages = (array)$messages[0];

            $type=$messages["type"];
            $texto = $messages["text"];
            $key = $messages["key"];

            if(stripos($texto, "internal")){

                if (!(stripos("errorCode", $texto))){
                    $codigoErro = substr($texto, strpos($texto,"errorCode")+strlen("errorCode"));
                    $mensagem = "Erro interno no envio de informações para a intelipost. Código do erro:{$codigoErro}<br>";
                }
                
            }else{
                $mensagem = "Retorno Intelipost pedido " . $rT005["T005_Id"] . ": " . $messages["text"];
            }

        }else{
            $type = "ERROR";
            $mensagem = "<b>Faltam as seguintes informações no seu pedido:</b><br><br>".$campos;
        }

        if($type=="SUCCESS"){
            log("realizou o envio dos dados para o intelipost");
            mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Envio de informações para a intelipost realizada com sucesso!.','')");
            mysql_query("UPDATE T005 set T005_Data_Envio_Elogistics=CURRENT_TIMESTAMP where T005_Id={$T005_Id}");
        }else{
            log("nao realizou o envio dos dados para a intelipost");
            mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Envio de informações para a intelipost falhou!<br>Erro:{$mensagem}.','')");
        }

	    $retorno[0] = trim($type);
	    $retorno[1] = $mensagem;

	    return $retorno;
    }

    public function modificarValoresPosQuerys(&$rT005, &$rD168)
    {
        return false;
    }

    /**
     * @return
     * 
     * Função que é utilizada na conexão do
     * Webhook da Intelipost. Quando um status é atualizado na
     * Intelipost é enviado um POST para a Hardness com um JSON
     * do novo status, a /api/rel/post.php/IntelipostStatus recebe
     * o JSON e passa os parâmetros nessa função para inserção/atualização
     * no banco de dados
     */
    public function AtualizaStatus($T005_Id_Pedido_Externo, $macroStatus, $microStatus, $datahStatus, $previsaoTransp, $codigoPlp = false)
    {
        global $g;

        /* DATA DE PREVISÃO DA TRANSPORTADORA */
        $previsaoTransp = str_replace('T', ' ', $previsaoTransp);
        $previsaoTransp = explode('.', $previsaoTransp)[0];

        /* DATA DE MODIFICAÇÃO DO STATUS */
        $dataHoraTemp = explode('T', $datahStatus);                       // Divide o horário da data
        $dataEntregaUpdate = trim($dataHoraTemp[0]);                      // Atribui a data de inserção no banco
        $dataStatus = trim(date("d/m/Y", strtotime($dataEntregaUpdate))); // Converte a data YYYY-MM-DD para DD-MM-YYYY para leitura
        $horaStatus = trim(explode('.', $dataHoraTemp[1])[0]);            // Extrai hora da data do status no intelipost

        $dataHoraUpdate = "$dataEntregaUpdate $horaStatus";

        // Verifica se o pedido passado por parametro existe no sistema
        $T005 = mysql_query("SELECT T005_Id,
                             T005A_Status_Elogistics        
                             FROM T005
                             LEFT JOIN T005A ON T005A_T005_Id = T005_Id
                             WHERE T005_Id_Pedido_Externo = '$T005_Id_Pedido_Externo' OR
                             T005_Id = '{$T005_Id_Pedido_Externo}'");
        if($erro = mysql_error()){error_log($erro);}

        $mT005 = mysql_fetch_assoc($T005);
        $T005_Id = $mT005['T005_Id'];

        $statusAtual = $mT005['T005A_Status_Elogistics'];

        // Junta macro e micro status para apresentação
        $status = $macroStatus . ' - ' . $microStatus;

        /*Caso o codigo da Plp do pedido não for vazio
        e o T005 id também não for*/
        if(!empty($codigoPlp) AND !empty($T005_Id))
        {
            mysql_query("UPDATE T005A
                         SET T005A_Codigo_Plp_Elogistics = '{$codigoPlp}'
                         WHERE T005A_T005_Id = '{$T005_Id}'");
            if($erro = mysql_error()){error_log($erro);}
        }

        /* Se o T005_Id não for vazio(pedido existe)
        Verifica se o status não é o mesmo enviado, para não
        abrir várias entradas no histórico*/
        if(!empty($T005_Id) && $statusAtual != $status)
        {
            /* Atualiza status do Intelipost dentro do pedido(T005) 
            Caso o macroStatus for entregue utiliza a data que o status
            foi acionado para o T005_Data_Entrega*/
            mysql_query("UPDATE T005 
                         LEFT JOIN T005A ON T005A_T005_Id = T005_Id
                         SET T005A_Status_Elogistics = '{$status}',
                         T005_Data_Entrega = IF('{$macroStatus}' = 'ENTREGUE', '{$dataEntregaUpdate}', T005_Data_Entrega),
                         T005A_Previsao_Transportadora_Elogistics = '{$previsaoTransp}',
                         T005A_Data_Ultimo_Status_Elogistics = '{$dataHoraUpdate}'
                         WHERE T005_Id = '{$T005_Id}'");
            if($erro = mysql_error()){error_log($erro);}
            
            // Insere o novo status no histórico para manter controle dos status
            mysql_query("insert into T178(T178_T005_Id,
                         T178_C007_Id, 
                         T178_Data, 
                         T178_Descricao, 
                         T178_Observacao) VALUES (
                         {$T005_Id}, 
                         '{$g['usuarioAtual']}',
                         CURRENT_TIMESTAMP(), 
                         '[INTELIPOST] Status atualizado para: $macroStatus - $microStatus ($dataStatus $horaStatus)',
                         '')");
            if($erro = mysql_error()){error_log($erro);}

            return true;
        }else{return false;}
    }

    /** Baixa o pedido na Intelipost
     * e devolve um array com informações
     * pré-selecionadas a partir do JSON.
     * Foi criado inicialmente para baixar o status
     * manual da Intelipost, mas você pode requisitar
     * outras informações se precisar, basta ver
     * o JSON que a API envia em -> https://docs.intelipost.com.br/docs/tms-embarcador/6edde64fdae5a-consultar-pedido-de-entrega
     */
    public function BaixaPedidoIntelipost($T005_Id_Pedido_Externo)
    {
        global $g;

        // Pega as informações necessárias para fazer uma requisição na API
        $apiKey          = $g['C031']['IntelipostApiKey'];        
        $platform        = $g['C031']['IntelipostPlataform'];
        $platformVersion = $g['C031']['IntelipostPlataformVersion'];
        $plugin          = $g['C031']['IntelipostPlugin'];
        $pluginVersion   = $g['C031']['IntelipostPluginVersion'];
        $urlApi          = $g['C031']['IntelipostUrlApi'];
        
        /* REQUISICAO API */
            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => "{$urlApi}shipment_order/$T005_Id_Pedido_Externo",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "api-key: $apiKey",
                "content-type: application/json",
                "platform: $platform",
                "platform-version: $platformVersion",
                "plugin: $plugin",
                "plugin-version: $pluginVersion"
            ),
            ));
            
            $data = json_decode(curl_exec($curl), true);
            
            curl_close($curl);
        /* ------------------- */

        // Se o JSON não responder com erro
        if($data['status'] != 'error')
        {
            // Cria array e atribui dados especificos do JSON
            $resposta = array(
                'code' => true,
                "MACRO_STATUS" => strtoupper( gLimpaAcentosPuro( trim($data['content']['shipment_order_volume_array'][0]['shipment_order_volume_state_localized']) ) ),
                "MICRO_STATUS" => strtoupper( gLimpaAcentosPuro( trim($data['content']['shipment_order_volume_array'][0]['shipment_order_volume_state_history_array'][0]['shipment_volume_micro_state']['default_name']) ) ),
                "DATA_STATUS"  => trim($data['content']['shipment_order_volume_array'][0]['shipment_order_volume_state_history_array'][0]['event_date_iso']),
                "DATA_PREVISAO_ENTREGA_TRANSPORTADORA" => trim($data['content']['shipment_order_volume_array'][0]['estimated_delivery_date_lp_iso']),
                "CODIGO_PLP" => ( trim( $data['content']['shipment_order_volume_array'][0]['pre_shipment_list_state'] ) == 'ERROR' ? 'Falha: ' : '' ) . trim( $data['content']['shipment_order_volume_array'][0]['pre_shipment_list_id'] ),
            );

            if(empty($resposta['MACRO_STATUS']))
            {
                $resposta['code'] = false;
            }
        }
        else
        {
            // Cria array de resposta da API
            $resposta = array(
                'code' => false,
                'erro' => $data['messages']['text']
            );
        }


        return $resposta;
    }
}
?>