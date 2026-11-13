<?php
namespace hardness;
/*
 * 
 */
Class Magento
{
	
	public function auth_magento($requestUrl)
	{

		$adminUrl='http://192.168.15.10:8001/rest/V1/integration/admin/token';
		$ch = curl_init();
		$data = array("username" => "root", "password" => "nex$$387@@");
		$data_string = json_encode($data);
		$ch = curl_init($adminUrl);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($data_string))
		);
		$token = curl_exec($ch);
		$token =  json_decode($token);

		$headers = array("Authorization: Bearer $token");

		$ch = curl_init();
		$ch = curl_init($requestUrl);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$result = curl_exec($ch);
		return $result;
    }

	/**
	 * importarPedido
	 * @param $id string
	 * @return bool
	 */
	public function buscarPedido()
	{
		global $g, $arquivoLogImportacao;

        $get_orders = $this->auth_magento( 'http://192.168.15.10:8001/rest/V1/orders?searchCriteria[filter_groups][0][filters][0][field]=status&searchCriteria[filter_groups][0][filters][0][value]=complete' );
   
        $orders = json_decode( $get_orders, true );

		$listOrders = (array) $orders['items'];

		require_once("bibliotecas/classes/VEN014.php");
		$VEN014 = new VEN014();
		$total_pedidos = 0;
		$sucesso = '';
		foreach ($listOrders as $key => $order) {
			
			$total_pedidos++;
			$items = (array) $order['items'];
			$billing_address = (array) $order['billing_address'];
			$payment = (array) $order['payment'];
			$additional_information = (array) $payment['additional_information'];
			$extension_attributes = (array) $order['extension_attributes'];
			$shipping_assignments = (array) $extension_attributes['shipping_assignments'][0];
			$shipping = (array) $shipping_assignments['shipping'];
			$address = (array) $shipping['address'];
			$address_street = (array) $address['street'];
			$product = (array) $items[0];	

			// $D024 = array();	
			// $T005 = array();
			// $D148 = array();
			// $T006 = array();

			//retirando dados do cliente
			$D024["D024_Cpf"]='65977391080';
			$D024["D024_Cnpj"]='24814653000172';
			$D024["D024_Nome_Fantasia"]=$billing_address['firstname'].' '.$billing_address['lastname'];
			$D024["D024_Nome_Empresa"]=$billing_address['firstname'].' '.$billing_address['lastname'];
			$D024["Razao_Social"]=$billing_address['firstname'].' '.$billing_address['lastname'];
			$D024["D024_Endereco"]=$billing_address['street'][0];
			$D024["D024_Numero"]=$billing_address['region_id'];
			$D024["D024_Cep"]=$billing_address['postcode'];
			$D024["D024_Complemento"]=$billing['street'][1];
			$D024["D024_Bairro"]='';
			$D024["Cidade"]=$billing_address['city'];
			$D024["UF"]=$billing_address['region_code'];
			$D024['Pais']=$billing_address['country_id'];
			$D024["D024_DDD_Telefone_1"]=$billing_address['telephone'];
			$D024["D024_Telefone_1"]='';
			$D024["D024_DDD_Telefone_2"]='';
			$D024["D024_Telefone_2"]='';
			$D024["D024_E_Mail"]=$billing_address['email'];
			$D024["Referencia_Endereco"]='';
			$D024["Informacao_adicional"]='';
			
			//endereço de entrega		

			$D148["UF"]=$address['region_code'];
			$D148["Cidade"]=$address['city'];
			$D148["Pais"]=$address['country_id'];
			$D148["D148_Nome"]=$address['firstname'].' '.$address['lastname'];
			$D148["D148_Cep"]=$address['postcode'];
			$D148["D148_Rua"]=$address['street'][0];
			$D148["D148_Numero"]='';
			$D148["D148_Complemento"]=$address['street'][1];
			$D148["D148_Bairro"]='';
			$D148["D148_DDD_Telefone"]='';
			$D148["D148_Telefone"]=$address['telephone'];
			$D148["D148_DDD_Fax"]='';
			$D148["D148_Fax"]='';
			$D148["D148_Email"]=$address['email'];
			
			$D024['D148'] = $D148;

			//cabeçalho do pedido
			$T005["T005_Valor_Desconto"]=$order['discount_amount'];
			$T005["T005_Valor_Total_Produtos"]=$order['subtotal_incl_tax'];
			$T005["T005_Valor_Total_Produtos_Sem_Desconto"]=$order['subtotal'];
			$T005["T005_Valor_Total"]=$order["grand_total"];
			$T005["T005_Valor_Frete"]=$order['base_shipping_amount'];
			$T005["T005_Observacao_Nota_Fiscal"]=$order['store_name']." ".$product['order_id'];
			//dentro do codigo ha um array $lojas que sera alimentado com as possiveis lojas 
			//$T005["T005_Pgto_Portador"]=;//vem do array de $lojas 
			//$T005["T005_Pgto_Primeiro_Vcto_Valor"]=;//vem do array $lojas
			//$T005["T005_Flag_Totalizar_Peso"]=($g['C031']['totalizarPesoVenda'] == 'S') ? 'S' : 'N';
			$T005["T005_Pedido_Ecommerce"]=$product['order_id'];
			$T005["T005_Ordem_Compra"]=$product['order_id'];
			$T005["T005_Id_Pedido_Externo"]=$product['order_id'];
			$T005["T005_Flag_Plataforma_Ecommerce"]=2;

			$count=0;
			$produtos = array();

			foreach ($items as $key => $item) {
				$count++;
				
				//itens do pedido
				$T006["Peso_Unitario"]=$item['row_weight'];
				$T006["T006_Numero_Pedido_Compra"]=$item['order_id'];
				$T006["T006_Codigo_Produto"]=$item['sku'];
				$T006["T006_Descricao_Produto"]=$item['name'];
				$T006["T006_Quantidade"]=$item['qty_shipped'];
				$T006["T006_Valor_Preco_Sem_Desconto_Unitario"]=$item['price'];
				$T006["Codigo_Barras"]='';
				
				$produtos[] = $T006;

			}
			$T005['D024'] = $D024;
			$T005['T006'] = $produtos;
			
			
			$retorno = $VEN014->cadastrarPedido($T005);  
			   

			if($retorno === false){
				 return "Pedido  ". $T005["T005_Id_Pedido_Externo"] ."  não importado, verifique o log de erros.";
			}else{
				$sucesso .= "Pedido ".$T005["T005_Id_Pedido_Externo"] ." importado com sucesso!";			
			}       
		}
			return $sucesso;
	}

}