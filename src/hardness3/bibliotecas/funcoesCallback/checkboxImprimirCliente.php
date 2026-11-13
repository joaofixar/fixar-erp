<?php
namespace hardness;
// D024_Id, D024_Flag_Imprimir_Etiqueta
function checkboxImprimirCliente($array){
	global $g,$r_acaoId;
	$retorno = "<input onclick=\"checkboxImprimirCliente(this, '{$array['todosCampos']['D024_Id']}', '{$g['divId']}')\" type='checkbox' id='D024_Flag_Imprimir_Etiqueta' title='Imprimir'";
	if ($array['todosCampos']['D024_Flag_Imprimir_Etiqueta']=='S'){
	    return $retorno .="checked='checked'";
	}
	return $retorno .= " />";
}