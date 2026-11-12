<?php
namespace hardness;

function campoImpTag($array){
	global $g;
	// Envia para seleção de etiquetas
	return <<<EOT
		<li class="ui-state-default ui-corner-all icone" style="list-style: none;" title="Imprimir Etiqueta">
			<span class="ui-icon ui-icon-tag" onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Impressão', '/cad/cad051/content/cad051SelecionarModelo/','&D004_Id={$array['todosCampos']['D004_Id']}', [300,150]);">
			</span>
		</li>
EOT;
}

function tipoLocacao($array) {
	if ($array['campoAtual'] == 1) {
		return "Um produto";
	} else if ($array['campoAtual'] == 2) {
		return "Vários produtos";
	}
}

function validaCampoImprimir($array){
        global $g,$r_acaoId;
        $retorno = "<input onclick=\"cad019CheckboxImprimir(this, '{$array['todosCampos']['D004_Id']}', '{$g['divId']}')\" type='checkbox' id='D004_Flag_Imprimir_Etiqueta' title='Imprimir'";
        if ($array['todosCampos']['D004_Flag_Imprimir_Etiqueta']=='S'){
            return $retorno .="checked='checked'";
        }
        return $retorno .= " />";
    }
?>
