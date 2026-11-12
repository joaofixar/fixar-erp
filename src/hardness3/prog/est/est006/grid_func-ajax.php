<?php
namespace hardness;
	/**
	* Weiberlan
	* 29/11/2011
	*/
//require_once('bibliotecas/classes/est006.php');
//$est006 = new est006();

$resposta = array('code' => true, 'data' => array());

	switch($g['r'][3]) {
		case 'excluir':
			$T011_Id = isset($_REQUEST['T011_Id']) ? $_REQUEST['T011_Id'] : false;
			if($T011_Id){
				mysql_query("DELETE FROM T012 WHERE T012_T011_Id = '{$T011_Id}'");
				mysql_query("DELETE FROM T011 WHERE T011_Id = '{$T011_Id}'");
				$resposta['code'] = true;
			} else {
				$resposta['code'] = false;
				$resposta['data'] = "Não foi passado o campo T011_Id. Por favor, contate o suporte técnico.";
			}
		break;

		case 'atualizarParcelas':
			require_once("bibliotecas/classes/EST006.php");
			
			
			$T190_Id 		= isset($r_T190_Id) ? $r_T190_Id : false;
			$campoEditado 	= isset($r_campoEditado) ? $r_campoEditado : false;
			$valorAntigo 	= isset($r_valorAntigo) ? $r_valorAntigo : false;
			$novoValor 		= isset($r_novoValor) ? $r_novoValor : false;
			
			$EST006 = new EST006();
			
			$retorno = $EST006->EST006AtualizarParcelas($T190_Id, $campoEditado, $valorAntigo, $novoValor);
			
			if ($retorno === true) {
				$resposta['code'] = true;
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
		break;

		case 'excluirAgregadoOrdemCompra':
			$T210_Id = isset($r_T210_Id) ? $r_T210_Id : false;
			$sqlT012 = mysql_query("SELECT * FROM T012 LEFT JOIN T210 ON T210_T012_Id = T012_Id WHERE T210_Id = '{$T210_Id}'");
			if(mysql_num_rows($sqlT012) > 0){
				$T012 = mysql_fetch_array($sqlT012);
				mysql_query("DELETE FROM T210 WHERE T210_Id = '{$T210_Id}'");
				mysql_query("CALL T011_Gravar_Totalizacao('{$T012['T012_T011_Id']}')");
				$resposta['code'] = true;
			} else {
				$resposta['code'] = false;
			}
		break;
	}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");"

?>