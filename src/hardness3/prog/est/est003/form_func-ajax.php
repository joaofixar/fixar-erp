<?php
namespace hardness;

$resposta = array('code' => true, 'data' => array());
require_once('bibliotecas/classes/EST003.php');
$EST003 = new EST003;

switch($g['r'][3]) {

	case 'produtoDefeito':
		$D009_Id = isset($_REQUEST['D009_Id']) ? $_REQUEST['D009_Id'] : false; 
		
		$sql = "SELECT * 
		 		  FROM D009 
			 LEFT JOIN D049 ON D009_D049_Id = D049_Id
			 LEFT JOIN D001 ON D049_D001_Id = D001_Id
			  	 WHERE D009_Id = '{$D009_Id}'";

		$res = mysql_query($sql);
		if(mysql_num_rows($res) > 0){

			$result = mysql_fetch_array($res);

			$result['locacao'] = gProcessaSelect(gGeraSelect('T066', 'T066_Id', "D004_Local,REPLACE(FORMAT(T066_Quantidade_Estoque[[virgula]] 2)[[virgula]] '.'[[virgula]] '[[virgula]]'),T066_Id", "LEFT JOIN D004 ON T066_D004_Id = D004_Id WHERE T066_D009_Id = '{$D009_Id}' ORDER BY D004_Local, T066_Id ASC", true, false, '%1 (%2) - %3'));

			$resposta['data'] = $result;
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
		}		
	break;

	case 'gerarPedidoProdutosDefeitos':
		$T005_Id = isset($_REQUEST['T005_Id']) ? $_REQUEST['T005_Id'] : false; 
		log("T005_Id no form= ".$T005_Id);
		$retorno = $EST003->gerarPedidoProdutosDefeitos($T005_Id);
		if($retorno === true){
			$resposta['code'] = true;
			$resposta['data'] = "Pedido gerado com sucesso";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}	
	break;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");"
?>