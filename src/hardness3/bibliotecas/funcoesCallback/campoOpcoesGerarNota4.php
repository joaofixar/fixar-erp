<?php
namespace hardness;
function campoOpcoesGerarNota4($array){
	global $g;
	$imagem = <<<EOT
		<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Imprimir Etiqueta', '/ven/ven012/content/ven123/','&T005_Id={$array['todosCampos']['T005_Id']}', [900,550]);">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-tag" title="Imprimir Etiquetas"></span>
		   </div>
		</span>
EOT;
	return $imagem;
}