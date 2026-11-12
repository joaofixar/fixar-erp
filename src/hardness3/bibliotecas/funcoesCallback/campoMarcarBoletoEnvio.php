<?php
namespace hardness;
function campoMarcarBoletoEnvio($array) {
	global $g;
	$T117_Id = $array['todosCampos']['T117_Id'];
	$T117 = mysql_query("SELECT * FROM T117 LEFT JOIN T002 ON T002_T117_Id = T117_Id WHERE T117_Id = '{$T117_Id}'");
	$T117 = mysql_fetch_array($T117);
	if($T117['T002_Data_Recebimento'] == '0000-00-00' && $T117['T117_Flag_Cancelado'] != 'S'){
		$retorno = "<span style='width: 50px; display:block;'><input onclick=\"fin005MarcarBoletoEnvio(this, '{$T117_Id}', '{$g['divId']}')\" type='checkbox' id='T117_Flag_Atualizar' title='Enviar por Email'";
		if ($array['todosCampos']['T117_Flag_Atualizar'] == 'S'){
		    $retorno .="checked='checked'";
		}
		return $retorno .= " /></span>";
	} else {
		return '';
	}
}