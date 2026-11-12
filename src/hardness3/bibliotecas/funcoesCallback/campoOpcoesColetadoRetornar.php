<?php
namespace hardness;
function campoOpcoesColetadoRetornar($array){
	global $g;
	$novoIdTelaForm = uniqid();	
	$imagem = <<<EOT
		<span onclick="est002RetornarPedidoColetado('{$array['todosCampos']['T005_Id']}', '{$g['divId']}');"> 
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-arrowreturnthick-1-w" title="Retornar pedido para aguardando coleta"></span>
		   </div>
		</span>
EOT;
	return $imagem;
}
