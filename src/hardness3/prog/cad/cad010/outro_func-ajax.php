<?php
namespace hardness;
  /**
 * Weiberlan
 * 25/11/2011
 * VEN - VEN001 - Cadastro de Orçamentos
 *
 * Processamento de ações AJAX
 */
$resposta = array('code' => true, 'data' => array());

/**
 * Ações
 */
switch($g['r'][3]) {
	                         			
	case 'verificaLogin':
		$senha = isset($r_senha) ? $r_senha : false;
		
		$sql = "SELECT D109_Id,D109_Nome FROM D109 WHERE D109_Senha = '".mysql_real_escape_string($senha)."';";
		$res = mysql_query($sql);
		$row = mysql_fetch_assoc($res);
		
		if (isset($row['D109_Id']) && !empty($row['D109_Id'])) {
			$resposta['code'] = true;
			$resposta['data'] = "A digital será cadastrada para o funcionário:<br /><br />" . $row['D109_Nome'];
			$resposta['D109_Id'] = $row['D109_Id'];
		} else {
			$resposta['code'] = false;
			$resposta['data'] = "Código de funcionário não encontrado.<br />Por favor, tente novamente.";
		}
	break;
	
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>
