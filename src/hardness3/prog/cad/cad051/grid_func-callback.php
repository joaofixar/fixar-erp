<?php
namespace hardness;
/**
 * Weiberlan
 * 28/11/2011
 */

function cad051CampoTipo($array){
	if($array['todosCampos']['D138_Flag_Tipo'] == 'P'){
		return "Produto";
	}elseif($array['todosCampos']['D138_Flag_Tipo'] == 'L'){
		return "Locação";
	}elseif($array['todosCampos']['D138_Flag_Tipo'] == 'C'){
		return "Cliente";
	}elseif($array['todosCampos']['D138_Flag_Tipo'] == 'G'){
		return "Generica";
	}elseif($array['todosCampos']['D138_Flag_Tipo'] == 'S'){
		return "Pedido";
	}
}

function campoImpTag($array){
	global $g;
	
	if ($array['todosCampos']['D138_Flag_Editor'] == 'S') {
		return <<<EOT
			<li class="ui-state-default ui-corner-all icone" style="list-style: none;" title="Imprimir Etiqueta">
				<span class="ui-icon ui-icon-tag" onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Editor Modelos Genérica - ID: {$array['todosCampos']['D138_Id']}', '/cad/cad051/content/cad051ContentImpressaoGenerica/', '&print=true&acaoId=' + encodeURIComponent('{$array['todosCampos']['D138_Id']}'), [800,500]);">
				</span>
			</li>
EOT;
	} else {
		return <<<EOT
			<li class="ui-state-default ui-corner-all icone" style="list-style: none;" title="Imprimir Etiqueta">
				<span class="ui-icon ui-icon-tag" onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Modelo - ID: {$array['todosCampos']['D138_Id']}', '/cad/cad051/content/cad051ContentCadastroModelo/', '&acaoId=' + encodeURIComponent('{$array['todosCampos']['D138_Id']}'), [500,500]);">
				</span>
			</li>
EOT;
	}
}
?>