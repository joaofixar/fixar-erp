<?php
namespace hardness;

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'alteraEstado':
		$T284_D018_Id = isset($r_T284_D018_Id) ? $r_T284_D018_Id : false;
		$resposta['code'] = true;
		
		if(!$T284_D018_Id){
			$resposta['data']['T284_D020_Id'] = "<option value=''>...</option>";
		} else {
			$resposta['data']['T284_D020_Id'] = gProcessaSelect(gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade', 'WHERE D020_D018_Id = ' . $T284_D018_Id . ' ORDER BY D020_Nome_Cidade'));
			//Get the city IBGE code to fill the field T284_Cidade_Descarregamento_Codigo_IBGE
			$sql = "SELECT D020_Codigo_IBGE FROM D020 WHERE D020_D018_Id = " . $T284_D018_Id;
			$result = mysql_query($sql);
			$arrayDataD020 = mysql_fetch_assoc($result);
			echo mysql_error();
			$resposta['data']['T284_Cidade_Descarregamento_Codigo_IBGE'] = $arrayDataD020['D020_Codigo_IBGE'];
		}
	break;

	case 'alteraEstadoCabecalho':
		$T276_D018_Id_Carregamento = isset($r_T276_D018_Id_Carregamento) ? $r_T276_D018_Id_Carregamento : false;
		$resposta['code'] = true;

		if(!$T276_D018_Id_Carregamento){
			$resposta['data']['T276_D020_Id_Carregamento'] = "<option value=''>...</option>";
		} else {
			$resposta['data']['T276_D020_Id_Carregamento'] = gProcessaSelect(gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade', 'WHERE D020_D018_Id = ' . $T276_D018_Id_Carregamento . ' ORDER BY D020_Nome_Cidade'));
		}
	break;

	case 'alteraEstadoDescarregamento':
		$Novo_D018_Descarregamento = isset($r_Novo_D018_Descarregamento) ? $r_Novo_D018_Descarregamento : false;
		$resposta['code'] = true;

		if(!$Novo_D018_Descarregamento){
			$resposta['data']['Novo_D020_Descarregamento'] = "<option value=''>...</option>";
		} else {
			$resposta['data']['Novo_D020_Descarregamento'] = gProcessaSelect(gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade', 'WHERE D020_D018_Id = ' . $Novo_D018_Descarregamento . ' ORDER BY D020_Nome_Cidade'));
		}
	break;

	case 'carregaDadosNfeMdfe':
		$T284_T007_Id = isset($r_T284_T007_Id) ? $r_T284_T007_Id : false;
		$T276_Id = isset($r_T276_Id) ? $r_T276_Id : false;
		log("T276_Id no ajax carregaDadosNfeMdfe= ".$T276_Id);

		if (empty($T284_T007_Id)) {
			$resposta['code'] = false;
			$resposta['data']['erro'] = "NF-e não existe";
		} else {

			// $T276 = mysql_query("SELECT T276_Id 
			// 	                   FROM T276
			// 	              LEFT JOIN T284 ON T284_T276_Id=T276_Id
			// 	              LEFT JOIN T007 ON T284_T007_Id=T007_Id
			// 	                  WHERE T007_Id='{$T284_T007_Id}'
			// 	                    AND T276_Id='{$T276_Id}'");
			// log("SELECT T276_Id 
			// 	                   FROM T276
			// 	              LEFT JOIN T284 ON T284_T276_Id=T276_Id
			// 	              LEFT JOIN T007 ON T284_T007_Id=T007_Id
			// 	                  WHERE T007_Id='{$T284_T007_Id}'
			// 	                    AND T276_Id='{$T276_Id}'");
			// if(mysql_num_rows($T276) > 0){
			// 	$resposta['code'] = false;
			// 	$resposta['data']['erro'] = "Essa NF-e já está cadastrada";
			// }

			$sql = <<<EOT
				SELECT
					 T007_Chave_Acesso_Nfe,
					 T007_Total_Peso_Bruto,
					 T007_Peso_Bruto,
					 T007_Valor_Total,
					 D024_D018_Id,
                     D024_D020_Id,
                     D148_D020_Id,
                     D148_D018_Id,
                     D018_Codigo_IBGE,
                     D020_Codigo_IBGE,
					 T007_Numero_Nota_Fiscal,
					 D024_Nome_Empresa,
					 COALESCE(T007_D148_Id_Entrega, 0) AS UF_Entrega,
					 T007_D024_Id
				FROM T007
		   LEFT JOIN D024 ON T007_D024_Id = D024_Id
		   LEFT JOIN D148 ON T007_D148_Id_Entrega = D148_Id
           LEFT JOIN D020 ON D024_D020_Id = D020_Id
           LEFT JOIN D018 ON D024_D018_Id = D018_Id   
		   LEFT JOIN T284 ON T284_T007_Id = T007_Id 
           LEFT JOIN T276 ON T284_T276_Id = T276_Id
			   WHERE T007_Id = '{$T284_T007_Id}'
                 AND (T276_Flag_Cancelada IS NULL OR T276_Flag_Cancelada != 'N')
                 AND (T276_Flag_Encerrada IS NULL OR T276_Flag_Encerrada != 'S')
EOT;
            log("SQL: ".$sql); 
			$result = mysql_query($sql);
			$arrayDataT007 = mysql_fetch_assoc($result);
			if($arrayDataT007['UF_Entrega'] > 0){ //se houver endereço de entrega cadastrado
                $D148 = mysql_query("SELECT D018_UF, 
                                            D018_Id,
                                            D018_Codigo_IBGE,
                                            D020_Nome_Cidade,
                                            D020_Codigo_IBGE,
                                            D020_Id
                                       FROM D018
                                  LEFT JOIN D020 ON D020_D018_Id=D018_Id
                                  LEFT JOIN D148 ON D148_D018_Id=D018_Id
                                      WHERE D148_Id='{$arrayDataT007['UF_Entrega']}'
                                        AND D018_Id='{$arrayDataT007['D148_D018_Id']}'
                                        AND D020_Id='{$arrayDataT007['D148_D020_Id']}'");
                $mD148 = mysql_fetch_array($D148);
                $Id_UF_Descarregamento = $mD148['D018_Id'];
                $sigla_UF_Descarregamento = $mD148['D018_UF']; 
                $Id_Cidade_Descarregamento = $mD148['D020_Id'];
                $nomeCidadeDescarregamento = $mD148['D020_Nome_Cidade'];
                $IBGE_Cidade_Descarregamento = $mD148['D020_Codigo_IBGE'];
                if($count == 0){
                    $UFtemporaria = $mD148['D018_UF'];
                    $count++;
                }                  
            }else{ //se não houver endereço de entrega, pega do cadastro do cliente
                $Id_UF_Descarregamento = $arrayDataT007['D024_D018_Id'];
                $sigla_UF_Descarregamento = $arrayDataT007['D018_UF'];
                $Id_Cidade_Descarregamento = $arrayDataT007['D024_D020_Id'];
                $nomeCidadeDescarregamento = $arrayDataT007['D020_Nome_Cidade'];
                $IBGE_Cidade_Descarregamento = $arrayDataT007['D020_Codigo_IBGE'];
                if($count == 0){
                    $UFtemporaria = $arrayDataT007['D018_UF'];
                    $count++;
                }
            }

			echo mysql_error();

			// Será utilizado o preço que estiver preenchido no formulário, se ele for maior que 0
			if($arrayDataT007['T007_Peso_Bruto'] > 0){
				$T284_Peso_Bruto_Total = gCorrigeNumero($arrayDataT007['T007_Peso_Bruto']);
			}else{
				$T284_Peso_Bruto_Total = gCorrigeNumero($arrayDataT007['T007_Total_Peso_Bruto']);
			}

			// Se a chave for vazia, essa NF ja esta em um MDFe.
			if(empty($arrayDataT007['T007_Chave_Acesso_Nfe'])) {
				// Busca em qual MDFe esta a NF
				$result = mysql_query("SELECT T284_T276_Id, T276_Numero_MDFe FROM T007 LEFT JOIN T284 ON T284_T007_Id = T007_Id LEFT JOIN T276 ON T284_T276_Id = T276_Id WHERE T007_Id = '{$T284_T007_Id}'");
				$mT007 = mysql_fetch_assoc($result);

				$resposta['data']['T284_T276_Id'] = $mT007['T284_T276_Id'];
				$resposta['data']['T276_Numero_MDFe'] = $mT007['T276_Numero_MDFe'];
			}

			$resposta['data']['T284_Chave_De_Acesso_NFe'] = $arrayDataT007['T007_Chave_Acesso_Nfe'];
			$resposta['data']['T284_Peso_Bruto_Total'] =  $T284_Peso_Bruto_Total;
			$resposta['data']['T284_Valor_Carga'] = gCorrigeNumero($arrayDataT007['T007_Valor_Total']);
			$resposta['data']['T284_Numero_NFe'] = $arrayDataT007['T007_Numero_Nota_Fiscal'];
			$resposta['data']['T284_Destinatario_Nome'] = $arrayDataT007['D024_Nome_Empresa'];
 			$resposta['data']['T284_D018_Id'] = gProcessaSelect(gGeraSelect('D018', 'D018_Id', 'D018_UF', 'WHERE D018_Id = ' . $Id_UF_Descarregamento . ' ORDER BY D018_UF'));				
 			$resposta['data']['T284_D020_Id'] = gProcessaSelect(gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade', 'WHERE D020_Id = ' . $Id_Cidade_Descarregamento . ' ORDER BY D020_Nome_Cidade'));				
			$resposta['data']['T284_Cidade_Descarregamento'] = $nomeCidadeDescarregamento;
			$resposta['data']['T284_Cidade_Descarregamento_Codigo_IBGE'] = $IBGE_Cidade_Descarregamento;
		}
	break;

	case 'carregaDadosMotorista':
		$D109_Id = isset($r_T278_D109_Id) ? $r_T278_D109_Id : false;
		$sqlD109 = mysql_query("SELECT D109_Nome, D109_Cpf FROM D109 WHERE D109_Id = " . "\"" . $D109_Id . "\"");
		$arrayD109 = mysql_fetch_assoc($sqlD109);

		echo mysql_error();

		$resposta['data']['T278_Nome'] = $arrayD109['D109_Nome'];
		$resposta['data']['T278_CPF'] = $arrayD109['D109_Cpf'];
	break;

	case 'carregaSeguradora':
		$D172_Id = isset($r_T279_D172_Id) ? $r_T279_D172_Id : false;

		$sqlD172 = mysql_query("SELECT D172_Nome, D172_CNPJ FROM D172 WHERE D172_Id = " . "\"" . $D172_Id . "\"");
		$arrayD172 = mysql_fetch_assoc($sqlD172);

		echo mysql_error();

		$resposta['data']['T279_Seguradora_Nome'] = $arrayD172['D172_Nome'];
		$resposta['data']['T279_Seguradora_CNPJ'] = $arrayD172['D172_CNPJ'];
	break;

	case 'verificaVeiculo':
	log("entra no ajax de verifica veiculo");
		global $g;
		
		$D170_Id = isset($r_T283_D170_Id) ? $r_T283_D170_Id : false;

		$result = mysql_query("SELECT D170_Placa,
									  D170_Renavam,
									  D170_Tara,
									  D170_Capacidade_Kg,
									  D170_Capacidade_M3,
									  D170_Flag_Rodado,
									  D170_Flag_Carroceria,
									  D170_D018_Id,
									  D170_Flag_Carro_Proprio,
									  D170_Categoria,
									  D170_Identificacao_Veiculo
		                         FROM D170 
		                        WHERE D170_Id='{$D170_Id}'");
		$D170 = mysql_fetch_assoc($result);


		$mD170_Id = trim($D170_Id);
		$D170_Placa = trim($D170['D170_Placa']);
		$D170_Renavam = trim($D170['D170_Renavam']);
		$D170_Tara = trim($D170['D170_Tara']);
		$D170_Capacidade_Kg = trim($D170['D170_Capacidade_Kg']);
		$D170_Capacidade_M3 = trim($D170['D170_Capacidade_M3']);
		$D170_Flag_Rodado = trim($D170['D170_Flag_Rodado']);
		$D170_Flag_Carroceria = trim($D170['D170_Flag_Carroceria']);
		$D170_D018_Id = trim($D170['D170_D018_Id']);
		$D170_Categoria = trim($D170['D170_Categoria']);
		$D170_Identificacao_Veiculo = trim($D170['D170_Identificacao_Veiculo']);
		
		$resposta['data']['T283_Placa'] = $D170_Placa;
		$resposta['data']['T283_Renavam'] = $D170_Renavam;
		$resposta['data']['T283_Tara'] = $D170_Tara;
		$resposta['data']['T283_Capacidade_Kg'] = $D170_Capacidade_Kg;
		$resposta['data']['T283_Capacidade_M3'] = $D170_Capacidade_M3;
		$resposta['data']['T283_Identificacao_Veiculo'] = $D170_Identificacao_Veiculo;

		//tip rodado
		if($D170_Flag_Rodado == '1'){
			$resposta['data']['T283_Flag_Rodado'] = gProcessaSelect(
				array(
					array('title' => 'Truck', 'value' => '1', 'selected' => true),   
					array('title' => 'Toco', 'value' => '2'),   
					array('title' => 'Cavalo Mecânico', 'value' => '3'),   
					array('title' => 'Van', 'value' => '4'),
					array('title' => 'Utilitário', 'value' => '5'),
					array('title' => 'Outros', 'value' => '6'),   
				));
		}else if($D170_Flag_Rodado == '2'){
			$resposta['data']['T283_Flag_Rodado'] = gProcessaSelect(
				array(
					array('title' => 'Truck', 'value' => '1'),   
					array('title' => 'Toco', 'value' => '2', 'selected' => true),   
					array('title' => 'Cavalo Mecânico', 'value' => '3'),   
					array('title' => 'Van', 'value' => '4'),
					array('title' => 'Utilitário', 'value' => '5'),
					array('title' => 'Outros', 'value' => '6'),   
				));
		}else if($D170_Flag_Rodado == '3'){
			$resposta['data']['T283_Flag_Rodado'] = gProcessaSelect(
				array(
					array('title' => 'Truck', 'value' => '1'),   
					array('title' => 'Toco', 'value' => '2'),   
					array('title' => 'Cavalo Mecânico', 'value' => '3', 'selected' => true),   
					array('title' => 'Van', 'value' => '4'),
					array('title' => 'Utilitário', 'value' => '5'),
					array('title' => 'Outros', 'value' => '6'),   
				));
		}else if($D170_Flag_Rodado == '4'){
			$resposta['data']['T283_Flag_Rodado'] = gProcessaSelect(
				array(
					array('title' => 'Truck', 'value' => '1'),   
					array('title' => 'Toco', 'value' => '2'),   
					array('title' => 'Cavalo Mecânico', 'value' => '3'),   
					array('title' => 'Van', 'value' => '4', 'selected' => true),
					array('title' => 'Utilitário', 'value' => '5'),
					array('title' => 'Outros', 'value' => '6'),   
				));
		}else if($D170_Flag_Rodado == '5'){
			$resposta['data']['T283_Flag_Rodado'] = gProcessaSelect(
				array(
					array('title' => 'Truck', 'value' => '1'),   
					array('title' => 'Toco', 'value' => '2'),   
					array('title' => 'Cavalo Mecânico', 'value' => '3'),   
					array('title' => 'Van', 'value' => '4'),
					array('title' => 'Utilitário', 'value' => '5', 'selected' => true),
					array('title' => 'Outros', 'value' => '6'),   
				));
		}else if($D170_Flag_Rodado == '6'){
			$resposta['data']['T283_Flag_Rodado'] = gProcessaSelect(
				array(
					array('title' => 'Truck', 'value' => '1'),   
					array('title' => 'Toco', 'value' => '2'),   
					array('title' => 'Cavalo Mecânico', 'value' => '3'),   
					array('title' => 'Van', 'value' => '4'),
					array('title' => 'Utilitário', 'value' => '5'),
					array('title' => 'Outros', 'value' => '6', 'selected' => true),   
				));
		}else{
			$resposta['data']['T283_Flag_Rodado'] = gProcessaSelect(
				array(
					array('title' => 'Truck', 'value' => '1', 'selected' => true),   
					array('title' => 'Toco', 'value' => '2'),   
					array('title' => 'Cavalo Mecânico', 'value' => '3'),   
					array('title' => 'Van', 'value' => '4'),
					array('title' => 'Utilitário', 'value' => '5'),
					array('title' => 'Outros', 'value' => '6'),   
				));
		}

		//carroceria
		if($D170_Flag_Carroceria == '0'){
			$resposta['data']['T283_Flag_Carroceria'] = gProcessaSelect(
				array(
					array('title' => '...', 'value' => ''),  
					array('title' => 'Não aplicável', 'value' => '00', 'selected' => true),   
					array('title' => 'Aberta', 'value' => '01'),   
					array('title' => 'Fechada/Baú', 'value' => '02'),   
					array('title' => 'Graneleira', 'value' => '03'),
					array('title' => 'Porta-conteiner', 'value' => '04'),
					array('title' => 'Sider', 'value' => '05'),   
				));
		}else if($D170_Flag_Carroceria == '1'){
			$resposta['data']['T283_Flag_Carroceria'] = gProcessaSelect(
				array(
					array('title' => '...', 'value' => ''),  
					array('title' => 'Não aplicável', 'value' => '00'),   
					array('title' => 'Aberta', 'value' => '01', 'selected' => true),   
					array('title' => 'Fechada/Baú', 'value' => '02'),   
					array('title' => 'Graneleira', 'value' => '03'),
					array('title' => 'Porta-conteiner', 'value' => '04'),
					array('title' => 'Sider', 'value' => '05'),   
				));
		}else if($D170_Flag_Carroceria == '2'){
			$resposta['data']['T283_Flag_Carroceria'] = gProcessaSelect(
				array(
					array('title' => '...', 'value' => ''),  
					array('title' => 'Não aplicável', 'value' => '00'),   
					array('title' => 'Aberta', 'value' => '01'),   
					array('title' => 'Fechada/Baú', 'value' => '02', 'selected' => true),   
					array('title' => 'Graneleira', 'value' => '03'),
					array('title' => 'Porta-conteiner', 'value' => '04'),
					array('title' => 'Sider', 'value' => '05'),   
				));
		}else if($D170_Flag_Carroceria == '3'){
			$resposta['data']['T283_Flag_Carroceria'] = gProcessaSelect(
				array(
					array('title' => '...', 'value' => ''),  
					array('title' => 'Não aplicável', 'value' => '00'),   
					array('title' => 'Aberta', 'value' => '01'),   
					array('title' => 'Fechada/Baú', 'value' => '02'),   
					array('title' => 'Graneleira', 'value' => '03', 'selected' => true),
					array('title' => 'Porta-conteiner', 'value' => '04'),
					array('title' => 'Sider', 'value' => '05'),   
				));
		}else if($D170_Flag_Carroceria == '4'){
			$resposta['data']['T283_Flag_Carroceria'] = gProcessaSelect(
				array(
					array('title' => '...', 'value' => ''),  
					array('title' => 'Não aplicável', 'value' => '00'),   
					array('title' => 'Aberta', 'value' => '01'),   
					array('title' => 'Fechada/Baú', 'value' => '02'),   
					array('title' => 'Graneleira', 'value' => '03'),
					array('title' => 'Porta-conteiner', 'value' => '04', 'selected' => true),
					array('title' => 'Sider', 'value' => '05'),   
				));
		}else if($D170_Flag_Carroceria == '5'){
			$resposta['data']['T283_Flag_Carroceria'] = gProcessaSelect(
				array(
					array('title' => '...', 'value' => ''),  
					array('title' => 'Não aplicável', 'value' => '00'),   
					array('title' => 'Aberta', 'value' => '01'),   
					array('title' => 'Fechada/Baú', 'value' => '02'),   
					array('title' => 'Graneleira', 'value' => '03'),
					array('title' => 'Porta-conteiner', 'value' => '04'),
					array('title' => 'Sider', 'value' => '05', 'selected' => true),   
				));				
		}else{
			$resposta['data']['T283_Flag_Carroceria'] = gProcessaSelect(
				array(
					array('title' => '...', 'value' => '','selected' => true),  
					array('title' => 'Não aplicável', 'value' => '00'),   
					array('title' => 'Aberta', 'value' => '01'),   
					array('title' => 'Fechada/Baú', 'value' => '02'),   
					array('title' => 'Graneleira', 'value' => '03'),
					array('title' => 'Porta-conteiner', 'value' => '04'),
					array('title' => 'Sider', 'value' => '05'),   
				));
		}


		//categoria
		if($D170_Categoria == '1'){
			$resposta['data']['T283_Categoria_Veiculo'] = gProcessaSelect(
				array(
					array('title' => '...', 'value' => ''),  
					array('title' => 'Reboque', 'value' => '1','selected' => true),   
					array('title' => 'Tração', 'value' => '2'),    
				));	
		}else if($D170_Categoria == '2'){
			$resposta['data']['T283_Categoria_Veiculo'] = gProcessaSelect(
				array(
					array('title' => '...', 'value' => ''),  
					array('title' => 'Reboque', 'value' => '1'),   
					array('title' => 'Tração', 'value' => '2','selected' => true),    
				));	
		}else{
			$resposta['data']['T283_Categoria_Veiculo'] = gProcessaSelect(
				array(
					array('title' => '...', 'value' => '','selected' => true),  
					array('title' => 'Reboque', 'value' => '1'),   
					array('title' => 'Tração', 'value' => '2'),    
				));	
		}
		$resposta['data']['T283_UF_Licenciamento'] = gProcessaSelect(gGeraSelect('D018','D018_UF', 'D018_UF', 'WHERE D018_Id = '.$D170_D018_Id.''));
	break;

	case 'finalizarMDFe':
		require_once("bibliotecas/classes/FIS012.php");

		$T276_Id = isset($r_T276_Id) ? $r_T276_Id : false;
		$resposta['T276_Id'] = $T276_Id;
		$FIS012 = new FIS012();

		$retorno = $FIS012->finalizarMDFe($T276_Id);
		
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'habilitarEdicaoMDFe':
		$T276_Id = isset($r_T276_Id) ? $r_T276_Id : false;
		$T276=mysql_query("SELECT T276_Chave_Acesso_MDFe from T276 WHERE T276_Id='{$T276_Id}'");
		$mT276=mysql_fetch_array($T276);
		$erro='';
		if(!empty($mT276['T276_Chave_Acesso_MDFe'])){
			$erro = 'O MDF-e já possui chave de acesso, para habilitar a edição favor acessar a aba "técnico" e apagar a chave de acesso preenchida';
		} else {
			mysql_query("UPDATE T276 SET T276_Flag_Travar_MDFe = 'N' WHERE T276_Id = '{$T276_Id}'");
			if(mysql_error()){
				$erro = 'Erro ao habilitar a nota para edição, por favor entre em contato com o suporte técnico. Erro: ' . mysql_error();
			}
		}
		if(!empty($erro)){
			$resposta['data'] = $erro;
			$resposta['code'] = false;
		} else {
			$resposta['code'] = true;
		}
	break;

	case 'estadoDescarregamento':
		$T276_D018_Id_Descarregamento = isset($r_T276_D018_Id_Descarregamento) ? $r_T276_D018_Id_Descarregamento : false;
		$resposta['code'] = true;

		if(!$T276_D018_Id_Descarregamento){
			$resposta['data']['T276_D020_Id_Descarregamento'] = "<option value=''>...</option>";
		} else {
			$resposta['data']['T276_D020_Id_Descarregamento'] = gProcessaSelect(gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade', 'WHERE D020_D018_Id = ' . $T276_D018_Id_Descarregamento . ' ORDER BY D020_Nome_Cidade'));
		}
	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";

?>