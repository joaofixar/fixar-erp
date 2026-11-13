<?php
namespace hardness;

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {

	case 'callback':
		if ($r_c2 == 'false') {
			if (isset($r_remove)) {
				$sql = "UPDATE D152 SET D152_D153_Id = NULL WHERE D152_Id = '{$r_D152_Id}'";
			} else {
				$sql = "UPDATE D152 SET D152_D153_Id = '{$r_D153_Id}' WHERE D152_Id = '{$r_D152_Id}'";
			}
		} else {
			if (isset($r_remove)) {
				$sql = "UPDATE D152 SET D152_D153_2_Id = NULL WHERE D152_Id = '{$r_D152_Id}'";
			} else {
				$sql = "UPDATE D152 SET D152_D153_2_Id = '{$r_D153_Id}' WHERE D152_Id = '{$r_D152_Id}'";
			}
		}
		mysqli_query($sql);
	break;

	case 'exportacaoEBSalteraFlagData':

		$D151_Id 	= $r_D151_Id ? $r_D151_Id : false;
		$selected	= $r_selected ? $r_selected : false;
		mysql_query("UPDATE D151 SET D151_Flag_Utiliza_Data='{$selected}' WHERE D151_Id='{$D151_Id}'");

	break;

}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>