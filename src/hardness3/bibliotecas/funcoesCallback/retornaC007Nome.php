<?php
namespace hardness;

function retornaC007Nome($array, $param = NULL){
	$sqlC007 = mysql_query("Select C007_Nome FROM C007 WHERE C007_Id='{$array['todosCampos']['C007_Id']}'");
	$C007 = mysql_fetch_assoc($sqlC007);

	return $C007['C007_Nome'];	
}
