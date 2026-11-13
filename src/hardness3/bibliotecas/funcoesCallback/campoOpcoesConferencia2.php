<?php
namespace hardness;
function campoOpcoesConferencia2($array){
	global $g;
	$novoIdTelaForm = uniqid();
	$retorno = ($array);
	$imagem = <<<EOT
		<span onclick="est002RetornarPedido('{$array['todosCampos']['T005_Id']}', '{$g['divId']}');"> 
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-arrowreturnthick-1-w" title="Retornar pedido para o vendedor"></span>
		   </div>
		</span>
EOT;
	return $imagem;
}