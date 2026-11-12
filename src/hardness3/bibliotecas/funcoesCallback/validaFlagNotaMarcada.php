<?php
namespace hardness;

function validaFlagNotaMarcada($array) {
	global $g;
	$T007_Id = $array['todosCampos']['T007_Id'];
	if($array['todosCampos']['T007_Flag_ACP'] == 6 && $array['todosCampos']['T007_Flag_Cancelada'] == 'N' && $array['todosCampos']['T007_Valor_Total'] > 0 && empty($array['todosCampos']['T276_Numero_MDFe'])){
		$retorno = "<input onclick=\"ven003CheckboxMarcar(this, '{$T007_Id}', '{$g['divId']}')\" type='checkbox' id='T007_Flag_Marcado' title='Gerar Mdf-e'";
		if ($array['todosCampos']['T007_Flag_Marcado'] == 'S'){
		    return $retorno .="checked='checked'";
		}
		return $retorno .= " />";
	}else if(!empty($array['todosCampos']['T276_Numero_MDFe']) && $array['todosCampos']['T276_Flag_Cancelada'] == 'S'){
		$retorno = "<input onclick=\"ven003CheckboxMarcar(this, '{$T007_Id}', '{$g['divId']}')\" type='checkbox' id='T007_Flag_Marcado' title='Gerar Mdf-e'";
		if ($array['todosCampos']['T007_Flag_Marcado'] == 'S'){
		    return $retorno .="checked='checked'";
		}
		return $retorno .= " />";
	}else{
		$retorno = "<a href=\"#\" onclick=\"abrirJanela(false, divIdRootAberto(), gridPegarDivId(\$(this)), unique(), '', 'Editar MDF-e:'+ encodeURIComponent('{$array['todosCampos']['T284_T276_Id']}'), '/fis/fis012/content/fis012MdfeCadastro/', '&acaoId=' + encodeURIComponent('{$array['todosCampos']['T284_T276_Id']}'), [650,550]); return false;\"><font color='#413a3a'>{$array['todosCampos']['T276_Numero_MDFe']}</font></a>";
		return $retorno;
	}
}