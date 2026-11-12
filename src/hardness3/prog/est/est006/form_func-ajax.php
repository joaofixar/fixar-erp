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
		
 case 'atualizarCamposProdutos':
			$D009_Id = isset($_REQUEST['D009_Id']) ? $_REQUEST['D009_Id'] : false;

			/*
			T012_Ultimo_Preco(D001_Id,'$mT003[T003_D024_Id]','$T003_Id',1) as Preco_Consultado, 
			T012_Ultimo_Preco(D001_Id,'$mT003[T003_D024_Id]','$T003_Id',2) as Preco_Confirmado 
			*/
			$sql = <<<EOT
			SELECT 
				*
	   				from D001
	   			left join D049 on D049_D001_Id=D001_Id
	   			left join D009 on D009_D049_Id=D049_Id
	   			left join D082 on D082_Id=D049_D082_Id
	   				WHERE D009_Id = '{$D009_Id}'
EOT;
			$resultado = mysql_query($sql);
			$rowD001 = mysql_fetch_assoc($resultado);
			// Substitue os campos
			/*
			echo "parent.document.getElementById('T012_Codigo_Substituto').value        = '$T012_Codigo_Substituto';";
			echo "parent.document.getElementById('T012_D037_Id').value                  = '$mD001[D001_D037_Id]';";
			echo "parent.document.getElementById('T012_Descricao_Produto').value        = '$mD001[D001_Descricao_Produto]';";
			echo "parent.document.getElementById('T012_Codigo_Produto').value           = '$mD001[D001_Codigo_Produto]';";
			echo "parent.document.getElementById('HT012_Peso_Unitario').value           = '".number_format($mD001[D001_Peso_Unitario_Kg],3,',','.')."';";
			echo "parent.document.getElementById('T012_Peso_Unitario').value            = '".number_format($mD001[D001_Peso_Unitario_Kg],3,',','.')."';";
			echo "parent.document.getElementById('T012_Ultimo_Preco').value             = '$mD001[Preco_Consultado]';";
			echo "parent.document.getElementById('HT012_Ultimo_Preco').value            = '$mD001[Preco_Consultado]';";
			echo "parent.document.getElementById('T012_Ultimo_Preco_Confirmado').value  = '$mD001[Preco_Confirmado]';";
			echo "parent.document.getElementById('HT012_Ultimo_Preco_Confirmado').value = '$mD001[Preco_Confirmado]';";
			echo "parent.document.getElementById('DivLoading').style.display = 'none';";
			echo "parent.PreencheCampos($mD001[D009_Id]);";
			*/

			$sql = <<<EOT
				select * 
					from D009 
				left join D049 on D049_Id=D009_D049_Id 
				left join D082 on D082_Id=D049_D082_Id 
				left join D001 on D001_Id=D049_D001_Id 
					where D009_Id='$D009_Id'
EOT;
			$resultado = mysql_query($sql);
			$rowD009 = mysql_fetch_assoc($resultado);
			// Substitue os campos
			/*
			echo "parent.document.getElementById('Marca').value='$mD009[D082_Marca]';";
			echo "parent.document.getElementById('T012_Valor_Preco_Sem_Desconto_Unitario').value='".number_format($mD009[Preco_Reais],$mC004[C004_Casas_Decimais_Preco],',','.')."';";
			$IPV=round($mD009[Preco_Reais]/$mD009[Custo_Reais],4);
		   	echo "parent.document.getElementById('T012_IPV').value='".number_format($IPV,4,',','.')."';";
		   	echo "parent.document.getElementById('HT012_IPV').value='".number_format($IPV,4,',','.')."';";
			*/
			$row = array_merge($rowD001, $rowD009);
			//$row['T012_Valor_Custo_Unitario'] = number_format($row['Preco_Reais'], $row['C004_Casas_Decimais_Preco'],',','.');
			//$row['IPV'] = round($row['Preco_Reais']/$row['Custo_Reais'], 4);
			
			if (!empty($row)) {
				$resposta['code'] = true;
				$resposta['data'] = $row;
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = "Erro SQL: $sql";
			}
   			
			break;
			
		case 'aplicarIPICadastro':
			require_once("bibliotecas/classes/EST006.php");
			
			$T011_Id = isset($r_T011_Id) ? $r_T011_Id : false;
			
			
			$EST006 = new EST006();
			
			$retorno = $EST006->aplicarIPICad($T011_Id);
			
			if ($retorno === true) {
				$resposta['code'] = true;
				$resposta['data'] = "IPI aplicado com sucesso!";
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
		break;
		
		case 'est006ImprimirOrdemCompra':
			require_once("bibliotecas/classes/EST006.php");
			$EST006 = new EST006();
			
			$T011_Id = isset($r_T011_Id) ? $r_T011_Id : false;
			
			$retorno = $EST006->montarEmailPdf($T011_Id);
			$resposta['code'] = true;
			$resposta['data'] = isset($retorno['AnexosWeb'][0]) ? $retorno['AnexosWeb'][0] : '';
		break;
		
	case 'inserirParcela':
		require_once("bibliotecas/classes/EST006.php");
		
		$form=$r_est006FormPagamento;		
		$EST006 = new EST006();		
		$retorno = $EST006->inserirParcela($form);
		
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = 'Falha na inclusão da parcela...<br>'.$retorno;
		}
	break;
	
	case 'atualizarCamposFornecedores':
		$D024_Id = (isset($r_D024_Id)) ? $r_D024_Id : false;
		
		$sql = "SELECT D024_Nome_Empresa FROM D024 WHERE D024_Id='{$D024_Id}'";
		$result = mysql_query($sql);
		$linha = mysql_fetch_array($result);
		
		if (!empty($result)) {
			$resposta['code'] = true;
			$resposta['data'] = $linha['D024_Nome_Empresa'];
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = "Erro SQL: $sql";
		}
	break;	

	case 'est006GerarNotaFiscal':
		$T011_Id = (isset($r_T011_Id)) ? $r_T011_Id : false;
		
		require_once("bibliotecas/classes/EST006.php");
		$EST006 = new EST006();
		
		$T011_Id = isset($r_T011_Id) ? $r_T011_Id : false;
		
		$retorno = $EST006->gerarNotaFiscalOC($T011_Id);
		
		if ($retorno===true) {
			$resposta['code'] = true;
			$resposta['data'] = "Nota fiscal gerada com sucesso.";
			$resposta['T013_Id'] = $T013_Id;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
		
	}

	echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>