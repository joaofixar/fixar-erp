<?php
namespace hardness;

function incluirDespesa($array, $param = array()){
	global $g;
	$valor = $array['todosCampos']['T168_Valor'];
	$vencimento = $array['todosCampos']['T168_Data'];
	$descricao = $array['todosCampos']['T168_Descricao'];
	$T075_Id = $array['todosCampos']['T168_T075_Id'];
	return <<<EOT
		<span style="margin: 0 0 -2px 2px; width: 20px; height: 20px;" class="ui-button-icon-primary ui-icon ui-icon-plusthick" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Título a Pagar', '/fin/fin002/content/fin002content02/', '&T075_Id={$T075_Id}&valor={$valor}&vencimento={$vencimento}&observacao={$descricao}&incluirDespesa=true', [600,500]);"></span>
EOT;
}
