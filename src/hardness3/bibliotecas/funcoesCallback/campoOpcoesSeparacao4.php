<?php
namespace hardness;
function campoOpcoesSeparacao4($array){
	global $g;
	$novoIdTelaForm = uniqid();	
	$imagem = <<<EOT
		<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Imprimir Etiqueta', '/est/est002/content/est002ContentEtiquetas/','&T005_Id={$array['todosCampos']['T005_Id']}', [900,550]);">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-tag" title="Imprimir Etiquetas"></span>
		   </div>
		</span>
EOT;

	return $imagem;
}