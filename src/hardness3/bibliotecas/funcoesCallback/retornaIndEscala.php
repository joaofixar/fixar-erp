<?php
namespace hardness;
function retornaIndEscala($array){
	global $g;
	
	if($array['todosCampos']['D149_Indicador_Escala'] == "S"){
		return "Relevante";
	} else if($array['todosCampos']['D149_Indicador_Escala'] == "N"){
		return "Não relevante";
	}
}