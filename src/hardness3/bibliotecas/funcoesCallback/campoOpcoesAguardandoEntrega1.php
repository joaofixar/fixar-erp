<?php
namespace hardness;
function campoOpcoesAguardandoEntrega1($array){
 	global $g;
	$novoIdTelaForm = uniqid();
	$retorno = ($array);
	$imagem = <<<EOT
		<span onclick="est002ConfirmarEntrega('{$array['todosCampos']['T005_Id']}', '{$g['divId']}');">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-circle-arrow-e" title="Confirmar Entrega"></span>
		   </div>
		</span>	
EOT;
	return $imagem;
}