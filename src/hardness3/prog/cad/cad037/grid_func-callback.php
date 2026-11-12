<?php
namespace hardness;
/**
 * Weiberlan
 * 28/11/2011
 */

function validaCampoTipo($array){
		if ($array['todosCampos']['D121_Tipo']==1){
			return "Balsa";
		}elseif($array['todosCampos']['D121_Tipo']==2){
			return "Imóvel";
		}elseif($array['todosCampos']['D121_Tipo']==3){
			return "Veículos";
		}else{
			return "";
		}
}
?>