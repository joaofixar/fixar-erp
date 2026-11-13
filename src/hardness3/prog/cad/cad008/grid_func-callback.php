<?php
namespace hardness;
/**
 * CAD - cad008 - Cadastro de Bens
 */
function campoExclusao($array) {
	global $g;
	return <<<EOT
		<a href="#" onclick="cad008CampoExclusao('{$array['todosCampos']['D106_Id']}', '{$g['divId']}')">
			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
		</a>
EOT;
}

function validaCampoStatus($array) {
	
	if ($array['todosCampos']['D106_Flag_Status'] == 'A'){
	return "Ativo";
	}
	if ($array['todosCampos']['D106_Flag_Status'] == 'I'){
		return "Inativo";
	}
}
function validaCampoStatusContratos($array){
	if($array['todosCampos']['T141_Flag_Status'] == 'A'){
		return "Ativo";
	}elseif($array['todosCampos']['T141_Flag_Status'] == 'I'){
		return "Inativo";
	}
}

function campoExcluirManutencao($array) {
	global $g;
	return <<<EOT
		<a href="#" onclick="cad008CampoExcluirManutencao('{$array['todosCampos']['T142_Id']}', '{$g['divId']}')">
			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
		</a>
EOT;
}

function campoExcluirLogistica($array) {
	global $g;
	return <<<EOT
    		<a href="#" onclick="cad008CampoExcluirLogistica('{$array['todosCampos']['T143_Id']}','{$g['divId']}')">
    			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
			</a>
EOT;
	
}
function campoCheckboxChecklist($array){
	global $g,$r_acaoId;
		$retorno = "<input onchange=\"cad008CheckboxChecklist(this, '{$array['todosCampos']['D120_Id']}', '{$r_acaoId}', '{$g['divId']}')\" type='checkbox' id='T159_Flag_Ok' title='Confirmar Item'";
		if ($array['todosCampos']['T159_Flag_Ok']=='S'){
			return $retorno .="checked='checked'";
		}
		return $retorno .= " />";
	
}
?>