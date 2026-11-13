<?php
namespace hardness;
/**
 * Lucas
 * 19/04/2012
 */

function campoTipoUsuario($array){
	switch ($array['todosCampos']['usuario.C007_Flag_Vendedor']){
		case 'S':
			return "SUPERVISOR";
		break;
		case 'V':
			return "VENDEDOR";
		break;
		case 'D':
			return "DIRETOR";
		break;
		case 'N':
			return "NORMAL";
		break;
		case 'C':
			return "COMPRADOR";
		break;
	}
}

function validaCampoPerfil($array){
	$C019 = mysql_query("SELECT * FROM C019 LEFT JOIN C021 on C021_Id=C019_C021_Id LEFT JOIN C004 on C004_Id=C019_C004_Id WHERE C019_C007_Id='{$array['todosCampos']['usuario.C007_Id']}'");
	$retorno ="";
	while($mC019 = mysql_fetch_array($C019)){
		$retorno .= $mC019['C004_Nome_Abreviado']." - ".$mC019['C021_Nome_Perfil']."<br />";
	}
	return $retorno;
}
?>