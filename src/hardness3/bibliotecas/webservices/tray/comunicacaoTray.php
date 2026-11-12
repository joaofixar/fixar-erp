<?php
namespace hardness;
/* INTEGRAÇÃO E-COMMERCE TRAY
 *
 * URL's da API utilizadas
 * https://api.commerce.tray.com.br/auth 					 Gerar/atualizar access_token (Retorna access_token)
 * https://api.commerce.tray.com.br/orders 					 Lista pedidos (Retorna json com array de pedidos)
 * https://api.commerce.tray.com.br/orders/{$id} 			 Consulta o pedido (Retorna json com os dados do pedido)
 * https://api.commerce.tray.com.br/customers/{$id} 		 Consulta o cliente (Retorna json com os dados do cliente)
 * https://api.commerce.tray.com.br/customer_addresses/{$id} Consulta o endereço do cliente (Retorna json com os dados do endereço do cliente)
 * https://api.commerce.tray.com.br/products_solds/{$id} 	 Consulta o produto vendido (Retorna json com os dados do item da venda)
 * https://api.commerce.tray.com.br/products/{$id} 			 Consulta o cadastro do produto (Retorna json com os dados do produto)
 */

definirClasse('COMUNICACAOTRAY');
class ComunicacaoTray_ {

	private $consumer_key = false;
	private $consumer_secret = false;
	private $store = false;
	private $code = false;
	private $db = false;
	private $export_url = false;
	private $access_token = false;
	private $refresh_token = false;
	private $access_token_expiration = false;
	private $refresh_token_expiration = false;
	private $update_data = false;

	public function __construct() {

	}

	public function getUpdate_data(){
        return $this->update_data;
    }

    public function setUpdate_data($update_data){
        $this->update_data = $update_data;
    }

	public function getConsumer_key(){
        return $this->consumer_key;
    }

    public function setConsumer_key($consumer_key){
        $this->consumer_key = $consumer_key;
    }

    public function getConsumer_secret(){
        return $this->consumer_secret;
    }

    public function setConsumer_secret($consumer_secret){
        $this->consumer_secret = $consumer_secret;
    }

    public function getStore(){
        return $this->store;
    }

    public function setStore($store){
        $this->store = $store;
    }

    public function getCode(){
        return $this->code;
    }

    public function setCode($code){
        $this->code = $code;
    }

    public function getDb(){
        return $this->db;
    }

    public function setDb($db){
        $this->db = $db;
    }

    public function getExport_url(){
        return $this->export_url;
    }

    public function setExport_url($export_url){
        $this->export_url = $export_url;
    }

    public function getAccess_token(){
        return $this->access_token;
    }

    public function setAccess_token($access_token){
        $this->access_token = $access_token;
    }

    public function getRefresh_token(){
        return $this->refresh_token;
    }

    public function setRefresh_token($refresh_token){
        $this->refresh_token = $refresh_token;
    }

    public function getAccess_token_expiration(){
        return $this->access_token_expiration;
    }

    public function setAccess_token_expiration($access_token_expiration){
        $this->access_token_expiration = $access_token_expiration;
    }

    public function getRefresh_token_expiration(){
        return $this->refresh_token_expiration;
    }

    public function setRefresh_token_expiration($refresh_token_expiration){
        $this->refresh_token_expiration = $refresh_token_expiration;
    }

    public function autenticacaoTray($T005_Id = 0)
	{
		global $g;

		set_time_limit(0);
		
		$consumerKey= $g['C031']['ConsumerKeyTray'];
		$consumerSecret= $g['C031']['ConsumerSecretTray'];

		//Seleciona as lojas e faz loop em todas elas
		$sql = mysql_query("SELECT * FROM Z001 WHERE IFNULL(Z001_Code,'') != ''");
		while($store = mysql_fetch_array($sql)){
			//Dados de integração da Hardness
			// $this->setConsumer_key('4e11b543a70cc2cadb69d7157a4d338fde21fdb9f4a2c3a83311a3e3bfe28957');
			// $this->setConsumer_secret('a3ecc6b74767611bfb5666c4dc9a0049eb4ed0e0f24f773bf7bb77f942606887');

			$this->setConsumer_key($consumerKey);
			$this->setConsumer_secret($consumerSecret);

			//Código de autorização do cliente
			$this->setCode($store['Z001_Code']);
			$this->setStore($store['Z001_Store']);
			$this->setDb($store['Z001_Db']);
			$this->setExport_url($store['Z001_Export_URL']);

			//Dados de acesso aos dados do cliente
			$this->setAccess_token($store['Z001_Access_Token']);
			$this->setRefresh_token($store['Z001_Refresh_Token']);
			$this->setAccess_token_expiration($store['Z001_Access_Token_Expiration']);
			$this->setRefresh_token_expiration($store['Z001_Refresh_Token_Expiration']);
			
			if($T005_Id > 0){
				$retorno = $this->atualizarPedidoTray($T005_Id);
			}else{
				$retorno = $this->listarPedidos();
			}

			if($this->getUpdate_data()){
				//Atualiza os dados de acesso ao cliente
				$update = "UPDATE Z001 SET ";
				$update .="Z001_Access_Token = '{$this->getAccess_token()}', ";
				$update .="Z001_Refresh_Token = '{$this->getRefresh_token()}', ";
				$update .="Z001_Access_Token_Expiration = '{$this->getAccess_token_expiration()}', ";
				$update .="Z001_Refresh_Token_Expiration = '{$this->getRefresh_token_expiration()}' ";
				$update .="WHERE Z001_Store = '{$store['Z001_Store']}'";
				mysql_query($update);
			}		
			return $retorno;
		}
	}

	public function autenticacaoTrayx($Z001_Id = 0)
	{
		global $g;
		$consumerKey= $g['C031']['ConsumerKeyTray'];
		$consumerSecret= $g['C031']['ConsumerSecretTray'];

		//Seleciona as lojas e faz loop em todas elas
		//$sql = mysql_query("SELECT * FROM Z001 WHERE Z001_Id='{$Z001_Id}'");
		$sql = mysql_query("SELECT * FROM Z001 WHERE IFNULL(Z001_Code,'') != ''");
		while($store = mysql_fetch_array($sql)){
			//if(mysql_num_rows($sql)>=0){
			//$store = mysql_fetch_array($sql);
			//Dados de integração da Hardness
			// $this->setConsumer_key('4e11b543a70cc2cadb69d7157a4d338fde21fdb9f4a2c3a83311a3e3bfe28957');
			// $this->setConsumer_secret('a3ecc6b74767611bfb5666c4dc9a0049eb4ed0e0f24f773bf7bb77f942606887');

			$this->setConsumer_key($consumerKey);
			$this->setConsumer_secret($consumerSecret);

			//Código de autorização do cliente
			$this->setCode($store['Z001_Code']);
			$this->setStore($store['Z001_Store']);
			$this->setDb($store['Z001_Db']);
			$this->setExport_url($store['Z001_Export_URL']);

			//Dados de acesso aos dados do cliente
			$this->setAccess_token($store['Z001_Access_Token']);
			$this->setRefresh_token($store['Z001_Refresh_Token']);
			$this->setAccess_token_expiration($store['Z001_Access_Token_Expiration']);
			$this->setRefresh_token_expiration($store['Z001_Refresh_Token_Expiration']);

			if($this->getUpdate_data()){
				//Atualiza os dados de acesso ao cliente
				$update = "UPDATE Z001 SET ";
				$update .="Z001_Access_Token = '{$this->getAccess_token()}', ";
				$update .="Z001_Refresh_Token = '{$this->getRefresh_token()}', ";
				$update .="Z001_Access_Token_Expiration = '{$this->getAccess_token_expiration()}', ";
				$update .="Z001_Refresh_Token_Expiration = '{$this->getRefresh_token_expiration()}' ";
				$update .="WHERE Z001_Store = '{$store['Z001_Store']}'";
				mysql_query($update);
			}
			return true;
		}				
		return false;
	}

