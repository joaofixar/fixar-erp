<?php
namespace hardness;
require_once('bibliotecas/classes/EST005.php');
$EST005 = new EST005();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch ($g['r'][3]) {
	case 'gerarContasAPagar':
		$T084_Id = (!empty($r_T084_Id))	? $r_T084_Id : false;
		$retorno = $EST005->gerarContasAPagar($T084_Id);
		if($retorno === true){
			$resposta['code'] = true;
			$resposta['data'] = "Título gerados com sucesso";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'populaD073':
	    $sql = mysql_query("SELECT D006_D073_Id FROM D006 WHERE D006_Id = '{$r_D006_Id}'");
	    $row = mysql_fetch_array($sql);
		$resposta['data']['D073_Id'] = $row['D006_D073_Id'];
	break;
	
	case 'atualizaAliquotas':
		$T084_Id = (!empty($r_T084_Id))	? $r_T084_Id : false;
		
		$retorno = $EST005->atualizarAliquotasConhecimento($T084_Id);

		if($retorno === true){
			$resposta['code'] = true;
			$resposta['data'] = "Alíquotas atualizadas com sucesso";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>