<?php
namespace hardness;
function campoMarcarNFImpressao($array) {
	global $g;
	$T007_Id = $array['todosCampos']['T007_Id'];
	if(!empty($array['todosCampos']['T007_Numero_Protocolo_Nfe'])) {
		$retorno = "<input onclick=\"ven003MarcarNFImpressao(this, '{$T007_Id}', '{$g['divId']}')\" type='checkbox' id='T007_Marcar_Imprimir' title='Imprimir'";
		if ($array['todosCampos']['T007_Flag_Impresso'] == 'M'){
		    return $retorno .="checked='checked'";
		}
		return $retorno .= " />";
	} else {
		return '';
	}
}