<?php
namespace hardness;
	/**
	* VEN - VEN001 - Cadastro de Orçamentos
	*
	* Processamento de ações AJAX
	*/
	$resposta = array('code' => true, 'data' => array());

	/**
	* Ações
	*/
	switch($g['r'][3]) {
		case 'atualizarCamposBens':
			$D106_Id = isset($r_D106_Id) ? $r_D106_Id : false;
			
			/*
			T004_Ultimo_Preco(D001_Id,'$mT003[T003_D024_Id]','$T003_Id',1) as Preco_Consultado, 
			T004_Ultimo_Preco(D001_Id,'$mT003[T003_D024_Id]','$T003_Id',2) as Preco_Confirmado 
			*/
			
			$sql =	"SELECT * from D106 WHERE D106_Id = '{$D106_Id}'";
			$resultado = mysql_query($sql);
			$row = mysql_fetch_assoc($resultado);
						
			
			if (!empty($row)) {
				$resposta['code'] = true;
				$resposta['data'] = $row;
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = "Erro SQL: $sql";
			}			

			// nao funciona direito, consertar para carregar todos os contratos deste produto
			$resposta['data']['D125_T141_Id'] = gProcessaSelect(gGeraSelect('T141','T141_Id','T141_Id,D024_Nome_Fantasia',"LEFT JOIN D024 ON D024_Id=T141_D024_Id LEFT JOIN T158 ON T158_D106_Id = T141_D106_Id WHERE T141_Flag_Status='A' AND T141_D106_Id='".$D106_Id."'",true));
   			
			break;
			
		case 'atualizarCamposServicos':
		global $g;
			$D112_Id = isset($_REQUEST['D112_Id']) ? $_REQUEST['D112_Id'] : false;
			$D112_Id = mysql_real_escape_string($D112_Id);

			$sql = <<<EOT
			SELECT 
				D112_Titulo,
	   			D112_Descricao,
	   			D112_Tempo_Estimado,
	   			D112_Valor_Unitario,
	   			D112_Id,
	   			D112_D110_Id,
	   			D110_Titulo
	   				from D112
   				left join D110 on D110_Id=D112_D110_Id
   				WHERE D112_Id = '{$D112_Id}'
EOT;
			$resultado = mysql_query($sql);
			$row = mysql_fetch_assoc($resultado);
			
			if (!empty($row)) {
				$resposta['code'] = true;
				$resposta['data'] = $row;
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = "Erro SQL: $sql";
			}
		break;
		
		case 'gerarPedido':
			require_once("bibliotecas/classes/VEN001.php");
			
			$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
			
			$VEN001 = new VEN001();
			
			$T005_Id = 0;
			$retorno = $VEN001->gerarPedido($T003_Id);
			
			if ($retorno === true) {
				$resposta['code'] = true;
				$resposta['data'] = "Pedido Gerado com sucesso!";
				$resposta['T005_Id'] = $T005_Id;
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
		break;
		
		case 'aplicarIPV':
			require_once("bibliotecas/classes/VEN001.php");
			
			$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
			$IPV = isset($r_IPV) ? $r_IPV : false;
			
			$IPV = str_replace('.', '', $r_IPV);
			$IPV = str_replace(',', '.', $IPV);
			
			$VEN001 = new VEN001();
			
			$retorno = $VEN001->aplicarIpvMinimo($T003_Id, $IPV);
			
			if ($retorno === true) {
				$resposta['code'] = true;
				$resposta['data'] = "IPV aplicado com sucesso!";
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
		break;
		
		
		case 'ImportarOrcamento':
			require_once("bibliotecas/classes/VEN001.php");
			
			$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
			$T003_Id_Origem = isset($r_T003_Id_Origem) ? $r_T003_Id_Origem : false;
			
			$VEN001 = new VEN001();
			
			$retorno = $VEN001->importarOrcamento($T003_Id, $T003_Id_Origem);
			
			if ($retorno === true) {
				$resposta['code'] = true;
				$resposta['data'] = "Orçamento importado com sucesso!";
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
		break;
		
		case 'atualizarAliquota':
			require_once("bibliotecas/classes/VEN001.php");
			
			$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
			
			$VEN001 = new VEN001();
			
			$retorno = $VEN001->atualizarAliquotas($T003_Id);
			
			if ($retorno === true) {
				$resposta['code'] = true;
				$resposta['data'] = "Atualizado!";
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
		break;
		
		case 'confirmarTudo':
			$T003_Id = isset($_REQUEST['T003_Id']) ? $_REQUEST['T003_Id'] : false;
			
			$sql =	"SELECT * from T004 WHERE T004_T003_Id = '{$T003_Id}'";
			mysql_query($sql);
			
			$sql = <<<EOT
 			update T004 set T004_Quantidade_Confirmada=T004_Quantidade;
EOT;
			$request = mysql_query($sql);

			
			if ($request) {
				$resposta['code'] = true;
			} else {
				$resposta['code'] = false;
				$resposta['data'] = "Erro SQL: $sql";
			}
   			
			break;
			
			case 'zerarTudo':
			$T003_Id = isset($_REQUEST['T003_Id']) ? $_REQUEST['T003_Id'] : false;
			
			$sql =	"SELECT * from T004 WHERE T004_T003_Id = '{$T003_Id}'";
			mysql_query($sql);
			
			$sql = <<<EOT
 			update T004 set T004_Quantidade_Confirmada=0;
EOT;
			$request = mysql_query($sql);

			
			if ($request) {
				$resposta['code'] = true;
			} else {
				$resposta['code'] = false;
				$resposta['data'] = "Erro SQL: $sql";
			}
   			
			break;
	}

	echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>