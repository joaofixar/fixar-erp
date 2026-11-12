<?php
namespace hardness;

function campoMostraValorBI($array, $param){
	switch ($param) {
		case 'D132_Grupo':
			if($array['todosCampos'][$param] == '1'){
				return "VENDAS";
			} else if($array['todosCampos'][$param] == '2'){
				return "COMPRAS";
			} else if($array['todosCampos'][$param] == '3'){
				return "ESTOQUE";
			} else if($array['todosCampos'][$param] == '4'){
				return "FINANCEIRO";
			} else {
				return "";
			}
		break;
		case 'D132_Flag_Exibir_Dashboard':
			return ($array['todosCampos'][$param] == 'S') ? 'SIM' : 'NÃO';
		break;
		case 'D132_Flag_Exibir_Alcada':
			if($array['todosCampos'][$param] == '1'){
				return "VENDEDOR/COMPRADOR";
			} else if($array['todosCampos'][$param] == '2'){
				return "SUPERVISOR";
			} else {
				return "DIRETOR";
			}
		break;
	}
	return '';
}