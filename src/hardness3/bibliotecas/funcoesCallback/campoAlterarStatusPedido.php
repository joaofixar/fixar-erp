<?php
namespace hardness;
function campoAlterarStatusPedido($array){
	global $g;
	$novoIdTelaForm = uniqid();	
	$imagem = <<<EOT
		<span onclick="campoAlterarStatusPedido('{$array['todosCampos']['T005_Id']}', '{$g['divId']}');"> 
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-arrowreturnthick-1-w" title="Alterar status do pedido"></span>
		   </div>
		</span>
EOT;
	return $imagem;
}
