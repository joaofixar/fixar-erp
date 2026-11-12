<?php
namespace hardness;
function campoMarcarBoletoAtualizar($array) {
	global $g;
	$T117_Id = $array['todosCampos']['T117_Id'];
	$T117 = mysql_query("SELECT * FROM T117 LEFT JOIN T002 ON T002_T117_Id = T117_Id WHERE T117_Id = '{$T117_Id}'");
	$T117 = mysql_fetch_array($T117);
	if($T117['T002_Data_Recebimento'] == '0000-00-00'){
		$retorno = "<span style='display:block;'><input onclick=\"fin005MarcarBoletoAtualizar(this, '{$T117_Id}', '{$g['divId']}')\" type='checkbox' id='T117_Flag_Atualizar' title='Atualizar Boleto'";
		if ($array['todosCampos']['T117_Flag_Atualizar'] == 'S'){
		    $retorno .="checked='checked'";
		}
		return $retorno .= " /></span>";
	} else {
		return '';
	}
}