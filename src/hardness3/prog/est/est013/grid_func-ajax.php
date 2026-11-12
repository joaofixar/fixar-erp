<?php
namespace hardness;

$resposta = array('code' => true, 'data' => array());

/**
 * Ações
 */
switch($g['r'][3]) {
	case 'est013AlterarPeriodo':
		$periodo = mysql_real_escape_string($r_periodo);
		$sql = "UPDATE C020 SET C020_Numero_Meses_Relatorio_Estoque = '{$periodo}' WHERE C020_Id = '{$g['c020']['C020_Id']}';";
		mysql_query($sql);
	break;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>