<?php
namespace hardness;	

require_once('prog/fin/fin005/grid_func-js.php');

	function campoCancelarT117($array){
		global $g;
		$T117_T002_Id = $array['todosCampos']['T117_T002_Id'];
		$sqlT002 = mysql_query("SELECT T002_T117_Id FROM T002 WHERE T002_Id = '{$T117_T002_Id}'");
		$T002 = mysql_fetch_array($sqlT002);
		if ($array['todosCampos']['T117_Flag_Cancelado']=='N')
		{
			//No antigo sistema: <a href=\"javascript:cancelar_boleto('$mT117[T117_Id]');\" style='color:blue;text-decoration:none;'>";
			return "<a href=\"#\" onclick=\"fin005CampoCancelar('{$array['todosCampos']['T117_Id']}', '{$g['divId']}')\">Cancelar</a>";
		} else {
			if($T117_T002_Id > 0 && $T002['T002_T117_Id'] == 0){
				return "<a href=\"#\" onclick=\"fin005CampoDesfazerCancelamento('{$array['todosCampos']['T117_Id']}', '{$T117_T002_Id}','{$g['divId']}')\">Restaurar</a>";
			} else {
				return "Cancelado";
			}
		}
	}