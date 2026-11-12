<?php
namespace hardness;
	
function validaCompetencia($array){
	$sql = "SELECT D110_Titulo FROM D110 WHERE D110_Id='{$array['todosCampos']['D113_D110_Id']}'";
	
	$result = mysql_query($sql);
	
	$retorno = mysql_fetch_assoc($result);
	
	return $retorno['D110_Titulo'];
}

function buscaUnidade($array) {
	return $array['todosCampos']['D037_Unidade'];
}

