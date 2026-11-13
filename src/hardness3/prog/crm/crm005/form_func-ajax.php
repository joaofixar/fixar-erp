<?php
namespace hardness;
/*
* Processamento de ações AJAX
*/

require_once("bibliotecas/classes/CRM005.php");
$CRM005 = new CRM005();

$resposta = array('code' => true, 'data' => array());
require_once('bibliotecas/classes/CAD002.php');
$CAD002 = new CAD002();

/**
* Ações
*/
switch($g['r'][3]) {
	case 'atualizarCamposProdutos':
		$D009_Id = isset($_REQUEST['D009_Id']) ? $_REQUEST['D009_Id'] : false;
		$D024_Id = (isset($r_D024_Id)) ? $r_D024_Id : false;
		
		if(!empty($D024_Id)){
			$extraSqlNovo = "AND D083_D024_Id = ".$D024_Id;
		}else{
			$extraSqlNovo="";
		}
		
		$sql = <<<EOT
		SELECT  *
   				from D001
   			left join D037 on D001_D037_Id=D037_Id
   			left join D049 on D049_D001_Id=D001_Id
   			left join D083 on D083_D001_Id=D001_Id {$extraSqlNovo} 
   			left join D009 on D009_D049_Id=D049_Id
   			left join D082 on D082_Id=D049_D082_Id
   			left join D041 on D041_D049_Id=D049_Id
   			left join D005 on D005_Id=D001_D005_Id
   				WHERE D009_Id = '{$D009_Id}'
EOT;

		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);
		
		if(empty($D024_Id)){
			$row['D049_Preco_1'] = gCorrigeNumero($row['D009_Preco_1']);
		}else{
			$row['D049_Preco_1'] = $CAD002->retornaPrecoVenda($row['D009_Id'], null, $D024_Id);
		}
		
		if (!empty($row)) {
			$resposta['code'] = true;
			$resposta['data'] = $row;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = "Erro SQL: $sql";
		}
   		
	break;
	case 'atualizarCamposCliente':
		$D024_Id = isset($_REQUEST['D024_Id']) ? $_REQUEST['D024_Id'] : false;
		
		$sql = <<<EOT
		SELECT * FROM D024 WHERE D024_Id = '{$D024_Id}'
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
}

	echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>
