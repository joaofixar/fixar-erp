<?php
namespace hardness;

definirClasse('VEN014');
class VEN014_
{
	/**
	 * procuraCadastroCliente
	 * @param $pedido array
	 * @return string
	 */
	public function gravarCliente($D024,$T005_Id_Pedido_Externo)
	{
		global $g, $logImportacao;
		
		// Retira ponto, barra e hifen do CNPJ e CPF
		$D024_Id = false;
		$D024_Cpf = trim($D024['D024_Cpf']);
		$D024_Cnpj= trim($D024['D024_Cnpj']);		

		log("entrando em cliente");

        // Busca cliente pelo CNPJ
		if(!empty($D024_Cnpj)){ //CNPJ
			$D024_Cnpj = substr($D024_Cnpj,0,2).'.'.substr($D024_Cnpj,2,3).'.'.substr($D024_Cnpj,5,3).'/'.substr($D024_Cnpj,8,4).'-'.substr($D024_Cnpj,12,2);
			$sqlD024 = mysql_query("SELECT D024_Id FROM D024 WHERE D024_Cnpj = '{$D024_Cnpj}'");
			if(mysql_num_rows($sqlD024) > 0){
				$rowD024 = mysql_fetch_array($sqlD024);
				$D024_Id = $rowD024['D024_Id'];
				log("Id encontrado: ".$D024_Id);
			} 
		}else if(!empty($D024_Cpf)){ //CPF
			$D024_Cpf = substr($D024_Cpf,0,3).'.'.substr($D024_Cpf,3,3).'.'.substr($D024_Cpf,6,3).'-'.substr($D024_Cpf,9,2);
			$sqlD024 = mysql_query("SELECT D024_Id FROM D024 WHERE D024_Cpf = '{$D024_Cpf}'");
			if(mysql_num_rows($sqlD024) > 0){
				$rowD024 = mysql_fetch_array($sqlD024);
				$D024_Id = $rowD024['D024_Id'];
				log("Id encontrado: ".$D024_Id);
			} 
		}else{
			log("Dados do cliente não encontrados!");
			$logImportacao .= "CPF/CNPJ não se encontra no pedido T005 Id Pedido: {$T005_Id_Pedido_Externo}";
			registrarLog(array("T005", '0', $g['empresaAtual'], "ID {$T005_Id_Pedido_Externo}", "CPF/CNPJ não se encontra no pedido!", $logImportacao, "S", "2"));
			mysqli_query("UPDATE T245 SET T245_Flag_Erro = 'S' WHERE T245_Referencia = 'ID {$T005_Id_Pedido_Externo}' AND T245_Tipo_Log = '2'",$g['conexaoBandoAuditoria']);
			return false;
		}

		//Localiza o país
		$D030_Nome_Pais = trim($D024['Pais']);
		$sqlD030 = mysql_query("SELECT D030_Id FROM D030 WHERE D030_Nome_Pais = '{$D030_Nome_Pais}'");
		if(mysql_num_rows($sqlD030) > 0){
			$rowD030 = mysql_fetch_array($sqlD030);
			$D024_D030_Id = $rowD030['D030_Id'];
		} else {
            $D024_D030_Id = 271;            
        }

		//Localiza o estado
		$D018_Estado = trim($D024["UF"]);
		$sqlD018 = mysql_query("SELECT D018_Id FROM D018 WHERE D018_Estado = '{$D018_Estado}' OR D018_UF = '{$D018_Estado}'");
		if(mysql_num_rows($sqlD018) > 0){
			$rowD018 = mysql_fetch_array($sqlD018);
			$D024_D018_Id = $rowD018['D018_Id'];
		}

		//Localiza a cidade
		$D020_Nome_Cidade = trim($D024["Cidade"]);
		$sqlD020 = mysql_query("SELECT D020_Id FROM D020 WHERE D020_Nome_Cidade = '{$D020_Nome_Cidade}' AND D020_D018_Id = '{$D024_D018_Id}'");
		if(mysql_num_rows($sqlD020) > 0){
			$rowD020 = mysql_fetch_array($sqlD020);
			$D024_D020_Id = $rowD020['D020_Id'];
		}

		$D024_Nome_Empresa  = $D024["D024_Nome_Empresa"];
		$D024_Nome_Fantasia = $D024["D024_Nome_Fantasia"];
		$D024_Nome_Empresa 		 	  = mysql_real_escape_string(trim(strtoupper($D024_Nome_Empresa)));
		$D024_Nome_Fantasia 	 	  = mysql_real_escape_string(trim(strtoupper($D024_Nome_Fantasia)));
		$D024_Inscricao_Estadual 	  = mysql_real_escape_string(trim($D024["D024_Inscricao_Estadual"]));
		$D024_Endereco 			 	  = mysql_real_escape_string(trim(strtoupper($D024["D024_Endereco"])));
		$D024_Numero 			 	  = mysql_real_escape_string(trim($D024["D024_Numero"]));
		$D024_Cep 				 	  = mysql_real_escape_string(trim($D024["D024_Cep"]));
		$D024_Complemento 		 	  = mysql_real_escape_string(trim($D024["D024_Complemento"]));
		$D024_Bairro 			 	  = mysql_real_escape_string(trim(strtoupper($D024["D024_Bairro"])));
		$D024_D020_Id 			 	  = mysql_real_escape_string(trim($D024_D020_Id));
		$D024_D018_Id 			 	  = mysql_real_escape_string(trim($D024_D018_Id));
		$D024_D030_Id 			 	  = mysql_real_escape_string(trim($D024_D030_Id));
		$D024_DDD_Telefone_1 	 	  = mysql_real_escape_string(trim($D024["D024_DDD_Telefone_1"]));
		$D024_Telefone_1 		 	  = mysql_real_escape_string(trim($D024["D024_Telefone_1"]));
		$D024_DDD_Telefone_2 	 	  = mysql_real_escape_string(trim($D024["D024_DDD_Telefone_2"]));
		$D024_Telefone_2 		 	  = mysql_real_escape_string(trim($D024["D024_Telefone_2"]));
		$D024_E_Mail 			 	  = mysql_real_escape_string(trim($D024["D024_E_Mail"]));
		$D024_Web_Site				  = mysql_real_escape_string(trim($D024["D024_Web_Site"]));

		//Tratamento do telefone 1 - Há casos que o DDD não vem no campo correto
		$D024_DDD_Telefone_1 = str_replace(array('(',')'), '', $D024_DDD_Telefone_1);
		$D024_Telefone_1     = str_replace(array('(',')'), '', $D024_Telefone_1);
		$D024_Telefone_1 	 = str_replace("-", '', $D024_Telefone_1);

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

		//Tratamento do telefone 2 - Há casos que o DDD não vem no campo correto
		$D024_DDD_Telefone_2 = str_replace(array('(',')'), '', $D024_DDD_Telefone_2);
		$D024_Telefone_2     = str_replace(array('(',')'), '', $D024_Telefone_2);
		$D024_Telefone_2 = str_replace("-", '', $D024_Telefone_2);

		if(substr($D024_Telefone_2,0,1) == '0'){ //Remove o zero do inicio se houver
			$D024_Telefone_2 = substr($D024_Telefone_2, 1);
		}
		if(empty($D024_DDD_Telefone_2)){ //Pega o DDD caso não venha em campo específico
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
		//Tratamento do CPF/CNPJ
		if(!empty($D024_Cpf)){
			$D024_Flag_Pessoa_Fisica_Juridica = 'F';
			$D024_Inscricao_Estadual = '';
			$D024_Flag_Contribuinte = '0';
		} else if(!empty($D024_Cnpj)){
			$D024_Flag_Pessoa_Fisica_Juridica = 'J';
			$D024_Flag_Contribuinte = '0';
		} 
		//Não localizou o cliente
		if(!$D024_Id) {

			log("Cliente não localizado!");
			$D024_Cpf 				 	  = mysql_real_escape_string($D024_Cpf);
			$D024_Cnpj 				 	  = mysql_real_escape_string($D024_Cnpj);
			$D024_Codigo_Regime_Tributario = '1';
			$D024_C007_Vendedor_Interno =  $D024["C007_Id_Vendedor_Interno"];
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
			$insert.= "D024_Web_Site,";
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
			$insert.= "'{$D024_Web_Site}', ";
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

			log("Cliente novo cadastrado!");

		} else { //Localizou o cliente, mas vai atualizar os dados

			log("Cliente encontrado, fazendo o update de dados!");

			$sqlD024 = mysql_query("SELECT * FROM D024 WHERE D024_Id = '{$D024_Id}'");
			$rowD024 = mysql_fetch_assoc($sqlD024);
			//Pega o primeiro contato
			$sqlD013 = mysql_query("SELECT * FROM D013 WHERE D013_D024_Id = '{$D024_Id}' LIMIT 1");
			$rowD013 = mysql_fetch_assoc($sqlD013);
			$D013_Id = $rowD013['D013_Id'];

			//Os campos só serão atualizados caso esteja vazio no banco ou diferente dos dados que estão preenchidos no pedido
			$countUpdate = 0;
			$update = "UPDATE D024 SET ";
			if((empty($rowD024['D024_Nome_Empresa']) && !empty($D024_Nome_Empresa)) || (!empty($D024_Nome_Empresa) && $rowD024['D024_Nome_Empresa'] != $D024_Nome_Empresa)) {
				$update .= "D024_Nome_Empresa = '{$D024_Nome_Empresa}', ";
				$countUpdate++;
			}
			if((empty($rowD024['D024_Nome_Fantasia']) && !empty($D024_Nome_Fantasia)) || (!empty($D024_Nome_Fantasia) && $rowD024['D024_Nome_Fantasia'] != $D024_Nome_Fantasia)) {
				$update .= "D024_Nome_Fantasia = '{$D024_Nome_Fantasia}', ";
				$countUpdate++;
			}
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
			
			log("Dados do cliente atualizados!");
		}
        
        //pega o array com os dados de endereço de entrega de dentro do array cliente
		$D148 = (array) $D024['D148'];

		// Cadastra o endereço de entrega se não existir

		//Localiza o país
		$D030_Nome_Pais = trim($D148['Pais']);
		$sqlD030 = mysql_query("SELECT D030_Id FROM D030 WHERE D030_Nome_Pais = '{$D030_Nome_Pais}'");
		if(mysql_num_rows($sqlD030) == 1){
			$rowD030 = mysql_fetch_array($sqlD030);
			$D148_D030_Id = $rowD030['D030_Id'];
		} else {
            $D148_D030_Id = 271;            
        }

		//Localiza o estado
		$D018_Estado = trim($D148["UF"]);
		$sqlD018 = mysql_query("SELECT D018_Id FROM D018 WHERE D018_Estado = '{$D018_Estado}' OR D018_UF = '{$D018_Estado}'");
		if(mysql_num_rows($sqlD018) == 1){
			$rowD018 = mysql_fetch_array($sqlD018);
			$D148_D018_Id = $rowD018['D018_Id'];
		}

	//Localiza a cidade
		$D020_Nome_Cidade = trim($D148["Cidade"]);
		$sqlD020 = mysql_query("SELECT D020_Id FROM D020 WHERE D020_Nome_Cidade = '{$D020_Nome_Cidade}' AND D020_D018_Id = '{$D148_D018_Id}'");
		if(mysql_num_rows($sqlD020) > 0){
			$rowD020 = mysql_fetch_array($sqlD020);
			$D148_D020_Id = $rowD020['D020_Id'];
		}

	    $D148_D024_Id 		 	 = $D024_Id;
	    $D148_Nome			 	 = mysql_real_escape_string(trim(strtoupper($D148["D148_Nome"])));
	    $D148_Cep			 	 = mysql_real_escape_string(trim($D148["D148_Cep"]));
	    $D148_Rua			 	 = mysql_real_escape_string(trim(strtoupper($D148["D148_Rua"])));
	    $D148_Numero		 	 = mysql_real_escape_string(trim($D148["D148_Numero"]));
	    $D148_Complemento		 = mysql_real_escape_string(trim(strtoupper($D148["D148_Complemento"].' '.$D148["Entrega_Referencia"].' '.$D148["Entrega_Informacoes_adicionais"])));
	    $D148_Bairro			 = mysql_real_escape_string(trim(strtoupper($D148["D148_Bairro"])));
	    $D148_DDD_Telefone		 = mysql_real_escape_string(trim($D148["D148_DDD_Telefone"]));
	    $D148_Telefone		 	 = mysql_real_escape_string(trim($D148["D148_Telefone"]));
	    $D148_DDD_Fax		 	 = mysql_real_escape_string(trim($D148["D148_DDD_Fax"]));
	    $D148_Fax				 = mysql_real_escape_string(trim($D148["D148_Fax"]));
	    $D148_Email			     = mysql_real_escape_string(trim($D148["D148_Email"]));
	    $D148_Flag_Tipo 		 = 'E';
		$D148_Cpf				 = mysql_real_escape_string(trim($D024_Cpf));
	    $D148_Cnpj			     = mysql_real_escape_string(trim($D024_Cnpj));
		$D148_Inscricao_Estadual = mysql_real_escape_string(trim($D024_Inscricao_Estadual));
		$D148_Flag_Pessoa_Fisica_Juridica = $D024_Flag_Pessoa_Fisica_Juridica;		

	    //Tratamento do Telefone - Há casos que o DDD não vem no campo correto
		$D148_DDD_Telefone = str_replace(array('(',')'), '', $D148_DDD_Telefone);
		$D148_Telefone     = str_replace(array('(',')'), '', $D148_Telefone);
		if(substr($D148_Telefone,0,1) == '0'){ //Remove o zero do inicio se houver
			$D148_Telefone = substr($D148_Telefone, 1);
		}
		if(empty($D148_DDD_Telefone)){ //Pega o DDD caso não venha em campo específico
			if(strpos($D148_Telefone, ' ') !== false) {
				$telefone = explode(' ', $D148_Telefone);
				if(is_array($telefone)){
					$D148_DDD_Telefone = $telefone[0];
					$D148_Telefone = $telefone[1];
				}
			} else if(strpos($D148_Telefone, '-') !== false) {
				$telefone = explode('-', $D148_Telefone);
				if(is_array($telefone)){
					$D148_DDD_Telefone = $telefone[0];
					$D148_Telefone = $telefone[1];
				}
			} else if(strpos($D148_Telefone, '_') !== false){
				$telefone = explode('_', $D148_Telefone);
				if(is_array($telefone)){
					$D148_DDD_Telefone = $telefone[0];
					$D148_Telefone = $telefone[1];
				}
			}
			if(empty($D148_DDD_Telefone)){
				$D148_DDD_Telefone = substr($D148_Telefone, 0, 2);
				$D148_Telefone     = substr($D148_Telefone, 2);
			}
		}
		//Tratamento do Fax - Há casos que o DDD não vem no campo correto
		$D148_DDD_Fax = str_replace(array('(',')'), '', $D148_DDD_Fax);
		$D148_Fax     = str_replace(array('(',')'), '', $D148_Fax);
		if(substr($D148_Fax,0,1) == '0'){ //Remove o zero do inicio se houver
			$D148_Fax = substr($D148_Fax, 1);
		}
		if(empty($D148_DDD_Fax)){ //Pega o DDD caso não venha em campo específico
			if(strpos($D148_Fax, ' ') !== false) {
				$telefone = explode(' ', $D148_Fax);
				if(is_array($telefone)){
					$D148_DDD_Fax = $telefone[0];
					$D148_Fax = $telefone[1];
				}
			} else if(strpos($D148_Fax, '-') !== false) {
				$telefone = explode('-', $D148_Fax);
				if(is_array($telefone)){
					$D148_DDD_Fax = $telefone[0];
					$D148_Fax = $telefone[1];
				}
			} else if(strpos($D148_Fax, '_') !== false){
				$telefone = explode('_', $D148_Fax);
				if(is_array($telefone)){
					$D148_DDD_Fax = $telefone[0];
					$D148_Fax = $telefone[1];
				}
			}
			if(empty($D148_DDD_Fax)){
				$D148_DDD_Fax = substr($D148_Fax, 0, 2);
				$D148_Fax     = substr($D148_Fax, 2);
			}
		}

	    //Verifica se o endereço já está cadastrado se estiver pega o id do endereço
	    $sqlD148 = mysql_query("SELECT D148_Id FROM D148 WHERE D148_D024_Id = '{$D024_Id}' AND D148_Cep = '{$D148_Cep}' AND D148_Numero = '{$D148_Numero}'");
		if(mysql_num_rows($sqlD148) == 0){
			log("Endereço de entrega não encontrado, será inserido no sistema!");
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
			$insert.= "D148_Flag_Tipo, ";
			$insert.= "D148_Cpf, ";
			$insert.= "D148_Cnpj, ";
			$insert.= "D148_Inscricao_Estadual, ";
			$insert.= "D148_Flag_Pessoa_Fisica_Juridica ";
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
			$insert.= "'{$D148_Flag_Tipo}', ";
			$insert.= "'{$D148_Cpf}', ";
			$insert.= "'{$D148_Cnpj}', ";
			$insert.= "'{$D148_Inscricao_Estadual}', ";
			$insert.= "'{$D148_Flag_Pessoa_Fisica_Juridica}'";
			$insert.= ")";
			mysql_query($insert);
			$erro = mysql_error();
			if(!empty($erro)){
				return false;
			}

			$D148_Id = $g['mysqlLastId'];

	    }else{		
			$rowD148 = mysql_fetch_array($sqlD148);
			$D148_Id= $rowD148['D148_Id'];
			log("ID entrega: ".$D148_Id);		
		}
	
		log("Cliente e endereço de entrega foram atualizados/inseridos com sucesso!");
	    return array($D024_Id, $D013_Id, $D148_Id);
	}

	/**
	 * procuraCadastroProduto
	 * @param $item array
	 * @return string
	 */
	public function procuraCadastroProduto($T006, $C004_Id = false)
	{
		global $g, $logImportacao;
		require_once("bibliotecas/classes/CAD002.php");

		$C004_Id = ($C004_Id === false ? $g['empresaAtual'] : $C004_Id);

		log("Verificando se o produto existe no sistema!");
		$D001_Codigo_Produto = $T006["T006_Codigo_Produto"];
		$D001_Descricao_Produto = $T006["T006_Descricao_Produto"];
		$D001_Especificacoes = $D001_Descricao_Produto;
		$D001_Peso_Unitario_Kg = $T006["Peso_Unitario"];	
		$D001_Descricao_Produto = mysql_real_escape_string(strtoupper(trim($D001_Descricao_Produto)));
		$D001_Especificacoes 	= mysql_real_escape_string(strtoupper(trim($D001_Especificacoes)));

		//Verifica se o produto existe no sistema
		$sqlD001 = mysql_query("SELECT D001_Id FROM D001 WHERE D001_Codigo_Produto = '{$D001_Codigo_Produto}'");
		if(mysql_num_rows($sqlD001) > 0){
			$rowD001 = mysql_fetch_assoc($sqlD001);
			$sqlD009 = mysql_query("SELECT D009_Id FROM D009 LEFT JOIN D049 ON D049_Id=D009_D049_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id WHERE D001_Id = '{$rowD001['D001_Id']}' AND D009_C004_Id = '{$C004_Id}'");
			$erro = mysql_error();
			if(!empty($erro)){
				return false;
			}
			$rowD009 = mysql_fetch_assoc($sqlD009);
			return $rowD009['D009_Id'];
		}
		log("Produto não encontrado no sistema!");
		return false;
	}

	/**
	 * CadastroProduto
	 * @param $item array
	 * @return string
	 */
	public function cadastrarProduto($T006)
	{
		global $g, $logImportacao;
		require_once("bibliotecas/classes/CAD002.php");
		$CAD002 = new CAD002();
		log("Cadastrando o produto no sistema!");
		$D001_Codigo_Produto = $T006["T006_Codigo_Produto"];
		$D001_Descricao_Produto = $T006["T006_Descricao_Produto"];
		$D001_Especificacoes = $D001_Descricao_Produto;
		$D001_Peso_Unitario_Kg = $T006["Peso_Unitario"];
		$D001_Codigo_Barras = $T006["Codigo_Barras"];
		$D001_Descricao_Produto = mysql_real_escape_string(strtoupper(trim($D001_Descricao_Produto)));
		$D001_Especificacoes 	= mysql_real_escape_string(strtoupper(trim($D001_Especificacoes)));

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
		log("Produto inserido no sistema!");
    	return $CAD002->preCadastroMarca($D049_Id, $D001_Id);
	}

	public function procuraPedido($T005_Id_Pedido_Externo) {
	
		global $g, $logImportacao;

		$condicao = "";

		// Valida se Id pedido externo é vazio
		if (empty($T005_Id_Pedido_Externo)){
			log("O T005_Pedido_Externo está vazio");
			$logImportacao .= "Não recebido o ID do pedido<br>";
			registrarLog(array("T005", 0, $g['empresaAtual'], "ID {$T005_Id_Pedido_Externo}", "Não recebido o ID do pedido", $logImportacao, "N", "2"));
			mysqli_query("UPDATE T245 SET T245_Flag_Erro = 'S' WHERE T245_Referencia = 'ID {$T005_Id_Pedido_Externo}' AND T245_Tipo_Log = '2'",$g['conexaoBandoAuditoria']);
			return true;
		}

		$urlApiEmpresaAtual = $g['C031']['MagentoUrlApi'];

		$sqlC031 = mysql_query("SELECT C031_C004_Id Where C031_Campo='MagentoUrlApi' AND C031_Valor='{$urlApiEmpresaAtual}'");
		while($C031=mysql_fetch_array($sqlC031)){
			if(empty($conficao)){
				$condicao .= "AND (T005_C004_Id={$C031['C031_C004_Id']}";
			}else{
				$condicao .= "OR T005_C004_Id={$C031['C031_C004_Id']}";
			}
		}

		if(!empty($condicao)){
			$condicao.=")";
		}
		

		log("$T005_Id_Pedido_Externo: O T005_Pedido_Externo não está vazio!");
		
		// Valida se Id pedido externo já foi importado
		$sqlT005 = mysql_query("SELECT T005_Id FROM T005 WHERE T005_Id_Pedido_Externo = '{$T005_Id_Pedido_Externo}' {$condicao}");
		log("SELECT T005_Id FROM T005 WHERE T005_Id_Pedido_Externo = '{$T005_Id_Pedido_Externo}' {$condicao}");
		if(mysql_num_rows($sqlT005) > 0){
			$rowT005 = mysql_fetch_array($sqlT005);
			// log("Pedido com T005_Id_Pedido_Externo já foi importado!");
			// $logImportacao .= "Pedido já importado<br>";
			// registrarLog(array("T005", $rowT005['T005_Id'], $g['empresaAtual'], "ID {$T005_Id_Pedido_Externo}", "Pedido já importado", $logImportacao, "N", "2"));
			// mysqli_query("UPDATE T245 SET T245_Flag_Erro = 'S' WHERE T245_Referencia = 'ID {$T005_Id_Pedido_Externo}' AND T245_Tipo_Log = '2'",$g['conexaoBandoAuditoria']);
			return true;
		}

		return false;

	}

	public function retornaC031ValorPorEmpresa($C004_Id, $campo)
	{
		$C031 = mysql_query("SELECT C031_Valor FROM C031 WHERE C031_Campo = '$campo' AND C031_C004_Id = '$C004_Id'");
		$mC031 = mysql_fetch_assoc($C031);

		return $mC031['C031_Valor'];
	}

	public function cadastrarPedido($T005, $C004_Id = false){
	
		global $g, $logImportacao;

		$T005_Id_Pedido_Externo = $T005["T005_Id_Pedido_Externo"];

		$C004_Id = ($C004_Id === false ? $g['empresaAtual'] : $C004_Id);

       	require_once("bibliotecas/classes/CAD002.php");
       	$CAD002 = new CAD002();
		require_once("bibliotecas/classes/VEN001.php");
       	$VEN001 = new VEN001();
    	require_once("bibliotecas/classes/VEN002.php");
    	$VEN002 = new VEN002();

		mysql_query("START TRANSACTION");
	
		$Cliente = (array) $T005['D024'];

		$T005_T134_Id="";
		$C004  = mysql_query("SELECT C004_Codigo_Regime_Tributario FROM C004 WHERE C004_Id = $C004_Id");
		$mC004 = mysql_fetch_assoc($C004);

		if($mC004['C004_Codigo_Regime_Tributario'] ==1 || $mC004['C004_Codigo_Regime_Tributario'] ==2)
		{
			$C031_Valor   = $this->retornaC031ValorPorEmpresa($C004_Id, 'padraoFaixaCalculoIcmsCredito');

			$T005_T134_Id = !empty($C031_Valor) ? $C031_Valor : '';
		}
		
		if (empty($Cliente["D024_Id"])) {
			
			$dataPedido = date("dmY"); //Data emissao no sistema Hardness
			$dir = "{$g['pathDados']}/pedidoExterno/{$dataPedido}/";
			is_dir($dir) OR mkdir($dir, 0777, true);
			$arquivoPedidoExterno = $dir."pedido_{$id}";
			file_put_contents($arquivoPedidoExterno, $T005);

			$logImportacao = "Id Pedido Externo {$T005_Id_Pedido_Externo}<br>";
			$logImportacao .= "Transação iniciada<br>";
		
			//Cadastra ou atualiza cadastro do cliente
			$logImportacao .= "Procurando/cadastrando cliente<br>";
			$Cliente = (array) $T005['D024'];

			$D024 = $this->gravarCliente($Cliente,$T005_Id_Pedido_Externo);

			if(!is_array($D024)){
				mysql_query("ROLLBACK");
				$logImportacao .=  "Erro ao localizar/pré-cadastrar cliente<br>";		
				registrarLog(array("T005", '0', $C004_Id, "ID {$T005_Id_Pedido_Externo}", "Erro ao importar pedido <b>{$T005_Id_Pedido_Externo}</b>, houve um problema ao localizar/pré-cadastrar cliente", $logImportacao, "S", "2"));
				mysqli_query("UPDATE T245 SET T245_Flag_Erro = 'S' WHERE T245_Referencia = 'ID {$T005_Id_Pedido_Externo}' AND T245_Tipo_Log = '2'",$g['conexaoBandoAuditoria']);
				return false;
			}

			$D024_Id = $D024[0];
			$D013_Id = $D024[1];
			$D148_Id = $D024[2];
		
			$logImportacao .= "Cliente encontrado (Id: {$D024_Id})<br>";
			
			$T005_D024_Id = $D024_Id;
			$T005_D013_Id = $D013_Id;
			$T005_D148_Id_Entrega = $D148_Id;
			$T005_D148_Id_Cobranca = '';
			$T005_C004_Id = $C004_Id;
			$T005_C007_Id_Vendedor_Interno =  trim($T005["C007_Id_Vendedor_Interno"]);
			$T005_C007_Id_Vendedor_Externo =  "";
			$T005_D021_Id = "";
			$T005_Pedido_Eccomerce = $T005["T005_Pedido_Ecommerce"];
			$T005_Ordem_Compra = $T005["T005_Ordem_Compra"];
			$T005_Data_Emissao = date("Y-m-d");
			$T005_Hora_Emissao = date("H:i:s");
			$T005_Flag_ACP = '6';
			$T005_Valor_Desconto_Manual = $T005["T005_Valor_Desconto_Manual"];
			$T005_Valor_Total_Produtos = mysql_real_escape_string(trim($T005["T005_Valor_Total_Produtos"]));
			$T005_Valor_Total_Produtos_Sem_Desconto = mysql_real_escape_string(trim($T005["T005_Valor_Total_Produtos_Sem_Desconto"]));
			$T005_Valor_Total = mysql_real_escape_string(trim($T005["T005_Valor_Total"]));
			$T005_Valor_Frete = mysql_real_escape_string(trim($T005["T005_Valor_Frete"]));
			$T005_Flag_Status = $this->retornaC031ValorPorEmpresa($C004_Id, 'StatusPedidoImportado');
			$T005_Flag_Lista_Preco = '1';
			$T005_Flag_Destino_Produto = '3';
			$T005_Observacao_Nota_Fiscal = $T005["T005_Observacao_Nota_Fiscal"];
			$T005_Observacao_Comercial = "";
			$T005_Data_Entrega = $T005_Data_Emissao;
			$T005_Flag_Primeira_Liberacao_Credito = '2';
			$T005_Prazo_Entrega = $T005["T005_Prazo_Entrega"];
			$totalizarPeso = ($this->retornaC031ValorPorEmpresa($C004_Id, 'totalizarPesoVenda') == 'S') ? 'S' : 'N';
			$T005_Flag_Plataforma_Ecommerce = $T005["T005_Flag_Plataforma_Ecommerce"];
			$T005_D022_Id = trim($T005["T005_D022_Id"]);     
			$T005_D036_Id = trim($T005["T005_D036_Id"]);
			$T005_D027_Id = trim($T005["T005_D027_Id"]);
			$T005_Canal_Vendas_Ecommerce = trim($T005["T005_Canal_Vendas_Ecommerce"]);
			$T005_Observacao_Pagamento = trim($T005["T005_Observacao_Pagamento"]);

			//tratando OC. Cli
			if (empty($T005_Ordem_Compra))
			{
				$T005_Ordem_Compra = $T005["T005_Id_Pedido_Externo"];
				
			}else{
				$T005_Ordem_Compra = $T005["T005_Ordem_Compra"];
			}

			//tratando desconto negativo
			if ($T005_Valor_Desconto_Manual < 0 ) {
			 	$T005_Valor_Desconto_Manual = $T005_Valor_Desconto_Manual * -1; 
			}

			//tratando desconto negativo
			if ($T005_Valor_Desconto_Manual < 0 ) {
			 	$T005_Valor_Desconto_Manual = $T005_Valor_Desconto_Manual * -1; 
			}
			 
			$T005_Flag_Frete = 0;     
			$T005_Pgto_Portador = '';
			$T005_Pgto_Primeiro_Vcto_Valor = '';
		
		} else {
			
			$Cliente = (array) $T005['D024'];
			$T005_D024_Id = $Cliente['D024_Id'];
			$T005_D013_Id = $Cliente['D013_Id'];
			$T005_D148_Id_Entrega = '';
			$T005_C004_Id = $C004_Id;
			$T005_C007_Id_Vendedor_Interno = $T005['C007_Id'];
			$T005_C007_Id_Vendedor_Externo = $T005['C007_Vendedor_Externo'];
			$T005_D021_Id = $T005['T005_D021_Id'];
			$T005_Pedido_Eccomerce = '';
			$T005_Data_Emissao = date("Y-m-d");
			$T005_Hora_Emissao = date("H:i:s");
			$T005_Flag_ACP = '6';
			$T005_Valor_Total_Produtos = 0;
			$T005_Valor_Total_Produtos_Sem_Desconto = 0;
			$T005_Valor_Total = 0;
			$T005_Valor_Frete = 0;
			$T005_Flag_Status = 0;
			$T005_Flag_Lista_Preco = '1';
			$T005_Flag_Destino_Produto = '3';
			$T005_Observacao_Nota_Fiscal ='';
			$T005_Observacao_Comercial = $T005['T005_Observacao_Comercial'];
			$T005_Data_Entrega = date("Y-m-d");
			$T005_Flag_Primeira_Liberacao_Credito = '2';
			$totalizarPeso = ($this->retornaC031ValorPorEmpresa($C004_Id, 'totalizarPesoVenda') == 'S') ? 'S' : 'N';
			$T005_D036_Id = 0;
			$T005_D022_Id = $T005['T005_D022_Id'];      
			$T005_Flag_Frete = 0;     
			$T005_D027_Id = trim($T005["T005_D027_Id"]);
			$T005_Pgto_Portador = '';
			$T005_Pgto_Primeiro_Vcto_Valor = '';
			$T005_Prazo_Entrega = $T005["T005_Prazo_Entrega"];
			$T005_Observacao_Pagamento = trim($T005["T005_Observacao_Pagamento"]);
		}

		$insert = "INSERT INTO T005 (";
		$insert.= "T005_D024_Id, ";
		$insert.= "T005_D013_Id, ";
		$insert.= "T005_C004_Id, ";
		$insert.= "T005_C007_Id_Vendedor_Interno, ";
		$insert.= "T005_C007_Id_Vendedor_Externo, ";
		$insert.= "T005_D021_Id, ";
        $insert.= "T005_D036_Id, ";
		$insert.= "T005_D022_Id, ";
		$insert.= "T005_D148_Id_Entrega, ";
		$insert.= "T005_D148_Id_Cobranca, ";
		$insert.= "T005_Data_Emissao, ";
		$insert.= "T005_Hora_Emissao, ";
		$insert.= "T005_Data_Entrega, ";
		$insert.= "T005_Pedido_Ecommerce, ";
		$insert.= "T005_Ordem_Compra, ";
		$insert.= "T005_Id_Pedido_Externo, ";
		$insert.= "T005_Flag_ACP, ";
        $insert.= "T005_Valor_Desconto_Manual, ";
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
        $insert.= "T005_Observacao_Comercial, ";
		$insert.= "T005_Pgto_Portador, ";
		$insert.= "T005_Pgto_Primeiro_Vcto_Valor, ";
		$insert.= "T005_Flag_Totalizar_Peso, ";
		$insert.= "T005_Prazo_Entrega, ";
		$insert.= "T005_Flag_Plataforma_Ecommerce, ";
		$insert.= "T005_D027_Id, ";
		$insert.= "T005_Canal_Vendas_Ecommerce, ";
		$insert.= "T005_T134_Id, ";
		$insert.= "T005_Observacao_Pagamento ";
		$insert.= ") VALUES (";
        $insert.= "'{$T005_D024_Id}', ";
        $insert.= "'{$T005_D013_Id}', ";
		$insert.= "'{$T005_C004_Id}', ";
		$insert.= "'{$T005_C007_Id_Vendedor_Interno}', ";
		$insert.= "'{$T005_C007_Id_Vendedor_Externo}', ";
		$insert.= "'{$T005_D021_Id}', ";
		$insert.= "'{$T005_D036_Id}', ";
		$insert.= "'{$T005_D022_Id}', ";
		$insert.= "'{$T005_D148_Id_Entrega}', ";
		$insert.= "'{$T005_D148_Id_Cobranca}', ";		
		$insert.= "'{$T005_Data_Emissao}', ";
		$insert.= "'{$T005_Hora_Emissao}', ";
		$insert.= "'{$T005_Data_Entrega}', ";
		$insert.= "'{$T005_Pedido_Ecommerce}', ";
		$insert.= "'{$T005_Ordem_Compra}', ";
		$insert.= "'{$T005_Id_Pedido_Externo}', ";
		$insert.= "'{$T005_Flag_ACP}', ";
        $insert.= "'{$T005_Valor_Desconto_Manual}', ";
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
        $insert.= "'{$T005_Observacao_Comercial}', ";
        $insert.= "'{$T005_Pgto_Portador}', ";
		$insert.= "'{$T005_Pgto_Primeiro_Vcto_Valor}', ";
  		$insert.= "'{$totalizarPeso}',";
  		$insert.= "'{$T005_Prazo_Entrega}',";
  		$insert.= "'{$T005_Flag_Plataforma_Ecommerce}',";
  		$insert.= "'{$T005_D027_Id}',";
  		$insert.= "'{$T005_Canal_Vendas_Ecommerce}',";
  		$insert.= "'{$T005_T134_Id}',";
		$insert.= "'{$T005_Observacao_Pagamento}'";
        $insert.= ")";
        mysql_query($insert);
		
        $logImportacao .= "Gravando cabeçalho do pedido<br>";
		$erro = mysql_error();
		if(!empty($erro)){
			mysql_query("ROLLBACK");
			file_put_contents($arquivoLogImportacao."_log", "\n[".date("d/m/Y H:i:s")."] Erro ao gravar cabeçalho do pedido", FILE_APPEND);
			$logImportacao .= "Erro ao gravar cabeçalho do pedido<br>Erro: {$erro}<br>";
			registrarLog(array("T005", '0', $C004_Id, "ID {$T005_Id_Pedido_Externo}", "Erro ao importar pedido <b>{$T005_Id_Pedido_Externo}</b>, houve um problema ao gravar cabeçalho do pedido", $logImportacao, "S", "2"));
			mysqli_query("UPDATE T245 SET T245_Flag_Erro = 'S' WHERE T245_Referencia = 'ID {$T005_Id_Pedido_Externo}' AND T245_Tipo_Log = '2'",$g['conexaoBandoAuditoria']);
			return false;
		}
		$T005_Id = $g['mysqlLastId'];

		/*
		 * Insere os produtos
		 */
		$logImportacao .= "Iniciando inclusão dos itens do pedido<br>";

		$ItensT006 = (array) $T005['T006'];
		$count = 0;
		foreach ($ItensT006 as $key => $T006) {
			
			$count++;

			if (empty($T006["T006_Numero_Pedido_Compra"]))
			{
				$T006_Numero_Pedido_Compra = $T005["T005_Id_Pedido_Externo"];
			
			}else{
				$T006_Numero_Pedido_Compra =  $T006["T006_Numero_Pedido_Compra"];
			}

			if ($T006['D009_Id']<=0){

				$D009_Id = $this->procuraCadastroProduto($T006, $C004_Id);

				if(!$D009_Id){

					$logImportacao .= "Erro ao localizar produto (Item {$count}: {$T006["T006_Codigo_Produto"]} - {$T006["T006_Descricao_Produto"]})<br>";
					$logImportacao .= "Incluindo produto (Item {$count}: {$T006["T006_Codigo_Produto"]} -{$T006["T006_Descricao_Produto"]})<br>";

					$D009_Id = $this->cadastrarProduto($T006);
					if(!$D009_Id){
						log("Erro ao pre-cadastrar o item do pedido!");
						$logImportacao .= "Erro ao pré-cadastrar produto (Item {$count}: {$T006["T006_Codigo_Produto"]} - {$T006["T006_Descricao_Produto"]})<br>";
						mysql_query("ROLLBACK");
						return false;
					}
				}
			}else{
				$D009_Id = $T006['D009_Id'];
				//$cClausula = mysql_query("select * from D009 where D009_Id = '{$D009_Id}'");
				//$mD009 = mysql_fetch_array($cClausula);
			} 

			$sqlD001 = mysql_query("SELECT D001_Codigo_Produto, D001_Descricao_Produto, D001_D005_Id, D001_D037_Id FROM D001 LEFT JOIN D049 ON D049_D001_Id=D001_Id LEFT JOIN D009 ON D009_D049_Id=D049_Id WHERE D009_Id = '{$D009_Id}'");
			$rowD001 = mysql_fetch_assoc($sqlD001);
			
            $rowD001['D001_Codigo_Produto'] = mysql_real_escape_string(trim($rowD001['D001_Codigo_Produto']));
			$rowD001['D001_Descricao_Produto'] = mysql_real_escape_string(strtoupper(trim($rowD001['D001_Descricao_Produto'])));

			$D006_Id = $VEN001->retornaCFOP($T005_D024_Id, $D009_Id, $C004_Id);

			$demonstrativoCFOP = $VEN001->retornaCFOP($T005_D024_Id, $D009_Id, $C004_Id, false, false, true);
			$demonstrativoCFOP = mysql_real_escape_string($demonstrativoCFOP);
			
			// Calculo do custo do produto
			$listPreco            = mysql_query("SELECT T005_Flag_Lista_Preco, T005_D024_Id, T005_C004_Id, T005_Flag_ACP, T005_C004_Id_Estoque, T005_Casas_Decimais_Preco FROM T005 WHERE T005_Id='{$T005_Id}'");
			$mLista_Preco         = mysql_fetch_array($listPreco);
			$D024_Id              = $mLista_Preco['T005_D024_Id'];

			$Aliquotas            = $VEN001->retornaAliquotas($D009_Id, false, $T005_Id, false, false, false, false, $D006_Id);
			$dadosPrecoVenda      = $CAD002->retornaPrecoVenda($D009_Id, $mLista_Preco['T005_Flag_Lista_Preco'], $D024_Id, false, $D006_Id, $Aliquotas['Aliquota_ICMS'], false, false, $mLista_Preco['T005_Casas_Decimais_Preco'], $Aliquotas['Aliquota_PIS'], $Aliquotas['Aliquota_COFINS'], $Aliquotas['Aliquota_IPI'], $Aliquotas['ST_VA'], $Aliquotas['Percentual_Reducao_ICMS_ST'], $Aliquotas['ST_ICMS_Interno'], $Aliquotas['Percentual_Reducao_ICMS'], $Aliquotas['ICMS_Inter_Estadual'], $Aliquotas['Aliquota_IRPJ'], $Aliquotas['Aliquota_CSLL'], 0, 0, 0, $C004_Id);
			$custoUnitarioProduto = $dadosPrecoVenda[4];

			$insert = "INSERT INTO T006 (";
			$insert.= "T006_T005_Id, ";
			$insert.= "T006_D024_Id, ";
			$insert.= "T006_D006_Id, ";
			$insert.= "T006_D006_Id_Info, ";
			$insert.= "T006_D009_Id, ";
			$insert.= "T006_C004_Id, ";
			$insert.= "T006_D037_Id, ";
			$insert.= "T006_D005_Id, ";
			$insert.= "T006_Preco_Venda_Ecommerce, ";
			$insert.= "T006_Preco_Tabela_Ecommerce, ";
			$insert.= "T006_Valor_Desconto_Ecommerce, ";
			$insert.= "T006_Valor_Custo_Unitario, ";
			$insert.= "T006_Item, ";
			$insert.= "T006_Numero_Pedido_Compra, ";
			$insert.= "T006_Codigo_Produto, ";
			$insert.= "T006_Descricao_Produto, ";
			$insert.= "T006_Quantidade, ";
			$insert.= "T006_Valor_Preco_Sem_Desconto_Unitario, ";			
			$insert.= "T006_Flag_Destino_Produto,";
			$insert.= "T006_Valor_Total_Preco_Sem_Desconto, ";
			$insert.= "T006_Valor_Preco_Original, ";
			if ($this->retornaC031ValorPorEmpresa($C004_Id, 'precopadraoVendaProduto') == 'bruto') {
				$insert .= " T006_Valor_Preco_Bruto, ";
				$insert .= " T006_Valor_Preco_Original_Bruto, ";
			}
			$insert.= "T006_Percentual_Desconto";
			$insert.= ") VALUES (";
			$insert.= "'{$T005_Id}', ";
			$insert.= "'{$T005_D024_Id}', ";
			$insert.= "'{$D006_Id}', ";
			$insert.= "'{$demonstrativoCFOP}', ";
			$insert.= "'{$D009_Id}', ";
			$insert.= "'{$C004_Id}', ";
			$insert.= "'{$rowD001['D001_D037_Id']}', ";
			$insert.= "'{$rowD001['D001_D005_Id']}', ";
			$insert.= "'{$T006['T006_Preco_Venda_Ecommerce']}', ";
			$insert.= "'{$T006['T006_Preco_Tabela_Ecommerce']}', ";
			$insert.= "'{$T006['T006_Valor_Desconto_Ecommerce']}', ";
			$insert.= "'{$custoUnitarioProduto}', ";
			$insert.= "'{$count}', ";
			$insert.= "'{$T006_Numero_Pedido_Compra}', ";
			$insert.= "'{$rowD001['D001_Codigo_Produto']}', ";
			$insert.= "'{$rowD001['D001_Descricao_Produto']}', ";
			$insert.= "'{$T006["T006_Quantidade"]}', ";
			$insert.= "'{$T006["T006_Valor_Preco_Sem_Desconto_Unitario"]}', ";
			$destinoProdutoB2B = $this->retornaC031ValorPorEmpresa($C004_Id, 'destinoProdutoB2B');
			$insert.= "{$destinoProdutoB2B},";
			$insert.="'{$T006["T006_Valor_Total_Preco_Sem_Desconto"]}', ";
			$insert.="'{$T006["T006_Valor_Preco_Original"]}', ";
			if ($this->retornaC031ValorPorEmpresa($C004_Id, 'precopadraoVendaProduto') == 'bruto') {
				$insert.= "'{$T006["T006_Valor_Preco_Sem_Desconto_Unitario"]}', ";
				$insert.= "'{$T006["T006_Valor_Preco_Sem_Desconto_Unitario"]}', ";
			}
			$insert.="'{$T006["T006_Percentual_Desconto"]}'";
			$insert.= ")";

			mysql_query($insert);
			$T006_Id = $g['mysqlLastId'];
			$logImportacao .= "Gravando item do pedido (Item {$count})<br>";
			$erro = mysql_error();

			if(!empty($erro)){

				$rowD001['D001_Descricao_Produto'] = mysql_real_escape_string($rowD001['D001_Descricao_Produto']);
				mysql_query("ROLLBACK");
				file_put_contents($arquivoLogImportacao."_log", "\n[".date("d/m/Y H:i:s")."] Erro ao gravar item do pedido (Item: {$count})", FILE_APPEND);
				$logImportacao .= "Erro ao gravar item do pedido (Item {$count} - Descrição: {$rowD001['D001_Descricao_Produto']})<br>Erro: {$erro}<br>";			
				registrarLog(array("T005", '0', $C004_Id, "ID {$T005_Id_Pedido_Externo}", "Erro ao importar pedido <b>{$T005_Id_Pedido_Externo}</b>, houve um problema ao gravar item do pedido (Item {$count} - Descrição: {$rowD001['D001_Descricao_Produto']})", $logImportacao, "S", "2"));
				mysqli_query("UPDATE T245 SET T245_Flag_Erro = 'S' WHERE T245_Referencia = 'ID {$T005_Id_Pedido_Externo}' AND T245_Tipo_Log = '2'",$g['conexaoBandoAuditoria']);
				return false;
			}
			
		}
		
		$logImportacao .= "Atualizando alíquotas e CFOP do pedido<br>";

		$VEN002->ven002atualizarCFOP($T005_Id);
		$VEN002->atualizarAliquotasPedidos($T005_Id, $C004_Id);

		$T090 = (array) $T005['T090'];
		$PagamentosT090 = (array) $T005['T090'];

		foreach ($PagamentosT090 as $key => $T090) {

			$insert = "INSERT INTO T090 (";
			$insert.= "T090_T005_Id, ";
			$insert.= "T090_D027_Id, ";
			$insert.= "T090_Numero_Parcela, ";
			$insert.= "T090_Prazos_Dias, ";
			$insert.= "T090_Data_Vencimento, ";
			$insert.= "T090_Valor_Parcela ";
			$insert.= ") VALUES (";
			$insert.= "'{$T005_Id}', ";
			$insert.= "'{$T090["T090_D027_Id"]}', ";
			$insert.= "'{$T090["T090_Numero_Parcela"]}', ";
			$insert.= "'{$T090["T090_Prazos_Dias"]}', ";
			$insert.= "'{$T090["T090_Data_Vencimento"]}', ";
			$insert.= "'{$T090["T090_Valor_Parcela"]}'";
			$insert.= ")";

			mysql_query($insert);
			$logImportacao .= "Gravando parcelas do pedido (Parcela {T090_Numero_Parcela})<br>";
			$erro = mysql_error();
			if(!empty($erro)){
				mysql_query("ROLLBACK");
				file_put_contents($arquivoLogImportacao."_log", "\n[".date("d/m/Y H:i:s")."] Erro ao gravar parcela do pedido (Parcela: {T090_Numero_Parcela})", FILE_APPEND);
				$logImportacao .= "Erro ao gravar parcela do pedido (Parcela {T090_Numero_Parcela})<br>Erro: {$erro}<br>";			
				registrarLog(array("T005", '0', $C004_Id, "ID {$T005_Id_Pedido_Externo}", "Erro ao importar pedido <b>{$T005_Id_Pedido_Externo}</b>, houve um problema ao gravar parcela do pedido (Parcela {T090_Numero_Parcela})", $logImportacao, "S", "2"));
				mysqli_query("UPDATE T245 SET T245_Flag_Erro = 'S' WHERE T245_Referencia = 'ID {$T005_Id_Pedido_Externo}' AND T245_Tipo_Log = '2'",$g['conexaoBandoAuditoria']);
				return false;
			}

		}

		// //Gera as parcelas
		// $logImportacao .= "Gerando parcelas do pedido<br>";
		// require_once('bibliotecas/classes/GFormWrap.php');
		// $formPgto = new GFormWrap(array(
  //           'T005_Id' => $T005_Id,
  //           'Total_Parcelas' => '1',
  //           'IntervalorDias' => '28',
  //           'diaFixo' => '28',
  //           'primeiroVencimentoNum' => $T005_Pgto_Primeiro_Vcto_Valor,
  //           'primeiroVencimentoData' => $T005_Pgto_Primeiro_Vcto_Valor,
  //           'Portador_D027_Id' => $T005_Pgto_Portador,
  //           'selecionaPrimeiroVencimento' => 'Dias',
  //           'selecionaDias' => 'I',
  //           'usarFreteParcela' => 'OFF',
  //           'usarSTParcela' => 'OFF',
  //           'usarIPIParcela' => 'OFF'
  //       ));

  //       $VEN002->atualizarParcelas($formPgto);

        $logImportacao .= "Finalizando a importação do pedido<br>";
		// echo "<br><br>D024_Id: " . $D024_Id;
		// echo "<br>T005_Id: " . $T005_Id;
		mysql_query("COMMIT");
		mysql_query("UNLOCK ALL");

		$T006 = mysql_query("select T006_D009_Id from T006 where T006_T005_Id='{$T005_Id}'");
		while ($mT006 = mysql_fetch_array($T006)) {
			$CAD002->D001_reprocessa_historico(false, $mT006['T006_D009_Id'], 50);
			$logImportacao .= "Atualizando estoque loop após commit D009_Id = {$mT006['T006_D009_Id']}<br>";
		}

		$logImportacao .= "Transação finalizada";
		registrarLog(array("T005", $T005_Id, $C004_Id, "ID {$T005_Id_Pedido_Externo}", "Importação do pedido realizada com sucesso", $logImportacao, "N", "2"));
		mysqli_query("UPDATE T245 SET T245_Flag_Erro = 'R' WHERE T245_Referencia = 'ID {$T005_Id_Pedido_Externo}' AND T245_Tipo_Log = '2'",$g['conexaoBandoAuditoria']);

		if($T005_Flag_Plataforma_Ecommerce>=3 || $T005_Flag_Plataforma_Ecommerce<6){
			
			if(!empty($T005['MAGENTOSOAP_filtroUtilizado']))
			{
				$MAGENTOSOAP_filtroUtilizado = "Filtro utilizado: " . $T005['MAGENTOSOAP_filtroUtilizado'];
			}
			mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Pedido importado magento. $MAGENTOSOAP_filtroUtilizado','')");
		}
		
		return $T005_Id;
	}


	public function cadastrarOrcamento($T003) {

		global $g, $logImportacao;

		require_once("bibliotecas/classes/CAD002.php");
		$CAD002 = new CAD002();
		require_once("bibliotecas/classes/VEN001.php");
		$VEN001 = new VEN001();
		require_once("bibliotecas/classes/VEN002.php");
		$VEN002 = new VEN002();

		mysql_query("START TRANSACTION");

		$T003_Id_Pedido_Externo = $T003["T003_Id_Pedido_Externo"];

		//$Cliente = (array) $T003['D024'];
		$Cliente = (array) $T003['D024'];	

		$T003_D024_Id = $Cliente['D024_Id'];
		$T003_D013_Id = $Cliente['D013_Id'];
		$T003_D148_Id_Entrega = '';
		$T003_C004_Id = $g['empresaAtual'];
		$T003_C007_Id_Vendedor_Interno = $T003['C007_Id'];
		$T003_C007_Id_Vendedor_Externo = $T003['C007_Vendedor_Externo'];
		$T003_D021_Id = $T003['T003_D021_Id'];
		$T003_Data_Emissao = date("Y-m-d");
		$T003_Hora_Emissao = date("H:i:s");
		$T003_Valor_Desconto = 0;
		$T003_Valor_Total_Produtos = 0;
		$T003_Valor_Total_Produtos_Sem_Desconto = 0;
		$T003_Valor_Total = 0;
		$T003_Valor_Frete = 0;
		$T003_Flag_Status_Orcamento = 0;
		$T003_Flag_Lista_Preco = '1';
		$T003_Flag_Destino_Produto = '2';
		$T003_Prazo_Entrega = date("Y-m-d");
		$T003_D036_Id = 0;
		$T003_D022_Id = '';      
		$T003_Flag_Frete = 0;     
		$T003_Pgto_Primeiro_Vcto_Tipo = 'DIAS';
		$T003_Pgto_Primeiro_Vcto_Valor = '5';
		$T003_Pgto_Dias_Vcto_Tipo = 'INTERVALO';
		$T003_Pgto_Dias_Vcto_Valor = '28';
		$T003_D027_Id = '';
		$T003_Flag_Frete_Primeira_Parcela = 'N';
		$T003_Flag_ST_Primeira_Parcela = 'N';
		$T003_Flag_IPI_Primeira_Parcela = 'N';
		$T003_Pgto_Parcelas = '1';
		$T003_Observacao_Comercial = $T003['T003_Observacao_Comercial'];

		$insert = "INSERT INTO T003 (";
		$insert.= "T003_D024_Id, ";
		$insert.= "T003_D013_Id, ";
		$insert.= "T003_C004_Id, ";
		$insert.= "T003_C007_Id_Vendedor_Interno, ";
		$insert.= "T003_C007_Id_Vendedor_Externo, ";
		$insert.= "T003_Observacao_Comercial, ";
		$insert.= "T003_D021_Id, ";
		$insert.= "T003_D036_Id, ";
		$insert.= "T003_D022_Id, ";
		$insert.= "T003_Data_Emissao, ";
		$insert.= "T003_Prazo_Entrega, ";
		$insert.= "T003_Ordem_Compra, ";
		$insert.= "T003_Id_Pedido_Externo, ";
		$insert.= "T003_Valor_Desconto_Manual, ";
		$insert.= "T003_Valor_Total_Produtos, ";
		$insert.= "T003_Valor_Total_Produtos_Sem_Desconto, ";
		$insert.= "T003_Valor_Total, ";
		$insert.= "T003_Flag_Status_Orcamento, ";
		$insert.= "T003_Valor_Frete, ";
		$insert.= "T003_Flag_Frete, ";
		$insert.= "T003_Flag_Lista_Preco, ";
		$insert.= "T003_Flag_Destino_Produto, ";
		$insert.= "T003_Pgto_Primeiro_Vcto_Tipo, ";
		$insert.= "T003_Pgto_Primeiro_Vcto_Valor, ";
		$insert.= "T003_Pgto_Dias_Vcto_Tipo, ";
		$insert.= "T003_Pgto_Dias_Vcto_Valor, ";
		$insert.= "T003_D027_Id, ";
		$insert.= "T003_Flag_Frete_Primeira_Parcela, ";
		$insert.= "T003_Flag_ST_Primeira_Parcela, ";
		$insert.= "T003_Flag_IPI_Primeira_Parcela, ";
		$insert.= "T003_Pgto_Parcelas";
		$insert.= ") VALUES (";
		$insert.= "'{$T003_D024_Id}', ";
		$insert.= "'{$T003_D013_Id}', ";
		$insert.= "'{$T003_C004_Id}', ";
		$insert.= "'{$T003_C007_Id_Vendedor_Interno}', ";
		$insert.= "'{$T003_C007_Id_Vendedor_Externo}', ";
		$insert.= "'{$T003_Observacao_Comercial}', ";
		$insert.= "'{$T003_D021_Id}', ";
		$insert.= "'{$T003_D036_Id}', ";
		$insert.= "'{$T003_D022_Id}', ";
		$insert.= "'{$T003_Data_Emissao}', ";
		$insert.= "'{$T003_Prazo_Entrega}', ";
		$insert.= "'{$T003_Pedido_Ecommerce}', ";
		$insert.= "'{$T003_Id_Pedido_Externo}', ";
		$insert.= "'{$T003_Valor_Desconto}', ";
		$insert.= "'{$T003_Valor_Total_Produtos}', ";
		$insert.= "'{$T003_Valor_Total_Produtos_Sem_Desconto}', ";
		$insert.= "'{$T003_Valor_Total}', ";
		$insert.= "'{$T003_Flag_Status_Orcamento}', ";
		$insert.= "'{$T003_Valor_Frete}', ";
		$insert.= "'{$T003_Flag_Frete}', ";
		$insert.= "'{$T003_Flag_Lista_Preco}', ";
		$insert.= "'{$T003_Flag_Destino_Produto}', ";
		$insert.= "'{$T003_Pgto_Primeiro_Vcto_Tipo}', ";
		$insert.= "'{$T003_Pgto_Primeiro_Vcto_Valor}', ";
		$insert.= "'{$T003_Pgto_Dias_Vcto_Tipo}', ";
		$insert.= "'{$T003_Pgto_Dias_Vcto_Valor}', ";
		$insert.= "'{$T003_D027_Id}', ";
		$insert.= "'{$T003_Flag_Frete_Primeira_Parcela}', ";
		$insert.= "'{$T003_Flag_ST_Primeira_Parcela}', ";
		$insert.= "'{$T003_Flag_IPI_Primeira_Parcela}', ";
		$insert.= "'{$T003_Pgto_Parcelas}' ";
		$insert.= ")";

		mysql_query($insert);

		$logImportacao .= "Gravando cabeçalho do orçamento<br>";
		$erro = mysql_error();
		
		if(!empty($erro)){
			mysql_query("ROLLBACK");
			file_put_contents($arquivoLogImportacao."_log", "\n[".date("d/m/Y H:i:s")."] Erro ao gravar cabeçalho do orçamento", FILE_APPEND);
			$logImportacao .= "Erro ao gravar cabeçalho do orçamento<br>Erro: {$erro}<br>";
			registrarLog(array("T003", '0', $g['empresaAtual'], "ID {$T003_Id_Pedido_Externo}", "Erro ao importar pedido <b>{$T003_Id_Pedido_Externo}</b>, houve um problema ao gravar cabeçalho do orçamento", $logImportacao, "S", "2"));
			mysqli_query("UPDATE T245 SET T245_Flag_Erro = 'S' WHERE T245_Referencia = 'ID {$T003_Id_Pedido_Externo}' AND T245_Tipo_Log = '2'",$g['conexaoBandoAuditoria']);
			return false;
		}else{
			$T003_Id = $g['mysqlLastId'];
		}
		

		/*
			* Insere os produtos
			*/
		$logImportacao .= "Iniciando inclusão dos itens do pedido<br>";

		$ItensT004 = (array) $T003['T004'];

		$sql = "SELECT D024_Id, 
					   D024_C004_Id, 
					   D024_Flag_Lista_Preco, 
					   6 as Flag_ACP, 
					   1 as Flag_Destino_Produto 
				  FROM D024 
				 WHERE D024_Id = '{$g['clienteAtual']}'";

		$D024  = mysql_query($sql);
		$mD024 = mysql_fetch_assoc($D024);
		$count = 0;

		foreach ($ItensT004 as $key => $T004) {
			$count++;
			$D009_Id = $T004['D009_Id'];		

			$sqlD001 = mysql_query("SELECT D001_Codigo_Produto, 
										   D001_Descricao_Produto,
										   D001_D005_Id, 
										   D001_D037_Id 
									  FROM D001 
								 LEFT JOIN D049 ON D049_D001_Id=D001_Id 
								 LEFT JOIN D009 ON D009_D049_Id=D049_Id
								     WHERE D009_Id = '{$D009_Id}'");

			$rowD001 = mysql_fetch_assoc($sqlD001);
			
			$rowD001['D001_Codigo_Produto']    = mysql_real_escape_string(trim($rowD001['D001_Codigo_Produto']));
			$rowD001['D001_Descricao_Produto'] = mysql_real_escape_string(strtoupper(trim($rowD001['D001_Descricao_Produto'])));
			$D006_Id 						   = $VEN001->retornaCFOP($D024_Id, $D009_Id, $g['empresaAtual']);
			$demonstrativoCFOP 				   = $VEN001->retornaCFOP($D024_Id, $D009_Id, $g['empresaAtual'], false, false, true);
			$demonstrativoCFOP 				   = mysql_real_escape_string($demonstrativoCFOP);
			$D009_Id 					       = $T004['D009_Id'];
			//$cClausula = mysql_query("select * from D009 where D009_Id = '{$D009_Id}'");
			//$mD009 = mysql_fetch_array($cClausula);

			// *******************************************************************************************
			// CHAMADO RETORNA PREÇO VENDA APENAS PARA INSERIR O PREÇO TABELA QUE NÃO TEM NA TABELA T245
			// *******************************************************************************************
			$aliquotas 			     = $VEN001->retornaAliquotas($D009_Id, $g['clienteAtual'], false, false, false, false, false, $D006_Id);
			$preco 				     = $CAD002->retornaPrecoVenda($D009_Id, $mD024['D024_Flag_Lista_Preco'], $mD024['D024_Id'], false, $D006_Id, $aliquotas['Aliquota_ICMS'], false, false, 2, $aliquotas['Aliquota_PIS'], $aliquotas['Aliquota_COFINS'], $aliquotas['Aliquota_IPI'], $aliquotas['ST_VA'], $aliquotas['Percentual_Reducao_ICMS_ST'], $aliquotas['ST_ICMS_Interno'], $aliquotas['Percentual_Reducao_ICMS'], $aliquotas['ICMS_Inter_Estadual'], $aliquotas['Aliquota_IRPJ'], $aliquotas['Aliquota_CSLL']);
			$custoUnitarioProduto    = $preco[4];
			//$Preco_Tabelado 	     = $preco[8] > 0 ? gCorrigeNumeroInverte($preco[8]) : $T004["T004_Valor_Preco_Sem_Desconto_Unitario"];
			$Preco_Tabelado 	     = $T004["T004_Valor_Preco_Sem_Desconto_Unitario"]; // Está setado direto o preço 1, no momento pegar ele para não dar diferença
			$demonstrativoPrecoVenda = mysql_real_escape_string(serialize($preco[1]));

			if ($Preco_Tabelado > 0) {
				$T004_Percentual_Desconto     = ((1 - ($T004["T004_Valor_Preco_Sem_Desconto_Unitario"] / $Preco_Tabelado)) * 100);
	            $T004_Valor_Desconto_Unitario = (($Preco_Tabelado * ($T004["T004_Valor_Preco_Sem_Desconto_Unitario"] / 100)));
        	} else {
        		$T004_Percentual_Desconto     = '';
	            $T004_Valor_Desconto_Unitario = '';
        	}

			$insert = "INSERT INTO T004 (";
			$insert.= "T004_T003_Id, ";
			$insert.= "T004_D006_Id, ";
			$insert.= "T004_D006_Id_Info, ";
			$insert.= "T004_D009_Id, ";
			$insert.= "T004_C004_Id, ";
			$insert.= "T004_D037_Id, ";
			$insert.= "T004_D005_Id, ";
			$insert.= "T004_Item, ";
			$insert.= "T004_Numero_Pedido_Compra, ";
			$insert.= "T004_Codigo_Produto, ";
			$insert.= "T004_Descricao_Produto, ";
			$insert.= "T004_Quantidade, ";
			$insert.= "T004_Valor_Custo_Unitario, ";
			$insert.= "T004_Valor_Preco_Sem_Desconto_Unitario, ";	
			$insert.= "T004_Demonstrativo_Preco_Venda, ";
			$insert.= "T004_Flag_Destino_Produto,";
			$insert.= "T004_Valor_Total_Preco_Sem_Desconto, ";
			//Caso o preço de venda esteja como bruto vai adicionar o valor do produto na coluna de preço bruto
			if($g['C031']['precopadraoVendaProduto']=="liquido"){
				$insert.= "T004_Valor_Preco_Original, ";
				$insert.= "T004_Valor_Preco_Unitario,";
			}else{
				$insert.= "T004_Valor_Preco_Original_Bruto, ";
				$insert.= "T004_Valor_Preco_Bruto,";
			}
			$insert .= " T004_Valor_Desconto_Unitario, ";
	        $insert .= " T004_Percentual_Desconto_Bruto, ";
	        $insert .= " T004_Valor_Desconto_Unitario_Bruto ";
								
			$insert.= ") VALUES (";
			$insert.= "'{$T003_Id}', ";
			$insert.= "'{$D006_Id}', ";
			$insert.= "'{$demonstrativoCFOP}', ";
			$insert.= "'{$D009_Id}', ";
			$insert.= "'{$g['empresaAtual']}', ";
			$insert.= "'{$rowD001['D001_D037_Id']}', ";
			$insert.= "'{$rowD001['D001_D005_Id']}', ";
			$insert.= "'{$count}', ";
			$insert.= "'{$T003_Pedido_Ecommerce}', ";
			$insert.= "'{$rowD001['D001_Codigo_Produto']}', ";
			$insert.= "'{$rowD001['D001_Descricao_Produto']}', ";
			$insert.= "'{$T004['T004_Quantidade']}', ";
			$insert.= "'{$custoUnitarioProduto}', ";
			$insert.= "'{$Preco_Tabelado}', ";
			$insert.= "'{$demonstrativoPrecoVenda}', ";
			$insert.= "'{$g['C031']['destinoProdutoB2B']}', ";
			$insert.= "'{$T004['T004_Valor_Total_Preco_Sem_Desconto']}', ";
			$insert.= "'{$Preco_Tabelado}', ";
			$insert.= "'{$T004['T004_Valor_Preco_Sem_Desconto_Unitario']}', ";
			$insert.= "'{$T004_Valor_Desconto_Unitario}', ";
	        $insert.= "'{$T004_Percentual_Desconto}', ";
	        $insert.= "'{$T004_Valor_Desconto_Unitario}' ";
			$insert.= ")";

			mysql_query($insert);
			$logImportacao .= "Gravando item do pedido (Item {$count})<br>";
			$erro = mysql_error();
			if(!empty($erro)){
				log("Erro: ". $erro);
				$rowD001['D001_Descricao_Produto'] = mysql_real_escape_string($rowD001['D001_Descricao_Produto']);
				mysql_query("ROLLBACK");
				file_put_contents($arquivoLogImportacao."_log", "\n[".date("d/m/Y H:i:s")."] Erro ao gravar item do pedido (Item: {$count})", FILE_APPEND);
				$logImportacao .= "Erro ao gravar item do pedido (Item {$count} - Descrição: {$rowD001['D001_Descricao_Produto']})<br>Erro: {$erro}<br>";			
				registrarLog(array("T003", '0', $g['empresaAtual'], "ID {$T003_Id_Pedido_Externo}", "Erro ao importar pedido <b>{$T003_Id_Pedido_Externo}</b>, houve um problema ao gravar item do pedido (Item {$count} - Descrição: {$rowD001['D001_Descricao_Produto']})", $logImportacao, "S", "2"));
				mysqli_query("UPDATE T245 SET T245_Flag_Erro = 'S' WHERE T245_Referencia = 'ID {$T003_Id_Pedido_Externo}' AND T245_Tipo_Log = '2'",$g['conexaoBandoAuditoria']);
				return false;
			}else{
				$T004_Id = $g['mysqlLastId'];
				//UPDATE esta sendo executado para fim de chamar a trigger T004 afterUpdate 
				mysql_query("Update T004 set T004_Valor_Preco_Bruto=0 where T004_Id='{$T004_Id}'");	
				$VEN001->atualizarAliquotas($T003_Id, $g['empresaAtual'], $T004_Id);
			}
			//$CAD002->D001_reprocessa_historico(false, $D009_Id);
		}

		$logImportacao .= "Atualizando alíquotas e CFOP do pedido<br>";
		$VEN001->ven001atualizarCFOP($T003_Id);


		//Gera as parcelas
		$logImportacao .= "Gerando parcelas do pedido<br>";
		//ARRAY UTILIZADO NA FUNÇÃO ATUALIZAR PARCELAS
		$dadosTabela = array('tabela' => 'T003', 
							 'tabelaId' => 	$T003_Id,
							 'campos' => 'T003_Pgto_Primeiro_Vcto_Tipo as primeiroVencimentoTipo,
	                             T003_Pgto_Primeiro_Vcto_Valor as primeiroVencimentoNum,
	                             T003_Pgto_Primeiro_Vcto_Valor_Data as primeiroVencimentoData,
	                             T003_Pgto_Dias_Vcto_Tipo as diasVctoTipo,
	                             T003_Pgto_Dias_Vcto_Valor as valorIntervaloDiaFixo,
	                             T003_D027_Id as D027_Id,
	                             T003_Flag_Frete_Primeira_Parcela as campoUsarFreteParcela,
	                             T003_Flag_ST_Primeira_Parcela as campoUsarSTParcela,
	                             T003_Flag_IPI_Primeira_Parcela as campoUsarIPIParcela,
	                             T003_Pgto_Parcelas as Total_Parcelas,
	                             T003_Valor_Total_IPI as valorTotalIPI,
	                             T003_Valor_Total as valorTotal,
	                             T003_Valor_Frete as valorTotalFrete,
	                             T003_Valor_ST as valorTotalST', 
	                         'where' => "T003_Id='{$T003_Id}'", 
	                         'tabelaPgto' => 'T089', 
	                         'camposPgto' => 'T089_Prazos_Dias as prazoDias,T089_D027_Id as D027Pagamento,T089_Numero_Parcela as numeroParcela', 
	                         'wherePgto' => "T089_T003_Id='{$T003_Id}'");
			// Gerar Parcelas			
		$retorno = $VEN001->atualizarParcelas($dadosTabela);

		$logImportacao .= "Finalizando a importação do pedido<br>";
		// echo "<br><br>D024_Id: " . $D024_Id;
		// echo "<br>T005_Id: " . $T003_Id;
		mysql_query("COMMIT");
		mysql_query("UNLOCK ALL");
		$logImportacao .= "Transação finalizada";
		registrarLog(array("T003", $T003_Id, $g['empresaAtual'], "ID {$T003_Id_Pedido_Externo}", "Importação do pedido realizada com sucesso", $logImportacao, "N", "2"));
		mysqli_query("UPDATE T245 SET T245_Flag_Erro = 'R' WHERE T245_Referencia = 'ID {$T003_Id_Pedido_Externo}' AND T245_Tipo_Log = '2'",$g['conexaoBandoAuditoria']);

		return $T003_Id;
	}
}