	public function obterAutorizacao($update = false)
	{
		global $g;
		$urlApiTray = $g['C031']['UrlApiTray'];

		if($update) {
			//Apenas atualiza o access_token
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "$urlApiTray/auth?refresh_token={$this->getRefresh_token()}");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);
			//echo "Atualizando token de acesso\n";
		} else {
			//Gera um novo access_token
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "$urlApiTray/auth");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
			curl_setopt($ch, CURLOPT_POSTFIELDS, "consumer_key={$this->getConsumer_key()}&consumer_secret={$this->getConsumer_secret()}&code={$this->getCode()}");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);
			//echo "Gerando token de acesso\n";
		}
		$auth = (array) json_decode($result);

		$this->setAccess_token($auth['access_token']);
		$this->setRefresh_token($auth['refresh_token']);
		$this->setAccess_token_expiration($auth['date_expiration_access_token']);
		$this->setRefresh_token_expiration($auth['date_expiration_refresh_token']);
	}

	public function validaAutorizacao()
	{
		$access_token 				   = $this->getAccess_token();
		$refresh_token 				   = $this->getRefresh_token();
		$date_expiration_access_token  = $this->getAccess_token_expiration();
		$date_expiration_refresh_token = $this->getRefresh_token_expiration();
		//Verifica se o access_token é valido
		// * Para verificar o prazo de validade do access_token utilizamos a data hora atual mais 5 minutos para eventuais delays de comunicação
		if(empty($access_token) || strtotime(date("Y-m-d H:i:s", strtotime("+5 minutes"))) > strtotime($date_expiration_access_token)) {
			//Se o refresh_token estiver preenchido e com válidade, apenas atualizamos o access_token
			if(!empty($refresh_token) && strtotime(date("Y-m-d H:i:s")) > strtotime($date_expiration_refresh_token)) {
				$this->obterAutorizacao(true);
			} else {
				//Senão geramos o access_token nov
				$this->obterAutorizacao();
			}
			$this->setUpdate_data(true);
		}
	}

	public function listarPedidos()
	{
		global $g;

		set_time_limit(0);

		$this->validaAutorizacao();

		/*	[availableFilters] => Array
	     *  (
	     *      [0] => id
	     *      [1] => status
	     *      [2] => partner_id
	     *      [3] => session_id
	     *      [4] => sending_code
	     *      [5] => modified
	     *      [6] => date
	     *      [7] => shipment_date
	     *  )
	     */
		$urlApiTray = $g['C031']['UrlApiTray'];
		$StatusPedidoImportar = $g['C031']['StatusPedidoTray'];
		$return = '';
		$countLinhasQuebra = 0;
		$totalPedidos = '';

        for ($i = 1; $i <= 3 ; $i++) {
			$ch = curl_init();
			$urlGet = "$urlApiTray/orders?access_token={$this->getAccess_token()}&sort=date_desc&status=$StatusPedidoImportar&page=$i";
			curl_setopt($ch, CURLOPT_URL, $urlGet);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);
			log("paginas de pedidos:".$i);
			log("Url get Pedidos Tray:" . $urlGet);
		    //file_put_contents('/tmp/exportTrayResultPedidos'.$i, $result);
		    //file_put_contents($g['pathDados'] . 'tmp/'.$i, $result);

			log('Retorno get Pedidos Tray: ' . $result);
			$orders = (array) json_decode($result);

			foreach ($orders['Orders'] as $key => $order) {
				$order = (array) $order;
				$order = (array) $order['Order'];
				$totalPedidos .= $order['id'].', ';
				$sqlT005 = mysql_query("SELECT T005_Id FROM T005 WHERE T005_Id_Pedido_Externo = '{$order['id']}' AND T005_C004_Id = '{$g['empresaAtual']}'");
				log("SELECT T005_Id FROM T005 WHERE T005_Id_Pedido_Externo = '{$order['id']}' AND T005_C004_Id = '{$g['empresaAtual']}'");
				$erro = mysql_error();
				if (!empty($erro)){
					log($erro);
					$return .= "<br><b><font color='red'> {$order['id']} -> Erro: {$erro} </b></font><br>";
					$countLinhasQuebra = 0;
					continue;
				}
				if(mysql_num_rows($sqlT005) <= 0){

					if(!empty($order['OrderInvoice'])) {
						$return .= "<font color='red'> {$order['id']} -> Possui dados de NF em tray, </font>";

						$countLinhasQuebra++;
						if ($countLinhasQuebra >= 10) {
							$return .= "<br>";
							$countLinhasQuebra = 0;
						}

						continue;
					}

					if (file_exists('/tmp/exportTrayData'.$order['id'])){
                        $pedido = unserialize(file_get_contents('/tmp/exportTrayData'.$order['id']));
					} else {
						$pedido = $this->buscarPedido($order['id']);
						file_put_contents('/tmp/exportTrayData'.$order['id'], serialize($pedido));
					}
					$T005_Id = $this->importarPedido($pedido,$order['id']);
					if (!$T005_Id){
						$return .= "<br><b><font color='red'> {$order['id']} -> ERRO AO IMPORTAR</b></font><br>";
						$countLinhasQuebra = 0;
					} else {
						$return .= "{$order['id']} -> $T005_Id, ";
						unlink('/tmp/exportTrayData'.$order['id']);
						$this->atualizarStatusPedidoIntegrado($order['id']);

						$countLinhasQuebra++;
						if ($countLinhasQuebra >= 10) {
							$return .= "<br>";
							$countLinhasQuebra = 0;
						}
					}
				} else {
					$this->atualizarStatusPedidoIntegrado($order['id']);
					$return .= "<font color='red'> {$order['id']} -> Já existente, </font>";

					$countLinhasQuebra++;
					if ($countLinhasQuebra >= 10) {
						$return .= "<br>";
						$countLinhasQuebra = 0;
					}
				}
			}

			$paging = (array) $orders['paging'];

			if ($paging['offset'] + $paging['limit'] > $paging['total']) {
				log("<b> SAINDO do LOOP da busca dos pedidos Tray: offset: {$paging['offset']} / limit: {$paging['limit']} / total: {$paging['total']} </b>");
				break;
			}
		}

	    //file_put_contents('/tmp/exportTrayTotalPedidos', $totalPedidos);
		if (empty($return)) {
			$return = 'Não existem pedidos novos para importar no status '.$StatusPedidoImportar;
		}
		return $return;
	}

	function atualizarStatusPedidoIntegrado($T005_Id_Pedido_Externo) {

		global $g;

		$idStatusPedidoTray = $g['C031']['IdStatusPedidoTrayAposImportacao'];

		if (!empty($idStatusPedidoTray)) {

			$idStatusPedidoTray = $idStatusPedidoTray;

			$urlApiTray = $g['C031']['UrlApiTray'];
			$urlPut = "$urlApiTray/orders/{$T005_Id_Pedido_Externo}?access_token={$this->getAccess_token()}";

			$T005 = array();
			$T005["Order"]["status_id"] = $idStatusPedidoTray;

	        //URL QUE MANDA DADOS DO PEDIDO
	        $ch = curl_init($urlPut);
	        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($T005));
	        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	            'Content-Type: application/json',
	            'Content-Length: ' . strlen(json_encode($T005)))
	        );

			log('Atualizando status do pedido na tray. Url: ' . $urlPut);
			log('Atualizando status do pedido na tray. Params: ' . json_encode($T005));

	        curl_setopt($ch, CURLOPT_HEADER, 0);
	        $data = curl_exec($ch);
	        $resposta = (array) json_decode($data);
	        curl_close($ch);
			log('Resposta tray: ' . json_encode($resposta));
		}

	}

	public function buscarPedido($id)
	{
		global $g;
		$urlApiTray = $g['C031']['UrlApiTray'];
		$this->validaAutorizacao();

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "$urlApiTray/orders/{$id}?access_token={$this->getAccess_token()}");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);

		$order = (array) json_decode($result);
		$order = (array) $order['Order'];

		$pedido = array();
		$pedido['cabecalho'] = $order;
		$pedido['cabecalho']['cliente'] = $this->buscarCliente($order['customer_id']);

		//echo "Cliente_Id:{$order['customer_id']} Loja:{$order['point_sale']} Frete:{$order['shipment']} Valor frete:{$order['shipment_value']} Obs:{$order['store_note']}\n";
		//echo "Itens: \n";
		foreach ($order['ProductsSold'] as $key => $item) {
			$item = (array) $item;
			$pedido['itens'][] = $this->buscarProdutoVendido($item['id']);
			//echo "Item_Id:{$item['id']}\n";
		}

		foreach ($order['Payment'] as $key => $pagamento) {
			$pagamento = (array) $pagamento;
			$pedido['pagamento'][] = $this->buscarFormaPagamentoPedido($pagamento['id']);
		}
		return $pedido;
	}

	public function buscarCliente($id)
	{
		global $g;
		$urlApiTray = $g['C031']['UrlApiTray'];
		$this->validaAutorizacao();

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "$urlApiTray/customers/{$id}?access_token={$this->getAccess_token()}");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);

		$customer = (array) json_decode($result);
		$customer = (array) $customer['Customer'];

		$cliente = array();
		$cliente['dadosCliente'] = $customer;

		//echo "Nome:{$customer['name']} CNPJ:{$customer['cnpj']} CPF:{$customer['cpf']} Telefone:{$customer['phone']} Cidade/UF:{$customer['city']}/{$customer['state']}\n";
		foreach ($customer['CustomerAddress'] as $key => $address) {
			$address = (array) $address;
			$cliente['enderecos'][] = $this->buscarEnderecoCliente($address['id']);
		}
		return $cliente;
	}

	public function buscarEnderecoCliente($id)
	{
		global $g;
		$urlApiTray = $g['C031']['UrlApiTray'];
		$this->validaAutorizacao();

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "$urlApiTray/customer_addresses/{$id}?access_token={$this->getAccess_token()}");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);

		$address = (array) json_decode($result);
		$address = (array) $address['CustomerAddress'];
		//echo "Endereço:{$address['address']} Numero:{$address['number']} Bairro:{$address['neighborhood']} CEP:{$address['zip_code']} Cidade/UF:{$address['city']}/{$address['state']}\n";

		return $address;
	}

	public function buscarProdutoVendido($id)
	{
		global $g;
		$urlApiTray = $g['C031']['UrlApiTray'];
		$this->validaAutorizacao();

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "$urlApiTray/products_solds/{$id}?access_token={$this->getAccess_token()}");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);

		$productSold = (array) json_decode($result);
		$productSold = (array) $productSold['ProductsSold'];
		//echo "variant:{$productSold['variant_id']} produto_Id:{$productSold['product_id']} Descricao:{$productSold['name']} Preço:{$productSold['price']} Preço original:{$productSold['original_price']} Peso:{$productSold['weight']} Marca:{$productSold['brand']}\n";

		$produto = $this->buscarProduto($productSold['product_id']);
		$return = array(
			'dadosItem' => $productSold,
			'cadastroProduto' => $produto
		);
		if(!empty($productSold['variant_id'])){
			$return['variacao'] = $this->buscarVariacaoProduto($productSold['variant_id']);
		}
		return $return;
	}

	public function buscarProduto($id)
	{
		global $g;
		$urlApiTray = $g['C031']['UrlApiTray'];
		$this->validaAutorizacao();

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "$urlApiTray/products/{$id}?access_token={$this->getAccess_token()}");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);

		$product = (array) json_decode($result);
		$product = (array) $product['Product'];
		$product['description'] = str_replace(array('\n', '\r'), '', trim($product['description']));
		//echo "Descricao:{$product['name']} Obs:{$product['description']} Preço:{$product['price']} Custo:{$product['cost_price']} EAN:{$product['ean']}\n";

		return $product;
	}

	public function buscarVariacaoProduto($id)
	{
		global $g;
		$urlApiTray = $g['C031']['UrlApiTray'];
		$this->validaAutorizacao();

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "$urlApiTray/variants/{$id}?access_token={$this->getAccess_token()}");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);

		$variant = (array) json_decode($result);
		$variant = (array) $variant['Variant'];
		return $variant;
	}

	public function buscarFormaPagamentoPedido($id)
	{
		global $g;
		$urlApiTray = $g['C031']['UrlApiTray'];
		$this->validaAutorizacao();

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "$urlApiTray/payments/{$id}?access_token={$this->getAccess_token()}");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);

		$payment = (array) json_decode($result);
		$payment = (array) $payment['Payment'];
		return $payment;
	}

	/**
	 * importarPedido
	 * @param $id string
	 * @return bool
	 */
	public function importarPedido($data,$order)
	{
		global $g, $arquivoLogImportacao;

		set_time_limit(0);

		//require('bibliotecas/conexaoBanco.php');

		$urlApiTray = $g['C031']['UrlApiTray'];

		$pedido    = (array) $data;
		$cabecalho = (array) $pedido['cabecalho'];
		$cliente   = (array) $cabecalho['cliente'];

		$id = $cabecalho['id'];

		log("TRAY - importarPedido - inicio - id " . $id);

		if (empty($id)){
			$logImportacao .= "Erro indenficação ID Tray ($order)<br>";
			$id = $order;
			mysqli_query("UPDATE T245 SET T245_Flag_Erro = 'R' WHERE T245_Referencia = 'ID {$id}' AND T245_Tipo_Log = '3'",$g['conexaoBandoAuditoria']);
			registrarLog(array("T005", '0', $g['empresaAtual'], "ID {$id}", "Erro ao importar pedido <b>{$id}</b>, houve um problema ao ler ID no corpo do pedido", $logImportacao, "S", "3"));
			$dataPedido = date("dmY"); //, strtotime($cabecalho['date']));
			$dir = "{$g['pathDados']}/pluggto/{$dataPedido}/";
			is_dir($dir) OR mkdir($dir, 0777, true);
			$arquivoLogImportacao = $dir."pedido_{$id}";
			return false;
		}

		log("TRAY - importarPedido - buscar T005_Id_Pedido_Externo - id " . $id);

		$T005 = mysql_query("select T005_Id from T005 where T005_Id_Pedido_Externo='$id'");
		log("select T005_Id from T005 where T005_Id_Pedido_Externo='$id'");
		log("erro: " . mysql_error());
		$mT005 = mysql_fetch_array($T005);
		if ($mT005['T005_Id']>0){
			$logImportacao .= "Pedido já importado com número {$mT005['T005_Id']}<br>";
			mysqli_query("UPDATE T245 SET T245_Flag_Erro = 'R' WHERE T245_Referencia = 'ID {$id}' AND T245_Tipo_Log = '3'",$g['conexaoBandoAuditoria']);
			registrarLog(array("T005", '0', $g['empresaAtual'], "ID {$id}", "Erro ao importar pedido <b>{$id}</b>, Pedido já importado com número {$mT005['T005_Id']}", $logImportacao, "S", "3"));
			$dataPedido = date("dmY"); //, strtotime($cabecalho['date']));
			$dir = "{$g['pathDados']}/pluggto/{$dataPedido}/";
			is_dir($dir) OR mkdir($dir, 0777, true);
			$arquivoLogImportacao = $dir."pedido_2_{$id}";
			return false;
		}


		$dataPedido = date("dmY"); //, strtotime($cabecalho['date']));
		$dir = "{$g['pathDados']}/pluggto/{$dataPedido}/";
		is_dir($dir) OR mkdir($dir, 0777, true);
		$arquivoLogImportacao = $dir."pedido_{$id}";

		//file_put_contents($arquivoLogImportacao, $data);
		$logImportacao = "Id Pedido Tray {$id}<br>";
		$logImportacao .= "Gravou conteudo do arquivo<br>";

		$T005_Id_Pedido_Externo = $id;
		$T005_Pedido_Ecommerce = $cabecalho['partner_id'];
		$loja = $cabecalho['point_sale'];
		$apelidoLoja = $cabecalho['marketplace_seller_name'];

		if ($g['C031']['FlagTipoFretePedidoTray'] <> '') {
			$T005_Flag_Frete = $g['C031']['FlagTipoFretePedidoTray'];
		} else {
			$T005_Flag_Frete = '1'; //Conta do destinatario
		}

		$logImportacao .= "Transação iniciada<br>";
		//Verifica se o pedido já foi importado
		if(!empty($T005_Id_Pedido_Externo)){
			$sqlT005 = mysql_query("SELECT T005_Id FROM T005 WHERE T005_Id_Pedido_Externo = '{$T005_Id_Pedido_Externo}' AND T005_C004_Id = '{$g['empresaAtual']}'");
			if(mysql_num_rows($sqlT005) > 0){
				//mysql_query("ROLLBACK");
				$rowT005 = mysql_fetch_array($sqlT005);
				file_put_contents($arquivoLogImportacao."_log", "\n[".date("d/m/Y H:i:s")."] Pedido já importado!", FILE_APPEND);
				return false;
			}
		}

		log("TRAY - importarPedido - buscar cliente - id " . $id);

		//Localiza ou pré cadastra o cliente
		$logImportacao .= "Procurando/cadastrando cliente<br>";
		$D024 = $this->procuraCadastroCliente($cliente);
		if(!$D024[0]){
			//mysql_query("ROLLBACK");
			file_put_contents($arquivoLogImportacao."_log", "\n[".date("d/m/Y H:i:s")."] Erro ao localizar/pré-cadastrar cliente", FILE_APPEND);
			$D024[1]=mysql_real_escape_string($D024[1]);
			$logImportacao .= "Erro ao localizar/pré-cadastrar cliente (".$D024[1].")<br>";
			mysqli_query("UPDATE T245 SET T245_Flag_Erro = 'R' WHERE T245_Referencia = 'ID {$id}' AND T245_Tipo_Log = '3'",$g['conexaoBandoAuditoria']);
			registrarLog(array("T005", '0', $g['empresaAtual'], "ID {$id}", "Erro ao importar pedido <b>{$id}</b>, houve um problema ao localizar/pré-cadastrar cliente", $logImportacao, "S", "3"));
			return false;
		}
		$D024_Id = $D024[0];
		$D013_Id = $D024[1];
		$D148_Id = $D024[2];
		$logImportacao .= "Cliente encontrado (Id: {$D024_Id})<br>";
		/*
		 * Insere o cabeçalho do pedido
		 *
		 * external  total 						Total
		 * subtotal  					Subtotal
		 * shipping  					Shipping Costs
		 * discount  					Discont
		 * created 						Data/hora criação (Não consta na documentação da API)
         */

		$totalParcial = mysql_real_escape_string(trim($cabecalho['partial_total']));
		$vlDesconto   = mysql_real_escape_string(trim($cabecalho['discount']));
		$vlFrete      = mysql_real_escape_string(trim($cabecalho['shipment_value']));
		$totalProduto = $totalParcial - $vlDesconto;
		$totalPedido  = $totalProduto + $vlFrete;

		$T005_D024_Id = $D024_Id;
		$T005_D013_Id = $D013_Id;
		$T005_D148_Id_Entrega = $D148_Id;
		$T005_C004_Id = $g['empresaAtual'];
		$T005_Data_Emissao = mysql_real_escape_string(trim($cabecalho['date']));
		$T005_Data_Emissao = date("Y-m-d");
		$T005_Hora_Emissao = mysql_real_escape_string(trim($cabecalho['hour']));
		$T005_Data_Entrega = mysql_real_escape_string(trim($cabecalho['delivered']));
		$T005_Flag_ACP = '6';
		$T005_Valor_Desconto = 0;
		$T005_Valor_Total_Produtos = $totalProduto;
		$T005_Valor_Total_Produtos_Sem_Desconto = $totalProduto;
		$T005_Percentual_Desconto               = 0;
		$T005_Valor_Total = $totalPedido;
		$T005_Valor_Frete = $vlFrete;
		$T005_Flag_Status = $g['C031']['StatusPedidoImportadoTray'];
		$T005_Flag_Lista_Preco = '1';
		$T005_Flag_Destino_Produto = '3';
		$T005_Observacao_Nota_Fiscal = mysql_real_escape_string($loja." ".$T005_Pedido_Ecommerce);
		$T005_Flag_Primeira_Liberacao_Credito = '2';
        $totalizarPeso = ($g['C031']['totalizarPesoVenda'] == 'S') ? 'S' : 'N';
        $T005_Flag_Plataforma_Ecommerce = 2;
		$T005_Observacao_Comercial = '';

		if ($T005_Valor_Desconto > 0) {
			$T005_Percentual_Desconto = (1-($T005_Valor_Total_Produtos / $T005_Valor_Total_Produtos_Sem_Desconto))* 100;
		}

        //Formato de como pegar os dados do marketplaceOrder
		$marketplaceOrders = $cabecalho['MarketplaceOrder'];
		$marketplaceName = null;
		$marketplaceSellerName = null;
		foreach($marketplaceOrders as $marketplaceOrderFor){
			$marketplaceName = $marketplaceOrderFor->marketplace_name;
			$marketplaceSellerName = $marketplaceOrderFor->marketplace_seller_name;
		}

		$T005_C007_Id_Vendedor_Interno = $this->buscarIdVendedorPedido($cabecalho['point_sale'], $marketplaceName, $marketplaceSellerName);
		$T005_D022_Id = $this->buscarIdTransportadoraPedido($cabecalho['shipment']);

		if($T005_C007_Id_Vendedor_Interno == $g['usuarioAtual']){
			$T005_Observacao_Comercial = "{$cabecalho['point_sale']} / {$marketplaceName} / {$marketplaceSellerName}";
		}

		log("TRAY - importarPedido - insert T005 - id " . $id);

		mysql_query("START TRANSACTION");

		$insert = "INSERT INTO T005 (";
		$insert.= "T005_D024_Id, ";
		$insert.= "T005_D013_Id, ";
		$insert.= "T005_C004_Id, ";
		$insert.= "T005_C007_Id_Vendedor_Interno, ";
        $insert.= "T005_D036_Id, ";
		$insert.= "T005_D022_Id, ";
		$insert.= "T005_D148_Id_Entrega, ";
		$insert.= "T005_Data_Emissao, ";
		$insert.= "T005_Hora_Emissao, ";
		$insert.= "T005_Data_Entrega, ";
		$insert.= "T005_Pedido_Ecommerce, ";
		$insert.= "T005_Ordem_Compra, ";
		$insert.= "T005_Id_Pedido_Externo, ";
		$insert.= "T005_Flag_ACP, ";
        $insert.= "T005_Valor_Desconto, ";
        $insert.= "T005_Percentual_Desconto, ";
        $insert.= "T005_Valor_Total_Produtos, ";
        $insert.= "T005_Valor_Total_Produtos_Sem_Desconto, ";
        $insert.= "T005_Valor_Total, ";
        $insert.= "T005_Flag_Status, ";
        $insert.= "T005_Valor_Frete, ";
        $insert.= "T005_Flag_Frete, ";
        $insert.= "T005_Flag_Lista_Preco, ";
        $insert.= "T005_Nome_Status, ";
        $insert.= "T005_Flag_Destino_Produto, ";
        $insert.= "T005_Flag_Primeira_Liberacao_Credito, ";
        $insert.= "T005_Observacao_Nota_Fiscal, ";
		$insert.= "T005_Pgto_Portador, ";
		$insert.= "T005_Pgto_Primeiro_Vcto_Valor, ";
		$insert.= "T005_Flag_Plataforma_Ecommerce, ";
		$insert.= "T005_Canal_Vendas_Ecommerce, ";
		$insert.= "T005_Flag_Totalizar_Peso, ";
		$insert.= "T005_Observacao_Comercial";
        $insert.= ") VALUES (";
        $insert.= "'{$T005_D024_Id}', ";
        $insert.= "'{$T005_D013_Id}', ";
		$insert.= "'{$T005_C004_Id}', ";
		$insert.= "'{$T005_C007_Id_Vendedor_Interno}', ";
		$insert.= "'{$T005_D036_Id}', ";
		$insert.= "'{$T005_D022_Id}', ";
		$insert.= "'{$T005_D148_Id_Entrega}', ";
		$insert.= "'{$T005_Data_Emissao}', ";
		$insert.= "'{$T005_Hora_Emissao}', ";
		$insert.= "'{$T005_Data_Entrega}', ";
		$insert.= "'{$T005_Pedido_Ecommerce}', ";
		$insert.= "'{$T005_Pedido_Ecommerce}', ";
		$insert.= "'{$T005_Id_Pedido_Externo}', ";
		$insert.= "'{$T005_Flag_ACP}', ";
        $insert.= "'{$T005_Valor_Desconto}', ";
        $insert.= "'{$T005_Percentual_Desconto}', ";
        $insert.= "'{$T005_Valor_Total_Produtos}', ";
        $insert.= "'{$T005_Valor_Total_Produtos_Sem_Desconto}', ";
        $insert.= "'{$T005_Valor_Total}', ";
        $insert.= "'{$T005_Flag_Status}', ";
        $insert.= "'{$T005_Valor_Frete}', ";
        $insert.= "'{$T005_Flag_Frete}', ";
        $insert.= "'{$T005_Flag_Lista_Preco}', ";
        $insert.= "T005_Status_Pedido(T005_Flag_Status, 1), ";
        $insert.= "'{$T005_Flag_Destino_Produto}', ";
        $insert.= "'{$T005_Flag_Primeira_Liberacao_Credito}', ";
        $insert.= "'{$T005_Observacao_Nota_Fiscal}', ";
        $insert.= "'{$T005_Pgto_Portador}', ";
		$insert.= "'{$T005_Pgto_Primeiro_Vcto_Valor}', ";
		$insert.= "'{$T005_Flag_Plataforma_Ecommerce}', ";
		$insert.= "'{$loja}', ";
  		$insert.= "'{$totalizarPeso}',";
		$insert .= "'{$T005_Observacao_Comercial}'";
        $insert.= ")";

        mysql_query($insert);
        //echo $insert."\n\n\n";
        $logImportacao .= "Gravando cabeçalho do pedido<br>";
		$erro = mysql_error();
		if(!empty($erro)){
			mysql_query("ROLLBACK");
			file_put_contents($arquivoLogImportacao."_log", "\n[".date("d/m/Y H:i:s")."] Erro ao gravar cabeçalho do pedido", FILE_APPEND);
			$logImportacao .= "Erro ao gravar cabeçalho do pedido<br>Erro: {$erro}<br>";
			mysqli_query("UPDATE T245 SET T245_Flag_Erro = 'R' WHERE T245_Referencia = 'ID {$id}' AND T245_Tipo_Log = '3'",$g['conexaoBandoAuditoria']);
			registrarLog(array("T005", '0', $g['empresaAtual'], "ID {$id}", "Erro ao importar pedido <b>{$id}</b>, houve um problema ao gravar cabeçalho do pedido", $logImportacao, "S", "3"));
			return false;
		}

		$T005_Id = $g['mysqlLastId'];

		// FAZ VINCULO NA TABELA ESTENDIDA
		mysqli_query("INSERT INTO T005A (T005A_T005_Id) VALUES ('{$T005_Id}')");

		/*
		 * Insere os produtos
		 */

		 log("TRAY - importarPedido - incluindo produtos - id " . $id);

		$count = 0;
		$logImportacao .= "Iniciando inclusão dos itens do pedido<br>";

		$itemUnico = count($pedido['itens']) == 1;

		foreach ($pedido['itens'] as $key => $item) {
			$count++;
			$item 	   = (array) $item;
			$dadosItem = (array) $item['dadosItem'];
			$D009_Id = $this->procuraCadastroProduto($item);

/*			if(!$D009_Id){
				$dadosItem['name'] = mysql_real_escape_string(strip_tags($dadosItem['name']));
				mysql_query("ROLLBACK");
				file_put_contents($arquivoLogImportacao."_log", "\n[".date("d/m/Y H:i:s")."] Erro ao localizar/pré-cadastrar produto (Item: {$count})", FILE_APPEND);
				$logImportacao .= "Erro ao localizar/pré-cadastrar produto (Item {$count} - Descrição: {$dadosItem['name']})<br>";
				mysqli_query("UPDATE T245 SET T245_Flag_Erro = 'R' WHERE T245_Referencia = 'ID {$id}' AND T245_Tipo_Log = '3'",$g['conexaoBandoAuditoria']);
				registrarLog(array("T005", '0', $g['empresaAtual'], "ID {$id}", "Erro ao importar pedido <b>{$id}</b>, houve um problema ao localizar/pré-cadastrar produto (Item {$count} - Descrição: {$dadosItem['name']})", $logImportacao, "S", "3"));
				return false;
			} */

			$sqlD001 = mysql_query("SELECT D001_Codigo_Produto, D001_Descricao_Produto, D001_D005_Id, D001_D037_Id FROM D001 LEFT JOIN D049 ON D049_D001_Id=D001_Id LEFT JOIN D009 ON D009_D049_Id=D049_Id WHERE D009_Id = '{$D009_Id}'");
			$rowD001 = mysql_fetch_assoc($sqlD001);
			if ($D009_Id<=0){
				$cadastroProduto = (array) $item['cadastroProduto'];
				$D001_Codigo_Produto = '';
				$D001_Descricao_Produto = strip_tags($dadosItem['name']); //A descrição do item na venda contem os detalhes da variação
				$D001_Especificacoes 	= strip_tags($cadastroProduto['description']);
				$D001_Codigo_Barras 	= $cadastroProduto['ean'];
				$reference			 	= $cadastroProduto['reference'];
				$D001_Peso_Unitario_Kg  = $cadastroProduto['weight'];
				if(!empty($item['variacao'])){
					$variacao = (array) $item['variacao'];
					$D001_Codigo_Barras 	= (!empty($variacao['ean'])) ? $variacao['ean'] : $D001_Codigo_Barras;
					$reference			 	= (!empty($variacao['reference'])) ? $variacao['reference'] : $reference;
					$D001_Peso_Unitario_Kg 	= $variacao['weight'];
				}
				if (!empty($reference)){
					$rowD001['D001_Codigo_Produto'] = mysql_real_escape_string(strtoupper(trim($reference)));
				} else {
					$rowD001['D001_Codigo_Produto'] = mysql_real_escape_string(strtoupper(trim($D001_Codigo_Barras)));
				}
				$rowD001['D001_Descricao_Produto'] = mysql_real_escape_string(strtoupper(trim($D001_Descricao_Produto)));
			} else {
				$rowD001['D001_Codigo_Produto'] = mysql_real_escape_string(trim($rowD001['D001_Codigo_Produto']));
				$rowD001['D001_Descricao_Produto'] = mysql_real_escape_string(strtoupper(trim($rowD001['D001_Descricao_Produto'])));
			}

			require_once("bibliotecas/classes/VEN001.php");
        	$VEN001 = new VEN001();
        	$D006_Id = $VEN001->retornaCFOP($D024_Id, $D009_Id, $g['empresaAtual']);
        	$demonstrativoCFOP = $VEN001->retornaCFOP($D024_Id, $D009_Id, $g['empresaAtual'], false, false, true);
        	$demonstrativoCFOP = mysql_real_escape_string($demonstrativoCFOP);

			$T006_Percentual_Desconto               = 0;
			$T006_Percentual_Desconto_Bruto         = 0;
			$T006_Valor_Preco_Original              = $dadosItem['price'];
			$T006_Valor_Preco_Original_Bruto        = $dadosItem['price'];
			$T006_Valor_Total_Preco_Sem_Desconto    = $dadosItem['price'];
			$T006_Valor_Preco_Unitario              = $dadosItem['price'];
			$T006_Valor_Preco_Sem_Desconto_Unitario = $dadosItem['price'];

			if (mysql_real_escape_string(trim($cabecalho['discount'])) > 0) {
				if ($itemUnico) {
					$ValorDesconto = mysql_real_escape_string(trim($cabecalho['discount']));
				} else {
					$PercentualRateio = (($dadosItem['price'] * 100) / $cabecalho['partial_total']) / 100;
					$ValorDesconto    = ($cabecalho['discount'] * $PercentualRateio);
	
					log('PercentualRateio itens: ' . $PercentualRateio);
				}
				log('ValorDesconto itens: '    . $ValorDesconto);

				$valorItem = $dadosItem['price'] - ($ValorDesconto / $dadosItem['quantity']);

				$T006_Valor_Preco_Original              = $valorItem;
				$T006_Valor_Preco_Original_Bruto        = $valorItem;
				$T006_Valor_Total_Preco_Sem_Desconto    = $valorItem;
				$T006_Valor_Preco_Unitario              = $valorItem;
				$T006_Valor_Preco_Sem_Desconto_Unitario = $valorItem;
			}

			if (mysql_real_escape_string(trim($cabecalho['payment_method_rate'])) > 0) {
				$T005_Percentual_Acrescimo = ((mysql_real_escape_string(trim($cabecalho['total'])) / mysql_real_escape_string(trim($cabecalho['partial_total'])))-1);
				$ValorAcrescimo = ($dadosItem['price'] * $T005_Percentual_Acrescimo);

				log('T005_Percentual_Acrescimo itens: ' . $T005_Percentual_Acrescimo);
				log('ValorAcrescimo itens: ' . $ValorAcrescimo);

				$T006_Valor_Preco_Original              = $dadosItem['price'] + $ValorAcrescimo;
				$T006_Valor_Preco_Original_Bruto        = $dadosItem['price'] + $ValorAcrescimo;
				$T006_Valor_Total_Preco_Sem_Desconto    = $dadosItem['price'] + $ValorAcrescimo;
				$T006_Valor_Preco_Unitario              = $dadosItem['price'] + $ValorAcrescimo;
				$T006_Valor_Preco_Sem_Desconto_Unitario = $dadosItem['price'] + $ValorAcrescimo;
			}

			$insert = "INSERT INTO T006 (";
			$insert.= "T006_T005_Id, ";
			$insert.= "T006_D006_Id, ";
			$insert.= "T006_D006_Id_Info, ";
			$insert.= "T006_D009_Id, ";
			$insert.= "T006_C004_Id, ";
			$insert.= "T006_D037_Id, ";
			$insert.= "T006_D005_Id, ";
			$insert.= "T006_Item, ";
			$insert.= "T006_Numero_Pedido_Compra, ";
			$insert.= "T006_Codigo_Produto, ";
			$insert.= "T006_Descricao_Produto, ";
			$insert.= "T006_Quantidade, ";
			$insert.= "T006_Valor_Preco_Original, ";
			$insert.= "T006_Valor_Preco_Original_Bruto, ";
			$insert.= "T006_Valor_Preco_Unitario, ";
			$insert.= "T006_Valor_Preco_Sem_Desconto_Unitario, ";
			$insert.= "T006_Valor_Total_Preco_Sem_Desconto, ";
			$insert.= "T006_Percentual_Desconto, ";
			$insert.= "T006_Percentual_Desconto_Bruto, ";
			$insert.= "T006_Valor_Desconto_Unitario, ";
			$insert.= "T006_Flag_Destino_Produto";
			$insert.= ") VALUES (";
			$insert.= "'{$T005_Id}', ";
			$insert.= "'{$D006_Id}', ";
			$insert.= "'{$demonstrativoCFOP}', ";
			$insert.= "'{$D009_Id}', ";
			$insert.= "'{$g['empresaAtual']}', ";
			$insert.= "'{$rowD001['D001_D037_Id']}', ";
			$insert.= "'{$rowD001['D001_D005_Id']}', ";
			$insert.= "'{$count}', ";
			$insert.= "'{$T005_Pedido_Ecommerce}', ";
			$insert.= "'{$rowD001['D001_Codigo_Produto']}', ";
			$insert.= "'{$rowD001['D001_Descricao_Produto']}', ";
			$insert.= "'{$dadosItem['quantity']}', ";
			$insert.= "'{$T006_Valor_Preco_Original}', ";
			$insert.= "'{$T006_Valor_Preco_Original_Bruto}', ";
			$insert.= "'{$T006_Valor_Preco_Unitario}', ";
			$insert.= "'{$T006_Valor_Preco_Sem_Desconto_Unitario}', ";
			$insert.= "'{$T006_Valor_Total_Preco_Sem_Desconto}', ";
			$insert.= "'{$T006_Percentual_Desconto}', ";
			$insert.= "'{$T006_Percentual_Desconto_Bruto}', ";
			$insert.= "'{$T006_Valor_Desconto_Unitario}', ";
			$insert.= "'3'";
			$insert.= ")";
			mysql_query($insert);

			log('insert itens: ' . $insert);
			//echo $insert."\n\n\n";
			$logImportacao .= "Gravando item do pedido (Item {$count} - D009_Id: {$D009_Id})<br>";
			$erro = mysql_error();
			if(!empty($erro)){
				$dadosItem['name'] = mysql_real_escape_string(strip_tags($dadosItem['name']));
				mysql_query("ROLLBACK");
				file_put_contents($arquivoLogImportacao."_log", "\n[".date("d/m/Y H:i:s")."] Erro ao gravar item do pedido (Item: {$count})", FILE_APPEND);
				$logImportacao .= "Erro ao gravar item do pedido (Item {$count} - Descrição: {$dadosItem['name']})<br>Erro: {$erro}<br>";
				mysqli_query("UPDATE T245 SET T245_Flag_Erro = 'R' WHERE T245_Referencia = 'ID {$id}' AND T245_Tipo_Log = '3'",$g['conexaoBandoAuditoria']);
				registrarLog(array("T005", '0', $g['empresaAtual'], "ID {$id}", "Erro ao importar pedido <b>{$id}</b>, houve um problema ao gravar item do pedido (Item {$count} - Descrição: {$dadosItem['name']})", $logImportacao, "S", "3"));
				return false;
			}
			$T006_Id = $g['mysqlLastId'];


        	require_once("bibliotecas/classes/CAD002.php");
        	$CAD002 = new CAD002();
        	$atualizacaoEstoque = $CAD002->D001_reprocessa_historico(0, $D009_Id, 50);
			if($atualizacaoEstoque !== true){
				$logImportacao .= "Ouve um erro na atualizacao de estoque D009_Id {$D009_Id}: {$atualizacaoEstoque}<br>";
			} else {
				$logImportacao .= "Atualizou estoque D009_Id {$D009_Id}<br>";
			}
		}
		$logImportacao .= "Atualizando alíquotas e CFOP do pedido<br>";
    	require_once("bibliotecas/classes/VEN002.php");
    	$VEN002 = new VEN002();
		$VEN002->ven002atualizarCFOP($T005_Id);
		$VEN002->atualizarAliquotasPedidos($T005_Id);
		if($g['C031']['atualizaPrecoTabelaAoImportarPedido'] == 'S'){
			$VEN002->atualizarPrecoTabela($T005_Id);
		}

		/*
		 * Forma de pagamento
		 *
		 * Payment.id			Id da forma de pagamento
		 * Payment.order_id		Id do pedido
		 * Payment.method		Portador
		 * Payment.value		Valor da parcela
		 * Payment.date			Data do portador
		 */
		$logImportacao .= "Inserindo forma de pagamento do pedido<br>";
		$parcela = 0;
		foreach($pedido['pagamento'] as $key => $pagamento){
			$D027_Id = 0;
			$parcela++;
			$pagamento = (array) $pagamento;
			$pagamento['method'] = mysql_real_escape_string(trim($pagamento['method']));
			$pagamento['value']  = mysql_real_escape_string(trim($pagamento['value']));
			$pagamento['date'] 	 = mysql_real_escape_string(trim($pagamento['date']));

			$sqlD027 = mysql_query("SELECT D027_Id FROM D027 WHERE D027_Portador = '{$pagamento['method']}'");
			if(mysql_num_rows($sqlD027) > 0){
				$rowD027 = mysql_fetch_array($sqlD027);
				$D027_Id = $rowD027['D027_Id'];
			} elseif (!empty($g['C031']['IdD027PedidoTray'])) {
				$D027_Id = $g['C031']['IdD027PedidoTray'];
			}

			mysql_query("INSERT INTO T090 (T090_T005_Id, T090_D027_Id, T090_Numero_Parcela, T090_Valor_Parcela, T090_Data_Vencimento, T090_Observacao) VALUES ('{$T005_Id}', '{$D027_Id}', '{$parcela}', '{$pagamento['value']}', '{$pagamento['date']}', '{$pagamento['method']}')");
			$erro = mysql_error();
			if(!empty($erro)){
				$dadosItem['name'] = mysql_real_escape_string(strip_tags($dadosItem['name']));
				mysql_query("ROLLBACK");
				file_put_contents($arquivoLogImportacao."_log", "\n[".date("d/m/Y H:i:s")."] Erro ao gravar forma de pagamento do pedido (Parcela: {$parcela})", FILE_APPEND);
				$logImportacao .= "Erro ao gravar forma de pagamento do pedido (Parcela: {$parcela})<br>Erro: {$erro}<br>";
				mysqli_query("UPDATE T245 SET T245_Flag_Erro = 'R' WHERE T245_Referencia = 'ID {$id}' AND T245_Tipo_Log = '3'",$g['conexaoBandoAuditoria']);
				registrarLog(array("T005", '0', $g['empresaAtual'], "ID {$id}", "Erro ao importar pedido <b>{$id}</b>, houve um problema ao gravar forma de pagamento do pedido (Parcela: {$parcela})", $logImportacao, "S", "3"));
				return false;
			}
		}

        $logImportacao .= "Finalizando a importação do pedido<br>";
		mysql_query("COMMIT");
		mysql_query("UNLOCK ALL");
		$logImportacao .= "Transação finalizada";
		registrarLog(array("T005", $T005_Id, $g['empresaAtual'], "ID {$id}", "Importação do pedido realizada com sucesso", $logImportacao, "N", "3"));
		mysqli_query("UPDATE T245 SET T245_Flag_Erro = 'R' WHERE T245_Referencia = 'ID {$id}' AND T245_Tipo_Log = '3'",$g['conexaoBandoAuditoria']);
		return $T005_Id;
	}

	/**
	 * procuraCadastroCliente
	 * @param $cliente array
	 * @return string
	 */
	public function procuraCadastroCliente($cliente)
	{
		global $g;
		$urlApiTray = $g['C031']['UrlApiTray'];
		$dadosCliente = (array) $cliente['dadosCliente'];
		set_time_limit(0);

		$D024_Id = false;
		$D024_Cnpj = str_replace(array('.', '/', '-'), '', trim($dadosCliente['cnpj']));
		$D024_Cpf = str_replace(array('.', '/', '-'), '', trim($dadosCliente['cpf']));

		if(!empty($dadosCliente['cnpj'])){ //CNPJ
			$sqlD024 = mysql_query("SELECT D024_Id FROM D024 WHERE REPLACE(REPLACE(REPLACE(TRIM(D024_Cnpj), '/', ''), '-', ''), '.', '') = '{$D024_Cnpj}'");
			if(mysql_num_rows($sqlD024) >= 1){
				$rowD024 = mysql_fetch_assoc($sqlD024);
				$D024_Id = $rowD024['D024_Id'];
			}
		}

		if(!empty($dadosCliente['cpf'])){ //CPF
			$sqlD024 = mysql_query("SELECT D024_Id FROM D024 WHERE REPLACE(REPLACE(REPLACE(TRIM(D024_Cpf), '/', ''), '-', ''), '.', '') = '{$D024_Cpf}'");
			log("SELECT D024_Id FROM D024 WHERE REPLACE(REPLACE(REPLACE(TRIM(D024_Cpf), '/', ''), '-', ''), '.', '') = '{$D024_Cpf}'");
            log('Erro: ' . mysql_error());
			if(mysql_num_rows($sqlD024) >= 1){
				$rowD024 = mysql_fetch_assoc($sqlD024);
				$D024_Id = $rowD024['D024_Id'];
				log("D024_Id  = " . $D024_Id);
			}
		}

		/*
		* Se não localizou o cliente faz o pré cadastro, senão atualiza os dados do cliente
		*
		* Customer.cnpj							CNPJ do Cliente
		* Customer.id							Código do Cliente
		* Customer.name							Nome do CLiente
		* Customer.registration_date			Data de Cadastro
		* Customer.rg							RG do Cliente
		* Customer.cpf							CPF do Cliente
		* Customer.phone						Telefône do Cliente
		* Customer.cellphone					Celular do Cliente
		* Customer.birth_date					Aniversário do Cliente
		* Customer.gender						Sexo do Cliente
		* Customer.email						Email do Cliente
		* Customer.nickname						Apelido do Cliente
		* Customer.token						Chave do Cliente
		* Customer.total_orders					Total de Pedidos
		* Customer.observation					Observação
		* Customer.type							Tipo de Cliente
		* Customer.company_name					Nome da Empresa
		* Customer.state_inscription			Inscrição Estatual
		* Customer.reseller						Código do Revendedor
		* Customer.discount						Valor de Desconto
		* Customer.blocked						Conta Bloqueada
		* Customer.credit_limit					Limite de Crédito
		* Customer.indicator_id					Código Indicador
		* Customer.profile_customer_id			Código do Perfil do Cliente
		* Customer.last_sending_newsletter		Envido do Último Email
		* Customer.last_purchase				Data da Última Compra
		* Customer.last_visit					Data da Última Visita
		* Customer.last_modification			Data da Última Atualização
		* Customer.city							Cidade do Cliente
		* Customer.state						Estado do Cliente
		* Customer.newsletter					Envido de Email de Novidades
		*/

		//Localiza o país
		$sqlD030 = mysql_query("SELECT D030_Id FROM D030 WHERE D030_Nome_Pais = 'BRASIL'");
		if(mysql_num_rows($sqlD030) == 1){
			$rowD030 = mysql_fetch_array($sqlD030);
			$D024_D030_Id = $rowD030['D030_Id'];
		}

		//Localiza o estado
		$D018_Estado = mysql_real_escape_string(trim($dadosCliente['state']));
		$sqlD018 = mysql_query("SELECT D018_Id FROM D018 WHERE D018_Estado = '{$D018_Estado}' OR D018_UF = '{$D018_Estado}'");
		if(mysql_num_rows($sqlD018) == 1){
			$rowD018 = mysql_fetch_array($sqlD018);
			$D024_D018_Id = $rowD018['D018_Id'];
		}

		//Localiza a cidade
		$D020_Nome_Cidade = mysql_real_escape_string(trim($dadosCliente['city']));
		$sqlD020 = mysql_query("SELECT D020_Id, D020_D018_Id FROM D020 WHERE D020_Nome_Cidade = '{$D020_Nome_Cidade}'");
		if(mysql_num_rows($sqlD020) == 1){
			$rowD020 = mysql_fetch_array($sqlD020);
			$D024_D020_Id = $rowD020['D020_Id'];
			$D024_D018_Id = (empty($D024_D018_Id)) ? $rowD020['D020_D018_Id'] : $D024_D018_Id;
		} else if(mysql_num_rows($sqlD020) > 1 && $D024_D018_Id > 0){
			//Localizou mais de uma cidade, verifica pelo estado também
			while($rowD020 = mysql_fetch_assoc($sqlD020)){
				if($rowD020['D020_D018_Id'] == $D024_D018_Id){
					$D024_D020_Id = $rowD020['D020_Id'];
					break;
				}
			}
		}

		if (empty($dadosCliente['name'])){
			return array(false,'Dados do cliente em branco');
		}

		$D024_Inscricao_Estadual 	  = mysql_real_escape_string(trim($dadosCliente['state_inscription']));
		$D024_Endereco 			 	  = mysql_real_escape_string(trim(strtoupper($dadosCliente['address'])));
		$D024_Numero 			 	  = mysql_real_escape_string(trim($dadosCliente['number']));
		$D024_Cep 				 	  = mysql_real_escape_string(trim($dadosCliente['zip_code']));
		$D024_Complemento 		 	  = mysql_real_escape_string(trim($dadosCliente['complement']));
		$D024_Bairro 			 	  = mysql_real_escape_string(trim(strtoupper($dadosCliente['neighborhood'])));
		$D024_D020_Id 			 	  = mysql_real_escape_string(trim($D024_D020_Id));
		$D024_D018_Id 			 	  = mysql_real_escape_string(trim($D024_D018_Id));
		$D024_D030_Id 			 	  = mysql_real_escape_string(trim($D024_D030_Id));
		$D024_DDD_Telefone_1 	 	  = '';
		$D024_Telefone_1 		 	  = mysql_real_escape_string(trim($dadosCliente['phone']));
		$D024_DDD_Telefone_2 	 	  = '';
		$D024_Telefone_2 		 	  = mysql_real_escape_string(trim($dadosCliente['cellphone']));
		$D024_E_Mail 			 	  = mysql_real_escape_string(trim($dadosCliente['email']));

		//Tratamento do telefone - Há casos que o DDD não vem no campo correto
		$D024_DDD_Telefone_1 = str_replace(array('(',')'), '', $D024_DDD_Telefone_1);
		$D024_Telefone_1     = str_replace(array('(',')'), '', $D024_Telefone_1);
		if(substr($D024_Telefone_1,0,1) == '0'){ //Remove o zero do inicio se houver
			$D024_Telefone_1 = substr($D024_Telefone_1, 1);
		}
		if(empty($D024_DDD_Telefone_1)){ //Pega o DDD caso não venha em campo específico
			if(strpos($D024_Telefone_1, ' ') !== false) {
				$telefone = explode(' ', $D024_Telefone_1);
				if(is_array($telefone)){
					$D024_DDD_Telefone_1 = $telefone[0];
					$D024_Telefone_1 = $telefone[1];
				}
			} else if(strpos($D024_Telefone_1, '-') !== false) {
				$telefone = explode('-', $D024_Telefone_1);
				if(is_array($telefone)){
					$D024_DDD_Telefone_1 = $telefone[0];
					$D024_Telefone_1 = $telefone[1];
				}
			} else if(strpos($D024_Telefone_1, '_') !== false){
				$telefone = explode('_', $D024_Telefone_1);
				if(is_array($telefone)){
					$D024_DDD_Telefone_1 = $telefone[0];
					$D024_Telefone_1 = $telefone[1];
				}
			}
			if(empty($D024_DDD_Telefone_1)){
				$D024_DDD_Telefone_1 = substr($D024_Telefone_1, 0, 2);
				$D024_Telefone_1     = substr($D024_Telefone_1, 2);
			}
		}

		$D024_DDD_Telefone_2 = str_replace(array('(',')'), '', $D024_DDD_Telefone_2);
		$D024_Telefone_2     = str_replace(array('(',')'), '', $D024_Telefone_2);
		if(substr($D024_Telefone_2,0,1) == '0'){ //Remove o zero do inicio se houver
			$D024_Telefone_2 = substr($D024_Telefone_2, 1);
		}
		if(empty($D024_DDD_Telefone_2) && !empty($D024_Telefone_2)){ //Pega o DDD caso não venha em campo específico
			if(strpos($D024_Telefone_2, ' ') !== false) {
				$telefone = explode(' ', $D024_Telefone_2);
				if(is_array($telefone)){
					$D024_DDD_Telefone_2 = $telefone[0];
					$D024_Telefone_2 = $telefone[1];
				}
			} else if(strpos($D024_Telefone_2, '-') !== false) {
				$telefone = explode('-', $D024_Telefone_2);
				if(is_array($telefone)){
					$D024_DDD_Telefone_2 = $telefone[0];
					$D024_Telefone_2 = $telefone[1];
				}
			} else if(strpos($D024_Telefone_2, '_') !== false){
				$telefone = explode('_', $D024_Telefone_2);
				if(is_array($telefone)){
					$D024_DDD_Telefone_2 = $telefone[0];
					$D024_Telefone_2 = $telefone[1];
				}
			}
			if(empty($D024_DDD_Telefone_2)){
				$D024_DDD_Telefone_2 = substr($D024_Telefone_2, 0, 2);
				$D024_Telefone_2     = substr($D024_Telefone_2, 2);
			}
		}

		//Não localizou o cliente
		if(!$D024_Id) {
			if(!empty($D024_Cpf)){
				$D024_Flag_Pessoa_Fisica_Juridica = 'F';
				$D024_Inscricao_Estadual = '';
				$D024_Flag_Contribuinte = '0';
				$D024_Cpf = substr($D024_Cpf,0,3).'.'.substr($D024_Cpf,3,3).'.'.substr($D024_Cpf,6,3).'-'.substr($D024_Cpf,9,2);
			} else if(!empty($D024_Cnpj)){
				$D024_Flag_Pessoa_Fisica_Juridica = 'J';

				if (empty($D024_Inscricao_Estadual) || strtoupper($D024_Inscricao_Estadual) == "ISENTO") {
					$D024_Flag_Contribuinte = '0';
				} else {
					$D024_Flag_Contribuinte = '5';
				}

				$D024_Cnpj = substr($D024_Cnpj,0,2).'.'.substr($D024_Cnpj,2,3).'.'.substr($D024_Cnpj,5,3).'/'.substr($D024_Cnpj,8,4).'-'.substr($D024_Cnpj,12,2);
			}
			$D024_Nome_Empresa  = $dadosCliente['name'];
			$D024_Nome_Fantasia = $dadosCliente['name'];
			$D024_Apelido 		= $dadosCliente['nickname'];

			$D024_Nome_Empresa 		 	  = mysql_real_escape_string(trim(strtoupper($D024_Nome_Empresa)));
			$D024_Nome_Fantasia 	 	  = mysql_real_escape_string(trim(strtoupper($D024_Nome_Fantasia)));
			$D024_Apelido 	 	  		  = mysql_real_escape_string(trim(strtoupper($D024_Apelido)));
			$D024_Cpf 				 	  = mysql_real_escape_string($D024_Cpf);
			$D024_Cnpj 				 	  = mysql_real_escape_string($D024_Cnpj);
			$D024_Codigo_Regime_Tributario = '1';
			$D024_C007_Vendedor_Interno = '0';
			$D024_Flag_Liberado = 'N';
			$D024_Data_Fundacao = mysql_real_escape_string($dadosCliente['birth_date']);

			$insert = "INSERT INTO D024 (";
			$insert.= "D024_Cpf, ";
			$insert.= "D024_Cnpj, ";
			$insert.= "D024_Flag_Pessoa_Fisica_Juridica, ";
			$insert.= "D024_Inscricao_Estadual, ";
			$insert.= "D024_Nome_Empresa, ";
			$insert.= "D024_Nome_Fantasia, ";
			$insert.= "D024_Apelido, ";
			$insert.= "D024_Endereco, ";
			$insert.= "D024_Numero, ";
			$insert.= "D024_Cep, ";
			$insert.= "D024_Complemento, ";
			$insert.= "D024_Bairro, ";
			$insert.= "D024_D020_Id, ";
			$insert.= "D024_D018_Id, ";
			$insert.= "D024_D030_Id, ";
			$insert.= "D024_DDD_Telefone_1, ";
			$insert.= "D024_Telefone_1, ";
			$insert.= "D024_DDD_Telefone_2, ";
			$insert.= "D024_Telefone_2, ";
			$insert.= "D024_E_Mail, ";
			$insert.= "D024_Flag_Cliente, ";
			$insert.= "D024_Flag_Fornecedor, ";
			$insert.= "D024_Flag_Pre_Cadastro, ";
			$insert.= "D024_Flag_Permite_ACP, ";
			$insert.= "D024_Data_Cadastro, ";
			$insert.= "D024_Flag_Contribuinte, ";
			$insert.= "D024_Flag_Liberado, ";
			$insert.= "D024_C007_Vendedor_Interno, ";
			$insert.= "D024_Codigo_Regime_Tributario, ";
			$insert.= "D024_Data_Fundacao";
			$insert.= ") VALUES (";
			$insert.= "'{$D024_Cpf}', ";
			$insert.= "'{$D024_Cnpj}', ";
			$insert.= "'{$D024_Flag_Pessoa_Fisica_Juridica}', ";
			$insert.= "'{$D024_Inscricao_Estadual}', ";
			$insert.= "'{$D024_Nome_Empresa}', ";
			$insert.= "'{$D024_Nome_Fantasia}', ";
			$insert.= "'{$D024_Apelido}', ";
			$insert.= "'{$D024_Endereco}', ";
			$insert.= "'{$D024_Numero}', ";
			$insert.= "'{$D024_Cep}', ";
			$insert.= "'{$D024_Complemento}', ";
			$insert.= "'{$D024_Bairro}', ";
			$insert.= "'{$D024_D020_Id}', ";
			$insert.= "'{$D024_D018_Id}', ";
			$insert.= "'{$D024_D030_Id}', ";
			$insert.= "'{$D024_DDD_Telefone_1}', ";
			$insert.= "'{$D024_Telefone_1}', ";
			$insert.= "'{$D024_DDD_Telefone_2}', ";
			$insert.= "'{$D024_Telefone_2}', ";
			$insert.= "'{$D024_E_Mail}', ";
			$insert.= "'S', ";
			$insert.= "'N', ";
			$insert.= "'N', ";
			$insert.= "'S', ";
			$insert.= "CURDATE(), ";
			$insert.= "'{$D024_Flag_Contribuinte}', ";
			$insert.= "'{$D024_Flag_Liberado}', ";
			$insert.= "'{$D024_C007_Vendedor_Interno}', ";
			$insert.= "'{$D024_Codigo_Regime_Tributario}', ";
			$insert.= "'{$D024_Data_Fundacao}'";
			$insert.= ")";

			mysql_query($insert);
			//echo $insert."\n\n\n";
			$erro = mysql_error();
			if(!empty($erro)){
				log($insert);
				log('Erro inclusao cliente ' . $erro);
				return array(false,'Erro inclusão cliente'.$erro);
			}
			$D024_Id = $g['mysqlLastId'];

			//Cadastra um contato
			mysql_query("INSERT INTO D013 (D013_Nome_Contato, D013_E_Mail, D013_DDD_Telefone_1, D013_Telefone_1, D013_D024_Id) VALUES ('{$D024_Nome_Empresa}', '{$D024_E_Mail}', '{$D024_DDD_Telefone_1}', '{$D024_Telefone_1}', '{$D024_Id}')");
			$D013_Id = $g['mysqlLastId'];

		} else { //Localizou o cliente, mas vai atualizar os dados

			$sqlD024 = mysql_query("SELECT * FROM D024 WHERE D024_Id = '{$D024_Id}'");
			$rowD024 = mysql_fetch_assoc($sqlD024);
			//Pega o primeiro contato
			$sqlD013 = mysql_query("SELECT * FROM D013 WHERE D013_D024_Id = '{$D024_Id}' LIMIT 1");
			$rowD013 = mysql_fetch_assoc($sqlD013);
			$D013_Id = $rowD013['D013_Id'];

			//Os campos só serão atualizados caso esteja vazio no banco ou diferente dos dados que estão preenchidos no pedido
			$countUpdate = 0;
			$update = "UPDATE D024 SET ";
			if((empty($rowD024['D024_Inscricao_Estadual']) && !empty($D024_Inscricao_Estadual)) || (!empty($D024_Inscricao_Estadual) && $rowD024['D024_Inscricao_Estadual'] != $D024_Inscricao_Estadual)) {
				$update .= "D024_Inscricao_Estadual = '{$D024_Inscricao_Estadual}', ";
				$countUpdate++;
			}
			if((empty($rowD024['D024_Endereco']) && !empty($D024_Endereco)) || (!empty($D024_Endereco) && $rowD024['D024_Endereco'] != $D024_Endereco)) {
				$update .= "D024_Endereco = '{$D024_Endereco}', ";
				$countUpdate++;
			}
			if((empty($rowD024['D024_Numero']) && !empty($D024_Numero)) || (!empty($D024_Numero) && $rowD024['D024_Numero'] != $D024_Numero)) {
				$update .= "D024_Numero = '{$D024_Numero}', ";
				$countUpdate++;
			}
			if((empty($rowD024['D024_Cep']) && !empty($D024_Cep)) || (!empty($D024_Cep) && $rowD024['D024_Cep'] != $D024_Cep)) {
				$update .= "D024_Cep = '{$D024_Cep}', ";
				$countUpdate++;
			}
			if((empty($rowD024['D024_Complemento']) && !empty($D024_Complemento)) || (!empty($D024_Complemento) && $rowD024['D024_Complemento'] != $D024_Complemento)) {
				$update .= "D024_Complemento = '{$D024_Complemento}', ";
				$countUpdate++;
			}
			if((empty($rowD024['D024_Bairro']) && !empty($D024_Bairro)) || (!empty($D024_Bairro) && $rowD024['D024_Bairro'] != $D024_Bairro)) {
				$update .= "D024_Bairro = '{$D024_Bairro}', ";
				$countUpdate++;
			}
			if((empty($rowD024['D024_D020_Id']) && !empty($D024_D020_Id)) || (!empty($D024_D020_Id) && $rowD024['D024_D020_Id'] != $D024_D020_Id)) {
				$update .= "D024_D020_Id = '{$D024_D020_Id}', ";
				$countUpdate++;
			}
			if((empty($rowD024['D024_D018_Id']) && !empty($D024_D018_Id)) || (!empty($D024_D018_Id) && $rowD024['D024_D018_Id'] != $D024_D018_Id)) {
				$update .= "D024_D018_Id = '{$D024_D018_Id}', ";
				$countUpdate++;
			}
			if((empty($rowD024['D024_D030_Id']) && !empty($D024_D030_Id)) || (!empty($D024_D030_Id) && $rowD024['D024_D030_Id'] != $D024_D030_Id)) {
				$update .= "D024_D030_Id = '{$D024_D030_Id}', ";
				$countUpdate++;
			}
			if((empty($rowD024['D024_DDD_Telefone_1']) && !empty($D024_DDD_Telefone_1)) || (!empty($D024_DDD_Telefone_1) && $rowD024['D024_DDD_Telefone_1'] != $D024_DDD_Telefone_1)) {
				$update .= "D024_DDD_Telefone_1 = '{$D024_DDD_Telefone_1}', ";
				$countUpdate++;
			}
			if((empty($rowD024['D024_Telefone_1']) && !empty($D024_Telefone_1)) || (!empty($D024_Telefone_1) && $rowD024['D024_Telefone_1'] != $D024_Telefone_1)) {
				$update .= "D024_Telefone_1 = '{$D024_Telefone_1}', ";
				$countUpdate++;
			}
			if((empty($rowD024['D024_DDD_Telefone_2']) && !empty($D024_DDD_Telefone_2)) || (!empty($D024_DDD_Telefone_2) && $rowD024['D024_DDD_Telefone_2'] != $D024_DDD_Telefone_2)) {
				$update .= "D024_DDD_Telefone_2 = '{$D024_DDD_Telefone_2}', ";
				$countUpdate++;
			}
			if((empty($rowD024['D024_Telefone_2']) && !empty($D024_Telefone_2)) || (!empty($D024_Telefone_2) && $rowD024['D024_Telefone_2'] != $D024_Telefone_2)) {
				$update .= "D024_Telefone_2 = '{$D024_Telefone_2}', ";
				$countUpdate++;
			}
			if((empty($rowD024['D024_E_Mail']) && !empty($D024_E_Mail)) || (!empty($D024_E_Mail) && $rowD024['D024_E_Mail'] != $D024_E_Mail)) {
				$update .= "D024_E_Mail = '{$D024_E_Mail}', ";
				$countUpdate++;
			}
			$update = substr($update, 0, -2);
			$update .= " WHERE D024_Id = '{$D024_Id}'";
			if($countUpdate > 0){
				mysql_query($update);
				$erro = mysql_error();
				if(!empty($erro)){
					return array(false,'Erro atualização cadastro cliente '.$erro);
				}
			}
		}


		/* Cadastra o endereço de entrega se não existir
		 *
		 * CustomerAddress.id				CNPJ do Cliente
		 * CustomerAddress.customer_id		Código do Comprador
		 * CustomerAddress.address			Endereço do Comprador
		 * CustomerAddress.number			Número do Endereço
		 * CustomerAddress.complement		Complemento
		 * CustomerAddress.neighborhood		Bairro
		 * CustomerAddress.city				Cidade
		 * CustomerAddress.state			Estado
		 * CustomerAddress.zip_code			CEP
		 * CustomerAddress.country			País
		 * CustomerAddress.type				Tipo de Endereço - Tabela Auxiliar - Tipo de Endereço
		 * CustomerAddress.active			Endereço Ativo
		 * CustomerAddress.description		Descrição do Endereço
		 * CustomerAddress.recipient		Destinatário do Endereço
		 * CustomerAddress.type_delivery	Tipo de Envio
		 * CustomerAddress.not_list			Endereço Visível
		 */

		foreach($cliente['enderecos'] as $key => $endereco) {
			$endereco = (array) $endereco;

			//Localiza o país
			$D030_Nome_Pais = trim($pedido['receiver_country']);
			$sqlD030 = mysql_query("SELECT D030_Id FROM D030 WHERE D030_Nome_Pais = 'BRASIL'");
			if(mysql_num_rows($sqlD030) == 1){
				$rowD030 = mysql_fetch_array($sqlD030);
				$D148_D030_Id = $rowD030['D030_Id'];
			}

			//Localiza o estado
			$D018_Estado = trim($endereco['state']);
			$sqlD018 = mysql_query("SELECT D018_Id FROM D018 WHERE D018_Estado = '{$D018_Estado}' OR D018_UF = '{$D018_Estado}'");
			if(mysql_num_rows($sqlD018) == 1){
				$rowD018 = mysql_fetch_array($sqlD018);
				$D148_D018_Id = $rowD018['D018_Id'];
			}

			//Localiza a cidade
			$D020_Nome_Cidade = trim($endereco['city']);
			$sqlD020 = mysql_query("SELECT D020_Id, D020_D018_Id FROM D020 WHERE D020_Nome_Cidade = '{$D020_Nome_Cidade}'");
			if(mysql_num_rows($sqlD020) == 1){
				$rowD020 = mysql_fetch_array($sqlD020);
				$D148_D020_Id = $rowD020['D020_Id'];
				$D148_D018_Id = $rowD020['D020_D018_Id'];
			} else if(mysql_num_rows($sqlD020) > 1 && $D148_D018_Id > 0){
				//Localizou mais de uma cidade, verifica pelo estado também
				while($rowD020 = mysql_fetch_assoc($sqlD020)){
					if($rowD020['D020_D018_Id'] == $D148_D018_Id){
						$D148_D020_Id = $rowD020['D020_Id'];
						break;
					}
				}
			}

		    $D148_D024_Id 		= $D024_Id;
		    $D148_D018_Id		= mysql_real_escape_string(trim($D148_D018_Id));
		    $D148_D020_Id		= mysql_real_escape_string(trim($D148_D020_Id));
		    $D148_D030_Id		= mysql_real_escape_string(trim($D148_D030_Id));
		    $D148_Nome			= mysql_real_escape_string(trim(strtoupper($dadosCliente['name'].' '.$endereco['description'])));
		    $D148_Cep			= mysql_real_escape_string(trim($endereco['zip_code']));
		    $D148_Rua			= mysql_real_escape_string(trim(strtoupper($endereco['address'])));
		    $D148_Numero		= mysql_real_escape_string(trim($endereco['number']));
		    $D148_Complemento	= mysql_real_escape_string(trim(strtoupper($endereco['complement'])));
		    $D148_Bairro		= mysql_real_escape_string(trim(strtoupper($endereco['neighborhood'])));
		    $D148_Email			= mysql_real_escape_string(trim($endereco['receiver_email']));
		    $D148_Flag_Tipo 	= ($endereco['type'] == '1') ? 'E' : 'C';

		    //Verifica se o endereço já está cadastrado
		    $D148_Id = 0;
		    $sqlD148 = mysql_query("SELECT * FROM D148 WHERE D148_D024_Id = '{$D024_Id}' AND D148_Cep = '{$D148_Cep}' AND D148_Numero = '{$D148_Numero}' AND D148_Flag_Tipo = '{$D148_Flag_Tipo}'");
		    if(mysql_fetch_assoc($sqlD148) <= 0){
		    	//Não localizando, cadastramos o novo endereço
		    	$insert = "INSERT INTO D148 (";
		    	$insert.= "D148_D024_Id, ";
				$insert.= "D148_D018_Id, ";
				$insert.= "D148_D020_Id, ";
				$insert.= "D148_D030_Id, ";
				$insert.= "D148_Nome, ";
				$insert.= "D148_Cep, ";
				$insert.= "D148_Rua, ";
				$insert.= "D148_Numero, ";
				$insert.= "D148_Complemento, ";
				$insert.= "D148_Bairro, ";
				$insert.= "D148_Flag_Tipo ";
				$insert.= ") VALUES (";
				$insert.= "'{$D148_D024_Id}', ";
				$insert.= "'{$D148_D018_Id}', ";
				$insert.= "'{$D148_D020_Id}', ";
				$insert.= "'{$D148_D030_Id}', ";
				$insert.= "'{$D148_Nome}', ";
				$insert.= "'{$D148_Cep}', ";
				$insert.= "'{$D148_Rua}', ";
				$insert.= "'{$D148_Numero}', ";
				$insert.= "'{$D148_Complemento}', ";
				$insert.= "'{$D148_Bairro}', ";
				$insert.= "'{$D148_Flag_Tipo}'";
				$insert.= ")";
				mysql_query($insert);
				//echo $insert."\n\n\n";
				$erro = mysql_error();
				if(!empty($erro)){
					return array(false,'Erro inclusão endereço cliente '.$erro);
				}

				if($D024_Cep != $D148_Cep){
					$D148_Id = $g['mysqlLastId'];
				}
			}
	    }

	    return array($D024_Id, $D013_Id, $D148_Id);
	}

	/**
	 * buscarIdVendedorPedido
	 * @param $T005_Canal_Vendas_Ecommerce string
	 * @return int
	 */
	public function buscarIdVendedorPedido($pointSale, $marketplaceName, $marketplaceSellerName)
	{
		global $g;
		$T005_C007_Id_Vendedor_Interno = $g['usuarioAtual'];

		if (!empty($marketplaceSellerName)) {
			$sqlC007 = " SELECT C007_Id 
			             FROM C007 
						 WHERE C007_Nome_Vendedor_Ecommerce = '{$marketplaceSellerName}' ";

			$qryC007 = mysql_query($sqlC007);
			while ($rowC007 = mysql_fetch_assoc($qryC007)) {
				return $rowC007['C007_Id'];
			}
		}

		if (!empty($marketplaceName)) {
			$marketplaceName = strtoupper($marketplaceName);

			$sqlC007 = " SELECT C007_Id 
			             FROM C007 
						 WHERE UPPER(C007_Nome_Vendedor_Ecommerce) = '{$marketplaceName}' ";

				$qryC007 = mysql_query($sqlC007);
				while ($rowC007 = mysql_fetch_assoc($qryC007)) {
					return $rowC007['C007_Id'];
				}
		}

		if (!empty($pointSale)) {
			$sqlC007 = " SELECT C007_Id 
			             FROM C007 
						 WHERE C007_Nome_Vendedor_Ecommerce = '{$pointSale}' ";

			$qryC007 = mysql_query($sqlC007);
			while ($rowC007 = mysql_fetch_assoc($qryC007)) {
				return $rowC007['C007_Id'];
			}
		}

		return $g['usuarioAtual'];
	}

		/**
	 * buscarIdTransportadoraPedido
	 * @param $T005_Canal_Vendas_Ecommerce string
	 * @return int
	 */
	public function buscarIdTransportadoraPedido($D022_Descricao_Ecommerce) {

		global $g;

		if (!empty($D022_Descricao_Ecommerce)) {

			$sqlD022 = " select D022_Id
			             from D022
						 where D022_Descricao_Ecommerce = {$D022_Descricao_Ecommerce} ";

			$qryD022 = mysql_query($sqlD022);
			while ($rowD022 = mysql_fetch_assoc($qryD022)) {
				return $rowD022['D022_Id'];
			}
		}

		if ($T005_D022_Id == 0 && !empty($g['C031']['IdD022PedidoTray'])) {
			return $g['C031']['IdD022PedidoTray'];
		}

		return 0;
	}
	

	public function buscarVendedorPedido($T005_Canal_Vendas_Ecommerce)
	{
		global $g;
		$T005_C007_Id_Vendedor_Interno = $g['usuarioAtual'];

		if (!empty($T005_Canal_Vendas_Ecommerce)) {
			$sqlC007 = " SELECT C007_Id 
			             FROM C007 
						 WHERE C007_Nome_Vendedor_Ecommerce = '{$T005_Canal_Vendas_Ecommerce}' ";

			$qryC007 = mysql_query($sqlC007);
			while ($rowC007 = mysql_fetch_assoc($qryC007)) {
				$T005_C007_Id_Vendedor_Interno = $rowC007['C007_Id'];
			}
		}

		return $T005_C007_Id_Vendedor_Interno;
	}

	/**
	 * procuraCadastroProduto
	 * @param $item array
	 * @return string
	 */
	public function procuraCadastroProduto($item)
	{
		global $g;
		$urlApiTray = $g['C031']['UrlApiTray'];
		$dadosItem 		 = (array) $item['dadosItem'];
		$cadastroProduto = (array) $item['cadastroProduto'];
		require_once("bibliotecas/classes/CAD002.php");
		/*
		 * ProductsSold.id						Código do Produto Vendido
		 * ProductsSold.product_id				Código do Produto
		 * ProductsSold.order_id				Código do Pedido
		 * ProductsSold.name					Nome do Produto
		 * ProductsSold.price					Preço do Produto Vendido
		 * ProductsSold.original_price			Preço Original do Produto
		 * ProductsSold.weight					Peso do Produto
		 * ProductsSold.weight_cubic			Peso Cúbico do Produto
		 * ProductsSold.quantity				Quantidade de Produto Vendido
		 * ProductsSold.brand					Marca do Produto
		 * ProductsSold.model					Modelo do Produto
		 * ProductsSold.reference				Referência do Produto
		 * ProductsSold.length					Comprimento do Produto
		 * ProductsSold.width					Largura do Produto
		 * ProductsSold.height					Altura do Produto
		 * ProductsSold.variant_id				Código de Variação do Produto
		 * ProductsSold.additional_information	Informações Adicionais
		 *
		 * Products.ean							Código do produto,
		 * Products.modified					Última data de modificação do produto,
		 * Products.id							ID do produto,
		 * Products.name						Título do produto,
		 * Products.description					Descrição completa do produto,
		 * Products.description_small			Descrição curta do produto,
		 * Products.price						Preço atual do produto,
		 * Products.promotional_price			Preço de promoção do produto,
		 * Products.start_promotion				Data inicial da promoção,
		 * Products.end_promotion				Data final da promoção,
		 * Products.brand						Marca do produto,
		 * Products.weight						Peso do produto,
		 * Products.length						Comprimento do produto,
		 * Products.width						Largura do produto,
		 * Products.height						Altura do produto,
		 * Products.stock						Estoque do produto,
		 * Products.category_id					Categoria do produto,
		 * Products.available					Disponpibilidade do produto,
		 * Products.reference					Código de referencia do produto,
		 * Products.productImage				Array contendo listagem de imagens do produto,
		 * Products.url							Array com URL amigável do produto, segura e não segura.,
		 * Products.Variant						Lista de id's das variações do produto,
		 * Products.created 					Data de cadastro do produto
		 *
		 * Variants.id 							ID da variação,
		 * Variants.ean 						Código ean,
		 * Variants.product_id 					ID do produto pai da variação,
		 * Variants.price 						preço da variação,
		 * Variants.stock 						Estoque da variação,
		 * Variants.reference 					Código de referencia da variação,
		 * Variants.weight 						Peso,
		 * Variants.length 						comprimento,
		 * Variants.width 						largura,
		 * Variants.height 						Altura,
		 * Variants.start_promotion 			Data de início da promoção,
		 * Variants.end_promotion 				Data de fim da promoção,
		 * Variants.promotional_price 			Preço de promoção,
		 * Variants.VariantImage 				Array de imagens da variação,
		 * Variants.Sku 						Tipo e Valor da variação
		 */

		$dadosItem['name'] = str_replace(array('<strong>', '<b>'), ' ', $dadosItem['name']);
		$D001_Id_Ecommerce = trim($dadosItem['id']);

		$variantId = $dadosItem['variant_id'];

		$D001_Codigo_Produto = '';
		$D001_Descricao_Produto = strip_tags($dadosItem['name']); //A descrição do item na venda contem os detalhes da variação
		$D001_Especificacoes 	= strip_tags($cadastroProduto['description']);
		$D001_Codigo_Barras 	= $cadastroProduto['ean'];
		$referencia         	= $cadastroProduto['reference'];
		$D001_Peso_Unitario_Kg  = $cadastroProduto['weight'];
		if(!empty($item['variacao'])){
			$variacao = (array) $item['variacao'];
			$D001_Codigo_Barras 	= (!empty($variacao['ean'])) ? $variacao['ean'] : $D001_Codigo_Barras;
			$referencia     	 	= (!empty($variacao['reference'])) ? $variacao['reference'] : $referencia;
			$D001_Peso_Unitario_Kg 	= $variacao['weight'];
		}
		if(empty($D001_Codigo_Barras)){
			$D001_Codigo_Barras = $referencia;
		}

		if (!empty($variantId)) {
			$sqlD001 = mysql_query(" SELECT D001_Id FROM D001 JOIN D186 ON D001_Id = D186_D001_Id WHERE D186_Integracao_ID = '{$variantId}' ");
		}

		if ($g['C031']['FlagBuscarProdPedidoCodBarrasTray'] == "S") {

			if (mysql_num_rows($sqlD001) <= 0) {
				$sqlD001 = mysql_query("SELECT D001_Id FROM D001 WHERE D001_Codigo_Barras = '{$D001_Codigo_Barras}'");
			}
		
			if(mysql_num_rows($sqlD001) <= 0 && !empty($referencia)){
				$D001_Codigo_Barras 	= $referencia;
				$sqlD001 = mysql_query("SELECT D001_Id FROM D001 WHERE D001_Codigo_Barras = '{$D001_Codigo_Barras}'");
			}
		}

		if(mysql_num_rows($sqlD001) <= 0) {

			$productId = $dadosItem['product_id'];

			$sqlD186 = " SELECT COUNT(D186_Id) AS QTDE_D186 
			             FROM D186 
			             JOIN D185 ON D186_D185_Id = D185_Id AND D185_Integracao_ID = {$productId} ";

			$rowD186 = mysql_fetch_assoc(mysql_query($sqlD186));

			$Qtde_D186 = $rowD186['QTDE_D186'];

			if ($Qtde_D186 == 1) {
				$query = " SELECT D001_Id 
						   FROM D001 
						   JOIN D186 ON D001_Id = D186_D001_Id 
						   JOIN D185 ON D186_D185_Id = D185_Id AND D185_Integracao_ID = {$productId} ";

				$sqlD001 = mysql_query($query);
			}
		}
		
		$D001_Descricao_Produto = mysql_real_escape_string(strtoupper(trim($D001_Descricao_Produto)));
		$D001_Especificacoes 	= mysql_real_escape_string(strtoupper(trim($D001_Especificacoes)));

		//Verifica se o produto existe no sistema
		//$sqlD001 = mysql_query("SELECT D001_Id FROM D001 WHERE D001_Id_Ecommerce = '{$D001_Id_Ecommerce}' OR D001_Codigo_Barras = '{$D001_Codigo_Barras}'");
		if(!empty($D001_Codigo_Barras) && mysql_num_rows($sqlD001) > 0){
			$rowD001 = mysql_fetch_assoc($sqlD001);
			$sqlD009 = mysql_query("SELECT D009_Id FROM D009 LEFT JOIN D049 ON D049_Id=D009_D049_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id WHERE D001_Id = '{$rowD001['D001_Id']}' AND D009_C004_Id = '{$g['empresaAtual']}'");
			$erro = mysql_error();
			if(!empty($erro)){
				return false;
			}
			$rowD009 = mysql_fetch_assoc($sqlD009);
			return $rowD009['D009_Id'];
		} else {
/*			$CAD002 = new CAD002();
			if(empty($D001_Codigo_Barras)){
				$D001_Codigo_Barras = $CAD002->obterNovoCodigoEAN();
			}

			//O produto não foi encontrado, vamos pré cadastrar
			$insert = "INSERT INTO D001 (";
			$insert.= "D001_Codigo_Produto, ";
			$insert.= "D001_Codigo_Barras, ";
			$insert.= "D001_Id_Ecommerce, ";
            $insert.= "D001_Flag_Pre_Cadastro, ";
            $insert.= "D001_Flag_Ativo, ";
            $insert.= "D001_Descricao_Produto, ";
            $insert.= "D001_Peso_Unitario_Kg, ";
            $insert.= "D001_Especificacoes ";
            $insert.= ") VALUES (";
            $insert.= "'{$D001_Codigo_Produto}', ";
            $insert.= "'{$D001_Codigo_Barras}', ";
            $insert.= "'{$D001_Id_Ecommerce}', ";
            $insert.= "'S', ";
            $insert.= "'S', ";
            $insert.= "'{$D001_Descricao_Produto}', ";
            $insert.= "'{$D001_Peso_Unitario_Kg}', ";
            $insert.= "'{$D001_Especificacoes}' ";
            $insert.= ")";
            mysql_query($insert);
            //echo $insert."\n\n\n";
			$erro = mysql_error();
			if(!empty($erro)){
				file_put_contents('/tmp/errointegracao', $erro);
				return false;
			}
			$D001_Id = $g['mysqlLastId'];

			if($g['C031']['gravarCodProdAoCadastrar'] == 'S'){
				require_once("bibliotecas/classes/CAD002.php");
				$CAD002 = new CAD002();
				$proxCodigo = $CAD002->obterNovoCodigoProduto($D001_Id);
				mysql_query("UPDATE D001 SET D001_Codigo_Produto = '{$proxCodigo}' WHERE D001_Id = '{$D001_Id}'");
			}

			$D082_Marca = mysql_real_escape_string(strtoupper(trim($cadastroProduto['brand'])));
			$sqlD082 	= mysql_query("SELECT * FROM D082 WHERE D082_Marca = '{$D082_Marca}'");
			if(mysql_num_rows($sqlD082) > 0){
				$rowD082 = mysql_fetch_array($sqlD082);
				$D082_Id = $rowD082['D082_Id'];
			} else {
				mysql_query("INSERT INTO D082 (D082_Marca) VALUES ('{$D082_Marca}')");
				$erro = mysql_error();
				if(!empty($erro)){
					file_put_contents('/tmp/errointegracao', $erro);
					return false;
				}
				$D082_Id = $g['mysqlLastId'];
			}

			mysql_query("INSERT INTO D049 (D049_D001_Id, D049_D082_Id, D049_Data_Cadastro, D049_Flag_Tipo) VALUES ('{$D001_Id}', '{$D082_Id}', CURDATE(), '');");
        	$D049_Id = $g['mysqlLastId'];
        	return $CAD002->preCadastroMarca($D049_Id, $D001_Id); 
        	*/
        	return 0;
		}
	}

	public function atualizarPedidoTray($T005_Id){
        global $g,$confUsuario;
		$urlApiTray = $g['C031']['UrlApiTray'];
		$this->validaAutorizacao();
        $T005 = array();
        $result = "";
 
        $sqlT005 = mysql_query("SELECT  T005_Id_Pedido_Externo,
                                        T005_Flag_Status,
                                        T005_Valor_Frete,
                                        T178_Data,
                                        T268_Codigo_Rastreio,
                                        D036_Tipo_Transporte 
                                  FROM  T005 
                                  LEFT JOIN T007 ON T007_T005_Id=T005_Id
                                  LEFT JOIN T268 ON T268_T007_Id=T007_Id
                                  LEFT JOIN D036 ON T005_D036_Id=D036_Id 
                                  LEFT JOIN T178 ON T178_T005_Id=T005_Id AND T178_Descricao = 'Coletado'
                                  WHERE T005_Id={$T005_Id}");
        $rT005 = mysql_fetch_array($sqlT005);

        if (empty($rT005['T005_Id_Pedido_Externo'])) {
            $result .= "<br>ID Ecommerce está vazio.";
        }else{

        	if($rT005['T005_Flag_Status']<=4){
            	$result.="<br>Pedido não está com status de coletado.";
	        }

	        if(empty($rT005['T268_Codigo_Rastreio'])){
	            $result.="<br>Código de rastreio está em branco.";
	        }

	        if(empty($rT005['T178_Data'])){
	            $result.="<br>Data de coleta do pedido está em branco.";
	        }
        }

        if(empty($result)){

            $T005["Order"]["status_id"] = "";
            $T005["Order"]["taxes"] = " ";
            $T005["Order"]["shipment"] = $rT005['D036_Tipo_Transporte'];
            $T005["Order"]["shipment_value"] = $rT005['T005_Valor_Frete'];
            $T005["Order"]["discount"] = " ";
            $T005["Order"]["sending_code"] = $rT005['T268_Codigo_Rastreio'];
            $T005["Order"]["sending_date"] = $rT005['T178_Data'];
            $T005["Order"]["store_note"] = " ";
            $T005["Order"]["customer_note"] = " ";
            $T005["Order"]["partner_id"] = " ";

	        //URL QUE MANDA DADOS DO PEDIDO
	        $ch = curl_init("{$urlApiTray}/orders/{$rT005['T005_Id_Pedido_Externo']}?access_token={$this->getAccess_token()}");
	        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($T005));
	        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	            'Content-Type: application/json',
	            'Content-Length: ' . strlen(json_encode($T005)))
	        );
	        curl_setopt($ch, CURLOPT_HEADER, 0);
	        $data = curl_exec($ch);
	        // JSON de retorno  
	        $resposta = (array) json_decode($data);
	        curl_close($ch);

	        if($resposta['code'] == "200"){
	        	mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Dados do rastreio enviados com sucesso!','')");

	        	$retorno = enviarDadosFaturamento($T005_Id);
	          
	            if($retorno===true){
	            	mysql_query("UPDATE T005 set T005_Data_Hora_Envio_Ecommerce=CURRENT_TIMESTAMP where T005_Id={$T005_Id}");
	            }else{
	            	return " Erro ao enviar dados, verifique o histórico do pedido.";
	            }

	        }else{
	           mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Erro ao enviar dados de rastreio do pedido!<br>Falha de comunicação com a Tray!<br>Retorno Tray:{$resposta['name']}','')");
	           return " Erro ao enviar dados, verifique o histórico do pedido.!";
	        }

		}else{
			mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'<b>Erro ao atualizar dados do pedido!</b><br><br>Faltam as seguintes informações no seu pedido:<br>{$result}','')");
            return " Erro ao enviar dados, verifique o histórico do pedido.";
        }
        return true;
    }

   	public function enviarDadosFaturamento($T005_Id){
        global $g,$confUsuario;
		$urlApiTray = $g['C031']['UrlApiTray'];
		$this->validaAutorizacao();
        $T007 = array();
        $T008 = array();
        $produtos = array();
        $result = "";

        $sqlT005 = mysql_query("SELECT  T005_Id_Pedido_Externo,
                                        T007_T005_Id,
                                        T007_Data_Emissao,
                                        T007_Numero_Nota_fiscal,
                                        T007_Serie,
                                        T007_Valor_Total,
                                        T007_Chave_Acesso_Nfe,
                                        T007_Arquivo_XML,
                                        T007_Id,
                                        T007_D024_Id
                                        FROM T005 
                                        LEFT JOIN T007 ON T007_T005_Id=T005_Id 
                                        WHERE T005_Id={$T005_Id}");
        $rT005 = mysql_fetch_array($sqlT005);

        if(empty($rT005['T007_Id'])){
            return "Não há NFe vinculada ao pedido.<br/>";
        }else{
            
            $Query = md5($rT005['T007_D024_Id'].'|'.$rT005['T007_Id']);
            $link = "{$confUsuario['urlRaiz']}/hardness3/hardness/danfe/imprimir_danfe.php?Query={$Query}&vlrNF=S&T007_Id={$rT005['T007_Id']}";

            if (empty($rT005['T005_Id_Pedido_Externo'])) {
                $result .= "ID Ecommerce está vazio.<br/>";
            }else if (empty($rT005['T007_Numero_Nota_fiscal'])) {
                $result .= "Numero da NFe está em branco<br/>";
            }else{

                if (empty($rT005['T007_Chave_Acesso_Nfe'])) {        
                    $result .= "Chave de acesso da NFe:".$rT005['T007_Numero_Nota_fiscal']." está em branco<br/>";
                }

                if (empty($rT005['T007_Data_Emissao'])) {
                    $result .= "Data de emissão NFe:".$rT005['T007_Numero_Nota_fiscal']." está em branco<br/>";
                }

                if (empty($link)) {
                    $result .= "Erro ao gerar link da Nfe:".$rT005['T007_Numero_Nota_fiscal']."<br/>";
                }

                if (empty($rT005['T007_Arquivo_XML'])) {
                    $result .= "XML DANFE da Nfe:".$rT005['T007_Numero_Nota_fiscal']." está vazio<br/>";
                }
            }     

            if(empty($result)){

                $sqlT008 = mysql_query("SELECT T008_Codigo_Produto,
                                               D006_Codigo_CFOP 
                                          FROM T008 
                                          LEFT JOIN T007 ON T008_T007_Id=T007_Id 
                                          LEFT JOIN D006 ON T008_D006_Id=D006_Id
                                          WHERE T007_Id='{$rT005['T007_Id']}'");

                while($rT008 = mysql_fetch_array($sqlT008)){

                    $T008["product_id"] = $rT008['T008_Codigo_Produto'];
                    $T008["variation_id"] = "";
                    $T008["cfop"] = $rT008['D006_Codigo_CFOP'];
                 
                    $produtos[] = $T008;
                    
                }

                $T007["issue_date"] = $rT005['T007_Data_Emissao'];
                $T007["number"] = $rT005['T007_Numero_Nota_fiscal'];
                $T007["serie"] = $rT005['T007_Serie'];
                $T007["value"] = $rT005['T007_Valor_Total'];
                $T007["key"] = $rT005['T007_Chave_Acesso_Nfe'];
                $T007["link"] = $link;
                $T007["xml_danfe"] = urlencode($rT005['T007_Arquivo_XML']);
                $T007['ProductCfop'] = $produtos;

	            //URL QUE MANDA DADOS DO FATURAMENTO
	            $ch = curl_init("{$urlApiTray}/orders/{$rT005['T005_Id_Pedido_Externo']}/invoices?access_token={$this->getAccess_token()}");
	            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($T007));
	            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	                'Content-Type: application/json',
	                'Content-Length: ' . strlen(json_encode($T007)))
	            );
	            curl_setopt($ch, CURLOPT_HEADER, 0);
	            $data = curl_exec($ch);
	            // JSON de retorno  
	            $resposta = (array) json_decode($data);
	            curl_close($ch);

	            if($resposta['code'] == "200"){
	                mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Dados de faturamento enviados com sucesso!','')");
	            }else{
	               mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Erro ao enviar dados de faturamento! <br>Falha de comunicação com a Tray!<br>Retorno Tray:{$resposta['name']}','')");
	               return false;
	            }
	        }else{
	        	mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Erro ao enviar dados de faturamento do pedido!<br>Faltam as seguintes informações no seu pedido:{$result}','')");
	            return false;
	        }
       
        }
        return true;
    }


    public function listarProdutos($pagina){
    	global $g;        
        $urlApiTray = $g['C031']['UrlApiTray'];
        $this->autenticacaoTrayx(2);
        $this->validaAutorizacao();
      	
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $urlApiTray."/products/?access_token={$this->getAccess_token()}&page=$pagina");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $produtos = (array) json_decode($result);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if($code!='200'){
        	$logImportacao .= "Erro ao buscar produtos na Tray<br>Erro: {$result}<br>";
			mysqli_query("UPDATE T245 SET T245_Flag_Erro = 'R' WHERE T245_Referencia = 'ID' AND T245_Tipo_Log = '3'",$g['conexaoBandoAuditoria']);
			registrarLog(array("Tray", '0', $g['empresaAtual'], "ID ", "Erro ao buscar lista produtos na tray", $logImportacao, "S", "3"));
	       	return 0;
        }else if(!empty($produtos['Products'])){
    		return $produtos;
    	}else{
    		return 1;
    	}
             
    }


	public function atualizarProduto($idProduto,$estoqueSistema,$eanProduto){
        global $g,$confUsuario;
		$urlApiTray = $g['C031']['UrlApiTray'];
		$this->autenticacaoTrayx(2);
		$this->validaAutorizacao();
        $data = array();
       	
        $data['Product']['stock'] = $estoqueSistema;
        ob_start();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $urlApiTray."/products/".$idProduto."?access_token={$this->getAccess_token()}");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($data)))
        );
        curl_exec($ch);
        // JSON de retorno  


	    $result = ob_get_contents();
	    // JSON de retorno  
	    $resposta = json_decode($result);
	    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
             
        ob_end_clean();
    	curl_close($ch);

        if($code=="200"){
            $retorno = true;
        } else {
            $retorno = false;

            $logImportacao = "Falha de comunicação com a tray, erro retornado:<br>".$result;
            mysqli_query("UPDATE T245 SET T245_Flag_Erro = 'R' WHERE T245_Referencia = 'P $eanProduto' AND T245_Tipo_Log = '3'",$g['conexaoBandoAuditoria']);
            registrarLog(array("Tray", '0', $g['empresaAtual'], "ID {$id}", "Erro ao atualizar estoque do produto EAN: {$eanProduto}", $logImportacao, "S", "3"));
        }
        return $retorno;
    }


}
