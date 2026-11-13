<?php
namespace hardness;

function incluirTituloProjetado($array, $param = array()){
	global $g;
	$D014_Id = $array['todosCampos']['D014_Id'];
	$D032_Id = $array['todosCampos']['D032_Id'];
	$D031_Id = $array['todosCampos']['D014_D031_Id'];
	$D024_Id = $array['todosCampos']['D014_D024_Id'];
	$D073_Id = $array['todosCampos']['D014_D073_Id'];
	$valor = $array['todosCampos']['T018_Valor_Saldo'];
	$documento = $array['todosCampos']['T018_Mes'].'/'.$array['todosCampos']['T018_Ano'];
	$vencimento = urlencode($array['todosCampos']['D014_Dia_Vencimento'].'/'.$documento);
	$documento = urlencode($documento);
	return <<<EOT
		<span style="margin: 0 0 -2px 2px; width: 20px; height: 20px;" class="ui-button-icon-primary ui-icon ui-icon-plusthick" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Título a Pagar', '/fin/fin002/content/fin002content02/', '&D024_Id={$D024_Id}&D031_Id={$D031_Id}&D014_Id={$D014_Id}&D032_Id={$D032_Id}&D073_Id={$D073_Id}&valor={$valor}&vencimento={$vencimento}&documento={$documento}&incluirTituloProjetado=true', [600,500]);"></span>
EOT;
}
