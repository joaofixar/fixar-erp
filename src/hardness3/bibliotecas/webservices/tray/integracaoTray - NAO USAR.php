<?php
namespace hardness;

Class Tray {
	/**
	 * importarPedido
	 * @param $id string
	 * @return bool
	 */
	public function importarPedido($data)
	{
		global $g, $arquivoLogImportacao;

		$pedido    = (array) $data;
		$cabecalho = (array) $pedido['cabecalho'];
		$cliente   = (array) $cabecalho['cliente'];

		$id = $cabecalho['id'];

		$dataPedido = date("dmY", strtotime($cabecalho['date']));
		$dir = "{$g['pathDados']}/pluggto/{$dataPedido}/";
		is_dir($dir) OR mkdir($dir, 0777, true);
		$arquivoLogImportacao = $dir."pedido_{$id}";

		file_put_contents($arquivoLogImportacao, $data);
		$logImportacao = "Id Pedido Tray {$id}<br>";
		$logImportacao .= "Gravou conteudo do arquivo<br>";

		$T005_Id_Pedido_Externo = $id;
		$T005_Pedido_Ecommerce = $cabecalho['partner_id'];
		$loja = $cabecalho['point_sale'];

		$T005_Flag_Frete = '1'; //Conta do destinatario

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
				return false;
			}
		}

		//Localiza ou pré cadastra o cliente
		$logImportacao .= "Procurando/cadastrando cliente<br>";
		$D024 = $this->procuraCadastroCliente($cliente);
		if(!is_array($D024)){
			mysql_query("ROLLBACK");
			file_put_contents($arquivoLogImportacao."_log", "\n[".date("d/m/Y H:i:s")."] Erro ao localizar/pré-cadastrar cliente", FILE_APPEND);
			$logImportacao .= "Erro ao localizar/pré-cadastrar cliente<br>";
			mysql_query("UPDATE T245 SET T245_Flag_Erro = 'R' WHERE T245_Referencia = 'ID {$id}' AND T245_Tipo_Log = '3'");
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

		$T005_D024_Id = $D024_Id;
		$T005_D013_Id = $D013_Id;
		$T005_D148_Id_Entrega = $D148_Id;
		$T005_C004_Id = $g['empresaAtual'];
		$T005_C007_Id_Vendedor_Interno = '63';
		$T005_Data_Emissao = mysql_real_escape_string(trim($cabecalho['date']));
		$T005_Data_Emissao = date("Y-m-d");
		$T005_Hora_Emissao = mysql_real_escape_string(trim($cabecalho['hour']));
		$T005_Data_Entrega = mysql_real_escape_string(trim($cabecalho['delivered']));
		$T005_Flag_ACP = '6';
		$T005_Valor_Desconto = mysql_real_escape_string(trim($cabecalho['discount']));
		$T005_Valor_Total_Produtos = mysql_real_escape_string(trim($cabecalho['partial_total']));
		$T005_Valor_Total_Produtos_Sem_Desconto = mysql_real_escape_string(trim($cabecalho['partial_total']));
		$T005_Valor_Total = mysql_real_escape_string(trim($cabecalho['total']));
		$T005_Valor_Frete = mysql_real_escape_string(trim($cabecalho['shipment_value']));
		$T005_Flag_Status = '0';
		$T005_Flag_Lista_Preco = '1';
		$T005_Flag_Destino_Produto = '3';
		$T005_Observacao_Nota_Fiscal = mysql_real_escape_string($loja." ".$T005_Pedido_Ecommerce);
		$T005_Flag_Primeira_Liberacao_Credito = '2';
        $totalizarPeso = ($g['C031']['totalizarPesoVenda'] == 'S') ? 'S' : 'N';
        $T005_Flag_Plataforma_Ecommerce = 2;

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
        //echo $insert."\n\n\n";
        $logImportacao .= "Gravando cabeçalho do pedido<br>";
		$erro = mysql_error();
		if(!empty($erro)){
			mysql_query("ROLLBACK");
			file_put_contents($arquivoLogImportacao."_log", "\n[".date("d/m/Y H:i:s")."] Erro ao gravar cabeçalho do pedido", FILE_APPEND);
			$logImportacao .= "Erro ao gravar cabeçalho do pedido<br>Erro: {$erro}<br>";
			mysql_query("UPDATE T245 SET T245_Flag_Erro = 'R' WHERE T245_Referencia = 'ID {$id}' AND T245_Tipo_Log = '3'");
			registrarLog(array("T005", '0', $g['empresaAtual'], "ID {$id}", "Erro ao importar pedido <b>{$id}</b>, houve um problema ao gravar cabeçalho do pedido", $logImportacao, "S", "3"));
			return false;
		}
		$T005_Id = $g['mysqlLastId'];

		/*
		 * Insere os produtos
		 */
		$count = 0;
		$logImportacao .= "Iniciando inclusão dos itens do pedido<br>";
		foreach ($pedido['itens'] as $key => $item) {
			$count++;
			$item 	   = (array) $item;
			$dadosItem = (array) $item['dadosItem'];
			$D009_Id = $this->procuraCadastroProduto($item);
			if(!$D009_Id){
				$dadosItem['name'] = mysql_real_escape_string(strip_tags($dadosItem['name']));
				mysql_query("ROLLBACK");
				file_put_contents($arquivoLogImportacao."_log", "\n[".date("d/m/Y H:i:s")."] Erro ao localizar/pré-cadastrar produto (Item: {$count})", FILE_APPEND);
				$logImportacao .= "Erro ao localizar/pré-cadastrar produto (Item {$count} - Descrição: {$dadosItem['name']})<br>";
				mysql_query("UPDATE T245 SET T245_Flag_Erro = 'R' WHERE T245_Referencia = 'ID {$id}' AND T245_Tipo_Log = '3'");
				registrarLog(array("T005", '0', $g['empresaAtual'], "ID {$id}", "Erro ao importar pedido <b>{$id}</b>, houve um problema ao localizar/pré-cadastrar produto (Item {$count} - Descrição: {$dadosItem['name']})", $logImportacao, "S", "3"));
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
			$insert.= "T006_Valor_Preco_Original, ";
			$insert.= "T006_Valor_Preco_Unitario, ";
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
			$insert.= "'{$dadosItem['quantity']}', ";
			$insert.= "'{$dadosItem['price']}', ";
			$insert.= "'{$dadosItem['price']}', ";
			$insert.= "'{$dadosItem['price']}', ";
			$insert.= "'3'";
			$insert.= ")";
			mysql_query($insert);
			//echo $insert."\n\n\n";
			$logImportacao .= "Gravando item do pedido (Item {$count})<br>";
			$erro = mysql_error();
			if(!empty($erro)){
				$dadosItem['name'] = mysql_real_escape_string(strip_tags($dadosItem['name']));
				mysql_query("ROLLBACK");
				file_put_contents($arquivoLogImportacao."_log", "\n[".date("d/m/Y H:i:s")."] Erro ao gravar item do pedido (Item: {$count})", FILE_APPEND);
				$logImportacao .= "Erro ao gravar item do pedido (Item {$count} - Descrição: {$dadosItem['name']})<br>Erro: {$erro}<br>";
				mysql_query("UPDATE T245 SET T245_Flag_Erro = 'R' WHERE T245_Referencia = 'ID {$id}' AND T245_Tipo_Log = '3'");
				registrarLog(array("T005", '0', $g['empresaAtual'], "ID {$id}", "Erro ao importar pedido <b>{$id}</b>, houve um problema ao gravar item do pedido (Item {$count} - Descrição: {$dadosItem['name']})", $logImportacao, "S", "3"));
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
			}

			mysql_query("INSERT INTO T090 (T090_T005_Id, T090_D027_Id, T090_Numero_Parcela, T090_Valor_Parcela, T090_Data_Vencimento, T090_Observacao) VALUES ('{$T005_Id}', '{$D027_Id}', '{$parcela}', '{$pagamento['value']}', '{$pagamento['date']}', '{$pagamento['method']}')");
			$erro = mysql_error();
			if(!empty($erro)){
				$dadosItem['name'] = mysql_real_escape_string(strip_tags($dadosItem['name']));
				mysql_query("ROLLBACK");
				file_put_contents($arquivoLogImportacao."_log", "\n[".date("d/m/Y H:i:s")."] Erro ao gravar forma de pagamento do pedido (Parcela: {$parcela})", FILE_APPEND);
				$logImportacao .= "Erro ao gravar forma de pagamento do pedido (Parcela: {$parcela})<br>Erro: {$erro}<br>";
				mysql_query("UPDATE T245 SET T245_Flag_Erro = 'R' WHERE T245_Referencia = 'ID {$id}' AND T245_Tipo_Log = '3'");
				registrarLog(array("T005", '0', $g['empresaAtual'], "ID {$id}", "Erro ao importar pedido <b>{$id}</b>, houve um problema ao gravar forma de pagamento do pedido (Parcela: {$parcela})", $logImportacao, "S", "3"));
				return false;
			}
		}

        $logImportacao .= "Finalizando a importação do pedido<br>";
		mysql_query("COMMIT");
		mysql_query("UNLOCK ALL");
		$logImportacao .= "Transação finalizada";
		registrarLog(array("T005", $T005_Id, $g['empresaAtual'], "ID {$id}", "Importação do pedido realizada com sucesso", $logImportacao, "N", "3"));
		mysql_query("UPDATE T245 SET T245_Flag_Erro = 'R' WHERE T245_Referencia = 'ID {$id}' AND T245_Tipo_Log = '3'");
		return true;
	}

	/**
	 * procuraCadastroCliente
	 * @param $cliente array
	 * @return string
	 */
	public function procuraCadastroCliente($cliente)
	{
		global $g;
		$dadosCliente = (array) $cliente['dadosCliente'];

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
			if(mysql_num_rows($sqlD024) >= 1){
				$rowD024 = mysql_fetch_assoc($sqlD024);
				$D024_Id = $rowD024['D024_Id'];
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
				$D024_Flag_Contribuinte = '5';
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
			$D024_C007_Vendedor_Interno = '63';
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
					return false;
				}

				if($D024_Cep != $D148_Cep){
					$D148_Id = $g['mysqlLastId'];
				}
			}
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

		$D001_Codigo_Produto = '';
		$D001_Descricao_Produto = strip_tags($dadosItem['name']); //A descrição do item na venda contem os detalhes da variação
		$D001_Especificacoes 	= strip_tags($cadastroProduto['description']);
		$D001_Codigo_Barras 	= $cadastroProduto['ean'];
		$D001_Peso_Unitario_Kg  = $cadastroProduto['weight'];
		if(!empty($item['variacao'])){
			$variacao = (array) $item['variacao'];
			$D001_Codigo_Barras 	= (!empty($variacao['ean'])) ? $variacao['ean'] : $D001_Codigo_Barras;
			$D001_Peso_Unitario_Kg 	= $variacao['weight'];
		}

		$D001_Descricao_Produto = mysql_real_escape_string(strtoupper(trim($D001_Descricao_Produto)));
		$D001_Especificacoes 	= mysql_real_escape_string(strtoupper(trim($D001_Especificacoes)));

		//Verifica se o produto existe no sistema
		$sqlD001 = mysql_query("SELECT D001_Id FROM D001 WHERE D001_Id_Ecommerce = '{$D001_Id_Ecommerce}' OR D001_Codigo_Barras = '{$D001_Codigo_Barras}'");
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
			$CAD002 = new CAD002();
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
		}
	}
}