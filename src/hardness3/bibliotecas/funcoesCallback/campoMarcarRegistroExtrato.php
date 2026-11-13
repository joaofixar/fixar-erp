<?php
namespace hardness;
function campoMarcarRegistroExtrato($array) {
	global $g;
	$T216_Id = $array['todosCampos']['T216_Id'];
	$retorno = "<span style='display:block; text-align: center;'><input onclick=\"fin003MarcarRegistroExtrato(this, '{$T216_Id}', '{$g['divId']}')\" type='checkbox' id='T216_Flag_Importar' title='Processar Transação'";
	if ($array['todosCampos']['T216_Flag_Importar'] == 'S'){
	    $retorno .="checked='checked'";
	}
	if($array['todosCampos']['T216_Flag_Importar'] == 'I') {
		return '<span class="ui-icon ui-icon-check" style="margin-left: 10px; float: left;" />';
	} else if($array['todosCampos']['T216_D027_Id'] <= 0 and $array['todosCampos']['T216_D031_Id'] <= 0){
		return '';
	} else {
		if($array['todosCampos']['T216_Flag_Importar'] == 'A'){
			$alert = '<span class="ui-icon ui-icon-notice" style="margin-right: -6px; float: left;" title="Esta transação precisa estar vínculada a uma conta a pagar!" />';		
			$retorno = $alert.$retorno;
		}
		return $retorno .= " /></span>";
	}
}