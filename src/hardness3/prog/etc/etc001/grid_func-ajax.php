<?php
namespace hardness;
/**
* FIN - FIN001 - Contas a Receber
* 
* Processamento de ações AJAX
*/

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'excluir':
		$T144_Id = isset($_REQUEST['T144_Id']) ? $_REQUEST['T144_Id'] : false;
		if (empty($T144_Id)) {
			$resposta['code'] = false;
			$resposta['data'] = 'Registro não existe.';
		} else {
			$T144_Id = mysql_real_escape_string($T144_Id);
			$sql = "DELETE FROM T144 WHERE T144_Id='{$T144_Id}'";
			$retorno = mysql_query($sql);
			if ($retorno === true) {
				$resposta['data'] = 'T144_Id-' . $T144_Id;
			} else {
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
		}
	break;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>