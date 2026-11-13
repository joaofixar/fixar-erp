<?php
namespace hardness;
require_once("bibliotecas/classes/FIN007.php");

$resposta = array('code' => true, 'data' => array());
	
switch($g['r'][3]) {
	
	
	case 'validaCampoLiberar':
		require_once("bibliotecas/classes/FIN007.php");
		$T005_Id = isset($_REQUEST['T005_Id']) ? $_REQUEST['T005_Id'] : false;
		$T005_Id = mysql_real_escape_string($T005_Id);
		
		$FIN007 = new FIN007();
		
		$retorno = $FIN007->validaCampoLiberar($T005_Id);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Pedido Liberado com sucesso";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'validarCamposNFe':
		
		if ($g['C031']['validarNFEAnaliseDeCredito']=='S'){	
			require_once("bibliotecas/validadorNFe/validadorNFe.php");

			$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;

			$validadorNFe = new validadorNFe();

			$mensagem = '';
			$retorno = $validadorNFe->validadorNFe(null, $T005_Id, null);

		}else{
			$mensagem = '';
			$retorno = true;
		}

		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = $mensagem;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}

	break;
	
	case 'fin007ValidaCampoRetornarAlmx':
		$T005_Id = isset($_REQUEST['T005_Id']) ? $_REQUEST['T005_Id'] : false;
		$T005_Id = mysql_real_escape_string($T005_Id);
		
		$FIN007 = new FIN007();
		
		$retorno = $FIN007->validaCampoRetornarAlmx($T005_Id);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Pedido Retornado com sucesso!";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'fin007CheckboxAguardar':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T005_Id = isset($_REQUEST['T005_Id']) ? $_REQUEST['T005_Id'] : false;
		$FIN007 = new FIN007();
		$FIN007->updateCheckboxAguardar($T005_Id, $selected);
	break;
	
	case 'fin007CheckboxProtestar':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$D024_Id = isset($_REQUEST['D024_Id']) ? $_REQUEST['D024_Id'] : false;
		mysql_query("UPDATE D024 SET D024_Flag_Protestar = '{$selected}' WHERE D024_Id = '{$D024_Id}'");
		$resposta['code'] = true;
	break;

	case 'validaCampoLiberarNota':
		$T007_Id = isset($_REQUEST['T007_Id']) ? $_REQUEST['T007_Id'] : false;
		$T007_Id = mysql_real_escape_string($T007_Id);
		
		$sql = "UPDATE T007 SET T007_Flag_Analise_Credito = 2 WHERE T007_Id = '{$T007_Id}'";
		mysql_query($sql);

		$resposta['code'] = true;
		$resposta['data'] = "Nota liberada com sucesso";
	break;

	case 'validaCampoNaoLiberarNota':
		$T007_Id = isset($_REQUEST['T007_Id']) ? $_REQUEST['T007_Id'] : false;
		$T007_Id = mysql_real_escape_string($T007_Id);
		
		$sql = "UPDATE T007 SET T007_Flag_Analise_Credito = 3 WHERE T007_Id = '{$T007_Id}'";
		mysql_query($sql);

		$resposta['code'] = true;
		$resposta['data'] = "Nota não liberada";
	break;
	
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>