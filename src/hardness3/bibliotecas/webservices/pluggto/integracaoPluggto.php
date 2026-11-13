<?php
namespace hardness;
/*
 * Classe de integração com a plataforma Plugg.to
 * Documentação: https://plugg.to/api/
 */
Class Pluggto
{
	/**
	 * processarNotificacao
	 * @param $post json
	 * @return bool
	 */
	public function processarNotificacao($post)
	{
		global $g, $arquivoLogImportacao;
		//Verifica se a notificação é referente a criação de uma nova ordem(pedido)
		if(($post['action'] == 'created' || $post['action'] == 'updated') && $post['type'] == 'orders'){
			$this->importarPedido($post['id']);
		} else {
			$logImportacao .= "Action diferente de Created e Updated ou type diferente de Orders (action: {$post['action']} type: {$post['type']})<br>";
			registrarLog(array("T005", '0', $g['empresaAtual'], "ID {$post['id']}", "Action diferente de Created e Updated ou type diferente de Orders (action: {$post['action']} type: {$post['type']})<br>", $logImportacao, "N", "2"));
			return false;
		}
	}

	public function obterAutorizacao()
	{
		global $g;
		/*
		 *	Autenticação
		 *  a autenticação é feita para requisitar o access token, este token é utilizado nas requisições de pedidos (também utilizado para criar, atualizar produtos e pedidos na plataforma)
		 *
		 *  http://api.plugg.to/oauth/token
		 *  client_id - Id do plugin Hardness na plataforma PluggTo
		 *  client_secret - Chave secreta do plugin Hardness na plataforma PluggTo
		 *  username - API Id do cliente na plataforma PluggTo
		 *  password - API Secret do cliente na plataforma PluggTo
		 *  grant_type = password
		 */
	    $urlApi=$g['C031']['UrlApiPluggto'];
	    $client_id =$g['C031']['ClientIdPluggto'];
	    $client_secret=$g['C031']['ClientSecretPluggto'];
	    $username=$g['C031']['UsernamePluggto'];
	    $password=$g['C031']['PasswordPluggto'];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "{$urlApi}/oauth/token");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, "client_id={$client_id}&client_secret={$client_secret}&username={$username}&password={$password}&grant_type=password");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);

		$auth = (array) json_decode($result);
		return $auth['access_token'];
	}

	/**
	 * importarPedido
	 * @param $id string
	 * @return bool
	 */
	public function importarPedido($id)
	{
		global $g, $arquivoLogImportacao;
		$access_token = $this->obterAutorizacao();
		$urlApi=$g['C031']['UrlApiPluggto'];
		/*
		 * Requisição do pedido na plataforma PluggTo
		 */
		$ch = curl_init("{$urlApi}/orders/{$id}?access_token={$access_token}&showExternal=true");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$data = curl_exec($ch);
		curl_close($ch);

		$pedido = (array) json_decode($data);
		$pedido = (array) $pedido['Order'];

		$dataPedido = date("dmY"); //Data emissao no sistema Hardness
		$dir = "{$g['pathDados']}/pluggto/{$dataPedido}/";
		is_dir($dir) OR mkdir($dir, 0777, true);
		$arquivoLogImportacao = $dir."pedido_{$id}";

		file_put_contents($arquivoLogImportacao, $data);
		$logImportacao = "Id PluggTo {$id}<br>";
		$logImportacao .= "Gravou conteudo do arquivo<br>";

		if($pedido['status'] != 'approved'){
			$logImportacao .= "Pedido não aprovado {$pedido['status']}<br>";
			registrarLog(array("T005", '0', $g['empresaAtual'], "ID {$id}", "Pedido não aprovado ({$pedido['status']})", $logImportacao, "N", "2"));
			return false;
		}

		//Converte campo 'external' para array
		$external = (array) $pedido['external'];
		$external = array_values($external);

		$T005_Id_Pedido_Externo = $id;
		$T005_Pedido_Ecommerce = $pedido['original_id'];
		$loja = $pedido['channel'];

		if(empty($pedido['original_id']) && !empty($external[0])){
			$loja = "MERCADO LIVRE";
			$T005_Pedido_Ecommerce = $external[0];
		}
		//$T005_Flag_Frete = '1'; //Conta do destinatario
		$T005_Flag_Frete = '0'; //Alterado 09/03/2017 para EMITENTE a pedidos do ALEXANDRE

		$frete = (array) $pedido['shipments'];
		$frete = (array) $frete[0];
		/*
		 * Definição do frete por loja
		 *
		 * A HASH especifica de cada loja vem no campo 'external'
		 */
		$lojas = array(
			'db88c2ffe78f0992c1d7d7371f58ca47' => array('T005_D022_Id' => '44', 'T005_D036_Id' => '4', 'T005_Pgto_Portador' => '23', 'T005_Pgto_Primeiro_Vcto_Valor' => '30', 'T005_Flag_Frete' => '0'), //Mercado Livre
			'64c86d1fe6adf98602994e2bec41bfe5' => array('T005_D022_Id' => '44', 'T005_D036_Id' => '4', 'T005_Pgto_Portador' => '21', 'T005_Pgto_Primeiro_Vcto_Valor' => '30', 'T005_Flag_Frete' => '0'), //CNOVA
			'adcb048802cc420eeba779cf5a084243' => array('T005_D022_Id' => '44', 'T005_D036_Id' => '4', 'T005_Pgto_Portador' => '21', 'T005_Pgto_Primeiro_Vcto_Valor' => '30', 'T005_Flag_Frete' => '0'), //CNOVA
			'041c45aceeecf5bc1ec173012ceb2dae' => array('T005_D022_Id' => '44', 'T005_D036_Id' => '4', 'T005_Pgto_Portador' => '22', 'T005_Pgto_Primeiro_Vcto_Valor' => '30', 'T005_Flag_Frete' => '0'), //(B2W) Americanas / Shoptime / Submarino
		);

		$external = (array) $pedido['external'];
		foreach ($external as $key => $value) {
			if(!empty($lojas[$key])){ //Verifica a loja
				$T005_D036_Id 				   = $lojas[$key]['T005_D036_Id'];
				$T005_D022_Id 				   = $lojas[$key]['T005_D022_Id'];
				$T005_Pgto_Portador 		   = $lojas[$key]['T005_Pgto_Portador'];
				$T005_Pgto_Primeiro_Vcto_Valor = $lojas[$key]['T005_Pgto_Primeiro_Vcto_Valor'];
				$T005_Flag_Frete 			   = $lojas[$key]['T005_Flag_Frete'];

				if($key == "db88c2ffe78f0992c1d7d7371f58ca47") { //Mercado livre
					if(!empty($frete)){
						if($frete['shipping_company'] != "A combinar"){
							//$T005_D022_Id = '141'; //Mercado envios
							$T005_D022_Id = '44'; // CORREIO - GUIMEPA
							$T005_D036_Id = (strpos($frete['shipping_method'], 'eSedex') !== false) ? '8' : $lojas[$key]['T005_D036_Id']; //Tipo de transporte (pac, sedex, e-sedex)
							//$pedido['shipping'] = 0;
						} else {
							$T005_D022_Id = $lojas[$key]['T005_D022_Id'];
							$T005_D036_Id = '';
						}
					} else {
						$T005_D022_Id = '38';
						$T005_D036_Id = '3';
					}
				}
			}
		}

		if(strtotime($pedido['created']) < strtotime("2016-05-03")){
			$logImportacao .= "Erro - Pedido criado antes de 03/05/2016 {$pedido['status']}<br>";
			registrarLog(array("T005", '0', $g['empresaAtual'], "ID {$id}", "Pedido criado antes de 03/05/2016 ({$pedido['status']})", $logImportacao, "N", "2"));
			return false;
		}
		if($T005_Id_Pedido_Externo != "57278918070f960d1e3b996b"){
		//return false;
		}

		mysql_query("LOCK T005");
		mysql_query("LOCK T006");
		mysql_query("LOCK D024");
		mysql_query("LOCK D001");
		mysql_query("START TRANSACTION");
		$logImportacao .= "Transação iniciada<br>";
		//Verifica se o pedido já foi importado
		if(!empty($T005_Id_Pedido_Externo)){
			$sqlT005 = mysql_query("SELECT T005_Id FROM T005 WHERE T005_Id_Pedido_Externo = '{$T005_Id_Pedido_Externo}' AND T005_C004_Id = '{$g['empresaAtual']}'");
			if(mysql_num_rows($sqlT005) > 0){
				mysql_query("ROLLBACK");
				$rowT005 = mysql_fetch_array($sqlT005);
				file_put_contents($arquivoLogImportacao."_log", "\n[".date("d/m/Y H:i:s")."] Pedido já importado!", FILE_APPEND);
				$logImportacao .= "Pedido já importado<br>";
				registrarLog(array("T005", $rowT005['T005_Id'], $g['empresaAtual'], "ID {$id}", "Pedido já importado", $logImportacao, "N", "2"));
				return false;
			}
		}


		//Localiza ou pré cadastra o cliente
		$logImportacao .= "Procurando/cadastrando cliente<br>";
		$D024 = $this->procuraCadastroCliente($pedido);
		if(!is_array($D024)){
			mysql_query("ROLLBACK");
			file_put_contents($arquivoLogImportacao."_log", "\n[".date("d/m/Y H:i:s")."] Erro ao localizar/pré-cadastrar cliente", FILE_APPEND);
			$logImportacao .= "Erro ao localizar/pré-cadastrar cliente<br>";
			mysql_query("UPDATE T245 SET T245_Flag_Erro = 'R' WHERE T245_Referencia = 'ID {$id}' AND T245_Tipo_Log = '2'");
			registrarLog(array("T005", '0', $g['empresaAtual'], "ID {$id}", "Erro ao importar pedido <b>{$id}</b>, houve um problema ao localizar/pré-cadastrar cliente", $logImportacao, "S", "2"));
			return false;
		}
		$D024_Id = $D024[0];
		$D013_Id = $D024[1];
		$D148_Id = $D024[2];
		$logImportacao .= "Cliente encontrado (Id: {$D024_Id})<br>";
		/*
		* C007_Id
		*/
		/*
		 * Insere o cabeçalho do pedido
		 *
		 * external  total 				Total
		 * subtotal  					Subtotal
		 * shipping  					Shipping Costs
		 * discount  					Discont
		 * created 						Data/hora criação (Não consta na documentação da API)
         */

		$T005_D024_Id = $D024_Id;
		$T005_D013_Id = $D013_Id;
		$T005_D148_Id_Entrega = $D148_Id;
		$T005_C004_Id = $g['empresaAtual'];
		$T005_C007_Id_Vendedor_Interno = '1040';
		//$T005_Data_Emissao = date("Y-m-d", strtotime($pedido['created']));
		$T005_Data_Emissao = date("Y-m-d");
		//$T005_Hora_Emissao = date("H:i:s", strtotime($pedido['created']));
		$T005_Hora_Emissao = date("H:i:s");
		$T005_Flag_ACP = '6';
		$T005_Valor_Desconto = mysql_real_escape_string(trim($pedido['discount']));
		$T005_Valor_Total_Produtos = mysql_real_escape_string(trim($pedido['subtotal']));
		$T005_Valor_Total_Produtos_Sem_Desconto = mysql_real_escape_string(trim($pedido['subtotal']));
		$T005_Valor_Total = mysql_real_escape_string(trim($pedido['total']));
		$T005_Valor_Frete = mysql_real_escape_string(trim($pedido['shipping']));
		$T005_Flag_Status = $g['C031']['StatusPedidoImportadoPluggto'];
		$T005_Flag_Lista_Preco = '1';
		$T005_Flag_Destino_Produto = '3';
		$T005_Observacao_Nota_Fiscal = mysql_real_escape_string($loja." ".$T005_Pedido_Ecommerce);
		$T005_Data_Entrega = $T005_Data_Emissao;
		$T005_Flag_Primeira_Liberacao_Credito = '2';
        $totalizarPeso = ($g['C031']['totalizarPesoVenda'] == 'S') ? 'S' : 'N';
        $T005_Flag_Plataforma_Ecommerce = 1;

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
		$insert.= "T005_Flag_Totalizar_Peso";
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
  		$insert.= "'{$totalizarPeso}'";
        $insert.= ")";

        mysql_query($insert);
        $logImportacao .= "Gravando cabeçalho do pedido<br>";
		$erro = mysql_error();
		if(!empty($erro)){
			mysql_query("ROLLBACK");
			file_put_contents($arquivoLogImportacao."_log", "\n[".date("d/m/Y H:i:s")."] Erro ao gravar cabeçalho do pedido", FILE_APPEND);
			$logImportacao .= "Erro ao gravar cabeçalho do pedido<br>Erro: {$erro}<br>";
			mysql_query("UPDATE T245 SET T245_Flag_Erro = 'R' WHERE T245_Referencia = 'ID {$id}' AND T245_Tipo_Log = '2'");
			registrarLog(array("T005", '0', $g['empresaAtual'], "ID {$id}", "Erro ao importar pedido <b>{$id}</b>, houve um problema ao gravar cabeçalho do pedido", $logImportacao, "S", "2"));
			return false;
		}
		$T005_Id = $g['mysqlLastId'];

		/*
		 * Insere os produtos
		 */
		$count = 0;
		$logImportacao .= "Iniciando inclusão dos itens do pedido<br>";
		foreach ($pedido['items'] as $key => $item) {
			$count++;
			$item = (array) $item;
			$D009_Id = $this->procuraCadastroProduto($item);
			if(!$D009_Id){
				$logImportacao .= "Erro ao localizar produto (Item {$count}: {$item['sku']} - {$item['name']})<br>";
				$logImportacao .= "Incluindo produto (Item {$count}: {$item['sku']} -{$item['name']})<br>";
				$D009_Id = $this->cadastrarProduto($item);
				if(!$D009_Id){
					$logImportacao .= "Erro ao pré-cadastrar produto (Item {$count}: {$item['sku']} - {$item['name']})<br>";
				}
			}

			if(!$D009_Id){
				$item['name'] = mysql_real_escape_string($item['name']);
				if(!empty($item['variation'])){
					$variacao = (array) $item['variation'];
					$item['sku'] 	= !empty($variacao['sku'])  ? '(Varação) '.$variacao['sku']  : $item['sku'];
				}
				mysql_query("ROLLBACK");
				file_put_contents($arquivoLogImportacao."_log", "\n[".date("d/m/Y H:i:s")."] Erro ao localizar/pré-cadastrar produto (Item: {$count})", FILE_APPEND);
				$logImportacao .= "Erro ao localizar/pré-cadastrar produto (Item {$count}: {$item['sku']} - {$item['name']})<br>";
				mysql_query("UPDATE T245 SET T245_Flag_Erro = 'R' WHERE T245_Referencia = 'ID {$id}' AND T245_Tipo_Log = '2'");
				registrarLog(array("T005", '0', $g['empresaAtual'], "ID {$id}", "Erro ao importar pedido <b>{$id}</b>, houve um problema ao localizar/pré-cadastrar produto (Item {$count}: {$item['sku']} - {$item['name']})", $logImportacao, "S", "2"));
				return false;
			}

			$sqlD001 = mysql_query("SELECT D001_Codigo_Produto, D001_Descricao_Produto, D001_D005_Id, D001_D037_Id FROM D001 LEFT JOIN D049 ON D049_D001_Id=D001_Id LEFT JOIN D009 ON D009_D049_Id=D049_Id WHERE D009_Id = '{$D009_Id}'");
			$rowD001 = mysql_fetch_assoc($sqlD001);
			$rowD001['D001_Codigo_Produto'] = mysql_real_escape_string(trim($rowD001['D001_Codigo_Produto']));
			$rowD001['D001_Descricao_Produto'] = mysql_real_escape_string(strtoupper(trim($rowD001['D001_Descricao_Produto'])));

			require_once("bibliotecas/classes/VEN001.php");
        	$VEN001 = new VEN001();
        	$D006_Id = $VEN001->retornaCFOP($D024_Id, $D009_Id, $g['empresaAtual']);
        	$demonstrativoCFOP = $VEN001->retornaCFOP($D024_Id, $D009_Id, $g['empresaAtual'], false, false, true);
        	$demonstrativoCFOP = mysql_real_escape_string($demonstrativoCFOP);

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
			$insert.= "T006_Valor_Preco_Sem_Desconto_Unitario, ";
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
			$insert.= "'{$item['quantity']}', ";
			$insert.= "'{$item['price']}', ";
			$insert.= "'3'";
			$insert.= ")";
			mysql_query($insert);
			$logImportacao .= "Gravando item do pedido (Item {$count})<br>";
			$erro = mysql_error();
			if(!empty($erro)){
				$item['name'] = mysql_real_escape_string($item['name']);
				mysql_query("ROLLBACK");
				file_put_contents($arquivoLogImportacao."_log", "\n[".date("d/m/Y H:i:s")."] Erro ao gravar item do pedido (Item: {$count})", FILE_APPEND);
				$logImportacao .= "Erro ao gravar item do pedido (Item {$count} - Descrição: {$item['name']})<br>Erro: {$erro}<br>";
				mysql_query("UPDATE T245 SET T245_Flag_Erro = 'R' WHERE T245_Referencia = 'ID {$id}' AND T245_Tipo_Log = '2'");
				registrarLog(array("T005", '0', $g['empresaAtual'], "ID {$id}", "Erro ao importar pedido <b>{$id}</b>, houve um problema ao gravar item do pedido (Item {$count} - Descrição: {$item['name']})", $logImportacao, "S", "2"));
				return false;
			}
			$T006_Id = $g['mysqlLastId'];


        	require_once("bibliotecas/classes/CAD002.php");
        	$CAD002 = new CAD002();
        	$CAD002->D001_reprocessa_historico(false, $D009_Id);
		}
		$logImportacao .= "Atualizando alíquotas e CFOP do pedido<br>";
    	require_once("bibliotecas/classes/VEN002.php");
    	$VEN002 = new VEN002();
		$VEN002->ven002atualizarCFOP($T005_Id);
		$VEN002->atualizarAliquotasPedidos($T005_Id);

		//Gera as parcelas
		$logImportacao .= "Gerando parcelas do pedido<br>";
		require_once('bibliotecas/classes/GFormWrap.php');
		$formPgto = new GFormWrap(array(
            'T005_Id' => $T005_Id,
            'Total_Parcelas' => '1',
            'IntervalorDias' => '28',
            'diaFixo' => '28',
            'primeiroVencimentoNum' => $T005_Pgto_Primeiro_Vcto_Valor,
            'primeiroVencimentoData' => $T005_Pgto_Primeiro_Vcto_Valor,
            'Portador_D027_Id' => $T005_Pgto_Portador,
            'selecionaPrimeiroVencimento' => 'Dias',
            'selecionaDias' => 'I',
            'usarFreteParcela' => 'OFF',
            'usarSTParcela' => 'OFF',
            'usarIPIParcela' => 'OFF'
        ));

        $VEN002->atualizarParcelas($formPgto);

        $logImportacao .= "Finalizando a importação do pedido<br>";
		echo "<br><br>D024_Id: " . $D024_Id;
		echo "<br>T005_Id: " . $T005_Id;
		mysql_query("COMMIT");
		mysql_query("UNLOCK ALL");
		$logImportacao .= "Transação finalizada";
		registrarLog(array("T005", $T005_Id, $g['empresaAtual'], "ID {$id}", "Importação do pedido realizada com sucesso", $logImportacao, "N", "2"));
		mysqli_query("UPDATE T245 SET T245_Flag_Erro = 'R' WHERE T245_Referencia = 'ID {$id}' AND T245_Tipo_Log = '2'",$g['conexaoBandoAuditoria']);
	}

	/**
	 * procuraCadastroCliente
	 * @param $pedido array
	 * @return string
	 */
	public function procuraCadastroCliente($pedido)
	{
		global $g;
		$urlApi=$g['C031']['UrlApiPluggto'];

		$D024_Id = false;
		$D024_Cnpj = str_replace(array('.', '/', '-'), '', trim($pedido['payer_cnpj']));
		$D024_Cpf = str_replace(array('.', '/', '-'), '', trim($pedido['payer_cpf']));

		if(!empty($pedido['payer_cnpj'])){ //CNPJ
			$sqlD024 = mysql_query("SELECT D024_Id FROM D024 WHERE REPLACE(REPLACE(REPLACE(TRIM(D024_Cnpj), '/', ''), '-', ''), '.', '') = '{$D024_Cnpj}'");
			if(mysql_num_rows($sqlD024) >= 1){
				$rowD024 = mysql_fetch_assoc($sqlD024);
				$D024_Id = $rowD024['D024_Id'];
			}
		}

		if(!empty($pedido['payer_cpf'])){ //CPF
			$sqlD024 = mysql_query("SELECT D024_Id FROM D024 WHERE REPLACE(REPLACE(REPLACE(TRIM(D024_Cpf), '/', ''), '-', ''), '.', '') = '{$D024_Cpf}'");
			if(mysql_num_rows($sqlD024) >= 1){
				$rowD024 = mysql_fetch_assoc($sqlD024);
				$D024_Id = $rowD024['D024_Id'];
			}
		}

		/*
		* Se não localizou o cliente faz o pré cadastro, senão atualiza os dados do cliente
		*
		* payer_name 					Payer name
		* payer_lastname 				Payer Lastname
		* payer_address 				Payer Address
		* payer_address_number 			Payer Address number
		* payer_zipcode 				Payer Zipcode
		* payer_address_complement 		Payer complement (ie Suite)
		* payer_address_reference 		Payer reference (ie next to the supermarket)
		* payer_additional_info 		Payer addicional info
		* payer_neighborhood 			payer Neighborhood
		* payer_city 					Payer City
		* Payer_state 					Payer State
		* payer_country 				Payer Country
		* Payer_phone_area 				Payer Phone Area
		* payer_phone 					Payer Phone
		* Payer_phone2_area 			Payer Phone Area 2
		* payer_phone2 					Payer Phone
		* payer_email 					Payer email
		* payer_cpf 					Payer CPF
		* payer_cnpj 					Payer CNPJ
		* payer_razao_social 			Payer Company Name
		* payer_ie 						Payer I.E (For Brasil)
		*/

		//Localiza o país
		$D030_Nome_Pais = trim($pedido['payer_country']);
		$sqlD030 = mysql_query("SELECT D030_Id FROM D030 WHERE D030_Nome_Pais = '{$D030_Nome_Pais}'");
		if(mysql_num_rows($sqlD030) == 1){
			$rowD030 = mysql_fetch_array($sqlD030);
			$D024_D030_Id = $rowD030['D030_Id'];
		}

		//Localiza o estado
		$D018_Estado = trim($pedido['payer_state']);
		$sqlD018 = mysql_query("SELECT D018_Id FROM D018 WHERE D018_Estado = '{$D018_Estado}' OR D018_UF = '{$D018_Estado}'");
		if(mysql_num_rows($sqlD018) == 1){
			$rowD018 = mysql_fetch_array($sqlD018);
			$D024_D018_Id = $rowD018['D018_Id'];
		}

		//Localiza a cidade
		$D020_Nome_Cidade = trim($pedido['payer_city']);
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

		$D024_D030_Id = '271';
		$D024_Inscricao_Estadual 	  = mysql_real_escape_string(trim($pedido['payer_ie']));
		$D024_Endereco 			 	  = mysql_real_escape_string(trim(strtoupper($pedido['payer_address'])));
		$D024_Numero 			 	  = mysql_real_escape_string(trim($pedido['payer_address_number']));
		$D024_Cep 				 	  = mysql_real_escape_string(trim($pedido['payer_zipcode']));
		$D024_Complemento 		 	  = mysql_real_escape_string(trim($pedido['payer_address_complement'] . ' ' . $pedido['payer_address_reference'] . ' ' . $pedido['payer_additional_info']));
		$D024_Bairro 			 	  = mysql_real_escape_string(trim(strtoupper($pedido['payer_neighborhood'])));
		$D024_D020_Id 			 	  = mysql_real_escape_string(trim($D024_D020_Id));
		$D024_D018_Id 			 	  = mysql_real_escape_string(trim($D024_D018_Id));
		$D024_D030_Id 			 	  = mysql_real_escape_string(trim($D024_D030_Id));
		$D024_DDD_Telefone_1 	 	  = mysql_real_escape_string(trim($pedido['payer_phone_area']));
		$D024_Telefone_1 		 	  = mysql_real_escape_string(trim($pedido['payer_phone']));
		$D024_DDD_Telefone_2 	 	  = mysql_real_escape_string(trim($pedido['payer_phone2_area']));
		$D024_Telefone_2 		 	  = mysql_real_escape_string(trim($pedido['payer_phone2']));
		$D024_E_Mail 			 	  = mysql_real_escape_string(trim($pedido['payer_email']));

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
				$D024_Nome_Empresa  = $pedido['payer_name'].' '.$pedido['payer_lastname'];
				$D024_Nome_Fantasia = $pedido['payer_name'].' '.$pedido['payer_lastname'];
				$D024_Inscricao_Estadual = '';
				$D024_Flag_Contribuinte = '0';
				$D024_Cpf = substr($D024_Cpf,0,3).'.'.substr($D024_Cpf,3,3).'.'.substr($D024_Cpf,6,3).'-'.substr($D024_Cpf,9,2);
			} else if(!empty($D024_Cnpj)){
				$D024_Flag_Pessoa_Fisica_Juridica = 'J';
				$D024_Nome_Empresa  = $pedido['payer_razao_social'];
				$D024_Nome_Fantasia = $pedido['payer_razao_social'];
				$D024_Flag_Contribuinte = '5';
				$D024_Cnpj = substr($D024_Cnpj,0,2).'.'.substr($D024_Cnpj,2,3).'.'.substr($D024_Cnpj,5,3).'/'.substr($D024_Cnpj,8,4).'-'.substr($D024_Cnpj,12,2);
			} else {
				$D024_Nome_Empresa  = $pedido['payer_name'].' '.$pedido['payer_lastname'];
				$D024_Nome_Fantasia = $pedido['payer_name'].' '.$pedido['payer_lastname'];
				$D024_Nome_Empresa .= !empty($pedido['payer_razao_social']) ? ' '.$pedido['payer_razao_social'] : '';
				$D024_Nome_Fantasia.= !empty($pedido['payer_razao_social']) ? ' '.$pedido['payer_razao_social'] : '';
			}

			$D024_Nome_Empresa 		 	  = mysql_real_escape_string(trim(strtoupper($D024_Nome_Empresa)));
			$D024_Nome_Fantasia 	 	  = mysql_real_escape_string(trim(strtoupper($D024_Nome_Fantasia)));
			$D024_Cpf 				 	  = mysql_real_escape_string($D024_Cpf);
			$D024_Cnpj 				 	  = mysql_real_escape_string($D024_Cnpj);
			$D024_Codigo_Regime_Tributario = '1';
			$D024_C007_Vendedor_Interno = '1040';
			$D024_Flag_Liberado = 'N';

			$insert = "INSERT INTO D024 (";
			$insert.= "D024_Cpf, ";
			$insert.= "D024_Cnpj, ";
			$insert.= "D024_Flag_Pessoa_Fisica_Juridica, ";
			$insert.= "D024_Inscricao_Estadual, ";
			$insert.= "D024_Nome_Empresa, ";
			$insert.= "D024_Nome_Fantasia, ";
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
			$insert.= "D024_Data_Cadastro, ";
			$insert.= "D024_Flag_Contribuinte, ";
			$insert.= "D024_Flag_Liberado, ";
			$insert.= "D024_C007_Vendedor_Interno, ";
			$insert.= "D024_Codigo_Regime_Tributario";
			$insert.= ") VALUES (";
			$insert.= "'{$D024_Cpf}', ";
			$insert.= "'{$D024_Cnpj}', ";
			$insert.= "'{$D024_Flag_Pessoa_Fisica_Juridica}', ";
			$insert.= "'{$D024_Inscricao_Estadual}', ";
			$insert.= "'{$D024_Nome_Empresa}', ";
			$insert.= "'{$D024_Nome_Fantasia}', ";
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
			$insert.= "CURDATE(), ";
			$insert.= "'{$D024_Flag_Contribuinte}', ";
			$insert.= "'{$D024_Flag_Liberado}', ";
			$insert.= "'{$D024_C007_Vendedor_Interno}', ";
			$insert.= "'{$D024_Codigo_Regime_Tributario}'";
			$insert.= ")";

			mysql_query($insert);
			$erro = mysql_error();
			if(!empty($erro)){
				return false;
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
					return false;
				}
			}
		}


		/* Cadastra o endereço de entrega se não existir
		 *
		 * receiver_name 					Receiver name
		 * receiver_lastname 				Receiver Lastname
		 * receiver_address 				Receiver Address
		 * receiver_address_number 			Receiver Address number
		 * receiver_zipcode 				Receiver Zipcode
		 * receiver_address_complement  	Receiver complement (ie Suite)
		 * receiver_address_reference 		Receiver reference (ie next to the supermarket)
		 * receiver_additional_info 		Receiver addicional info
		 * receiver_neighborhood 			Receiver Neighborhood
		 * receiver_city 					Receiver City
		 * receiver_state 					Receiver State
		 * receiver_country 				Receiver Country
		 * receiver_phone_area 				Receiver Phone Area
		 * receiver_phone 					Receiver Phone
		 * receiver_phone2_area 			Receiver Phone Area 2
		 * receiver_phone2 					Receiver Phone
		 * receiver_email 					Receiver Email
		 */

		//Localiza o país
		$D030_Nome_Pais = trim($pedido['receiver_country']);
		$sqlD030 = mysql_query("SELECT D030_Id FROM D030 WHERE D030_Nome_Pais = '{$D030_Nome_Pais}'");
		if(mysql_num_rows($sqlD030) == 1){
			$rowD030 = mysql_fetch_array($sqlD030);
			$D148_D030_Id = $rowD030['D030_Id'];
		}

		//Localiza o estado
		$D018_Estado = trim($pedido['receiver_state']);
		$sqlD018 = mysql_query("SELECT D018_Id FROM D018 WHERE D018_Estado = '{$D018_Estado}' OR D018_UF = '{$D018_Estado}'");
		if(mysql_num_rows($sqlD018) == 1){
			$rowD018 = mysql_fetch_array($sqlD018);
			$D148_D018_Id = $rowD018['D018_Id'];
		}

		//Localiza a cidade
		$D020_Nome_Cidade = trim($pedido['receiver_city']);
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
	    $D148_Nome			= mysql_real_escape_string(trim(strtoupper($pedido['receiver_name'].' '.$pedido['receiver_lastname'])));
	    $D148_Cep			= mysql_real_escape_string(trim($pedido['receiver_zipcode']));
	    $D148_Rua			= mysql_real_escape_string(trim(strtoupper($pedido['receiver_address'])));
	    $D148_Numero		= mysql_real_escape_string(trim($pedido['receiver_address_number']));
	    $D148_Complemento	= mysql_real_escape_string(trim(strtoupper($pedido['receiver_address_complement'].' '.$pedido['receiver_address_reference'].' '.$pedido['receiver_additional_info'])));
	    $D148_Bairro		= mysql_real_escape_string(trim(strtoupper($pedido['receiver_neighborhood'])));
	    $D148_DDD_Telefone	= mysql_real_escape_string(trim($pedido['receiver_phone_area']));
	    $D148_Telefone		= mysql_real_escape_string(trim($pedido['receiver_phone']));
	    $D148_DDD_Fax		= mysql_real_escape_string(trim($pedido['receiver_phone2_area']));
	    $D148_Fax			= mysql_real_escape_string(trim($pedido['receiver_phone2']));
	    $D148_Email			= mysql_real_escape_string(trim($pedido['receiver_email']));
	    $D148_Flag_Tipo 	= 'E';

	    //Verifica se o endereço já está cadastrado
	    $D148_Id = 0;
	    $sqlD148 = mysql_query("SELECT D148_Id FROM D148 WHERE D148_D024_Id = '{$D024_Id}' AND REPLACE(D148_Cep,'-','') = '{$D148_Cep}' AND D148_Numero = '{$D148_Numero}'");
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
			$insert.= "D148_DDD_Telefone, ";
			$insert.= "D148_Telefone, ";
			$insert.= "D148_DDD_Fax, ";
			$insert.= "D148_Fax, ";
			$insert.= "D148_Email, ";
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
			$insert.= "'{$D148_DDD_Telefone}', ";
			$insert.= "'{$D148_Telefone}', ";
			$insert.= "'{$D148_DDD_Fax}', ";
			$insert.= "'{$D148_Fax}', ";
			$insert.= "'{$D148_Email}', ";
			$insert.= "'{$D148_Flag_Tipo}'";
			$insert.= ")";
			mysql_query($insert);
			$erro = mysql_error();
			if(!empty($erro)){
				return false;
			}
			$Verifica_D024_CEP = $D024_Cep . $D024_Numero;
			$Verifica_D148_CEP = $D148_Cep . $D148_Numero;

			if($Verifica_D024_CEP != $Verifica_D148_CEP){
				$D148_Id = $g['mysqlLastId'];
			}
	    }else{
	    	$rowD148 = mysql_fetch_array($sqlD148);
			$D148_Id= $rowD148['D148_Id'];
	    }

	    return array($D024_Id, $D013_Id, $D148_Id);
	}

	/**
	 * procuraCadastroProduto
	 * @param $item array
	 * @return string
	 */
	public function procuraCadastroProduto($item)
	{
		global $g;
		require_once("bibliotecas/classes/CAD002.php");
		$urlApi=$g['C031']['UrlApiPluggto'];
		/*
		 * ItemSchema
		 *	  name  Name of the item
		 *	  id  Item ID in PLugg.To (pluggto_id)
		 *	  sku sku
		 *	  price price
		 *	  discount discount
		 *	  quantity quantity
		 *	  total total
		 *	  external external
		 *	  variation see VariationSchema
		 *
		 * VariationSchema
		 *	  name ation name
		 *	  id Variation id
		 *	  external External
		 *	  sku External
		 *	  ean External
		 *	  attributes see AttributesSchema
		 *	  dimension see DimensionSchema
		 *
		 * DimensionSchema
		 *    length Length
		 *	  width Width
		 *	  height Height
		 *	  weight Weight
		 */
		$D001_Codigo_Produto = $item['sku'];
		$D001_Descricao_Produto = $item['name'];
		$D001_Especificacoes = $D001_Descricao_Produto;
		if(!empty($item['variation'])){
			$variacao = (array) $item['variation'];
			$D001_Codigo_Produto 	= !empty($variacao['sku'])  ? $variacao['sku']  : $D001_Codigo_Produto;
			$D001_Descricao_Produto = !empty($variacao['name']) ? $variacao['name'] : $D001_Descricao_Produto;
			$D001_Codigo_Barras 	= !empty($variacao['ean'])  ? $variacao['ean']  : $D001_Codigo_Barras;
			$D001_Especificacoes .= ' - ' . $D001_Descricao_Produto;
			if(!empty($variacao['dimension'])){
				$dimensao = (array) $variacao['dimension'];
				$D001_Peso_Unitario_Kg = $dimensao['weight'];
			}
		}

		$D001_Descricao_Produto = mysql_real_escape_string(strtoupper(trim($D001_Descricao_Produto)));
		$D001_Especificacoes 	= mysql_real_escape_string(strtoupper(trim($D001_Especificacoes)));

		//Verifica se o produto existe no sistema
		$sqlD001 = mysql_query("SELECT D001_Id FROM D001 WHERE D001_Codigo_Produto = '{$D001_Codigo_Produto}'");
		if(mysql_num_rows($sqlD001) > 0){
			$rowD001 = mysql_fetch_assoc($sqlD001);
			$sqlD009 = mysql_query("SELECT D009_Id FROM D009 LEFT JOIN D049 ON D049_Id=D009_D049_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id WHERE D001_Id = '{$rowD001['D001_Id']}' AND D009_C004_Id = '{$g['empresaAtual']}'");
			$erro = mysql_error();
			if(!empty($erro)){
				return false;
			}
			$rowD009 = mysql_fetch_assoc($sqlD009);
			return $rowD009['D009_Id'];
		}
		return false;
	}

	/**
	 * procuraCadastroProduto
	 * @param $item array
	 * @return string
	 */
	public function cadastrarProduto($item)
	{
		global $g;
		require_once("bibliotecas/classes/CAD002.php");
		$urlApi=$g['C031']['UrlApiPluggto'];
		/*
		 * ItemSchema
		 *	  name  Name of the item
		 *	  id  Item ID in PLugg.To (pluggto_id)
		 *	  sku sku
		 *	  price price
		 *	  discount discount
		 *	  quantity quantity
		 *	  total total
		 *	  external external
		 *	  variation see VariationSchema
		 *
		 * VariationSchema
		 *	  name ation name
		 *	  id Variation id
		 *	  external External
		 *	  sku External
		 *	  ean External
		 *	  attributes see AttributesSchema
		 *	  dimension see DimensionSchema
		 *
		 * DimensionSchema
		 *    length Length
		 *	  width Width
		 *	  height Height
		 *	  weight Weight
		 */
		$D001_Codigo_Produto = $item['sku'];
		$D001_Descricao_Produto = $item['name'];
		$D001_Especificacoes = $D001_Descricao_Produto;
		if(!empty($item['variation'])){
			$variacao = (array) $item['variation'];
			$D001_Codigo_Produto 	= !empty($variacao['sku'])  ? $variacao['sku']  : $D001_Codigo_Produto;
			$D001_Descricao_Produto = !empty($variacao['name']) ? $variacao['name'] : $D001_Descricao_Produto;
			$D001_Codigo_Barras 	= !empty($variacao['ean'])  ? $variacao['ean']  : $D001_Codigo_Barras;
			$D001_Especificacoes .= ' - ' . $D001_Descricao_Produto;
			if(!empty($variacao['dimension'])){
				$dimensao = (array) $variacao['dimension'];
				$D001_Peso_Unitario_Kg = $dimensao['weight'];
			}
		}

		$D001_Descricao_Produto = mysql_real_escape_string(strtoupper(trim($D001_Descricao_Produto)));
		$D001_Especificacoes 	= mysql_real_escape_string(strtoupper(trim($D001_Especificacoes)));

		//Verifica se o produto existe no sistema
		$CAD002 = new CAD002();
		if(empty($D001_Codigo_Barras)){
			$D001_Codigo_Barras = $CAD002->obterNovoCodigoEAN();
		}

		//O produto não foi encontrado, vamos pré cadastrar
		$insert = "INSERT INTO D001 (";
		$insert.= "D001_Codigo_Produto, ";
		$insert.= "D001_Codigo_Barras, ";
        $insert.= "D001_Flag_Pre_Cadastro, ";
        $insert.= "D001_Flag_Ativo, ";
        $insert.= "D001_Descricao_Produto, ";
        $insert.= "D001_Peso_Unitario_Kg, ";
        $insert.= "D001_Especificacoes ";
        $insert.= ") VALUES (";
        $insert.= "'{$D001_Codigo_Produto}', ";
        $insert.= "'{$D001_Codigo_Barras}', ";
        $insert.= "'S', ";
        $insert.= "'S', ";
        $insert.= "'{$D001_Descricao_Produto}', ";
        $insert.= "'{$D001_Peso_Unitario_Kg}', ";
        $insert.= "'{$D001_Especificacoes}' ";
        $insert.= ")";
        mysql_query($insert);
		$erro = mysql_error();
		if(!empty($erro)){
			file_put_contents('/tmp/errointegracao', $erro);
			return false;
		}
		$D001_Id = $g['mysqlLastId'];
		mysql_query("INSERT INTO D049 (D049_D001_Id, D049_D082_Id, D049_Data_Cadastro, D049_Flag_Tipo) VALUES ('{$D001_Id}', 1, CURDATE(), '');");
    	$D049_Id = $g['mysqlLastId'];
    	return $CAD002->preCadastroMarca($D049_Id, $D001_Id);
	}

	/**
	 * atualizarPedidoPluggto
	 * @param $T005_Id string
	 * @return bool
	 */
	public function atualizarPedidoPluggto($T005_Id)
	{
		global $g, $confUsuario;
		$urlApi=$g['C031']['UrlApiPluggto'];
		$access_token = $this->obterAutorizacao();
		$sqlPedido = mysql_query("SELECT * FROM T005 LEFT JOIN D036 ON D036_Id=T005_D036_Id LEFT JOIN T007 ON T007_T005_Id=T005_Id LEFT JOIN D022 ON D022_Id=T007_D022_Id LEFT JOIN T268 ON T268_T007_Id=T007_Id WHERE T005_Id = '{$T005_Id}'");
		$pedido    = mysql_fetch_array($sqlPedido);

		if(empty($pedido['T005_Id_Pedido_Externo'])){
			return 'Id pedido PluggTo não informado';
		}
		if(empty($pedido['T007_Numero_Nota_Fiscal']) || empty($pedido['T007_Chave_Acesso_Nfe']) || empty($pedido['T007_Numero_Protocolo_Nfe'])){
			return 'Nota fiscal não ainda transmitida';
		}
		$id = $pedido['T005_Id_Pedido_Externo'];

		//Carrega JSON original do pedido
		$dataPedido = date("dmY", strtotime($pedido['T005_Data_Emissao']));
		$arquivoJson = "{$g['pathDados']}/pluggto/{$dataPedido}/pedido_{$id}";

		/*
		 * Requisição do pedido atualizado na plataforma PluggTo
		 */
		$ch = curl_init("{$urlApi}/orders/{$id}?access_token={$access_token}&showExternal=true");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$data = curl_exec($ch);
		curl_close($ch);

		$arrayPedido = (array) json_decode($data);
		$arrayPedido['Order'] = (array) $arrayPedido['Order'];
		//echo "\n\n";
		/*
		TRANSPORTADORA
		CODIGO DE RASTREIO
		URL DE RASTREIO
		DATA ESTIMADA DE ENTREGA
		DATA DA NOTA FISCAL
		NUMERO DA NOTA FISCAL
		SERIE DA NOTA FISCAL
		CHAVE DE ACESSO DA NOTA FISCAL
		LINK PARA CONSULTA DA NOTA FISCAL
		*/
		if (!empty($pedido['T007_Numero_Nota_Fiscal']) and empty($pedido['T268_Codigo_Rastreio'])) {
			$arrayPedido['Order']['status'] = 'invoiced';
		} else if (!empty($pedido['T007_Numero_Nota_Fiscal']) and !empty($pedido['T268_Codigo_Rastreio'])) {
			$arrayPedido['Order']['status'] = 'shipping_informed';
		} else {
			$arrayPedido['Order']['status'] = '';
		}
		$dados = (array) $arrayPedido['Order']['shipments'][0];
		$dados['shipping_company']		  = $pedido['D022_Nome_Empresa'];
		$dados['shipping_method']		  = $pedido['D036_Tipo_Transporte'];
		$dados['track_code']			  = $pedido['T268_Codigo_Rastreio'];
		//$dados['track_url']			  = $pedido[''];
		//$dados['estimate_delivery_date']= $pedido['T005_Data_Entrega'];
		$dados['nfe_date']				  = $pedido['T007_Data_Emissao'];
		$dados['nfe_number']			  = $pedido['T007_Numero_Nota_Fiscal'];
		$dados['nfe_serie']			  	  = $pedido['T007_Serie'];
		$dados['nfe_key']				  = $pedido['T007_Chave_Acesso_Nfe'];

		$Query = md5($pedido['T007_D024_Id'].'|'.$pedido['T007_Id']);
		//http://hardness.guimepa.com.br/hardness/danfe/imprimir_danfe.php?Query=846dd4c8851df1575677debfff631672&vlrNF=S&T007_Id=16562
		$link = "{$confUsuario['urlRaiz']}/hardness3/hardness/danfe/imprimir_danfe.php?Query={$Query}&vlrNF=S&T007_Id={$pedido['T007_Id']}";
		$dados['nfe_link']			  	  = $link;
		//print_r($dados);
		//die();

		$arrayPedido['Order']['shipments'][0] = (object) $dados;

		$arrayPedido['Order'] = (object) $arrayPedido['Order'];
		$arrayPedido = (object) $arrayPedido;
		$arrayPedido = json_encode($arrayPedido, JSON_UNESCAPED_UNICODE); //Funciona no PHP >= 5.4

		$ch = curl_init("{$urlApi}/orders/{$id}?access_token={$access_token}");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayPedido);
		$data = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		if($info['http_code'] == '200'){
			//Grava o arquivo JSON atualizado
			file_put_contents($arquivoJson, $arrayPedido);
			return true;
		} else {
			print_r($info);
			print_r($data);
			return "HTTP Code {$info['http_code']}";
		}
	}
}

